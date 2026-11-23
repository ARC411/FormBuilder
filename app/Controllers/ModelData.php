<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelListModel;
use App\Models\LogActivityModel;

class ModelData extends BaseController
{
    protected $session;
    protected $modelListModel;
    protected $logActivityModel;

    public function __construct()
    {
        $this->session          = \Config\Services::session();
        $this->modelListModel   = new ModelListModel();
        $this->logActivityModel = new LogActivityModel();
    }

    // 1. GET Daftar Model per Process (Untuk Dropdown di Form Builder, Input, dan History)
    public function getList()
    {
        $processCode = trim((string)$this->request->getGet('process'));
        $docType     = trim((string)$this->request->getGet('type'));

        if ($processCode === '' || $processCode === '-' || $processCode === 'null') {
            return $this->response->setJSON([]);
        }

        // 1. Ambil dari master model_list
        $models = [];
        try {
            $models = $this->modelListModel->getModelsByProcess($processCode);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memuat master model_list: ' . $e->getMessage());
        }

        $modelNames = [];
        foreach ($models as &$m) {
            $m['standards'] = !empty($m['standards_json']) ? json_decode($m['standards_json'], true) : [];
            $modelNames[strtoupper(trim((string)$m['model_name']))] = true;
        }

        // 2. Ambil juga model-model dari riwayat transaksi (production, foregoing, startup)
        // khusus untuk proses ini, agar dropdown di History dan Input terisi lengkap by process
        try {
            $db = \Config\Database::connect('default');
            $tables = ['production', 'foregoing', 'startup'];
            if ($docType && in_array($docType, $tables, true)) {
                $tables = array_unique(array_merge([$docType], $tables));
            }

            foreach ($tables as $tbl) {
                if ($db && $db->tableExists($tbl)) {
                    $rows = $db->table($tbl)
                        ->distinct()
                        ->select('model')
                        ->groupStart()
                            ->where('process', $processCode)
                            ->orWhere('process', strtolower($processCode))
                            ->orWhere('process', strtoupper($processCode))
                        ->groupEnd()
                        ->where('model IS NOT NULL', null, false)
                        ->where('model !=', '')
                        ->where('model !=', '-')
                        ->orderBy('model', 'ASC')
                        ->get()
                        ->getResultArray();

                    foreach ($rows as $r) {
                        $mName = strtoupper(trim((string)$r['model']));
                        if ($mName !== '' && $mName !== '-' && !isset($modelNames[$mName])) {
                            $modelNames[$mName] = true;
                            $models[] = [
                                'id'             => null,
                                'process_code'   => $processCode,
                                'model_name'     => $mName,
                                'standards_json' => null,
                                'standards'      => [],
                            ];
                        }
                    }
                }
            }

            // Urutkan alfabetis
            usort($models, function($a, $b) {
                return strcasecmp((string)$a['model_name'], (string)$b['model_name']);
            });
        } catch (\Throwable $e) {
            log_message('error', 'Gagal memuat model transaksi di ModelData::getList: ' . $e->getMessage());
        }

        return $this->response->setJSON($models);
    }

    // 2. POST Auto-Register Model Baru dari Halaman Input Operator
    public function autoRegister()
    {
        $processCode = strtolower(trim((string)$this->request->getPost('process_code')));
        $modelName   = strtoupper(trim((string)$this->request->getPost('model_name')));

        if ($processCode === '' || $modelName === '' || $modelName === '-') {
            return $this->response->setJSON(['status' => 'ignored']);
        }

        $existing = $this->modelListModel->groupStart()
                                         ->where('process_code', $processCode)
                                         ->orWhere('process_code', strtoupper($processCode))
                                         ->orWhere('LOWER(TRIM(process_code))', $processCode)
                                         ->groupEnd()
                                         ->where('model_name', $modelName)
                                         ->first();

        if (!$existing) {
            $record = $this->modelListModel->autoRegister($processCode, $modelName);
            $this->catatLogModel(
                'AUTO-REGISTER MODEL',
                "Model baru '{$modelName}' otomatis terdaftar dari halaman Input pada proses " . strtoupper($processCode),
                $processCode
            );
            return $this->response->setJSON([
                'status'  => 'registered',
                'is_new'  => true,
                'data'    => $record
            ]);
        }

        $existing['standards'] = !empty($existing['standards_json']) ? json_decode($existing['standards_json'], true) : [];
        return $this->response->setJSON([
            'status' => 'exists',
            'is_new' => false,
            'data'   => $existing
        ]);
    }

