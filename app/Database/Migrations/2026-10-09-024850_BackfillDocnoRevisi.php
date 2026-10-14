<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillDocnoRevisi extends Migration
{
    public function up()
    {
        // 1. Suntik data lama di tabel Startup
        $this->db->query("UPDATE `startup` s JOIN `proses` p ON s.process = p.process_code SET s.docno = p.docno, s.revisi = p.revisi WHERE s.docno IS NULL");

        // 2. Suntik data lama di tabel Production
        $this->db->query("UPDATE `production` pr JOIN `proses` p ON pr.process = p.process_code SET pr.docno = p.docno, pr.revisi = p.revisi WHERE pr.docno IS NULL");

        // 3. Suntik data lama di tabel Foregoing
        $this->db->query("UPDATE `foregoing` f JOIN `proses` p ON f.process = p.process_code SET f.docno = p.docno, f.revisi = p.revisi WHERE f.docno IS NULL");
    }

    public function down()
    {
        // Jika di-rollback, kembalikan kolom menjadi NULL
        $this->db->query("UPDATE `startup` SET docno = NULL, revisi = NULL");
        $this->db->query("UPDATE `production` SET docno = NULL, revisi = NULL");
        $this->db->query("UPDATE `foregoing` SET docno = NULL, revisi = NULL");
    }
}