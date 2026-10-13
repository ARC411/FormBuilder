<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStdDieBondToProduction extends Migration
{
    public function up()
    {
        $fields = [
            'std_die_bond' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
                'after'      => 'lotno' // Akan diletakkan setelah kolom lotno
            ],
        ];

        // PENTING: Ganti 'production' dengan nama tabel Anda yang sebenarnya di database
        $this->forge->addColumn('production', $fields);
    }

    public function down()
    {
        // PENTING: Ganti 'production' dengan nama tabel Anda yang sebenarnya
        $this->forge->dropColumn('production', 'std_die_bond');
    }
}
