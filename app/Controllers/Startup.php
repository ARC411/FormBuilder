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

    public function index()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        $empFilter  = $this->isRegularOperator() ? (string) $this->session->get('empid') : null;
        $nameFilter = $this->isRegularOperator() ? (string) $this->session->get('name') : null;

        $dateStart = $this->request->getGet('dateStart');
        $dateEnd   = $this->request->getGet('dateEnd');

        $data = [
            'title'      => 'Data Startup | Startup Management',
            'alldata'    => $this->StartupModel->getAllData($empFilter, $nameFilter, $dateStart, $dateEnd),
            'isOperator' => $this->isRegularOperator(),
            'dateStart'  => $dateStart,
            'dateEnd'    => $dateEnd,
        ];
        return view('startup', $data);
    }
    public function formInputStartup()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }

        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        
        if(empty($_GET['device']) || empty($_GET['process'])){
            return "";
        }

        $folder = $this->getFolderType($_GET['process']);
        
        try {
            return view('/layout/'.$_GET['device']."/input/".$folder."/".$_GET['process'],$data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, form input belum tersedia.</div>";
        }
    }

    public function formEdit()
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->StartupModel->getByNumber($_GET['number']),
            'number' => $_GET['number']
        ];
        
        if(empty($data['alldata'])) return "Data tidak ditemukan.";
        
        $folder = $this->getFolderType($data['alldata'][0]['process']);
        try {
            return view("/layout/".$data['alldata'][0]['device']."/input/".$folder."/".$data['alldata'][0]['process'],$data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, form edit belum tersedia.</div>";
        }
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
     * MENYIMPAN ALASAN REJECT DAN MEMBERIKAN NOTIFIKASI KE FOREMAN (Tanpa Email)
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

        $note = trim((string)$this->request->getPost('reason'));
        if ($note === '') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Alasan / catatan pengajuan reject wajib diisi.']);
        }

        // Kirim note ke Model untuk disimpan di database dan mengubah status
        $this->StartupModel->markRejectRequested($number, $note);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Notifikasi permintaan reject berhasil dikirim dan akan muncul di layar Approval Foreman.'
        ]);
    }

    public function startupByNumber($number)
    {
        $data = [
            'alldata' => $this->StartupModel->getByNumber($number)
        ];

        if(empty($data['alldata'])) return "Data tidak ditemukan.";

        $folder = $this->getFolderType($data['alldata'][0]['process']);
        
        try {
            $machno = !empty($data['alldata'][0]['machno']) ? esc($data['alldata'][0]['machno']) : '';
            $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machno . '">';
            $tableHtml = view("/layout/".$data['alldata'][0]['device']."/history/".$folder."/".$data['alldata'][0]['process'],$data);

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
                'machnoHidden'=> $machnoHidden,
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
            return view("/layout/".$deviceStr."/history/".$folder."/".$processStr, $data);
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
        if(!$this->session->has('empid')){
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to(base_url()."login");
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
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        $data = [
            'title' => 'Create Startup',
            'validation' => \Config\Services::validation()
        ];

        $isEdit = !empty($this->request->getVar('number-edit'));
        $number = $isEdit ? $this->request->getVar('number-edit') : ($this->StartupModel->getLatestId()+1);
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
