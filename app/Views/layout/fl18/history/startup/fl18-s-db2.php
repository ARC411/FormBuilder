<?php require_once(dirname( __FILE__ ). '/'.'../../../fillHistory.php'); ?>
<div class="table-responsive mt-3" id="fl18-s-db2">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" colspan="2" rowspan="2">Start Up Check Item</th>
            <th scope="col" rowspan="2">Standard</th>
            <th scope="col" colspan=<?= $lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td colspan="2">@ Air Pressure 1</td>
            <td >0.05 ~ 0.15 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par001'],0.05,0.15); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >2</td>
            <td colspan="2">@ Air Pressure 2</td>
            <td >0.35 ~ 0.50 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par002'],0.35,0.5); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td colspan="2">Middle Push Up</td>
            <td >-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par003'],-9999999,-80); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td colspan="2">@ Needle Push Up</td>
            <td >0.30 ~ 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par004'],0.3,0.6); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">5</td>
            <td rowspan="2" colspan="2">* Collet Cleaning <br> (Bersihkan dengan Cotton Bud yang dibasahi dengan Alkohol)</td>
            <td >D/B Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td colspan="3">* Checking contact probe (Check jika patah atau bengkok ganti yang baru)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >7</td>
            <td colspan="2">@ Colled Load</td>
            <td >0.60 ~ 1.00N (60 ~ 100 gr)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par008'],0.6,1); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >8</td>
            <td colspan="3">* Pengecekan posisi die bonding di monitor</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="4">9</td>
            <td rowspan="4">@ Dispenser indication</td>
            <td >Air Pressure</td>
            <td >0.01 ~ 0.2 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par010'],0.01,0.2); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Dispense Time</td>
            <td >10 ~ 100 ms</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par011'],10,100); ?></td>
            <?php } ?>
        </tr>
       
        <tr>
            <td >Vacuum pressure</td>
            <td >-0.30 ~ -0.10 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par012'],-0.30,-0.10); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="2">Nilai Air pressure, Dispenser time , vacuum pressure disesuaikan agar mendapatkan nilai <br>/hasil ag paste yg standard</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par024'] ?? '-'); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">* Check kondisi Ag Paste di monitor</td>
            <td >Posisi</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Quantity</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">11</td>
            <td rowspan="2" colspan="2">* Check arah Ion Blower Fan</td>
            <td >Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>12</td>
            <td colspan="2">* Cleaning Machine</td>
            <td >Bersihkan mesin dan sekitarnya dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >13</td>
            <td colspan="2">* Pemeriksaan grounding <br> (Baut pengunci di conductive mat, banana plug, koneksi kabel grounding)</td>
            <td>Terpasang bagus/tidak longgar, lepas, putus</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>14</td>
            <td colspan="2">* Cek Kondisi Collet</td>
            <td >Jika terjadi kerusakan atau diluar standard, <br> lakukan pergantian dengan yang baru. <br> Jika OK lanjutkan proses </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >15</td>
            <td colspan="2">* Cek Kondisi Mesin terhadap Pick Up Miss Chip</td>
            <td >Berdasarkan Sensor</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">16</td>
            <td rowspan="2" colspan="2">Collet Bonding Arm Check (gunakan jari untuk mengecek <br> pergerakan bonding collet)</td>
            <td >Arah depan-belakang (fix tidak bergerak)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Arah atas-bawah</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
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


    </tbody>   
    </table>
</div>