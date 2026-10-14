<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocnoRevisiToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'docno'  => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'revisi' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
        ];

        // Menyuntikkan kolom ke tabel-tabel transaksi
        $this->forge->addColumn('checksheet', $fields);
        $this->forge->addColumn('startup', $fields);
        $this->forge->addColumn('production', $fields);
        $this->forge->addColumn('foregoing', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('checksheet', ['docno', 'revisi']);
        $this->forge->dropColumn('startup', ['docno', 'revisi']);
        $this->forge->dropColumn('production', ['docno', 'revisi']);
        $this->forge->dropColumn('foregoing', ['docno', 'revisi']);
    }
}