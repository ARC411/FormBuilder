<div class="table-responsive mt-3" id="fl18-p-wbqc">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-striped-columns" id="table1">
    <thead align="center">
      <tr>
        <th colspan="44" scope="col"><?php echo nl2br("INSPECTION WIRE BONDING CONTROL CHECK SHEET (FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
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
        <th class="input-form" colspan="31" scope="col">Reject Item</th>
        <th rowspan="2" >Note</th>    
      </tr>
      <tr>            
        <th class="input-form">Input</th>
        <th class="input-form">Output</th>
        <th class="input-form">Reject</th>
        
        <th class="input-form">GD1t Diff</th>
        <th class="input-form">GD2d Diff</th>
        <th class="input-form">GD2d Miss</th>
        <th class="input-form">GD Loop NG</th>
        <th class="input-form">GD No Wire</th>
        <th class="input-form">GD1t Miss</th>
        <th class="input-form">No GL2d GD</th>
        <th class="input-form">LD1t Diff</th>
        <th class="input-form">LD2d Diff</th>
        <th class="input-form">LD1t Miss</th>
        <th class="input-form">LD2d Miss</th>
        <th class="input-form">LD Loop NG</th>
        <th class="input-form">W Det Miss</th>
        <th class="input-form">@LD Lack</th>
        <th class="input-form">@LD Dirt</th>
        <th class="input-form">LD Peel</th>
        <th class="input-form">LD Scratch</th>
        <th class="input-form">LD Crack</th>
        <th class="input-form">Ag Few</th>
        <th class="input-form">Ag Much</th>
        <th class="input-form">Ag NG</th>
        <th class="input-form">Sic Slant</th>
        <th class="input-form">Sic Peel</th>
        <th class="input-form">Doublewire</th>
        <th class="input-form">Wire Touch</th>
        <th class="input-form">Wire Cut</th>
        <th class="input-form">A Failure</th>
        <th class="input-form">B Point NG</th>
        <th class="input-form">Gold Thick</th>
        <th class="input-form">Pos. Gold</th>
        <th class="input-form">Sic Float</th>
      </tr>
    </thead>

    <tbody>
      <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+2) { ?>
            <tr>
                <td ><?= $i/2+1; ?></td>
                <td ><?= (string)$alldata[$i]['name'];?></td>
                  <td ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                  <td >
                  <?php
                      if(!empty($alldata[$i]['name2'])){
                      echo  (string)$alldata[$i]['name2'];
                      }
                      else{
                      echo "";
                      }
                  ?>
                  </td>
                  <td >
                  <?php
                      if(!empty($alldata[$i]['shift2'])){
                      echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                      }
                      else{
                      echo "";
                      }
                  ?>
                  </td>
                <td ><?= (string)$alldata[$i]['machno']; ?></td>
                <td ><?= date('d/m/Y', strtotime((string)$alldata[$i]['par008'])); ?></td>
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
                <td ><?= (string)$alldata[$i]['par023']; ?></td>
                <td ><?= (string)$alldata[$i]['par024']; ?></td>
                <td ><?= (string)$alldata[$i]['par025']; ?></td>
                <td ><?= (string)$alldata[$i]['par026']; ?></td>
                <td ><?= (string)$alldata[$i]['par027']; ?></td>
                <td ><?= (string)$alldata[$i]['par028']; ?></td>
                <td ><?= (string)$alldata[$i]['par029']; ?></td>
                <td ><?= (string)$alldata[$i]['par030']; ?></td>
                <td ><?= (string)$alldata[$i]['par031']; ?></td>

                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par017'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par018'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par019'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par020'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par021'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par022'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par023'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par024'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par025'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par026'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par027'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par028'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par029'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par030'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par031'] : ''; ?></td>
                <td ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par032'] : ''; ?></td>

                <td ><?= (string)$alldata[$i]['par003']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
