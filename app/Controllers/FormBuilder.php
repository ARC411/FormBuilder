<?php

namespace App\Controllers;

use App\Models\DeviceModel;
use App\Models\ProcessModel;
use App\Models\FormRevisionModel;
use App\Models\FormParameterModel;
use App\Models\LogActivityModel;

class FormBuilder extends BaseController
{
    protected $deviceModel;
    protected $processModel;
    protected $formRevModel;
    protected $formParamModel;
    protected $logActivityModel;

    public function __construct()
    {
        $uri = service('uri');
        $seg1 = strtolower($uri->getTotalSegments() >= 1 ? $uri->getSegment(1) : '');
        $seg2 = strtolower($uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '');

        // Hanya batasi jika user mengakses URL /form-builder secara langsung (kecuali render-input)
        $isDirectFormBuilderRoute = ($seg1 === 'form-builder' || $seg1 === 'formbuilder');
        $isRenderInput = ($seg2 === 'render-input');

        if ($isDirectFormBuilderRoute && !$isRenderInput && !session()->get('isadmin')) {
            echo "<script>alert('Akses Ditolak! Khusus Admin/QC.'); window.location.href='" . base_url('home') . "';</script>";
            exit;
        }
    }

    public function index()
    {
        $data = [
            'title'   => 'Form Builder & Revision',
            'devices' => $this->deviceModel->findAll()
        ];
        return view('form_builder', $data);
    }

    public function getProcesses()
    {
        $deviceCode = $this->request->getGet('device_code');
        $type       = $this->request->getGet('type');
        // Baik startup menurun maupun startup mendatar sama-sama mengambil daftar proses bertipe 'startup'
        $dbType     = ($type === 'startup_horizontal') ? 'startup' : $type;
        $processes  = $this->processModel->getAll($deviceCode, $dbType);
        return $this->response->setJSON($processes);
    }

    // 3. AJAX Load Form & Parameter
    public function loadForm()
    {
        $processCode  = trim((string)$this->request->getPost('process'));
        $selectedType = trim((string)$this->request->getPost('doc_type'));
        $forceReparse = $this->request->getPost('reparse');
        $switchLayout = trim((string)$this->request->getPost('switch_layout'));

        if (!$processCode) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih proses terlebih dahulu!']);
        }

        if (!empty($selectedType) && in_array($selectedType, ['startup', 'startup_horizontal', 'production', 'foregoing'])) {
            $detectedKategori = $selectedType;
        } else {
            $parts = explode('-', strtolower($processCode));
            $typeKode = $parts[1] ?? 'p';
            $detectedKategori = ($typeKode === 's') ? 'startup' : (($typeKode === 'f') ? 'foregoing' : 'production');
        }

        $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();

