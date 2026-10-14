<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\LogActivityModel;
use App\Models\WeeklyApprovalModel;
use App\Models\ProductionModel; // Pastikan model ini dipanggil karena dipakai di approvalById

class Approval extends BaseController
{
    protected $session;
    protected $StartupModel;
    protected $ProductionModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->StartupModel = new StartupModel();
        $this->ProductionModel = new ProductionModel(); // Deklarasikan jika digunakan
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
        
        // Perbaikan: Gunakan getGet() untuk mencegah error jika parameter URL kosong
        // Default menampilkan data 1 bulan terakhir hingga hari ini
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
        
        $data = [
            'title' => 'Weekly Approval | Startup Management',
            'weekly_data' => $weeklyModel->getWeeklyList(),
            'user_role' => $this->session->get('role') // Asumsi: session role menyimpan 'qc' atau 'production'
        ];

        return view('weekly_approval', $data);
    }

    // 2. Fungsi untuk memproses tombol Approve
    public function processWeekly()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        
        $weekly_id = $this->request->getPost('weekly_id');
        $action = $this->request->getPost('action'); // isinya: 'Approve' atau 'Reject'
        $departemen = $this->request->getPost('departemen'); // isinya: 'qc' atau 'production'

        $updateData = [];
        if ($departemen === 'qc') {
            $updateData['status_qc'] = $action;
        } else if ($departemen === 'production') {
            $updateData['status_production'] = $action;
        }

        if (!empty($updateData)) {
            $weeklyModel->update($weekly_id, $updateData);
            $this->session->setFlashdata('message', "Data mingguan berhasil di-$action oleh $departemen");
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

        $data = [
            'title' => 'Detail Mingguan | Startup Management',
            'mingguan' => $dataMingguan,
            'alldata' => $this->StartupModel->getByWeeklyId($id)
        ];

        return view('weekly_detail', $data);
    }

}
