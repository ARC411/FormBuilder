<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MachineModel;
use App\Models\LogActivityModel;

class Machine extends BaseController
{
    protected $session;
    protected $logActivityModel;

    public function __construct()
    {
        $this->session          = \Config\Services::session();
        $this->logActivityModel = new LogActivityModel();
    }

    public function getList()
    {
        error_reporting(0);
        $processCode = $this->request->getGet('process'); 
        
        if (empty($processCode) || $processCode === '-' || $processCode === 'null') {
            return $this->response->setJSON([]);
        }

        try {
            $machineModel = new \App\Models\MachineModel();
            $mesin = $machineModel->getMachineByProcess($processCode);
            return $this->response->setJSON($mesin);
        } catch (\Throwable $th) {
            return $this->response->setJSON([]);
        }
    }

    public function index()
    {
        if (!$this->session->get('isadmin')) {
            return redirect()->to(base_url('/'))->with('message', 'Akses Ditolak: Khusus Admin!');
        }

        $db = \Config\Database::connect();
        $machineModel = new MachineModel();
        
        $data = [
            'title'   => 'Machine Data',
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

        // Catat ke Log Activity
        $this->catatLogMesin(
            'TAMBAH MESIN',
            "Menambahkan Machine Number baru '{$machine_name}' pada proses " . strtoupper($process_code),
            $process_code
        );

        return redirect()->to(base_url('machine'))->with('message', "Sukses! Mesin berhasil ditambahkan.");
    }

    public function deleteMachine($id)
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setStatusCode(403);
        }

        $machineModel = new MachineModel();
        $oldData      = $machineModel->find($id);

        $machineModel->delete($id);

        // Catat ke Log Activity
        if ($oldData) {
            $oldName = $oldData['machine_name'] ?? ('ID ' . $id);
            $oldProc = $oldData['process_code'] ?? 'machine';
            $this->catatLogMesin(
                'HAPUS MESIN',
                "Menghapus Machine Number '{$oldName}' dari proses " . strtoupper($oldProc),
                $oldProc
            );
        }
        
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

        $id           = $this->request->getPost('id');
        $process_code = $this->request->getPost('process_code');
        $machine_name = strtoupper(trim($this->request->getPost('machine_name')));

        $machineModel = new MachineModel();
        $oldData      = $machineModel->find($id);
        
        $machineModel->update($id, [
            'process_code' => $process_code,
            'machine_name' => $machine_name
        ]);

        // Catat ke Log Activity (lengkap dengan perbandingan data lama -> baru)
        $oldName = $oldData['machine_name'] ?? '-';
        $oldProc = isset($oldData['process_code']) ? strtoupper($oldData['process_code']) : '-';
        $newProc = strtoupper($process_code);

        $this->catatLogMesin(
            'EDIT MESIN',
            "Mengubah Machine Number dari '{$oldName}' ({$oldProc}) menjadi '{$machine_name}' ({$newProc})",
            $process_code
        );

        return redirect()->to(base_url('machine'))->with('message', "Sukses! Data mesin berhasil diperbarui.");
    }

    // --- HELPER PENCATAT LOG ACTIVITY MESIN ---
    private function catatLogMesin(string $aksi, string $detail, string $processCode = '')
    {
        try {
            $tableName = $processCode !== '' ? strtoupper($processCode) : 'machine';
            $pesan     = "[Machine - {$aksi}] {$detail}";

            if (method_exists($this->logActivityModel, 'logAction')) {
                $res = $this->logActivityModel->logAction($aksi, $pesan, $tableName);
                if ($res !== false) {
                    return;
                }
            }

            $this->logActivityModel->protect(false)->insert([
                'emp_id'        => (string)($this->session->get('empid') ?? 'admin'),
                'type_activity' => $aksi,
                'activity'      => $pesan,
                'user'          => (string)($this->session->get('name') ?? $this->session->get('empid') ?? 'admin'),
                'table_name'    => $tableName,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat Log Activity Machine: ' . $e->getMessage());
        }
    }
}