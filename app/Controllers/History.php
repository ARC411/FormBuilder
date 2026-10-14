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
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ForegoingModel->getAll($_GET['dateStart'], $_GET['dateEnd'], $_GET['device'], $_GET['process'])
        ];
        return view("/layout/" . $_GET['device'] . "/history/foregoing/" . $_GET['process'], $data);
    }

    public function foregoingByNumber($number)
    {
        $data = ['alldata' => $this->ForegoingModel->getByNumber($number)];
        return view("/layout/" . $data['alldata'][0]['device'] . "/history/foregoing/" . $data['alldata'][0]['process'], $data);
    }

    // =========================================================================
    // DAPUR DATA: RESOLVER UNTUK GENERATOR EXPORT (KOP SURAT & CLEANER)
    // =========================================================================

    private function getProsesInfo(string $processCode): array
    {
        $db = \Config\Database::connect();
        $row = $db->table('proses')->where('process_code', $processCode)->get()->getRowArray();
        return $row ?: [];
    }

    private function getDeviceInfo(string $deviceCode): array
    {
        $db = \Config\Database::connect();
        $row = $db->table('device')->where('code', $deviceCode)->get()->getRowArray();
        return $row ?: [];
    }

    private function resolveExportContext(string $typeProcess, string $process, string $device, string $dateStart, string $dateEnd, string $model = '', string $lotno = '', string $machno = ''): array
    {
        // 1. Tarik Data Mentah
        $alldata = [];
        if ($typeProcess === 'production') {
            $alldata = $this->ProductionModel->getAll($dateStart, $dateEnd, $process, $model, $lotno, $machno);
        } elseif ($typeProcess === 'foregoing') {
            $alldata = $this->ForegoingModel->getAll($dateStart, $dateEnd, $device, $process, $model, $lotno, $machno);
        } else {
            $alldata = $this->StartupModel->getAll($dateStart, $dateEnd, $device, $process, $model, $lotno, $machno);
        }

        // 2. PROSES LOGIKA KOP SURAT & DATA CLEANER
        $latestDate = 0;
        $namaApprover = '';

        foreach ($alldata as &$row) {
            // A. Cari Tanggal Terbaru (Dinamis Berlaku)
            $rowDate = 0;
            if (!empty($row['created_at'])) {
                $rowDate = strtotime($row['created_at']);
            } elseif (!empty($row['date'])) {
                $rowDate = strtotime($row['date']);
            }
            
            if ($rowDate > $latestDate) {
                $latestDate = $rowDate;
            }

            // B. Cari Nama Approver (Prioritas tertinggi ke rendah) - DIUBAH DENGAN FORMAT JABATAN
            if ($namaApprover === '') {
                if (!empty($row['supervisor']) && $row['supervisor'] !== '-') { 
                    $namaApprover = trim($row['supervisor']) . "\n(Supervisor)"; 
                }
                elseif (!empty($row['leader']) && $row['leader'] !== '-') { 
                    $namaApprover = trim($row['leader']) . "\n(Leader)"; 
                }
                elseif (!empty($row['foreman']) && $row['foreman'] !== '-') { 
                    $namaApprover = trim($row['foreman']) . "\n(Foreman)"; 
                }
            }

            // C. Data Cleaner (Sapu bersih data kosong/null menjadi "-")
            foreach ($row as $key => $value) {
                if ($value === null || trim((string)$value) === '') {
                    $row[$key] = '-';
                }
            }
        }
        unset($row);

        // 3. Olah Format Kop Surat
        $prosesInfo = $this->getProsesInfo($process);
        $deviceInfo = $this->getDeviceInfo($device);

        $namaProduk = $deviceInfo['name'] ?? strtoupper($device);
        $namaProsesRaw = $prosesInfo['name'] ?? strtoupper($process);

        if ($typeProcess === 'production') {
            $cleanName = trim(preg_replace('/^(Production\s+Process\s+Control\s+Sheet|Production\s+Control\s+Sheet|Produciton\s+Control\s+Sheet\s+of|Produciton\s+Control\s+Sheet)\s*/i', '', $namaProsesRaw));
            $judulProses = 'Production Process Control Sheet ' . $cleanName;
        } elseif ($typeProcess === 'startup') {
            $cleanName = trim(preg_replace('/\s*Start\s*Up\s*Check\s*Sheet.*$/i', '', $namaProsesRaw));
            $judulProses = $cleanName . ' Start Up Check Sheet';
        } else {
            $judulProses = $namaProsesRaw;
        }

        $noDok = $prosesInfo['docno'] ?? ('FF-' . strtoupper(explode('-', $process)[2] ?? '001') . '-001');
        $revisi = str_pad(!empty($prosesInfo['revisi']) ? $prosesInfo['revisi'] : 0, 2, '0', STR_PAD_LEFT);
        
        // --- PERBAIKAN LOGIKA NAMA MESIN DI SINI ---
        // Jika parameter machno dikirimkan dan bukan string kosong/'null'/'ALL', tampilkan nama mesin tersebut.
        // Jika kosong, berarti user ingin melihat semua mesin, maka tampilkan "ALL MACHINES".
        $machNo = ($machno !== '' && $machno !== 'null' && $machno !== 'ALL') ? strtoupper($machno) : 'ALL MACHINES';
        
        // Definisikan juga variabel $nama_mesin agar pasti nyambung dengan kop_surat.php
        $nama_mesin = $machNo;

        $tglBerlaku = '-';
        if ($latestDate > 0) {
            $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tglBerlaku = date('d', $latestDate) . ' ' . $bulanIndo[date('n', $latestDate) - 1] . ' ' . date('Y', $latestDate);
        } elseif (!empty($prosesInfo['berlaku']) && $prosesInfo['berlaku'] !== '0000-00-00') {
            $ts = strtotime($prosesInfo['berlaku']);
            $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $tglBerlaku = date('d', $ts) . ' ' . $bulanIndo[date('n', $ts) - 1] . ' ' . date('Y', $ts);
        }

        // --- Variabel $nama_mesin ditambahkan ke return compact ---
        return compact('alldata', 'namaProduk', 'judulProses', 'noDok', 'machNo', 'nama_mesin', 'prosesInfo', 'typeProcess', 'process', 'tglBerlaku', 'namaApprover', 'revisi');
    }

    // =========================================================================
    // EXPORT METHOD INTI
    // =========================================================================

    public function exportExcel()
    {
        $typeProcess = $this->request->getGet('typeProcess');
        $process     = $this->request->getGet('process');
        $device      = $this->request->getGet('device');

        $rawStart = $this->request->getGet('dateStart');
        $rawEnd   = $this->request->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model = $this->request->getGet('model') ?? '';
        $lotno = $this->request->getGet('lotno') ?? '';
        $machno = $this->request->getGet('machno') === 'null' ? '' : ($this->request->getGet('machno') ?? '');

        try {
            $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno);

            $_SERVER['REQUEST_URI'] = 'exportExcel';
            $viewPath = "/layout/" . $device . "/history/" . $typeProcess . "/" . $process;
            $htmlString = view($viewPath, ['alldata' => $context['alldata']]);

            // Bersihkan output yang mungkin sudah tercetak sebelum header file dikirim
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

        $rawStart = $this->request->getGet('dateStart');
        $rawEnd   = $this->request->getGet('dateEnd');
        $dateStart = $rawStart ? date('Y-m-d', strtotime($rawStart)) . ' 00:00:00' : '';
        $dateEnd   = $rawEnd ? date('Y-m-d', strtotime($rawEnd)) . ' 23:59:59' : '';

        $model = $this->request->getGet('model') ?? '';
        $lotno = $this->request->getGet('lotno') ?? '';
        $machno = $this->request->getGet('machno') === 'null' ? '' : ($this->request->getGet('machno') ?? '');

        $context = $this->resolveExportContext($typeProcess, $process, $device, $dateStart, $dateEnd, $model, $lotno, $machno);

        $_SERVER['REQUEST_URI'] = 'exportPDF';
        $viewPath = "/layout/" . $device . "/history/" . $typeProcess . "/" . $process;
        try {
            $htmlString = view($viewPath, ['alldata' => $context['alldata']]);
        } catch (\Exception $e) {
            die("<h2>SYSTEM ERROR DALAM VIEW HTML:</h2><p><b>Pesan:</b> " . $e->getMessage() . "</p><p><b>Lokasi:</b> " . $e->getFile() . " (Baris: " . $e->getLine() . ")</p>"); 
        }

        $pdfGen = new PdfGenerator();
        $pdfGen->generate($htmlString, $context);
    }
}