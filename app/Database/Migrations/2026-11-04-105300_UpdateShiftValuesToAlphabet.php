<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateShiftValuesToAlphabet extends Migration
{
    public function up()
    {
        // Update tabel startup
        $this->db->query("UPDATE `startup` SET `shift` = 'A' WHERE `shift` = '1'");
        $this->db->query("UPDATE `startup` SET `shift` = 'B' WHERE `shift` = '2'");
        $this->db->query("UPDATE `startup` SET `shift` = 'C' WHERE `shift` = '3'");

        // Update tabel production
        $this->db->query("UPDATE `production` SET `shift` = 'A' WHERE `shift` = '1'");
        $this->db->query("UPDATE `production` SET `shift` = 'B' WHERE `shift` = '2'");
        $this->db->query("UPDATE `production` SET `shift` = 'C' WHERE `shift` = '3'");

        // Update tabel foregoing
        $this->db->query("UPDATE `foregoing` SET `shift` = 'A' WHERE `shift` = '1'");
        $this->db->query("UPDATE `foregoing` SET `shift` = 'B' WHERE `shift` = '2'");
        $this->db->query("UPDATE `foregoing` SET `shift` = 'C' WHERE `shift` = '3'");
    }

    public function down()
    {
        // Rollback tabel startup
        $this->db->query("UPDATE `startup` SET `shift` = '1' WHERE `shift` = 'A'");
        $this->db->query("UPDATE `startup` SET `shift` = '2' WHERE `shift` = 'B'");
        $this->db->query("UPDATE `startup` SET `shift` = '3' WHERE `shift` = 'C'");

        // Rollback tabel production
        $this->db->query("UPDATE `production` SET `shift` = '1' WHERE `shift` = 'A'");
        $this->db->query("UPDATE `production` SET `shift` = '2' WHERE `shift` = 'B'");
        $this->db->query("UPDATE `production` SET `shift` = '3' WHERE `shift` = 'C'");

        // Rollback tabel foregoing
        $this->db->query("UPDATE `foregoing` SET `shift` = '1' WHERE `shift` = 'A'");
        $this->db->query("UPDATE `foregoing` SET `shift` = '2' WHERE `shift` = 'B'");
        $this->db->query("UPDATE `foregoing` SET `shift` = '3' WHERE `shift` = 'C'");
    }
}