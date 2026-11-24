<?php 

namespace App\Models;

use CodeIgniter\Model;

class ProcessModel extends Model {

	protected $table = 'proses';
    protected $useTimestamps = true;
    protected $allowedFields = ['id','device','type','process_code','name','docno', 'deleted_at'];

    public function getAll($device = '', $type = '')
    {
        // `name` tetap "docno (name)" supaya pemakai lama tidak rusak.
        // `process_name` = nama asli, dipakai dropdown Process di form input.
        $builder = $this->db->table('proses')
            ->select("id, device, type, process_code, docno, name AS process_name, CONCAT(docno,' (',name,')') AS name", false);
        if ($device) {
            $builder->where('device', $device);
        }
        if ($type) {
            $builder->where('type', $type);
        }
        return $builder->orderBy('process_name', 'ASC')->orderBy('docno', 'ASC')->get()->getResultArray();
    }

    public function getAllData()
    {
        $query = "SELECT p.id, p.device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name  FROM proses as p INNER JOIN device as d ON p.device=d.code";
        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function getProcessById($id)
    {
        $query = "SELECT p.id, p.device, p.type, p.process_code, p.name as process_name, p.docno, d.name as device_name  FROM proses as p INNER JOIN device as d ON p.device=d.code
        WHERE p.id = $id";
        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function addProcess($data)
    {
        try {
            $add = $this->insert($data);
            if ($add) {
                return "Berhasil menambahkan data";
            } else {
                return "Gagal menambahklan data";
            }
        } catch (\Throwable $th) {
            return "Gagal Menambahkan data";
        }
    }

    public function updateProcess($id,$data)
    {
        try {
            $query = "SELECT * FROM proses WHERE `id` = $id";
            $result = $this->query($query)->getResultArray();
            $data['device'] = $data['device']=='-' ? $result[0]['device'] : $data['device'];
            $update = $this->update($id,$data);
            if($update){
                return "Berhasil Update";
            }
            else{
                return "Gagal update";
            }
        } catch (\Throwable $th) {
            return "Gagal update";
        }
        
    }

    public function deleteProcess($id)
    {
        try {
            $delete = $this->delete($id);
            if($delete){
                return "Berhasil menghapus data";
            }
            else{
                return "Gagal menghapus data";
            }
        } catch (\Throwable $th) {
            return "Gagal menghapus data";
        }
    }
}