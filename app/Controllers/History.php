<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InputModel;
use App\Models\StartupModel;
use App\Models\ProductionModel;
use App\Models\ForegoingModel;
use App\Models\EmpModel;
use App\Models\FormRevisionModel;
use App\Models\FormParameterModel;
use App\Libraries\PdfGenerator;
use App\Libraries\ExcelGenerator;

class History extends BaseController
{
    protected $session;
    protected $InputModel;
    protected $ProductionModel;
    protected $StartupModel;
    protected $ForegoingModel;
    protected $EmpModel;
    protected $formRevModel;
    protected $formParamModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->InputModel      = new InputModel();
        $this->ProductionModel = new ProductionModel();
        $this->StartupModel    = new StartupModel();
        $this->ForegoingModel  = new ForegoingModel();
        $this->EmpModel        = new EmpModel();
        $this->formRevModel    = new FormRevisionModel();
        $this->formParamModel  = new FormParameterModel();
        $this->session         = \Config\Services::session();
        $this->session->start();
    }

    public function index(): string
    {
        error_reporting(0);
        $data = ['title' => 'History | Startup Management'];
        return view('history', $data);
    }

    // 1. AJAX Endpoint: Ambil Daftar Versi Revisi untuk Dropdown di History
    public function getRevisions()
    {
        $processCode = trim((string)$this->request->getGet('process'));
        $docType     = trim((string)$this->request->getGet('type'));

        if (!$processCode || $processCode === '-') {
            return $this->response->setJSON([]);
        }

        $revisions = $this->ensureProcessRevisions($processCode, $docType);
        return $this->response->setJSON($revisions);
    }

    // 2. AJAX Endpoint: Render Tabel History Berdasarkan Versi Revisi Terpilih
    public function getDataTable()
    {
        $typeProcess = trim((string)$this->request->getGet('typeProcess'));
        $process     = trim((string)$this->request->getGet('process'));
        $device      = trim((string)$this->request->getGet('device'));
        $dateStart   = trim((string)$this->request->getGet('dateStart'));
        $dateEnd     = trim((string)$this->request->getGet('dateEnd'));
        $model       = trim((string)($this->request->getGet('model') ?? ''));
        $lotno       = trim((string)($this->request->getGet('lotno') ?? ''));
        $machno      = $this->request->getGet('machno') === 'null' ? '' : trim((string)($this->request->getGet('machno') ?? ''));
        $revisi      = trim((string)($this->request->getGet('revisi') ?? ''));

        if (!$process || !$typeProcess) {
            return '';
        }

        $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno, $revisi);
        return $this->renderHistoryHtml($context, false);
    }

    public function dataForegoing()
    {
        $data = [
            'title'   => 'History | Startup Management',
            'alldata' => $this->ForegoingModel->getAll($_GET['dateStart'], $_GET['dateEnd'], $_GET['device'], $_GET['process'])
        ];
        return view("/layout/" . $_GET['device'] . "/history/foregoing/" . $_GET['process'], $data);
    }

    public function foregoingByNumber($number)
    {
        $data = ['alldata' => $this->ForegoingModel->getByNumber($number)];
        return view("/layout/" . $data['alldata'][0]['device'] . "/history/foregoing/" . $data['alldata'][0]['process'], $data);
    }

    private function getProsesInfo(string $processCode): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('proses')->where('process_code', $processCode)->get()->getRowArray();
        return $row ?: [];
    }

    private function getDeviceInfo(string $deviceCode): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('device')->where('code', $deviceCode)->get()->getRowArray();
        return $row ?: [];
    }

    private function ensureProcessRevisions(string $processCode, string $docType = ''): array
    {
        if (empty($docType)) {
            $parts = explode('-', strtolower($processCode));
            $typeKode = $parts[1] ?? 'p';
            $docType = ($typeKode === 's') ? 'startup' : (($typeKode === 'f') ? 'foregoing' : 'production');
        }

        $revisions = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->findAll();
        if (empty($revisions)) {
            $prosesInfo = $this->getProsesInfo($processCode);
            $revDefault = $prosesInfo['revisi'] ?? '0';
            $this->formRevModel->protect(false)->insert([
                'process_code'  => $processCode,
                'revisi'        => (string)$revDefault,
                'kategori_form' => $docType,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revisions = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->findAll();
        }
        return $revisions;
    }

    private function buildTree(array $elements, $parentId = null): array
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

    private function filterDataByRevisi(array $alldata, string $selectedRevisi, bool $includeEmptyLegacy = false): array
    {
        if (empty($alldata) || $selectedRevisi === '' || strtoupper($selectedRevisi) === 'ALL') {
            return $alldata;
        }

        $filtered = [];
        $selInt   = is_numeric($selectedRevisi) ? (int)$selectedRevisi : null;

        foreach ($alldata as $row) {
            $rowRev = isset($row['revisi']) ? trim((string)$row['revisi']) : '';

            if (($rowRev === '' || $rowRev === '-') && !empty($row['docno']) && $row['docno'] !== '-') {
                $dParts = explode('-', trim((string)$row['docno']));
                if (count($dParts) >= 4) {
                    $rowRev = end($dParts);
                }
            }

            if ($rowRev === '' || $rowRev === '-') {
                $filtered[] = $row;
                continue;
            }

            if ($selInt !== null && is_numeric($rowRev) && (int)$rowRev === $selInt) {
                $filtered[] = $row;
            } elseif (strcasecmp($rowRev, $selectedRevisi) === 0) {
                $filtered[] = $row;
            }
        }

        return !empty($filtered) ? $filtered : $alldata;
    }

    private function resolveExportContext(
        string $typeProcess,
        string $process,
        string $device,
        string $dateStart,
        string $dateEnd,
        string $model = '',
        string $lotno = '',
        string $machno = '',
        string $selectedRevisi = ''
    ): array {
        $alldata = [];
        if ($typeProcess === 'production') {
            $alldata = $this->ProductionModel->getAll($dateStart, $dateEnd, $process, $model, $lotno, $machno);
        } elseif ($typeProcess === 'foregoing') {
            $alldata = $this->ForegoingModel->getAll($dateStart, $dateEnd, $device, $process, $model, $lotno, $machno);
        } else {
            $alldata = $this->StartupModel->getAll($dateStart, $dateEnd, $device, $process, $model, $lotno, $machno);
        }

        $revisionsList = $this->ensureProcessRevisions($process, $typeProcess);
        $activeRevRow  = $revisionsList[0] ?? null;
        $oldestRevRow  = !empty($revisionsList) ? end($revisionsList) : null;

        if ($selectedRevisi !== '' && strtoupper($selectedRevisi) !== 'ALL') {
            foreach ($revisionsList as $revItem) {
                if (
                    strcasecmp((string)$revItem['revisi'], $selectedRevisi) === 0 ||
                    (is_numeric($revItem['revisi']) && is_numeric($selectedRevisi) && (int)$revItem['revisi'] === (int)$selectedRevisi)
                ) {
                    $activeRevRow = $revItem;
                    break;
                }
            }
        }

        $isOldestOrSingleRev = (count($revisionsList) <= 1) || ($activeRevRow && $oldestRevRow && $activeRevRow['id'] == $oldestRevRow['id']);
        $alldata = array_values($this->filterDataByRevisi($alldata, $selectedRevisi, $isOldestOrSingleRev));

        $latestDate   = 0;
        $namaApprover = '';

        foreach ($alldata as &$row) {
            $rowDate = 0;
            if (!empty($row['created_at'])) {
                $rowDate = strtotime($row['created_at']);
            } elseif (!empty($row['date'])) {
                $rowDate = strtotime($row['date']);
            }
            if ($rowDate > $latestDate) {
                $latestDate = $rowDate;
            }

            if ($namaApprover === '') {
                if (!empty($row['supervisor']) && $row['supervisor'] !== '-') { 
                    $namaApprover = trim($row['supervisor']) . "\n(Supervisor)"; 
                } elseif (!empty($row['leader']) && $row['leader'] !== '-') { 
                    $namaApprover = trim($row['leader']) . "\n(Leader)"; 
                } elseif (!empty($row['foreman']) && $row['foreman'] !== '-') { 
                    $namaApprover = trim($row['foreman']) . "\n(Foreman)"; 
                }
            }

            foreach ($row as $key => $value) {
                if ($value === null || trim((string)$value) === '') {
                    $row[$key] = '-';
                }
            }
        }
        unset($row);

        $prosesInfo    = $this->getProsesInfo($process);
        $deviceInfo    = $this->getDeviceInfo($device);
        $namaProduk    = $deviceInfo['name'] ?? strtoupper($device);
        $namaProsesRaw = $prosesInfo['name'] ?? strtoupper($process);

        if ($typeProcess === 'production') {
            $cleanName   = trim(preg_replace('/^(Production\s+Process\s+Control\s+Sheet|Production\s+Control\s+Sheet|Produciton\s+Control\s+Sheet\s+of|Produciton\s+Control\s+Sheet)\s*/i', '', $namaProsesRaw));
            $judulProses = 'Production Process Control Sheet ' . $cleanName;
        } elseif ($typeProcess === 'startup' || $typeProcess === 'startup_horizontal') {
            $cleanName   = trim(preg_replace('/\s*Start\s*Up\s*Check\s*Sheet.*$/i', '', $namaProsesRaw));
            $judulProses = $cleanName . ' Start Up Check Sheet';
        } else {
            $judulProses = $namaProsesRaw;
        }

        $activeRevNum = $activeRevRow['revisi'] ?? ($prosesInfo['revisi'] ?? '0');
        if ($selectedRevisi !== '' && strtoupper($selectedRevisi) !== 'ALL') {
            $activeRevNum = $selectedRevisi;
        }
        $revisi = str_pad((string)$activeRevNum, 2, '0', STR_PAD_LEFT);

        $noDok = $prosesInfo['docno'] ?? ('FF-' . strtoupper(explode('-', $process)[2] ?? '001') . '-001');
        $docParts = explode('-', $noDok);
        if (count($docParts) >= 4) {
            $docParts[count($docParts) - 1] = $revisi;
            $noDok = implode('-', $docParts);
        }

        $machNo     = ($machno !== '' && $machno !== 'null' && $machno !== 'ALL') ? strtoupper($machno) : 'ALL MACHINES';
        $nama_mesin = $machNo;

        $tglBerlaku = '-';
        $bulanIndo  = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if ($latestDate > 0) {
            $tglBerlaku = date('d', $latestDate) . ' ' . $bulanIndo[date('n', $latestDate) - 1] . ' ' . date('Y', $latestDate);
        } elseif (!empty($prosesInfo['berlaku']) && $prosesInfo['berlaku'] !== '0000-00-00') {
            $ts         = strtotime($prosesInfo['berlaku']);
            $tglBerlaku = date('d', $ts) . ' ' . $bulanIndo[date('n', $ts) - 1] . ' ' . date('Y', $ts);
        }

        return compact(
            'alldata', 'namaProduk', 'judulProses', 'noDok', 'machNo', 'nama_mesin',
            'prosesInfo', 'typeProcess', 'process', 'device', 'tglBerlaku', 'namaApprover',
            'revisi', 'activeRevRow'
        );
    }

    private function renderHistoryHtml(array &$context, bool $isExport = false): string
    {
        $activeRevRow = $context['activeRevRow'] ?? null;
        $typeProcess  = $context['typeProcess'];
        $process      = $context['process'];
        $device       = $context['device'];

        if ($activeRevRow) {
            $rawParams = $this->formParamModel->where('revision_id', $activeRevRow['id'])->orderBy('urutan', 'ASC')->findAll();

            $needsReparse = empty($rawParams);
            if (!$needsReparse) {
                $hasAnyParent = false;
                $hasAnyGrid   = false;
                foreach ($rawParams as $rp) {
                    if (!empty($rp['parent_id'])) $hasAnyParent = true;
                    if (!empty($rp['posisi_grid'])) $hasAnyGrid = true;
                    if (
                        preg_match('/^(Input\s+)?PAR\d+[a-z]?$/i', trim($rp['nama_parameter'])) ||
                        strpos($rp['nama_parameter'], 'COL_') === 0 ||
                        $rp['nama_parameter'] === 'Elemen Kosong'
                    ) {
                        $needsReparse = true;
                        break;
                    }
                }
                if (!$hasAnyParent && count($rawParams) > 1) {
                    $needsReparse = true;
                }
                if ($typeProcess === 'foregoing' && !$hasAnyGrid) {
                    $needsReparse = true;
                }
            }

            if ($needsReparse) {
                try {
                    $fb  = new \App\Controllers\FormBuilder();
                    $ref = new \ReflectionMethod($fb, 'parseSingleProcess');
                    $ref->setAccessible(true);
                    $ref->invoke($fb, $process, $activeRevRow['id'], $typeProcess);
                    $activeRevRow = $this->formRevModel->find($activeRevRow['id']);
                    $context['activeRevRow'] = $activeRevRow;
                    $rawParams = $this->formParamModel->where('revision_id', $activeRevRow['id'])->orderBy('urutan', 'ASC')->findAll();
                } catch (\Throwable $e) {
                    log_message('error', 'Auto-reparse di History gagal: ' . $e->getMessage());
                }
            }

            if (!empty($rawParams)) {
                $treeParams = $this->buildTree($rawParams);
                $katForm    = $activeRevRow['kategori_form'] ?? $typeProcess;
                return view('history_table_dynamic', [
                    'process_code'  => $process,
                    'judul_proses'  => $context['judulProses'] ?? $process,
                    'kategori_form' => $katForm,
                    'revisi_label'  => $context['revisi'] ?? '00',
                    'treeParams'    => $treeParams,
                    'alldata'       => $context['alldata'],
                    'is_export'     => $isExport
                ]);
            }
        }

        $viewPath = "/layout/" . $device . "/history/" . $typeProcess . "/" . $process;
        return view($viewPath, ['alldata' => $context['alldata']]);
    }

    public function exportExcel()
    {
        $typeProcess = $this->request->getGet('typeProcess');
        $process     = $this->request->getGet('process');
        $device      = $this->request->getGet('device');
        $revisi      = $this->request->getGet('revisi') ?? '';

        $rawStart  = $this->request->getGet('dateStart');
        $rawEnd    = $this->request->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model  = $this->request->getGet('model') ?? '';
        $lotno  = $this->request->getGet('lotno') ?? '';
        $machno = $this->request->getGet('machno') === 'null' ? '' : ($this->request->getGet('machno') ?? '');

        try {
            $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno, $revisi);

            $_SERVER['REQUEST_URI'] = 'exportExcel';
            $htmlString = $this->renderHistoryHtml($context, true);

            // Jika form ini berformat Startup Mendatar, arahkan ExcelGenerator menggunakan mode horizontal
            if (($context['activeRevRow']['kategori_form'] ?? '') === 'startup_horizontal') {
                $context['typeProcess'] = 'startup_horizontal';
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $excelGen = new ExcelGenerator();
            $excelGen->generate($htmlString, $context);
        } catch (\Throwable $e) {
            log_message('error', 'exportExcel gagal: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            die("<h2>EXPORT EXCEL GAGAL</h2><p><b>Pesan:</b> " . esc($e->getMessage())
                . "</p><p><b>Lokasi:</b> " . esc($e->getFile()) . " (Baris: " . $e->getLine() . ")</p>");
        }
    }

    public function exportPDF()
    {
        $typeProcess = $this->request->getGet('typeProcess');
        $process     = $this->request->getGet('process');
        $device      = $this->request->getGet('device');
        $revisi      = $this->request->getGet('revisi') ?? '';

        $rawStart  = $this->request->getGet('dateStart');
        $rawEnd    = $this->request->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model  = $this->request->getGet('model') ?? '';
        $lotno  = $this->request->getGet('lotno') ?? '';
        $machno = $this->request->getGet('machno') === 'null' ? '' : ($this->request->getGet('machno') ?? '');

        $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno, $revisi);

        $_SERVER['REQUEST_URI'] = 'exportPDF';
        try {
            $htmlString = $this->renderHistoryHtml($context, true);
        } catch (\Throwable $e) {
            die("<h2>SYSTEM ERROR DALAM VIEW HTML:</h2><p><b>Pesan:</b> " . $e->getMessage() . "</p><p><b>Lokasi:</b> " . $e->getFile() . " (Baris: " . $e->getLine() . ")</p>"); 
        }

        // Jika form ini berformat Startup Mendatar, arahkan PdfGenerator menggunakan mode horizontal
        if (($context['activeRevRow']['kategori_form'] ?? '') === 'startup_horizontal') {
            $context['typeProcess'] = 'startup_horizontal';
        }

        $pdfGen = new PdfGenerator();
        $pdfGen->generate($htmlString, $context);
    }
}