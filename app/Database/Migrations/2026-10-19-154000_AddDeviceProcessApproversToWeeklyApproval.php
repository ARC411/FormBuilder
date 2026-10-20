<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeviceProcessApproversToWeeklyApproval extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('device', 'weekly_approvals')) {
            $fields['device'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'id',
            ];
        }
        if (!$this->db->fieldExists('process', 'weekly_approvals')) {
            $fields['process'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'device',
            ];
        }
        if (!$this->db->fieldExists('approver_qc', 'weekly_approvals')) {
            $fields['approver_qc'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'status_qc',
            ];
        }
        if (!$this->db->fieldExists('date_qc', 'weekly_approvals')) {
            $fields['date_qc'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approver_qc',
            ];
        }
        if (!$this->db->fieldExists('approver_production', 'weekly_approvals')) {
            $fields['approver_production'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'status_production',
            ];
        }
        if (!$this->db->fieldExists('date_production', 'weekly_approvals')) {
            $fields['date_production'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approver_production',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('weekly_approvals', $fields);
        }
    }

    public function down()
    {
        $columns = ['device', 'process', 'approver_qc', 'date_qc', 'approver_production', 'date_production'];
        foreach ($columns as $col) {
            if ($this->db->fieldExists($col, 'weekly_approvals')) {
                $this->forge->dropColumn('weekly_approvals', $col);
            }
        }
    }
}