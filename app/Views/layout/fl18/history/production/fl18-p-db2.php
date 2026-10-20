<div class="table-responsive mt-3" id="fl18-p-db2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan="33" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL DIE BONDING 2")?></th>
      </tr>
      <tr>
        <th rowspan="4" >No.</th>   
        <th rowspan="4" >Operator Start</th>
        <th rowspan="4" >Shift/Group</th>
        <th rowspan="4" >Operator Finish</th>
        <th rowspan="4" >Shift/Group</th>      
        <th rowspan="4" >Mch. No.</th>         
        <th rowspan="4" >Date</th>
        <th rowspan="4" >Model</th>
        <th rowspan="4" >Lot No.</th>
        <th colspan="3" scope="col">Quantity</th>
        <th rowspan="4">Yield (%)</th>
        <th colspan="2">Time</th>
        <th colspan="6" scope="col">Foregoing Result (40 pcs/lot)</th>
        <th colspan="3">@ Die Bond Strength</th>
        <th colspan="2" rowspan="2" >* Check Mark 2Pcs/shift</th>
        <th colspan="2"># Check of Ag Paste</th>
        <th colspan="2"># Check ketebalan Ag Paste (nilai h)</th>
        <th rowspan="4">**Disp. Air Pressure</th>
        <th rowspan="4">**Disp. time</th>
        <th rowspan="4">**Vacuum pressure</th>
      </tr>
      <tr>
        <th rowspan="3">In</th>
        <th rowspan="3">Out</th>
        <th rowspan="3">NG</th>
        <th rowspan="3">IN</th>
        <th rowspan="3">OUT</th>
        <th colspan="2" scope="col">SM chip</th>
        <th colspan="4" scope="col">LD chip</th>
        <th colspan="3" scope="col">(2pcs/shift)</th>
        <th colspan="2" scope="col">(2pcs/Lot)</th>
        <th colspan="2" scope="col">(2pcs/Lot)</th>
      </tr>
      <tr>
        <th rowspan="2">Crack</th>
        <th rowspan="2">Chipping</th>
        <th rowspan="2">LD pos</th>
        <th rowspan="2">Miss LD chip</th>
        <th rowspan="2">Crack</th>
        <th rowspan="2">Chipping</th>
        <th rowspan="2">Std (Min)</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
      </tr>
      <tr>
        <th >1st</th>
        <th >2nd</th>
        <th >1st</th>
        <th >2nd</th>
        <th >1st</th>
        <th >2nd</th>
        <th >1st</th>
        <th >2nd</th>
      </tr>
    </thead>

    <tbody>
    <?php for($i = 0; $i < count($alldata); $i=$i+1) { ?>
        <tr>
        <td rowspan="1" ><?= $i+1; ?></td>
        <td rowspan="1" ><?= $alldata[$i]['name'];?></td>
          <td rowspan="1" ><?= $alldata[$i]['shift'] .' / '. $alldata[$i]['group'];?></td>
          <td rowspan="1" >
          <?php
              if($alldata[$i]['name2']){
              echo  $alldata[$i]['name2'];
              }
              else{
              echo "";
              }
          ?>
          </td>
          <td rowspan="1" >
          <?php
              if($alldata[$i]['shift2']){
              echo $alldata[$i]['shift2'] . ' / ' . $alldata[$i]['group2'];
              }
              else{
              echo "";
              }
          ?>
          </td>
        <td rowspan="1" ><?= (string)$alldata[$i]['machno']; ?></td>
        <td rowspan="1" ><?= date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['model']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['lotno']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par016']; ?></td>
        <td rowspan="1" ><?= date('H:i', strtotime($alldata[$i]['par008'])) ?></td>
        <td >
          <?php
            if($alldata[$i]['par009']){
              echo date('H:i', strtotime($alldata[$i]['par009'])); 
            }
            else{
              echo "";
            }
          ?>
        </td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par035']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par036']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par037']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par038']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par039']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par040']; ?></td>
        <td rowspan="1" ><?= !empty($alldata[$i]['std_die_bond']) ? $alldata[$i]['std_die_bond'] . ' N' : '-'; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par041']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par042']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par043']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par002']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par003']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par004']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par005']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par006']; ?></td>
        <td rowspan="1" ><?= (string)$alldata[$i]['par007']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
  </table>
</div>