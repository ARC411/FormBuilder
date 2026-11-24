<?php 

namespace App\Models;

use CodeIgniter\Model;

class InputModel extends Model {

	protected $table = 'checksheet';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'idcsheet', 'device', 'process', 'lotno', 'model', 'machno', 'empid', 'group', 'shift', 'docno', 'revisi',
        'par001', 'par002', 'par003', 'par004', 'par005', 'par006', 'par007', 'par008', 'par009', 'par010',
        'par011', 'par012', 'par013', 'par014', 'par015', 'par016', 'par017', 'par018', 'par019', 'par020',
        'par021', 'par022', 'par023', 'par024', 'par025', 'par026', 'par027', 'par028', 'par029', 'par030',
        'par031', 'par032', 'par033', 'par034', 'par035', 'par036', 'par037', 'par038', 'par039', 'par040',
        'par041', 'par042', 'par043', 'par044', 'par045', 'par046', 'par047', 'par048', 'par049', 'par050',
        'created_at', 'updated_at', 'deleted_at'
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
            if ($db->tableExists($this->table)) {
                if ($db->fieldExists('deleted_at', $this->table)) {
                    $db->query("ALTER TABLE `{$this->table}` MODIFY COLUMN `deleted_at` DATETIME NULL DEFAULT NULL");
                }
                if ($db->fieldExists('updated_at', $this->table)) {
                    $db->query("ALTER TABLE `{$this->table}` MODIFY COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
                }
            }
        } catch (\Throwable $e) {}
    }

    public function getCsheet($slug = false)
    {
        if ($slug == false)
        {
            return $this->findall();
        }

        return $this->where(['slug' => $slug])->first();

    }
    
}

?>