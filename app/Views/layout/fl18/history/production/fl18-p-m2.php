<div class="table-responsive mt-3" id="fl18-p-m2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="21" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET MARKING\n(FRAME LASER 1.8t AND 1.3t)")?></th>
        </tr>
        <tr>
            <th rowspan="3" >No.</th>
            <th rowspan="3" >Operator Start</th>
            <th rowspan="3" >Shift/Group</th>
            <th rowspan="3" >Operator Finish</th>
            <th rowspan="3" >Shift/Group</th>
            <th rowspan="3" >Mch. No.</th>         
            <th rowspan="3" >Model</th>
            <th rowspan="3" >Lot No.</th> 
            <th colspan="3" scope="col" >Quantity</th>
            <th colspan="2" scope="col" >Time</th>
            <th colspan="2" scope="col" >Marking Item</th>
            <th colspan="6" scope="col" >Foregoing Inspection (OK, NG)</th>
        </tr>
        <tr>
            <th class="input-form" rowspan="2">Input</th>
            <th class="input-form" rowspan="2">Output</th>
            <th class="input-form" rowspan="2">Reject</th>
            <th class="input-form" rowspan="2">In</th>
            <th class="input-form" rowspan="2">Out</th>
            <th class="input-form" rowspan="2">Model Code</th>
            <th class="input-form" rowspan="2">Factory (FTI)</th>
            <th class="input-form" colspan="3">Visual check 1 frame/lot</th>
            <th class="input-form" colspan="3">Vic Inspection sampling 1 frame/lot</th>
        </tr>
        <tr>
            <th class="input-form" >Pecah</th>
            <th class="input-form" >Tipis & Pecah</th>
            <th class="input-form" >Bentuk</th>
            <th class="input-form" >Pecah</th>
            <th class="input-form" >Tipis & Pecah</th>
            <th class="input-form" >Bentuk</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
            <tr>
                <td ><?= $i+1; ?></td>
                <td ><?= (string)$alldata[$i]['name'];?></td>
                <td ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                <td >
                <?php
                    if((string)$alldata[$i]['name2']){
                    echo  (string)$alldata[$i]['name2'];
                    }
                    else{
                    echo "";
                    }
                ?>
                </td>
                <td >
                <?php
                    if((string)$alldata[$i]['shift2']){
                    echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                    }
                    else{
                    echo "";
                    }
                ?>
                </td>
                <td ><?= (string)$alldata[$i]['machno']; ?></td>
                <td ><?= (string)$alldata[$i]['model']; ?></td>
                <td ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td ><?= (string)$alldata[$i]['par013']; ?></td>
                <td ><?= (string)$alldata[$i]['par014']; ?></td>
                <td ><?= (string)$alldata[$i]['par015']; ?></td>
                <td rowspan="1" ><?= date('H:i', strtotime((string)$alldata[$i]['par008'])); ?></td>
                <td >
                  <?php
                    if((string)$alldata[$i]['par009']){
                      echo date('H:i', strtotime((string)$alldata[$i]['par009'])); 
                    }
                    else{
                      echo "";
                    }
                  ?>
                </td>
                <td ><?= (string)$alldata[$i]['par002']; ?></td>
                <td ><?= (string)$alldata[$i]['par003']; ?></td>
                <td ><?= (string)$alldata[$i]['par017']; ?></td>
                <td ><?= (string)$alldata[$i]['par018']; ?></td>
                <td ><?= (string)$alldata[$i]['par019']; ?></td>
                <td ><?= (string)$alldata[$i]['par020']; ?></td>
                <td ><?= (string)$alldata[$i]['par021']; ?></td>
                <td ><?= (string)$alldata[$i]['par022']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
