<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyStartupColumnsCapacity extends Migration
{
    public function up()
    {
        // 1. Memperbesar kapasitas kolom dari VARCHAR(11) menjadi VARCHAR(30)
        // Tujuan: Agar cukup menampung angka beserta teks satuannya (Contoh: "0.005 kgf/cm^2")
        $fields = [
            'par015' => ['type' => 'VARCHAR', 'constraint' => 30],
            'par016' => ['type' => 'VARCHAR', 'constraint' => 30],
            'par018' => ['type' => 'VARCHAR', 'constraint' => 30],
            'par019' => ['type' => 'VARCHAR', 'constraint' => 30],
            'par020' => ['type' => 'VARCHAR', 'constraint' => 30],
            'par021' => ['type' => 'VARCHAR', 'constraint' => 30],
        ];

        $this->forge->modifyColumn('startup', $fields);

        // ==============================================================================
        // 2. CLEAN UP (OPSIONAL)
        // Jika sebelumnya abang sempat terlanjur mengeksekusi query Skenario A 
        // (menambah kolom par015_unit dst) langsung di phpMyAdmin, 
        // hapus tanda komentar (//) pada baris di bawah ini agar kolom tersebut dihapus otomatis.
        // ==============================================================================
        // $this->forge->dropColumn('startup', ['par015_unit', 'par016_unit', 'par018_unit', 'par019_unit', 'par020_unit', 'par021_unit']);
    }

    public function down()
    {
        // Kembalikan ke pengaturan awal jika sewaktu-waktu di-rollback (VARCHAR 11)
        $fields = [
            'par015' => ['type' => 'VARCHAR', 'constraint' => 11],
            'par016' => ['type' => 'VARCHAR', 'constraint' => 11],
            'par018' => ['type' => 'VARCHAR', 'constraint' => 11],
            'par019' => ['type' => 'VARCHAR', 'constraint' => 11],
            'par020' => ['type' => 'VARCHAR', 'constraint' => 11],
            'par021' => ['type' => 'VARCHAR', 'constraint' => 11],
        ];

        $this->forge->modifyColumn('startup', $fields);
    }
}