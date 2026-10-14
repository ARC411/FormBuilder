<div class="table-responsive mt-3" id="sl56-s-ht1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?=6+$lenData?> scope="col"><?php echo nl2br("HOT TEST MACHINE START UP (LD1220TE Type)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="2" rowspan="2" scope="col">Start up Check Item</th>
            <th colspan="3" rowspan="2" scope="col" >Standard</th>
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
            <td colspan="2">@Air Pressure</td>
            <td colspan="3">0.4 ~ 0.6 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="9">2</td>
            <td rowspan="9" colspan="2">Standard Sample Measurement <br> (Pengukuran Standard Sample)</td>
            <td rowspan="9" colspan="2">Ukur standard sample pada setiap holder</td>
        </tr>

        <tr>
            <td >1</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >3</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >4</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >6</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >7</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >8</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td colspan="2">*Check of Contact pin<br>(Memeriksa Kontak Pin)</td>
            <td colspan="3">Check kondisi probe pin holder apakah terjadi pembengkokan, patah.</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">4</td>
            <td rowspan="2" colspan="2">*Check of Classification<br>*(Memeriksa Klasifikasi)</td>
            <td colspan="3">Good Product</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">NG Product</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td colspan="2">Cleaning of Machine<br>(Membersihkan Mesin)</td>
            <td colspan="3">Bersihkan mesin dari debu atau kotoran dengan Lap dan dari device yang terjatuh.</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="9">6</td>
            <td rowspan="9" colspan="2">@Cek temperature holder <br> (Memeriksa temperatur holder)</td>
        </tr>

        <tr>
            <td colspan="3">1</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">3</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">4</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">6</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">7</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">8</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">7</td>
            <td rowspan="2" colspan="2">* Check & Wear safety goggles <br> (Memeriksa & memakai kacamata pengaman)</td>
            <td rowspan="2" colspan="3">Check kondisi kacamata pengaman <br> Ketika mengukur model High Power, harus menggunakan kacamata pengaman</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td rowspan="2"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>

        <tr>
        <tr>
            <td >8</td>
            <td colspan="2">* Check Over Ride Setting, Cover Panel & Door Lock <br> (Memeriksa setting over ride, panel penutup & kunci pintu)</td>
            <td colspan="3">Confirm setting Over Ride "OFF" <br>* Confirm semua bagian panel penutup mesin <br> * Confirm kunci pintu saat auto running</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>
        </tr>

        <tr>
            <td rowspan="2">9</td>
            <td rowspan="2" colspan="2">* Check movement of sensor at holder unit of supply position <br> (Memeriksa pergerakan sensor pada unit holder)</td>
            <td rowspan="2" colspan="3">Check pergerakan sensor pada holder stage open/close <br> Ketika pressing-down device tertutup dan output sensor terbuka</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td rowspan="2"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>

        <tr>
        <tr>
            <td >10</td>
            <td colspan="2">* Check Hot blower temperature</td>
            <td colspan="3">Pastikan bahwa temperaature (A sensor) dari hot blower berada dibawah ± 1 °C dari setting temperature</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>
        </tr>

        <tr>
            <td colspan="6">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="6">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="6">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="1">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td colspan="1">  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="6">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>

    </tbody>
    
    </table>
</div>