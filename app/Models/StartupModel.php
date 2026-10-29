<?php

namespace App\Models;

use CodeIgniter\Model;

class StartupModel extends Model
{
    protected $table = 'startup';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'number','idcsheet','device','process','lotno','model','machno',
        'empid','group','shift','par000','par001','par002','par003','par004',
        'par005','par006','par007','par008','par009','par010','par011','par012',
        'par013','par014','par015','par016','par017','par018','par019','par020',
        'par021','par022','par023','par024','par025','par026','par027','par028',
        'par029','par030','par031','par032','par033','par034','par035','par036',
        'par037','par038','par039','par040','par041','par042','par043','par044',
        'status','role','foreman','leader','supervisor','name', 'docno', 'revisi',
        'par015_unit', 'par016_unit', 'par018_unit', 'par019_unit', 'par020_unit', 
        'par021_unit', 'weekly_id', 'edit_status', 'unlocked_by', 'reject_reason', 'reject_by', 'reject_date'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('reject_reason', 'startup')) {
                $db->query("ALTER TABLE `startup` 
                    ADD COLUMN `reject_reason` TEXT NULL AFTER `status`,
                    ADD COLUMN `reject_by` VARCHAR(100) NULL AFTER `reject_reason`,
                    ADD COLUMN `reject_date` DATETIME NULL AFTER `reject_by`");
            }
            if (!$db->fieldExists('edit_status', 'startup')) {
                $db->query("ALTER TABLE `startup` 
                    ADD COLUMN `edit_status` TINYINT(1) NOT NULL DEFAULT 0 AFTER `revisi`,
                    ADD COLUMN `unlocked_by` VARCHAR(100) NULL DEFAULT NULL AFTER `edit_status`");
            }
        } catch (\Throwable $e) {
            // Abaikan jika kolom sudah ada
        }
    }

    public function getCsheet($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }
        return $this->where(
            ['slug' => $slug]
        )->first();
    }

    public function getRejectedCountByOperator(
        $empid = '',
        $name = ''
    ) {
        $builder = $this->db->table(
            $this->table
        );
        $builder->where('status', 'Reject');
        $builder->where('par000', '1');

        $cleanEmp = trim((string) $empid);
        $cleanName = trim((string) $name);
        $hasEmp = !empty($cleanEmp);
        $hasName = !empty($cleanName);

        if ($hasEmp) {
            $builder->groupStart();
            $builder->where('empid', $cleanEmp);
            if ($hasName) {
                $builder->orWhere('name', $cleanName);
            }
            $builder->groupEnd();
        } elseif ($hasName) {
            $builder->where('name', $cleanName);
        }

        return $builder->countAllResults();
    }

    public function getAllData(
        $empid = null,
        $name = null
    ) {
        $whereEmp = "";
        $conds = [];

        if (!empty($empid)) {
            $escEmp = $this->db->escape(
                trim((string) $empid)
            );
            $conds[] = "TRIM(s.empid) = " . $escEmp;
        }
        if (!empty($name)) {
            $escName = $this->db->escape(
                trim((string) $name)
            );
            $conds[] = "TRIM(s.name) = " . $escName;
        }
        if (!empty($conds)) {
            $whereEmp = " AND (" . implode(" OR ", $conds) . ")";
        }

        $query = "SELECT DISTINCT s.id, s.`number`, s.created_at, s.`process`, d.name as device_name, 
                  s.`machno`, s.`model`, s.`lotno`, s.`empid`, s.`shift`, s.`group`, s.`status`, s.`role`, 
                  s.`foreman`, s.`leader`, s.`supervisor`, s.name, p.name as process_name, 
                  s.edit_status, s.unlocked_by, s.reject_reason, s.reject_by, s.reject_date
                  FROM `startup` as s
                  INNER JOIN `proses` as p ON s.process = p.process_code 
                  INNER JOIN `device` as d ON p.device = d.code
                  WHERE s.`par000` = '1' "
                  . $whereEmp
                  . " ORDER BY CASE WHEN s.status = 'Reject' THEN 0 ELSE 1 END, s.created_at DESC";

        return $this->query(
            $query
        )->getResultArray();
    }

    public function markRejectRequested($number)
    {
        $builder = $this->db->table(
            $this->table
        );
        return $builder
            ->where('number', (int) $number)
            ->update(['edit_status' => 1]);
    }

    public function updateApproval(
        $number,
        $name,
        $level,
        $action = 'Approve',
        $rejectReason = ''
    ) {
        $data = $this->where(
            'number',
            $number
        )->first();

        if (!$data) {
            return false;
        }

        $curStatus = (string) ($data['status'] ?? '');
        $allowedStatuses = ['Pending', 'Reject', '', '-'];
        if (!in_array($curStatus, $allowedStatuses, true)) {
            return false;
        }

        $updateData = [];

        if ($action === 'Reject') {
            $roleName = 'foreman';
            if ($level == 10) {
                $roleName = 'leader';
            } elseif ($level <= 7 && $level > 0) {
                $roleName = 'supervisor';
            }

            $updateData['status'] = 'Reject';
            $updateData['role'] = $roleName;
            $updateData['reject_reason'] = $rejectReason;
            $updateData['reject_by'] = $name;
            $updateData['reject_date'] = date('Y-m-d H:i:s');
            $updateData['edit_status'] = 0;
            $updateData['foreman'] = ($roleName === 'foreman') ? $name : null;
            $updateData['leader'] = ($roleName === 'leader') ? $name : null;
            $updateData['supervisor'] = ($roleName === 'supervisor') ? $name : null;

            return $this->where(
                'number',
                $number
            )->set(
                $updateData
            )->update();
        }

        $updateData['reject_reason'] = null;
        $updateData['reject_by'] = null;
        $updateData['reject_date'] = null;
        $updateData['edit_status'] = 0;

        if ($level == 14) {
            $updateData['status'] = "Approve by Foreman";
            $updateData['role'] = "foreman";
            $updateData['foreman'] = $name;
        } elseif ($level == 10) {
            $updateData['role'] = "leader";
            if (empty($data['foreman'])) {
                $updateData['status'] = "Bypass Leader";
                $updateData['foreman'] = $name;
                $updateData['leader'] = $name;
            } else {
                $updateData['status'] = "Approve Leader";
                $updateData['leader'] = $name;
            }
        } elseif ($level <= 7 && $level > 0) {
            $updateData['role'] = "supervisor";
            $updateData['supervisor'] = $name;

            if (!empty($data['leader'])) {
                $updateData['status'] = "Approve Supervisor";
            } elseif (!empty($data['foreman'])) {
                $updateData['status'] = "Bypass Supervisor";
                $updateData['leader'] = $name;
            } else {
                $updateData['status'] = "Bypass Supervisor";
                $updateData['foreman'] = $name;
                $updateData['leader'] = $name;
            }
        }

        return $this->where(
            'number',
            $number
        )->set(
            $updateData
        )->update();
    }

    public function addProses($data)
    {
        $db = \Config\Database::connect();
        $procCode = $data['process'];
        $prosesMaster = $db->table('proses')
            ->where('process_code', $procCode)
            ->get()
            ->getRowArray();

        $latestRevRow = $db->table('form_revisions')
            ->where('process_code', $procCode)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $activeRevisi = $data['revisi'] ?? ($latestRevRow['revisi'] ?? ($prosesMaster['revisi'] ?? '0'));

        if ($prosesMaster) {
            $data['par041'] = (string)$activeRevisi;
            $data['par042'] = $prosesMaster['berlaku'];
            $data['docno']  = $prosesMaster['docno'];
        }
        $data['revisi'] = (string)$activeRevisi;

        $numVal  = (int)$data['number'];
        $par0Val = (int)$data['par000'];
        $query   = "SELECT * FROM startup WHERE `number` = " . $numVal . " AND `par000` = " . $par0Val;
        $result  = $this->query($query)->getRow();

        if ($result == null) {
            $data['edit_status'] = 0;
            $this->insert($data);
            return "Add Startup";
        } else {
            // Pertahankan revisi asli saat dokumen lama diedit
            if (!empty($result->revisi) && $result->revisi !== '-') {
                $data['revisi'] = $result->revisi;
                $data['par041'] = $result->revisi;
            }
            if (($result->status ?? '') === 'Reject') {
                $data['status']        = 'Pending';
                $data['reject_reason'] = null;
                $data['reject_by']     = null;
                $data['reject_date']   = null;
            }
            $data['edit_status'] = 0;
            $this->update($result->id, $data);
            return "Update Startup";
        }
    }

    public function getByWeeklyId(
        $weekly_id,
        $periodStart = null,
        $periodEnd = null
    ) {
        $escWeekly = $this->db->escape(
            $weekly_id
        );
        $whereWeekly = "s.weekly_id = " . $escWeekly;

        if (!empty($periodStart)) {
            if (!empty($periodEnd)) {
                $tsStart = strtotime($periodStart);
                $tsEnd = strtotime($periodEnd);
                $startStr = date('Y-m-d', $tsStart) . ' 00:00:00';
                $endStr = date('Y-m-d', $tsEnd) . ' 23:59:59';
                $whereWeekly = "(s.weekly_id = "
                    . $escWeekly
                    . " OR (s.created_at >= '"
                    . $startStr
                    . "' AND s.created_at <= '"
                    . $endStr
                    . "'))";
            }
        }

        $sql = "SELECT DISTINCT s.id, s.number, s.created_at, s.process, s.device, COALESCE(s.device, p.device) as final_device, d.name as device_name, s.machno, s.model, s.lotno, s.empid, s.shift, s.group, s.status, s.role, s.foreman, s.leader, s.supervisor, p.name as process_name, s.reject_reason, s.reject_by, s.reject_date
        FROM `startup` as s
        INNER JOIN `proses` as p ON s.process = p.process_code 
        INNER JOIN `device` as d ON p.device = d.code
        WHERE "
        . $whereWeekly
        . " AND s.par000 = '1'
        ORDER BY s.created_at ASC, s.id ASC";

        return $this->query(
            $sql
        )->getResultArray();
    }

    public function getWeeklyChecksheetData(
        $weeklyId = null,
        $device = '',
        $process = '',
        $machno = '',
        $periodStart = '',
        $periodEnd = ''
    ) {
        $sql = "SELECT DISTINCT s.*, p.device as p_device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name
                FROM `startup` as s
                INNER JOIN `proses` as p ON s.process = p.process_code 
                INNER JOIN `device` as d ON p.device = d.code
                WHERE 1=1";

        if (!empty($process)) {
            $escProc = $this->db->escape(
                $process
            );
            $sql .= " AND s.process = " . $escProc;
        }

        if (!empty($device)) {
            $escDev = $this->db->escape(
                $device
            );
            $sql .= " AND (s.device = "
                . $escDev
                . " OR p.device = "
                . $escDev
                . ")";
        }

        $hasWeekly = !empty($weeklyId);
        $hasStart = !empty($periodStart);
        $hasEnd = !empty($periodEnd);

        if ($hasWeekly && $hasStart && $hasEnd) {
            $escW = $this->db->escape(
                $weeklyId
            );
            $tsS = strtotime($periodStart);
            $tsE = strtotime($periodEnd);
            $startStr = date('Y-m-d', $tsS) . ' 00:00:00';
            $endStr = date('Y-m-d', $tsE) . ' 23:59:59';
            $sql .= " AND (s.weekly_id = "
                . $escW
                . " OR (s.created_at >= '"
                . $startStr
                . "' AND s.created_at <= '"
                . $endStr
                . "'))";
        } elseif ($hasWeekly) {
            $escW = $this->db->escape(
                $weeklyId
            );
            $sql .= " AND s.weekly_id = " . $escW;
        } elseif ($hasStart && $hasEnd) {
            $tsS = strtotime($periodStart);
            $tsE = strtotime($periodEnd);
            $startStr = date('Y-m-d', $tsS) . ' 00:00:00';
            $endStr = date('Y-m-d', $tsE) . ' 23:59:59';
            $sql .= " AND s.created_at >= '"
                . $startStr
                . "' AND s.created_at <= '"
                . $endStr
                . "'";
        }

        $ignoreMach = ['', 'ALL', 'null', '-'];
        if (!in_array((string) $machno, $ignoreMach, true)) {
            $escMach = $this->db->escape(
                $machno
            );
            $sql .= " AND s.machno = " . $escMach;
        } elseif ($machno === '-') {
            $sql .= " AND (s.machno = '' OR s.machno IS NULL OR s.machno = '-')";
        }

        $sql .= " ORDER BY s.created_at ASC, s.id ASC";

        return $this->query(
            $sql
        )->getResultArray();
    }

    public function getLatestId()
    {
        $query = "SELECT `number` FROM startup ORDER BY `number` DESC LIMIT 1 ";
        $result = $this->query(
            $query
        );
        if ($result == null) {
            return 0;
        }
        $row = $result->getRow();
        return $row->number ?? 0;
    }

    public function getAll(
        $dateStart = '1970-01-01',
        $dateEnd = '2070-01-01',
        $device = '',
        $process = '',
        $model = '',
        $lotno = '',
        $machno = ''
    ) {
        if (empty($dateStart)) {
            $startStr = '1970-01-01 00:00:00';
        } else {
            $tsS = strtotime($dateStart);
            $startStr = ($tsS ? date('Y-m-d', $tsS) : '1970-01-01') . ' 00:00:00';
        }

        if (empty($dateEnd)) {
            $endStr = '2070-01-01 23:59:59';
        } else {
            $tsE = strtotime($dateEnd);
            $endStr = ($tsE ? date('Y-m-d', $tsE) : '2070-01-01') . ' 23:59:59';
        }

        $sql = "SELECT DISTINCT s.*, p.device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name
                FROM `startup` as s
                INNER JOIN `proses` as p ON s.process = p.process_code 
                INNER JOIN `device` as d ON p.device = d.code
                WHERE s.created_at >= '"
                . $startStr
                . "' AND s.created_at <= '"
                . $endStr
                . "' AND (s.device LIKE '%"
                . $device
                . "%' OR p.device LIKE '%"
                . $device
                . "%') AND s.process LIKE '%"
                . $process
                . "%'";

        if ($model !== '') {
            $sql .= " AND s.model LIKE '%" . $model . "%'";
        }
        if ($lotno !== '') {
            $sql .= " AND s.lotno LIKE '%" . $lotno . "%'";
        }

        $ignoreMach = ['', 'ALL', 'null', '-', 'undefined'];
        if (!in_array(trim((string) $machno), $ignoreMach, true)) {
            $sql .= " AND s.machno = '" . $machno . "'";
        }

        $sql .= " ORDER BY s.created_at ASC";

        return $this->query(
            $sql
        )->getResultArray();
    }

    public function deleteByNumber($number)
    {
        $query = "DELETE FROM `startup` WHERE `number` = ?";
        $result = $this->query(
            $query,
            [(int) $number]
        );
        return $result ? "Berhasil" : "Gagal";
    }

    public function getApprovalData(
        $dateStart = '',
        $dateEnd = '',
        $device = '',
        $process = ''
    ) {
        if (empty($dateStart)) {
            $start = '1970-01-01 00:00:00';
        } else {
            $tsS = strtotime($dateStart);
            $start = ($tsS ? date('Y-m-d', $tsS) : '1970-01-01') . ' 00:00:00';
        }

        if (empty($dateEnd)) {
            $end = date('Y-m-d') . ' 23:59:59';
        } else {
            $tsE = strtotime($dateEnd);
            $end = ($tsE ? date('Y-m-d', $tsE) : date('Y-m-d')) . ' 23:59:59';
        }

        $query = "SELECT DISTINCT s.*, p.device as proc_device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name
        FROM `startup` as s
        INNER JOIN `proses` as p ON s.process = p.process_code 
        INNER JOIN `device` as d ON p.device = d.code
        WHERE s.created_at >= '"
        . $start
        . "' AND s.created_at <= '"
        . $end
        . "' AND (s.device LIKE '%"
        . $device
        . "%' OR p.device LIKE '%"
        . $device
        . "%') AND s.process LIKE '%"
        . $process
        . "%' AND s.par000 = 1
        ORDER BY CASE WHEN s.status = 'Reject' THEN 0 WHEN s.status = 'Pending' OR s.status IS NULL OR s.status = '' THEN 1 ELSE 2 END, s.created_at DESC";

        return $this->query(
            $query
        )->getResultArray();
    }

    public function getByNumber($number)
    {
        $query = "SELECT s.*, p.name as process_name, d.name as device_name, p.docno 
                  FROM startup as s
                  LEFT JOIN proses as p ON s.process = p.process_code
                  LEFT JOIN device as d ON p.device = d.code
                  WHERE s.`number` = ? 
                  ORDER BY s.`par000` ASC";

        $rows = $this->query(
            $query,
            [(int) $number]
        )->getResultArray();

        if (!empty($rows)) {
            $empModel = new \App\Models\EmpModel();
            foreach ($rows as &$r) {
                $curName = trim((string) ($r['name'] ?? ''));
                $procName = trim((string) ($r['process_name'] ?? ''));
                $isInvalidName = in_array($curName, ['', '-'], true);
                if (!$isInvalidName) {
                    $isInvalidName = (strcasecmp($curName, $procName) === 0);
                }

                if ($isInvalidName) {
                    $rowEmp = $r['empid'] ?? '';
                    if (!empty($rowEmp)) {
                        try {
                            $empData = $empModel->getDataEmp(
                                $rowEmp
                            );
                            if ($empData) {
                                $empFullName = $empData->name ?? '';
                                if (!empty($empFullName)) {
                                    $r['name'] = trim((string) $empFullName);
                                }
                            }
                        } catch (\Throwable $e) {
                            // Abaikan jika gagal koneksi ke DB second
                        }
                    }
                }
            }
            unset($r);
        }

        return $rows;
    }
}