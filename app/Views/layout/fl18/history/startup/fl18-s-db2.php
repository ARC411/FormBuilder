<?php require_once(dirname(__FILE__) . '/../../../fillHistory.php'); ?>
<?php
if (!function_exists('resolveStartupEmpId')) {
    function resolveStartupEmpId($val) {
        static $empMap = null;
        $clean = trim((string)$val);
        if ($clean === '' || $clean === '-') {
            return '-';
        }
        if (preg_match('/^[0-9A-Za-z_-]{3,12}$/', $clean) && preg_match('/\d/', $clean)) {
            return $clean;
        }
        if ($empMap === null) {
            $empMap = [];
            try {
                $db2 = \Config\Database::connect('second');
                $rows = $db2->query("SELECT empid, firstname, midname, lastname FROM tbempinfa")->getResultArray();
                foreach ($rows as $r) {
                    $eid = trim((string)($r['empid'] ?? ''));
                    if ($eid === '') continue;
                    $full = strtoupper(trim(preg_replace('/\s+/', ' ', ($r['firstname'] ?? '') . ' ' . ($r['midname'] ?? '') . ' ' . ($r['lastname'] ?? ''))));
                    if ($full !== '') $empMap[$full] = $eid;
                    $fl = strtoupper(trim(preg_replace('/\s+/', ' ', ($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? ''))));
                    if ($fl !== '' && !isset($empMap[$fl])) $empMap[$fl] = $eid;
                }
            } catch (\Throwable $e) {
                // Abaikan jika gagal koneksi
            }
        }
        $key = strtoupper((string)preg_replace('/\s+/', ' ', $clean));
        return $empMap[$key] ?? $clean;
    }
}

if (!function_exists('formatStartupCheckedEmpId')) {
    function formatStartupCheckedEmpId($row) {
        if (!is_array($row)) return '-';
        $rawStatus = strtolower(trim((string)($row['status'] ?? '')));
        if ($rawStatus === '' || $rawStatus === '-' || (strpos($rawStatus, 'approve') === false && strpos($rawStatus, 'bypass') === false && strpos($rawStatus, 'check') === false)) {
            return '-';
        }
        $spv = trim((string)($row['supervisor'] ?? ''));
        $ldr = trim((string)($row['leader'] ?? ''));
        $frm = trim((string)($row['foreman'] ?? ''));
        if ($spv !== '' && $spv !== '-') {
            return resolveStartupEmpId($spv) . ' (Supervisor)';
        }
        if ($ldr !== '' && $ldr !== '-') {
            return resolveStartupEmpId($ldr) . ' (Leader)';
        }
        if ($frm !== '' && $frm !== '-') {
            return resolveStartupEmpId($frm) . ' (Foreman)';
        }
        return '-';
    }
}
?>
<div class="table-responsive mt-3" id="fl18-s-db2">
<?= csrf_field(); ?>
<?php $lenData = count($alldata); ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="<?= 4 + $lenData ?>" scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" colspan="2" rowspan="2">Start Up Check Item</th>
            <th scope="col" rowspan="2">Standard</th>
            <th scope="col" colspan="<?= $lenData > 0 ? $lenData : 1 ?>">Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>1</td>
            <td colspan="2">@ Air Pressure 1</td>
            <td>0.05 ~ 0.15 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par001'],0.05,0.15); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>2</td>
            <td colspan="2">@ Air Pressure 2</td>
            <td>0.35 ~ 0.50 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par002'],0.35,0.5); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>3</td>
            <td colspan="2">Middle Push Up</td>
            <td>-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par003'],-9999999,-80); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>4</td>
            <td colspan="2">@ Needle Push Up</td>
            <td>0.30 ~ 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par004'],0.3,0.6); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">5</td>
            <td rowspan="2" colspan="2">* Collet Cleaning <br> (Bersihkan dengan Cotton Bud yang dibasahi dengan Alkohol)</td>
<<<<<<< HEAD
            <td >D/B Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par005'] ?? '-'); ?></td>
=======
            <td>D/B Collet</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
<<<<<<< HEAD
            <td >Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par006'] ?? '-'); ?></td>
=======
            <td>Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>6</td>
            <td colspan="3">* Checking contact probe (Check jika patah atau bengkok ganti yang baru)</td>
<<<<<<< HEAD
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par007'] ?? '-'); ?></td>
=======
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>7</td>
            <td colspan="2">@ Colled Load</td>
            <td>0.60 ~ 1.00N (60 ~ 100 gr)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>8</td>
            <td colspan="3">* Pengecekan posisi die bonding di monitor</td>
<<<<<<< HEAD
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par009'] ?? '-'); ?></td>
=======
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="4">9</td>
            <td rowspan="4">@ Dispenser indication</td>
<<<<<<< HEAD
            <td >Air Pressure</td>
            <td >0.01 ~ 0.2 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
<<<<<<< HEAD
                <td colspan="col"><?= (string)($alldata[$i]['par010'] ?? '-'); ?></td>
=======
=======
            <td>Air Pressure</td>
            <td>0.01 ~ 0.2 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par010'],0.01,0.2); ?></td>
>>>>>>> 16e0f8e3cc94470898f083d8e9c97e8e2f874487
            <?php } ?>
        </tr>

        <tr>
<<<<<<< HEAD
            <td >Dispense Time</td>
            <td >10 ~ 100 ms</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
<<<<<<< HEAD
                <td colspan="col"><?= (string)($alldata[$i]['par011'] ?? '-'); ?></td>
=======
=======
            <td>Dispense Time</td>
            <td>10 ~ 100 ms</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par011'],10,100); ?></td>
>>>>>>> 16e0f8e3cc94470898f083d8e9c97e8e2f874487
            <?php } ?>
        </tr>
        
        <tr>
