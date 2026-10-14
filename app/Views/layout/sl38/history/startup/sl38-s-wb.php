<div class="table-responsive mt-3" id="sl38-s-wb">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 11+$lenData ?> scope="col"><?php echo nl2br("WIRE BOND MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start up Check Item</th>
            <th colspan="3" rowspan="2" scope="col" >Standard</th>
            <th scope="col" colspan=<?= 6+$lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td >Cleaning of the wire course <br> (Membersihkan rute gold wire)</td>
            <td colspan="3">Lap dan bersihkan wire guide dan ring guide dengan bemcot</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >2</td>
            <td >@ Confirm the value of the vacuum pressure gauge <br> (Confirm nilai air pressure)</td>
            <td colspan="3">-40 kPa or less</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >@ Confirm the value of the air pressure gauge <br> (Confirm nilai air pressure)</td>
            <td colspan="3">0.29 ~ 0.59 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td >@ Confirm of air tension <br> (Confirm nilai air tension)</td>
            <td colspan="3">3.0 ± 0.5 l/min</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td >@ Confirm of wireFeed</td>
            <td colspan="3">20.0 ± 0.5 l/min</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td >6</td>
            <td >@ Capilary usage count <br> (pemakaian capilary)</td>
            <td colspan="3">100.000 wire/kurang <br> =200.000 point/kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >7</td>
            <td >@ Confirm of heater temperature <br> (Confirm nilai heater temperature)</td>
            <td colspan="3">200 ~ 220 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="4">8 </td>
            <td rowspan="4">@ Checking of bonding parameter <br> (Memeriksa parameter bonding)</td>
            <td >LD</td>
            <td >MS</td>
            <td >PD</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td><?="LD"; ?></td>
                <td><?="MS"; ?></td>
                <td><?="PD"; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">POWER</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
           
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
           
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">TIME</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
           
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td colspan="3">PRESS</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
 
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
  
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td colspan="5">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="3">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td>  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="3"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
      </tbody>
    </table>
</div>