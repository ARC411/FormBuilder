<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InputModel;
use App\Models\ProductionModel;
use App\Models\StartupModel;
use App\Models\ForegoingModel;
use App\Models\EmpModel;
use App\Models\LogActivityModel;
use CodeIgniter\CodeIgniter;

class Input extends BaseController
{
    protected $session;
    public $InputModel;
    public $ProductionModel;
    public $EmpModel;
    public $StartupModel;
    public $ForegoingModel;
    public $LogActivityModel;
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->InputModel = new InputModel();
        $this->ProductionModel = new ProductionModel();
        $this->EmpModel = new EmpModel();
        $this->StartupModel = new StartupModel();
        $this->ForegoingModel = new ForegoingModel();
        $this->LogActivityModel = new LogActivityModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function index()
    {
        error_reporting(0); // Tambahkan ini agar halaman utama tidak dilabeli 500
        $data = [
            'title' => 'Input | Startup Management'
        ];
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        return view('input', $data);
    }
    
    public function formInputStartup()
    {
        // jika belum login tidak dapat membuka form startup
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

        // jika role tidak sesuai tidak dapat membuka form production
        if(substr($_GET['device'], 0, 2)!=$this->session->get('role') && $this->session->get('role')!='all'){
            return view('forbidden',$data);
        }
        return view('/layout/'.$_GET['device']."/input/startup/".$_GET['process'],$data);
    }

    public function edit($number)
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        $data = [
            'title' => 'Edit | Startup Management',
            'validation' => \Config\Services::validation(),
            'alldata' => $this->ProductionModel->getByNumber($number)
        ];

        // jika role tidak sesuai tidak dapat membuka form production
        if(substr($_GET['device'], 0, 2)!=$this->session->get('role') && $this->session->get('role')!='all'){
            return view('forbidden',$data);
        }

        // jika data finish telah diisi, maka user tidak boleh untuk mengisi kembali
        if($data['alldata'][0]['name2']){
            if(!$this->session->get('isadmin')){
                return view('forbidden',$data);
            }
        }

        return view('edit',$data);
    }

    public function formEdit($process,$number)
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        $data = [
            'title' => 'Edit | Startup Management',
            'alldata' => $this->ProductionModel->getByNumber($number),
            'process' => $process,
            'number' => $number,
        ];

        // jika role tidak sesuai tidak dapat membuka form production
        if(substr($_GET['device'], 0, 2)!=$this->session->get('role') && $this->session->get('role')!='all'){
            return view('forbidden',$data);
        }

        // jika data finish telah diisi, maka user tidak boleh untuk mengisi kembali
        if($data['alldata'][0]['name2']){
            if(!$this->session->get('isadmin')){
                return view('forbidden',$data);
            }
        }
        
        return view("/layout/form/production/".$process,$data);
    }

    // Perbaikan pada Input.php (Controller)
    public function formStandard($process)
    {
        if(empty($process) || $process === 'null' || $process === '-'){
             return "Data catatan tidak ditemukan atau proses tidak valid.";
        }

        $words = preg_split('/-/', $process, -1, PREG_SPLIT_NO_EMPTY);
        
        // Pengecekan keamanan: pastikan array memiliki minimal 1 elemen
        if(count($words) == 0){
             return "Format proses tidak valid.";
        }

        $viewPath = '/layout/'.$words[0].'/note/'.$process;
        
        // Cek apakah file view secara fisik ada sebelum di-render (opsional tapi disarankan)
        // Jika tidak ada, kembalikan pesan kosong daripada CRITICAL error
        try {
            return view($viewPath);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "Catatan belum tersedia untuk mesin/proses ini.";
        }
    }

    
    // --- FUNGSI SIMPAN CHECKSEET DINAMIS ---
    public function save()
    {
        // 1. Tangkap semua data POST dari form HTML asli
        $postData = $this->request->getPost();

        // 2. Siapkan array data Master (Header)
        $dataToSave = [
            'device'  => $postData['device-txt'] ?? '',
            'process' => $postData['process-txt'] ?? '',
            'lotno'   => $postData['lotno-txt'] ?? '',
            'model'   => $postData['model-txt'] ?? '',
            'machno'  => $postData['machno-txt'] ?? '',
            'empid'   => $postData['empid-txt'] ?? '',
            'group'   => $postData['group-txt'] ?? '',
            'shift'   => $postData['shift-txt'] ?? '',
            'docno'   => $postData['docno'] ?? '', 
            'revisi'  => $postData['revisi-txt'] ?? ''
        ];

        // 3. Looping Otomatis untuk Parameter (par001 sampai par031)
        // Menyesuaikan dengan struktur tabel checksheet Abang saat ini
        for ($i = 1; $i <= 31; $i++) {
            // Format angka jadi 3 digit: 1 jadi 001, 15 jadi 015
            $parNum = str_pad($i, 3, '0', STR_PAD_LEFT); 
            $parName = 'par' . $parNum;

            // Jika di file HTML ada input dengan name="par001", ambil nilainya
            // Jika tidak ada (misal di form itu cuma butuh par013), isi dengan string kosong
            if (isset($postData[$parName])) {
                $dataToSave[$parName] = $postData[$parName];
            } else {
                $dataToSave[$parName] = ''; 
            }
        }

        // 4. Simpan ke Database
        if ($this->InputModel->insert($dataToSave)) {
            $this->session->setFlashdata('message', 'Mantap! Data checksheet berhasil disimpan.');
            return redirect()->to(base_url('input'));
        } else {
            $this->session->setFlashdata('message', 'Gagal menyimpan data.');
            return redirect()->back();
        }
    }
}
