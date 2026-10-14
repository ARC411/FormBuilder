<?php require_once(dirname( __FILE__ ). '/'.'../../../fillHistory.php'); ?>
<div class="table-responsive mt-3" id="fl18-s-cs">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 5+$lenData ?> scope="col"><?php echo nl2br("CAP SET MACHINE AUTO START UP")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="3" rowspan="2" scope="col">Start up Check Item</th>
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
            <td rowspan="2" colspan="3">* Cleaning Part Feeder</td>
            <td >Hopper</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >Part</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2" colspan="3">* Cleaning PP Head</td>
            <td >Head A</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Head B</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td >3</td>
            <td colspan="3">* Main Air Pressure</td>
            <td >0.38 ~ 0.42 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par005'],0.38,0.42); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">4</td>
            <td rowspan="2" colspan="1">@ Part Feeder Pressure</td>
            <td colspan="1">Head A</td>
            <td colspan="1"></td>
            <td >0.38 ~ 0.42 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par006'],0.38,0.42); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="1">Head B</td>
            <td colspan="1"></td>
            <td >0.38 ~ 0.42 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par007'],0.38,0.42); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="6">5</td>
            <td rowspan="6">@ Part Feeder Controler</td>
            <td rowspan="3">Head A</td>
            <td >Linear</td>
            <td >70.0 ± 7.0 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par008'],63,77); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Part</td>
            <td >23.0 ± 2.3 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par009'],20.7,25.3); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Hooper</td>
            <td >4.0 ± 0.4 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par010'],3.6,4.4); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">Head B</td>
            <td >Linear</td>
            <td >70.0 ± 7.0 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par011'],63,77); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Part</td>
            <td >23.0 ± 2.3 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par012'],20.7,25.3); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Hooper</td>
            <td >4.0 ± 0.4 </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= fillHistory((string)$alldata[$i]['par013'],3.6,4.4); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2" colspan="1">* Ion Blower</td>
            <td colspan="2">SW</td>
            <td > ON</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="2">Vol</td>
            <td >MIN</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Operator</td>
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
            <td colspan="5">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>
    
    </table>
</div>
<script type="text/javascript">
    document
            .addEventListener("DOMContentLoaded",
                function () {

                    console.log("ready 1")
                });

</script>