<<<<<<< HEAD
            <td >Vacuum pressure</td>
            <td >-0.30 ~ -0.10 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
<<<<<<< HEAD
                <td colspan="col"><?= (string)($alldata[$i]['par012'] ?? '-'); ?></td>
=======
=======
            <td>Vacuum pressure</td>
            <td>-0.30 ~ -0.10 kPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par012'],-0.30,-0.10); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="2">Nilai Air pressure, Dispenser time , vacuum pressure disesuaikan agar mendapatkan nilai <br>/hasil ag paste yg standard</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par024'] ?? '-'); ?></td>
>>>>>>> 16e0f8e3cc94470898f083d8e9c97e8e2f874487
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">* Check kondisi Ag Paste di monitor</td>
<<<<<<< HEAD
            <td >Posisi</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par013'] ?? '-'); ?></td>
=======
            <td>Posisi</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
<<<<<<< HEAD
            <td >Quantity</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par014'] ?? '-'); ?></td>
=======
            <td>Quantity</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">11</td>
            <td rowspan="2" colspan="2">* Check arah Ion Blower Fan</td>
<<<<<<< HEAD
            <td >Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par015'] ?? '-'); ?></td>
=======
            <td>Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
<<<<<<< HEAD
            <td >Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par016'] ?? '-'); ?></td>
=======
            <td>Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>12</td>
            <td colspan="2">* Cleaning Machine</td>
<<<<<<< HEAD
            <td >Bersihkan mesin dan sekitarnya dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par017'] ?? '-'); ?></td>
=======
            <td>Bersihkan mesin dan sekitarnya dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>13</td>
            <td colspan="2">* Pemeriksaan grounding <br> (Baut pengunci di conductive mat, banana plug, koneksi kabel grounding)</td>
            <td>Terpasang bagus/tidak longgar, lepas, putus</td>
<<<<<<< HEAD
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par018'] ?? '-'); ?></td>
=======
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>14</td>
            <td colspan="2">* Cek Kondisi Collet</td>
<<<<<<< HEAD
            <td >Jika terjadi kerusakan atau diluar standard, <br> lakukan pergantian dengan yang baru. <br> Jika OK lanjutkan proses </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par019'] ?? '-'); ?></td>
=======
            <td>Jika terjadi kerusakan atau diluar standard, <br> lakukan pergantian dengan yang baru. <br> Jika OK lanjutkan proses </td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td>15</td>
            <td colspan="2">* Cek Kondisi Mesin terhadap Pick Up Miss Chip</td>
<<<<<<< HEAD
            <td >Berdasarkan Sensor</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par020'] ?? '-'); ?></td>
=======
            <td>Berdasarkan Sensor</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">16</td>
            <td rowspan="2" colspan="2">Collet Bonding Arm Check (gunakan jari untuk mengecek <br> pergerakan bonding collet)</td>
<<<<<<< HEAD
            <td >Arah depan-belakang (fix tidak bergerak)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par021'] ?? '-'); ?></td>
=======
            <td>Arah depan-belakang (fix tidak bergerak)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
<<<<<<< HEAD
            <td >Arah atas-bawah</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par022'] ?? '-'); ?></td>
=======
            <td>Arah atas-bawah</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Operator</td>
            <?php for($i = 0; $i < $lenData; $i++) { 
                $opEmp = !empty($alldata[$i]['empid']) ? resolveStartupEmpId($alldata[$i]['empid']) : resolveStartupEmpId($alldata[$i]['name'] ?? '-');
            ?>
                <td colspan="col"><?= esc($opEmp); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Checked</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= esc(formatStartupCheckedEmpId($alldata[$i] ?? null)); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Note</td>
<<<<<<< HEAD
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par023'] ?? '-'); ?></td>
            <?php } ?>
        </tr>

<<<<<<< HEAD
        <tr>
            <td colspan="4">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['status'] ?? '-'); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <?php
                    // 1. Ambil role, pangkas spasi, dan paksa jadi huruf kecil (menghindari error case-sensitive)
                    $roleKey = strtolower(trim((string)$alldata[$i]['role']));
                    
                    // 2. Prioritas Utama: Cari berdasarkan Role-nya (misal: 'operator', 'leader')
                    if ($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey]) && $alldata[$i][$roleKey] !== '-') {
                        echo '<td colspan="col">'. (string)$alldata[$i][$roleKey] . '</td>';
                    } 
                    // 3. Fallback 1: Jika gagal, langsung cari ke kolom 'operator'
                    elseif (isset($alldata[$i]['operator']) && $alldata[$i]['operator'] !== '-') {
                        echo '<td colspan="col">'. (string)$alldata[$i]['operator'] . '</td>';
                    }
                    // 4. Fallback 2: Jika masih gagal, cari ke kolom 'nama'
                    elseif (isset($alldata[$i]['nama']) && $alldata[$i]['nama'] !== '-') {
                        echo '<td colspan="col">'. (string)$alldata[$i]['nama'] . '</td>';
                    }
                    // 5. Jika memang benar-benar kosong dari database, baru cetak strip
                    else {
                        echo '<td colspan="col">-</td>';
                    }
                ?>
            <?php } ?>
        </tr>

<<<<<<< HEAD
        <tr>
            <td colspan="4">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= isset($alldata[$i]['updated_at']) ? date('H:i', strtotime((string)$alldata[$i]['updated_at'])) : '-'; ?></td>
            <?php } ?>
        </tr>
=======
=======
>>>>>>> ba8ca53db7ace9995c2132bd84acc42099457ffb

>>>>>>> 16e0f8e3cc94470898f083d8e9c97e8e2f874487
=======
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>
>>>>>>> bb968fd6dde1440a711712a1783bc333af4a1f61
    </tbody>   
</table>
</div>