<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdvancedLayoutAndJudgementToFormDynamic extends Migration
{
    public function up()
    {
        // 1. Tambah kolom baru pada tabel form_parameters
        $fieldsParameters = [
            // Menyimpan daftar opsi custom dropdown dalam format JSON (contoh: ["Others (N)", "Green (Y)"] atau ["CDA", "Nitrogen"])
            'opsi_dropdown' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'standar_dinamis',
            ],
            // Kontrol presisi angka desimal (contoh: '0.001', '0.00000001', '0.01', '1', atau default 'any')
            'step_value' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'any',
                'after'      => 'opsi_dropdown',
            ],
            // Validasi rentang ganda (contoh: changeRange2 di mana nilai valid jika 40~50 ATAU 85~95) disimpan dalam JSON
            'rentang_ganda' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'step_value',
            ],
            // Untuk tipe parameter 'auto_judgment': menyimpan daftar parameter yang dievaluasi untuk ceklis (✔) / silang (✖), atau '*' untuk seluruh field
            'target_evaluasi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rentang_ganda',
            ],
        ];
        $this->forge->addColumn('form_parameters', $fieldsParameters);

        // 2. Tambah kolom baru pada tabel form_revisions
        $fieldsRevisions = [
            // Mode layout form: 'standard', 'matrix_grid' (seperti Foregoing & LDSM Production), atau 'hybrid_multi_table'
            'layout_mode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'standard',
                'after'      => 'kategori_form',
            ],
            // Menyimpan teks / instruksi SOP inspeksi yang tampil di bawah tabel form (<div class="note">)
            'catatan_sop' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'layout_mode',
            ],
        ];
        $this->forge->addColumn('form_revisions', $fieldsRevisions);
    }

    public function down()
    {
        $this->forge->dropColumn('form_parameters', [
            'opsi_dropdown',
            'step_value',
            'rentang_ganda',
            'target_evaluasi',
        ]);

        $this->forge->dropColumn('form_revisions', [
            'layout_mode',
            'catatan_sop',
        ]);
    }
}
