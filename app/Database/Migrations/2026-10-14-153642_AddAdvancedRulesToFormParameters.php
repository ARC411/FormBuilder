<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdvancedRulesToFormParameters extends Migration
{
    public function up()
    {
        $fields = [
            // 1. Menyimpan JSON daftar satuan & batas min/max per satuan (Contoh: N/gr atau MPa/kPa)
            'satuan_opsi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'batas_max',
            ],
            // 2. Penanda apakah kolom input ini Read-Only (1 = Ya, 0 = Tidak)
            'is_readonly' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'satuan_opsi',
            ],
            // 3. Penanda pada Induk jika anak-anaknya hanya boleh diisi salah satu (Saling Mengunci / Gray Out)
            'is_exclusive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'is_readonly',
            ],
            // 4. Menyimpan JSON rumus hitung otomatis (Contoh: NG = In - Out, Yield = (Out / In) * 100)
            'rumus_otomatis' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'is_exclusive',
            ],
            // 5. Menyimpan JSON dropdown penentu standar dinamis (Contoh: Jenis SM-Chip -> Min: 8.7 N)
            'standar_dinamis' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rumus_otomatis',
            ],
        ];

        $this->forge->addColumn('form_parameters', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('form_parameters', [
            'satuan_opsi',
            'is_readonly',
            'is_exclusive',
            'rumus_otomatis',
            'standar_dinamis',
        ]);
    }
}