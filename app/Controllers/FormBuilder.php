<?php

namespace App\Controllers;

use App\Models\DeviceModel;
use App\Models\ProcessModel;
use App\Models\FormRevisionModel;
use App\Models\FormParameterModel;

class FormBuilder extends BaseController
{
    protected $deviceModel;
    protected $processModel;
    protected $formRevModel;
    protected $formParamModel;

   public function __construct()
    {
        // Izinkan Operator mengakses fungsi renderOperatorForm untuk halaman Input
        $uri = service('uri');
        $isRenderInput = ($uri->getTotalSegments() >= 2 && $uri->getSegment(2) === 'render-input');

        if (!session()->get('isadmin') && !$isRenderInput) { 
            echo "<script>alert('Akses Ditolak! Khusus Admin/QC.'); window.location.href='/';</script>";
            exit;
        }

        $this->deviceModel    = new DeviceModel();
        $this->processModel   = new ProcessModel();
        $this->formRevModel   = new FormRevisionModel();
        $this->formParamModel = new FormParameterModel();
    }

    // 1. Menampilkan Halaman Utama
    public function index()
    {
        
        $data = [
            'title'   => 'Form Builder & Revision',
            'devices' => $this->deviceModel->findAll()
        ];
        return view('form_builder', $data); // Kita akan buat file view ini di langkah selanjutnya
    }

    // 2. AJAX Cascading Dropdown (Menggunakan fungsi getAll dari ProcessModel)
    public function getProcesses()
    {
        $deviceCode = $this->request->getGet('device_code');
        $type       = $this->request->getGet('type');

        // Panggil fungsi custom bawaan ProcessModel Abang
        $processes = $this->processModel->getAll($deviceCode, $type);
        
        return $this->response->setJSON($processes);
    }

    // 3. AJAX Load Form & Parameter (Auto-Upgrade & Auto-Parse V5)
    public function loadForm()
    {
        $processCode  = trim((string)$this->request->getPost('process'));
        $selectedType = trim((string)$this->request->getPost('doc_type'));
        $forceReparse = $this->request->getPost('reparse');

        if (!$processCode) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih proses terlebih dahulu!']);
        }

        // Deteksi kategori form
        if (!empty($selectedType) && in_array($selectedType, ['startup', 'production', 'foregoing'])) {
            $detectedKategori = $selectedType;
        } else {
            $parts = explode('-', strtolower($processCode));
            $typeKode = $parts[1] ?? 'p';
            $detectedKategori = ($typeKode === 's') ? 'startup' : (($typeKode === 'f') ? 'foregoing' : 'production');
        }

        // 1. Cari atau buat data Revisi
        $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
        
        if (!$revision) {
            $this->formRevModel->protect(false)->insert([
                'process_code'  => $processCode,
                'revisi'        => '0',
                'kategori_form' => $detectedKategori,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revisionId = $this->formRevModel->getInsertID();
            $revision = $this->formRevModel->find($revisionId);
        } else {
            $revisionId = $revision['id'];
            if (($revision['kategori_form'] ?? '') !== $detectedKategori) {
                $this->formRevModel->protect(false)->update($revisionId, ['kategori_form' => $detectedKategori]);
                $revision['kategori_form'] = $detectedKategori;
            }
        }

        // 2. Ambil Semua Parameter Mentah
        $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();

        // Deteksi otomatis jika data di DB masih format fallback ("PAR013", "Input PAR013", atau belum punya silsilah induk-anak)
        $isLegacyFlat = false;
        $hasAnyParent = false;
        foreach ($rawParams as $rp) {
            if (!empty($rp['parent_id'])) {
                $hasAnyParent = true;
            }
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
            $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        }
        
        // 3. Masukkan ke Mesin Pohon Keluarga
        $treeParams = $this->buildTree($rawParams);

        if (empty($treeParams) && !empty($rawParams)) {
            foreach ($rawParams as &$rp) {
                $rp['children'] = [];
            }
            $treeParams = $rawParams;
        }

        // 4. Hitung Maksimal Kedalaman
        $maxTableDepth = 0;
        foreach ($treeParams as $node) {
            $d = $this->calculateDepth($node);
            if ($d > $maxTableDepth) {
                $maxTableDepth = $d;
            }
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
            'params_list' => $rawParams
        ]); 
    }

