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
        $weeklyId    = $this->request->getGet('weekly_id');

        if (!$process || !$typeProcess) {
            return '';
        }

        $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno, $weeklyId, $revisi);
        return $this->renderHistoryHtml($context, false);
    }

    public function dataForegoing()
    {
        $req    = $this->request;
        $dStart = $req->getGet('dateStart');
        $dEnd   = $req->getGet('dateEnd');
        $dev    = $req->getGet('device');
        $proc   = $req->getGet('process');

        $alldata = $this->ForegoingModel->getAll($dStart, $dEnd, $dev, $proc);

        $data = [
            'title'   => 'History | Startup Management',
            'alldata' => $alldata,
        ];
        $viewPath = "/layout/" . $dev . "/history/foregoing/" . $proc;
        return view($viewPath, $data);
    }

    public function foregoingByNumber($number)
    {
        $rows = $this->ForegoingModel->getByNumber($number);
        if (empty($rows)) {
            return "Data tidak ditemukan.";
        }

        $machno       = !empty($rows[0]['machno']) ? esc($rows[0]['machno']) : '';
        $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machno . '">';

        try {
            $fb = new \App\Controllers\FormBuilder();
            return $machnoHidden . $fb->renderDetailTableByRows($rows[0]['process'], 'foregoing', $rows);
        } catch (\Throwable $e) {
            $dev  = $rows[0]['device'];
            $proc = $rows[0]['process'];
            return $machnoHidden . view("/layout/" . $dev . "/history/foregoing/" . $proc, ['alldata' => $rows]);
        }
    }

    private function getProsesInfo(string $processCode): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('proses')
            ->where('process_code', $processCode)
            ->get()
            ->getRowArray();
        return $row ?: [];
    }

    private function getDeviceInfo(string $deviceCode): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('device')
            ->where('code', $deviceCode)
            ->get()
            ->getRowArray();
        return $row ?: [];
    }

    private function ensureProcessRevisions(string $processCode, string $docType = ''): array
    {
        if (empty($docType)) {
            $parts    = explode('-', strtolower($processCode));
            $typeKode = $parts[1] ?? 'p';
            $docType  = ($typeKode === 's') ? 'startup' : (($typeKode === 'f') ? 'foregoing' : 'production');
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

    /**
     * KUNCI DATA: Mencegah data revisi lama tampil di revisi baru
     */
    private function filterDataByRevisi(array $alldata, string $selectedRevisi, bool $includeEmptyLegacy = false): array
    {
        if (empty($alldata) || $selectedRevisi === '' || strtoupper($selectedRevisi) === 'ALL') {
            return $alldata;
        }

        $filtered = [];
        $selInt   = is_numeric($selectedRevisi) ? (int)$selectedRevisi : null;

        foreach ($alldata as $row) {
            $rowRev = isset($row['revisi']) ? trim((string)$row['revisi']) : '';

            // Jika kolom revisi kosong, cek apakah ada di segmen ke-4 docno
            if (($rowRev === '' || $rowRev === '-') && !empty($row['docno']) && $row['docno'] !== '-') {
                $dParts = explode('-', trim((string)$row['docno']));
                if (count($dParts) >= 4 && is_numeric(end($dParts))) {
                    $rowRev = end($dParts);
                }
            }

            // Data legacy tanpa nomor revisi HANYA boleh tampil di revisi paling tua ($includeEmptyLegacy === true)
            if ($rowRev === '' || $rowRev === '-') {
                if ($includeEmptyLegacy) {
                    $filtered[] = $row;
                }
                continue;
            }

            if ($selInt !== null && is_numeric($rowRev) && (int)$rowRev === $selInt) {
                $filtered[] = $row;
            } elseif (strcasecmp($rowRev, $selectedRevisi) === 0) {
                $filtered[] = $row;
            }
        }

        // Kembalikan $filtered apa adanya (JANGAN fallback ke $alldata jika data revisi baru memang masih kosong!)
        return $filtered;
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
        $weeklyIdParam = null,
        string $selectedRevisi = ''
    ): array {
        $alldata = [];
        if ($typeProcess === 'production') {
            if (!empty($weeklyIdParam)) {
                $alldata = $this->ProductionModel->getByWeeklyId($weeklyIdParam);
            } else {
                $alldata = $this->ProductionModel->getAll(
                    $dateStart,
                    $dateEnd,
                    $process,
                    $model,
                    $lotno,
                    $machno
                );
            }
        } elseif ($typeProcess === 'foregoing') {
            $alldata = $this->ForegoingModel->getAll(
                $dateStart,
                $dateEnd,
                $device,
                $process,
                $model,
                $lotno,
                $machno
            );
        } else {
            $alldata = $this->StartupModel->getAll(
                $dateStart,
                $dateEnd,
                $device,
                $process,
                $model,
                $lotno,
                $machno
            );
        }

        // Ambil daftar revisi Form Builder untuk proses ini
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

        $targetRevToFilter   = ($selectedRevisi !== '' && strtoupper($selectedRevisi) !== 'ALL')
            ? $selectedRevisi
            : (string)($activeRevRow['revisi'] ?? '');
        $isOldestOrSingleRev = (count($revisionsList) <= 1) || ($activeRevRow && $oldestRevRow && $activeRevRow['id'] == $oldestRevRow['id']);
        $alldata             = array_values($this->filterDataByRevisi($alldata, $targetRevToFilter, $isOldestOrSingleRev));

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
                $spvVal = trim((string)($row['supervisor'] ?? ''));
                $ldrVal = trim((string)($row['leader'] ?? ''));
                $frmVal = trim((string)($row['foreman'] ?? ''));

                if ($spvVal !== '' && $spvVal !== '-') {
                    $namaApprover = $spvVal . "\n(Supervisor)";
                } elseif ($ldrVal !== '' && $ldrVal !== '-') {
                    $namaApprover = $ldrVal . "\n(Leader)";
                } elseif ($frmVal !== '' && $frmVal !== '-') {
                    $namaApprover = $frmVal . "\n(Foreman)";
                }
            }

            if ($typeProcess === 'production') {
                $emp2Check  = trim((string)($row['empid2'] ?? ''));
                $name2Check = trim((string)($row['name2'] ?? ''));
                $noOpFinish = (($emp2Check === '' || $emp2Check === '-') && ($name2Check === '' || $name2Check === '-'));
                if ($noOpFinish) {
                    $row['empid2'] = '-';
                    $row['name2']  = '-';
                    $row['shift2'] = '-';
                    $row['group2'] = '-';
                    $row['par009'] = '-';
                    $row['par010'] = '-';
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
            $pattern     = '/^(Production\s+Process\s+Control\s+Sheet|Production\s+Control\s+Sheet|Produciton\s+Control\s+Sheet\s+of|Produciton\s+Control\s+Sheet)\s*/i';
            $cleanName   = trim(preg_replace($pattern, '', $namaProsesRaw));
            $judulProses = 'Production Process Control Sheet ' . $cleanName;
        } elseif ($typeProcess === 'startup' || $typeProcess === 'startup_horizontal') {
            $cleanName   = trim(preg_replace('/\s*Start\s*Up\s*Check\s*Sheet.*$/i', '', $namaProsesRaw));
            $judulProses = $cleanName . ' Start Up Check Sheet';
        } elseif ($typeProcess === 'foregoing') {
            $cleanName   = trim(preg_replace('/^(Foregoing\s+Check\s+Sheet\s+of|Foregoing\s+Check\s+Sheet|Foregoing)\s+/i', '', $namaProsesRaw));
            $cleanName   = trim(preg_replace('/\s*Foregoing\s*Check\s*Sheet.*$/i', '', $cleanName));
            $judulProses = $cleanName . ' Foregoing Check Sheet';
        } else {
            $judulProses = $namaProsesRaw;
        }

        $procParts    = explode('-', $process);
        $fallbackCode = strtoupper($procParts[2] ?? '001');
        $noDok        = $prosesInfo['docno'] ?? ('FF-' . $fallbackCode . '-001');

        $activeRevNum = $activeRevRow['revisi'] ?? (!empty($prosesInfo['revisi']) ? $prosesInfo['revisi'] : '0');
        if ($selectedRevisi !== '' && strtoupper($selectedRevisi) !== 'ALL') {
            $activeRevNum = $selectedRevisi;
        }
        $revisi = str_pad((string)$activeRevNum, 2, '0', STR_PAD_LEFT);

        $docParts = explode('-', $noDok);
        if (count($docParts) >= 4) {
            $docParts[count($docParts) - 1] = $revisi;
            $noDok = implode('-', $docParts);
        }

        $isValidMach = ($machno !== '' && $machno !== 'null' && $machno !== 'ALL');
        $machNo      = $isValidMach ? strtoupper($machno) : 'ALL MACHINES';
        $nama_mesin  = $machNo;

        $tglBerlaku = '-';
        $bulanIndo  = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        if ($latestDate > 0) {
            $mIdx       = (int)date('n', $latestDate) - 1;
            $tglBerlaku = date('d', $latestDate) . ' ' . $bulanIndo[$mIdx] . ' ' . date('Y', $latestDate);
        } elseif (!empty($prosesInfo['berlaku']) && $prosesInfo['berlaku'] !== '0000-00-00') {
            $ts         = strtotime($prosesInfo['berlaku']);
            $mIdx       = (int)date('n', $ts) - 1;
            $tglBerlaku = date('d', $ts) . ' ' . $bulanIndo[$mIdx] . ' ' . date('Y', $ts);
        }

        $weeklyInfo = null;
        try {
            if ($typeProcess === 'startup') {
                $weeklyModel = new \App\Models\WeeklyApprovalModel();
                $weeklyId    = null;
                if (!empty($alldata)) {
                    foreach ($alldata as $r) {
                        if (!empty($r['weekly_id']) && $r['weekly_id'] !== '-') {
                            $weeklyId = $r['weekly_id'];
                            break;
                        }
                    }
                }
                if ($weeklyId) {
                    $weeklyInfo = $weeklyModel->find($weeklyId);
                }

                if (!$weeklyInfo) {
                    $refDate      = '';
                    $firstCreated = $alldata[0]['created_at'] ?? '';
                    if (!empty($firstCreated) && $firstCreated !== '-') {
                        $refDate = $firstCreated;
                    } elseif (!empty($dateStart)) {
                        $refDate = $dateStart;
                    } elseif (!empty($dateEnd)) {
                        $refDate = $dateEnd;
                    }

                    if (!empty($refDate)) {
                        $time   = strtotime($refDate);
                        $monday = date('Y-m-d', strtotime('monday this week', $time));
                        $sunday = date('Y-m-d', strtotime('sunday this week', $time));

                        $weeklyInfo = $weeklyModel->where('period_start', $monday)
                            ->where('period_end', $sunday)
                            ->where('device', null)
                            ->first();

                        if (!$weeklyInfo) {
                            $weeklyInfo = $weeklyModel->where('period_start', $monday)
                                ->where('period_end', $sunday)
                                ->orderBy('id', 'DESC')
                                ->first();
                        }
                    }
                }
            } elseif ($typeProcess === 'production') {
                $weeklyModel    = new \App\Models\WeeklyApprovalModel();
                $targetWeeklyId = !empty($weeklyIdParam) ? $weeklyIdParam : null;
                if (!$targetWeeklyId && !empty($alldata)) {
                    foreach ($alldata as $r) {
                        if (!empty($r['weekly_id']) && $r['weekly_id'] !== '-') {
                            $targetWeeklyId = $r['weekly_id'];
                            break;
                        }
                    }
                }
                if ($targetWeeklyId) {
                    $weeklyInfo = $weeklyModel->find($targetWeeklyId);
                }
            }
        } catch (\Throwable $e) {
            $weeklyInfo = null;
        }

        return compact(
            'alldata',
            'namaProduk',
            'judulProses',
            'noDok',
            'machNo',
            'nama_mesin',
            'prosesInfo',
            'typeProcess',
            'process',
            'device',
            'tglBerlaku',
            'namaApprover',
            'revisi',
            'weeklyInfo',
            'activeRevRow'
        );
    }

    private function renderHistoryHtml(array &$context, bool $isExport = false): string
    {
        $process      = $context['process'] ?? '';
        $typeProcess  = $context['typeProcess'] ?? 'production';
        $device       = !empty($context['device']) ? $context['device'] : (explode('-', $process)[0] ?? '');
        $activeRevRow = $context['activeRevRow'] ?? null;

        if (!$activeRevRow && $process !== '') {
            $revs                    = $this->ensureProcessRevisions($process, $typeProcess);
            $activeRevRow            = $revs[0] ?? null;
            $context['activeRevRow'] = $activeRevRow;
        }

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
                    $activeRevRow            = $this->formRevModel->find($activeRevRow['id']);
                    $context['activeRevRow'] = $activeRevRow;
                    $rawParams               = $this->formParamModel->where('revision_id', $activeRevRow['id'])->orderBy('urutan', 'ASC')->findAll();
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
        $req         = $this->request;
        $typeProcess = (string)$req->getGet('typeProcess');
        $process     = (string)$req->getGet('process');
        $device      = (string)$req->getGet('device');
        $revisi      = (string)($req->getGet('revisi') ?? '');

        $rawStart  = $req->getGet('dateStart');
        $rawEnd    = $req->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model    = (string)($req->getGet('model') ?? '');
        $lotno    = (string)($req->getGet('lotno') ?? '');
        $rawMach  = $req->getGet('machno');
        $machno   = ($rawMach === 'null') ? '' : (string)($rawMach ?? '');
        $weeklyId = $req->getGet('weekly_id');

        try {
            $context = $this->resolveExportContext(
                $typeProcess,
                $process,
                $device,
                $dateStart,
                $dateEnd,
                $model,
                $lotno,
                $machno,
                $weeklyId,
                $revisi
            );

            $_SERVER['REQUEST_URI'] = 'exportExcel';
            $htmlString             = $this->renderHistoryHtml($context, true);

            if (($context['activeRevRow']['kategori_form'] ?? '') === 'startup_horizontal') {
                $context['typeProcess'] = 'startup_horizontal';
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $excelGen = new ExcelGenerator();
            $excelGen->generate($htmlString, $context);
        } catch (\Throwable $e) {
            log_message('error', 'exportExcel gagal: ' . $e->getMessage());
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            die("<h2>EXPORT EXCEL GAGAL</h2><p><b>Pesan:</b> " . esc($e->getMessage()) . "</p>");
        }
    }

    public function exportPDF()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $req         = $this->request;
        $typeProcess = (string)$req->getGet('typeProcess');
        $process     = (string)$req->getGet('process');
        $device      = (string)$req->getGet('device');
        $revisi      = (string)($req->getGet('revisi') ?? '');

        $rawStart  = $req->getGet('dateStart');
        $rawEnd    = $req->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model    = (string)($req->getGet('model') ?? '');
        $lotno    = (string)($req->getGet('lotno') ?? '');
        $rawMach  = $req->getGet('machno');
        $machno   = ($rawMach === 'null') ? '' : (string)($rawMach ?? '');
        $weeklyId = $req->getGet('weekly_id');

        try {
            $context = $this->resolveExportContext(
                $typeProcess,
                $process,
                $device,
                $dateStart,
                $dateEnd,
                $model,
                $lotno,
                $machno,
                $weeklyId,
                $revisi
            );

            $_SERVER['REQUEST_URI'] = 'exportPDF';
            $htmlString             = $this->renderHistoryHtml($context, true);

            if (($context['activeRevRow']['kategori_form'] ?? '') === 'startup_horizontal') {
                $context['typeProcess'] = 'startup_horizontal';
            }

            $pdfGen = new PdfGenerator();
            $pdfGen->generate($htmlString, $context);
        } catch (\Throwable $e) {
            log_message('error', 'exportPDF gagal: ' . $e->getMessage());
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            die("<h2>EXPORT PDF GAGAL</h2><p><b>Pesan:</b> " . esc($e->getMessage()) . "</p>");
        }
    }
}