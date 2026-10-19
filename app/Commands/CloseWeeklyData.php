<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\StartupModel;
use App\Models\WeeklyApprovalModel;

class CloseWeeklyData extends BaseCommand
{
    protected $group       = 'Cronjob';
    protected $name        = 'cronjob:closeweeklydata';
    protected $description = 'Menutup data startup mingguan dan memasukkannya ke tabel Weekly Approval.';
    protected $usage       = 'cronjob:closeweeklydata [options]';
    protected $options     = [
        '--all'       => 'Tarik semua data startup yang belum memiliki weekly_id dan kelompokkan per minggu',
        '--last-week' => 'Tutup data minggu lalu (Senin s/d Minggu lalu)',
    ];

    public function run(array $params)
    {
        date_default_timezone_set('Asia/Jakarta');

        $db          = \Config\Database::connect();
        $weeklyModel = new WeeklyApprovalModel();

        $isAll      = CLI::getOption('all') !== null;
        $isLastWeek = CLI::getOption('last-week') !== null;

        if ($isAll) {
            $unassigned = $db->table('startup')
                ->select("MIN(created_at) as min_date, MAX(created_at) as max_date, COUNT(*) as total")
                ->where('weekly_id', null)
                ->get()->getRowArray();

            if (empty($unassigned) || empty($unassigned['total']) || $unassigned['total'] == 0) {
                CLI::write("Tidak ada data startup yang belum masuk ke weekly approval.", 'yellow');
                return;
            }

            CLI::write("Ditemukan " . $unassigned['total'] . " baris data yang belum memiliki weekly_id. Memproses...", 'cyan');

            $pendingRows = $db->table('startup')
                ->select('id, created_at')
                ->where('weekly_id', null)
                ->get()->getResultArray();

            $weeks = [];
            foreach ($pendingRows as $row) {
                $time = strtotime($row['created_at']);
                $monday = date('Y-m-d', strtotime('monday this week', $time));
                $sunday = date('Y-m-d', strtotime('sunday this week', $time));
                $weekKey = $monday . '_' . $sunday;

                if (!isset($weeks[$weekKey])) {
                    $weeks[$weekKey] = [
                        'start' => $monday,
                        'end'   => $sunday,
                        'ids'   => []
                    ];
                }
                $weeks[$weekKey]['ids'][] = $row['id'];
            }

            foreach ($weeks as $wKey => $wData) {
                $existing = $weeklyModel->where('period_start', $wData['start'])
                                        ->where('period_end', $wData['end'])
                                        ->first();

                if ($existing) {
                    $weeklyId = $existing['id'];
                } else {
                    $weeklyId = $weeklyModel->insert([
                        'period_start'      => $wData['start'],
                        'period_end'        => $wData['end'],
                        'status_qc'         => 'Pending',
                        'status_production' => 'Pending',
                    ]);
                }

                $chunkIds = array_chunk($wData['ids'], 500);
                foreach ($chunkIds as $chunk) {
                    $db->table('startup')->whereIn('id', $chunk)->update(['weekly_id' => $weeklyId]);
                }

                CLI::write("Periode " . $wData['start'] . " s/d " . $wData['end'] . " -> Weekly ID #" . $weeklyId . " (Total: " . count($wData['ids']) . " baris)", 'green');
            }

            CLI::write("Selesai memproses seluruh data mingguan.", 'green');
            return;
        }

        if ($isLastWeek) {
            $startOfWeek = date('Y-m-d 00:00:00', strtotime('monday last week'));
            $endOfWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
        } else {
            $startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $endOfWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        }

        $builder = $db->table('startup');
        $builder->where('created_at >=', $startOfWeek);
        $builder->where('created_at <=', $endOfWeek);
        $builder->where('weekly_id', null);

        $pendingData = $builder->countAllResults(false);

        if ($pendingData > 0) {
            $periodStart = date('Y-m-d', strtotime($startOfWeek));
            $periodEnd   = date('Y-m-d', strtotime($endOfWeek));

            $existing = $weeklyModel->where('period_start', $periodStart)
                                    ->where('period_end', $periodEnd)
                                    ->first();
            if ($existing) {
                $newWeeklyId = $existing['id'];
            } else {
                $newWeeklyId = $weeklyModel->insert([
                    'period_start'      => $periodStart,
                    'period_end'        => $periodEnd,
                    'status_qc'         => 'Pending',
                    'status_production' => 'Pending'
                ]);
            }

            $builder->update(['weekly_id' => $newWeeklyId]);

            CLI::write("Cronjob Berhasil: Minggu $periodStart s/d $periodEnd di-close ke Weekly ID #$newWeeklyId (Total: $pendingData baris).", 'green');
        } else {
            CLI::write("Cronjob Selesai: Tidak ada data baru tanpa weekly_id untuk rentang $startOfWeek s/d $endOfWeek.", 'yellow');
            CLI::write("Tips: Gunakan 'php spark cronjob:closeweeklydata --all' untuk menutup seluruh data yang belum terhubung.", 'cyan');
        }
    }
}