        if (!$revision) {
            $procRow = $this->processModel->where('process_code', $processCode)->first();
            $revCode = $procRow['revisi'] ?? '0';
            $this->formRevModel->protect(false)->insert([
                'process_code'  => $processCode,
                'revisi'        => $revCode,
                'kategori_form' => $detectedKategori,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revisionId = $this->formRevModel->getInsertID();
            $revision = $this->formRevModel->find($revisionId);
        } else {
            $revisionId = $revision['id'];
            // Jika user secara eksplisit mengganti tipe layout (misal dari Startup Menurun <-> Startup Mendatar)
            if ($switchLayout === 'true' && in_array($detectedKategori, ['startup', 'startup_horizontal'])) {
                $this->formRevModel->protect(false)->update($revisionId, ['kategori_form' => $detectedKategori]);
                $revision['kategori_form'] = $detectedKategori;
            } elseif (!empty($revision['kategori_form']) && in_array($revision['kategori_form'], ['startup', 'startup_horizontal']) && in_array($detectedKategori, ['startup', 'startup_horizontal'])) {
                // Pertahankan kategori startup/startup_horizontal yang sudah tersimpan di DB
                $detectedKategori = $revision['kategori_form'];
            } elseif (($revision['kategori_form'] ?? '') !== $detectedKategori) {
                $this->formRevModel->protect(false)->update($revisionId, ['kategori_form' => $detectedKategori]);
                $revision['kategori_form'] = $detectedKategori;
            }
        }

        $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();

        $isLegacyFlat = false;
        $hasAnyParent = false;
        foreach ($rawParams as $rp) {
            if (!empty($rp['parent_id'])) $hasAnyParent = true;
            if (
                preg_match('/^(Input\s+)?PAR\d+[a-z]?$/i', trim($rp['nama_parameter'])) ||
                strpos($rp['nama_parameter'], 'COL_') === 0 ||
                $rp['nama_parameter'] === 'Elemen Kosong'
            ) {
                $isLegacyFlat = true;
            }
        }
        if (!$hasAnyParent && count($rawParams) > 1) {
            $isLegacyFlat = true;
        }

        if (empty($rawParams) || $isLegacyFlat || $forceReparse === 'true') {
            $this->parseSingleProcess($processCode, $revisionId, $detectedKategori);
            $revision = $this->formRevModel->find($revisionId);
            $detectedKategori = $revision['kategori_form'] ?? $detectedKategori;
            $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();

            if ($forceReparse === 'true') {
                $this->catatLog('RESYNC HTML', "Menarik ulang (reset) struktur form dari file HTML asli pada Revisi {$revision['revisi']}", $processCode);
            }
        }

        $treeParams = $this->buildTree($rawParams);
        if (empty($treeParams) && !empty($rawParams)) {
            foreach ($rawParams as &$rp) {
                $rp['children'] = [];
            }
            $treeParams = $rawParams;
        }

        $maxTableDepth = 0;
        foreach ($treeParams as $node) {
            $d = $this->calculateDepth($node);
            if ($d > $maxTableDepth) $maxTableDepth = $d;
        }

        $data = [
            'process_code'  => $processCode,
            'revision'      => $revision,
            'kategori_form' => $detectedKategori,
            'treeParams'    => $treeParams,
            'maxTableDepth' => max(1, $maxTableDepth)
        ];

        $htmlUtuh = view('form_builder_visual', $data);
        return $this->response->setJSON([
            'status'      => 'success',
            'html'        => $htmlUtuh,
            'revision'    => $revision,
            'params_list' => $rawParams,
            'tree_params' => $treeParams
        ]);
    }

    // 4. AJAX Save Tree (Simpan Rekursif Tanpa Batas Tingkat: Bapak -> Anak -> Cucu -> dst.)
    public function saveTreeParameter()
    {
        $revisionId = (int)$this->request->getPost('revision_id');
        $rootParent = $this->request->getPost('root_parent_id') ?: null;
        $treeJson   = $this->request->getPost('tree_json');

        if (!$revisionId || empty($treeJson)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data parameter tidak lengkap!']);
        }

        $treeData = json_decode($treeJson, true);
        if (!is_array($treeData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format struktur parameter tidak valid!']);
        }

        // Ambil daftar baris matriks Foregoing (jika form ini adalah matriks Foregoing seperti X, Y, Z, θ)
        $allExisting = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        $gridRows = [];
        $hasSuffixMode = false;
        foreach ($allExisting as $ex) {
            if (!empty($ex['posisi_grid'])) {
                if (!in_array($ex['posisi_grid'], $gridRows)) {
                    $gridRows[] = $ex['posisi_grid'];
                }
                if (!empty($ex['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $ex['kolom_db'])) {
                    $hasSuffixMode = true;
                }
            }
        }

        // Jika ini adalah Edit pada node yang sudah ada, kumpulkan semua ID lama di bawah subtree tersebut
        // (kecuali anak-anak posisi_grid) agar node yang dihapus di modal ikut terhapus di DB
        $keptIds = [];
        $oldSubtreeIds = [];
        if (!empty($treeData['id'])) {
            $this->collectSubtreeNonGridIds((int)$treeData['id'], $allExisting, $oldSubtreeIds);
        }

        // Jalankan penyimpanan rekursif
        $this->saveNodeRecursive($treeData, $revisionId, $rootParent, $keptIds, $gridRows, $hasSuffixMode);

        // Hapus node yang dibuang oleh Admin saat di dalam modal
        $toDelete = array_diff($oldSubtreeIds, $keptIds);
        if (!empty($toDelete)) {
            $this->formParamModel->whereIn('id', $toDelete)->delete();
        }

        $revRow    = $this->formRevModel->find($revisionId);
        $procCode  = $revRow['process_code'] ?? '';
        $revNum    = $revRow['revisi'] ?? '-';
        $namaUtama = $treeData['nama_parameter'] ?? 'Parameter';
        $jmlAnak   = isset($treeData['children']) && is_array($treeData['children']) ? count($treeData['children']) : 0;
        $jenisAksi = !empty($treeData['id']) ? 'EDIT PARAMETER' : 'TAMBAH PARAMETER';

        $this->catatLog(
            $jenisAksi,
            "Menyimpan parameter '{$namaUtama}'" . ($jmlAnak > 0 ? " beserta {$jmlAnak} sub-parameter" : "") . " pada Revisi {$revNum}",
            $procCode
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Struktur parameter berhasil disimpan!'
        ]);
    }

    private function collectSubtreeNonGridIds(int $rootId, array $allParams, array &$collected)
    {
        $collected[] = $rootId;
        foreach ($allParams as $p) {
            if ((int)$p['parent_id'] === $rootId && empty($p['posisi_grid'])) {
                $this->collectSubtreeNonGridIds((int)$p['id'], $allParams, $collected);
            }
        }
    }

    private function generateNextParCode(int $revisionId, string $suffix = ''): string
    {
        $existingParams = $this->formParamModel->where('revision_id', $revisionId)->findAll();
        $maxParNum = 0;
        foreach ($existingParams as $ep) {
            if (!empty($ep['kolom_db']) && preg_match('/^par(\d+)/i', $ep['kolom_db'], $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxParNum) $maxParNum = $num;
            }
        }
        $nextNum = $maxParNum + 1;
        return 'par' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT) . $suffix;
    }

    private function saveNodeRecursive(array $node, int $revisionId, $parentId, array &$keptIds, array $gridRows, bool $hasSuffixMode)
    {
        $nodeId    = !empty($node['id']) ? (int)$node['id'] : null;
        $children  = (!empty($node['children']) && is_array($node['children'])) ? $node['children'] : [];
        $hasChildren = count($children) > 0;

        // Cek apakah ini di tabel Foregoing yang memiliki baris matriks (X, Y, Z, θ)
        $isForegoingLeafColumn = (!$hasChildren && !empty($gridRows) && ($node['jenis_elemen'] ?? 'input') === 'input');

        $tipeInput      = null;
        $kolomDb        = null;
        $batasMin       = null;
        $batasMax       = null;
        $satuanOpsi     = null;
        $isReadonly     = 0;
        $rumusOtomatis  = null;
        $isExclusive    = !empty($node['is_exclusive']) ? 1 : 0;
        $standarDinamis = (!empty($node['standar_dinamis']) && is_array($node['standar_dinamis']))
            ? json_encode($node['standar_dinamis'], JSON_UNESCAPED_UNICODE) : null;

        if (!$hasChildren && ($node['jenis_elemen'] ?? 'input') === 'input' && !$isForegoingLeafColumn) {
            $tipeInput  = $node['tipe_input'] ?: 'text';
            $kolomDb    = !empty($node['kolom_db']) ? trim($node['kolom_db']) : $this->generateNextParCode($revisionId);
            $isReadonly = !empty($node['is_readonly']) ? 1 : 0;

            if ($tipeInput === 'number') {
                $batasMin = (isset($node['batas_min']) && $node['batas_min'] !== '') ? (float)$node['batas_min'] : null;
                $batasMax = (isset($node['batas_max']) && $node['batas_max'] !== '') ? (float)$node['batas_max'] : null;
                if (!empty($node['satuan_opsi']) && is_array($node['satuan_opsi'])) {
                    $satuanOpsi = json_encode($node['satuan_opsi'], JSON_UNESCAPED_UNICODE);
                }
                if (!empty($node['rumus_otomatis']) && is_array($node['rumus_otomatis']) && !empty($node['rumus_otomatis']['type'])) {
                    $rumusOtomatis = json_encode($node['rumus_otomatis'], JSON_UNESCAPED_UNICODE);
                    $isReadonly = 1; // Kolom rumus otomatis wajib Read-Only
                }
            }
        }

        $urutan = !empty($node['urutan']) ? (int)$node['urutan'] : 0;
        if ($urutan <= 0) {
            $last = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'DESC')->first();
            $urutan = $last ? ((int)$last['urutan'] + 1) : 1;
        }

        $rowData = [
            'revision_id'     => $revisionId,
            'parent_id'       => $parentId ?: null,
            'nama_parameter'  => trim((string)($node['nama_parameter'] ?? 'Parameter')),
            'kolom_db'        => $kolomDb,
            'tipe_input'      => $tipeInput,
            'batas_min'       => $batasMin,
            'batas_max'       => $batasMax,
            'satuan_opsi'     => $satuanOpsi,
            'is_readonly'     => $isReadonly,
            'is_exclusive'    => $isExclusive,
            'rumus_otomatis'  => $rumusOtomatis,
            'standar_dinamis' => $standarDinamis,
            'standar_nilai'   => !empty($node['standar_nilai']) ? trim($node['standar_nilai']) : null,
            'urutan'          => $urutan,
        ];

        if ($nodeId) {
            $this->formParamModel->protect(false)->update($nodeId, $rowData);
            $savedId = $nodeId;
        } else {
            $this->formParamModel->protect(false)->insert($rowData);
            $savedId = (int)$this->formParamModel->getInsertID();
        }
        $keptIds[] = $savedId;

        // Jika ini adalah kolom daun pada tabel Matriks Foregoing, sinkronkan pengaturan inputnya ke semua baris (X, Y, Z, θ)
        if ($isForegoingLeafColumn) {
            $this->syncForegoingColumnCells($savedId, $revisionId, $node, $gridRows, $hasSuffixMode);
        }

        // Simpan anak-anak di bawahnya secara rekursif (Level 2 -> Level 3 -> Level 4 -> dst.)
        foreach ($children as $idx => $childNode) {
            if (empty($childNode['urutan'])) {
                $childNode['urutan'] = $idx + 1;
            }
            $this->saveNodeRecursive($childNode, $revisionId, $savedId, $keptIds, $gridRows, $hasSuffixMode);
        }
    }

    // Sinkronisasi pengaturan kolom header ke seluruh sel baris matriks Foregoing (X, Y, Z, θ)
    private function syncForegoingColumnCells(int $headerLeafId, int $revisionId, array $colConfig, array $gridRows, bool $hasSuffixMode)
    {
        $existingCells = $this->formParamModel
            ->where('revision_id', $revisionId)
            ->where('parent_id', $headerLeafId)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        $cellMap = [];
        $baseParPrefix = null;
        foreach ($existingCells as $c) {
            if (!empty($c['posisi_grid'])) {
                $cellMap[$c['posisi_grid']] = $c;
            }
            if (!$baseParPrefix && !empty($c['kolom_db']) && preg_match('/^(par\d+)[a-z]$/i', $c['kolom_db'], $m)) {
                $baseParPrefix = strtolower($m[1]);
            }
        }

        if (!$baseParPrefix && $hasSuffixMode) {
            $baseParPrefix = $this->generateNextParCode($revisionId);
        }

        $tipeInput  = $colConfig['tipe_input'] ?: 'number';
        $isReadonly = !empty($colConfig['is_readonly']) ? 1 : 0;
        $batasMin   = (isset($colConfig['batas_min']) && $colConfig['batas_min'] !== '') ? (float)$colConfig['batas_min'] : null;
        $batasMax   = (isset($colConfig['batas_max']) && $colConfig['batas_max'] !== '') ? (float)$colConfig['batas_max'] : null;
        $satuanOpsi = (!empty($colConfig['satuan_opsi']) && is_array($colConfig['satuan_opsi']))
            ? json_encode($colConfig['satuan_opsi'], JSON_UNESCAPED_UNICODE) : null;
        $rumusOtomatis = (!empty($colConfig['rumus_otomatis']) && is_array($colConfig['rumus_otomatis']) && !empty($colConfig['rumus_otomatis']['type']))
            ? json_encode($colConfig['rumus_otomatis'], JSON_UNESCAPED_UNICODE) : null;

        foreach ($gridRows as $rIdx => $rLabel) {
            $suffix = chr(97 + $rIdx); // a, b, c, d...
            if (isset($cellMap[$rLabel])) {
                $this->formParamModel->protect(false)->update($cellMap[$rLabel]['id'], [
                    'tipe_input'     => $tipeInput,
                    'batas_min'      => $batasMin,
                    'batas_max'      => $batasMax,
                    'satuan_opsi'    => $satuanOpsi,
                    'is_readonly'    => $isReadonly,
                    'rumus_otomatis' => $rumusOtomatis,
                ]);
            } else {
                $newColDb = $baseParPrefix ? ($baseParPrefix . $suffix) : $this->generateNextParCode($revisionId);
                $this->formParamModel->protect(false)->insert([
                    'revision_id'    => $revisionId,
                    'parent_id'      => $headerLeafId,
                    'nama_parameter' => $rLabel,
                    'kolom_db'       => $newColDb,
                    'tipe_input'     => $tipeInput,
                    'batas_min'      => $batasMin,
                    'batas_max'      => $batasMax,
                    'satuan_opsi'    => $satuanOpsi,
                    'is_readonly'    => $isReadonly,
                    'rumus_otomatis' => $rumusOtomatis,
                    'posisi_grid'    => $rLabel,
                    'urutan'         => $rIdx + 1
                ]);
            }
        }
    }

    // Legacy single save fallback
    public function saveParameter()
    {
        return $this->saveTreeParameter();
    }

    // 5. AJAX Delete Parameter
    public function deleteParameter()
    {
        $id = $this->request->getPost('id');
        $param = $this->formParamModel->find($id);
        if ($param) {
            $revRow   = $this->formRevModel->find($param['revision_id']);
            $procCode = $revRow['process_code'] ?? '';
            $revNum   = $revRow['revisi'] ?? '-';
            $namaPar  = $param['nama_parameter'] ?? ('ID ' . $id);

            $all = $this->formParamModel->where('revision_id', $param['revision_id'])->findAll();
            $idsToDelete = [];
            $this->collectAllDescendantIds((int)$id, $all, $idsToDelete);
            $this->formParamModel->whereIn('id', $idsToDelete)->delete();

            $jmlHapus = count($idsToDelete);
            $this->catatLog(
                'HAPUS PARAMETER',
                "Menghapus parameter '{$namaPar}' (total {$jmlHapus} item dengan sub-bagiannya) pada Revisi {$revNum}",
                $procCode
            );
        }
        return $this->response->setJSON(['status' => 'success', 'message' => 'Parameter beserta sub-bagiannya berhasil dihapus.']);
    }

    private function collectAllDescendantIds(int $rootId, array $allParams, array &$collected)
    {
        $collected[] = $rootId;
        foreach ($allParams as $p) {
            if ((int)$p['parent_id'] === $rootId) {
                $this->collectAllDescendantIds((int)$p['id'], $allParams, $collected);
            }
        }
    }

    // --- 6. CRUD KHUSUS BARIS SAMPLE / LABEL FOREGOING (X, Y, Z, θ) ---
    public function saveMatrixRow()
    {
        $revisionId = (int)$this->request->getPost('revision_id');
        $mode       = $this->request->getPost('mode'); // 'add', 'edit', atau 'edit_header'
        $rowLabel   = trim((string)$this->request->getPost('row_label'));
        $oldLabel   = trim((string)$this->request->getPost('old_label'));
        $afterRow   = trim((string)$this->request->getPost('after_row'));

        if (!$revisionId || $rowLabel === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama tidak boleh kosong!']);
        }

        // MODE EDIT HEADER: Ubah nama judul kolom "Sample / Label" (misal jadi "Pos")
        if ($mode === 'edit_header') {
            $revRow   = $this->formRevModel->find($revisionId);
            $procCode = $revRow['process_code'] ?? '';

            // Ambil semua revision_id milik process_code ini agar di History (revisi berapa pun) namanya ikut berubah!
            $allRevs = $procCode
                ? $this->formRevModel->where('process_code', $procCode)->findAll()
                : [['id' => $revisionId]];
            $revIds = array_column($allRevs, 'id');

            $db = \Config\Database::connect();
            $db->table('form_parameters')
                ->whereIn('revision_id', !empty($revIds) ? $revIds : [$revisionId])
                ->where('posisi_grid IS NOT NULL', null, false)
                ->where('posisi_grid !=', '')
                ->update(['standar_nilai' => $rowLabel]);

            $this->catatLog(
                'EDIT HEADER FOREGOING',
                "Mengubah nama kolom header matriks dari '{$oldLabel}' menjadi '{$rowLabel}' pada Revisi " . ($revRow['revisi'] ?? '-'),
                $procCode
            );

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => "Nama kolom berhasil diubah menjadi '$rowLabel'."
            ]);
        }

