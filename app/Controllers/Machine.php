<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MachineModel;

class Machine extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    // Ubah nama method dari getList menjadi list agar sesuai dengan URL AJAX
    // Ganti nama menjadi getList agar aman dari error PHP
    public function getList()
    {
        error_reporting(0);
        $processCode = $this->request->getGet('process'); 
        
        if (empty($processCode) || $processCode === '-' || $processCode === 'null') {
            return $this->response->setJSON([]);
        }

        try {
            $machineModel = new \App\Models\MachineModel();
            // Menggunakan fungsi pintar dari model Anda
            $mesin = $machineModel->getMachineByProcess($processCode);
            return $this->response->setJSON($mesin);
        } catch (\Throwable $th) {
            return $this->response->setJSON([]);
        }
    }

    // ... (Fungsi index, getProcessesByDevice, save, dll TETAP SAMA SEPERTI SEBELUMNYA) ...
    public function index()
    {
        if (!$this->session->get('isadmin')) {
            return redirect()->to(base_url('/'))->with('message', 'Akses Ditolak: Khusus Admin!');
        }

        $db = \Config\Database::connect();
        $machineModel = new MachineModel();
        
        $data = [
            'title' => 'Machine Data',
            'devices' => $db->table('device')->get()->getResultArray(),
            'alldata' => $machineModel->findAll()
        ];
        
        return view('machine', $data); 
    }

    public function getProcessesByDevice()
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Akses Ditolak']);
        }

        $deviceCode = $this->request->getGet('device_code');
        $type = $this->request->getGet('type');

        $db = \Config\Database::connect();
        $processes = $db->table('proses')
                        ->where('device', $deviceCode)
                        ->where('type', $type)
                        ->get()
                        ->getResultArray();
        
        return $this->response->setJSON($processes);
    }

    public function save()
    {
        if (!$this->session->get('isadmin')) {
            return redirect()->to(base_url('/'))->with('message', 'Akses Ditolak!');
        }

        $process_code = $this->request->getPost('process_code');
        $machine_name = strtoupper(trim($this->request->getPost('machine_name')));

        if (empty($process_code) || empty($machine_name)) {
            return redirect()->back()->with('message', 'Data tidak boleh kosong.');
        }

        $machineModel = new MachineModel();
        $cekDuplikat = $machineModel->where('process_code', $process_code)
                                    ->where('machine_name', $machine_name)
                                    ->first();
                                    
        if ($cekDuplikat) {
            return redirect()->back()->with('message', "Gagal! Mesin {$machine_name} sudah terdaftar.");
        }

        $machineModel->insert([
            'process_code' => $process_code,
            'machine_name' => $machine_name
        ]);

        return redirect()->to(base_url('machine'))->with('message', "Sukses! Mesin berhasil ditambahkan.");
    }

    public function deleteMachine($id)
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setStatusCode(403);
        }

        $machineModel = new MachineModel();
        $machineModel->delete($id);
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function editMachineForm($id)
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setStatusCode(403);
        }

        $machineModel = new MachineModel();
        $data = [
            'machine' => $machineModel->find($id)
        ];

        return view('layout/edit-machine', $data);
    }

    public function updateMachine()
    {
        if (!$this->session->get('isadmin')) {
            return redirect()->to(base_url('/'));
        }

        $id = $this->request->getPost('id');
        $process_code = $this->request->getPost('process_code');
        $machine_name = strtoupper(trim($this->request->getPost('machine_name')));

        $machineModel = new MachineModel();
        
        $machineModel->update($id, [
            'process_code' => $process_code,
            'machine_name' => $machine_name
        ]);

        return redirect()->to(base_url('machine'))->with('message', "Sukses! Data mesin berhasil diperbarui.");
    }
}