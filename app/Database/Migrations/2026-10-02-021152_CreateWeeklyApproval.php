<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWeeklyApproval extends Migration
{
    public function up()
    {
        // 1. Membuat tabel weekly_approvals
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'period_start' => [
                'type' => 'DATE',
            ],
            'period_end' => [
                'type' => 'DATE',
            ],
            'status_qc' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pending', // Pending, Approved, Rejected
            ],
            'status_production' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('weekly_approvals', TRUE);

        // 2. Menambahkan kolom weekly_id pada tabel startup
        if (! $this->db->fieldExists('weekly_id', 'startup')) {
            $fields = [
                'weekly_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'status' 
                ],
            ];
            $this->forge->addColumn('startup', $fields);
        }
    } // <--- Nah, kurung ini nih yang tadi kehapus Bang!

    public function down()
    {
        // Rollback jika terjadi kesalahan
        $this->forge->dropColumn('startup', 'weekly_id');
        $this->forge->dropTable('weekly_approvals');
    }
}