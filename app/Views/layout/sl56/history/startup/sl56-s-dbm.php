<div class="table-responsive mt-3" id="sl56-s-dbm">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("PIN DIE BONDING 1 MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="2" rowspan="2" scope="col">Start up Check Item</th>
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
            <td rowspan="2">1</td>
            <td rowspan="2">@ Pressure</td>
            <td >Air Pressure</td>
            <td >0.30 ~ 0.50 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td >N2 Pressure</td>
            <td >0.14 ~ 0.16 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2">@ Vacuum pressure</td>
            <td >Chip PP</td>
            <td >-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Stem PP</td>
            <td >-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="2">3</td>
            <td rowspan="2">@ Collet Load</td>
            <td >Chip PP1 collet</td>
            <td >0.30 ~ 0.50 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Chip PP2 collet</td>
            <td >0.30 ~ 0.50 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td colspan="2">@ Needle Push Up Level</td>
            <td >0.4 ~ 0.6 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td colspan="3">* Needle and collet position check <br>(Cek posisi needle and collet)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td colspan="3">* Needle and collet Check the shape and dirt <br> (Bersihkan dengan Cotton Bud)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">7</td>
            <td rowspan="3">@ Collet down position</td>
            <td >Chip sheet</td>
            <td >-80 ~ -120 μm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Middle stage</td>
            <td >-80 ~ -120 μm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >D/B stage</td>
            <td >-80 ~ -120 μm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">8</td>
            <td rowspan="3">@ Dispenser indication</td>
            <td >Application pressure</td>
            <td >110 ~ -200 Kpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Dispense Time</td>
            <td >120 ~ 180 msec</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Vaccum Pressure</td>
            <td >-1.1 ~ -0.8 Kpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">9</td>
            <td rowspan="3" colspan="2">* Clean up</td>
            <td >Chip PP1 collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr> 
        
        <tr>
            <td >Chip PP2 collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>
 
        <tr>
            <td >Middle stage</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">* Check kondisi Ag Paste</td>
            <td >Posisi</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr> 
        
        <tr>
            <td >Quantity</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >11</td>
            <td colspan="3">* Check the safe door <br> (Buka Pintu dan seharusnya tidak bisa untuk jalankan Automatically)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td >12</td>
            <td colspan="3">* Cleaning Machine <br> (Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran)</td>
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