    // --- MESIN PEMBEDAH HTML V5 (PRESISI UNTUK STARTUP, PRODUCTION, & FOREGOING) ---
    private function parseSingleProcess($processCode, $revisionId, $kategori)
    {
        $cleanCode = strtolower(trim($processCode));
        $parts = explode('-', $cleanCode);
        $device = $parts[0] ?? '';

        // 1. Cari file di folder input (atau history jika tidak ada di input)
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

        // 2. Pertahankan teks di dalam nl2br("...") lalu bersihkan seluruh tag PHP & komentar HTML
        $htmlContent = preg_replace('/<\?(?:php|=)\s*(?:echo\s+)?nl2br\(\s*[\'"](.*?)[\'"]\s*\)\s*;?\s*\?>/is', '$1', $rawSource);
        $htmlContent = preg_replace('/<\?(?:php|=).*?\?>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<!--.*?-->/is', '', $htmlContent);

        // 3. PERBAIKAN UTAMA PRODUCTION: Bungkus <td> di dalam <tbody> dengan <tr> jika file asli lupa menulis <tr>
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

        // Jika file tidak punya tag <table>, bungkus dengan <table>
        if (stripos($htmlContent, '<table') === false && preg_match('/<(thead|tbody|tr|td|th)\b/i', $htmlContent)) {
            $htmlContent = "<table>\n" . $htmlContent . "\n</table>";
        }

        // Bersihkan parameter lama di revisi ini
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
            // Pisahkan baris <thead> dan <tbody> agar rowspan di <thead> tidak bocor ke <tbody>
            $theadRowsRaw = $xpath->query('./thead/tr', $table);
            $tbodyRowsRaw = $xpath->query('./tbody/tr | ./tr', $table);

            $theadRows = [];
            foreach ($theadRowsRaw as $tr) {
                $cells = $xpath->query('./th | ./td', $tr);
                if ($cells->length === 0) continue;
                // Abaikan baris judul utama mesin (hanya 1 sel dengan colspan besar, misal colspan="19")
                if ($cells->length === 1 && (int)$cells->item(0)->getAttribute('colspan') > 2) {
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

            // Fungsi internal untuk memetakan baris ke dalam matriks 2D
            $processRowGroup = function (array $rowsGroup, bool $isHeaderGroup) use (
                &$matrix, &$cellList, &$rowIndex, &$maxCols, $numTheadRows, $xpath
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

                        // KUNCI: Jangan biarkan rowspan di <thead> menembus batas bawah <thead>!
                        if ($isHeaderGroup && $numTheadRows > 0) {
                            $maxAllowedSpan = max(1, $numTheadRows - $rowIndex);
                            if ($rowspan > $maxAllowedSpan) {
                                $rowspan = $maxAllowedSpan;
                            }
                        }

                        // Deteksi elemen input di dalam sel (termasuk kombinasi hidden parXXX + parXXX_val + parXXX_unit)
                        $parInputs = $xpath->query('.//input[contains(@name, "par")] | .//select[contains(@name, "par")] | .//textarea[contains(@name, "par")]', $cell);
                        $anyInputs = $xpath->query('.//input[not(@type="hidden" or @type="submit" or @type="button" or @type="reset")] | .//select | .//textarea', $cell);

                        $isLeaf    = ($parInputs->length > 0 || $anyInputs->length > 0);
                        $dbCol     = null;
                        $inputType = 'text';
                        $batasMin  = null;
                        $batasMax  = null;
                        $stdNilai  = null;
                        $name      = '';

                        if ($isLeaf) {
                            $mainNode = ($parInputs->length > 0) ? $parInputs->item(0) : $anyInputs->item(0);
                            $dbCol = $mainNode->getAttribute('name') ?: $mainNode->getAttribute('id');
                            if ($dbCol) {
                                $dbCol = preg_replace('/\[.*\]$/', '', $dbCol);
                                $dbCol = preg_replace('/_(val|unit)$/i', '', $dbCol);
                            }

                            // Jika mainNode adalah hidden input (seperti par015 di Startup Skenario C), cek input angkanya
                            $visibleNode = ($anyInputs->length > 0) ? $anyInputs->item(0) : $mainNode;
                            if ($mainNode->nodeName === 'select' || ($visibleNode->nodeName === 'select' && $anyInputs->length === 1)) {
                                $inputType = 'dropdown_ok_ng';
                            } elseif ($mainNode->nodeName === 'textarea' || $visibleNode->nodeName === 'textarea') {
                                $inputType = 'textarea';
                            } else {
                                $typeAttr = strtolower($visibleNode->getAttribute('type'));
                                if ($typeAttr === 'number' || $visibleNode->hasAttribute('step')) {
                                    $inputType = 'number';
                                }
                            }

                            // Ekstrak otomatis batas_min & batas_max dari atribut onchange="changeRange('par...', min, max)"
                            $cellHtml = $cell->ownerDocument->saveHTML($cell);
                            if (preg_match('/changeRange\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/i', $cellHtml, $mRange)) {
                                $minVal = (float)$mRange[1];
                                $maxVal = (float)$mRange[2];
                                if ($minVal > -999999) $batasMin = $minVal;
                                if ($maxVal < 999999)  $batasMax = $maxVal;
                            }

                            // Jika ada dropdown satuan pendamping (misal N / gr atau MPa / kPa)
                            $unitSelects = $xpath->query('.//select[contains(@id, "_unit")]/option', $cell);
                            if ($unitSelects->length > 0) {
                                $units = [];
                                foreach ($unitSelects as $opt) {
                                    $uVal = trim($opt->textContent);
                                    if ($uVal !== '') $units[] = $uVal;
                                }
                                if (!empty($units)) {
                                    $stdNilai = 'Satuan: ' . implode(' / ', $units);
                                }
                            }
                        } else {
                            $clone = $cell->cloneNode(true);
                            foreach ($xpath->query('.//select | .//script | .//style', $clone) as $rem) {
                                $rem->parentNode->removeChild($rem);
                            }
                            $name = trim(preg_replace('/\s+/', ' ', $clone->textContent));
                        }

                        $cellId = uniqid('c_', true);
                        $cellList[$cellId] = [
                            'id'        => $cellId,
                            'name'      => $name,
                            'dbCol'     => $dbCol,
                            'inputType' => $inputType,
                            'batasMin'  => $batasMin,
                            'batasMax'  => $batasMax,
                            'stdNilai'  => $stdNilai,
                            'isLeaf'    => $isLeaf,
                            'isHeader'  => $isHeaderGroup,
                            'r'         => $rowIndex,
                            'c'         => $colIndex,
                            'rowspan'   => $rowspan,
                            'colspan'   => $colspan
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

            // Jalankan pemetaan <thead> dulu, baru <tbody>
            $processRowGroup($theadRows, true);
            $processRowGroup($tbodyRows, false);

            // --- ARAH 1: STARTUP (VERTIKAL: BACA BAPAK DARI KIRI KE KANAN PADA BARIS YANG SAMA) ---
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
                        // Abaikan kolom nomor urut (1, 2, 3...) di kolom paling kiri
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
                                'standar_nilai'  => $leafCell['stdNilai'],
                                'urutan'         => $urutan++
                            ]);
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
            // --- ARAH 2 & 3: PRODUCTION & FOREGOING (BACA HEADER DARI ATAS KE BAWAH) ---
            else {
                $inputsPerCol = [];
                $firstInputRow = [];
                foreach ($cellList as $cellId => $node) {
                    if ($node['isLeaf']) {
                        $c = $node['c'];
                        $inputsPerCol[$c] = ($inputsPerCol[$c] ?? 0) + 1;
                        if (!isset($firstInputRow[$c]) || $node['r'] < $firstInputRow[$c]) {
                            $firstInputRow[$c] = $node['r'];
                        }
                    }
                }

                foreach ($cellList as $cellId => $leafCell) {
                    if (!$leafCell['isLeaf']) continue;
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
                            $this->formParamModel->protect(false)->insert([
                                'revision_id'    => $revisionId,
                                'parent_id'      => $parentId,
                                'nama_parameter' => $chNode['name'],
                                'kolom_db'       => $leafCell['dbCol'],
                                'tipe_input'     => $leafCell['inputType'],
                                'batas_min'      => $leafCell['batasMin'],
                                'batas_max'      => $leafCell['batasMax'],
                                'standar_nilai'  => $leafCell['stdNilai'],
                                'urutan'         => $urutan++
                            ]);
                            $parentId = $this->formParamModel->getInsertID();
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

                    // Jika kolom matriks Foregoing (Sample 1, Sample 2 / X, Y, Z, θ / X., Y., r., s.)
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
                            'posisi_grid'    => $isMultiRowCol ? $rowLabel : null,
                            'urutan'         => $urutan++
                        ]);
                    }
                }
            }
        }
        return true;
    }


    // 4. AJAX Save (Ramah Awam: Otomatis Buat parXXX & Simpan Batas Min/Max)
    public function saveParameter()
    {
        $id          = $this->request->getPost('id');
        $revisionId  = $this->request->getPost('revision_id');
        $jenisElemen = $this->request->getPost('jenis_elemen'); // 'header' atau 'input'
        $tipeInput   = $this->request->getPost('tipe_input') ?: null;
        $kolomDb     = $this->request->getPost('kolom_db') ?: null;

        // Jika Admin memilih "Judul Kelompok", kosongkan tipe input & kolom DB
        if ($jenisElemen === 'header') {
            $tipeInput = null;
            $kolomDb   = null;
            $batasMin  = null;
            $batasMax  = null;
        } else {
            // Jika Kolom Isian Operator tapi belum punya kode parXXX, buatkan otomatis!
            if (empty($kolomDb)) {
                $existingParams = $this->formParamModel->where('revision_id', $revisionId)->findAll();
                $maxParNum = 0;
                foreach ($existingParams as $ep) {
                    if (!empty($ep['kolom_db']) && preg_match('/^par(\d+)$/i', $ep['kolom_db'], $matches)) {
                        $num = (int)$matches[1];
                        if ($num > $maxParNum) $maxParNum = $num;
                    }
                }
                $nextNum = $maxParNum + 1;
                $kolomDb = 'par' . str_pad((string)$nextNum, 3, '0', STR_PAD_LEFT);
            }

            $batasMin = ($this->request->getPost('batas_min') !== '' && $this->request->getPost('batas_min') !== null) 
                        ? $this->request->getPost('batas_min') : null;
            $batasMax = ($this->request->getPost('batas_max') !== '' && $this->request->getPost('batas_max') !== null) 
                        ? $this->request->getPost('batas_max') : null;
        }

        // Hitung urutan otomatis jika tambah baru
        $urutan = $this->request->getPost('urutan');
        if (!$id && empty($urutan)) {
            $last = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'DESC')->first();
            $urutan = $last ? ($last['urutan'] + 1) : 1;
        }

        $data = [
            'revision_id'    => $revisionId,
            'parent_id'      => $this->request->getPost('parent_id') ?: null,
            'nama_parameter' => $this->request->getPost('nama_parameter'),
            'kolom_db'       => $kolomDb,
            'tipe_input'     => $tipeInput,
            'batas_min'      => $batasMin,
            'batas_max'      => $batasMax,
            'standar_nilai'  => $this->request->getPost('standar_nilai') ?: null,
            'urutan'         => $urutan ?: 1,
        ];

        if ($id) {
            $this->formParamModel->protect(false)->update($id, $data);
            $msg = 'Parameter berhasil diperbarui!';
        } else {
            $this->formParamModel->protect(false)->insert($data);
            $msg = 'Parameter baru berhasil ditambahkan!';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    // 5. AJAX Delete Parameter (Anak & Cucu otomatis ikut terhapus berkat CASCADE database)
    public function deleteParameter()
    {
        $id = $this->request->getPost('id');
        $this->formParamModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Parameter beserta anaknya berhasil dihapus.']);
    }

    // 6. AJAX Naik Revisi (A ke B, dst)
    public function upRevisi()
    {
        $oldRevisionId = $this->request->getPost('revision_id');
        $oldRevision = $this->formRevModel->find($oldRevisionId);
        
        // Magic Increment Huruf/Angka Revisi
        $newRevisiCode = $oldRevision['revisi'];
        $newRevisiCode++; 

        // Insert Revisi Baru ke tabel form_revisions
        $this->formRevModel->insert([
            'process_code' => $oldRevision['process_code'],
            'revisi'       => $newRevisiCode,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        $newRevisionId = $this->formRevModel->getInsertID();

        // DUPLIKASI seluruh parameter dari revisi lama ke revisi baru
        $oldParameters = $this->formParamModel->where('revision_id', $oldRevisionId)->findAll();
        
        // Kita butuh pemetaan ID lama ke ID baru karena parent_id-nya akan berubah
        $idMapping = []; 
        
        // Step 1: Duplikasi Parameter Utama (parent_id = NULL)
        foreach ($oldParameters as $param) {
            if (is_null($param['parent_id'])) {
                $param['revision_id'] = $newRevisionId;
                $oldParamId = $param['id'];
                unset($param['id']); // Hapus ID lama biar auto-increment jalan
                
                $this->formParamModel->insert($param);
                $idMapping[$oldParamId] = $this->formParamModel->getInsertID();
            }
        }
        
        // Step 2: Duplikasi Anak-anaknya (hubungkan ke bapak yang baru)
        foreach ($oldParameters as $param) {
            if (!is_null($param['parent_id'])) {
                $param['revision_id'] = $newRevisionId;
                // Ganti parent_id dengan ID bapak yang baru saja diduplikasi
                $param['parent_id'] = $idMapping[$param['parent_id']] ?? null; 
                unset($param['id']);
                
                $this->formParamModel->insert($param);
                // Tambahkan mapping untuk cucu-cucunya
                // $idMapping[$oldParamId] = $this->formParamModel->getInsertID(); (Jika level lebih dari 2)
            }
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => 'Revisi berhasil dinaikkan menjadi ' . $newRevisiCode
        ]);
    }

    // Fungsi Rahasia: Menjalankan Migrasi lewat Web (Bypass Terminal)
    public function eksekusiMigrasi()
    {
        $migrate = \Config\Services::migrations();
        try {
            $migrate->latest();
            echo "<h1>MANTAP! Migrasi Database Berhasil Dijalankan!</h1>";
            echo "<a href='".base_url('form-builder')."'>Kembali ke Form Builder</a>";
        } catch (\Throwable $e) {
            echo "Waduh, gagal Bang: " . $e->getMessage();
        }
    }

    // --- ROBOT MASSAL V3 (MATRIX DOM PARSER) ---
    public function sedotHtml()
    {
        $semuaProses = $this->processModel->findAll();
        $totalProses = count($semuaProses);
        
        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>LOG ROBOT V3 (MATRIX PARSER):</h2>";
        
        if ($totalProses == 0) return;

        $totalTersedot = 0;

        foreach ($semuaProses as $proses) {
            $processCode = $proses['process_code'];
            $angkaRevisi = $proses['revisi'] ?? '0';

            // 1. Cek atau Bikin Revisi
            $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
            if (!$revision) {
                $this->formRevModel->insert([
                    'process_code'  => $processCode,
                    'revisi'        => $angkaRevisi,
                    'kategori_form' => 'production',
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
                $revisionId = $this->formRevModel->getInsertID();
            } else {
                $revisionId = $revision['id'];
            }

            // PENTING: Kosongkan data lama di revisi ini agar tidak duplikat saat disedot ulang
            $this->formParamModel->where('revision_id', $revisionId)->delete();

            // 2. Cari & Baca File HTML
            $parts = explode('-', $processCode);
            if (count($parts) < 2) continue; 
            
            $device = $parts[0];
            $typeKode = $parts[1];
            $typeFolder = ($typeKode == 's') ? 'startup' : (($typeKode == 'f') ? 'foregoing' : 'production');
            $filePath = APPPATH . "Views/layout/{$device}/input/{$typeFolder}/{$processCode}.php";

            if (!file_exists($filePath)) continue; 

            $htmlContent = file_get_contents($filePath);
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);

            // 3. LOGIKA PAPAN CATUR (MATRIX 2D)
            $rows = $xpath->query('//tr');
            $matrix = [];
            $cellList = [];
            $rowIndex = 0;

            foreach ($rows as $tr) {
                $colIndex = 0;
                foreach ($tr->childNodes as $cell) {
                    if ($cell->nodeName !== 'th' && $cell->nodeName !== 'td') continue;

                    // Lewati kotak yang sudah diisi oleh rowspan/colspan dari sel sebelumnya
                    while (isset($matrix[$rowIndex][$colIndex])) {
                        $colIndex++;
                    }

                    $colspan = (int)$cell->getAttribute('colspan') ?: 1;
                    $rowspan = (int)$cell->getAttribute('rowspan') ?: 1;

                    // Deteksi Input Data (Daun/Ujung Parameter)
                    $inputs = $xpath->query('.//input[contains(@name, "par")] | .//select[contains(@name, "par")]', $cell);
                    $isLeaf = $inputs->length > 0;
                    $dbCol = null;
                    $inputType = 'text';

                    if ($isLeaf) {
                        $inputNode = $inputs->item(0);
                        $dbCol = $inputNode->getAttribute('name');
                        $name = "Input " . strtoupper($dbCol);
                        if ($inputNode->nodeName == 'select') $inputType = 'dropdown_ok_ng';
                        elseif ($inputNode->getAttribute('type') == 'number' || $inputNode->getAttribute('type') == 'step') $inputType = 'number';
                    } else {
                        // Ambil teks sebagai nama Bapak, bersihkan spasi
                        $name = trim(preg_replace('/\s+/', ' ', $cell->textContent));
                        if (empty($name)) $name = "Elemen Kosong";
                        if (strlen($name) > 45) $name = substr($name, 0, 45) . "..."; // Potong teks kepanjangan
                    }

                    $cellId = uniqid();
                    $cellList[$cellId] = [
                        'id_temp'   => $cellId, 'name' => $name, 'dbCol' => $dbCol,
                        'inputType' => $inputType, 'isLeaf' => $isLeaf,
                        'r' => $rowIndex, 'c' => $colIndex
                    ];

                    // Isi Papan Catur
                    for ($r = 0; $r < $rowspan; $r++) {
                        for ($c = 0; $c < $colspan; $c++) {
                            $matrix[$rowIndex + $r][$colIndex + $c] = $cellId;
                        }
                    }
                    $colIndex += $colspan;
                }
                $rowIndex++;
            }

            // 4. SIMPAN KE DATABASE BERDASARKAN URUTAN BAPAK-ANAK
            $dbIdMapping = []; 
            $urutan = 1;

            for ($r = 0; $r < $rowIndex; $r++) {
                foreach ($cellList as $tempId => $node) {
                    if ($node['r'] === $r) {
                        // DETEKSI BAPAK: Lihat satu petak ke atas persis!
                        $parentIdDB = null;
                        if ($r > 0) {
                            $parentTempId = $matrix[$r - 1][$node['c']] ?? null;
                            if ($parentTempId && $parentTempId !== $tempId) {
                                $parentIdDB = $dbIdMapping[$parentTempId] ?? null;
                            }
                        }

                        $this->formParamModel->insert([
                            'revision_id'    => $revisionId,
                            'parent_id'      => $parentIdDB,
                            'nama_parameter' => $node['name'],
                            'kolom_db'       => $node['isLeaf'] ? $node['dbCol'] : null,
                            'tipe_input'     => $node['isLeaf'] ? $node['inputType'] : null,
                            'urutan'         => $urutan++
                        ]);
                        
                        $dbIdMapping[$tempId] = $this->formParamModel->getInsertID();
                        $totalTersedot++;
                    }
                }
            }
        }
        echo "<h3 style='color:green'>Sihir Matriks Selesai! $totalTersedot elemen sukses dirajut silsilahnya!</h3>";
        echo "<a href='".base_url('form-builder')."'>Kembali ke Form Builder</a></div>";
    }

    // --- ENGINE 1: PENYUSUN POHON KELUARGA (REKURSIF) ---
    private function buildTree(array $elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                } else {
                    $element['children'] = [];
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    // --- ENGINE 2: PENGHITUNG LEBAR KOTAK (COLSPAN) ---
    private function calculateDepth($node) {
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

    // 7. Endpoint untuk merender Tabel Dinamis di Halaman Input Operator
    public function renderOperatorForm()
    {
        $processCode  = trim((string)($this->request->getPost('process') ?: $this->request->getGet('process')));
        $selectedType = trim((string)($this->request->getPost('doc_type') ?: $this->request->getGet('doc_type')));

        if (!$processCode) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Process code kosong']);
        }

        if (!empty($selectedType) && in_array($selectedType, ['startup', 'production', 'foregoing'])) {
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
        }

        $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        if (empty($rawParams)) {
            $this->parseSingleProcess($processCode, $revisionId, $detectedKategori);
            $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        }

        $treeParams = $this->buildTree($rawParams);
        $maxTableDepth = 0;
        foreach ($treeParams as $node) {
            $d = $this->calculateDepth($node);
            if ($d > $maxTableDepth) $maxTableDepth = $d;
        }

        $procInfo = $this->processModel->where('process_code', $processCode)->first();

        $data = [
            'process_code'  => $processCode,
            'process_name'  => $procInfo['name'] ?? $processCode,
            'revision'      => $revision,
            'kategori_form' => $revision['kategori_form'] ?? $detectedKategori,
            'treeParams'    => $treeParams,
            'maxTableDepth' => max(1, $maxTableDepth)
        ];

        $html = view('form_input_dynamic', $data);

        return $this->response->setJSON([
            'status'      => 'success',
            'revisi'      => $revision['revisi'],
            'revision_id' => $revision['id'],
            'html'        => $html
        ]);
    }
}