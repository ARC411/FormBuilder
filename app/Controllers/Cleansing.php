<?php

namespace App\Controllers;

class Cleansing extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // 1. Tarik Kamus Suci (Semua mesin master dari database)
        $masterMachines = $db->table('machine_list')->get()->getResult();
        
        // 2. Tiga tabel yang akan dieksekusi sekaligus
        $tables = ['startup', 'production', 'foregoing'];
        
        $html = "<html><head><title>Preview 3 Tabel Cleansing</title>";
        $html .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'></head>";
        $html .= "<body class='p-4'>";
        $html .= "<div class='alert alert-info d-flex justify-content-between align-items-center mb-4 shadow-sm'><div><b><i class='fas fa-clock mr-1'></i> Penyelarasan Tanggal Shift C Malam (00:00 - 06:59 WIB):</b> Butuh memundurkan tanggal data Shift C yang masuk ke tanggal keesokan harinya?</div><a href='" . base_url('cleansing/shiftc') . "' class='btn btn-primary btn-sm font-weight-bold'>Buka Fitur Penyelarasan Shift C</a></div>";
        $html .= "<h3 class='mb-3'>Mode Simulasi: Penyelarasan GLOBAL 3 Tabel Utama</h3>";
        $html .= "<p>Sistem ini mencocokkan otomatis seluruh anomali <b>machno</b> dari tabel <b>STARTUP, PRODUCTION, dan FOREGOING</b> dengan tabel master <b>machine_list</b>. Data di bawah ini BELUM diubah di database.</p>";
        
        $html .= "<table class='table table-bordered table-sm table-striped'><thead><tr class='table-warning'>";
        $html .= "<th>Tabel</th><th>ID Data</th><th>Process</th><th>Machno (Data Mentah)</th><th>Machno (Akan Diselaraskan)</th>";
        $html .= "</tr></thead><tbody>";
        
        $count = 0;

        foreach ($tables as $table) {
            // Tarik seluruh data di tabel masing-masing
            $query = $db->table($table)->get();
            
            foreach ($query->getResult() as $row) {
                $original = $row->machno;
                
                // Ekstrak nama device dari kolom process (Contoh: "fl18-s-db2" -> dapet "fl18")
                $device = '';
                $processParts = explode('-', $row->process);
                if (count($processParts) > 0) {
                    $device = $processParts[0];
                }
                
                // Cocokkan data asli dengan kamus master
                $cleaned = $this->matchMachine($original, $device, $masterMachines);
                
                // Jika hasil pembersihan berbeda dengan data asli, tampilkan di tabel
                if ($original !== $cleaned) {
                    $count++;
                    $html .= "<tr>";
                    $html .= "<td><span class='badge badge-dark'>" . strtoupper($table) . "</span></td>";
                    $html .= "<td>{$row->id}</td>";
                    $html .= "<td>{$row->process}</td>";
                    $html .= "<td class='text-danger'>'{$original}'</td>";
                    $html .= "<td class='text-success font-weight-bold'>'{$cleaned}'</td>";
                    $html .= "</tr>";
                }
            }
        }
        
        $html .= "</tbody></table>";
        
        if ($count > 0) {
            $html .= "<div class='mt-4 p-3 bg-light border rounded'>";
            $html .= "<form method='post' action='" . base_url('cleansing/execute') . "'>";
            $html .= "<h5>Pola Global Terdeteksi!</h5>";
            $html .= "<p>Klik tombol di bawah untuk menyelaraskan <b>{$count} data</b> secara permanen ke database.</p>";
            $html .= "<button type='submit' class='btn btn-danger btn-lg' onclick=\"return confirm('Yakin eksekusi {$count} data ke database?');\">EKSEKUSI UPDATE KE 3 TABEL SEKALIGUS</button>";
            $html .= "</form></div>";
        } else {
            $html .= "<div class='alert alert-success mt-4'>Luar Biasa! Seluruh machno di 3 tabel utama (Startup, Production, Foregoing) sudah selaras 100% dengan machine_list.</div>";
        }
        
        $html .= "</body></html>";
        
        return $html;
    }

    public function execute()
    {
        $db = \Config\Database::connect();
        $masterMachines = $db->table('machine_list')->get()->getResult();
        $tables = ['startup', 'production', 'foregoing'];
        $count = 0;

        foreach ($tables as $table) {
            $query = $db->table($table)->get();
            
            foreach ($query->getResult() as $row) {
                $original = $row->machno;
                
                $device = '';
                $processParts = explode('-', $row->process);
                if (count($processParts) > 0) {
                    $device = $processParts[0];
                }
                
                $cleaned = $this->matchMachine($original, $device, $masterMachines);
                
                if ($original !== $cleaned) {
                    // Tembak update langsung ke database (Sesuai tabel yang sedang dilooping)
                    $db->table($table)->where('id', $row->id)->update(['machno' => $cleaned]);
                    $count++;
                }
            }
        }

        $html = "<html><head><title>Sukses Eksekusi</title>";
        $html .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'></head>";
        $html .= "<body class='p-5 text-center'>";
        $html .= "<h1 class='text-success'>EKSEKUSI GLOBAL SELESAI!</h1>";
        $html .= "<h4><b>{$count} data anomali di tabel STARTUP, PRODUCTION, dan FOREGOING</b> berhasil diselaraskan.</h4>";
        $html .= "<a href='" . base_url('cleansing') . "' class='btn btn-primary mt-3'>Cek Ulang Data</a>";
        $html .= "</body></html>";

        return $html;
    }

    // FUNGSI PINTAR DYNAMIC FUZZY MATCHING
    private function matchMachine($raw, $device, $masterMachines) 
    {
        if (empty($raw) || $raw === '-' || $raw === 'null') return $raw;

        $rawClean = strtoupper(preg_replace('/[^A-Z0-9]/', '', $raw));
        
        preg_match_all('/\d+/', $raw, $rawMatches);
        $rawNums = implode('', $rawMatches[0]);

        $candidates = [];

        foreach ($masterMachines as $master) {
            $masterDevice = explode('-', $master->process_code)[0];
            if (strtolower($masterDevice) !== strtolower($device)) {
                continue;
            }

            $masterName = $master->machine_name;
            $masterClean = strtoupper(preg_replace('/[^A-Z0-9]/', '', $masterName));
            
            preg_match_all('/\d+/', $masterName, $masterMatches);
            $masterNums = implode('', $masterMatches[0]);

            if ($rawClean === $masterClean) {
                return $masterName;
            }

            if (strlen($rawClean) >= 3 && strpos($masterClean, $rawClean) !== false) {
                return $masterName;
            }

            if ($rawNums !== '' && $rawNums === $masterNums) {
                $candidates[] = $masterName;
            }
        }

        if (count($candidates) === 1) {
            return $candidates[0];
        }

        return $raw;
    }

    public function shiftc()
    {
        $db = \Config\Database::connect();
        $targetDate = $this->request->getGet('target_date') ?: '2026-11-05';
        $prevDate = date('Y-m-d', strtotime($targetDate . ' -1 day'));

        $tables = ['foregoing', 'production', 'startup'];
        $candidates = [];

        foreach ($tables as $tbl) {
            $sql = "SELECT id, number, device, process, machno, model, lotno, shift, `group`, empid, name, created_at
                    FROM `{$tbl}`
                    WHERE DATE(created_at) = ? 
                      AND TIME(created_at) < '07:00:00'
                      AND (shift = 'C' OR shift = '3')
                    ORDER BY id ASC";
            $rows = $db->query($sql, [$targetDate])->getResultArray();
            foreach ($rows as $r) {
                $r['table_source'] = $tbl;
                $candidates[] = $r;
            }
        }

        $html = "<html><head><title>Penyelarasan Tanggal Shift C</title>";
        $html .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'></head>";
        $html .= "<body class='p-4 bg-light'>";
        $html .= "<div class='container-fluid'>";
        $html .= "<div class='d-flex justify-content-between align-items-center mb-3'>";
        $html .= "<h3>Penyelarasan Tanggal Shift C Malam (00:00 - 06:59 WIB)</h3>";
        $html .= "<a href='" . base_url('cleansing') . "' class='btn btn-outline-secondary'>Kembali ke Cleansing Mesin</a>";
        $html .= "</div>";

        $html .= "<div class='card mb-4 shadow-sm border-0'><div class='card-body'>";
        $html .= "<p class='mb-2'>Fitur ini mendeteksi data checksheet Shift C yang diinput pada tanggal <b>" . esc($targetDate) . "</b> antara pukul <b>00:00 s/d 06:59 WIB</b> yang seharusnya masuk sebagai tanggal operasional shift kemarin (<b>" . esc($prevDate) . "</b>).</p>";
        $html .= "<form method='get' class='form-inline'>";
        $html .= "<label class='mr-2 font-weight-bold'>Target Tanggal Input:</label>";
        $html .= "<input type='date' name='target_date' class='form-control form-control-sm mr-2' value='" . esc($targetDate) . "'>";
        $html .= "<button type='submit' class='btn btn-sm btn-info'>Filter Tanggal</button>";
        $html .= "</form>";
        $html .= "</div></div>";

        $count = count($candidates);

        if ($count > 0) {
            $html .= "<div class='alert alert-warning'><b>Ditemukan " . $count . " baris data</b> Shift C pada tgl " . esc($targetDate) . " (00:00 - 06:59 WIB) yang siap dimundurkan ke tgl " . esc($prevDate) . ".</div>";
            $html .= "<table class='table table-bordered table-sm table-striped bg-white shadow-sm'><thead><tr class='table-dark'>";
            $html .= "<th>Tabel</th><th>No/ID</th><th>Process</th><th>Mach. No</th><th>Model</th><th>Lot No</th><th>Shift/Group</th><th>Operator</th><th>Tanggal Asli</th><th>Tanggal Baru (H-1)</th>";
            $html .= "</tr></thead><tbody>";

            foreach ($candidates as $row) {
                $newCreated = date('Y-m-d H:i:s', strtotime($row['created_at'] . ' -1 day'));
                $html .= "<tr>";
                $html .= "<td><span class='badge badge-primary'>" . strtoupper($row['table_source']) . "</span></td>";
                $html .= "<td>" . esc($row['number'] ?? $row['id']) . "</td>";
                $html .= "<td>" . esc($row['process'] ?? '-') . "</td>";
                $html .= "<td>" . esc($row['machno'] ?? '-') . "</td>";
                $html .= "<td>" . esc($row['model'] ?? '-') . "</td>";
                $html .= "<td>" . esc($row['lotno'] ?? '-') . "</td>";
                $html .= "<td>" . esc($row['shift']) . " / " . esc($row['group'] ?? '-') . "</td>";
                $html .= "<td>" . esc($row['empid'] ?? '-') . " (" . esc($row['name'] ?? '-') . ")</td>";
                $html .= "<td class='text-danger font-weight-bold'>" . date('d/m/Y H:i:s', strtotime($row['created_at'])) . "</td>";
                $html .= "<td class='text-success font-weight-bold'>" . date('d/m/Y H:i:s', strtotime($newCreated)) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";

            $html .= "<div class='mt-4 p-3 bg-white border rounded shadow-sm'>";
            $html .= "<form method='post' action='" . base_url('cleansing/shiftc/execute') . "'>";
            $html .= csrf_field();
            $html .= "<input type='hidden' name='target_date' value='" . esc($targetDate) . "'>";
            $html .= "<h5>Konfirmasi Eksekusi Perubahan Tanggal</h5>";
            $html .= "<p>Klik tombol di bawah untuk memundurkan tanggal <b>{$count} data di atas</b> menjadi tanggal <b>" . esc($prevDate) . "</b> pada database.</p>";
            $html .= "<button type='submit' class='btn btn-danger btn-lg font-weight-bold' onclick=\"return confirm('Yakin eksekusi memundurkan tanggal {$count} data Shift C ke tanggal {$prevDate}?');\">EKSEKUSI UPDATE KE " . esc($prevDate) . " (H-1)</button>";
            $html .= "</form></div>";
        } else {
            $html .= "<div class='alert alert-success mt-4'>Tidak ditemukan data Shift C abnormal pada tanggal " . esc($targetDate) . " pukul 00:00 - 06:59 WIB. Seluruh data sudah sesuai atau telah diperbarui.</div>";
        }

        $html .= "</div></body></html>";
        return $html;
    }

    public function executeShiftc()
    {
        $db = \Config\Database::connect();
        $targetDate = $this->request->getVar('target_date') ?: '2026-11-05';
        $prevDate = date('Y-m-d', strtotime($targetDate . ' -1 day'));

        $totalForegoing = 0;
        $totalProduction = 0;
        $totalStartup = 0;

        // 1. Update Foregoing
        $db->query("UPDATE foregoing 
                    SET created_at = DATE_SUB(created_at, INTERVAL 1 DAY) 
                    WHERE DATE(created_at) = ? 
                      AND TIME(created_at) < '07:00:00' 
                      AND (shift = 'C' OR shift = '3')", [$targetDate]);
        $totalForegoing = $db->affectedRows();

        // 2. Update Production
        $db->query("UPDATE production 
                    SET created_at = DATE_SUB(created_at, INTERVAL 1 DAY),
                        par008 = CASE 
                            WHEN par008 LIKE CONCAT(?, '%') THEN DATE_SUB(par008, INTERVAL 1 DAY) 
                            ELSE par008 
                        END
                    WHERE DATE(created_at) = ? 
                      AND TIME(created_at) < '07:00:00' 
                      AND (shift = 'C' OR shift = '3')", [$targetDate, $targetDate]);
        $totalProduction = $db->affectedRows();

        // 3. Update Startup
        $db->query("UPDATE startup 
                    SET created_at = DATE_SUB(created_at, INTERVAL 1 DAY) 
                    WHERE DATE(created_at) = ? 
                      AND TIME(created_at) < '07:00:00' 
                      AND (shift = 'C' OR shift = '3')", [$targetDate]);
        $totalStartup = $db->affectedRows();

        $grandTotal = $totalForegoing + $totalProduction + $totalStartup;

        $html = "<html><head><title>Sukses Eksekusi Shift C</title>";
        $html .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'></head>";
        $html .= "<body class='p-5 text-center bg-light'>";
        $html .= "<div class='container card shadow p-5 bg-white'>";
        $html .= "<h1 class='text-success font-weight-bold mb-3'>EKSEKUSI TANGGAL SHIFT C SELESAI!</h1>";
        $html .= "<h4>Total <b>{$grandTotal} baris data</b> dari tanggal <b>{$targetDate}</b> berhasil dimundurkan 1 hari ke tanggal <b>{$prevDate}</b>.</h4>";
        $html .= "<div class='row justify-content-center my-4'>";
        $html .= "<div class='col-md-3'><div class='card bg-light'><div class='card-body'><h6>Foregoing</h6><h3>{$totalForegoing}</h3></div></div></div>";
        $html .= "<div class='col-md-3'><div class='card bg-light'><div class='card-body'><h6>Production</h6><h3>{$totalProduction}</h3></div></div></div>";
        $html .= "<div class='col-md-3'><div class='card bg-light'><div class='card-body'><h6>Startup</h6><h3>{$totalStartup}</h3></div></div></div>";
        $html .= "</div>";
        $html .= "<div class='mt-3'>";
        $html .= "<a href='" . base_url('cleansing/shiftc?target_date=' . $targetDate) . "' class='btn btn-primary mr-2'>Cek Ulang Shift C</a> ";
        $html .= "<a href='" . base_url('history') . "' class='btn btn-success mr-2'>Lihat History Checksheet</a> ";
        $html .= "<a href='" . base_url('input') . "' class='btn btn-secondary'>Form Input Checksheet</a>";
        $html .= "</div>";
        $html .= "</div></body></html>";

        return $html;
    }
}