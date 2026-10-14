<div class="table-responsive mt-3" id="sl90-s-cs">
<input type="hidden" id="cnt-table" value="1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
    <table class="table table-bordered table-hover" id="table1"
    >
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("CAP SEAL MACHINE START UP (Daitoh Machine)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start Up Check Item</th>
            <th scope="col" rowspan="2"colspan="2">Standard</th>
            <th scope="col" colspan=<?= 4+$lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td rowspan="2">1</td>
            <td rowspan="2">@ Checking of air pressure <br> Memeriksa tekanan air pressure</td>
            <td >Air Pressure (root)</td>
            <td >0.49 ± 0.05 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Super dry air</td>
            <td >0.20 ± 0.05 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">2</td>
            <td rowspan="3">@ Confirm of seal condition <br> Konfirmasi kondisi seal</td>
            <td >Pressure</td>
            <td >1100 ~ 1300 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Current</td>
            <td >8.9 ~ 9.9 kA</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Welding time</td>
            <td >9.4 ms</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >@ Confirm dew point condition <br> Konfirmasi kondisi dew point</td>
            <td colspan="2">Kurang dari -36°C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="2">4</td>
            <td rowspan="2">@ Checking of electrode use time <br> Memeriksa pemakaian electrode</td>
            <td >Upper electrode</td>
            <td >Max 10000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Middle electrode</td>
            <td >Max 20000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">5</td>
            <td rowspan="2">@ Confirm of baking condition (for Stem & Cap) <br> Konfirmasi kondisi baking (Untuk Stem & Cap)</td>
            <td >Temperature</td>
            <td >190°C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Time</td>
            <td >1 ~ 3 hours</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td >* Cleaning of machine <br> Membersihkan mesin</td>
            <td colspan="2">Bersihkan mesin dari stem atau cap yang terjatuh dan bersihkan dari debu/kotoran</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td colspan="4">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
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
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="col">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td>  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>   
    </table>
</div>