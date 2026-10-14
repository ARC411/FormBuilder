<div class="table-responsive mt-3" id="sl56-s-ft5">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 5+2*$lenData ?> scope="col"><?php echo nl2br("FINAL TEST")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start up Check Item</th>
            <th colspan="3" rowspan="2" scope="col" >Standard</th>
            <th scope="col" colspan=<?= 2*$lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td >@Air Pressure</td>
            <td colspan="3">0.4-0.6 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="9">2</td>
            <td rowspan="9">Standard Sample Measurement <br> (Pengukuran Standard Sample)</td>
            <td rowspan="9">Ukur standard sample pada setiap holder</td>
            <td rowspan="1">In side</td>
            <td rowspan="1">Out side</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td rowspan="1">In side</td>
                <td rowspan="1">Out side</td>
            <?php } ?>
        </tr>

        <tr>
            <td >1</td>
            <td >1</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >2</td>
            <td >2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >3</td>
            <td >3</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >4</td>
            <td >4</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >5</td>
            <td >5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >6</td>
            <td >6</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >7</td>
            <td >7</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >8</td>
            <td >8</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >*Check of Contact pin<br>(Memeriksa Kontak Pin)</td>
            <td colspan="3">Check kondisi probe pin holder apakah terjadi pembengkokan, patah</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="3">4</td>
            <td rowspan="3">*Check of Classification<br>*(Memeriksa Klasifikasi)</td>
            <td colspan="3">Good Product</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">LD Open</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Ld short</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="5">5</td>
            <td rowspan="5">@Check of Gain light detector<br>(Memeriksa Gain Light detector)</td>
            <td colspan="1">FFP-V</td>
            <td colspan="2">5000-28000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="1">FFP-H</td>
            <td colspan="2">5000-28000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="1">FFP-V</td>
            <td colspan="2">5000-28000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="1">FFP-H</td>
            <td colspan="2">5000-28000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="1">POL</td>
            <td colspan="2">5000-28000</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td >Cleaning of Machine<br>(Membersihkan Mesin)</td>
            <td colspan="3">Bersihkan mesin dari debu atau kotoran dengan Lap dan dari device yang terjatuh</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par027']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['par028']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= (string)$alldata[$i]['status']; ?></td>
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
            <td colspan="5">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="2"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>

    </tbody>
    
    </table>
</div>