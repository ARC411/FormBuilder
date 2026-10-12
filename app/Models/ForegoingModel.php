<?php 

namespace App\Models;

use CodeIgniter\Model;

class ForegoingModel extends Model {

	protected $table = 'foregoing';
    protected $useTimestamps = true;
    protected $allowedFields = ['number','idcsheet', 'device', 'process', 'lotno', 'model', 'machno', 'empid', 'group', 'shift','par000', 'par001', 'par002', 'par003', 'par004', 'par005', 'par006', 'par007', 'par008',
    'par009', 'par010', 'par011', 'par012', 'par013', 'par014', 'par015', 'par016', 'par017', 'par018', 'par019', 'par020', 'par021', 'par022', 'par023', 'par024', 'par025', 'par026', 'par027', 'par028', 'par029',
    'par030', 'par031','par032', 'par033', 'par034', 'par035', 'par036', 'par037', 'par038', 'par039', 'par040','par041','par042', 'docno', 'revisi'];

    public function getAllData()
    {
        $query = "SELECT DISTINCT s.id,`number`, s.created_at, `process`,p.name as `process_name`,d.name as device_name, `machno`, `model`, `lotno`, `empid`, `shift`, `group`
        FROM `foregoing` as s
        INNER JOIN `proses` as p ON s.process=p.process_code 
        INNER JOIN `device` as d ON p.device=d.code
        WHERE `par000`='1'";
        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function addProses($data)
{
    // --- TAMBAHAN BARU: SNAPSHOT REVISI & BERLAKU ---
    $db = \Config\Database::connect();
    $prosesMaster = $db->table('proses')->where('process_code',$data['process'])->get()->getRowArray();

    if ($prosesMaster) {
        $data['par041'] =$prosesMaster['revisi'];
        $data['par042'] =$prosesMaster['berlaku'];

        // --- TAMBAHAN BARU: Stempel Otomatis ---
        $data['docno']  = $prosesMaster['docno'];
        $data['revisi'] = $prosesMaster['revisi'];
        // --------------------------------------
    }

        $query = 'SELECT * FROM foregoing  WHERE `number` = ' . $data["number"] . ' AND `par000` = ' . $data["par000"];
        $result=$this->query($query)->getRow();

        if($result==null){
            $this->insert($data);
            return "Add foregoing";
        }
        else{
            $this->update($result->id,$data);
            return "Update foregoing";
        }
    }
    public function getLatestId()
    {
        $query = "SELECT `number` FROM foregoing ORDER BY `number` DESC LIMIT 1 " ;
        $result=$this->query($query);
        if($result==null){
            return 0;
        }
        return $result->getRow()->number ?? 0;
    }
    public function getAll($dateStart='1970-1-1',$dateEnd='2070-1-1',$device='',$process='',$model='',$lotno='',$machno='')
    {
        $machnoCondition = "";
        if ($machno === "" || $machno === "ALL") {
            // Jika kosong atau "ALL", abaikan filter
        } else {
            // FIX: Gunakan = untuk pencarian mutlak
            $machnoCondition = "AND s.machno = '$machno'";
        }

        $modelCondition = "";
        if ($model === "") {
            $modelCondition = "AND (s.model LIKE '%%' OR s.model IS NULL)";
        } else {
            $modelCondition = "AND s.model LIKE '%$model%'";
        }

        $lotnoCondition = "";
        if ($lotno === "") {
            $lotnoCondition = "AND (s.lotno LIKE '%%' OR s.lotno IS NULL)";
        } else {
            $lotnoCondition = "AND s.lotno LIKE '%$lotno%'";
        }

        $query = "SELECT DISTINCT *
        FROM `foregoing` as s
        INNER JOIN (SELECT device, type, process_code, name as process_name, docno FROM proses ) as p ON s.process=p.process_code 
        INNER JOIN (SELECT code, name as device_name FROM device ) as d ON p.device=d.code
        WHERE 
        s.created_at >= '$dateStart' AND 
        s.created_at <= '$dateEnd' AND
        s.device LIKE '%$device%' AND
        s.process LIKE '%$process\%'$modelCondition 
        $lotnoCondition$machnoCondition
        ORDER BY s.machno ASC, s.created_at ASC"; // <-- UBAH BARIS INI
        
        $data = $this->query($query);
        return $data->getResultArray();
    }
    public function getByNumber($number)
    {
        $query = "SELECT *
        FROM foregoing as s
        INNER JOIN (SELECT device, type, process_code, name, docno FROM proses ) as p ON s.process=p.process_code
        WHERE 
        `number` = $number 
        ORDER BY `par000`";
        $data = $this->query($query);
        return $data->getResultArray();
    }
}

?>