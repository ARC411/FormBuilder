<?php 

namespace App\Models;

use CodeIgniter\Model;

class DeviceModel extends Model {

	protected $table = 'device';
    protected $useTimestamps = true;
    protected $allowedFields = ['id','code','name', 'deleted_at'];

    public function getAll()
    {

        $query = "SELECT id, code, name FROM device" ;
    
        // return $query;
        $data = $this->query($query);
        return $data->getResultArray();
    }

    public function getDeviceById($id)
    {
        $query = "SELECT * FROM device WHERE `id` = $id";
        return $this->query($query)->getResultArray();
    }

    public function addDevice($data)
    {
        $code = $this->db->escape($data['code']);
        $name = $this->db->escape($data['name']);

        $query = "SELECT * FROM `device` WHERE code = $code OR name = $name";
        $result = $this->query($query)->getResultArray();

        if ($result) {
            return "Code atau nama tidak boleh sama";
        }

        $add = $this->insert($data);

        if ($add) {
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1), 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/history', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/history/foregoing', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/history/production', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/history/startup', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/input', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/input/foregoing', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/input/production', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/input/startup', 0777, true);
            $tes = mkdir('../app/Views/layout'.'/'.substr($code,1,-1).'/note', 0777, true);
            return "Berhasil menambahkan data";
        } else {
            return "Gagal menambahklan data";
        }
    }
    public function updateDevice($id,$data)
    {
        try {
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

    public function deleteDevice($id)
    {
        $delete = $this->delete($id);
        if($delete){
            return "Berhasil menghapus data";
        }
        else{
            return "Gagal menghapus data";
        }
    }
}

?>