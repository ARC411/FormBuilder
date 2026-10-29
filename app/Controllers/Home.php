<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InputModel;
use App\Models\ProductionModel;
use App\Models\EmpModel;
use CodeIgniter\CodeIgniter;

class Home extends BaseController
{
    protected $session;
    protected $InputModel;
    protected $ProductionModel;
    protected $EmpModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->InputModel = new InputModel();
        $this->ProductionModel = new ProductionModel();
        $this->EmpModel = new EmpModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        return view('index', $data);
    }

    public function formProduction($device, $process): string
    {
        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        return view("/layout/" . $device . "/input/production/" . $process, $data);
    }

    public function formStartup($device, $process): string
    {
        $data = [
            'title' => 'Input | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        return view("/layout/sl56/input/startup/" . $process, $data);
    }

    public function approve()
    {
        if (!$this->session->has('empid')) {
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to('');
        }
        $data = [
            'title' => 'Approval | Startup Management',
            'validation' => \Config\Services::validation()
        ];
        return view('approval', $data);
    }

    public function approval(): string
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->getAll($_GET['dateStart'], $_GET['dateEnd'], $_GET['process'], $_GET['model'], $_GET['lotno'], $_GET['machno'])
        ];

        return view("/layout/approval/" . $_GET['process'], $data);
    }

    public function ajaxAutofill()
    {
        $empid = trim((string)$this->request->getGet('empid-txt'));

        if ($empid === '') {
            return $this->response->setJSON(['found' => false]);
        }

        $q = null;
        try {
            $q = $this->EmpModel->getEmp($empid);
        } catch (\Throwable $e) {
            log_message('error', 'ajaxAutofill fallback: ' . $e->getMessage());
        }

        if ($q) {
            return $this->response->setJSON([
                'found'      => true,
                'empid'      => $q->empid ?? $empid,
                'name'       => $q->name ?? strtoupper($empid),
                'positionid' => $q->positionid ?? ($this->session->get('positionid') ?? 'Operator'),
                'groupid'    => $q->groupid ?? '',
                'acc'        => $q->acc ?? '',
            ]);
        }

        // Fallback ke session jika EmpID yang diketik sama dengan user yang sedang login
        $sessEmp = trim((string)$this->session->get('empid'));
        if ($sessEmp !== '' && strcasecmp($empid, $sessEmp) === 0) {
            return $this->response->setJSON([
                'found'      => true,
                'empid'      => $sessEmp,
                'name'       => (string)($this->session->get('name') ?: $sessEmp),
                'positionid' => (string)($this->session->get('positionid') ?: 'Operator'),
                'groupid'    => (string)($this->session->get('groupid') ?? ''),
                'acc'        => (string)($this->session->get('shift') ?? ''),
            ]);
        }

        // Kembalikan HTTP 200 (bukan 500) agar form tidak menghapus isian manual operator
        return $this->response->setJSON([
            'found'   => false,
            'empid'   => $empid,
            'name'    => strtoupper($empid),
            'groupid' => '',
            'acc'     => '',
        ]);
    }

    public function data()
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->getAll($_GET['dateStart'], $_GET['dateEnd'], $_GET['process'], $_GET['model'], $_GET['lotno'], $_GET['machno'])
        ];

        return view("/layout/" . $_GET['device'] . "/history/production/" . $_GET['process'], $data);
    }

    public function history(): string
    {
        $data = [
            'title' => 'History | Startup Management',
        ];
        return view('history', $data);
    }

    public function test2()
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->getLotHistory($_GET['dateStart'], $_GET['dateEnd'], $_GET['process'], $_GET['model'], $_GET['lotno'], $_GET['machno'], $_GET['device'])
        ];
        return view('data', $data);
    }
}