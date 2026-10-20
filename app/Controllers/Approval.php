<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\LogActivityModel;
use App\Models\WeeklyApprovalModel;
use App\Models\ProductionModel;

class Approval extends BaseController
{
    protected $session;
    protected $StartupModel;
    protected $ProductionModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->StartupModel = new StartupModel();
        $this->ProductionModel = new ProductionModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    // Menampilkan data 
    public function index()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }
        $data = [
            'title' => 'Approval | Startup Management'
        ];

        return view("approval",$data);
    }

    public function formApproval()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }
        
        $dateStart = $this->request->getGet('dateStart') ?? date('Y-m-d', strtotime('-1 month'));
        $dateEnd = $this->request->getGet('dateEnd') ?? date('Y-m-d');

        $data = [
            'title' => 'Approval | Startup Management',
            'alldata' => $this->StartupModel->getApprovalData($dateStart, $dateEnd)
        ];

        return view("layout/approval-table",$data);
    }

    public function approvalById($id)
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->getByNumber($id)
        ];

        return view("/layout/history/".$this->ProductionModel->getByNumber($id)[0]['process'],$data);
    }
    
    public function updateApproval()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        
        $number = $this->request->getVar('number');
        $name = $this->request->getVar('name');
        $level = $this->request->getVar('level');
        
        $update = $this->StartupModel->updateApproval($number, $name, $level);
        return redirect()->to(base_url().'approve');
    }

    public function weekly()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        
        $role = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));

        $is_qc = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $is_prod = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        $data = [
            'title'       => 'Weekly Approval | Startup Management',
            'weekly_data' => $weeklyModel->getWeeklyList(),
            'user_role'   => $this->session->get('role'),
            'is_admin'    => $isAdmin,
            'is_qc'       => $is_qc,
            'is_prod'     => $is_prod
        ];

        return view('weekly_approval', $data);
    }

    // Fungsi untuk memproses tombol Approve Weekly (QC / Production)
    public function processWeekly()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        
        $weekly_id  = $this->request->getPost('weekly_id');
        $action     = $this->request->getPost('action'); // 'Approve' atau 'Reject'
        $departemen = strtolower(trim((string)$this->request->getPost('departemen'))); // 'qc' atau 'production'

        $role    = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));
        $canQc   = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $canProd = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        // 1. Validasi Otoritas Akses Departemen
        if ($departemen === 'qc' && !$canQc) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC.');
            return redirect()->to(base_url().'approval/weekly');
        }
        if ($departemen === 'production' && !$canProd) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan Production.');
            return redirect()->to(base_url().'approval/weekly');
        }

        // 2. Syarat Wajib Lengkap:
        // Weekly approval hanya dapat dilakukan jika semua dokumen startup harian di minggu tersebut
        // sudah selesai di-approve daily (oleh Foreman/Leader/Spv).
        if ($action === 'Approve') {
            $db = \Config\Database::connect();
            $pendingDaily = $db->table('startup')
                ->where('weekly_id', $weekly_id)
                ->where('par000', '1')
                ->groupStart()
                    ->where('status', 'Pending')
                    ->orWhere('status IS NULL')
                    ->orWhere('status', '')
                    ->orWhere('status', '-')
                ->groupEnd()
                ->countAllResults();

            if ($pendingDaily > 0) {
                $this->session->setFlashdata('error', "Persetujuan ditolak: Masih terdapat {$pendingDaily} checksheet startup harian pada minggu ini yang belum di-approve oleh Foreman/Leader/Supervisor. Selesaikan daily approval terlebih dahulu.");
                return redirect()->to(base_url().'approval/weekly');
            }
        }

        $approverName = $this->session->get('name') ?? 'Approver';
        $now = date('Y-m-d H:i:s');

        $updateData = [];
        if ($departemen === 'qc') {
            $updateData['status_qc']   = $action;
            $updateData['approver_qc'] = $approverName;
            $updateData['date_qc']     = $now;
        } else if ($departemen === 'production') {
            $updateData['status_production']   = $action;
            $updateData['approver_production'] = $approverName;
            $updateData['date_production']     = $now;
        }

        if (!empty($updateData)) {
            $weeklyModel->update($weekly_id, $updateData);
            $this->session->setFlashdata('message', "Data mingguan #{$weekly_id} berhasil di-{$action} oleh {$approverName} (" . strtoupper($departemen) . ").");
        }

        return redirect()->to(base_url().'approval/weekly');
    }

    // Fungsi untuk menampilkan isi dokumen dari satu periode mingguan
    public function detail_mingguan($id)
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        $dataMingguan = $weeklyModel->find($id);

        if(empty($dataMingguan)) {
            return "Data mingguan tidak ditemukan.";
        }

        $db = \Config\Database::connect();
        $deviceInfo = !empty($dataMingguan['device']) ? $db->table('device')->where('code', $dataMingguan['device'])->get()->getRowArray() : null;
        $processInfo = !empty($dataMingguan['process']) ? $db->table('proses')->where('process_code', $dataMingguan['process'])->get()->getRowArray() : null;

        $alldata = $this->StartupModel->getByWeeklyId($id);

        $pendingDaily = 0;
        foreach ($alldata as $doc) {
            $st = trim((string)($doc['status'] ?? ''));
            if ($st === 'Pending' || $st === '' || $st === '-') {
                $pendingDaily++;
            }
        }

        $data = [
            'title'         => 'Detail Mingguan | Startup Management',
            'mingguan'      => $dataMingguan,
            'device_info'   => $deviceInfo,
            'process_info'  => $processInfo,
            'pending_daily' => $pendingDaily,
            'alldata'       => $alldata
        ];

        return view('weekly_detail', $data);
    }
}