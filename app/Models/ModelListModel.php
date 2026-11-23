<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelListModel extends Model
{
    protected $table         = 'model_list';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['process_code', 'model_name', 'standards_json', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    /**
     * Memastikan tabel model_list tersedia di database lokal/kantor
     * dan otomatis mengisi 22 model default untuk fl18-f-fdb2 jika baru dibuat.
     */
    private function ensureSchema(): void
    {
        try {
            $db = \Config\Database::connect();
            if ($db && !$db->tableExists($this->table)) {
                $db->query("CREATE TABLE IF NOT EXISTS `model_list` (
                    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    `process_code` VARCHAR(50) NOT NULL,
                    `model_name` VARCHAR(100) NOT NULL,
                    `standards_json` TEXT NULL DEFAULT NULL,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    INDEX `idx_proc_model` (`process_code`, `model_name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                // Pre-seed 22 model fl18-f-fdb2 sesuai standar Foregoing
                $now = date('Y-m-d H:i:s');
                $seedModels = [
                    ['GH15130D8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
                    ['GH15030D8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
                    ['GH15230H8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
                    ['GH15250F8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH15120M8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 2.8],
                    ['GH15030R8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 2.8],
                    ['GH15230Q8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
                    ['GH16330A8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH1631AA8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH16350A8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH16P32B8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH33540A8C', 2.37, 2.43, null, null, 0.31, 0.39, -1.3, -0.3, 8.7],
                    ['GH33540D8C', 2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH3325A8C',  2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH33035A3C', 1.97, 2.03, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH33035D3C', 1.97, 2.03, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH17P28C8CL',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
                    ['GH17P28A8C2',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
                    ['GH17P28F8CL',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
                    ['GH33035A8C', 2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH15130C8C07',2.41,2.49, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                    ['GH15230C8C07',2.42,2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
                ];
                $batch = [];
                foreach ($seedModels as $m) {
                    $std = [
                        'X' => ['min' => $m[1], 'max' => $m[2], 'active' => 1],
                        'Y' => ['min' => $m[3], 'max' => $m[4], 'active' => ($m[3] !== null ? 1 : 0)],
                        'Z' => ['min' => $m[5], 'max' => $m[6], 'active' => 1],
                        'θ' => ['min' => $m[7], 'max' => $m[8], 'active' => 1],
                    ];
                    $batch[] = [
                        'process_code'   => 'fl18-f-fdb2',
                        'model_name'     => $m[0],
                        'standards_json' => json_encode($std, JSON_UNESCAPED_UNICODE),
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
                $db->table($this->table)->insertBatch($batch);
            }
        } catch (\Throwable $e) {
            // Silently ignore if connection fails or table exists
        }
    }

    /**
     * Ambil daftar model berdasarkan process_code.
     * Mendukung pencarian case-insensitive dan lintas tipe.
     */
    public function getModelsByProcess(string $processCode): array
    {
        $processCode = strtolower(trim($processCode));
        if ($processCode === '' || $processCode === '-') {
            return [];
        }

        try {
            // 1. Cari yang persis sesuai process_code (case-insensitive)
            $exact = $this->groupStart()
                          ->where('process_code', $processCode)
                          ->orWhere('process_code', strtoupper($processCode))
                          ->orWhere('LOWER(TRIM(process_code))', $processCode)
                          ->groupEnd()
                          ->orderBy('model_name', 'ASC')
                          ->findAll();

            if (!empty($exact)) {
                return $exact;
            }

            // 2. Jika belum ada (misal diakses dari Production fl18-p-db2 sedangkan model di fl18-f-fdb2),
            // cari berdasarkan kemiripan device & suffix mesinnya
            $parts = explode('-', $processCode);
            if (count($parts) >= 3) {
                $device      = $parts[0];
                $suffix      = $parts[2];
                $cleanSuffix = ltrim($suffix, 'fs'); // fdb2 -> db2

                $candidates = [
                    "{$device}-p-{$cleanSuffix}",
                    "{$device}-f-f{$cleanSuffix}",
                    "{$device}-f-{$cleanSuffix}",
                    "{$device}-s-{$cleanSuffix}",
                    "{$device}-p-f{$cleanSuffix}",
                    "{$device}-s-f{$cleanSuffix}",
                ];

                $candidatesUpper = array_map('strtoupper', $candidates);
                $allCandidates   = array_unique(array_merge($candidates, $candidatesUpper));

                return $this->whereIn('process_code', $allCandidates)
                            ->orderBy('model_name', 'ASC')
                            ->findAll();
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error in getModelsByProcess: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Auto-Register: Jika model_name belum ada pada process_code tersebut, otomatis tambahkan ke database.
     */
    public function autoRegister(string $processCode, string $modelName): ?array
    {
        $processCode = strtolower(trim($processCode));
        $modelName   = strtoupper(trim($modelName));

        if ($processCode === '' || $processCode === '-' || $modelName === '' || $modelName === '-') {
            return null;
        }

        try {
            $existing = $this->groupStart()
                             ->where('process_code', $processCode)
                             ->orWhere('process_code', strtoupper($processCode))
                             ->orWhere('LOWER(TRIM(process_code))', $processCode)
                             ->groupEnd()
                             ->where('model_name', $modelName)
                             ->first();

            if ($existing) {
                return $existing;
            }

            $this->insert([
                'process_code'   => $processCode,
                'model_name'     => $modelName,
                'standards_json' => null,
            ]);

            $newId = $this->getInsertID();
            return $this->find($newId);
        } catch (\Throwable $e) {
            log_message('error', 'Error in autoRegister model: ' . $e->getMessage());
            return null;
        }
    }
}