        // MODE EDIT: Ganti nama baris di seluruh kolom matriks
        if ($mode === 'edit' && $oldLabel !== '') {
            $cells = $this->formParamModel
                ->where('revision_id', $revisionId)
                ->where('posisi_grid', $oldLabel)
                ->findAll();

            foreach ($cells as $c) {
                $this->formParamModel->protect(false)->update($c['id'], [
                    'nama_parameter' => $rowLabel,
                    'posisi_grid'    => $rowLabel
                ]);
            }

            $revRow = $this->formRevModel->find($revisionId);
            $this->catatLog('EDIT BARIS FOREGOING', "Mengubah nama baris matriks dari '{$oldLabel}' menjadi '{$rowLabel}' pada Revisi " . ($revRow['revisi'] ?? '-'), $revRow['process_code'] ?? '');
            return $this->response->setJSON(['status' => 'success', 'message' => "Baris '$oldLabel' berhasil diubah menjadi '$rowLabel'."]);
        }

        // MODE ADD: Tambah baris baru (misal menyelipkan baris 'Y') ke seluruh kolom matriks
        $allParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        $gridColumns = []; // parent_id => array of grid cells
        $gridRowNames = [];

        foreach ($allParams as $p) {
            if (!empty($p['posisi_grid']) && !empty($p['parent_id'])) {
                $pid = (int)$p['parent_id'];
                $gridColumns[$pid][] = $p;
                if (!in_array($p['posisi_grid'], $gridRowNames)) {
                    $gridRowNames[] = $p['posisi_grid'];
                }
            }
        }

        if (in_array($rowLabel, $gridRowNames)) {
            return $this->response->setJSON(['status' => 'error', 'message' => "Baris '$rowLabel' sudah ada di tabel ini!"]);
        }

        // Jika tabel Foregoing belum punya baris matriks sama sekali, jadikan semua leaf kolom sebagai induk matriks
        if (empty($gridColumns)) {
            $parentIds = array_filter(array_unique(array_column($allParams, 'parent_id')));
            foreach ($allParams as $p) {
                if (!in_array($p['id'], $parentIds)) {
                    $gridColumns[(int)$p['id']] = [];
                }
            }
        }

        // Tentukan urutan daftar baris baru
        $newRowOrder = [];
        if ($afterRow === '__FIRST__') {
            $newRowOrder[] = $rowLabel;
        }
        foreach ($gridRowNames as $rName) {
            $newRowOrder[] = $rName;
            if ($rName === $afterRow) {
                $newRowOrder[] = $rowLabel;
            }
        }
        if (!in_array($rowLabel, $newRowOrder)) {
            $newRowOrder[] = $rowLabel;
        }

