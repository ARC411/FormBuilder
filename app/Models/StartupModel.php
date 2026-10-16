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
        'par021_unit'
    ];

    public function getCsheet($slug = false)
    {
        if ($slug == false) {
            return $this->findall();
        }
        return $this->where(['slug' =>$slug])->first();
    }

    public function getAllData()
    {
        $query = "SELECT DISTINCT s.id,`number`, s.created_at, `process`,d.name as device_name, `machno`, `model`, `lotno`, `empid`, `shift`, `group` ,`status`,`role`, `foreman` , `leader` , `supervisor`, p.name
        FROM `startup` as s
        INNER JOIN `proses` as p ON s.process=p.process_code 
        INNER JOIN `device` as d ON p.device=d.code
        WHERE `par000`='1'";
        
        return $this->query($query)->getResultArray();
    }

    // Perbaikan Logika Update Approval menggunakan Query Builder
    public function updateApproval($number, $name,$level)
    {
        // Ambil data berdasarkan number
        $data = $this->where('number',$number)->first();
        
        if (!$data) {
            return false;
        }

        $updateData = [];

        // Level Foreman
        if ($level == 14) {$updateData['status'] = "Approve by Foreman";
            $updateData['role'] = "foreman";
            $updateData['foreman'] =$name;
        }
        // Level Leader
        else if ($level == 10) {$updateData['role'] = "leader";
            
            // Logika diperbaiki: Jika foreman kosong, Bypass. Jika ada, Approve.
            if (empty($data['foreman'])) {$updateData['status'] = "Bypass Leader";
                $updateData['foreman'] =$name;
                $updateData['leader'] =$name;
            } else {
                $updateData['status'] = "Approve Leader";
                $updateData['leader'] =$name;
            }
        }
        // Level diatas assisten supervisor
        else if ($level <= 7) {$updateData['role'] = "supervisor";
            $updateData['supervisor'] =$name;

            if (!empty($data['leader'])) {$updateData['status'] = "Approve Supervisor";
            } else if (!empty($data['foreman'])) {$updateData['status'] = "Bypass Supervisor";
                $updateData['leader'] =$name;
            } else {
                $updateData['status'] = "Bypass Supervisor";
                $updateData['foreman'] =$name;
                $updateData['leader'] =$name;
            }
        }

        // Update semua data (par000 = 1 sampai 5) yang memiliki number tersebut
        return $this->where('number', $number)->set($updateData)->update();
    }

    public function addProses($data)
{
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

        $query = 'SELECT * FROM startup  WHERE `number` = ' . $data["number"] . ' AND `par000` = ' . $data["par000"];
        $result = $this->query($query)->getRow();

        if ($result == null) {
            $this->insert($data);
            return "Add Startup";
        } else {
            $this->update($result->id,$data);
            return "Update Startup";
        }
    }

    // Fungsi untuk mengambil data startup berdasarkan ID Mingguan
    public function getByWeeklyId($weekly_id)
    {
        $sql = "SELECT DISTINCT s.id, `number`, s.created_at, `process`, d.name as device_name, `machno`, `model`, `lotno`, `empid`, `shift`, `group`, `status`, `role`, `foreman`, `leader`, `supervisor`, p.name
        FROM `startup` as s
        INNER JOIN `proses` as p ON s.process=p.process_code 
        INNER JOIN `device` as d ON p.device=d.code
        WHERE s.weekly_id = '$weekly_id' AND s.par000='1'
        ORDER BY s.created_at ASC";
        
        return $this->query($sql)->getResultArray();
    }

    public function getLatestId()
    {
        $query = "SELECT `number` FROM startup ORDER BY `number` DESC LIMIT 1 ";
        $result = $this->query($query);
        if ($result == null) {
            return 0;
        }
        return $result->getRow()->number ?? 0;
    }

    public function getAll($dateStart = '1970-01-01', $dateEnd = '2070-01-01',$device = '', $process = '',$model = '', $lotno = '',$machno = '')
    {
        // Normalisasi format tanggal agar tidak terjadi duplikasi jam (00:00:00 00:00:00)
        $startStr = empty($dateStart) ? '1970-01-01 00:00:00' : date('Y-m-d', strtotime($dateStart)) . ' 00:00:00';$endStr   = empty($dateEnd)   ? '2070-01-01 23:59:59' : date('Y-m-d', strtotime($dateEnd))   . ' 23:59:59';

        $sql = "SELECT DISTINCT s.*, p.device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name
                FROM `startup` as s
                INNER JOIN `proses` as p ON s.process = p.process_code 
                INNER JOIN `device` as d ON p.device = d.code
                WHERE s.created_at >= '$startStr' 
                AND s.created_at <= '$endStr' 
                AND s.device LIKE '%$device%' 
                AND s.process LIKE '%$process%'";

        if ($model !== '') {$sql .= " AND s.model LIKE '%$model%'";
        }
        if ($lotno !== '') {$sql .= " AND s.lotno LIKE '%$lotno%'";
        }
        if ($machno !== '' &&$machno !== 'ALL' && $machno !== 'null') {$sql .= " AND s.machno = '$machno'";
        }

        $sql .= " ORDER BY s.created_at ASC";
        
        return $this->query($sql)->getResultArray();
    }

    public function deleteByNumber($number)
    {
        $query = "DELETE FROM `startup` WHERE `number` = $number ";
        $result = $this->query($query);
        if ($result) {
            return "Berhasil";
        } else {
            return "Gagal";
        }
    }

    // Perbaikan utama untuk Approval Form
    public function getApprovalData($dateStart = '',$dateEnd = '', $device = '',$process = '')
    {
        // Pasang Default jika kosong
        $dateStart = empty($dateStart) ? date('Y-m-d', strtotime('-1 month')) : $dateStart;
        $dateEnd = empty($dateEnd) ? date('Y-m-d') : $dateEnd;

        // Perbaikan Batas Waktu (Jam ditambahkan otomatis)
        $start =$dateStart . ' 00:00:00';
        $end =$dateEnd . ' 23:59:59';

        $query = "SELECT DISTINCT *
        FROM `startup` as s
        INNER JOIN (SELECT device, type, process_code, name as process_name, docno FROM proses ) as p ON s.process=p.process_code 
        INNER JOIN (SELECT code, name as device_name FROM device ) as d ON p.device=d.code
        WHERE 
        s.created_at >= '$start' AND 
        s.created_at <= '$end' AND
        s.device LIKE '%$device%' AND
        s.process LIKE '%$process%' AND
        s.par000 = 1
        ORDER BY s.created_at DESC";

        return $this->query($query)->getResultArray();
    }

    public function getByNumber($number)
    {
        // PERBAIKAN: Tambahkan JOIN ke tabel proses dan device untuk mendapatkan nama yang benar
        $query = "SELECT s.*, p.name as process_name, d.name as device_name, p.docno 
                  FROM startup as s
                  LEFT JOIN proses as p ON s.process = p.process_code
                  LEFT JOIN device as d ON p.device = d.code
                  WHERE s.`number` = ? 
                  ORDER BY s.`par000` ASC";
        
        return $this->query($query, [$number])->getResultArray();
    }
}