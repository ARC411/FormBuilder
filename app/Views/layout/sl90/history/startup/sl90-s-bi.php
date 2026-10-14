<div class="table-responsive mt-3" id="sl90-s-bi">
<input type="hidden" id="cnt-table" value="1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
    <table class="table table-bordered table-hover" id="table1"
    >
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("BURN IN MACHINE START UP (Chiller Type)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start Up Check Item</th>
            <th scope="col" rowspan="2">Standard</th>
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
            <td>1</td>
            <td >*Checking Safety-Device <br> Memeriksa perangkat keamanan</td>
            <td >Lakukan aging dengan ACC, buka tutupnya dengan sengaja dan pastikan power suply berhenti</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2">* Water leak confirmation <br> Konfirmasi kebocoran air</td>
            <td >Pastikan apakah tidak ada kebocoran air pada chiller </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Pastikan tidak ada kebocoran pipa dari chiller ke heatsink</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >* Confirmation of cooling water level (Chiller) <br> Konfirmasi ketinggian air pada chiller</td>
            <td >Pastikan apakah tidak ada kebocoran air pada chiller </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td >* Confirmation of overheating revention temperature (Chiller) <br> Konfirmasi overheat pada chiller</td>
            <td >Setting Temperature 90°C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td >@ Chiller temperatur check <br> Memeriksa suhu chiller</td>
            <td >87°C ~ 93°C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">6</td>
            <td rowspan="3">* Confirmation of the filter <br> Konfirmasi Filter</td>
            <td >Pastikan filter tidak rusak</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Pastikan filter tidak ternoda</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Pastikan filter tidak terlepas</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">7</td>
            <td rowspan="3">* Confirmation of alumunium tape <br> Konfirmasi alumunium tape</td>
            <td >Pastikan alumunium tape tidak terlepas</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Pastikan alumunium tape tidak sobek</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Bersihkan kotoran pada alumunium tape dengan alcohol</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td colspan="3" >Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Operator</td>
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
            <td colspan="3">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>   
    </table>
</div>