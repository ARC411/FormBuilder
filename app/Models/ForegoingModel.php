<?php 

namespace App\Models;

use CodeIgniter\Model;

class ForegoingModel extends Model
{
    protected $table = 'foregoing';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'number', 'idcsheet', 'device', 'process', 'lotno', 'model', 'machno', 'empid', 'group', 'shift', 'name',
        'par000', 'par001', 'par002', 'par003', 'par004', 'par005', 'par006', 'par007', 'par008',
        'par009', 'par010', 'par011', 'par012', 'par013', 'par014', 'par015', 'par016', 'par017', 'par018',
        'par019', 'par020', 'par021', 'par022', 'par023', 'par024', 'par025', 'par026', 'par027', 'par028',
        'par029', 'par030', 'par031', 'par032', 'par033', 'par034', 'par035', 'par036', 'par037', 'par038',
        'par039', 'par040', 'par041', 'par042', 'docno', 'revisi',
        'status', 'role', 'foreman', 'leader', 'supervisor', 'edit_status', 'unlocked_by',
        'reject_reason', 'reject_by', 'reject_date'
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
            $colsToCheck = [
                'name'          => "ALTER TABLE `foregoing` ADD COLUMN `name` VARCHAR(100) NULL DEFAULT NULL AFTER `shift`",
                'status'        => "ALTER TABLE `foregoing` ADD COLUMN `status` VARCHAR(50) NULL DEFAULT 'Pending' AFTER `revisi`",
                'role'          => "ALTER TABLE `foregoing` ADD COLUMN `role` VARCHAR(50) NULL DEFAULT NULL AFTER `status`",
                'foreman'       => "ALTER TABLE `foregoing` ADD COLUMN `foreman` VARCHAR(100) NULL DEFAULT NULL AFTER `role`",
                'leader'        => "ALTER TABLE `foregoing` ADD COLUMN `leader` VARCHAR(100) NULL DEFAULT NULL AFTER `foreman`",
                'supervisor'    => "ALTER TABLE `foregoing` ADD COLUMN `supervisor` VARCHAR(100) NULL DEFAULT NULL AFTER `leader`",
                'edit_status'   => "ALTER TABLE `foregoing` ADD COLUMN `edit_status` TINYINT(1) NOT NULL DEFAULT 0 AFTER `supervisor`",
                'unlocked_by'   => "ALTER TABLE `foregoing` ADD COLUMN `unlocked_by` VARCHAR(100) NULL DEFAULT NULL AFTER `edit_status`",
                'reject_reason' => "ALTER TABLE `foregoing` ADD COLUMN `reject_reason` TEXT NULL AFTER `unlocked_by`",
                'reject_by'     => "ALTER TABLE `foregoing` ADD COLUMN `reject_by` VARCHAR(100) NULL AFTER `reject_reason`",
                'reject_date'   => "ALTER TABLE `foregoing` ADD COLUMN `reject_date` DATETIME NULL AFTER `reject_by`",
            ];
            foreach ($colsToCheck as $col => $sql) {
                if (!$db->fieldExists($col, 'foregoing')) {
                    $db->query($sql);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika kolom sudah ada
        }
    }

    private function attachOperatorNames(array $rows): array
    {
        if (empty($rows)) {
            return $rows;
        }

        $empCache = [];
        $empModel = new \App\Models\EmpModel();

        foreach ($rows as &$r) {
            $curName = trim((string) ($r['name'] ?? ''));
            $procName = trim((string) ($r['process_name'] ?? ''));
            $needsLookup = ($curName === '' || $curName === '-' || strcasecmp($curName, $procName) === 0);

            if ($needsLookup && !empty($r['empid'])) {
                $empId = trim((string) $r['empid']);
                if (!array_key_exists($empId, $empCache)) {
                    $empCache[$empId] = '';
                    try {
                        $empData = $empModel->getDataEmp($empId);
                        if ($empData && !empty($empData->name)) {
                            $empCache[$empId] = trim((string) $empData->name);
                        }
                    } catch (\Throwable $e) {
                        // Abaikan jika koneksi DB second gagal
                    }
                }
                if ($empCache[$empId] !== '') {
                    $r['name'] = $empCache[$empId];
                }
            }
        }
        unset($r);

        return $rows;
    }

