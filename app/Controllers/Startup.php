<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\LogActivityModel;
use CodeIgniter\CodeIgniter;

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

    // Fungsi otomatis pendeteksi tipe folder (startup/production/foregoing)
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

    public function index(): string
    {
        $data = [
            'title' => 'Data Startup | Startup Management',
            'alldata' => $this->StartupModel->getAllData()
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
        $data = [
            'title' => 'History | Startup Management',
            'validation' => \Config\Services::validation(),
            'alldata' => $this->StartupModel->getByNumber($number)
        ];
        
        return view('edit-startup',$data);
    }

    public function startupByNumber($number)
    {
        $data = [
            'alldata' => $this->StartupModel->getByNumber($number)
        ];

        if(empty($data['alldata'])) return "Data tidak ditemukan.";

        $folder = $this->getFolderType($data['alldata'][0]['process']);
        
        try {
            return view("/layout/".$data['alldata'][0]['device']."/history/".$folder."/".$data['alldata'][0]['process'],$data); 
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, tampilan detail untuk proses ini belum tersedia.</div>";
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

    public function createStartup()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        $data = [
            'title' => 'Create Startup',
            'validation' => \Config\Services::validation()
        ];
        

        $number = $this->request->getVar('number-edit') ? $this->request->getVar('number-edit') : ($this->StartupModel->getLatestId()+1);
        $cnt_input = $this->request->getVar('cnt-proses') ? $this->request->getVar('cnt-proses') : 1; 

        $par001a = $this->request->getVar('par001a')!==null ? $this->request->getVar('par001a') : ($this->request->getVar('par001')!==null ? $this->request->getVar('par001') : null);
        $par002a = $this->request->getVar('par002a')!==null ? $this->request->getVar('par002a') : ($this->request->getVar('par002')!==null ? $this->request->getVar('par002') : null);
        $par003a = $this->request->getVar('par003a')!==null ? $this->request->getVar('par003a') : ($this->request->getVar('par003')!==null ? $this->request->getVar('par003') : null);
        $par004a = $this->request->getVar('par004a')!==null ? $this->request->getVar('par004a') : ($this->request->getVar('par004')!==null ? $this->request->getVar('par004') : null);
        $par005a = $this->request->getVar('par005a')!==null ? $this->request->getVar('par005a') : ($this->request->getVar('par005')!==null ? $this->request->getVar('par005') : null);
        $par006a = $this->request->getVar('par006a')!==null ? $this->request->getVar('par006a') : ($this->request->getVar('par006')!==null ? $this->request->getVar('par006') : null);
        $par007a = $this->request->getVar('par007a')!==null ? $this->request->getVar('par007a') : ($this->request->getVar('par007')!==null ? $this->request->getVar('par007') : null);
        $par008a = $this->request->getVar('par008a')!==null ? $this->request->getVar('par008a') : ($this->request->getVar('par008')!==null ? $this->request->getVar('par008') : null);
        $par009a = $this->request->getVar('par009a')!==null ? $this->request->getVar('par009a') : ($this->request->getVar('par009')!==null ? $this->request->getVar('par009') : null);
        $par010a = $this->request->getVar('par010a')!==null ? $this->request->getVar('par010a') : ($this->request->getVar('par010')!==null ? $this->request->getVar('par010') : null);

        $par011a = $this->request->getVar('par011a')!==null ? $this->request->getVar('par011a') : ($this->request->getVar('par011')!==null ? $this->request->getVar('par011') : null);
        $par012a = $this->request->getVar('par012a')!==null ? $this->request->getVar('par012a') : ($this->request->getVar('par012')!==null ? $this->request->getVar('par012') : null);
        $par013a = $this->request->getVar('par013a')!==null ? $this->request->getVar('par013a') : ($this->request->getVar('par013')!==null ? $this->request->getVar('par013') : null);
        $par014a = $this->request->getVar('par014a')!==null ? $this->request->getVar('par014a') : ($this->request->getVar('par014')!==null ? $this->request->getVar('par014') : null);
        $par015a = $this->request->getVar('par015a')!==null ? $this->request->getVar('par015a') : ($this->request->getVar('par015')!==null ? $this->request->getVar('par015') : null);
        $par016a = $this->request->getVar('par016a')!==null ? $this->request->getVar('par016a') : ($this->request->getVar('par016')!==null ? $this->request->getVar('par016') : null);
        $par017a = $this->request->getVar('par017a')!==null ? $this->request->getVar('par017a') : ($this->request->getVar('par017')!==null ? $this->request->getVar('par017') : null);
        $par018a = $this->request->getVar('par018a')!==null ? $this->request->getVar('par018a') : ($this->request->getVar('par018')!==null ? $this->request->getVar('par018') : null);
        $par019a = $this->request->getVar('par019a')!==null ? $this->request->getVar('par019a') : ($this->request->getVar('par019')!==null ? $this->request->getVar('par019') : null);
        $par020a = $this->request->getVar('par020a')!==null ? $this->request->getVar('par020a') : ($this->request->getVar('par020')!==null ? $this->request->getVar('par020') : null);
        
        $par021a = $this->request->getVar('par021a')!==null ? $this->request->getVar('par021a') : ($this->request->getVar('par021')!==null ? $this->request->getVar('par021') : null);
        $par022a = $this->request->getVar('par022a')!==null ? $this->request->getVar('par022a') : ($this->request->getVar('par022')!==null ? $this->request->getVar('par022') : null);
        $par023a = $this->request->getVar('par023a')!==null ? $this->request->getVar('par023a') : ($this->request->getVar('par023')!==null ? $this->request->getVar('par023') : null);
        $par024a = $this->request->getVar('par024a')!==null ? $this->request->getVar('par024a') : ($this->request->getVar('par024')!==null ? $this->request->getVar('par024') : null);
        $par025a = $this->request->getVar('par025a')!==null ? $this->request->getVar('par025a') : ($this->request->getVar('par025')!==null ? $this->request->getVar('par025') : null);
        $par026a = $this->request->getVar('par026a')!==null ? $this->request->getVar('par026a') : ($this->request->getVar('par026')!==null ? $this->request->getVar('par026') : null);
        $par027a = $this->request->getVar('par027a')!==null ? $this->request->getVar('par027a') : ($this->request->getVar('par027')!==null ? $this->request->getVar('par027') : null);
        $par028a = $this->request->getVar('par028a')!==null ? $this->request->getVar('par028a') : ($this->request->getVar('par028')!==null ? $this->request->getVar('par028') : null);
        $par029a = $this->request->getVar('par029a')!==null ? $this->request->getVar('par029a') : ($this->request->getVar('par029')!==null ? $this->request->getVar('par029') : null);
        $par030a = $this->request->getVar('par030a')!==null ? $this->request->getVar('par030a') : ($this->request->getVar('par030')!==null ? $this->request->getVar('par030') : null);

        $par031a = $this->request->getVar('par031a')!==null ? $this->request->getVar('par031a') : ($this->request->getVar('par031')!==null ? $this->request->getVar('par031') : null);
        $par032a = $this->request->getVar('par032a')!==null ? $this->request->getVar('par032a') : ($this->request->getVar('par032')!==null ? $this->request->getVar('par032') : null);
        $par033a = $this->request->getVar('par033a')!==null ? $this->request->getVar('par033a') : ($this->request->getVar('par033')!==null ? $this->request->getVar('par033') : null);
        $par034a = $this->request->getVar('par034a')!==null ? $this->request->getVar('par034a') : ($this->request->getVar('par034')!==null ? $this->request->getVar('par034') : null);
        $par035a = $this->request->getVar('par035a')!==null ? $this->request->getVar('par035a') : ($this->request->getVar('par035')!==null ? $this->request->getVar('par035') : null);
        $par036a = $this->request->getVar('par036a')!==null ? $this->request->getVar('par036a') : ($this->request->getVar('par036')!==null ? $this->request->getVar('par036') : null);
        $par037a = $this->request->getVar('par037a')!==null ? $this->request->getVar('par037a') : ($this->request->getVar('par037')!==null ? $this->request->getVar('par037') : null);
        $par038a = $this->request->getVar('par038a')!==null ? $this->request->getVar('par038a') : ($this->request->getVar('par038')!==null ? $this->request->getVar('par038') : null);
        $par039a = $this->request->getVar('par039a')!==null ? $this->request->getVar('par039a') : ($this->request->getVar('par039')!==null ? $this->request->getVar('par039') : null);
        $par040a = $this->request->getVar('par040a')!==null ? $this->request->getVar('par040a') : ($this->request->getVar('par040')!==null ? $this->request->getVar('par040') : null);

        $par041a = $this->request->getVar('par041a')!==null ? $this->request->getVar('par041a') : ($this->request->getVar('par041')!==null ? $this->request->getVar('par041') : null);
        $par042a = $this->request->getVar('par042a')!==null ? $this->request->getVar('par042a') : ($this->request->getVar('par042')!==null ? $this->request->getVar('par042') : null);
        $par043a = $this->request->getVar('par043a')!==null ? $this->request->getVar('par043a') : ($this->request->getVar('par043')!==null ? $this->request->getVar('par043') : null);
        $par044a = $this->request->getVar('par044a')!==null ? $this->request->getVar('par044a') : ($this->request->getVar('par044')!==null ? $this->request->getVar('par044') : null);
        $resulta = $this->StartupModel->addProses
        (
            [
                'number' => $number,
                'par000' => 1,
                'device' => (string)$this->request->getVar('device-txt'),
                'model' => (string)$this->request->getVar('model-txt'),
                'process' => (string)$this->request->getVar('process-txt'),
                'lotno' => (string)$this->request->getVar('lotno-txt'),
                'machno' => (string)$this->request->getVar('machno-txt'),
                'empid' => (string)$this->request->getVar('empid-txt'),
                'group' => (string)$this->request->getVar('group-txt'),
                'shift' => (string)$this->request->getVar('shift-txt'),
                'name' => (string)$this->request->getVar('name-txt'),
                'par001' => $par001a,'par002' => $par002a,'par003' => $par003a,'par004' => $par004a,'par005' => $par005a,
                'par006' => $par006a,'par007' => $par007a,'par008' => $par008a,'par009' => $par009a,'par010' => $par010a,
                'par011' => $par011a,'par012' => $par012a,'par013' => $par013a,'par014' => $par014a,'par015' => $par015a,
                'par016' => $par016a,'par017' => $par017a,'par018' => $par018a,'par019' => $par019a,'par020' => $par020a,
                'par021' => $par021a,'par022' => $par022a,'par023' => $par023a,'par024' => $par024a,'par025' => $par025a,
                'par026' => $par026a,'par027' => $par027a,'par028' => $par028a,'par029' => $par029a,'par030' => $par030a,
                'par031' => $par031a,'par032' => $par032a,'par033' => $par033a,'par034' => $par034a,'par035' => $par035a,
                'par036' => $par036a,'par037' => $par037a,'par038' => $par038a,'par039' => $par039a,'par040' => $par040a,
                'par041' => $par041a,'par042' => $par042a,'par043' => $par043a,'par044' => $par044a
            ]
        );

        if($cnt_input == 1){ 
            if ($resulta) {
                session()->setFlashdata('message', 'Input Berhasil');
            } else {
                session()->setFlashdata('message', 'Input Failed!'); 
            }
            return redirect()->to(base_url('input'));
        }

        $par001b = $this->request->getVar('par001b')!==null ? $this->request->getVar('par001b') : null;
        $par002b = $this->request->getVar('par002b')!==null ? $this->request->getVar('par002b') : null;
        $par003b = $this->request->getVar('par003b')!==null ? $this->request->getVar('par003b') : null;
        $par004b = $this->request->getVar('par004b')!==null ? $this->request->getVar('par004b') : null;
        $par005b = $this->request->getVar('par005b')!==null ? $this->request->getVar('par005b') : null;
        $par006b = $this->request->getVar('par006b')!==null ? $this->request->getVar('par006b') : null;
        $par007b = $this->request->getVar('par007b')!==null ? $this->request->getVar('par007b') : null;
        $par008b = $this->request->getVar('par008b')!==null ? $this->request->getVar('par008b') : null;
        $par009b = $this->request->getVar('par009b')!==null ? $this->request->getVar('par009b') : null;
        $par010b = $this->request->getVar('par010b')!==null ? $this->request->getVar('par010b') : null;

        $par011b = $this->request->getVar('par011b')!==null ? $this->request->getVar('par011b') : null;
        $par012b = $this->request->getVar('par012b')!==null ? $this->request->getVar('par012b') : null;
        $par013b = $this->request->getVar('par013b')!==null ? $this->request->getVar('par013b') : null;
        $par014b = $this->request->getVar('par014b')!==null ? $this->request->getVar('par014b') : null;
        $par015b = $this->request->getVar('par015b')!==null ? $this->request->getVar('par015b') : null;
        $par016b = $this->request->getVar('par016b')!==null ? $this->request->getVar('par016b') : null;
        $par017b = $this->request->getVar('par017b')!==null ? $this->request->getVar('par017b') : null;
        $par018b = $this->request->getVar('par018b')!==null ? $this->request->getVar('par018b') : null;
        $par019b = $this->request->getVar('par019b')!==null ? $this->request->getVar('par019b') : null;
        $par020b = $this->request->getVar('par020b')!==null ? $this->request->getVar('par020b') : null;
        
        $par021b = $this->request->getVar('par021b')!==null ? $this->request->getVar('par021b') : null;
        $par022b = $this->request->getVar('par022b')!==null ? $this->request->getVar('par022b') : null;
        $par023b = $this->request->getVar('par023b')!==null ? $this->request->getVar('par023b') : null;
        $par024b = $this->request->getVar('par024b')!==null ? $this->request->getVar('par024b') : null;
        $par025b = $this->request->getVar('par025b')!==null ? $this->request->getVar('par025b') : null;
        $par026b = $this->request->getVar('par026b')!==null ? $this->request->getVar('par026b') : null;
        $par027b = $this->request->getVar('par027b')!==null ? $this->request->getVar('par027b') : null;
        $par028b = $this->request->getVar('par028b')!==null ? $this->request->getVar('par028b') : null;
        $par029b = $this->request->getVar('par029b')!==null ? $this->request->getVar('par029b') : null;
        $par030b = $this->request->getVar('par030b')!==null ? $this->request->getVar('par030b') : null;

        $par031b = $this->request->getVar('par031b')!==null ? $this->request->getVar('par031b') : null;
        $par032b = $this->request->getVar('par032b')!==null ? $this->request->getVar('par032b') : null;
        $par033b = $this->request->getVar('par033b')!==null ? $this->request->getVar('par033b') : null;
        $par034b = $this->request->getVar('par034b')!==null ? $this->request->getVar('par034b') : null;
        $par035b = $this->request->getVar('par035b')!==null ? $this->request->getVar('par035b') : null;
        $par036b = $this->request->getVar('par036b')!==null ? $this->request->getVar('par036b') : null;
        $par037b = $this->request->getVar('par037b')!==null ? $this->request->getVar('par037b') : null;
        $par038b = $this->request->getVar('par038b')!==null ? $this->request->getVar('par038b') : null;
        $par039b = $this->request->getVar('par039b')!==null ? $this->request->getVar('par039b') : null;
        $par040b = $this->request->getVar('par040b')!==null ? $this->request->getVar('par040b') : null;

        $par041b = $this->request->getVar('par041b')!==null ? $this->request->getVar('par041b') : null;
        $par042b = $this->request->getVar('par042b')!==null ? $this->request->getVar('par042b') : null;
        $par043b = $this->request->getVar('par043b')!==null ? $this->request->getVar('par043b') : null;
        $par044b = $this->request->getVar('par044b')!==null ? $this->request->getVar('par044b') : null;
        $resultb = $this->StartupModel->addProses
        (
            [
                'number' => $number,
                'par000' => 2,
                'device' => $this->request->getVar('device-txt'),
                'model' => $this->request->getVar('model-txt'),
                'process' => $this->request->getVar('process-txt'),
                'lotno' => $this->request->getVar('lotno-txt'),
                'machno' => $this->request->getVar('machno-txt'),
                'empid' => $this->request->getVar('empid-txt'),
                'group' => $this->request->getVar('group-txt'),
                'shift' => $this->request->getVar('shift-txt'),
                'name' => $this->request->getVar('name-txt'),
                'par001' => $par001b,'par002' => $par002b,'par003' => $par003b,'par004' => $par004b,'par005' => $par005b,
                'par006' => $par006b,'par007' => $par007b,'par008' => $par008b,'par009' => $par009b,'par010' => $par010b,
                'par011' => $par011b,'par012' => $par012b,'par013' => $par013b,'par014' => $par014b,'par015' => $par015b,
                'par016' => $par016b,'par017' => $par017b,'par018' => $par018b,'par019' => $par019b,'par020' => $par020b,
                'par021' => $par021b,'par022' => $par022b,'par023' => $par023b,'par024' => $par024b,'par025' => $par025b,
                'par026' => $par026b,'par027' => $par027b,'par028' => $par028b,'par029' => $par029b,'par030' => $par030b,
                'par031' => $par031b,'par032' => $par032b,'par033' => $par033b,'par034' => $par034b,'par035' => $par035b,
                'par036' => $par036b,'par037' => $par037b,'par038' => $par038b,'par039' => $par039b,'par040' => $par040b,
                'par041' => $par041b,'par042' => $par042b,'par043' => $par043b,'par044' => $par044b
            ]
        );

        if($cnt_input == 2){ 
            if ($resultb) {
                session()->setFlashdata('message', 'Input Berhasil');
            } else {
                session()->setFlashdata('message', 'Input Failed!'); 
            }
            return redirect()->to(base_url('input'));
        }

        $par001c = $this->request->getVar('par001c')!==null ? $this->request->getVar('par001c') : null;
        $par002c = $this->request->getVar('par002c')!==null ? $this->request->getVar('par002c') : null;
        $par003c = $this->request->getVar('par003c')!==null ? $this->request->getVar('par003c') : null;
        $par004c = $this->request->getVar('par004c')!==null ? $this->request->getVar('par004c') : null;
        $par005c = $this->request->getVar('par005c')!==null ? $this->request->getVar('par005c') : null;
        $par006c = $this->request->getVar('par006c')!==null ? $this->request->getVar('par006c') : null;
        $par007c = $this->request->getVar('par007c')!==null ? $this->request->getVar('par007c') : null;
        $par008c = $this->request->getVar('par008c')!==null ? $this->request->getVar('par008c') : null;
        $par009c = $this->request->getVar('par009c')!==null ? $this->request->getVar('par009c') : null;
        $par010c = $this->request->getVar('par010c')!==null ? $this->request->getVar('par010c') : null;

        $par011c = $this->request->getVar('par011c')!==null ? $this->request->getVar('par011c') : null;
        $par012c = $this->request->getVar('par012c')!==null ? $this->request->getVar('par012c') : null;
        $par013c = $this->request->getVar('par013c')!==null ? $this->request->getVar('par013c') : null;
        $par014c = $this->request->getVar('par014c')!==null ? $this->request->getVar('par014c') : null;
        $par015c = $this->request->getVar('par015c')!==null ? $this->request->getVar('par015c') : null;
        $par016c = $this->request->getVar('par016c')!==null ? $this->request->getVar('par016c') : null;
        $par017c = $this->request->getVar('par017c')!==null ? $this->request->getVar('par017c') : null;
        $par018c = $this->request->getVar('par018c')!==null ? $this->request->getVar('par018c') : null;
        $par019c = $this->request->getVar('par019c')!==null ? $this->request->getVar('par019c') : null;
        $par020c = $this->request->getVar('par020c')!==null ? $this->request->getVar('par020c') : null;
        
        $par021c = $this->request->getVar('par021c')!==null ? $this->request->getVar('par021c') : null;
        $par022c = $this->request->getVar('par022c')!==null ? $this->request->getVar('par022c') : null;
        $par023c = $this->request->getVar('par023c')!==null ? $this->request->getVar('par023c') : null;
        $par024c = $this->request->getVar('par024c')!==null ? $this->request->getVar('par024c') : null;
        $par025c = $this->request->getVar('par025c')!==null ? $this->request->getVar('par025c') : null;
        $par026c = $this->request->getVar('par026c')!==null ? $this->request->getVar('par026c') : null;
        $par027c = $this->request->getVar('par027c')!==null ? $this->request->getVar('par027c') : null;
        $par028c = $this->request->getVar('par028c')!==null ? $this->request->getVar('par028c') : null;
        $par029c = $this->request->getVar('par029c')!==null ? $this->request->getVar('par029c') : null;
        $par030c = $this->request->getVar('par030c')!==null ? $this->request->getVar('par030c') : null;

        $par031c = $this->request->getVar('par031c')!==null ? $this->request->getVar('par031c') : null;
        $par032c = $this->request->getVar('par032c')!==null ? $this->request->getVar('par032c') : null;
        $par033c = $this->request->getVar('par033c')!==null ? $this->request->getVar('par033c') : null;
        $par034c = $this->request->getVar('par034c')!==null ? $this->request->getVar('par034c') : null;
        $par035c = $this->request->getVar('par035c')!==null ? $this->request->getVar('par035c') : null;
        $par036c = $this->request->getVar('par036c')!==null ? $this->request->getVar('par036c') : null;
        $par037c = $this->request->getVar('par037c')!==null ? $this->request->getVar('par037c') : null;
        $par038c = $this->request->getVar('par038c')!==null ? $this->request->getVar('par038c') : null;
        $par039c = $this->request->getVar('par039c')!==null ? $this->request->getVar('par039c') : null;
        $par040c = $this->request->getVar('par040c')!==null ? $this->request->getVar('par040c') : null;

        $par041c = $this->request->getVar('par041c')!==null ? $this->request->getVar('par041c') : null;
        $par042c = $this->request->getVar('par042c')!==null ? $this->request->getVar('par042c') : null;
        $par043c = $this->request->getVar('par043c')!==null ? $this->request->getVar('par043c') : null;
        $par044c = $this->request->getVar('par044c')!==null ? $this->request->getVar('par044c') : null;
        $resultc = $this->StartupModel->addProses(
            [
                'number' => $number,
                'par000' => 3,
                'device' => $this->request->getVar('device-txt'),
                'model' => $this->request->getVar('model-txt'),
                'process' => $this->request->getVar('process-txt'),
                'lotno' => $this->request->getVar('lotno-txt'),
                'machno' => $this->request->getVar('machno-txt'),
                'empid' => $this->request->getVar('empid-txt'),
                'group' => $this->request->getVar('group-txt'),
                'shift' => $this->request->getVar('shift-txt'),
                'name' => $this->request->getVar('name-txt'),
                'par001' => $par001c,'par002' => $par002c,'par003' => $par003c,'par004' => $par004c,'par005' => $par005c,
                'par006' => $par006c,'par007' => $par007c,'par008' => $par008c,'par009' => $par009c,'par010' => $par010c,
                'par011' => $par011c,'par012' => $par012c,'par013' => $par013c,'par014' => $par014c,'par015' => $par015c,
                'par016' => $par016c,'par017' => $par017c,'par018' => $par018c,'par019' => $par019c,'par020' => $par020c,
                'par021' => $par021c,'par022' => $par022c,'par023' => $par023c,'par024' => $par024c,'par025' => $par025c,
                'par026' => $par026c,'par027' => $par027c,'par028' => $par028c,'par029' => $par029c,'par030' => $par030c,
                'par031' => $par031c,'par032' => $par032c,'par033' => $par033c,'par034' => $par034c,'par035' => $par035c,
                'par036' => $par036c,'par037' => $par037c,'par038' => $par038c,'par039' => $par039c,'par040' => $par040c,
                'par041' => $par041c,'par042' => $par042c,'par043' => $par043c,'par044' => $par044c
            ]
        );

        if($cnt_input == 3){ 
            if ($resultc) {
                session()->setFlashdata('message', 'Input Berhasil');
            } else {
                session()->setFlashdata('message', 'Input Failed!'); 
            }
            return redirect()->to(base_url('input'));
        }

        $par001d = $this->request->getVar('par001d')!==null ? $this->request->getVar('par001d') : null;
        $par002d = $this->request->getVar('par002d')!==null ? $this->request->getVar('par002d') : null;
        $par003d = $this->request->getVar('par003d')!==null ? $this->request->getVar('par003d') : null;
        $par004d = $this->request->getVar('par004d')!==null ? $this->request->getVar('par004d') : null;
        $par005d = $this->request->getVar('par005d')!==null ? $this->request->getVar('par005d') : null;
        $par006d = $this->request->getVar('par006d')!==null ? $this->request->getVar('par006d') : null;
        $par007d = $this->request->getVar('par007d')!==null ? $this->request->getVar('par007d') : null;
        $par008d = $this->request->getVar('par008d')!==null ? $this->request->getVar('par008d') : null;
        $par009d = $this->request->getVar('par009d')!==null ? $this->request->getVar('par009d') : null;
        $par010d = $this->request->getVar('par010d')!==null ? $this->request->getVar('par010d') : null;

        $par011d = $this->request->getVar('par011d')!==null ? $this->request->getVar('par011d') : null;
        $par012d = $this->request->getVar('par012d')!==null ? $this->request->getVar('par012d') : null;
        $par013d = $this->request->getVar('par013d')!==null ? $this->request->getVar('par013d') : null;
        $par014d = $this->request->getVar('par014d')!==null ? $this->request->getVar('par014d') : null;
        $par015d = $this->request->getVar('par015d')!==null ? $this->request->getVar('par015d') : null;
        $par016d = $this->request->getVar('par016d')!==null ? $this->request->getVar('par016d') : null;
        $par017d = $this->request->getVar('par017d')!==null ? $this->request->getVar('par017d') : null;
        $par018d = $this->request->getVar('par018d')!==null ? $this->request->getVar('par018d') : null;
        $par019d = $this->request->getVar('par019d')!==null ? $this->request->getVar('par019d') : null;
        $par020d = $this->request->getVar('par020d')!==null ? $this->request->getVar('par020d') : null;
        
        $par021d = $this->request->getVar('par021d')!==null ? $this->request->getVar('par021d') : null;
        $par022d = $this->request->getVar('par022d')!==null ? $this->request->getVar('par022d') : null;
        $par023d = $this->request->getVar('par023d')!==null ? $this->request->getVar('par023d') : null;
        $par024d = $this->request->getVar('par024d')!==null ? $this->request->getVar('par024d') : null;
        $par025d = $this->request->getVar('par025d')!==null ? $this->request->getVar('par025d') : null;
        $par026d = $this->request->getVar('par026d')!==null ? $this->request->getVar('par026d') : null;
        $par027d = $this->request->getVar('par027d')!==null ? $this->request->getVar('par027d') : null;
        $par028d = $this->request->getVar('par028d')!==null ? $this->request->getVar('par028d') : null;
        $par029d = $this->request->getVar('par029d')!==null ? $this->request->getVar('par029d') : null;
        $par030d = $this->request->getVar('par030d')!==null ? $this->request->getVar('par030d') : null;

        $par031d = $this->request->getVar('par031d')!==null ? $this->request->getVar('par031d') : null;
        $par032d = $this->request->getVar('par032d')!==null ? $this->request->getVar('par032d') : null;
        $par033d = $this->request->getVar('par033d')!==null ? $this->request->getVar('par033d') : null;
        $par034d = $this->request->getVar('par034d')!==null ? $this->request->getVar('par034d') : null;
        $par035d = $this->request->getVar('par035d')!==null ? $this->request->getVar('par035d') : null;
        $par036d = $this->request->getVar('par036d')!==null ? $this->request->getVar('par036d') : null;
        $par037d = $this->request->getVar('par037d')!==null ? $this->request->getVar('par037d') : null;
        $par038d = $this->request->getVar('par038d')!==null ? $this->request->getVar('par038d') : null;
        $par039d = $this->request->getVar('par039d')!==null ? $this->request->getVar('par039d') : null;
        $par040d = $this->request->getVar('par040d')!==null ? $this->request->getVar('par040d') : null;

        $par041d = $this->request->getVar('par041d')!==null ? $this->request->getVar('par041d') : null;
        $par042d = $this->request->getVar('par042d')!==null ? $this->request->getVar('par042d') : null;
        $par043d = $this->request->getVar('par043d')!==null ? $this->request->getVar('par043d') : null;
        $par044d = $this->request->getVar('par044d')!==null ? $this->request->getVar('par044d') : null;
        $resultd = $this->StartupModel->addProses
        (
            [
                'number' => $number,
                'par000' => 4,
                'device' => $this->request->getVar('device-txt'),
                'model' => $this->request->getVar('model-txt'),
                'process' => $this->request->getVar('process-txt'),
                'lotno' => $this->request->getVar('lotno-txt'),
                'machno' => $this->request->getVar('machno-txt'),
                'empid' => $this->request->getVar('empid-txt'),
                'group' => $this->request->getVar('group-txt'),
                'shift' => $this->request->getVar('shift-txt'),
                'name' => $this->request->getVar('name-txt'),
                'par001' => $par001d,'par002' => $par002d,'par003' => $par003d,'par004' => $par004d,'par005' => $par005d,
                'par006' => $par006d,'par007' => $par007d,'par008' => $par008d,'par009' => $par009d,'par010' => $par010d,
                'par011' => $par011d,'par012' => $par012d,'par013' => $par013d,'par014' => $par014d,'par015' => $par015d,
                'par016' => $par016d,'par017' => $par017d,'par018' => $par018d,'par019' => $par019d,'par020' => $par020d,
                'par021' => $par021d,'par022' => $par022d,'par023' => $par023d,'par024' => $par024d,'par025' => $par025d,
                'par026' => $par026d,'par027' => $par027d,'par028' => $par028d,'par029' => $par029d,'par030' => $par030d,
                'par031' => $par031d,'par032' => $par032d,'par033' => $par033d,'par034' => $par034d,'par035' => $par035d,
                'par036' => $par036d,'par037' => $par037d,'par038' => $par038d,'par039' => $par039d,'par040' => $par040d,
                'par041' => $par041d,'par042' => $par042d,'par043' => $par043d,'par044' => $par044d
            ]
        );

        if($cnt_input == 4){ 
            if ($resultd) {
                session()->setFlashdata('message', 'Input Berhasil');
            } else {
                session()->setFlashdata('message', 'Input Failed!'); 
            }
            return redirect()->to(base_url('input'));
        }

        $par001e = $this->request->getVar('par001e')!==null ? $this->request->getVar('par001e') : null;
        $par002e = $this->request->getVar('par002e')!==null ? $this->request->getVar('par002e') : null;
        $par003e = $this->request->getVar('par003e')!==null ? $this->request->getVar('par003e') : null;
        $par004e = $this->request->getVar('par004e')!==null ? $this->request->getVar('par004e') : null;
        $par005e = $this->request->getVar('par005e')!==null ? $this->request->getVar('par005e') : null;
        $par006e = $this->request->getVar('par006e')!==null ? $this->request->getVar('par006e') : null;
        $par007e = $this->request->getVar('par007e')!==null ? $this->request->getVar('par007e') : null;
        $par008e = $this->request->getVar('par008e')!==null ? $this->request->getVar('par008e') : null;
        $par009e = $this->request->getVar('par009e')!==null ? $this->request->getVar('par009e') : null;
        $par010e = $this->request->getVar('par010e')!==null ? $this->request->getVar('par010e') : null;

        $par011e = $this->request->getVar('par011e')!==null ? $this->request->getVar('par011e') : null;
        $par012e = $this->request->getVar('par012e')!==null ? $this->request->getVar('par012e') : null;
        $par013e = $this->request->getVar('par013e')!==null ? $this->request->getVar('par013e') : null;
        $par014e = $this->request->getVar('par014e')!==null ? $this->request->getVar('par014e') : null;
        $par015e = $this->request->getVar('par015e')!==null ? $this->request->getVar('par015e') : null;
        $par016e = $this->request->getVar('par016e')!==null ? $this->request->getVar('par016e') : null;
        $par017e = $this->request->getVar('par017e')!==null ? $this->request->getVar('par017e') : null;
        $par018e = $this->request->getVar('par018e')!==null ? $this->request->getVar('par018e') : null;
        $par019e = $this->request->getVar('par019e')!==null ? $this->request->getVar('par019e') : null;
        $par020e = $this->request->getVar('par020e')!==null ? $this->request->getVar('par020e') : null;
        
        $par021e = $this->request->getVar('par021e')!==null ? $this->request->getVar('par021e') : null;
        $par022e = $this->request->getVar('par022e')!==null ? $this->request->getVar('par022e') : null;
        $par023e = $this->request->getVar('par023e')!==null ? $this->request->getVar('par023e') : null;
        $par024e = $this->request->getVar('par024e')!==null ? $this->request->getVar('par024e') : null;
        $par025e = $this->request->getVar('par025e')!==null ? $this->request->getVar('par025e') : null;
        $par026e = $this->request->getVar('par026e')!==null ? $this->request->getVar('par026e') : null;
        $par027e = $this->request->getVar('par027e')!==null ? $this->request->getVar('par027e') : null;
        $par028e = $this->request->getVar('par028e')!==null ? $this->request->getVar('par028e') : null;
        $par029e = $this->request->getVar('par029e')!==null ? $this->request->getVar('par029e') : null;
        $par030e = $this->request->getVar('par030e')!==null ? $this->request->getVar('par030e') : null;

        $par031e = $this->request->getVar('par031e')!==null ? $this->request->getVar('par031e') : null;
        $par032e = $this->request->getVar('par032e')!==null ? $this->request->getVar('par032e') : null;
        $par033e = $this->request->getVar('par033e')!==null ? $this->request->getVar('par033e') : null;
        $par034e = $this->request->getVar('par034e')!==null ? $this->request->getVar('par034e') : null;
        $par035e = $this->request->getVar('par035e')!==null ? $this->request->getVar('par035e') : null;
        $par036e = $this->request->getVar('par036e')!==null ? $this->request->getVar('par036e') : null;
        $par037e = $this->request->getVar('par037e')!==null ? $this->request->getVar('par037e') : null;
        $par038e = $this->request->getVar('par038e')!==null ? $this->request->getVar('par038e') : null;
        $par039e = $this->request->getVar('par039e')!==null ? $this->request->getVar('par039e') : null;
        $par040e = $this->request->getVar('par040e')!==null ? $this->request->getVar('par040e') : null;

        $par041e = $this->request->getVar('par041e')!==null ? $this->request->getVar('par041e') : null;
        $par042e = $this->request->getVar('par042e')!==null ? $this->request->getVar('par042e') : null;
        $par043e = $this->request->getVar('par043e')!==null ? $this->request->getVar('par043e') : null;
        $par044e = $this->request->getVar('par044e')!==null ? $this->request->getVar('par044e') : null;
        $resulte = $this->StartupModel->addProses
        (
            [
                'number' => $number,
                'par000' => 5,
                'device' => $this->request->getVar('device-txt'),
                'model' => $this->request->getVar('model-txt'),
                'process' => $this->request->getVar('process-txt'),
                'lotno' => $this->request->getVar('lotno-txt'),
                'machno' => $this->request->getVar('machno-txt'),
                'empid' => $this->request->getVar('empid-txt'),
                'group' => $this->request->getVar('group-txt'),
                'shift' => $this->request->getVar('shift-txt'),
                'name' => $this->request->getVar('name-txt'),
                'par001' => $par001e,'par002' => $par002e,'par003' => $par003e,'par004' => $par004e,'par005' => $par005e,
                'par006' => $par006e,'par007' => $par007e,'par008' => $par008e,'par009' => $par009e,'par010' => $par010e,
                'par011' => $par011e,'par012' => $par012e,'par013' => $par013e,'par014' => $par014e,'par015' => $par015e,
                'par016' => $par016e,'par017' => $par017e,'par018' => $par018e,'par019' => $par019e,'par020' => $par020e,
                'par021' => $par021e,'par022' => $par022e,'par023' => $par023e,'par024' => $par024e,'par025' => $par025e,
                'par026' => $par026e,'par027' => $par027e,'par028' => $par028e,'par029' => $par029e,'par030' => $par030e,
                'par031' => $par031e,'par032' => $par032e,'par033' => $par033e,'par034' => $par034e,'par035' => $par035e,
                'par036' => $par036e,'par037' => $par037e,'par038' => $par038e,'par039' => $par039e,'par040' => $par040e,
                'par041' => $par041e,'par042' => $par042e,'par043' => $par043e,'par044' => $par044e
            ]
        );

        if($cnt_input == 5){ 
            if ($resulte) {
                session()->setFlashdata('message', 'Input Berhasil');
            } else {
                session()->setFlashdata('message', 'Input Failed!'); 
            }
            return redirect()->to(base_url('input'));
        }
    }
}
