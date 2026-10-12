<?php 

namespace App\Models;

use CodeIgniter\Model;

class LogActivityModel extends Model {

	protected $table = 'log_activity';
    protected $useTimestamps = true;
    protected $allowedFields = ['id','emp_id','type_activity','activity','user','table_name','created_at'];

    public function addLogActivity($data)
    {
        return $this->insert($data);
    }
    public function logAction($type_activity, $activity, $table_name = '')
    {
        $session = \Config\Services::session();
        $is_operator = (stripos((string)$session->get('positionid'), 'operator') !== false);
        
        // Jangan log jika yang melakukan adalah operator (terutama untuk input data)
        if ($is_operator) {
            return false;
        }

        $data = [
            'emp_id' => $session->get('empid'),
            'type_activity' => $type_activity,
            'activity' => $activity,
            'user' => $session->get('name'),
            'table_name' => $table_name
        ];
        return $this->insert($data);
    }
    public function getAll($dateStart='1970-1-1',$dateEnd='2070-1-1')
    {
        $query = "SELECT *
        FROM log_activity
        WHERE 
        `created_at` >= '$dateStart' AND 
        `created_at` <= '$dateEnd' ";
        
        $data = $this->query($query);
        return $data->getResultArray();
    }
}

?>
