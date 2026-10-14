<div class="table-responsive mt-3" id="sl56-f-fldb2">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" name="cnt-proses" value="4">
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan=<?= 31 ?> scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
      </tr>
    </thead>
    <thead align="center">
      <tr>
        <th rowspan="3" >No.</th>
        <th rowspan="3" >Mch. No.</th>         
        <th rowspan="3" >Model</th>
        <th rowspan="3" >Lot No.</th> 
        <!-- <th rowspan="2" >Alasan Inspeksi</th>
        <th colspan="4" scope="col" >Visual & Peel Test Check *</th> -->
        <th rowspan="3" >Measure Die Bond Pos</th>
        <th colspan="16" >Foregoing Check Sample No. <br> (2 pcs/head/shift)</th>
        <th rowspan="3" >Judge</th>
        <th colspan="6">Change Bonding Offset Parameter</th>
        <th rowspan="3">Status Approval</th>
        <th rowspan="3">Operator</th>
        <th rowspan="3">Time</th>
      </tr>
      <tr>
        <th colspan="2">Stage 1</th>
        <th colspan="2">Stage 2</th>
        <th colspan="2">Stage 3</th>
        <th colspan="2">Stage 4</th>
        <th colspan="2">Stage 5</th>
        <th colspan="2">Stage 6</th>
        <th colspan="2">Stage 7</th>
        <th colspan="2">Stage 8</th>
        <th colspan="2">X</th>
        <th colspan="2">Y</th>
        <th colspan="2">θ</th>
      </tr>
      <tr>
        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>

        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>
        <th >1</th>
        <th >2</th>

        <th >From</th>
        <th >To</th>
        <th >From</th>
        <th >To</th>
        <th >From</th>
        <th >To</th>
      </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($alldata); $i=$i+4) { ?>
            <tr>
                <td rowspan="4" ><?= $i/4+1; ?></td>
                <td rowspan="4" ><?= (string)$alldata[$i]['machno']; ?></td>
                <td rowspan="4" ><?= (string)$alldata[$i]['model']; ?></td>
                <td rowspan="4" ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td rowspan="1" ><b>X</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par001']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par002']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par003']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par004']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par005']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par006']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par007']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par008']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par009']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par010']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par011']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par012']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par023']; ?></td>
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
                <td rowspan="1" ><b>Y</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par001']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par002']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par003']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par004']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par005']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par006']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par007']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par008']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par009']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par010']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par011']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par012']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par023']; ?></td>
            </tr>
            <tr>
                <td rowspan="1" ><b>Z</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par001']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par002']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par003']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par004']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par005']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par006']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par007']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par008']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par009']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par010']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par011']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par012']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par023']; ?></td>
            </tr>
            <tr>
                <td rowspan="1" ><b>θ</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par001']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par002']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par003']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par004']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par005']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par006']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par007']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par008']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par009']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par010']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par011']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par012']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+3]['par023']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>