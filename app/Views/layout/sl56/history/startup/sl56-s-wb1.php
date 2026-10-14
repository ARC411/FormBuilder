<div class="table-responsive mt-3" id="sl56-s-wb1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 5+8*$lenData ?> scope="col"><?php echo nl2br("WIRE BONDING START UP")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start Up Check Item</th>
            <th colspan="3" rowspan="2" scope="col" >Standard</th>
            <th scope="col" colspan=<?= 8*$lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>1</td>
            <td >Checking of gold wire route (Pemeriksaan rute gold wire)</td>
            <td colspan="3">Lap dan bersihkan wire guide dan ring guide dengan bemcot beralkohol.</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2">@ Checking of air pressure (Memeriksa nilai air pressure)</td>
            <td colspan="3">0.35 ~ 0.55 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="3">0.39 ~ 0.59 MPa (GH045/GH05series)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>3</td>
            <td >@ Capilary usage count</td>
            <td colspan="3">100.000 wire = 200.000 point</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="5">4</td>
            <td rowspan="5">@ Checking of bonding parameter (Memeriksa parameter bonding)</td>
            <td rowspan="2">LD</td>
            <td rowspan="2">MS</td>
            <td rowspan="2">PD</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2" >LD</td>
                <td colspan="2" >MS 1</td>
                <td colspan="2" >MS 2</td>
                <td colspan="2" >PD</td>
            <?php } ?>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td >Head A</td>
                <td >Head B</td>
                <td >Head A</td>
                <td >Head B</td>
                <td >Head A</td>
                <td >Head B</td>
                <td >Head A</td>
                <td >Head B</td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">POWER</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">TIME</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">FORCE</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par027']; ?></td>
            <?php } ?>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par028']; ?></td>
            <?php } ?>
        </tr>

        <tr>
          <td>5</td>
          <td >Cleaning of machine (Membersihkan mesin)</td>
          <td colspan="3">Bersihkan mesin dari debu/kotoran dengan lap dan device yang terjatuh</td>
          <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par029']; ?></td>
          <?php } ?>
        </tr>

        <tr>
          <td colspan="5">Note</td>
          <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['par030']; ?></td>
          <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="8">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td colspan="8">  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="8"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>

    </tbody>   
    </table>
</div>