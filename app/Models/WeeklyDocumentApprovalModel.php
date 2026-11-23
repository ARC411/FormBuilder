<?php

namespace App\Models;

use CodeIgniter\Model;

class WeeklyDocumentApprovalModel extends Model
{
    protected $table            = 'weekly_document_approvals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields    = [
        'weekly_id', 'device', 'process', 'machno',
        'status_qc', 'approver_qc', 'date_qc',
        'status_production', 'approver_production', 'date_production',
        'created_at', 'updated_at'
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
            $sql = "CREATE TABLE IF NOT EXISTS `weekly_document_approvals` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `weekly_id` INT(11) NOT NULL,
                `device` VARCHAR(50) NOT NULL,
                `process` VARCHAR(50) NOT NULL,
                `machno` VARCHAR(50) NOT NULL DEFAULT '',
                `status_qc` VARCHAR(50) NOT NULL DEFAULT 'Pending',
                `approver_qc` VARCHAR(100) NULL DEFAULT NULL,
                `date_qc` DATETIME NULL DEFAULT NULL,
                `status_production` VARCHAR(50) NOT NULL DEFAULT 'Pending',
                `approver_production` VARCHAR(100) NULL DEFAULT NULL,
                `date_production` DATETIME NULL DEFAULT NULL,
                `created_at` DATETIME NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_weekly_doc` (`weekly_id`, `device`, `process`, `machno`),
                KEY `idx_weekly_id` (`weekly_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $db->query($sql);
        } catch (\Throwable $e) {
            log_message('error', 'WeeklyDocumentApprovalModel::ensureSchema error: ' . $e->getMessage());
        }
    }

    /**
     * Ambil data approval spesifik satu dokumen
     */
    public function getDocApproval($weeklyId, $device, $process, $machno)
    {
        $machnoClean = trim((string)$machno);
        return $this->where('weekly_id', (int)$weeklyId)
            ->where('device', trim((string)$device))
            ->where('process', trim((string)$process))
            ->where('machno', $machnoClean)
            ->first();
    }

    /**
     * Ambil seluruh data approval per-dokumen pada suatu weekly_id
     * Return associative array keyed by "device___process___machno"
     */
    public function getGroupedByWeeklyId($weeklyId)
    {
        $rows = $this->where('weekly_id', (int)$weeklyId)->findAll();
        $map = [];
        foreach ($rows as $r) {
            $key = trim((string)$r['device']) . '___' . trim((string)$r['process']) . '___' . trim((string)$r['machno']);
            $map[$key] = $r;
        }
        return $map;
    }
}

