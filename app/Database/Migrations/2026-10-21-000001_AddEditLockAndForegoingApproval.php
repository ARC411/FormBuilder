<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditLockAndForegoingApproval extends Migration
{
    public function up()
    {
        // 1. Tambah kolom pada tabel foregoing
        $foregoingFields = [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'shift',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'Pending',
                'after'      => 'revisi',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'status',
            ],
            'foreman' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'role',
            ],
            'leader' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'foreman',
            ],
            'supervisor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'leader',
            ],
            'edit_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '0=Locked, 1=Requested, 2=Unlocked',
                'after'      => 'supervisor',
            ],
            'unlocked_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'edit_status',
            ],
        ];

        foreach ($foregoingFields as $colName => $colDef) {
            if (!$this->db->fieldExists($colName, 'foregoing')) {
                $this->forge->addColumn('foregoing', [$colName => $colDef]);
            }
        }

        // 2. Tambah kolom edit_status & unlocked_by pada tabel startup
        $startupFields = [
            'edit_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '0=Locked, 1=Requested, 2=Unlocked',
                'after'      => 'revisi',
            ],
            'unlocked_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'edit_status',
            ],
        ];

        foreach ($startupFields as $colName => $colDef) {
            if (!$this->db->fieldExists($colName, 'startup')) {
                $this->forge->addColumn('startup', [$colName => $colDef]);
            }
        }
    }

    public function down()
    {
        $foregoingCols = ['name', 'status', 'role', 'foreman', 'leader', 'supervisor', 'edit_status', 'unlocked_by'];
        foreach ($foregoingCols as $col) {
            if ($this->db->fieldExists($col, 'foregoing')) {
                $this->forge->dropColumn('foregoing', $col);
            }
        }

        $startupCols = ['edit_status', 'unlocked_by'];
        foreach ($startupCols as $col) {
            if ($this->db->fieldExists($col, 'startup')) {
                $this->forge->dropColumn('startup', $col);
            }
        }
    }
}