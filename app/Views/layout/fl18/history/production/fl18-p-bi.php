<div class="table-responsive mt-3" id="fl18-p-bi">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan="30" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET BURN IN MANUAL\n(FL 1.8t and FL 1.3t)")?></th>
      </tr>
    </thead>
    <thead align="center">
      <tr>
        <th rowspan="3">No.</th>
        <th rowspan="3">Operator Start</th>
        <th rowspan="3">Shift/Group</th>
        <th rowspan="3">Operator Finish</th>
        <th rowspan="3">Shift/Group</th>
        <th rowspan="3">PC / Mch. No.</th>         
        <th rowspan="3">Model</th>
        <th rowspan="3">Lot No.</th> 
        <th colspan="4">Setting Machine</th>
        <th rowspan="3">File Name</th>
        <th rowspan="3">PC No. and Box ID</th>
        <th colspan="5">Resetting Machine</th>
        <th colspan="11">Failure Item (NG)</th>
      </tr>
  
      <tr>
        <th rowspan="2">Date</th>
        <th rowspan="2">Operator / Shift</th>
        <th rowspan="2">Time</th>
        <th rowspan="2">Quantity Input</th>
       
        <th rowspan="2">Date</th>
        <th rowspan="2">Operator / Shift</th>
        <th rowspan="2">Time</th>
        <th colspan="2">Quantity</th>
        
        <th rowspan="2">A1</th>
        <th rowspan="2">A3-A5</th>
        <th rowspan="2">B1-B5</th>
        <th rowspan="2">E1</th>
        <th rowspan="2">E2</th>
        <th rowspan="2">E3</th>
        <th rowspan="2">E4</th>
        <th rowspan="2">E5</th>
        <th rowspan="2">E7</th>
        <th rowspan="2">RE</th>
        <th rowspan="2">Others</th>
      </tr>
    
      <tr>
        <th scope="col">Pass</th>
        <th scope="col">Reject</th>
      </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+2) { ?>
            <tr>
                <td><?= $i/2+1; ?></td>
                <td><?= (string)$alldata[$i]['name'];?></td>
                <td><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                <td>
                  <?php
                      if(!empty($alldata[$i]['name2'])){
                        echo (string)$alldata[$i]['name2'];
                      } else {
                        echo "-";
                      }
                  ?>
                </td>
                <td>
                  <?php
                      if(!empty($alldata[$i]['shift2'])){
                        echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                      } else {
                        echo "-";
                      }
                  ?>
                </td>
                <td><?= (string)$alldata[$i]['machno']; ?></td>
                <td><?= (string)$alldata[$i]['model']; ?></td>
                <td><?= (string)$alldata[$i]['lotno']; ?></td>
                <td><?= !empty($alldata[$i]['par008']) ? date('d/m/Y', strtotime((string)$alldata[$i]['par008'])) : date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
                <td><?= (string)$alldata[$i]['name'] . "(" . (string)$alldata[$i]['shift'].")"; ?></td>
                <td><?= !empty($alldata[$i]['par008']) ? date('H:i', strtotime((string)$alldata[$i]['par008'])) : '-'; ?></td>
                <td><?= (string)$alldata[$i]['par013']; ?></td>
                <td><?= (string)$alldata[$i]['par002']; ?></td>
                <td><?= (string)$alldata[$i]['par003']; ?></td>
                <td>
                  <?php
                    if(!empty($alldata[$i]['par009'])){
                      echo date('d/m/Y', strtotime((string)$alldata[$i]['par009'])); 
                    } else {
                      echo "-";
                    }
                  ?>
                </td>
                <td>
                  <?php
                    if(!empty($alldata[$i]['name2'])){
                      echo (string)$alldata[$i]['name2'] . "(" . (string)$alldata[$i]['shift2'].")";
                    } else {
                      echo "-";
                    }
                  ?>
                </td>
                <td>
                  <?php
                    if(!empty($alldata[$i]['par009'])){
                      echo date('H:i', strtotime((string)$alldata[$i]['par009'])); 
                    } else {
                      echo "-";
                    }
                  ?>
                </td>
                <td><?= (string)$alldata[$i]['par014']; ?></td>
                <td><?= (string)$alldata[$i]['par015']; ?></td>

                <td><?= (string)$alldata[$i]['par017']; ?></td>
                <td><?= (string)$alldata[$i]['par018']; ?></td>
                <td><?= (string)$alldata[$i]['par019']; ?></td>
                <td><?= (string)$alldata[$i]['par020']; ?></td>
                <td><?= (string)$alldata[$i]['par021']; ?></td>
                <td><?= (string)$alldata[$i]['par022']; ?></td>
                <td><?= (string)$alldata[$i]['par023']; ?></td>
                <td><?= (string)$alldata[$i]['par024']; ?></td>
                <td><?= (string)$alldata[$i]['par025']; ?></td>
                <td><?= (string)$alldata[$i]['par026']; ?></td>
                <td><?= (string)$alldata[$i]['par027']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
