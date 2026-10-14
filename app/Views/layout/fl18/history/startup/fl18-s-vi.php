<div class="table-responsive mt-3" id="fl18-s-vi">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("VACUUM JIG START UP")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" scope="col" rowspan="2" >No</th>
            <th class="input-form" scope="col" rowspan="2" >Date</th>
            <th class="input-form" scope="col" colspan="2" >Start Up Check Item</th>
            <th class="input-form" scope="col" rowspan="2">Note</td>
            <th class="input-form" scope="col" rowspan="2">Status Approval</td>
            <th class="input-form" scope="col" rowspan="2">Operator</td>
            <th class="input-form" scope="col" rowspan="2">Time</td>
        </tr>

        <tr>
            <th class="input-form">A</td>
            <th class="input-form">B</td>
        </tr>
    </thead>

    <tbody>
        <?php for($i = 0; $i < count($alldata); $i=$i+1) { ?>
            <tr>
                <td colspan="col"><?= $i+1; ?></td>
                <td colspan="col"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="col">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td>  </td>';
                    }
                ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            </tr>
        <?php } ?>
    </tbody>   
    </table>
</div>