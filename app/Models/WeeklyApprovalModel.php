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
        'period_start', 'period_end', 'status_qc', 'status_production'
    ];

    // Fungsi baru untuk mengambil data mingguan + jumlah dokumen startup di dalamnya
    public function getWeeklyList()
    {
        return $this->db->table($this->table)
            ->select('weekly_approvals.*, COUNT(startup.id) as total_docs')
            ->join('startup', 'startup.weekly_id = weekly_approvals.id', 'left')
            ->groupBy('weekly_approvals.id')
            ->orderBy('weekly_approvals.id', 'DESC')
            ->get()->getResultArray();
    }
}