    // 3. POST Tambah atau Edit Model Name (dari Form Builder / Manajemen Model)
    public function saveModel()
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak!']);
        }

        $id          = (int)$this->request->getPost('id');
        $processCode = strtolower(trim((string)$this->request->getPost('process_code')));
        $modelName   = strtoupper(trim((string)$this->request->getPost('model_name')));

        if ($processCode === '' || $modelName === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kode Proses dan Nama Model wajib diisi!']);
        }

        if ($id > 0) {
            $old = $this->modelListModel->find($id);
            $oldName = $old['model_name'] ?? '-';
            $this->modelListModel->update($id, [
                'model_name' => $modelName
            ]);
            $this->catatLogModel('EDIT MODEL', "Mengubah nama Model dari '{$oldName}' menjadi '{$modelName}' pada proses " . strtoupper($processCode), $processCode);
            return $this->response->setJSON(['status' => 'success', 'message' => "Model berhasil diubah menjadi {$modelName}."]);
        } else {
            $dup = $this->modelListModel->groupStart()
                                       ->where('process_code', $processCode)
                                       ->orWhere('process_code', strtoupper($processCode))
                                       ->orWhere('LOWER(TRIM(process_code))', $processCode)
                                       ->groupEnd()
                                       ->where('model_name', $modelName)
                                       ->first();
            if ($dup) {
                return $this->response->setJSON(['status' => 'error', 'message' => "Model {$modelName} sudah terdaftar di proses ini!"]);
            }
            $this->modelListModel->insert([
                'process_code' => $processCode,
                'model_name'   => $modelName
            ]);
            $newId = $this->modelListModel->getInsertID();
            $this->catatLogModel('TAMBAH MODEL', "Menambahkan Model baru '{$modelName}' pada proses " . strtoupper($processCode), $processCode);
            return $this->response->setJSON(['status' => 'success', 'id' => $newId, 'model_name' => $modelName, 'message' => "Model {$modelName} berhasil ditambahkan."]);
        }
    }

    // 4. POST Simpan Standar X, Y, Z, θ untuk Model Tertentu di Form Builder Foregoing
    public function saveStandards()
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak!']);
        }

        $modelId       = (int)$this->request->getPost('model_id');
        $standardsJson = $this->request->getPost('standards_json');

        $modelRow = $this->modelListModel->find($modelId);
        if (!$modelRow) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data Model tidak ditemukan!']);
        }

        $decoded = json_decode((string)$standardsJson, true);
        if (!is_array($decoded)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format standar tidak valid!']);
        }

        $this->modelListModel->update($modelId, [
            'standards_json' => json_encode($decoded, JSON_UNESCAPED_UNICODE)
        ]);

        // Ringkasan log
        $summaryParts = [];
        foreach ($decoded as $posKey => $cfg) {
            if (!empty($cfg['active'])) {
                $minTxt = ($cfg['min'] !== '' && $cfg['min'] !== null) ? $cfg['min'] : '-∞';
                $maxTxt = ($cfg['max'] !== '' && $cfg['max'] !== null) ? $cfg['max'] : '∞';
                $summaryParts[] = "{$posKey}: {$minTxt}~{$maxTxt}";
            } else {
                $summaryParts[] = "{$posKey}: OFF";
            }
        }
        $summaryStr = implode(', ', $summaryParts);

        $this->catatLogModel(
            'UPDATE STANDAR MODEL',
            "Memperbarui standar Foregoing untuk Model '{$modelRow['model_name']}' ({$summaryStr})",
            $modelRow['process_code']
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "Standar untuk Model {$modelRow['model_name']} berhasil disimpan!"
        ]);
    }

    // 5. POST Hapus Model
    public function deleteModel()
    {
        if (!$this->session->get('isadmin')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak!']);
        }

        $id  = (int)$this->request->getPost('id');
        $old = $this->modelListModel->find($id);
        if ($old) {
            $this->modelListModel->delete($id);
            $this->catatLogModel('HAPUS MODEL', "Menghapus Model '{$old['model_name']}' dari proses " . strtoupper($old['process_code']), $old['process_code']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Model berhasil dihapus.']);
    }

    private function catatLogModel(string $aksi, string $detail, string $processCode = '')
    {
        try {
            $tableName = $processCode !== '' ? strtoupper($processCode) : 'model_list';
            $pesan     = "[Model - {$aksi}] {$detail}";

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
            log_message('error', 'Gagal mencatat Log Activity Model: ' . $e->getMessage());
        }
    }
}
