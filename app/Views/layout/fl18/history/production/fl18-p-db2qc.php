<div class="table-responsive mt-3" id="fl18-p-db2qc">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-striped-columns" id="table1">
    <thead align="center">
      <tr>
        <th colspan="33" scope="col"><?php echo nl2br("INSPECTION LD D/B 2 CONTROL CHECK SHEET\n(FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
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
        <th class="input-form" colspan="4" scope="col">Quantity</th>
        <th class="input-form" colspan="20" scope="col">Reject Item</th>
      </tr>
      <tr>
        <th class="input-form">Input</th>
        <th class="input-form">Output</th>
        <th class="input-form">Reject</th>
        <th class="input-form">Yield</th>

        <th class="input-form">Sic Slant</th>
        <th class="input-form">Sic X Diff</th>
        <th class="input-form">Sic Y Diff</th>
        <th class="input-form">Sic Crack</th>
        <th class="input-form">Sic Dirt</th>
        <th class="input-form">Sic Nothing</th>
        <th class="input-form">LD X Diff</th>
        <th class="input-form">LD Y Diff</th>
        <th class="input-form">LD TH Slant</th>
        <th class="input-form">LD Crack</th>
        <th class="input-form">LD Dirt</th>
        <th class="input-form">LD Nothing</th>
        <th class="input-form">AG Few</th>
        <th class="input-form">AG Much</th>
        <th class="input-form">AG NG</th>
        <th class="input-form">Dust NG</th>
        <th class="input-form">LD Chipping</th>
        <th class="input-form">Sic Float</th>
        <th class="input-form">LD Scratch</th>
        <th class="input-form">Others</th>
      
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
                <td ><?= (string)$alldata[$i]['par016']; ?></td>
                <td ><?= (string)$alldata[$i]['par017']; ?></td>
                <td ><?= (string)$alldata[$i]['par018']; ?></td>
                <td ><?= (string)$alldata[$i]['par019']; ?></td>
                <td ><?= (string)$alldata[$i]['par020']; ?></td>
                <td ><?= (string)$alldata[$i]['par021']; ?></td>
                <td ><?= (string)$alldata[$i]['par022']; ?></td>
                <td ><?= (string)$alldata[$i]['par023']; ?></td>
                <td ><?= (string)$alldata[$i]['par024']; ?></td>
                <td ><?= (string)$alldata[$i]['par025']; ?></td>
                <td ><?= (string)$alldata[$i]['par026']; ?></td>
                <td ><?= (string)$alldata[$i]['par027']; ?></td>
                <td ><?= (string)$alldata[$i]['par028']; ?></td>
                <td ><?= (string)$alldata[$i]['par029']; ?></td>
                <td ><?= (string)$alldata[$i]['par030']; ?></td>
                <td ><?= (string)$alldata[$i]['par031']; ?></td>
                <td ><?= (string)$alldata[$i]['par032']; ?></td>
                <td ><?= (string)$alldata[$i]['par033']; ?></td>
                <td ><?= (string)$alldata[$i]['par034']; ?></td>
                <td ><?= (string)$alldata[$i]['par035']; ?></td>
                <td ><?= (string)$alldata[$i]['par036']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>