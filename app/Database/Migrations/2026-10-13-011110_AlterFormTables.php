<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterFormTables extends Migration
{
    public function up()
    {
        // 1. Modifikasi tabel form_parameters
        $fieldsParameters = [
            'batas_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,4',
                'null'       => true,
                'after'      => 'tipe_input'
            ],
            'batas_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,4',
                'null'       => true,
                'after'      => 'batas_min'
            ],
            'posisi_grid' => [ // Penanda khusus untuk Sumbu X / Sumbu Y di form Foregoing
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'batas_max'
            ],
        ];
        $this->forge->addColumn('form_parameters', $fieldsParameters);

        // 2. Modifikasi tabel form_revisions
        $fieldsRevisions = [
            'kategori_form' => [ // Penanda apakah ini form startup, production, atau foregoing
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'default'    => 'production', // Default diasumsikan production
                'after'      => 'revisi'
            ],
        ];
        $this->forge->addColumn('form_revisions', $fieldsRevisions);
    }

    public function down()
    {
        // Rollback jika terjadi kesalahan
        $this->forge->dropColumn('form_parameters', ['batas_min', 'batas_max', 'posisi_grid']);
        $this->forge->dropColumn('form_revisions', ['kategori_form']);
    }
}