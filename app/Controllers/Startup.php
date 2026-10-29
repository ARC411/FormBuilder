<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\EmpModel;
use App\Models\LogActivityModel;

class Startup extends BaseController
{
    protected $session;
    private $StartupModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->StartupModel = new StartupModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    private function isRegularOperator(): bool
    {
        $pos = (string) $this->session->get('positionid');
        $isAdmin = (bool) $this->session->get('isadmin');
        if ($isAdmin) {
            return false;
        }
        return (stripos($pos, 'operator') !== false);
    }

    private function getFolderType($processStr)
    {
        if (empty($processStr)) return 'startup';
        $parts = explode('-', $processStr);
        if (count($parts) > 1) {
            if ($parts[1] === 'p') return 'production';
            if ($parts[1] === 'f') return 'foregoing';
        }
        return 'startup';
    }

    /**
     * Mengambil daftar email semua user yang memiliki role Foreman dari DB Secondary (EmpModel) & fallback .env
     */
    /**
     * Mengambil daftar email tujuan Request Reject
     */
    private function getForemanEmails(): array
    {
        // =================================================================
        // MODE UJI COBA (TESTING): Hanya kirim ke 1 email ini dulu.
        // Jika nanti sudah siap dipakai semua Foreman, cukup beri tanda // 
        // di depan baris return di bawah ini.
        // =================================================================
        return ['yusup.alhusaeni@fti-foxconn.com'];

        // =================================================================
        // MODE PRODUCTION (SEMUA FOREMAN) - Aktif otomatis saat baris return di atas di-comment
        // =================================================================
        $emails = [];

        try {
            $empModel = new EmpModel();
            $rows = [];
            if (method_exists($empModel, 'getForemanEmails')) {
                $rows = (array) $empModel->getForemanEmails();
            } elseif (method_exists($empModel, 'testing')) {
                $rows = (array) $empModel->testing();
            }

            foreach ($rows as $r) {
                $r = (array) $r;
                $roleStr = implode(' ', [
                    (string)($r['positionid'] ?? ''),
                    (string)($r['position'] ?? ''),
                    (string)($r['position_name'] ?? ''),
                    (string)($r['jabatan'] ?? ''),
                    (string)($r['role'] ?? ''),
                ]);

                $isForeman = (stripos($roleStr, 'foreman') !== false);
                if (!$isForeman && preg_match('/\b14\b/', (string)($r['positionid'] ?? ''))) {
                    $isForeman = true;
                }

                if ($isForeman) {
                    foreach (['email', 'mail', 'email_address', 'user_email', 'work_email'] as $col) {
                        if (!empty($r[$col]) && filter_var(trim((string)$r[$col]), FILTER_VALIDATE_EMAIL)) {
                            $emails[] = strtolower(trim((string)$r[$col]));
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengambil email Foreman dari EmpModel: ' . $e->getMessage());
        }

        $envEmails = env('email.foremanEmails', '');
        if (!empty($envEmails)) {
            foreach (explode(',', (string)$envEmails) as $em) {
                $em = strtolower(trim($em));
                if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $em;
                }
            }
        }

        return array_values(array_unique($emails));
    }

    public function index()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        // Poin 1: Jika Operator biasa, hanya tampilkan data dari inputan operator itu sendiri
        $empFilter  = $this->isRegularOperator() ? (string) $this->session->get('empid') : null;
        $nameFilter = $this->isRegularOperator() ? (string) $this->session->get('name') : null;

        $data = [
            'title'      => 'Data Startup | Startup Management',
            'alldata'    => $this->StartupModel->getAllData($empFilter, $nameFilter),
            'isOperator' => $this->isRegularOperator(),
        ];
        return view('startup', $data);
    }

    public function formInputStartup()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url() . "login");
        }

        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];

        if (empty($_GET['device']) || empty($_GET['process'])) {
            return "";
        }

        $folder = $this->getFolderType($_GET['process']);

        try {
            return view('/layout/' . $_GET['device'] . "/input/" . $folder . "/" . $_GET['process'], $data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, form input belum tersedia.</div>";
        }
    }

    public function formEdit()
    {
        $number  = $this->request->getGet('number');
        $alldata = $this->StartupModel->getByNumber($number);

        if (empty($alldata)) {
            return "Data tidak ditemukan.";
        }

        $process   = $alldata[0]['process'];
        $rowRevisi = $alldata[0]['revisi'] ?? null;

        try {
            $fb  = new \App\Controllers\FormBuilder();
            $res = $fb->buildOperatorFormPayload($process, 'startup', $alldata, $number, $rowRevisi);
            if (!empty($res['html'])) {
                return $res['html'];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Startup formEdit dynamic fallback: ' . $e->getMessage());
        }

        $folder = $this->getFolderType($process);
        return view("/layout/" . $alldata[0]['device'] . "/input/" . $folder . "/" . $process, [
            'title'   => 'History | Startup Management',
            'alldata' => $alldata,
            'number'  => $number
        ]);
    }

    public function edit($number)
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        $alldata = $this->StartupModel->getByNumber($number);
        if (empty($alldata)) {
            return redirect()->to(base_url('startup'))->with('message', 'Data tidak ditemukan.');
        }

        // Perbaikan Poin 2: Operator bisa mengedit form miliknya jika berstatus 'Reject'
        if ($this->isRegularOperator()) {
            $docEmp  = trim((string)($alldata[0]['empid'] ?? ''));
            $docName = trim((string)($alldata[0]['name'] ?? ''));
            $sesEmp  = trim((string)$this->session->get('empid'));
            $sesName = trim((string)$this->session->get('name'));

            $isOwner = ($sesEmp !== '' && $sesEmp === $docEmp) || ($sesName !== '' && strcasecmp($sesName, $docName) === 0);
            if (!$isOwner) {
                return redirect()->to(base_url('startup'))->with('message', 'Akses ditolak: Anda hanya dapat mengedit data milik Anda sendiri.');
            }

            $isRejected = (($alldata[0]['status'] ?? '') === 'Reject');
            if (!$isRejected) {
                return redirect()->to(base_url('startup'))->with('message', 'Form masih terkunci! Form hanya dapat diperbaiki setelah di-Reject oleh Foreman pada menu Approval.');
            }
        }

        $data = [
            'title'      => 'Edit Startup | Startup Management',
            'validation' => \Config\Services::validation(),
            'alldata'    => $alldata
        ];

        return view('edit-startup', $data);
    }

    /**
     * Poin 3 & 4: Mengirim email permohonan Reject ke semua Foreman di latar belakang
     */
    public function requestReject($number)
    {
        if (!$this->session->has('empid')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Sesi login berakhir, silakan login kembali.']);
        }

        $alldata = $this->StartupModel->getByNumber($number);
        if (empty($alldata)) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Data checksheet tidak ditemukan.']);
        }

        $row = $alldata[0];
        $note = trim((string)$this->request->getPost('reason'));
        if ($note === '') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Alasan / catatan pengajuan reject wajib diisi.']);
        }

        $foremanEmails = $this->getForemanEmails();
        if (empty($foremanEmails)) {
            // Tetap tandai di sistem meskipun daftar email belum ditemukan
            $this->StartupModel->markRejectRequested($number);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Alamat email Foreman tidak ditemukan di database. Pastikan data email Foreman tersedia atau isi email.foremanEmails di file .env.'
            ]);
        }

        $tanggalInput = !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : date('d/m/Y H:i');
        $operatorName = !empty($row['name']) ? $row['name'] : ($this->session->get('name') ?: '-');
        $empId        = !empty($row['empid']) ? $row['empid'] : ($this->session->get('empid') ?: '-');
        $shiftGroup   = ($row['shift'] ?? '-') . ' / ' . ($row['group'] ?? '-');
        $deviceName   = $row['device_name'] ?? $row['device'] ?? '-';
        $processName  = $row['process_name'] ?? $row['process'] ?? '-';
        $docNo        = $row['docno'] ?? '-';
        $machNo       = !empty($row['machno']) ? $row['machno'] : '-';
        $modelName    = !empty($row['model']) ? $row['model'] : '-';
        $lotNo        = !empty($row['lotno']) ? $row['lotno'] : '-';
        $approvalUrl  = base_url('approve');

        $subject = "[Request Reject Startup] {$processName} - Mesin {$machNo} - Lot {$lotNo} ({$operatorName})";

        $htmlMessage = "
        <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
            <h3 style='color: #d9534f; margin-bottom: 5px;'>Permohonan Reject Checksheet Startup</h3>
            <p>Halo Foreman,</p>
            <p>Operator berikut mengajukan permohonan <strong>Reject</strong> pada dokumen <strong>Startup Checksheet</strong> agar data dapat diperbaiki:</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin-bottom: 15px;' border='1' cellpadding='8'>
                <tr style='background-color: #f8f9fa;'><td style='width: 180px;'><strong>Kategori</strong></td><td>Startup Checksheet (#{$number})</td></tr>
                <tr><td><strong>Tanggal Input</strong></td><td>{$tanggalInput}</td></tr>
                <tr style='background-color: #f8f9fa;'><td><strong>Nama Operator</strong></td><td>" . esc($operatorName) . "</td></tr>
                <tr><td><strong>Emp. ID</strong></td><td>" . esc($empId) . "</td></tr>
                <tr style='background-color: #f8f9fa;'><td><strong>Shift / Group</strong></td><td>" . esc($shiftGroup) . "</td></tr>
                <tr><td><strong>Device</strong></td><td>" . esc($deviceName) . "</td></tr>
                <tr style='background-color: #f8f9fa;'><td><strong>Process / Doc. No</strong></td><td>" . esc($processName) . " (" . esc($docNo) . ")</td></tr>
                <tr><td><strong>Machine Number</strong></td><td>" . esc($machNo) . "</td></tr>
                <tr style='background-color: #f8f9fa;'><td><strong>Model</strong></td><td>" . esc($modelName) . "</td></tr>
                <tr><td><strong>Lot Number</strong></td><td>" . esc($lotNo) . "</td></tr>
                <tr style='background-color: #fff3cd;'><td><strong>Alasan Minta Reject (Note Operator)</strong></td><td><strong>" . nl2br(esc($note)) . "</strong></td></tr>
            </table>
            <p>Silakan buka halaman <a href='{$approvalUrl}' style='color: #0d6efd; font-weight: bold;'>Approval Checksheet</a> untuk melakukan Reject pada dokumen tersebut.</p>
        </div>";

        try {
            $emailConfig = new \Config\Email();
            $email = \Config\Services::email($emailConfig);

            $senderEmail = !empty($emailConfig->fromEmail) ? $emailConfig->fromEmail : $emailConfig->SMTPUser;
            $senderName  = !empty($emailConfig->fromName) ? $emailConfig->fromName : 'Checksheet Management System';

            $email->setFrom($senderEmail, $senderName);
            $email->setTo($foremanEmails);
            $email->setMailType('html');
            $email->setNewline("\r\n");
            $email->setCRLF("\r\n");
            $email->setSubject($subject);
            $email->setMessage($htmlMessage);

            if ($email->send(false)) {
                $this->StartupModel->markRejectRequested($number); // Untuk Foregoing.php gunakan: $this->ForegoingModel->markRejectRequested($number);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Permintaan Reject berhasil dikirim ke email: ' . implode(', ', $foremanEmails)
                ]);
            } else {
                $debug = strip_tags((string) $email->printDebugger(['headers']));
                log_message('error', 'Gagal kirim email Request Reject: ' . $debug);

                // Potong pesan debug agar ringkas saat ditampilkan di alert browser
                $shortDebug = trim(preg_replace('/\s+/', ' ', substr($debug, 0, 350)));
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => 'error',
                    'message' => "Gagal mengirim email.\nDetail Server: " . ($shortDebug ?: 'Konfigurasi SMTP belum sesuai.')
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Exception Request Reject: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage()
            ]);
        }
    }

    public function startupByNumber($number)
    {
        $data = [
            'alldata' => $this->StartupModel->getByNumber($number)
        ];

        if (empty($data['alldata'])) return "Data tidak ditemukan.";

        $folder = $this->getFolderType($data['alldata'][0]['process']);

        try {
            $machno = !empty($data['alldata'][0]['machno']) ? esc($data['alldata'][0]['machno']) : '';
            $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machno . '">';
            $fb = new \App\Controllers\FormBuilder();
            $tableHtml = $fb->renderDetailTableByRows($data['alldata'][0]['process'], 'startup', $data['alldata']);
           
            if ($this->request->isAJAX()) {
                return $machnoHidden . $tableHtml;
            }

            $firstRow = $data['alldata'][0];
            return view('startup_detail', [
                'title'       => 'Detail Form Checksheet - ' . ($firstRow['number'] ?? $number),
                'device'      => $firstRow['device'] ?? '',
                'process'     => $firstRow['process'] ?? '',
                'machno'      => $firstRow['machno'] ?? '',
                'lotno'       => $firstRow['lotno'] ?? '',
                'number'      => $firstRow['number'] ?? $number,
                'created_at'  => $firstRow['created_at'] ?? '',
                'status'      => $firstRow['status'] ?? '-',
                'tableHtml'   => $tableHtml,
                'machnoHidden' => $machnoHidden,
                'alldata'     => $data['alldata']
            ]);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            if ($this->request->isAJAX()) {
                return "<div class='alert alert-danger'>Maaf, tampilan detail untuk proses ini belum tersedia.</div>";
            }
            return "<div class='container mt-4'><div class='alert alert-danger'>Maaf, tampilan detail untuk proses ini belum tersedia.</div></div>";
        }
    }

    public function data()
    {
        $processStr = $this->request->getGet('process');
        $deviceStr = $this->request->getGet('device');

        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->StartupModel->getAll(
                $this->request->getGet('dateStart') ?? date('Y-m-d', strtotime('-1 month')),
                $this->request->getGet('dateEnd') ?? date('Y-m-d'),
                $deviceStr,
                $processStr,
                $this->request->getGet('model') ?? '',
                $this->request->getGet('lotno') ?? '',
                $this->request->getGet('machno') ?? ''
            )
        ];

        $folder = $this->getFolderType($processStr);

        try {
            return view("/layout/" . $deviceStr . "/history/" . $folder . "/" . $processStr, $data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, tampilan history untuk proses " . $processStr . " belum tersedia (File tidak ditemukan).</div>";
        }
    }

    public function dataStartup()
    {
        return $this->data();
    }

    public function deleteStartup($number)
    {
        if (!$this->session->has('empid')) {
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to(base_url() . "login");
        }
        $data = [
            'validation' => \Config\Services::validation(),
            'alldata' => $this->StartupModel->deleteByNumber($number)
        ];
        return "berhasil";
    }

    public function updateStartup($number = null)
    {
        return $this->createStartup();
    }

    public function createStartup()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url() . "login");
        }
        $data = [
            'title' => 'Create Startup',
            'validation' => \Config\Services::validation()
        ];

        $isEdit = !empty($this->request->getVar('number-edit'));
        $number = $isEdit ? $this->request->getVar('number-edit') : ($this->StartupModel->getLatestId() + 1);
        $cnt_input = $this->request->getVar('cnt-proses') ? $this->request->getVar('cnt-proses') : 1;

        $operatorName = trim((string)$this->request->getVar('name-txt'));
        if ($operatorName === '' || $operatorName === '-') {
            $operatorName = trim((string)($this->session->get('name') ?? $this->session->get('username') ?? $this->session->get('empid') ?? ''));
        }

        $redirectUrl = $isEdit ? base_url('startup') : base_url('input');

        $suffixes = ['a', 'b', 'c', 'd', 'e'];
        foreach ($suffixes as $idx => $sfx) {
            $blockNo = $idx + 1;
            $rowPayload = [
                'number'  => $number,
                'par000'  => $blockNo,
                'device'  => (string)$this->request->getVar('device-txt'),
                'model'   => (string)$this->request->getVar('model-txt'),
                'process' => (string)$this->request->getVar('process-txt'),
                'lotno'   => (string)$this->request->getVar('lotno-txt'),
                'machno'  => (string)$this->request->getVar('machno-txt'),
                'empid'   => (string)$this->request->getVar('empid-txt'),
                'group'   => (string)$this->request->getVar('group-txt'),
                'shift'   => (string)$this->request->getVar('shift-txt'),
                'name'    => $operatorName,
            ];

            for ($p = 1; $p <= 44; $p++) {
                $key = 'par' . str_pad((string)$p, 3, '0', STR_PAD_LEFT);
                $valSfx = $this->request->getVar($key . $sfx);
                if ($blockNo === 1) {
                    $finalVal = ($valSfx !== null) ? $valSfx : $this->request->getVar($key);
                    // Fallback anti-gagal: jika input hidden kosong tapi _val diisi operator, gabungkan langsung di server!
                    if (($finalVal === null || trim((string)$finalVal) === '') && $this->request->getVar($key . '_val') !== null) {
                        $rawNum = str_replace(',', '.', trim((string)$this->request->getVar($key . '_val')));
                        $rawUnit = trim((string)($this->request->getVar($key . '_unit') ?? ''));
                        if ($rawNum !== '') {
                            $finalVal = $rawUnit !== '' ? ($rawNum . ' ' . $rawUnit) : $rawNum;
                        }
                    }
                    $rowPayload[$key] = $finalVal;
                } else {
                    $rowPayload[$key] = $valSfx;
                }
            }

            $res = $this->StartupModel->addProses($rowPayload);

            if ((int)$cnt_input === $blockNo) {
                session()->setFlashdata('message', $res ? ($isEdit ? 'Perbaikan Form Startup Berhasil Disimpan!' : 'Input Berhasil') : 'Input Failed!');
                return redirect()->to($redirectUrl);
            }
        }

        return redirect()->to($redirectUrl);
    }
}
