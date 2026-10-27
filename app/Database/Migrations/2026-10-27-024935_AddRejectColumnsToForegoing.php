<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectColumnsToForegoing extends Migration
{
    public function up()
    {
        $fields = [
            'reject_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'reject_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'reject_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        // Cek dulu apakah kolom 'reject_reason' sudah ada di tabel 'foregoing'
        if (! $this->db->fieldExists('reject_reason', 'foregoing')) {
            $this->forge->addColumn('foregoing', $fields);
        }
    }

    public function down()
    {
        // Cek dulu apakah kolomnya ada sebelum dihapus saat rollback
        if ($this->db->fieldExists('reject_reason', 'foregoing')) {
            $this->forge->dropColumn('foregoing', ['reject_reason', 'reject_by', 'reject_date']);
        }
    }
}