    public function getRejectedCountByOperator(
        $empid = '',
        $name = ''
    ) {
        $builder = $this->db->table($this->table);
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
        // Fallback otomatis: jika dipanggil tanpa parameter, cek apakah user yang login adalah operator biasa
        if ($empid === null && $name === null) {
            $session = \Config\Services::session();
            $sessRole  = strtolower(trim((string) ($session->get('role') ?? '')));
            $sessLevel = (int) ($session->get('level') ?? 99);
            $sessEmpId = trim((string) ($session->get('empid') ?? ''));
            $sessName  = trim((string) ($session->get('name') ?? ''));

            $isApproverOrAdmin = (
                in_array($sessRole, ['admin', 'foreman', 'leader', 'supervisor'], true) ||
                strtolower($sessEmpId) === 'admin' ||
                $sessLevel === 14 ||
                $sessLevel === 10 ||
                ($sessLevel > 0 && $sessLevel <= 7)
            );

            if (!$isApproverOrAdmin) {
                $empid = $sessEmpId;
                $name  = $sessName;
            }
        }

        $whereEmp = "";
        $conds = [];

        if (!empty($empid)) {
            $escEmp = $this->db->escape(trim((string) $empid));
            $conds[] = "TRIM(s.empid) = " . $escEmp;
        }
        if (!empty($name)) {
            $escName = $this->db->escape(trim((string) $name));
            $conds[] = "COALESCE(TRIM(s.name), '') = " . $escName;
        }
        if (!empty($conds)) {
            $whereEmp = " AND (" . implode(" OR ", $conds) . ")";
        }

        $query = "SELECT DISTINCT s.id, s.`number`, s.created_at, s.`process`, p.name as `process_name`, 
                  d.name as device_name, s.`machno`, s.`model`, s.`lotno`, s.`empid`, s.`name`, s.`shift`, s.`group`,
                  s.`status`, s.`role`, s.`foreman`, s.`leader`, s.`supervisor`, 
                  s.`edit_status`, s.`unlocked_by`, s.`reject_reason`, s.`reject_by`, s.`reject_date`
                  FROM `foregoing` as s
                  INNER JOIN `proses` as p ON s.process = p.process_code 
                  INNER JOIN `device` as d ON p.device = d.code
                  WHERE s.`par000` = '1' "
                  . $whereEmp
                  . " ORDER BY CASE WHEN s.status = 'Reject' THEN 0 ELSE 1 END, s.created_at DESC";

        $rows = $this->query($query)->getResultArray();
        return $this->attachOperatorNames($rows);
    }

    public function markRejectRequested($number)
    {
        return $this->db->table($this->table)
            ->where('number', (int) $number)
            ->update(['edit_status' => 1]);
    }

    public function addProses($data)
    {
        $db = \Config\Database::connect();
        $prosesMaster = $db->table('proses')->where('process_code', $data['process'])->get()->getRowArray();
        $latestRevRow = $db->table('form_revisions')
            ->where('process_code', $data['process'])
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
        $query   = "SELECT * FROM foregoing WHERE `number` = " . $numVal . " AND `par000` = " . $par0Val;
        $result  = $this->query($query)->getRow();

        if ($result == null) {
            if (!isset($data['status'])) {
                $data['status'] = 'Pending';
            }
            $data['edit_status'] = 0;
            $this->insert($data);
            return "Add foregoing";
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
            return "Update foregoing";
        }
    }

    public function getLatestId()
    {
        $query = "SELECT `number` FROM foregoing ORDER BY `number` DESC LIMIT 1";
        $result = $this->query($query);
        if ($result == null) {
            return 0;
        }
        return $result->getRow()->number ?? 0;
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
        $startStr = empty($dateStart) ? '1970-01-01 00:00:00' : date('Y-m-d', strtotime($dateStart)) . ' 00:00:00';
        $endStr   = empty($dateEnd)   ? '2070-01-01 23:59:59' : date('Y-m-d', strtotime($dateEnd))   . ' 23:59:59';

        $machnoCondition = "";
        if ($machno !== "" && $machno !== "ALL" && $machno !== "null" && $machno !== "-") {
            $machnoCondition = " AND s.machno = '" . $machno . "'";
        }

        $modelCondition = "";
        if ($model === "") {
            $modelCondition = " AND (s.model LIKE '%%' OR s.model IS NULL)";
        } else {
            $modelCondition = " AND s.model LIKE '%" . $model . "%'";
        }

        $lotnoCondition = "";
        if ($lotno === "") {
            $lotnoCondition = " AND (s.lotno LIKE '%%' OR s.lotno IS NULL)";
        } else {
            $lotnoCondition = " AND s.lotno LIKE '%" . $lotno . "%'";
        }

        $query = "SELECT DISTINCT s.*, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name 
                  FROM `foregoing` as s 
                  INNER JOIN `proses` as p ON s.process = p.process_code 
                  INNER JOIN `device` as d ON p.device = d.code 
                  WHERE s.created_at >= '" . $startStr . "' 
                  AND s.created_at <= '" . $endStr . "' 
                  AND s.device LIKE '%" . $device . "%' 
                  AND s.process LIKE '%" . $process . "%'"
                  . $modelCondition
                  . $lotnoCondition
                  . $machnoCondition
                  . " ORDER BY s.created_at ASC, s.number ASC, s.par000 ASC";

        $rows = $this->query($query)->getResultArray();
        return $this->attachOperatorNames($rows);
    }

    public function getByNumber($number)
    {
        $query = "SELECT s.*, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name 
                  FROM foregoing as s 
                  LEFT JOIN `proses` as p ON s.process = p.process_code 
                  LEFT JOIN `device` as d ON p.device = d.code 
                  WHERE s.`number` = ? 
                  ORDER BY s.`par000` ASC";
        
        $rows = $this->query($query, [(int) $number])->getResultArray();
        return $this->attachOperatorNames($rows);
    }

    public function deleteByNumber($number)
    {
        $query = "DELETE FROM `foregoing` WHERE `number` = ?";
        $result = $this->query($query, [(int) $number]);
        return $result ? "Berhasil" : "Gagal";
    }
}