<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsRequiredToFormParameters extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('is_required', 'form_parameters')) {
            $this->forge->addColumn('form_parameters', [
                'is_required' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'is_readonly',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_required', 'form_parameters')) {
            $this->forge->dropColumn('form_parameters', 'is_required');
        }
    }
}
