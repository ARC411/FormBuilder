<?php

namespace App\Models;

use CodeIgniter\Model;

class WeeklyApprovalModel extends Model
{
    protected $table            = 'weekly_approvals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    
    protected $allowedFields    = [
        'device', 'process', 'period_start', 'period_end',
        'status_qc', 'approver_qc', 'date_qc',
        'status_production', 'approver_production', 'date_production'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $db = \Config\Database::connect();
        if ($db->tableExists($this->table) && !$db->fieldExists('device', $this->table)) {
            $forge = \Config\Database::forge();
            $fields = [
                'device' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'id',
                ],
                'process' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'device',
                ],
                'approver_qc' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'status_qc',
                ],
                'date_qc' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'approver_qc',
                ],
                'approver_production' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'status_production',
                ],
                'date_production' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'approver_production',
                ],
            ];
            $forge->addColumn($this->table, $fields);
        }
    }

    // Mengambil data mingguan + total dokumen startup + jumlah checksheet harian yang belum di-approve
    public function getWeeklyList()
    {
        return $this->db->table($this->table)
            ->select('weekly_approvals.*, 
                      device.name as device_name, 
                      proses.name as process_name, 
                      COUNT(DISTINCT CASE WHEN startup.par000 = "1" THEN startup.id END) as total_docs,
                      SUM(CASE WHEN startup.par000 = "1" AND (startup.status = "Pending" OR startup.status IS NULL OR startup.status = "" OR startup.status = "-") THEN 1 ELSE 0 END) as pending_daily')
            ->join('startup', 'startup.weekly_id = weekly_approvals.id', 'left')
            ->join('proses', 'proses.process_code = weekly_approvals.process', 'left')
            ->join('device', 'device.code = weekly_approvals.device', 'left')
            ->groupBy('weekly_approvals.id')
            ->orderBy('weekly_approvals.period_start', 'DESC')
            ->orderBy('weekly_approvals.id', 'DESC')
            ->get()->getResultArray();
    }
}