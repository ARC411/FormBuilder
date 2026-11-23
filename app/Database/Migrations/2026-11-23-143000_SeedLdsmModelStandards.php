<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedLdsmModelStandards extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('model_list')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $procCode = 'sl56-p-ldsm';

        $modelsData = [
            'GH04C01A2G' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH04C01B2G' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH0382AA2G' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH04W10A2GC7' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.24, 'max' => 1.30, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH04W10A2GC5' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.020, 'max' => 0.080, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.24, 'max' => 1.30, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH0406AA2G05' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.32, 'max' => 1.38, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH0406AA2G07' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.32, 'max' => 1.38, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH0393AA2G' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH04V01series' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH05series' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => -0.015, 'max' => 0.015, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => -0.080, 'max' => 0.020, 'active' => 1],
                'Point 6 (Z)'  => ['min' => 1.47, 'max' => 1.53, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
            'GH0637AA2G' => [
                'Point 1 (θc)' => ['min' => -3, 'max' => 3, 'active' => 1],
                'Point 2 (Xc)' => ['min' => -0.050, 'max' => 0.050, 'active' => 1],
                'Point 3 (Zo)' => ['min' => 0.010, 'max' => 0.030, 'active' => 1],
                'Point 4 (X)'  => ['min' => -0.040, 'max' => 0.040, 'active' => 1],
                'Point 5 (Y)'  => ['min' => null, 'max' => null, 'active' => 0], // OFF (Tidak Diukur)
                'Point 6 (Z)'  => ['min' => 1.62, 'max' => 1.68, 'active' => 1],
                'Point 7 (θ)'  => ['min' => -1.5, 'max' => 1.5, 'active' => 1],
            ],
        ];

        foreach ($modelsData as $mName => $stds) {
            $exist = $db->table('model_list')
                        ->where('process_code', $procCode)
                        ->where('model_name', $mName)
                        ->get()
                        ->getRow();

            $payload = [
                'process_code'   => $procCode,
                'model_name'     => $mName,
                'standards_json' => json_encode($stds, JSON_UNESCAPED_UNICODE),
                'updated_at'     => $now,
            ];

            if ($exist) {
                $db->table('model_list')->where('id', $exist->id)->update($payload);
            } else {
                $payload['created_at'] = $now;
                $db->table('model_list')->insert($payload);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('model_list')) {
            $db->table('model_list')->where('process_code', 'sl56-p-ldsm')->delete();
        }
    }
}
