<div class="table-responsive mt-3" id="sl33-s-ag">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("AG PASTE HARDENING OVEN START UP")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start up Check Item</th>
            <th scope="col" rowspan="2">Standard</th>
            <th scope="col" colspan=<?= $lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td >@ Temperature Setting Oven</td>
            <td >150 ~ 160 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td >@ Setting open-close damper</td>
            <td >25% ~ 35%</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >@ Hardening time of silver paste</td>
            <td >90 ~ 120 min</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >Pintu Oven</td>
            <td >Periksa apakah pintu oven rapat saat ditutup</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td >Membersihkan Oven</td>
            <td >Bersihkan Oven dari debu atau kotoran dengan lap dan NG product yang terjatuh</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
    
        <tr>
            <td colspan="3">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td colspan="3">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= $alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= esc(getOperatorName($alldata[$i] ?? null)); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime($alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>
    </table>
</div>