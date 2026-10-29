<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionModel extends Model
{
        public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists($this->table) && !$db->fieldExists('weekly_id', $this->table)) {
                $db->query("ALTER TABLE `production` ADD COLUMN `weekly_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `revisi`, ADD INDEX `idx_prod_weekly_id` (`weekly_id`)");
            }
        } catch (\Throwable $e) {
            // Abaikan jika kolom sudah ada
        }
    }

    protected $table = 'production';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id', 'number', 'device', 'process', 'model', 'lotno', 'machno',
        'empid', 'group', 'shift', 'name', 'empid2', 'group2', 'shift2', 'name2', 'std_die_bond',
        'par001', 'par002', 'par003', 'par004', 'par005', 'par006', 'par007',
        'par008', 'par009', 'par010', 'par011', 'par012', 'par013', 'par014',
        'par015', 'par016', 'par017', 'par018', 'par019', 'par020', 'par021',
        'par022', 'par023', 'par024', 'par025', 'par026', 'par027', 'par028',
        'par029', 'par030', 'par031', 'par032', 'par033', 'par034', 'par035',
        'par036', 'par037', 'par038', 'par039', 'par040', 'par041', 'par042',
        'par043', 'par044', 'par045', 'foreman', 'leader', 'supervisor', 'docno', 'revisi', 'weekly_id'
    ];

    public function getCsheet($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }
        return $this->where(['slug' => $slug])->first();
    }

    /**
     * Data untuk halaman index (versi terbatas, hanya kolom yang dipakai).
     */
    public function getAllData($limit = 1000)
    {
        return $this->db->table('production')
            ->select('number, created_at, device, name, lotno, empid, empid2')
            ->where('par001', 1)
            ->orderBy('created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Data untuk DataTables server-side.
     */
    public function getDatatable($start, $length, $search, $orderCol, $orderDir)
    {
        $cols     = ['number', 'created_at', 'device', 'name', 'lotno', 'empid', 'empid2'];
        $orderCol = $cols[$orderCol] ?? 'created_at';
        $orderDir = strtolower($orderDir) === 'asc' ? 'ASC' : 'DESC';

        $builder = $this->db->table('production')->where('par001', 1);
        $total   = $builder->countAllResults(false);

        if ($search !== '') {
            $builder->groupStart()
                ->like('lotno', $search)
                ->orLike('device', $search)
                ->orLike('name', $search)
                ->orLike('empid', $search)
                ->orLike('empid2', $search)
                ->groupEnd();
        }
        $filtered = $builder->countAllResults(false);

        $rows = $builder->select(implode(',', $cols))
            ->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return ['total' => $total, 'filtered' => $filtered, 'rows' => $rows];
    }

    public function getAll($dateStart = '1970-1-1', $dateEnd = '2070-1-1', $process = '', $model = '', $lotno = '', $machno = '')
    {
        $builder = $this->db->table('production')
            ->where('created_at >=', $dateStart)
            ->where('created_at <=', $dateEnd)
            ->where('process', $process);

        if ($model !== '' && $model !== null) {
            $builder->like('model', $model);
        }
        if ($lotno !== '' && $lotno !== null) {
            $builder->like('lotno', $lotno);
        }
        if (!in_array($machno, ['', 'ALL', null], true)) {
            $builder->where('machno', $machno);
        }

        return $builder->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getLatestId()
    {
        $result = $this->query("SELECT `number` FROM production ORDER BY `number` DESC LIMIT 1");
        if ($result == null) {
            return 0;
        }
        return $result->getRow()->number ?? 0;
    }

    public function getByNumber($number)
    {
        // Perbaikan: Lakukan JOIN ke tabel proses dan device untuk mendapatkan nama spesifik
        $query = "SELECT pr.*, p.name AS process_name, d.name AS device_name, p.docno 
                  FROM production AS pr
                  LEFT JOIN proses AS p ON pr.process = p.process_code
                  LEFT JOIN device AS d ON pr.device = d.code
                  WHERE pr.`number` = ? 
                  ORDER BY pr.`par001` ASC";
                  
        return $this->query($query, [$number])->getResultArray();
    }

    public function getApprovalData($status = "PENDING")
    {
        $query = "SELECT DISTINCT `number`, `created_at`, `process`, `machno`, `model`, `lotno`, `empid`, `shift`, `group`, `par044`, `foreman`, `leader`, `supervisor`
                  FROM production
                  WHERE `par001` <= 1";
        return $this->query($query)->getResultArray();
    }

    public function getLotHistory($dateStart = '1970-1-1', $dateEnd = '2070-1-1', $process = '', $model = '', $lotno = '', $machno = '', $device = '', $status = 'Process Start')
    {
        $query = "SELECT DISTINCT `number`, p.name AS name, d.name AS device_name, cs.created_at, `process`, cs.device, `machno`, `model`, `lotno`, `empid`, `shift`, `group`, `par009`, `par045`
                  FROM production AS cs
                  INNER JOIN proses AS p ON cs.process = p.process_code
                  INNER JOIN device AS d ON p.device = d.code
                  WHERE `par001` <= 1
                    AND cs.created_at >= ?
                    AND cs.created_at <= ?
                    AND `process` LIKE ?
                    AND cs.device LIKE ?
                    AND `model` LIKE ?
                    AND `lotno` LIKE ?
                    AND `machno` LIKE ?
                    AND `par045` = ?
                  ORDER BY created_at DESC";

        return $this->query($query, [
            $dateStart,
            $dateEnd,
            "%$process%",
            "%$device%",
            "%$model%",
            "%$lotno%",
            "%$machno%",
            $status,
        ])->getResultArray();
    }

   public function addProses($data)
    {
        $prosesMaster = $this->db->table('proses')
            ->where('process_code', $data['process'])
            ->get()
            ->getRowArray();

        // Ambil revisi terbaru dari form_revisions jika ada
        $latestRevRow = $this->db->table('form_revisions')
            ->where('process_code', $data['process'])
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $activeRevisi = $data['revisi'] ?? ($latestRevRow['revisi'] ?? ($prosesMaster['revisi'] ?? '0'));

        if ($prosesMaster) {
            $data['docno'] = $prosesMaster['docno'];
        }
        $data['revisi'] = (string)$activeRevisi;

        $result = $this->query(
            "SELECT * FROM production WHERE `number` = ? AND `par001` = ?",
            [$data['number'], $data['par001']]
        )->getRow();

        if ($result == null) {
            $data['empid2'] = null;
            $data['group2'] = null;
            $data['shift2'] = null;
            $data['name2']  = null;
            $data['par009'] = null;
            $data['par045'] = 'Process Start';
            $this->insert($data);
            return 'Add Production (Process Start)';
        }

        // PENTING: Saat Edit / Complete dokumen yang sudah ada, PERTAHANKAN nomor revisi aslinya!
        if (!empty($result->revisi) && $result->revisi !== '-') {
            $data['revisi'] = $result->revisi;
        }

        if ($result->par045 == 'Process Complete') {
            $this->update($result->id, $data);
            return 'Update Production';
        }

        $data['empid']  = $result->empid;
        $data['group']  = $result->group;
        $data['shift']  = $result->shift;
        $data['name']   = $result->name;
        $data['par008'] = $result->par008;
        $data['par045'] = 'Process Complete';
        $this->update($result->id, $data);
        return 'Add Production (Process Complete)';
    }

    public function deleteByNumber($number)
    {
        $result = $this->query("DELETE FROM `production` WHERE `number` = ?", [$number]);
        return $result ? "Berhasil" : "Gagal";
    }

    public function testProses($number, $par)
    {
        $result = $this->query(
            "SELECT * FROM production WHERE `number` = ? AND `par001` = ?",
            [$number, $par]
        );
        return $result->getRow()->id;
    }

        public function getByWeeklyId($weeklyId)
    {
        $query = "SELECT pr.*, p.name AS process_name, d.name AS device_name, p.docno 
                  FROM production AS pr
                  LEFT JOIN proses AS p ON pr.process = p.process_code
                  LEFT JOIN device AS d ON pr.device = d.code
                  WHERE pr.`weekly_id` = ? 
                  ORDER BY pr.`created_at` ASC, pr.`number` ASC, pr.`par001` ASC";
        return $this->query($query, [(int)$weeklyId])->getResultArray();
    }
}