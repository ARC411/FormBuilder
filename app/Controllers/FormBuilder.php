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
        $processCode = $this->request->getPost('process');
        if (!$processCode) {
            return redirect()->back()->with('error', 'Pilih proses terlebih dahulu!');
        }

        // 1. Cari data Revisi
        $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
        
        if (!$revision) {
            // Jika belum ada revisi sama sekali, buatkan revisi 0 (Default kategori production)
            $this->formRevModel->insert([
                'process_code'  => $processCode,
                'revisi'        => '0',
                'kategori_form' => 'production', // Kategori default
                'created_at'    => date('Y-m-d H:i:s')
            ]);
            $revisionId = $this->formRevModel->getInsertID();
            $revision = $this->formRevModel->find($revisionId);
        } else {
            $revisionId = $revision['id'];
        }

        // 2. Ambil Semua Parameter Mentah
        $rawParams = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'ASC')->findAll();
        
        // 3. Masukkan ke Mesin Pohon Keluarga
        $treeParams = $this->buildTree($rawParams);

        // 4. Hitung Maksimal Kedalaman (Untuk Colspan Total)
        $maxTableDepth = 0;
        foreach ($treeParams as $node) {
            $d = $this->calculateDepth($node);
            if ($d > $maxTableDepth) {
                $maxTableDepth = $d;
            }
        }

        // 5. Lempar data ke View Visual Builder
        $data = [
            'process_code'  => $processCode,
            'revision'      => $revision,
            'kategori_form' => $revision['kategori_form'] ?? 'production',
            'treeParams'    => $treeParams,
            'maxTableDepth' => $maxTableDepth
        ];

        // RENDER VIEW KE DALAM VARIABEL, LALU BUNGKUS JADI JSON
        $htmlUtuh = view('form_builder_visual', $data); 
        return $this->response->setJSON([
            'status' => 'success', 
            'html'   => $htmlUtuh
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

    // --- ROBOT MASSAL V2 (MODE DETEKTIF) ---
    public function sedotHtml()
    {
        $semuaProses = $this->processModel->findAll();
        $totalProses = count($semuaProses);
        
        echo "<div style='font-family: Arial; padding: 20px; background-color: #f8f9fa; border: 1px solid #ddd;'>";
        echo "<h2 style='color: #333;'>LOG INVESTIGASI ROBOT:</h2>";
        echo "<b>LANGKAH 1:</b> Mencari data proses di database... Ditemukan: <b style='color:blue;'>{$totalProses} proses</b>.<br>";
        
        // Kalau tabel proses kosong, langsung hentikan!
        if ($totalProses == 0) {
            echo "<p style='color:red;'><b>STOP:</b> Pantesan 0 parameter, tabel 'proses' di database Abang KOSONG! Pastikan Abang sudah meng-copy database dari server intra3 ke localhost.</p>";
            echo "</div>";
            return;
        }

        $totalTersedot = 0;
        $totalDuplikat = 0;
        $fileGagal = [];
        $fileSukses = 0;

        foreach ($semuaProses as $proses) {
            $processCode = $proses['process_code'];
            $angkaRevisi = $proses['revisi'] ?? '0';

            // Bikin ID Revisi
            $revision = $this->formRevModel->where('process_code', $processCode)->orderBy('id', 'DESC')->first();
            if (!$revision) {
                $this->formRevModel->insert([
                    'process_code' => $processCode,
                    'revisi'       => $angkaRevisi,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
                $revisionId = $this->formRevModel->getInsertID();
            } else {
                $revisionId = $revision['id'];
            }

            // Cari File
            $parts = explode('-', $processCode);
            if (count($parts) < 2) continue; 
            
            $device = $parts[0];
            $typeKode = $parts[1];
            
            $typeFolder = 'production';
            if ($typeKode == 's') $typeFolder = 'startup';
            if ($typeKode == 'f') $typeFolder = 'foregoing';

            // Lokasi File (Pastikan huruf besar-kecilnya sesuai dengan di folder Abang)
            $filePath = APPPATH . "Views/layout/{$device}/input/{$typeFolder}/{$processCode}.php";

            if (!file_exists($filePath)) {
                $fileGagal[] = $processCode . " <br><small><i>(Dicari di: " . $filePath . ")</i></small>";
                continue; 
            }

            $fileSukses++;
            $htmlContent = file_get_contents($filePath);
            
            // Hapus Regex (Biar tag HTML aman tidak terpotong)
            // Parsing DOM
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($htmlContent);
            libxml_clear_errors();
            
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//input[contains(@name, "par")] | //select[contains(@name, "par")]');
            
            $lastUrutan = $this->formParamModel->where('revision_id', $revisionId)->orderBy('urutan', 'DESC')->first();
            $urutan = $lastUrutan ? ($lastUrutan['urutan'] + 1) : 1;

            foreach ($nodes as $node) {
                $kolomDb = $node->getAttribute('name'); 
                
                $cekData = $this->formParamModel->where(['revision_id' => $revisionId, 'kolom_db' => $kolomDb])->first();
                if (!$cekData) {
                    $tagName = $node->tagName;
                    $inputType = 'text'; 
                    if ($tagName == 'select') {
                        $inputType = 'dropdown_ok_ng';
                    } elseif ($tagName == 'input') {
                        $typeAttr = $node->getAttribute('type');
                        if ($typeAttr == 'number' || $typeAttr == 'step') {
                            $inputType = 'number';
                        }
                    }

                    $this->formParamModel->insert([
                        'revision_id'    => $revisionId,
                        'parent_id'      => null, 
                        'nama_parameter' => 'Input ' . strtoupper($kolomDb), 
                        'kolom_db'       => $kolomDb,
                        'tipe_input'     => $inputType,
                        'standar_nilai'  => null,
                        'urutan'         => $urutan
                    ]);
                    $totalTersedot++;
                    $urutan++;
                } else {
                    $totalDuplikat++;
                }
            }
        }

        echo "<b>LANGKAH 2:</b> Cek fisik file HTML... Ditemukan: <b style='color:green;'>{$fileSukses} file</b>. Gagal: <b style='color:red;'>".count($fileGagal)." file</b>.<br>";
        
        echo "<hr><h2 style='color: green;'>EKSEKUSI MASSAL SELESAI!</h2>";
        echo "<h3>Total Parameter Tersedot: <b>{$totalTersedot}</b> parameter baru.</h3>";
        
        if ($totalDuplikat > 0) {
             echo "<h4 style='color: orange;'>Terdapat <b>{$totalDuplikat}</b> parameter yang di-skip karena sudah ada di database (mencegah duplikat).</h4>";
        }

        if (!empty($fileGagal)) {
            echo "<p style='color: red;'><b>Daftar File yang gagal dibaca karena tidak ada di folder:</b><br>" . implode("<br>", $fileGagal) . "</p>";
        }
        
        echo "<br><a href='".base_url('form-builder')."' class='btn btn-primary'>Kembali ke Form Builder</a>";
        echo "</div>";
    }

    // --- ENGINE 1: PENYUSUN POHON KELUARGA (REKURSIF) ---
    private function buildTree(array $elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                } else {
                    $element['children'] = [];
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    // --- ENGINE 2: PENGHITUNG LEBAR KOTAK (COLSPAN) ---
    private function calculateDepth($node) {
        if (empty($node['children'])) {
            return 1;
        }
        $maxDepth = 0;
        foreach ($node['children'] as $child) {
            $depth = $this->calculateDepth($child);
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
        return $maxDepth + 1;
    }
}