        // Sisipkan sel baru di setiap kolom matriks dan rapikan urutan & akhiran huruf a, b, c, d
        foreach ($gridColumns as $colParentId => $cells) {
            $templateCell = $cells[0] ?? $this->formParamModel->find($colParentId);
            $basePar = null;
            foreach ($cells as $c) {
                if (!empty($c['kolom_db']) && preg_match('/^(par\d+)[a-z]$/i', $c['kolom_db'], $m)) {
                    $basePar = strtolower($m[1]);
                    break;
                }
            }

            $this->formParamModel->protect(false)->insert([
                'revision_id'    => $revisionId,
                'parent_id'      => $colParentId,
                'nama_parameter' => $rowLabel,
                'kolom_db'       => $basePar ? ($basePar . 'z') : $this->generateNextParCode($revisionId),
                'tipe_input'     => $templateCell['tipe_input'] ?: 'number',
                'batas_min'      => $templateCell['batas_min'] ?? null,
                'batas_max'      => $templateCell['batas_max'] ?? null,
                'satuan_opsi'    => $templateCell['satuan_opsi'] ?? null,
                'is_readonly'    => $templateCell['is_readonly'] ?? 0,
                'posisi_grid'    => $rowLabel,
                'urutan'         => 999
            ]);

            // Jika kolom induk tadinya memegang kolom_db sendiri, kosongkan agar murni menjadi header kolom
            $this->formParamModel->protect(false)->update($colParentId, [
                'kolom_db'   => null,
                'tipe_input' => null
            ]);
        }

        // Urutkan ulang urutan & huruf suffix (a, b, c, d) sesuai urutan baris terbaru
        $this->reorderMatrixRowsInternal($revisionId, $newRowOrder);

        $revRow = $this->formRevModel->find($revisionId);
        $this->catatLog('TAMBAH BARIS FOREGOING', "Menambahkan baris matriks baru '{$rowLabel}' pada Revisi " . ($revRow['revisi'] ?? '-'), $revRow['process_code'] ?? '');

