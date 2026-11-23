<div class="table-responsive mt-3" id="fl18-p-vi">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan="37" scope="col"><?php echo nl2br("VISUAL INSPECTION CONTROL SHEET (FRAME LASER 1.8t AND FL 1.3t)")?></th>
      </tr>
      <tr>
            <th rowspan="2" >No.</th>
            <th rowspan="2" >Operator Start</th>
            <th rowspan="2" >Shift/Group</th>
            <th rowspan="2" >Operator Finish</th>
            <th rowspan="2" >Shift/Group</th>
            <th rowspan="2" >Mch. No.</th>         
            <th rowspan="2" >Date (dd/mm/yyyy)</th>
            <th rowspan="2" >Model</th>
            <th rowspan="2" >Lot No.</th> 
            <th colspan="3" scope="col">Quantity</th>
            <th colspan="24" scope="col">Reject Item</th>
            <th rowspan="2">Note</th>
      </tr>
      <tr>
            <th scope="col">Input</th>
            <th scope="col">Output</th>
            <th scope="col">Reject</th>
            <th scope="col">DUST</th>
            <th scope="col">PAINT NG</th>
            <th scope="col">FRAME NG</th>
            <th scope="col">GOLDWIRE</th>
            <th scope="col">CAP CRACK</th>
            <th scope="col">RSN CRACK</th>
            <th scope="col">RSN CHIPING</th>
            <th scope="col">LEAD NG</th>
            <th scope="col">LD LACK</th>
            <th scope="col">LD SCRATCH</th>
            <th scope="col">CAP NG</th>
            <th scope="col">LEAD BEND</th>
            <th scope="col">MARK NG</th>
            <th scope="col">@ AG PST NG</th>
            <th scope="col">LD CHIPING</th>
            <th scope="col">CAP PEEL</th>
            <th scope="col">LD DIRT</th>
            <th scope="col">LD COAT</th>
            <th scope="col">DISCOLORATION</th>
            <th scope="col">@ WIRE FALL</th>
            <th scope="col">@ WIRE BENDING</th>
            <th scope="col"># Electrode Peel Off</th>
            <th scope="col"># Slight wave</th>
            <th scope="col">OTHERS</th>
    </tr>
    </thead>

    <tbody>
        <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
           <tr>
            <td rowspan="1" ><?= $i+1; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['name'];?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
            <td rowspan="1" ><?= !empty($alldata[$i]['name2']) ? (string)$alldata[$i]['name2'] : ''; ?></td>
            <td rowspan="1" ><?= !empty($alldata[$i]['shift2']) ? (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'] : ''; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['machno']; ?></td>
            <td rowspan="1" ><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['model']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['lotno']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par019']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par020']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par021']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par022']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par023']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par024']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par025']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par026']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par027']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par028']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par029']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par030']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par031']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par032']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par033']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par034']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par035']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par036']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par039']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par040']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par002']; ?></td>
           </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
