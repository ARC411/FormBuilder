<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRevisiFromDocno extends Migration
{
    public function up()
    {
        // Ekstrak angka setelah tanda '-' terakhir ke kolom revisi
        $sql = "UPDATE proses SET revisi = SUBSTRING_INDEX(docno, '-', -1) WHERE docno LIKE '%-%'";
        $this->db->query($sql);
    }

    public function down()
    {
        // Kosongkan kembali kalau di-rollback
        $sql = "UPDATE proses SET revisi = NULL";
        $this->db->query($sql);
    }
}