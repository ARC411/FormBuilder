<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ForegoingModel;
use App\Models\EmpModel;
use App\Models\LogActivityModel;

class Foregoing extends BaseController
{
    protected $session;
    private $ForegoingModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->ForegoingModel = new ForegoingModel();
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

    public function index(): string
    {
        if (!$this->session->has('empid')) {
            return redirect()->to('/checkseet/login');
        }

        $empid = trim((string) $this->session->get('empid'));
        $name  = trim((string) $this->session->get('name'));
        $role  = strtolower(trim((string) $this->session->get('role')));
        $level = (int) ($this->session->get('level') ?? 99);

        $isApproverOrAdmin = (
            in_array($role, ['admin', 'foreman', 'leader', 'supervisor'], true) ||
            strtolower($empid) === 'admin' ||
            $level === 14 ||
            $level === 10 ||
            ($level > 0 && $level <= 7)
        );

        $filterEmpId = $isApproverOrAdmin ? null : $empid;
        $filterName  = $isApproverOrAdmin ? null : $name;

        $data = [
            'title'         => 'Data Foregoing | Startup Management',
            'alldata'       => $this->ForegoingModel->getAllData($filterEmpId, $filterName),
            'rejectedCount' => $this->ForegoingModel->getRejectedCountByOperator($empid, $name),
        ];

        return view('foregoing', $data);
    }

    public function data()
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ForegoingModel->getAll(
                $_GET['dateStart'], 
                $_GET['dateEnd'], 
                $_GET['device'], 
                $_GET['process'], 
                $_GET['model'] ?? '', 
                $_GET['lotno'] ?? '', 
                $_GET['machno'] ?? ''
            )
        ];
        return view("/layout/".$_GET['device']."/history/foregoing/".$_GET['process'],$data);
    }

    public function formEdit()
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ForegoingModel->getByNumber($_GET['number']),
            'number' => $_GET['number']
        ];
        return view("/layout/".$data['alldata'][0]['device']."/input/foregoing/".$data['alldata'][0]['process'],$data);
    }

    public function edit($number)
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        $alldata = $this->ForegoingModel->getByNumber($number);
        if (empty($alldata)) {
            return redirect()->to(base_url('foregoing'))->with('message', 'Data tidak ditemukan.');
        }

        // Perbaikan Poin 2: Operator dapat memperbaiki form ketika berstatus 'Reject'
        if ($this->isRegularOperator()) {
            $isRejected = (($alldata[0]['status'] ?? '') === 'Reject');
            if (!$isRejected) {
                return redirect()->to(base_url('foregoing'))->with('message', 'Form masih terkunci! Form hanya dapat diperbaiki setelah di-Reject oleh Foreman.');
            }
        }

        $data = [
            'title'      => 'Edit Foregoing | Startup Management',
            'validation' => \Config\Services::validation(),
            'alldata'    => $alldata
        ];
        
        return view('edit-foregoing', $data);
    }

    /**
     * Poin 3 & 4: Mengirim email permohonan Reject Foregoing ke semua Foreman di latar belakang
     */
    public function requestReject($number)
    {
        if (!$this->session->has('empid')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Sesi login berakhir, silakan login kembali.']);
        }

        $alldata = $this->ForegoingModel->getByNumber($number);
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
            $this->ForegoingModel->markRejectRequested($number);
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

        $subject = "[Request Reject Foregoing] {$processName} - Mesin {$machNo} - Lot {$lotNo} ({$operatorName})";

        $htmlMessage = "
        <div style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
            <h3 style='color: #d9534f; margin-bottom: 5px;'>Permohonan Reject Checksheet Foregoing</h3>
            <p>Halo Foreman,</p>
            <p>Operator berikut mengajukan permohonan <strong>Reject</strong> pada dokumen <strong>Foregoing Checksheet</strong> agar data dapat diperbaiki:</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin-bottom: 15px;' border='1' cellpadding='8'>
                <tr style='background-color: #f8f9fa;'><td style='width: 180px;'><strong>Kategori</strong></td><td>Foregoing Checksheet (#{$number})</td></tr>
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
            $email = \Config\Services::email();
            $email->setMailType('html');
            $email->setTo($foremanEmails);
            $email->setSubject($subject);
            $email->setMessage($htmlMessage);

            if ($email->send()) {
                $this->ForegoingModel->markRejectRequested($number);
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Permintaan Reject berhasil dikirim via Gmail ke Foreman (' . count($foremanEmails) . ' penerima).'
                ]);
            } else {
                $debug = $email->printDebugger(['headers']);
                log_message('error', 'Gagal kirim email Request Reject Foregoing: ' . $debug);
                return $this->response->setStatusCode(500)->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal mengirim email. Pastikan konfigurasi SMTP Gmail di .env / app/Config/Email.php sudah benar.'
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Exception Request Reject Foregoing: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteForegoing($number)
    {
        if(!$this->session->has('empid')){
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to(base_url()."login");
        }
        $result = $this->ForegoingModel->deleteByNumber($number);
        if ($result) {
            return "berhasil";
        }
        return "gagal";
    }

    public function foregoingByNumber($number)
    {
        $data = ['alldata' => $this->ForegoingModel->getByNumber($number)];
        if(empty($data['alldata'])) return "Data tidak ditemukan.";
        
        $folder = 'foregoing';
        try {
            $machno = !empty($data['alldata'][0]['machno']) ? esc($data['alldata'][0]['machno']) : '';
            $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machno . '">';
            return $machnoHidden . view("/layout/".$data['alldata'][0]['device']."/history/".$folder."/".$data['alldata'][0]['process'],$data); 
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, tampilan detail untuk proses ini belum tersedia.</div>";
        }
    }

    public function formInputForegoing()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }

        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        
        if($_GET['device']=='' || $_GET['process']==''){
            return "";
        }

        return view('/layout/'.$_GET['device']."/input/foregoing/".$_GET['process'],$data);
    }

    public function createForegoing()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }

        $isEdit = !empty($this->request->getVar('number-edit'));
        $number = $isEdit ? $this->request->getVar('number-edit') : ($this->ForegoingModel->getLatestId()+1);
        $cnt_input = $this->request->getVar('cnt-proses') ? $this->request->getVar('cnt-proses') : 1; 

        $operatorName = trim((string)$this->request->getVar('name-txt'));
        if ($operatorName === '' || $operatorName === '-') {
            $operatorName = trim((string)($this->session->get('name') ?? $this->session->get('username') ?? $this->session->get('empid') ?? ''));
        }

        $redirectUrl = $isEdit ? base_url('foregoing') : base_url('input');

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
                    $rowPayload[$key] = ($valSfx !== null) ? $valSfx : $this->request->getVar($key);
                } else {
                    $rowPayload[$key] = $valSfx;
                }
            }

            $res = $this->ForegoingModel->addProses($rowPayload);

            if ((int)$cnt_input === $blockNo) {
                session()->setFlashdata('message', $res ? ($isEdit ? 'Perbaikan Form Foregoing Berhasil Disimpan!' : 'Input success!') : 'Input Failed!');
                return redirect()->to($redirectUrl);
            }
        }

        return redirect()->to($redirectUrl);
    }
}