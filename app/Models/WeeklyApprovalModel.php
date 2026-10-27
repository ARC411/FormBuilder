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
        'doc_type', 'device', 'process', 'machno', 'page_no', 'period_start', 'period_end',
        'status', 'approver', 'approver_role', 'approval_date',
        'reject_note', 'rejected_days', 'rejected_by', 'rejected_at',
        'status_qc', 'approver_qc', 'date_qc',
        'status_production', 'approver_production', 'date_production'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private static bool $schemaChecked = false;

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists($this->table)) {
                $colsToAdd = [
                    'doc_type'      => "ALTER TABLE `weekly_approvals` ADD COLUMN `doc_type` VARCHAR(50) NOT NULL DEFAULT 'startup' AFTER `id`",
                    'device'        => "ALTER TABLE `weekly_approvals` ADD COLUMN `device` VARCHAR(50) NULL AFTER `doc_type`",
                    'process'       => "ALTER TABLE `weekly_approvals` ADD COLUMN `process` VARCHAR(50) NULL AFTER `device`",
                    'machno'        => "ALTER TABLE `weekly_approvals` ADD COLUMN `machno` VARCHAR(50) NULL AFTER `process`",
                    'page_no'       => "ALTER TABLE `weekly_approvals` ADD COLUMN `page_no` INT(11) NULL DEFAULT 1 AFTER `machno`",
                    'status'        => "ALTER TABLE `weekly_approvals` ADD COLUMN `status` VARCHAR(50) NULL DEFAULT 'Pending' AFTER `period_end`",
                    'approver'      => "ALTER TABLE `weekly_approvals` ADD COLUMN `approver` VARCHAR(100) NULL AFTER `status`",
                    'approver_role' => "ALTER TABLE `weekly_approvals` ADD COLUMN `approver_role` VARCHAR(50) NULL AFTER `approver`",
                    'approval_date' => "ALTER TABLE `weekly_approvals` ADD COLUMN `approval_date` DATETIME NULL AFTER `approver_role`",
                    'reject_note'   => "ALTER TABLE `weekly_approvals` ADD COLUMN `reject_note` TEXT NULL DEFAULT NULL AFTER `approval_date`",
                    'rejected_days' => "ALTER TABLE `weekly_approvals` ADD COLUMN `rejected_days` VARCHAR(255) NULL DEFAULT NULL AFTER `reject_note`",
                    'rejected_by'   => "ALTER TABLE `weekly_approvals` ADD COLUMN `rejected_by` VARCHAR(100) NULL DEFAULT NULL AFTER `rejected_days`",
                    'rejected_at'   => "ALTER TABLE `weekly_approvals` ADD COLUMN `rejected_at` DATETIME NULL DEFAULT NULL AFTER `rejected_by`",
                ];
                foreach ($colsToAdd as $col => $sql) {
                    if (!$db->fieldExists($col, $this->table)) {
                        $db->query($sql);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika kolom sudah ada
        }
    }

    // Mengambil data mingguan STARTUP + total dokumen startup + jumlah checksheet harian yang belum di-approve
    public function getWeeklyList()
    {
        return $this->db->table($this->table)
            ->select('weekly_approvals.*, 
                      COUNT(DISTINCT CASE WHEN startup.par000 = "1" THEN CONCAT(COALESCE(startup.device, proses.device, ""), "___", COALESCE(startup.process, ""), "___", COALESCE(startup.machno, "")) END) as total_docs,
                      COUNT(DISTINCT CASE WHEN startup.par000 = "1" THEN COALESCE(startup.device, proses.device) END) as total_devices,
                      GROUP_CONCAT(DISTINCT CASE WHEN startup.par000 = "1" THEN COALESCE(device.name, startup.device) END ORDER BY device.name SEPARATOR " &bull; ") as device_names,
                      SUM(CASE WHEN startup.par000 = "1" AND (startup.status = "Pending" OR startup.status = "Reject" OR startup.status IS NULL OR startup.status = "" OR startup.status = "-") THEN 1 ELSE 0 END) as pending_daily,
                      SUM(CASE WHEN startup.par000 = "1" AND startup.status = "Reject" THEN 1 ELSE 0 END) as rejected_daily')
            ->join('startup', 'startup.weekly_id = weekly_approvals.id', 'left')
            ->join('proses', 'proses.process_code = startup.process', 'left')
            ->join('device', 'device.code = COALESCE(startup.device, proses.device)', 'left')
            ->groupStart()
                ->where('weekly_approvals.doc_type', 'startup')
                ->orWhere('weekly_approvals.doc_type IS NULL')
                ->orWhere('weekly_approvals.doc_type', '')
            ->groupEnd()
            ->groupBy('weekly_approvals.id')
            ->orderBy('weekly_approvals.period_start', 'DESC')
            ->orderBy('weekly_approvals.id', 'DESC')
            ->get()->getResultArray();
    }

    public function getProductionBatchList($sortDir = 'desc')
    {
        $direction = strtolower((string)$sortDir) === 'asc' ? 'ASC' : 'DESC';

        $builder = $this->db->table($this->table)
            ->select('weekly_approvals.*, 
                      device.name as device_name, 
                      proses.name as process_name, 
                      COUNT(DISTINCT CASE WHEN production.par001 = 1 THEN production.number END) as total_docs')
            ->join('production', 'production.weekly_id = weekly_approvals.id', 'left')
            ->join('proses', 'proses.process_code = weekly_approvals.process', 'left')
            ->join('device', 'device.code = COALESCE(weekly_approvals.device, proses.device)', 'left')
            ->where('weekly_approvals.doc_type', 'production')
            ->groupBy(['weekly_approvals.id', 'device.name', 'proses.name']);

        if ($direction === 'ASC') {
            $builder->orderBy('weekly_approvals.period_start', 'ASC')
                    ->orderBy('weekly_approvals.period_end', 'ASC')
                    ->orderBy('weekly_approvals.id', 'ASC');
        } else {
            $builder->orderBy('weekly_approvals.period_end', 'DESC')
                    ->orderBy('weekly_approvals.period_start', 'DESC')
                    ->orderBy('weekly_approvals.id', 'DESC');
        }

        return $builder->get()->getResultArray();
    }

    public function getForegoingBatchList()
    {
        return $this->db->table($this->table)
            ->select('weekly_approvals.*, 
                      device.name as device_name, 
                      proses.name as process_name, 
                      COUNT(DISTINCT CASE WHEN foregoing.par000 = "1" THEN foregoing.number END) as total_docs')
            ->join('foregoing', 'foregoing.weekly_id = weekly_approvals.id', 'left')
            ->join('proses', 'proses.process_code = weekly_approvals.process', 'left')
            ->join('device', 'device.code = COALESCE(weekly_approvals.device, proses.device)', 'left')
            ->where('weekly_approvals.doc_type', 'foregoing')
            ->groupBy(['weekly_approvals.id', 'device.name', 'proses.name'])
            ->orderBy('weekly_approvals.id', 'DESC')
            ->get()->getResultArray();
    }

    // Auto-sync checksheet production yang belum memiliki weekly_id menjadi batch 20 data (1 lembar penuh)
    // Dibatasi dengan limit (default 500) agar eksekusi cepat dan tidak memicu 504 Timeout
    public function syncProductionBatches($limit = 500)
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('weekly_id', 'production') || !$db->fieldExists('doc_type', $this->table)) {
                return 0;
            }

            // Ambil entri production unik yang belum masuk batch lembar
            $unassigned = $db->table('production')
                ->select('number, device, process, machno, created_at')
                ->where('par001', 1)
                ->groupStart()
                    ->where('weekly_id', null)
                    ->orWhere('weekly_id', 0)
                ->groupEnd()
                ->orderBy('created_at', 'ASC')
                ->orderBy('number', 'ASC')
                ->limit((int)$limit)
                ->get()->getResultArray();

            if (empty($unassigned)) {
                return 0;
            }

            // Kelompokkan data berdasarkan kombinasi device, process, machno
            $grouped = [];
            foreach ($unassigned as $row) {
                $dev  = trim((string)$row['device']);
                $proc = trim((string)$row['process']);
                $mach = trim((string)$row['machno']);
                $key  = $dev . '___' . $proc . '___' . $mach;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'device'  => $dev,
                        'process' => $proc,
                        'machno'  => $mach,
                        'rows'    => []
                    ];
                }
                $grouped[$key]['rows'][] = $row;
            }

            $syncedCount = 0;

            foreach ($grouped as $g) {
                $dev  = $g['device'];
                $proc = $g['process'];
                $mach = $g['machno'];
                $rows = $g['rows'];

                // 1. Cek apakah ada batch yang belum penuh (< 20 data) dan statusnya masih Pending
                $openBatch = $db->table($this->table)
                    ->where('doc_type', 'production')
                    ->where('device', $dev)
                    ->where('process', $proc)
                    ->where('machno', $mach)
                    ->where('status', 'Pending')
                    ->orderBy('page_no', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get()->getRowArray();

                if ($openBatch) {
                    $currCount = $db->table('production')
                        ->where('weekly_id', $openBatch['id'])
                        ->where('par001', 1)
                        ->countAllResults();

                    if ($currCount < 20) {
                        $needed = 20 - $currCount;
                        $fillRows = array_splice($rows, 0, $needed);
                        $fillNumbers = array_column($fillRows, 'number');

                        if (!empty($fillNumbers)) {
                            $db->table('production')
                                ->whereIn('number', $fillNumbers)
                                ->update(['weekly_id' => $openBatch['id']]);

                            $syncedCount += count($fillNumbers);

                            $lastDate = end($fillRows)['created_at'];
                            $db->table($this->table)
                                ->where('id', $openBatch['id'])
                                ->update([
                                    'period_end' => date('Y-m-d', strtotime($lastDate)),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                }

                // 2. Jika masih ada sisa data, buat batch baru dengan pembagian tepat 20 data per lembar
                if (!empty($rows)) {
                    $lastBatch = $db->table($this->table)
                        ->select('MAX(page_no) as max_page')
                        ->where('doc_type', 'production')
                        ->where('device', $dev)
                        ->where('process', $proc)
                        ->where('machno', $mach)
                        ->get()->getRowArray();

                    $nextPage = !empty($lastBatch['max_page']) ? ((int)$lastBatch['max_page'] + 1) : 1;

                    $chunks = array_chunk($rows, 20);
                    foreach ($chunks as $chunk) {
                        $chunkNumbers = array_column($chunk, 'number');
                        $startDate = date('Y-m-d', strtotime($chunk[0]['created_at']));
                        $endDate   = date('Y-m-d', strtotime(end($chunk)['created_at']));

                        $newBatchData = [
                            'doc_type'          => 'production',
                            'device'            => $dev,
                            'process'           => $proc,
                            'machno'            => $mach,
                            'page_no'           => $nextPage,
                            'period_start'      => $startDate,
                            'period_end'        => $endDate,
                            'status'            => 'Pending',
                            'status_qc'         => 'Pending',
                            'status_production' => 'Pending',
                            'created_at'        => date('Y-m-d H:i:s'),
                            'updated_at'        => date('Y-m-d H:i:s')
                        ];

                        $db->table($this->table)->insert($newBatchData);
                        $newBatchId = $db->insertID();

                        $db->table('production')
                            ->whereIn('number', $chunkNumbers)
                            ->update(['weekly_id' => $newBatchId]);

                        $syncedCount += count($chunkNumbers);
                        $nextPage++;
                    }
                }
            }

            return $syncedCount;
        } catch (\Throwable $e) {
            log_message('error', 'syncProductionBatches gagal: ' . $e->getMessage());
            return 0;
        }
    }
}
