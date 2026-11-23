<div class="table-responsive mt-3" id="fl18-p-ftbc">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-striped-columns" id="table1">
    <thead align="center">
      <tr>
        <th colspan="19" scope="col"><?php echo nl2br("INSPECTION TIE BAR CUTTING CONTROL CHECK SHEET\n(FRAME LASER 1.8t AND 1.3t)")?></th>
      </tr>
      <tr>
        <th rowspan="2" >No.</th>    
        <th rowspan="2" >Operator Start</th>
        <th rowspan="2" >Shift/Group</th>
        <th rowspan="2" >Operator Finish</th>
        <th rowspan="2" >Shift/Group</th>     
        <th rowspan="2" >Mch. No.</th>         
        <th rowspan="2" >Date</th>
        <th rowspan="2" >Model</th>
        <th rowspan="2" >Lot No.</th> 
        <th class="input-form" colspan="3" scope="col">Quantity</th>
        <th class="input-form" colspan="6" scope="col">Reject Item</th>
        <th class="input-form" rowspan="2" scope="col">Note</th>
      </tr>
      <tr>            
        <th class="input-form">Input</th>
        <th class="input-form">Output</th>
        <th class="input-form">Reject</th>
        <th class="input-form">WIRETOUCH</th>
        <th class="input-form">AG MUCH</th>
        <th class="input-form">DUST</th>
        <th class="input-form">FRAME</th>
        <th class="input-form">RESIN CAP PEEL</th>
        <th class="input-form">OTHERS</th>
      </tr>
    </thead>

    <tbody>
      <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
            <tr>
                <td ><?= $i/1+1; ?></td>
                <td ><?= $alldata[$i]['name'];?></td>
                  <td ><?= $alldata[$i]['shift'] .' / '. $alldata[$i]['group'];?></td>
                  <td >
                  <?php
                      if($alldata[$i]['name2']){
                      echo  $alldata[$i]['name2'];
                      }
                      else{
                      echo "";
                      }
                  ?>
                  </td>
                  <td >
                  <?php
                      if($alldata[$i]['shift2']){
                      echo $alldata[$i]['shift2'] . ' / ' . $alldata[$i]['group2'];
                      }
                      else{
                      echo "";
                      }
                  ?>
                  </td>
                <td ><?= $alldata[$i]['machno']; ?></td>
                <td ><?= date('d/m/Y', strtotime($alldata[$i]['par008'])); ?></td>
                <td ><?= (string)$alldata[$i]['model']; ?></td>
                <td ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td ><?= (string)$alldata[$i]['par013']; ?></td>
                <td ><?= (string)$alldata[$i]['par014']; ?></td>
                <td ><?= (string)$alldata[$i]['par015']; ?></td>
                <td ><?= (string)$alldata[$i]['par017']; ?></td>
                <td ><?= (string)$alldata[$i]['par018']; ?></td>
                <td ><?= (string)$alldata[$i]['par019']; ?></td>
                <td ><?= (string)$alldata[$i]['par020']; ?></td>
                <td ><?= (string)$alldata[$i]['par021']; ?></td>
                <td ><?= (string)$alldata[$i]['par022']; ?></td>
                <td ><?= (string)$alldata[$i]['par002']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
