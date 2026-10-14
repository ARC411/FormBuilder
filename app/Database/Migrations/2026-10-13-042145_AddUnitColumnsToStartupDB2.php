<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitColumnsToStartupDB2 extends Migration
{
    public function up()
    {
        $fields = [
            'par015_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'par016_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'par018_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'par019_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'par020_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'par021_unit' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
        ];

        // Menambahkan kolom ke tabel startup
        $this->forge->addColumn('startup', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('startup', ['par015_unit', 'par016_unit', 'par018_unit', 'par019_unit', 'par020_unit', 'par021_unit']);
    }
}