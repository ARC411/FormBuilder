<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InputModel;
use App\Models\ProductionModel;
use App\Models\StartupModel;
use App\Models\EmpModel;
use CodeIgniter\CodeIgniter;

class Test extends BaseController
{
    protected $session;
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->InputModel = new InputModel();
        $this->ProductionModel = new ProductionModel();
        $this->StartupModel = new StartupModel();
        $this->EmpModel = new EmpModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function testAdd($number,$par)
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->testProses($number,$par)
        ];
        return view("data",$data);
    }

    public function testGetAll()
    {
        $data = [
            'alldata' => $this->ProductionModel->getAll($process='sl56-p-lt')
        ];
        return view("data",$data);
    }

    public function testEmp()
    {
        $data = [
            'alldata' => $this->EmpModel->getAll()
        ];
        return view("data", $data);
    }

    public function testStartup($id,$name)
    {
        $data=[
            'title' => 'testing',
            'alldata' => $this->StartupModel->updateApproval($id,$name,$id)
        ];
        return view("data",$data);
    }
}
