<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModelListTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'process_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'model_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            // Menyimpan JSON standar batas per baris posisi (X, Y, Z, θ) khusus model ini
            'standards_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['process_code', 'model_name']);
        $this->forge->createTable('model_list', true);

        // Pre-seed 22 Model & Standar X, Y, Z, θ untuk fl18-f-fdb2 sesuai Excel FF-D2-003_rev15
        $seedModels = [
            ['GH15130D8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
            ['GH15030D8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
            ['GH15230H8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
            ['GH15250F8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH15120M8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 2.8],
            ['GH15030R8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 2.8],
            ['GH15230Q8C', 2.41, 2.49, 0.59, 0.67, 0.31, 0.39, -0.5, 0.5, 3.6],
            ['GH16330A8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH1631AA8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH16350A8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH16P32B8C', 2.42, 2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH33540A8C', 2.37, 2.43, null, null, 0.31, 0.39, -1.3, -0.3, 8.7],
            ['GH33540D8C', 2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH3325A8C',  2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH33035A3C', 1.97, 2.03, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH33035D3C', 1.97, 2.03, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH17P28C8CL',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
            ['GH17P28A8C2',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
            ['GH17P28F8CL',2.42, 2.48, null, null, 0.31, 0.39, -1.0, 1.0, 8.7],
            ['GH33035A8C', 2.37, 2.43, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH15130C8C07',2.41,2.49, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
            ['GH15230C8C07',2.42,2.48, null, null, 0.31, 0.39, -0.5, 0.5, 8.7],
        ];

        $batch = [];
        $now = date('Y-m-d H:i:s');
        foreach ($seedModels as $m) {
            $std = [
                'X' => ['min' => $m[1], 'max' => $m[2], 'active' => 1],
                'Y' => ['min' => $m[3], 'max' => $m[4], 'active' => ($m[3] !== null ? 1 : 0)],
                'Z' => ['min' => $m[5], 'max' => $m[6], 'active' => 1],
                'θ' => ['min' => $m[7], 'max' => $m[8], 'active' => 1],
            ];
            $batch[] = [
                'process_code'   => 'fl18-f-fdb2',
                'model_name'     => $m[0],
                'standards_json' => json_encode($std, JSON_UNESCAPED_UNICODE),
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }
        $this->db->table('model_list')->insertBatch($batch);
    }

    public function down()
    {
        $this->forge->dropTable('model_list', true);
    }
}