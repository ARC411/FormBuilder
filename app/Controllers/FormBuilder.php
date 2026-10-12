<?php

namespace App\Controllers;

use App\Models\DeviceModel;
use App\Models\ProcessModel;
use App\Models\FormRevisionModel;
use App\Models\FormParameterModel;

class FormBuilder extends BaseController
{
    protected $deviceModel;
    protected $processModel;
    protected $formRevModel;
    protected $formParamModel;

    public function __construct()
    {
        // 1. Kunci Pintu: Usir kalau bukan Admin
        if (!session()->get('isadmin')) { 
            echo "<script>alert('Akses Ditolak! Khusus Admin/QC.'); window.location.href='/';</script>";
            exit;
        }

        // 2. Load Model (Ini jangan dihapus)
        $this->deviceModel = new DeviceModel();
        $this->processModel = new ProcessModel();
        $this->formRevModel = new FormRevisionModel();
        $this->formParamModel = new FormParameterModel();
    }

    // 1. Menampilkan Halaman Utama
    public function index()
    {
        
        $data = [
            'title'   => 'Form Builder & Revision',
            'devices' => $this->deviceModel->findAll()
        ];
        return view('form_builder', $data); // Kita akan buat file view ini di langkah selanjutnya
    }

    // 2. AJAX Cascading Dropdown (Menggunakan fungsi getAll dari ProcessModel)
    public function getProcesses()
    {
        $deviceCode = $this->request->getGet('device_code');
        $type       = $this->request->getGet('type');

        // Panggil fungsi custom bawaan ProcessModel Abang
        $processes = $this->processModel->getAll($deviceCode, $type);
        
        return $this->response->setJSON($processes);
    }

    // 3. AJAX Load Form & Parameter (Mencari revisi terakhir)
    public function loadForm()
    {
        $processCode = $this->request->getPost('process_code');
        
        // 1. Cari atau Buat Revisi
        $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
        if (!$revision) {
            $masterProses = $this->processModel->where('process_code', $processCode)->first();
            $angkaRevisi = $masterProses['revisi'] ?? '0'; 
            
            $this->formRevModel->insert([
                'process_code' => $processCode,
                'revisi'       => $angkaRevisi,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            $revision = $this->formRevModel->orderBy('id', 'DESC')->first();
        }

        // 2. Tarik Data Parameter Murni dari Database
        $parameters = $this->formParamModel->where('revision_id', $revision['id'])
                                           ->orderBy('urutan', 'ASC') // Pakai kolom 'urutan' sesuai desain kita
                                           ->findAll();
                                           
        // 3. Kembalikan array murni ke View agar JavaScript yang menggambarnya
        return $this->response->setJSON([
            'status'     => 'success',
            'revision'   => $revision,   // Mengembalikan seluruh row revisi (termasuk .id dan .revisi)
            'parameters' => $parameters  // Mengembalikan array data
        ]);
    }

    // 4. AJAX Save (Untuk Create & Update Parameter)
    public function saveParameter()
    {
        $id = $this->request->getPost('id'); // Kalau kosong berarti Tambah, kalau ada isinya berarti Edit
        
        $data = [
            'revision_id'    => $this->request->getPost('revision_id'),
            'parent_id'      => $this->request->getPost('parent_id') ?: null,
            'nama_parameter' => $this->request->getPost('nama_parameter'),
            'kolom_db'       => $this->request->getPost('kolom_db') ?: null,
            'tipe_input'     => $this->request->getPost('tipe_input') ?: null,
            'standar_nilai'  => $this->request->getPost('standar_nilai') ?: null,
            'urutan'         => $this->request->getPost('urutan') ?: 0,
        ];

        if ($id) {
            $this->formParamModel->update($id, $data);
            $msg = 'Parameter berhasil diupdate!';
        } else {
            $this->formParamModel->insert($data);
            $msg = 'Parameter baru berhasil ditambahkan!';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    // 5. AJAX Delete Parameter (Anak & Cucu otomatis ikut terhapus berkat CASCADE database)
    public function deleteParameter()
    {
        $id = $this->request->getPost('id');
        $this->formParamModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Parameter beserta anaknya berhasil dihapus.']);
    }

    // 6. AJAX Naik Revisi (A ke B, dst)
    public function upRevisi()
    {
        $oldRevisionId = $this->request->getPost('revision_id');
        $oldRevision = $this->formRevModel->find($oldRevisionId);
        
        // Magic Increment Huruf/Angka Revisi
        $newRevisiCode = $oldRevision['revisi'];
        $newRevisiCode++; 

        // Insert Revisi Baru ke tabel form_revisions
        $this->formRevModel->insert([
            'process_code' => $oldRevision['process_code'],
            'revisi'       => $newRevisiCode,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
        $newRevisionId = $this->formRevModel->getInsertID();

        // DUPLIKASI seluruh parameter dari revisi lama ke revisi baru
        $oldParameters = $this->formParamModel->where('revision_id', $oldRevisionId)->findAll();
        
        // Kita butuh pemetaan ID lama ke ID baru karena parent_id-nya akan berubah
        $idMapping = []; 
        
        // Step 1: Duplikasi Parameter Utama (parent_id = NULL)
        foreach ($oldParameters as $param) {
            if (is_null($param['parent_id'])) {
                $param['revision_id'] = $newRevisionId;
                $oldParamId = $param['id'];
                unset($param['id']); // Hapus ID lama biar auto-increment jalan
                
                $this->formParamModel->insert($param);
                $idMapping[$oldParamId] = $this->formParamModel->getInsertID();
            }
        }
        
        // Step 2: Duplikasi Anak-anaknya (hubungkan ke bapak yang baru)
        foreach ($oldParameters as $param) {
            if (!is_null($param['parent_id'])) {
                $param['revision_id'] = $newRevisionId;
                // Ganti parent_id dengan ID bapak yang baru saja diduplikasi
                $param['parent_id'] = $idMapping[$param['parent_id']] ?? null; 
                unset($param['id']);
                
                $this->formParamModel->insert($param);
                // Tambahkan mapping untuk cucu-cucunya
                // $idMapping[$oldParamId] = $this->formParamModel->getInsertID(); (Jika level lebih dari 2)
            }
        }

        return $this->response->setJSON([
            'status' => 'success', 
            'message' => 'Revisi berhasil dinaikkan menjadi ' . $newRevisiCode
        ]);
    }

    // Fungsi Rahasia: Menjalankan Migrasi lewat Web (Bypass Terminal)
    public function eksekusiMigrasi()
    {
        $migrate = \Config\Services::migrations();
        try {
            $migrate->latest();
            echo "<h1>MANTAP! Migrasi Database Berhasil Dijalankan!</h1>";
            echo "<a href='".base_url('form-builder')."'>Kembali ke Form Builder</a>";
        } catch (\Throwable $e) {
            echo "Waduh, gagal Bang: " . $e->getMessage();
        }
    }
}