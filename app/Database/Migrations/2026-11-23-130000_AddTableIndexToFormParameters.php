<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTableIndexToFormParameters extends Migration
{
    public function up()
    {
        $fields = [
            // Penanda urutan tabel fisik (1 untuk Tabel 1, 2 untuk Tabel 2, dst. pada form multi-tabel / hybrid)
            'table_index' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 1,
                'null'       => false,
                'after'      => 'parent_id',
            ],
        ];

        $this->forge->addColumn('form_parameters', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('form_parameters', 'table_index');
    }
}
