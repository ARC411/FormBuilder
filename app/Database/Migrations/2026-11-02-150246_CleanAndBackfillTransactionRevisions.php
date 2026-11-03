<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanAndBackfillTransactionRevisions extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. Ambil daftar master proses beserta revisi dan nomor dokumennya
        $prosesList = $db->table('proses')->get()->getResultArray();

        foreach (['startup', 'production', 'foregoing'] as $table) {
            if (!$db->tableExists($table)) {
                continue;
            }

            // Pastikan kolom revisi ada di tabel transaksi
            if (!$db->fieldExists('revisi', $table)) {
                $this->forge->addColumn($table, [
                    'revisi' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 20,
                        'null'       => true,
                        'default'    => '0',
                    ],
                ]);
            }

            // A. Backfill dari segmen ke-4 docno jika ada (Contoh: FF-D2-003-14 -> revisi = '14')
            $db->query("
                UPDATE `{$table}`
                SET `revisi` = SUBSTRING_INDEX(`docno`, '-', -1)
                WHERE (`revisi` IS NULL OR `revisi` = '' OR `revisi` = '0' OR `revisi` = '-')
                  AND `docno` IS NOT NULL
                  AND `docno` LIKE '%-%-%-%'
                  AND SUBSTRING_INDEX(`docno`, '-', -1) REGEXP '^[0-9]+$'
            ");

            // B. Sinkronkan baris yang masih kosong berdasarkan revisi default di tabel proses
            foreach ($prosesList as $p) {
                $procCode = $p['process_code'];
                $revVal   = !empty($p['revisi']) ? (string)$p['revisi'] : '0';

                $db->table($table)
                    ->where('process', $procCode)
                    ->groupStart()
                        ->where('revisi IS NULL', null, false)
                        ->orWhere('revisi', '')
                        ->orWhere('revisi', '-')
                        ->orWhere('revisi', '0')
                    ->groupEnd()
                    ->where('created_at <', '2026-10-27 00:00:00')
                    ->update(['revisi' => $revVal]);
            }

            // C. Khusus proses SL-D2-002 (DB2) data sebelum 27 Oktober dikunci ke Revisi 3 agar tidak bocor ke Revisi 4
            $db->query("
                UPDATE `{$table}` 
                SET `revisi` = '3' 
                WHERE `process` LIKE '%db2%' 
                  AND (`revisi` IS NULL OR `revisi` = '' OR `revisi` = '0' OR `revisi` = '4')
                  AND `created_at` < '2026-10-27 00:00:00'
            ");
        }
    }

    public function down()
    {
        // Down migration dibiarkan kosong agar data stempel yang sudah dirapikan tidak kembali rusak/NULL
    }
}