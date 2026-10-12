<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormDynamicTables extends Migration
{
    public function up()
    {
        // 1. Tabel MASTER REVISI (Menyimpan identitas form & revisinya)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'process_code' => ['type' => 'VARCHAR', 'constraint' => 50],
            'revisi'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('form_revisions');

        // 2. Tabel PARAMETER (Menyimpan silsilah bapak-anak & form builder)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'revision_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'parent_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nama_parameter' => ['type' => 'VARCHAR', 'constraint' => 255], // Contoh: Foregoing, MSP Chip, Crack
            'kolom_db'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // Contoh: par001, par002
            'tipe_input'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true], // text, number, ok_ng
            'standar_nilai'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], // Contoh: Min 2.5
            'urutan'         => ['type' => 'INT', 'constraint' => 5, 'default' => 0], // Urutan tampilan dari kiri ke kanan
        ]);
        $this->forge->addKey('id', true);
        
        // Relasi: Kalau dokumen revisi dihapus, semua parameternya ikut hangus
        $this->forge->addForeignKey('revision_id', 'form_revisions', 'id', 'CASCADE', 'CASCADE');
        
        // Relasi Silsilah: Kalau parameter bapak dihapus (-), anak dan cucunya otomatis terhapus!
        $this->forge->addForeignKey('parent_id', 'form_parameters', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('form_parameters');
    }

    public function down()
    {
        // Rollback hapus dari anak dulu baru bapaknya
        $this->forge->dropTable('form_parameters');
        $this->forge->dropTable('form_revisions');
    }
}