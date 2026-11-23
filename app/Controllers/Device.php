<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DeviceModel;
use App\Models\LogActivityModel;

class Device extends BaseController
{
    protected $DeviceModel;
    private $LogActivityModel;
    private $session;
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->DeviceModel = new DeviceModel();
        $this->LogActivityModel = new LogActivityModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    public function listDevice()
    {
        error_reporting(0);
        $allDevices = $this->DeviceModel->getAll();

        // Filter device sesuai divisi/role user jika bukan admin atau role bukan 'all'
        $role    = strtolower((string)($this->session->get('role') ?? 'all'));
        $isAdmin = (bool)($this->session->get('isadmin') ?? false);

        if (!$isAdmin && $role !== 'all') {
            $filtered = [];
            foreach ($allDevices as $dev) {
                $code = strtolower((string)($dev['code'] ?? ''));
                $name = strtoupper((string)($dev['name'] ?? ''));
                if ($role === 'fl') {
                    if (str_starts_with($code, 'fl') || str_contains($name, 'FRAME')) {
                        $filtered[] = $dev;
                    }
                } elseif ($role === 'sl') {
                    if (str_starts_with($code, 'sl') || str_contains($name, 'SINGLE')) {
                        $filtered[] = $dev;
                    }
                }
            }
            $allDevices = array_values($filtered);
        }

        echo json_encode($allDevices);
    }

    public function index()
    {
        $data = [
            'title' => 'Device Data',
            'alldata' => $this->DeviceModel->getAll()
        ];

        return view("device", $data);
    }

    public function createDeviceForm()
    {
        $data = [
            'title' => 'Add Device'
        ];

        return view("layout/add-device", $data);
    }

    public function updateDeviceForm($id)
    {
        $data = [
            'title' => 'Add Device',
            'alldata' => $this->DeviceModel->getDeviceById($id)
        ];

        return view('layout/edit-device',$data);
        // echo "edit deviceeee";
    }

    public function createDevice()
    {
        $data = [
            'title' => 'Create Startup',
            'alldata' => $this->DeviceModel->getAll()
        ];

        $device_code = $this->request->getVar('device-code');
        $device_name = $this->request->getVar('device-name');

        $result = $this->DeviceModel->addDevice(
            [
                'code' => $device_code,
                'name' => $device_name,
            ]
        );

        if($result=="Berhasil menambahkan data"){
            $this->LogActivityModel->addLogActivity(
                [
                    'emp_id'=> $this->session->get('empid'),
                    'type_activity'=>'Add',
                    'activity'=> 'Add Device '.$device_name,
                    'user'=> $this->session->get('name'),
                    'table_name' => 'Device'
                ]
            );
        }

        session()->setFlashdata('message', $result);
        return redirect()->to(base_url().'device');
    }

    public function updateDevice()
    {
        $id = $this->request->getVar('id');
        $device_code = $this->request->getVar('device-code');
        $device_name = $this->request->getVar('device-name');

        $result = $this->DeviceModel->updateDevice(
            $id,
            [
                'code' => $device_code,
                'name' => $device_name,
            ]
        );
        if($result=="Berhasil Update"){
            $this->LogActivityModel->addLogActivity(
                [
                    'emp_id'=> $this->session->get('empid'),
                    'type_activity'=>'Update',
                    'activity'=> 'Update Device '.$device_name,
                    'user'=> $this->session->get('name'),
                    'table_name' => 'Device'
                ]
            );
        }
        session()->setFlashdata('message', $result);
        return redirect()->to(base_url().'device');
    }

    public function deleteDevice($id)
    {
        $devicebyId = $this->DeviceModel->getDeviceById($id);
        $result = $this->DeviceModel->deleteDevice($id);
        if($result){
            $this->LogActivityModel->addLogActivity(
                [
                    'emp_id'=> $this->session->get('empid'),
                    'type_activity'=>'Delete',
                    'activity'=> 'Delete Device '.$devicebyId[0]['name'],
                    'user'=> $this->session->get('name'),
                    'table_name' => 'Device'
                ]
            );
        }
        session()->setFlashdata('message', $result ? 'Berhasil menghapus perangkat.' : 'Gagal menghapus perangkat.');
        return redirect()->to(base_url().'device');
    }
}