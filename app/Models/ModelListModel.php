<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelListModel extends Model
{
    protected $table         = 'model_list';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['process_code', 'model_name', 'standards_json', 'created_at', 'updated_at'];

    /**
     * Ambil daftar model berdasarkan process_code.
     * Mendukung pencarian lintas tipe (misal fl18-p-db2 <-> fl18-f-fdb2) jika di proses tersebut belum ada.
     */
    public function getModelsByProcess(string $processCode): array
    {
        $processCode = strtolower(trim($processCode));
        if ($processCode === '' || $processCode === '-') {
            return [];
        }

        // 1. Cari yang persis sesuai process_code terlebih dahulu
        $exact = $this->where('process_code', $processCode)
                      ->orderBy('model_name', 'ASC')
                      ->findAll();
        if (!empty($exact)) {
            return $exact;
        }

        // 2. Jika kosong (misal dibuka dari Production fl18-p-db2 sedangkan model terdaftar di fl18-f-fdb2),
        // cari berdasarkan kemiripan kode mesinnya
        $parts = explode('-', $processCode);
        if (count($parts) >= 3) {
            $device = $parts[0];
            $suffix = $parts[2]; // misal db2 atau fdb2
            $cleanSuffix = ltrim($suffix, 'fs'); // fdb2 -> db2

            $candidates = [
                "{$device}-p-{$cleanSuffix}",
                "{$device}-f-f{$cleanSuffix}",
                "{$device}-f-{$cleanSuffix}",
                "{$device}-s-{$cleanSuffix}",
            ];

            return $this->whereIn('process_code', array_unique($candidates))
                        ->orderBy('model_name', 'ASC')
                        ->findAll();
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

        $existing = $this->where('process_code', $processCode)
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
    }
}