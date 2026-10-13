<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\WeeklyApprovalModel;

class Cronjob extends BaseController
{
    public function closeWeeklyData()
    {
        // Pastikan hanya bisa dijalankan melalui CLI / Cronjob untuk keamanan
        if (!is_cli()) {
            return "Akses ditolak. Hanya untuk Cronjob.";
        }

        date_default_timezone_set('Asia/Jakarta');
        
        $startupModel = new StartupModel();
        $weeklyModel = new WeeklyApprovalModel();

        // Mengambil rentang tanggal minggu ini (Senin - Minggu)
        // Karena Cron berjalan di hari Minggu 23:59, maka "this week" akurat.
        $startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $endOfWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        // 1. Cek apakah ada data startup di minggu ini yang sudah di-check harian tapi belum masuk mingguan
        // Asumsi data sudah di-approve harian memiliki status yang tidak 'Pending' (Bisa disesuaikan logika querynya)
        $db = \Config\Database::connect();
        $builder = $db->table('startup');
        $builder->where('created_at >=', $startOfWeek);
        $builder->where('created_at <=', $endOfWeek);
        $builder->where('weekly_id', null); // Yang belum masuk form mingguan
        
        $pendingData = $builder->countAllResults(false);

        if ($pendingData > 0) {
            // 2. Buat ID Weekly Approval baru
            $weeklyData = [
                'period_start'      => date('Y-m-d', strtotime($startOfWeek)),
                'period_end'        => date('Y-m-d', strtotime($endOfWeek)),
                'status_qc'         => 'Pending',
                'status_production' => 'Pending'
            ];
            $weeklyModel->insert($weeklyData);
            $newWeeklyId = $weeklyModel->getInsertID();

            // 3. Update semua data startup minggu ini agar masuk ke id mingguan tersebut
            $builder->update(['weekly_id' => $newWeeklyId]);

            // 4. Kirim Notifikasi (Contoh menggunakan Email Bawaan CI4)
            $this->sendNotification($newWeeklyId, $weeklyData['period_start'], $weeklyData['period_end']);

            echo "Cronjob Berhasil: Minggu " . $weeklyData['period_start'] . " s/d " . $weeklyData['period_end'] . " di-close. (Total: $pendingData dokumen)\n";
        } else {
            echo "Cronjob Selesai: Tidak ada data baru untuk minggu ini.\n";
        }
    }

    private function sendNotification($weeklyId, $start, $end)
    {
        $email = \Config\Services::email();

        // Ganti dengan email departemen terkait
        $to_emails = ['qc_dept@domain.com', 'production_dept@domain.com']; 
        
        $email->setTo($to_emails);
        $email->setSubject("Approval Mingguan Diperlukan - Periode $start s/d $end");
        
        $message = "
        <h3>Halo Tim QC & Production,</h3>
        <p>Data Checksheet Mingguan untuk periode <b>$start</b> sampai <b>$end</b> telah di-close secara otomatis oleh sistem.</p>
        <p>Mohon segera lakukan pengecekan dan Approval melalui sistem Checksheet.</p>
        <p><b>ID Approval Mingguan:</b> #$weeklyId</p>
        <br>
        <p>Terima kasih.</p>
        ";
        
        $email->setMessage($message);
        $email->send();
    }
}