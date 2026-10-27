<div class="table-responsive mt-3" id="sl33-s-db2c">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("LD Die Bonding 2 for High Speed Auto Machine")?></th>
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
            <td rowspan="2">@ Air Pressure</td>
            <td >Transfer</td>
            <td >0.4 ~ 0.49 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >CTBU</td>
            <td >0.08 ~ 0.12 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2">@ Vacuum level</td>
            <td >Stem transfer</td>
            <td >-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td >CTBU</td>
            <td >-90 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">3</td>
            <td rowspan="3">@ Collet Vacum Level</td>
            <td >A collet</td>
            <td >-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >B collet</td>
            <td >-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Middle stage</td>
            <td >-80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">4</td>
            <td colspan="2" rowspan="3">* Collet Cleaning</td>
            <td >Collet A</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Collet B</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td colspan="2">* Cek kondisi collet guide</td>
            <td >Cek kondisi collet guide goyang atau tidak</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2" colspan="2">@ Colled Load</td>
            <td >Collet A : 0.49 ~ 0.88 N (49~88 gr)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Collet B : 0.69 ~ 1.47 N (69~147 gr)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >7</td>
            <td colspan="2">@ Needle Push Up Level</td>
            <td >0.20 ~ 0.60 mm 0.40 ~ 0.80 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >8</td>
            <td colspan="2">* Cek posisi die bonding pada monitor</td>
            <td >Pergeseran LD maksimal 2 mm dari cursor</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="6">9</td>
            <td rowspan="6">Ag Paste Dispenser</td>
            <td rowspan="3">Digital</td>
            <td >@ Air Pressure : 0.005 ~ 0.1 MPa (5 ~ 100 kPa)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >@ Dispense Time : 5 ~ 100 ms (0.005 ~ 0.100 s)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >@ Suction force : -0.4 ~ 0 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">Analog</td>
            <td >@ Air Pressure : 0.01 ~ 0.10 MPa (10 ~ 100 kPa)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >@ Dispense Time : 10 ~ 100 ms (0.010 ~ 0.100 s)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >* Suction force : kurang dari 1/6 dari posisi closed <br> (putaran berlawanan arah jarum jam)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">Ag Paste Condition</td>
            <td >* Posisi</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >@ Quantity</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">11</td>
            <td rowspan="2" colspan="2">* Direction Ion Blower Fan</td>
            <td >Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >12</td>
            <td colspan="2">* Cek Stem Nozzle Condition</td>
            <td >Tidak ada gompal atau crack pada stem nozzle</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">13</td>
            <td rowspan="3">@ Bonding Offset Parameter</td>
            <td >X</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par027']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Y</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par028']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >θ</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par029']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >14</td>
            <td colspan="2">* Cleaning Machine</td>
            <td >Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par030']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par031']; ?></td>
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
                <td colspan="col"><?= esc(getOperatorName($alldata[$i] ?? null)); ?></td>
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