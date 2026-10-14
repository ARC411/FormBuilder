<div class="table-responsive mt-3" id="sl56-s-fldsm">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" name="cnt-proses" value="4">
<input type="hidden" id="cnt-table" value="4">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("BURN IN START UP (DAITOH MACHINE)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th rowspan="3" >No.</th>
            <th rowspan="3" >Mch. No.</th>         
            <th rowspan="3" >Model</th>
            <th rowspan="3" >Lot No.</th>
            <th colspan="6" scope="col">Die Bonding Strength*</th>
            <th colspan="2" scope="col">Wettability area >= 90%</th>
            <th rowspan="3">Remark</th>
            <th rowspan="3">Status Approval</th>
            <th rowspan="3">Operator</th>
            <th rowspan="3">Time</th>
            </tr>
            <tr>
            <th  rowspan="2">Sample</th>
            <th  scope="col" colspan="3">LD Chip & SM</th>

            <th  scope="col" colspan="2">Submount & Stem</th>

            <th  rowspan="2">LD Chip & SM</th>
            <th  rowspan="2">Submount & Stem</th>

        </tr>
        <tr>
            <th scope="col">Result</th>
            <th scope="col">Remain <br> % LD</th>
            <th scope="col">Judge <br> (OK/NG)</th>
            <th scope="col">Result</th>
            <th scope="col">Judge <br> (OK/NG)</th>
        </tr>
    </thead>

    <tbody>
        <?php for($i = 0; $i < count($alldata); $i=$i+4) { ?>
        <tr>
            <td rowspan="4" ><?= $i/4+1; ?></td>
            <td rowspan="4" ><?= (string)$alldata[$i]['machno']; ?></td>
            <td rowspan="4" ><?= (string)$alldata[$i]['model']; ?></td>
            <td rowspan="4" ><?= (string)$alldata[$i]['lotno']; ?></td>
            <td rowspan="1" ><b>Front 1</b></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par024']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par025']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par026']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par041']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par042']; ?></td>
            <td rowspan="4" ><?= (string)$alldata[$i]['par003']; ?></td>
            <td rowspan="4" ><?= (string)$alldata[$i]['status']; ?></td>
            <?php
                $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                    echo '<td rowspan=4>'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                } 
                else{
                    echo '<td rowspan=4>  </td>';
                }
            ?>
            <td rowspan="4" ><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
        </tr>
        <tr>
            <td rowspan="1" ><b>Front 2</b></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par024']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par025']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par026']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par041']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+1]['par042']; ?></td>
        </tr>
        <tr>
            <td rowspan="1" ><b>Back 1</b></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par024']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par025']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par026']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par041']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+2]['par042']; ?></td>
        </tr>
        <tr>
            <td rowspan="1" ><b>Back 2</b></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par024']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par025']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par026']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par041']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i+3]['par042']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>