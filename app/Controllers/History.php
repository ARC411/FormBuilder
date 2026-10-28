<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InputModel;
use App\Models\StartupModel;
use App\Models\ProductionModel;
use App\Models\ForegoingModel;
use App\Models\EmpModel;
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
    private ?array $nameToEmpIdMap = null;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->InputModel = new InputModel();
        $this->ProductionModel = new ProductionModel();
        $this->StartupModel = new StartupModel();
        $this->ForegoingModel = new ForegoingModel();
        $this->EmpModel = new EmpModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function index(): string
    {
        error_reporting(0);
        $data = ['title' => 'History | Startup Management'];
        return view('history', $data);
    }

    public function dataForegoing()
    {
        $req = $this->request;
        $dStart = $req->getGet('dateStart');
        $dEnd = $req->getGet('dateEnd');
        $dev = $req->getGet('device');
        $proc = $req->getGet('process');

        $alldata = $this->ForegoingModel->getAll(
            $dStart,
            $dEnd,
            $dev,
            $proc
        );

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
        $data = ['alldata' => $rows];
        $dev = $rows[0]['device'];
        $proc = $rows[0]['process'];
        $viewPath = "/layout/" . $dev . "/history/foregoing/" . $proc;
        return view($viewPath, $data);
    }

    private function getProsesInfo(string $processCode): array
    {
        $db = \Config\Database::connect();
        $row = $db->table('proses')
            ->where('process_code', $processCode)
            ->get()
            ->getRowArray();
        return $row ?: [];
    }

    private function getDeviceInfo(string $deviceCode): array
    {
        $db = \Config\Database::connect();
        $row = $db->table('device')
            ->where('code', $deviceCode)
            ->get()
            ->getRowArray();
        return $row ?: [];
    }

    /**
     * Mengubah Nama Karyawan / Foreman / Approver menjadi EmpID berdasarkan database second (tbempinfa)
     */
    private function resolveNameToEmpId(string $val): string
    {
        $clean = trim($val);
        if ($clean === '' || $clean === '-') {
            return '-';
        }

        // Jika sudah berbentuk EmpID (angka / kode pendek tanpa spasi), langsung kembalikan
        if (preg_match('/^[0-9A-Za-z_-]{3,12}$/', $clean) && preg_match('/\d/', $clean)) {
            return $clean;
        }

        if ($this->nameToEmpIdMap === null) {
            $this->nameToEmpIdMap = [];
            try {
                $db2 = \Config\Database::connect('second');
                $rows = $db2->query("SELECT empid, firstname, midname, lastname FROM tbempinfa")->getResultArray();
                foreach ($rows as $r) {
                    $empId = trim((string) ($r['empid'] ?? ''));
                    if ($empId === '') {
                        continue;
                    }
                    $full = trim(($r['firstname'] ?? '') . ' ' . ($r['midname'] ?? '') . ' ' . ($r['lastname'] ?? ''));
                    $fullNorm = strtoupper((string) preg_replace('/\s+/', ' ', $full));
                    if ($fullNorm !== '') {
                        $this->nameToEmpIdMap[$fullNorm] = $empId;
                    }
                    $firstLast = trim(($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? ''));
                    $flNorm = strtoupper((string) preg_replace('/\s+/', ' ', $firstLast));
                    if ($flNorm !== '' && !isset($this->nameToEmpIdMap[$flNorm])) {
                        $this->nameToEmpIdMap[$flNorm] = $empId;
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika gagal koneksi DB second
            }
        }

        $lookupKey = strtoupper((string) preg_replace('/\s+/', ' ', $clean));
        return $this->nameToEmpIdMap[$lookupKey] ?? $clean;
    }

    private function resolveExportContext(
        string $typeProcess,
        string $process,
        string $device,
        string $dateStart,
        string $dateEnd,
        string $model = '',
        string $lotno = '',
        string $machno = ''
    ): array {
        $alldata = [];
        if ($typeProcess === 'production') {
            $alldata = $this->ProductionModel->getAll(
                $dateStart,
                $dateEnd,
                $process,
                $model,
                $lotno,
                $machno
            );
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

        $latestDate = 0;
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

            // Konversi Foreman, Leader, Supervisor, dan Operator menjadi EmpID
            if (!empty($row['empid']) && $row['empid'] !== '-') {
                $row['name'] = trim((string) $row['empid']);
            } elseif (!empty($row['name']) && $row['name'] !== '-') {
                $row['empid'] = $this->resolveNameToEmpId((string) $row['name']);
                $row['name']  = $row['empid'];
            }

            foreach (['supervisor', 'leader', 'foreman'] as $appCol) {
                if (!empty($row[$appCol]) && $row[$appCol] !== '-') {
                    $row[$appCol] = $this->resolveNameToEmpId((string) $row[$appCol]);
                }
            }

            if ($namaApprover === '') {
                $spvVal = trim((string) ($row['supervisor'] ?? ''));
                $ldrVal = trim((string) ($row['leader'] ?? ''));
                $frmVal = trim((string) ($row['foreman'] ?? ''));

                if ($spvVal !== '' && $spvVal !== '-') { 
                    $namaApprover = $spvVal . "\n(Supervisor)"; 
                } elseif ($ldrVal !== '' && $ldrVal !== '-') { 
                    $namaApprover = $ldrVal . "\n(Leader)"; 
                } elseif ($frmVal !== '' && $frmVal !== '-') { 
                    $namaApprover = $frmVal . "\n(Foreman)"; 
                }
            }

            if ($typeProcess === 'production') {
                $emp2Check = trim((string) ($row['empid2'] ?? ''));
                $name2Check = trim((string) ($row['name2'] ?? ''));
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
                if ($value === null || trim((string) $value) === '') {
                    $row[$key] = '-';
                }
            }
        }
        unset($row);

        $prosesInfo = $this->getProsesInfo($process);
        $deviceInfo = $this->getDeviceInfo($device);

        $namaProduk = $deviceInfo['name'] ?? strtoupper($device);
        $namaProsesRaw = $prosesInfo['name'] ?? strtoupper($process);

        if ($typeProcess === 'production') {
            $pattern = '/^(Production\s+Process\s+Control\s+Sheet|Production\s+Control\s+Sheet|Produciton\s+Control\s+Sheet\s+of|Produciton\s+Control\s+Sheet)\s*/i';
            $cleanName = trim(preg_replace($pattern, '', $namaProsesRaw));
            $judulProses = 'Production Process Control Sheet ' . $cleanName;
        } elseif ($typeProcess === 'startup') {
            $cleanName = trim(preg_replace('/\s*Start\s*Up\s*Check\s*Sheet.*$/i', '', $namaProsesRaw));
            $judulProses = $cleanName . ' Start Up Check Sheet';
        } elseif ($typeProcess === 'foregoing') {
            $cleanName = trim(preg_replace('/^(Foregoing\s+Check\s+Sheet\s+of|Foregoing\s+Check\s+Sheet|Foregoing)\s+/i', '', $namaProsesRaw));
            $cleanName = trim(preg_replace('/\s*Foregoing\s*Check\s*Sheet.*$/i', '', $cleanName));
            $judulProses = $cleanName . ' Foregoing Check Sheet';
        } else {
            $judulProses = $namaProsesRaw;
        }

        $procParts = explode('-', $process);
        $fallbackCode = strtoupper($procParts[2] ?? '001');
        $noDok = $prosesInfo['docno'] ?? ('FF-' . $fallbackCode . '-001');
        $revRaw = !empty($prosesInfo['revisi']) ? $prosesInfo['revisi'] : 0;
        $revisi = str_pad((string) $revRaw, 2, '0', STR_PAD_LEFT);
        
        $isValidMach = ($machno !== '' && $machno !== 'null' && $machno !== 'ALL');
        $machNo = $isValidMach ? strtoupper($machno) : 'ALL MACHINES';
        $nama_mesin = $machNo;

        $tglBerlaku = '-';
        $bulanIndo = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        if ($latestDate > 0) {
            $mIdx = (int) date('n', $latestDate) - 1;
            $tglBerlaku = date('d', $latestDate) . ' ' . $bulanIndo[$mIdx] . ' ' . date('Y', $latestDate);
        } elseif (!empty($prosesInfo['berlaku']) && $prosesInfo['berlaku'] !== '0000-00-00') {
            $ts = strtotime($prosesInfo['berlaku']);
            $mIdx = (int) date('n', $ts) - 1;
            $tglBerlaku = date('d', $ts) . ' ' . $bulanIndo[$mIdx] . ' ' . date('Y', $ts);
        }

        $weeklyInfo = null;
        if ($typeProcess === 'startup') {
            $weeklyModel = new \App\Models\WeeklyApprovalModel();
            
            $weeklyId = null;
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
                $refDate = '';
                $firstCreated = $alldata[0]['created_at'] ?? '';
                if (!empty($firstCreated) && $firstCreated !== '-') {
                    $refDate = $firstCreated;
                } elseif (!empty($dateStart)) {
                    $refDate = $dateStart;
                } elseif (!empty($dateEnd)) {
                    $refDate = $dateEnd;
                }

                if (!empty($refDate)) {
                    $time = strtotime($refDate);
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

            if ($weeklyInfo) {
                if (!empty($weeklyInfo['approver_qc'])) {
                    $weeklyInfo['approver_qc'] = $this->resolveNameToEmpId((string) $weeklyInfo['approver_qc']);
                }
                if (!empty($weeklyInfo['approver_production'])) {
                    $weeklyInfo['approver_production'] = $this->resolveNameToEmpId((string) $weeklyInfo['approver_production']);
                }
            }
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
            'tglBerlaku',
            'namaApprover',
            'revisi',
            'weeklyInfo'
        );
    }

    public function exportExcel()
    {
        $req = $this->request;
        $typeProcess = (string) $req->getGet('typeProcess');
        $process     = (string) $req->getGet('process');
        $device      = (string) $req->getGet('device');

        $rawStart = $req->getGet('dateStart');
        $rawEnd   = $req->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model = (string) ($req->getGet('model') ?? '');
        $lotno = (string) ($req->getGet('lotno') ?? '');
        $rawMach = $req->getGet('machno');
        $machno = ($rawMach === 'null') ? '' : (string) ($rawMach ?? '');

        try {
            $context = $this->resolveExportContext(
                $typeProcess,
                $process,
                $device,
                $dateStart,
                $dateEnd,
                $model,
                $lotno,
                $machno
            );

            $_SERVER['REQUEST_URI'] = 'exportExcel';
            $viewPath = "/layout/" . $device . "/history/" . $typeProcess . "/" . $process;
            $htmlString = view($viewPath, ['alldata' => $context['alldata']]);

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

        $req = $this->request;
        $typeProcess = (string) $req->getGet('typeProcess');
        $process     = (string) $req->getGet('process');
        $device      = (string) $req->getGet('device');

        $rawStart = $req->getGet('dateStart');
        $rawEnd   = $req->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model = (string) ($req->getGet('model') ?? '');
        $lotno = (string) ($req->getGet('lotno') ?? '');
        $rawMach = $req->getGet('machno');
        $machno = ($rawMach === 'null') ? '' : (string) ($rawMach ?? '');

        try {
            $context = $this->resolveExportContext(
                $typeProcess,
                $process,
                $device,
                $dateStart,
                $dateEnd,
                $model,
                $lotno,
                $machno
            );

            $_SERVER['REQUEST_URI'] = 'exportPDF';
            $viewPath = "/layout/" . $device . "/history/" . $typeProcess . "/" . $process;
            $htmlString = view($viewPath, ['alldata' => $context['alldata']]);

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