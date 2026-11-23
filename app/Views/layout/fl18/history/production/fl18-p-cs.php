<div class="table-responsive mt-3" id="fl18-p-cs">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
                <th colspan="23" scope="col" class="header"><?php echo nl2br("PRODUCTION CONTROL SHEET CAP SET AUTO\n(FRAME LASER 1.8t)")?></th>
            </tr>
        </thead>
        <thead align="center">
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">Operator Start</th>
                <th rowspan="3">Shift/Group</th>
                <th rowspan="3">Operator Finish</th>
                <th rowspan="3">Shift/Group</th>
                <th rowspan="3">Mch. No.</th>         
                <th rowspan="3">Model</th>
                <th rowspan="3">Lot No.</th> 
                <th rowspan="3">Date (dd/mm/yyyy)</th>
                <th colspan="2" scope="col">Time</th>
                <th colspan="3" scope="col">Quantity</th>
                <th colspan="7" scope="col">Foregoing Check (2 frame/lot)</th>
                <th colspan="2" scope="col">@ Cap Strength</th>
            </tr>
            <tr>
                <th rowspan="2">In</th>
                <th rowspan="2">Out</th>
                <th rowspan="2">Input</th>
                <th rowspan="2">Output</th>
                <th rowspan="2">NG</th>
                <th rowspan="2">*Float</th>
                <th colspan="2" scope="col">* Direction</th>
                <th colspan="2" scope="col">* Scratch</th>
                <th rowspan="2">*No Cap</th>
                <th rowspan="2">*Burr</th>
                <th colspan="2" scope="col">0.6 N atau lebih<br>(2pcs/hari)</th>
            </tr>
            <tr>
                <th scope="col">*Surface</th>
                <th scope="col">*180&deg;</th>
                <th scope="col">*Cap</th>
                <th scope="col">*Frame</th>
                <th scope="col">A</th>
                <th scope="col">B</th>
            </tr>
        </thead>
        
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
            <tr>
                <td><?= $i+1; ?></td>
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
                <td><?= !empty($alldata[$i]['par008']) ? date('d/m/Y', strtotime($alldata[$i]['par008'])) : date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
                <td><?= !empty($alldata[$i]['par008']) ? date('H:i', strtotime($alldata[$i]['par008'])) : '-'; ?></td>
                <td>
                  <?php
                    if(!empty($alldata[$i]['par009'])){
                      echo date('H:i', strtotime($alldata[$i]['par009'])); 
                    } else {
                      echo "-";
                    }
                  ?>
                </td>
                <td><?= (string)$alldata[$i]['par013']; ?></td>
                <td><?= (string)$alldata[$i]['par014']; ?></td>
                <td><?= (string)$alldata[$i]['par015']; ?></td>
                <td><?= (string)$alldata[$i]['par035']; ?></td>
                <td><?= (string)$alldata[$i]['par036']; ?></td>
                <td><?= (string)$alldata[$i]['par037']; ?></td>
                <td><?= (string)$alldata[$i]['par038']; ?></td>
                <td><?= (string)$alldata[$i]['par039']; ?></td>
                <td><?= (string)$alldata[$i]['par040']; ?></td>
                <td><?= !empty($alldata[$i]['par041']) ? (string)$alldata[$i]['par041'] : '-'; ?></td>
                <td><?= (string)$alldata[$i]['par017']; ?></td>
                <td><?= (string)$alldata[$i]['par018']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table> 
</div>
