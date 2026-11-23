<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

class SimulateWeekly extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'simulate:weekly';
    protected $description = 'Simulasi input data Checkseet hari Senin sebelum jam 06:59';

    public function run(array $params)
    {
        // Mengunci waktu simulasi ke hari Senin (contoh: 26 Oktober 2026 jam 06:30 pagi)
        $simulatedTime = Time::parse('2026-10-26 07:00:00');
        CLI::write('Waktu Input Simulasi: ' . $simulatedTime->toDateTimeString(), 'yellow');

        // format('N') menghasilkan 1 untuk Senin. 'H' untuk format 24 jam, 'i' untuk menit.
        $hari = $simulatedTime->format('N');
        $jam = (int) $simulatedTime->format('H');
        $menit = (int) $simulatedTime->format('i');

        $masukMingguLalu = false;

        // Logika pengecekan: Jika Senin dan waktu masih <= 06:59
        if ($hari == 1) {
            if ($jam < 6 || ($jam == 6 && $menit <= 59)) {
                $masukMingguLalu = true;
            }
        }

        if ($masukMingguLalu) {
            CLI::write('Berhasil: Data dieksekusi masuk ke weekly approval MINGGU SEBELUMNYA.', 'green');

            // Letakkan $model->insert() atau pemanggilan helper Checkseet Anda di sini
            // untuk memastikan datanya benar-benar masuk ke tabel yang tepat.
        } else {
            CLI::write('Gagal/Beda Kondisi: Data masuk ke weekly approval MINGGU INI.', 'red');
        }
    }
}