        return $this->response->setJSON(['status' => 'success', 'message' => "Baris '$rowLabel' berhasil ditambahkan!"]);
    }

    public function deleteMatrixRow()
    {
        $revisionId = (int)$this->request->getPost('revision_id');
        $rowLabel   = trim((string)$this->request->getPost('row_label'));

        if (!$revisionId || $rowLabel === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Baris tidak valid!']);
        }

        $this->formParamModel
            ->where('revision_id', $revisionId)
            ->where('posisi_grid', $rowLabel)
            ->delete();

        // Rapikan kembali suffix a, b, c setelah baris dihapus
        $remaining = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        $rowOrder = [];
        foreach ($remaining as $r) {
            if (!empty($r['posisi_grid']) && !in_array($r['posisi_grid'], $rowOrder)) {
                $rowOrder[] = $r['posisi_grid'];
            }
        }
        if (!empty($rowOrder)) {
            $this->reorderMatrixRowsInternal($revisionId, $rowOrder);
        }

        $revRow = $this->formRevModel->find($revisionId);
        $this->catatLog('HAPUS BARIS FOREGOING', "Menghapus baris matriks '{$rowLabel}' pada Revisi " . ($revRow['revisi'] ?? '-'), $revRow['process_code'] ?? '');

        return $this->response->setJSON(['status' => 'success', 'message' => "Baris '$rowLabel' berhasil dihapus."]);
    }

    public function moveMatrixRow()
    {
        $revisionId = (int)$this->request->getPost('revision_id');
        $rowLabel   = trim((string)$this->request->getPost('row_label'));
        $direction  = $this->request->getPost('direction'); // 'up' atau 'down'

        $allParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        $rowOrder = [];
        foreach ($allParams as $p) {
            if (!empty($p['posisi_grid']) && !in_array($p['posisi_grid'], $rowOrder)) {
                $rowOrder[] = $p['posisi_grid'];
            }
        }

        $idx = array_search($rowLabel, $rowOrder);
        if ($idx !== false) {
            $targetIdx = ($direction === 'up') ? $idx - 1 : $idx + 1;
            if (isset($rowOrder[$targetIdx])) {
                $temp = $rowOrder[$idx];
                $rowOrder[$idx] = $rowOrder[$targetIdx];
                $rowOrder[$targetIdx] = $temp;
                $this->reorderMatrixRowsInternal($revisionId, $rowOrder);
            }
        }

        $revRow = $this->formRevModel->find($revisionId);
        $arah   = ($direction === 'up') ? 'ke atas' : 'ke bawah';
        $this->catatLog('GESER BARIS FOREGOING', "Menggeser posisi baris matriks '{$rowLabel}' {$arah} pada Revisi " . ($revRow['revisi'] ?? '-'), $revRow['process_code'] ?? '');
        
        return $this->response->setJSON(['status' => 'success']);
    }

    private function reorderMatrixRowsInternal(int $revisionId, array $newRowOrder)
    {
        $allParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        $gridColumns = [];
        foreach ($allParams as $p) {
            if (!empty($p['posisi_grid']) && !empty($p['parent_id'])) {
                $gridColumns[(int)$p['parent_id']][$p['posisi_grid']] = $p;
            }
        }

        $urutanCounter = 1000;
        foreach ($gridColumns as $colParentId => $cellMap) {
            $basePar = null;
            foreach ($cellMap as $c) {
                if (!empty($c['kolom_db']) && preg_match('/^(par\d+)[a-z]$/i', $c['kolom_db'], $m)) {
                    $basePar = strtolower($m[1]);
                    break;
                }
            }

            foreach ($newRowOrder as $rIdx => $rLabel) {
                if (isset($cellMap[$rLabel])) {
                    $cell = $cellMap[$rLabel];
                    $suffix = chr(97 + $rIdx); // a, b, c, d...
                    $updateData = ['urutan' => $urutanCounter++];
                    if ($basePar) {
                        $updateData['kolom_db'] = $basePar . $suffix;
                    }
                    $this->formParamModel->protect(false)->update($cell['id'], $updateData);
                }
            }
        }
    }

    // 7. AJAX Naik Revisi (Mengunci Data Lama & Sinkron ke Tabel Master Proses)
    public function upRevisi()
    {
        $oldRevisionId = $this->request->getPost('revision_id');
        $oldRevision   = $this->formRevModel->find($oldRevisionId);

        if (!$oldRevision) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Revisi lama tidak ditemukan!']);
        }

        $procCode      = $oldRevision['process_code'];
        $oldRevisiCode = (string)$oldRevision['revisi'];
        $newRevisiCode = is_numeric($oldRevisiCode) ? ((int)$oldRevisiCode + 1) : ($oldRevisiCode . '_new');

        $db = \Config\Database::connect();

        // 1. KUNCI DATA LAMA: Semua transaksi pada proses ini yang kolom revisinya masih kosong/NULL
        // langsung diberi stempel revisi lama ($oldRevisiCode) agar TIDAK BOCOR ke revisi baru!
        foreach (['startup', 'production', 'foregoing'] as $tbl) {
            try {
                if ($db->tableExists($tbl) && $db->fieldExists('revisi', $tbl)) {
                    $db->table($tbl)
                        ->where('process', $procCode)
                        ->groupStart()
                            ->where('revisi IS NULL', null, false)
                            ->orWhere('revisi', '')
                            ->orWhere('revisi', '-')
                        ->groupEnd()
                        ->update(['revisi' => $oldRevisiCode]);
                }
            } catch (\Throwable $e) {
                log_message('error', "Gagal mengunci revisi lama pada {$tbl}: " . $e->getMessage());
            }
        }

        // 2. SINKRONKAN TABEL MASTER PROSES: Update kolom revisi (dan docno jika memiliki segmen revisi)
        try {
            $procMaster = $db->table('proses')->where('process_code', $procCode)->get()->getRowArray();
            if ($procMaster) {
                $updateProses = ['revisi' => (string)$newRevisiCode];
                if (!empty($procMaster['docno'])) {
                    $dParts = explode('-', $procMaster['docno']);
                    if (count($dParts) >= 4 && is_numeric(end($dParts))) {
                        $dParts[count($dParts) - 1] = (string)$newRevisiCode;
                        $updateProses['docno'] = implode('-', $dParts);
                    }
                }
                $db->table('proses')->where('process_code', $procCode)->update($updateProses);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal update revisi di tabel proses: ' . $e->getMessage());
        }

        // 3. Buat Revisi Baru di form_revisions
        $this->formRevModel->protect(false)->insert([
            'process_code'  => $procCode,
            'revisi'        => (string)$newRevisiCode,
            'kategori_form' => $oldRevision['kategori_form'] ?? 'production',
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $newRevisionId = $this->formRevModel->getInsertID();

        // 4. Gandakan struktur parameter ke revisi baru
        $oldParameters = $this->formParamModel->where('revision_id', $oldRevisionId)->orderBy('urutan', 'ASC')->findAll();
        $tree = $this->buildTree($oldParameters);

        $duplicateRecursive = function (array $nodes, $newParentId = null) use (&$duplicateRecursive, $newRevisionId) {
            foreach ($nodes as $node) {
                $children = $node['children'] ?? [];
                unset($node['id'], $node['children']);
                $node['revision_id'] = $newRevisionId;
                $node['parent_id']   = $newParentId;

                $this->formParamModel->protect(false)->insert($node);
                $insertedId = $this->formParamModel->getInsertID();

                if (!empty($children)) {
                    $duplicateRecursive($children, $insertedId);
                }
            }
        };

        $duplicateRecursive($tree, null);

        $this->catatLog(
            'NAIK REVISI',
            "Menaikkan versi revisi dokumen dari Revisi {$oldRevisiCode} menjadi Revisi {$newRevisiCode} (data lama telah dikunci pada Revisi {$oldRevisiCode})",
            $procCode
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Revisi berhasil dinaikkan menjadi Revisi {$newRevisiCode}. Seluruh data riwayat sebelumnya telah dikunci pada Revisi {$oldRevisiCode}."
        ]);
    }

    // 8. Endpoint Render Tabel Dinamis untuk Halaman Input & Edit Operator
    public function renderOperatorForm()
    {
        $processCode  = trim((string)($this->request->getPost('process') ?: $this->request->getGet('process')));
        $selectedType = trim((string)($this->request->getPost('doc_type') ?: $this->request->getGet('doc_type')));

        if (!$processCode) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Process code kosong']);
        }

        $res = $this->buildOperatorFormPayload($processCode, $selectedType, null, null, null);
        return $this->response->setJSON($res);
    }

    /**
     * Helper publik untuk merender HTML Form Builder pada halaman Edit (Production, Startup, Foregoing)
     * sesuai dengan versi Revisi saat dokumen tersebut dibuat!
     */
    public function buildOperatorFormPayload(string $processCode, string $selectedType = '', ?array $alldata = null, $number = null, ?string $targetRevisi = null): array
    {
        if (!empty($selectedType) && in_array($selectedType, ['startup', 'startup_horizontal', 'production', 'foregoing'])) {
            $detectedKategori = $selectedType;
        } else {
            $parts    = explode('-', strtolower($processCode));
            $typeKode = $parts[1] ?? 'p';
            $detectedKategori = ($typeKode === 's') ? 'startup' : (($typeKode === 'f') ? 'foregoing' : 'production');
        }

        // Jika sedang mengedit dokumen lama yang memiliki nomor revisi spesifik, cari revisi yang sesuai
        $revision = null;
        if ($targetRevisi !== null && $targetRevisi !== '' && $targetRevisi !== '-') {
            $revs = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->findAll();
            foreach ($revs as $rItem) {
                if (
                    strcasecmp((string)$rItem['revisi'], $targetRevisi) === 0 ||
                    (is_numeric($rItem['revisi']) && is_numeric($targetRevisi) && (int)$rItem['revisi'] === (int)$targetRevisi)
                ) {
                    $revision = $rItem;
                    break;
                }
            }
        }

        if (!$revision) {
            $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
        }

        if (!$revision) {
            $procRow = $this->processModel->where('process_code', $processCode)->first();
            $revCode = $procRow['revisi'] ?? '0';
            $this->formRevModel->protect(false)->insert([
                'process_code'  => $processCode,
                'revisi'        => $revCode,
                'kategori_form' => $detectedKategori,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revisionId = $this->formRevModel->getInsertID();
            $revision   = $this->formRevModel->find($revisionId);
        } else {
            $revisionId = $revision['id'];
        }

        $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        if (empty($rawParams)) {
            $this->parseSingleProcess($processCode, $revisionId, $detectedKategori);
            $revision  = $this->formRevModel->find($revisionId);
            $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        }

        $treeParams    = $this->buildTree($rawParams);
        $maxTableDepth = 0;
        foreach ($treeParams as $node) {
            $d = $this->calculateDepth($node);
            if ($d > $maxTableDepth) $maxTableDepth = $d;
        }

        $procInfo = $this->processModel->where('process_code', $processCode)->first();

        $viewData = [
            'process_code'  => $processCode,
            'process_name'  => $procInfo['name'] ?? $processCode,
            'revision'      => $revision,
            'kategori_form' => $revision['kategori_form'] ?? $detectedKategori,
            'treeParams'    => $treeParams,
            'maxTableDepth' => max(1, $maxTableDepth),
            'alldata'       => $alldata,
            'number'        => $number,
        ];

        $html = view('form_input_dynamic', $viewData);

        return [
            'status'      => 'success',
            'revisi'      => $revision['revisi'],
            'revision_id' => $revision['id'],
            'html'        => $html
        ];
    }

    /**
     * Helper publik untuk merender Tabel Detail (History per Number) di Modal Operations (Production, Startup, Foregoing)
     */
    public function renderDetailTableByRows(string $processCode, string $typeProcess, array $alldata): string
    {
        $rowRevisi = isset($alldata[0]['revisi']) ? trim((string)$alldata[0]['revisi']) : '';
        $revs      = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->findAll();

        if (empty($revs)) {
            $procRow = $this->processModel->where('process_code', $processCode)->first();
            $this->formRevModel->protect(false)->insert([
                'process_code'  => $processCode,
                'revisi'        => (string)($procRow['revisi'] ?? '0'),
                'kategori_form' => $typeProcess,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revs = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->findAll();
        }

        $activeRevRow = $revs[0];
        if ($rowRevisi !== '' && $rowRevisi !== '-') {
            foreach ($revs as $rItem) {
                if (
                    strcasecmp((string)$rItem['revisi'], $rowRevisi) === 0 ||
                    (is_numeric($rItem['revisi']) && is_numeric($rowRevisi) && (int)$rItem['revisi'] === (int)$rowRevisi)
                ) {
                    $activeRevRow = $rItem;
                    break;
                }
            }
        }

        $rawParams = $this->formParamModel->where('revision_id', $activeRevRow['id'])->orderBy('urutan', 'ASC')->findAll();
        if (empty($rawParams)) {
            $this->parseSingleProcess($processCode, $activeRevRow['id'], $typeProcess);
            $activeRevRow = $this->formRevModel->find($activeRevRow['id']);
            $rawParams    = $this->formParamModel->where('revision_id', $activeRevRow['id'])->orderBy('urutan', 'ASC')->findAll();
        }

        $treeParams = $this->buildTree($rawParams);
        $procInfo   = $this->processModel->where('process_code', $processCode)->first();
        $revLabel   = str_pad((string)($activeRevRow['revisi'] ?? '0'), 2, '0', STR_PAD_LEFT);

        return view('history_table_dynamic', [
            'process_code'  => $processCode,
            'judul_proses'  => $procInfo['name'] ?? $processCode,
            'kategori_form' => $activeRevRow['kategori_form'] ?? $typeProcess,
            'revisi_label'  => $revLabel,
            'treeParams'    => $treeParams,
            'alldata'       => $alldata,
            'is_export'     => false
        ]);
    }

    // --- MESIN PEMBEDAH HTML V6 (OTOMATIS DETEKSI 5 FITUR BARU DARI FILE ASLI) ---
    private function parseSingleProcess($processCode, $revisionId, $kategori)
    {
        $cleanCode = strtolower(trim($processCode));
        $parts = explode('-', $cleanCode);
        $device = $parts[0] ?? '';

        $candidatePaths = [
            APPPATH . "Views/layout/{$device}/input/{$kategori}/{$cleanCode}.php",
            APPPATH . "Views/layout/{$device}/input/production/{$cleanCode}.php",
            APPPATH . "Views/layout/{$device}/input/startup/{$cleanCode}.php",
            APPPATH . "Views/layout/{$device}/input/foregoing/{$cleanCode}.php",
            APPPATH . "Views/layout/{$device}/history/{$kategori}/{$cleanCode}.php",
        ];

        $filePath = null;
        foreach ($candidatePaths as $path) {
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath && is_dir(APPPATH . "Views/layout/{$device}")) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(APPPATH . "Views/layout/{$device}")
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getFilename()) === $cleanCode . '.php') {
                    if (stripos($file->getPathname(), 'input') !== false) {
                        $filePath = $file->getPathname();
                        break;
                    }
                    if (!$filePath) $filePath = $file->getPathname();
                }
            }
        }

        if (!$filePath || !file_exists($filePath)) {
            return false;
        }

        $rawSource = file_get_contents($filePath);

        // Deteksi Standar Dinamis (seperti Jenis SM-Chip -> label_die_bond_std di fl18-p-db2.php)
        $detectedDynamicStd = null;
        if (stripos($rawSource, 'label_die_bond_std') !== false && stripos($rawSource, 'sm_chip_type') !== false) {
            $detectedDynamicStd = json_encode([
                'label_dropdown' => 'Jenis SM-Chip',
                'db_col'         => 'std_die_bond',
                'prefix_text'    => 'Min: ',
                'suffix_text'    => ' N',
                'options'        => [
                    ['label' => '350 μm', 'value' => '350', 'min' => 2.8],
                    ['label' => '450 μm', 'value' => '450', 'min' => 3.6],
                    ['label' => '850 μm', 'value' => '850', 'min' => 8.7],
                ]
            ], JSON_UNESCAPED_UNICODE);
        }

        // Peta limit satuan dinamis dari script validateDynamicUnit (seperti di sl56-s-db2.php)
        $unitLimitsMap = [
            'par015' => [['unit' => 'N', 'min' => 0.60, 'max' => 1.00], ['unit' => 'gr', 'min' => 60, 'max' => 100]],
            'par016' => [['unit' => 'N', 'min' => 0.60, 'max' => 1.40], ['unit' => 'gr', 'min' => 60, 'max' => 140]],
            'par018' => [['unit' => 'MPa', 'min' => 0.01, 'max' => 0.10], ['unit' => 'kPa', 'min' => 10, 'max' => 100], ['unit' => 'kgf/cm^2', 'min' => 0.10, 'max' => 1]],
            'par019' => [['unit' => 'MPa', 'min' => 0.005, 'max' => 0.10], ['unit' => 'kPa', 'min' => 5, 'max' => 100], ['unit' => 'kgf/cm^2', 'min' => 0.05, 'max' => 1]],
            'par020' => [['unit' => 'sec', 'min' => 0.01, 'max' => 0.15], ['unit' => 'ms', 'min' => 10, 'max' => 150]],
            'par021' => [['unit' => 'sec', 'min' => 0.005, 'max' => 0.15], ['unit' => 'ms', 'min' => 5, 'max' => 150]],
        ];

        $htmlContent = preg_replace('/<\?(?:php|=)\s*(?:echo\s+)?nl2br\(\s*[\'"](.*?)[\'"]\s*\)\s*;?\s*\?>/is', '$1', $rawSource);
        $htmlContent = preg_replace('/<\?(?:php|=).*?\?>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<!--.*?-->/is', '', $htmlContent);

        $htmlContent = preg_replace_callback('/<tbody\b[^>]*>(.*?)<\/tbody>/is', function ($m) {
            $inner = trim($m[1]);
            if ($inner === '') return $m[0];
            if (stripos($inner, '<tr') === false && preg_match('/<(td|th)\b/i', $inner)) {
                return "<tbody>\n<tr>\n" . $inner . "\n</tr>\n</tbody>";
            }
            $firstTrPos   = stripos($inner, '<tr');
            $firstCellPos = stripos($inner, '<td');
            if ($firstCellPos !== false && ($firstTrPos === false || $firstCellPos < $firstTrPos)) {
                $beforeTr = substr($inner, 0, $firstTrPos);
                $fromTr   = substr($inner, $firstTrPos);
                return "<tbody>\n<tr>\n" . $beforeTr . "\n</tr>\n" . $fromTr . "\n</tbody>";
            }
            return $m[0];
        }, $htmlContent);

        if (stripos($htmlContent, '<table') === false && preg_match('/<(thead|tbody|tr|td|th)\b/i', $htmlContent)) {
            $htmlContent = "<table>\n" . $htmlContent . "\n</table>";
        }

        $this->formParamModel->where('revision_id', $revisionId)->delete();

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $tables = $xpath->query('//table');
        $urutan = 1;
        $dbIdMapping = [];

        foreach ($tables as $table) {
            $theadRowsRaw = $xpath->query('./thead/tr', $table);
            $tbodyRowsRaw = $xpath->query('./tbody/tr | ./tr', $table);

            $theadRows = [];
            foreach ($theadRowsRaw as $tIdx => $tr) {
                $cells = $xpath->query('./th | ./td', $tr);
                if ($cells->length === 0) continue;
                // Hanya abaikan baris judul mesin utama (1 sel dengan colspan besar >= 10)
                if ($cells->length === 1 && (int)$cells->item(0)->getAttribute('colspan') >= 10) {
                    continue;
                }
                $theadRows[] = $tr;
            }

            $tbodyRows = [];
            foreach ($tbodyRowsRaw as $tr) {
                if ($xpath->query('./th | ./td', $tr)->length > 0) {
                    $tbodyRows[] = $tr;
                }
            }

            if (empty($theadRows) && empty($tbodyRows)) continue;

            $matrix = [];
            $cellList = [];
            $rowIndex = 0;
            $maxCols = 0;
            $numTheadRows = count($theadRows);

            $processRowGroup = function (array $rowsGroup, bool $isHeaderGroup) use (
                &$matrix,
                &$cellList,
                &$rowIndex,
                &$maxCols,
                $numTheadRows,
                $xpath,
                $unitLimitsMap,
                $detectedDynamicStd
            ) {
                foreach ($rowsGroup as $tr) {
                    $colIndex = 0;
                    foreach ($tr->childNodes as $cell) {
                        if ($cell->nodeName !== 'th' && $cell->nodeName !== 'td') continue;

                        while (isset($matrix[$rowIndex][$colIndex])) {
                            $colIndex++;
                        }

                        $colspan = max(1, (int)$cell->getAttribute('colspan'));
                        $rowspan = max(1, (int)$cell->getAttribute('rowspan'));

                        if ($isHeaderGroup && $numTheadRows > 0) {
                            $maxAllowedSpan = max(1, $numTheadRows - $rowIndex);
                            if ($rowspan > $maxAllowedSpan) {
                                $rowspan = $maxAllowedSpan;
                            }
                        }

                        $parInputs = $xpath->query('.//input[contains(@name, "par")] | .//select[contains(@name, "par")] | .//textarea[contains(@name, "par")]', $cell);
                        $anyInputs = $xpath->query('.//input[not(@type="hidden" or @type="submit" or @type="button" or @type="reset")] | .//select | .//textarea', $cell);

                        $isLeaf         = ($parInputs->length > 0 || $anyInputs->length > 0);
                        $dbCol          = null;
                        $inputType      = 'text';
                        $batasMin       = null;
                        $batasMax       = null;
                        $stdNilai       = null;
                        $satuanOpsi     = null;
                        $isReadonly     = 0;
                        $isExclusive    = 0;
                        $rumusOtomatis  = null;
                        $standarDinamis = null;
                        $name           = '';

                        $cellHtml = $cell->ownerDocument->saveHTML($cell);

                        if ($isLeaf) {
                            $mainNode = ($parInputs->length > 0) ? $parInputs->item(0) : $anyInputs->item(0);
                            $dbCol = $mainNode->getAttribute('name') ?: $mainNode->getAttribute('id');
                            if ($dbCol) {
                                $dbCol = preg_replace('/\[.*\]$/', '', $dbCol);
                                $dbCol = preg_replace('/_(val|unit)$/i', '', $dbCol);
                            }

                            $visibleNode = ($anyInputs->length > 0) ? $anyInputs->item(0) : $mainNode;
                            if ($mainNode->nodeName === 'select' || ($visibleNode->nodeName === 'select' && $anyInputs->length === 1)) {
                                $inputType = 'dropdown_ok_ng';
                            } elseif ($mainNode->nodeName === 'textarea' || $visibleNode->nodeName === 'textarea') {
                                $inputType = 'textarea';
                            } else {
                                $typeAttr = strtolower($visibleNode->getAttribute('type'));
                                if ($typeAttr === 'number' || $visibleNode->hasAttribute('step') || in_array($dbCol, ['par013', 'par014', 'par015', 'par016'])) {
                                    $inputType = 'number';
                                }
                            }

                            // 1. Deteksi Read-Only
                            if ($visibleNode->hasAttribute('readonly') || $mainNode->hasAttribute('readonly')) {
                                $isReadonly = 1;
                            }

                            // 2. Deteksi Exclusive (Tidak boleh isi keduanya!)
                            if (stripos($cellHtml, 'Tidak boleh isi keduanya') !== false) {
                                $isExclusive = 1;
                            }

                            // 3. Deteksi Batas Min & Max dari changeRange
                            if (preg_match('/changeRange\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/i', $cellHtml, $mRange)) {
                                $minVal = (float)$mRange[1];
                                $maxVal = (float)$mRange[2];
                                if ($minVal > -999999) $batasMin = $minVal;
                                if ($maxVal < 999999)  $batasMax = $maxVal;
                            }

                            // 4. Deteksi Dropdown Satuan (satuan_opsi)
                            $unitSelects = $xpath->query('.//select[contains(@id, "_unit")]/option', $cell);
                            if ($unitSelects->length > 0) {
                                if ($dbCol && isset($unitLimitsMap[$dbCol])) {
                                    $satuanOpsi = json_encode($unitLimitsMap[$dbCol], JSON_UNESCAPED_UNICODE);
                                } else {
                                    $uList = [];
                                    foreach ($unitSelects as $opt) {
                                        $uVal = trim($opt->textContent);
                                        if ($uVal !== '') {
                                            $uList[] = ['unit' => $uVal, 'min' => $batasMin, 'max' => $batasMax];
                                        }
                                    }
                                    if (!empty($uList)) {
                                        $satuanOpsi = json_encode($uList, JSON_UNESCAPED_UNICODE);
                                    }
                                }
                            }
                        } else {
                            $clone = $cell->cloneNode(true);
                            foreach ($xpath->query('.//select | .//script | .//style', $clone) as $rem) {
                                $rem->parentNode->removeChild($rem);
                            }
                            $name = trim(preg_replace('/\s+/', ' ', $clone->textContent));

                            // 5. Deteksi Header Standar Dinamis (label_die_bond_std / Min: - N)
                            if ($detectedDynamicStd && (stripos($cellHtml, 'label_die_bond_std') !== false || preg_match('/Min:\s*-\s*N/i', $name))) {
                                $standarDinamis = $detectedDynamicStd;
                            }
                        }

                        $cellId = uniqid('c_', true);
                        $cellList[$cellId] = [
                            'id'             => $cellId,
                            'name'           => $name,
                            'dbCol'          => $dbCol,
                            'inputType'      => $inputType,
                            'batasMin'       => $batasMin,
                            'batasMax'       => $batasMax,
                            'stdNilai'       => $stdNilai,
                            'satuanOpsi'     => $satuanOpsi,
                            'isReadonly'     => $isReadonly,
                            'isExclusive'    => $isExclusive,
                            'rumusOtomatis'  => $rumusOtomatis,
                            'standarDinamis' => $standarDinamis,
                            'isLeaf'         => $isLeaf,
                            'isHeader'       => $isHeaderGroup,
                            'r'              => $rowIndex,
                            'c'              => $colIndex,
                            'rowspan'        => $rowspan,
                            'colspan'        => $colspan
                        ];

                        for ($r = 0; $r < $rowspan; $r++) {
                            for ($c = 0; $c < $colspan; $c++) {
                                $matrix[$rowIndex + $r][$colIndex + $c] = $cellId;
                            }
                        }
                        $colIndex += $colspan;
                        if ($colIndex > $maxCols) $maxCols = $colIndex;
                    }
                    $rowIndex++;
                }
            };

            $processRowGroup($theadRows, true);
            $processRowGroup($tbodyRows, false);

            // Deteksi otomatis apakah file Startup ini berformat MENDATAR (Horizontal) seperti sp56-s-ag atau Burn In
            if ($kategori === 'startup') {
                $inputsPerRowCount = [];
                foreach ($cellList as $cNode) {
                    if ($cNode['isLeaf']) {
                        $inputsPerRowCount[$cNode['r']] = ($inputsPerRowCount[$cNode['r']] ?? 0) + 1;
                    }
                }
                $maxInputsInOneRow = !empty($inputsPerRowCount) ? max($inputsPerRowCount) : 1;
                if ($maxInputsInOneRow > 1) {
                    $kategori = 'startup_horizontal';
                    $this->formRevModel->protect(false)->update($revisionId, ['kategori_form' => 'startup_horizontal']);
                }
            }

            // --- ARAH 1: STARTUP MENURUN (VERTIKAL) ---
            if ($kategori === 'startup') {
                foreach ($cellList as $cellId => $leafCell) {
                    if (!$leafCell['isLeaf']) continue;
                    $r = $leafCell['r'];
                    $cInput = $leafCell['c'];

                    $chain = [];
                    for ($c = 0; $c < $cInput; $c++) {
                        if (!isset($matrix[$r][$c])) continue;
                        $leftId = $matrix[$r][$c];
                        if (in_array($leftId, $chain)) continue;

                        $leftNode = $cellList[$leftId];
                        if ($leftNode['isLeaf'] || $leftNode['name'] === '') continue;
                        if ($leftNode['c'] === 0 && is_numeric($leftNode['name'])) continue;

                        $chain[] = $leftId;
                    }

                    $parentId = null;
                    $totalChain = count($chain);
                    foreach ($chain as $idx => $chId) {
                        $chNode = $cellList[$chId];
                        $isLastText = ($idx === $totalChain - 1);

                        if ($isLastText) {
                            $this->formParamModel->protect(false)->insert([
                                'revision_id'    => $revisionId,
                                'parent_id'      => $parentId,
                                'nama_parameter' => $chNode['name'],
                                'kolom_db'       => $leafCell['dbCol'],
                                'tipe_input'     => $leafCell['inputType'],
                                'batas_min'      => $leafCell['batasMin'],
                                'batas_max'      => $leafCell['batasMax'],
                                'satuan_opsi'    => $leafCell['satuanOpsi'],
                                'is_readonly'    => $leafCell['isReadonly'],
                                'standar_nilai'  => $leafCell['stdNilai'],
                                'urutan'         => $urutan++
                            ]);
                            if ($leafCell['isExclusive'] && $parentId) {
                                $this->formParamModel->protect(false)->update($parentId, ['is_exclusive' => 1]);
                            }
                        } else {
                            if (!isset($dbIdMapping[$chId])) {
                                $this->formParamModel->protect(false)->insert([
                                    'revision_id'    => $revisionId,
                                    'parent_id'      => $parentId,
                                    'nama_parameter' => $chNode['name'],
                                    'kolom_db'       => null,
                                    'tipe_input'     => null,
                                    'urutan'         => $urutan++
                                ]);
                                $dbIdMapping[$chId] = $this->formParamModel->getInsertID();
                            }
                            $parentId = $dbIdMapping[$chId];
                        }
                    }
                }
            }
            // --- ARAH 2 & 3: STARTUP MENDATAR, PRODUCTION, & FOREGOING ---
            else {
                $inputsPerCol = [];
                $firstInputRow = [];
                foreach ($cellList as $cellId => $node) {
                    // Untuk Startup Mendatar (seperti Burn In), sel teks di tbody (Start up Checking Item / Standard)
                    // juga diperlakukan sebagai kolom daun (header_only) agar ikut tampil di baris tabel!
                    $isStaticBodyCol = ($kategori === 'startup_horizontal' && !$node['isHeader'] && !$node['isLeaf'] && $node['name'] !== '');
                    if ($node['isLeaf'] || $isStaticBodyCol) {
                        $c = $node['c'];
                        $inputsPerCol[$c] = ($inputsPerCol[$c] ?? 0) + 1;
                        if (!isset($firstInputRow[$c]) || $node['r'] < $firstInputRow[$c]) {
                            $firstInputRow[$c] = $node['r'];
                        }
                    }
                }

                // Urutkan berdasarkan kolom kiri ke kanan agar urutan kolom mendatar selalu presisi
                $bodyLeaves = [];
                foreach ($cellList as $cellId => $node) {
                    $isStaticBodyCol = ($kategori === 'startup_horizontal' && !$node['isHeader'] && !$node['isLeaf'] && $node['name'] !== '');
                    if ($node['isLeaf'] || $isStaticBodyCol) {
                        $node['_isStaticBody'] = $isStaticBodyCol;
                        $bodyLeaves[] = $node;
                    }
                }
                usort($bodyLeaves, function ($a, $b) {
                    if ($a['c'] === $b['c']) return $a['r'] <=> $b['r'];
                    return $a['c'] <=> $b['c'];
                });

                foreach ($bodyLeaves as $leafCell) {
                    $rInput = $leafCell['r'];
                    $c = $leafCell['c'];
                    $topLimit = $firstInputRow[$c] ?? $rInput;

                    $chain = [];
                    for ($r = 0; $r < $topLimit; $r++) {
                        if (!isset($matrix[$r][$c])) continue;
                        $upId = $matrix[$r][$c];
                        if (in_array($upId, $chain)) continue;

                        $upNode = $cellList[$upId];
                        if ($upNode['isLeaf'] || $upNode['name'] === '') continue;

                        $chain[] = $upId;
                    }

                    $parentId = null;
                    $totalChain = count($chain);
                    $isMultiRowCol = (($inputsPerCol[$c] ?? 1) > 1);

                    foreach ($chain as $idx => $chId) {
                        $chNode = $cellList[$chId];
                        $isLastHeader = ($idx === $totalChain - 1);

                        if ($isLastHeader && !$isMultiRowCol) {
                            $autoFormula = null;
                            $isRo = $leafCell['isReadonly'];
                            $dbCol = $leafCell['dbCol'];
                            $hNameLower = strtolower($chNode['name']);

                            if ($kategori === 'production') {
                                if ($dbCol === 'par015' && ($hNameLower === 'ng' || $hNameLower === 'others')) {
                                    $autoFormula = json_encode(['type' => 'ng', 'in_col' => 'par013', 'out_col' => 'par014']);
                                    $isRo = 1;
                                } elseif ($dbCol === 'par016' && strpos($hNameLower, 'yield') !== false) {
                                    $autoFormula = json_encode(['type' => 'yield', 'in_col' => 'par013', 'out_col' => 'par014']);
                                    $isRo = 1;
                                } elseif ($dbCol === 'par019' && $hNameLower === 'ng') {
                                    $autoFormula = json_encode(['type' => 'ng', 'in_col' => 'par017', 'out_col' => 'par018']);
                                    $isRo = 1;
                                } elseif ($dbCol === 'par020' && $hNameLower === '%') {
                                    $autoFormula = json_encode(['type' => 'yield', 'in_col' => 'par017', 'out_col' => 'par018']);
                                    $isRo = 1;
                                }
                            }

                            $finalTipeInput = !empty($leafCell['_isStaticBody']) ? 'header_only' : $leafCell['inputType'];
                            $finalStdNilai  = !empty($leafCell['_isStaticBody']) ? $leafCell['name'] : $leafCell['stdNilai'];

                            $this->formParamModel->protect(false)->insert([
                                'revision_id'     => $revisionId,
                                'parent_id'       => $parentId,
                                'nama_parameter'  => $chNode['name'],
                                'kolom_db'        => $dbCol,
                                'tipe_input'      => $finalTipeInput,
                                'batas_min'       => $leafCell['batasMin'],
                                'batas_max'       => $leafCell['batasMax'],
                                'satuan_opsi'     => $leafCell['satuanOpsi'],
                                'is_readonly'     => $isRo,
                                'rumus_otomatis'  => $autoFormula,
                                'standar_dinamis' => $chNode['standarDinamis'],
                                'standar_nilai'   => $finalStdNilai,
                                'urutan'          => $urutan++
                            ]);
                            $parentId = $this->formParamModel->getInsertID();
                        } else {
                            if (!isset($dbIdMapping[$chId])) {
                                $this->formParamModel->protect(false)->insert([
                                    'revision_id'     => $revisionId,
                                    'parent_id'       => $parentId,
                                    'nama_parameter'  => $chNode['name'],
                                    'kolom_db'        => null,
                                    'tipe_input'      => null,
                                    'standar_dinamis' => $chNode['standarDinamis'],
                                    'urutan'          => $urutan++
                                ]);
                                $dbIdMapping[$chId] = $this->formParamModel->getInsertID();
                            }
                            $parentId = $dbIdMapping[$chId];
                        }
                    }

                    if ($isMultiRowCol || empty($chain)) {
                        $rowLabel = '';
                        for ($cLeft = $c - 1; $cLeft >= 0; $cLeft--) {
                            if (!isset($matrix[$rInput][$cLeft])) continue;
                            $leftNode = $cellList[$matrix[$rInput][$cLeft]];
                            if (!$leftNode['isLeaf'] && $leftNode['name'] !== '' && $leftNode['rowspan'] === 1) {
                                $rowLabel = $leftNode['name'];
                                break;
                            }
                        }

                        if ($rowLabel === '' && !empty($leafCell['dbCol']) && preg_match('/^par\d+([a-z])$/i', $leafCell['dbCol'], $mSuf)) {
                            $suf = strtolower($mSuf[1]);
                            $defaultAxisMap = ['a' => 'X', 'b' => 'Y', 'c' => 'Z', 'd' => 'θ'];
                            $rowLabel = $defaultAxisMap[$suf] ?? ('Sample ' . (ord($suf) - 96));
                        }

                        if ($rowLabel === '') {
                            $rowLabel = strtoupper($leafCell['dbCol'] ?: 'ROW_' . $urutan);
                        }

                        $this->formParamModel->protect(false)->insert([
                            'revision_id'    => $revisionId,
                            'parent_id'      => $parentId,
                            'nama_parameter' => $rowLabel,
                            'kolom_db'       => $leafCell['dbCol'],
                            'tipe_input'     => $leafCell['inputType'],
                            'batas_min'      => $leafCell['batasMin'],
                            'batas_max'      => $leafCell['batasMax'],
                            'satuan_opsi'    => $leafCell['satuanOpsi'],
                            'is_readonly'    => $leafCell['isReadonly'],
                            'posisi_grid'    => ($isMultiRowCol || $kategori === 'foregoing') ? $rowLabel : null,
                            'urutan'         => $urutan++
                        ]);
                    }
                }
            }
        return true;
    }
}

    private function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                $element['children'] = $children ?: [];
                $branch[] = $element;
            }
        }
        return $branch;
    }

    private function calculateDepth($node)
    {
        if (empty($node['children'])) {
            return 1;
        }
        $maxDepth = 0;
        foreach ($node['children'] as $child) {
            $depth = $this->calculateDepth($child);
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
        return $maxDepth + 1;
    }

   // --- HELPER PENCATAT LOG ACTIVITY OTOMATIS (SESUAI LOGACTIVITYMODEL) ---
    private function catatLog(string $aksi, string $detail, string $processCode = '')
    {
        try {
            $tableName = $processCode !== '' ? strtoupper($processCode) : 'form_parameters';
            $pesan     = "[Form Builder - {$aksi}] {$detail}";

            // Gunakan fungsi logAction bawaan LogActivityModel
            if (method_exists($this->logActivityModel, 'logAction')) {
                $res = $this->logActivityModel->logAction($aksi, $pesan, $tableName);
                if ($res !== false) {
                    return;
                }
            }

            // Fallback insert langsung jika session positionid kosong/berbeda
            $session = session();
            $this->logActivityModel->protect(false)->insert([
                'emp_id'        => (string)($session->get('empid') ?? 'admin'),
                'type_activity' => $aksi,
                'activity'      => $pesan,
                'user'          => (string)($session->get('name') ?? $session->get('empid') ?? 'admin'),
                'table_name'    => $tableName,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat Log Activity FormBuilder: ' . $e->getMessage());
        }
    }
}
