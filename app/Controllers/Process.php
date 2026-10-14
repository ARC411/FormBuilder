<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProcessModel;
use App\Models\DeviceModel;
use App\Models\LogActivityModel;

class Process extends BaseController
{
    protected $ProcessModel;
    private $DeviceModel;
    private $LogActivityModel;
    private $session;
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->ProcessModel = new ProcessModel();
        $this->DeviceModel = new DeviceModel();
        $this->LogActivityModel = new LogActivityModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function listProcess()
    {
        error_reporting(0);
        try {
            // Mengambil get dengan aman agar tidak error jika nilainya kosong
            $device = $this->request->getGet('device') ?? '';
            $type = $this->request->getGet('type') ?? '';
            
            $data = $this->ProcessModel->getAll($device, $type);
            return $this->response->setJSON($data);
        } catch (\Throwable $th) {
            // Jika tabel error/tidak ditemukan, kembalikan array kosong, bukan error 500
            return $this->response->setJSON([]);
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Process Data',
            'alldata' => $this->ProcessModel->getAllData(),
            'listDevice' => $this->DeviceModel->getAll()
        ];
        return view("process",$data);
    }

    public function addProcess()
    {
        $data = [
            'title' => 'Add Process',
            'listDevice' => $this->DeviceModel->getAll()
        ];

        return view("layout/add-process",$data);
    }

    public function updateProcessForm($id)
    {
        $data = [
            'title' => 'Add Device',
            'alldata' => $this->ProcessModel->getProcessById($id),
            'listDevice' => $this->DeviceModel->getAll(),
        ];

        return view('layout/edit-process',$data);
    }

    public function createProcess()
    {
        $data = [
            'title' => 'Create Startup',
            'validation' => \Config\Services::validation()
        ];

        $device = $this->request->getVar('device');
        $type = $this->request->getVar('type');
        $process_name = $this->request->getVar('process-name');
        $process_code = $this->request->getVar('process-code');
        $docno = $this->request->getVar('docno');

        $result = $this->ProcessModel->addProcess
        (
            [
                'device' => $device,
                'type' => $type,
                'process_code' => $process_code,
                'name' => $process_name,
                'docno' => $docno,
            ]
        );
        $this->LogActivityModel->addLogActivity(
            [
                'emp_id'=> $this->session->get('empid'),
                'type_activity'=>'Create',
                'activity'=> 'Create Process '.$process_name,
                'user'=> $this->session->get('name'),
                'table_name' => 'Process'
            ]
        );
        session()->setFlashdata('message', $result);

        return redirect()->to(base_url().'process');
    }

    public function updateProcess()
    {
        $data = [
            'title' => 'Create Startup',
            'validation' => \Config\Services::validation()
        ];

        $id = $this->request->getVar('id');
        $device = $this->request->getVar('device');
        $type = $this->request->getVar('type');
        $process_name = $this->request->getVar('process-name');
        $process_code = $this->request->getVar('process-code');
        $docno = $this->request->getVar('docno');

        $result = $this->ProcessModel->updateProcess
        (
            $id,
            [
                'device' => $device,
                'type' => $type,
                'process_code' => $process_code,
                'name' => $process_name,
                'docno' => $docno,
            ]
        );
        $this->LogActivityModel->addLogActivity(
            [
                'emp_id'=> $this->session->get('empid'),
                'type_activity'=>'Update',
                'activity'=> 'Update Process '.$process_name,
                'user'=> $this->session->get('name'),
                'table_name' => 'Process'
            ]
        );
        session()->setFlashdata('message', $result);

        return redirect()->to(base_url().'process');
    }

    public function deleteProcess($id)
    {
        $data = [
            'title' => 'Delete Process',
            'validation' => \Config\Services::validation()
        ];

        $process = $this->ProcessModel->getProcessById($id);
        $result = $this->ProcessModel->deleteProcess($id);
        $this->LogActivityModel->addLogActivity(
            [
                'emp_id'=> $this->session->get('empid'),
                'type_activity'=>'Delete',
                'activity'=> 'Delete Process '.$process[0]['process_name'],
                'user'=> $this->session->get('name'),
                'table_name' => 'Process'
            ]
        );
        session()->setFlashdata('message', $result);
        return redirect()->to(base_url().'process');
    }
}
