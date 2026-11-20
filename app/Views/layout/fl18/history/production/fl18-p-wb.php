<div class="table-responsive mt-3" id="fl18-p-wb">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1" data-cols-width="10,15,15,15,10,10,20,20,20,10,10,10,10,10,10,10,10,15,10,10,15">
        <thead align="center">
            <tr>
                <th colspan="46" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET WIRE BONDING (FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
            </tr>
            <tr>
                <th rowspan="4" >No.</th>
                <th rowspan="4" >Operator Start</th>
                <th rowspan="4" >Shift/Group</th>
                <th rowspan="4" >Operator Finish</th>
                <th rowspan="4" >Shift/Group</th>
                <th rowspan="4" >Mch. No.</th>         
                <th rowspan="4" >Model</th>
                <th rowspan="4" >Lot No.</th> 
                <th rowspan="4" >Date (dd/mm/yyyy)</th>
                <th rowspan="4" >Gold Wire Lot No.</th>
                <th rowspan="4" >Assy Lot No.</th>
                <th colspan="3" >Quantity</th>
                <th colspan="2" >Time</th>
                <th colspan="6" scope="col" >Foregoing Inspection (40pcs/Magazine)</th>
                <th colspan="24" scope="col" >Lakukan wire pull test 4 pcs/shift</th>
            </tr>
        
            <tr>
                <th rowspan="3">Input</th>
                <th rowspan="3">Output</th>
                <th rowspan="3">NG</th>
                <th rowspan="3">In</th>
                <th rowspan="3">Out</th>
                <th scope="col" colspan="3">Magazine 1</th>
                <th scope="col" colspan="3">Magazine 2</th>
                <th scope="col" colspan="6">Sample 1</th>
                <th scope="col" colspan="6">Sample 2</th>
                <th scope="col" colspan="6">Sample 3</th>
                <th scope="col" colspan="6">Sample 4</th>
            </tr>

            <tr>
                <th rowspan="2">Ball Pos</th>
                <th rowspan="2">Ball Size</th>
                <th rowspan="2">Loop Shape</th>
                <th rowspan="2">Ball Pos</th>
                <th rowspan="2">Ball Size</th>
                <th rowspan="2">Loop Shape</th>
                <th colspan="2">SM</th>
                <th colspan="2">LD1</th>
                <th colspan="2">LD2</th>
                <th colspan="2">SM</th>
                <th colspan="2">LD1</th>
                <th colspan="2">LD2</th>
                <th colspan="2">SM</th>
                <th colspan="2">LD1</th>
                <th colspan="2">LD2</th>
                <th colspan="2">SM</th>
                <th colspan="2">LD1</th>
                <th colspan="2">LD2</th>
            </tr>
        
            <tr>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
                <th>Val</th>
                <th>Pos</th>
            </tr>
        
        </thead>
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+4) { ?>
                <tr>
                <td rowspan="1" ><?= $i/4+1; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['name'];?></td>
                    <td rowspan="1" ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                    <td rowspan="1" >
                    <?php
                        if((string)$alldata[$i]['name2']){
                        echo  (string)$alldata[$i]['name2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                    <td rowspan="1" >
                    <?php
                        if((string)$alldata[$i]['shift2']){
                        echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                <td rowspan="1" ><?= (string)$alldata[$i]['machno']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['model']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td rowspan="1" ><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par002']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par003']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
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
                <td rowspan="1" ><?= (string)$alldata[$i]['par035']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par036']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par037']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par038']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par039']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par040']; ?></td>

                <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par041']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par042']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par043']; ?></td>
                
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par017'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par041'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par018'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par042'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par019'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par043'] : ''; ?></td>

                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par017'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par041'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par018'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par042'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par019'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par043'] : ''; ?></td>
                
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par017'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par041'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par018'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par042'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par019'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+3]) ? (string)$alldata[$i+3]['par043'] : ''; ?></td>
                </tr>
            <?php }; ?>
        </tbody>
        </table>
    </div>
</div>
