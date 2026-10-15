<?php 

namespace App\Models;

use CodeIgniter\Model;

class ForegoingModel extends Model {

    protected $table = 'foregoing';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'number','idcsheet', 'device', 'process', 'lotno', 'model', 'machno', 'empid', 'group', 'shift',
        'par000', 'par001', 'par002', 'par003', 'par004', 'par005', 'par006', 'par007', 'par008',
        'par009', 'par010', 'par011', 'par012', 'par013', 'par014', 'par015', 'par016', 'par017', 'par018',
        'par019', 'par020', 'par021', 'par022', 'par023', 'par024', 'par025', 'par026', 'par027', 'par028',
        'par029', 'par030', 'par031', 'par032', 'par033', 'par034', 'par035', 'par036', 'par037', 'par038',
        'par039', 'par040', 'par041', 'par042', 'docno', 'revisi'
    ];

    public function getAllData()
    {
        $query = "SELECT DISTINCT s.id, `number`, s.created_at, `process`, p.name as `process_name`, d.name as device_name, `machno`, `model`, `lotno`, `empid`, `shift`, `group`
        FROM `foregoing` as s
        INNER JOIN `proses` as p ON s.process = p.process_code 
        INNER JOIN `device` as d ON p.device = d.code
        WHERE `par000` = '1'";
        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function addProses($data)
    {
        $db = \Config\Database::connect();$prosesMaster = $db->table('proses')->where('process_code',$data['process'])->get()->getRowArray();

        if ($prosesMaster) {
            $data['par041'] =$prosesMaster['revisi'];
            $data['par042'] =$prosesMaster['berlaku'];
            $data['docno']  =$prosesMaster['docno'];
            $data['revisi'] =$prosesMaster['revisi'];
        }

        $query = 'SELECT * FROM foregoing WHERE `number` = ' . $data["number"] . ' AND `par000` = ' . $data["par000"];
        $result = $this->query($query)->getRow();

        if ($result == null) {
            $this->insert($data);
            return "Add foregoing";
        } else {
            $this->update($result->id,$data);
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

    public function getAll($dateStart = '1970-01-01', $dateEnd = '2070-01-01',$device = '', $process = '',$model = '', $lotno = '',$machno = '')
    {
        $startStr = empty($dateStart) ? '1970-01-01 00:00:00' : date('Y-m-d', strtotime($dateStart)) . ' 00:00:00';$endStr   = empty($dateEnd)   ? '2070-01-01 23:59:59' : date('Y-m-d', strtotime($dateEnd))   . ' 23:59:59';

        $machnoCondition = "";
        if ($machno !== "" && $machno !== "ALL" && $machno !== "null") {
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

        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function getByNumber($number)
    {
        $query = "SELECT s.*, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name 
                  FROM foregoing as s 
                  LEFT JOIN `proses` as p ON s.process = p.process_code 
                  LEFT JOIN `device` as d ON p.device = d.code 
                  WHERE s.`number` = ? 
                  ORDER BY s.`par000` ASC";
        
        return $this->query($query, [$number])->getResultArray();
    }

    public function deleteByNumber($number)
    {
        $query = "DELETE FROM `foregoing` WHERE `number` = ?";
        $result =$this->query($query, [$number]);
        if ($result) {
            return "Berhasil";
        } else {
            return "Gagal";
        }
    }
}