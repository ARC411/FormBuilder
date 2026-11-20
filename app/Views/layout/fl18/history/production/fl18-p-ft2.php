<div class="table-responsive mt-3" id="fl18-p-ft2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover"  id="table1">
        <thead align="center">
            <tr>
                <th colspan="22" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET OF FINAL TEST GH33SERIES MODEL\n(FL 1.8t AND FL 1.3t)")?></th>
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
                <th colspan="3" scope="col" >Quantity</th>
                <th colspan="10" scope="col" >Failure Item</th>
            </tr>
        
            <tr>            
                <th rowspan="3">Input</th>
                <th rowspan="1" colspan="2">Output</th>
                <th >PD </th>
                <th >Open/Short_1 </th>
                <th >Rd/Vop_1</th>
                <th >LD.Ir_1</th>
                <th >CW_1</th>
                <th >NE_1</th>
                <th >Lkink_1</th>
                <th >Pulse_1</th>
                <th >Pol/PhPI_1</th>
                <th >Thv_1</th>
            </tr>
        
            <tr>                
                <th class="input-form" rowspan="2">GO</th>
                <th class="input-form" rowspan="2">NG</th>
                <th>Thh_1</th>
                <th>Axis Diff_1</th>
                <th>Ripple/Ub_1</th>
                <th>Lp_1</th>
                <th>SP Err</th>
                <th>Open/Short_2</th>
                <th>Rd/Vop_2</th>
                <th>LD.Ir_2</th>
                <th>CW_2</th>
                <th>NE_2</th>
            </tr>
            <tr>  
                <th scope="col">Lkink_2</th>
                <th scope="col">Pulse_2</th>
                <th scope="col">Pol/PhPI_2</th>
                <th scope="col">Thv_2</th>
                <th scope="col">Thh_2</th>
                <th scope="col">Axis Diff_2</th>
                <th scope="col">Ripple/Ub_2</th>
                <th scope="col">Lp_2</th>
                <th scope="col">Axis Diff 1_2</th>
                <th scope="col">Other</th>
            </tr>

        </thead>
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+3) { ?>
                <tr>
                    <td rowspan="3" ><?= $i/3+1; ?></td>
                    <td rowspan="3" ><?= $alldata[$i]['name'];?></td>
                        <td rowspan="3" ><?= $alldata[$i]['shift'] .' / '. $alldata[$i]['group'];?></td>
                        <td rowspan="3" >
                        <?php
                            if($alldata[$i]['name2']){
                            echo  $alldata[$i]['name2'];
                            }
                            else{
                            echo "";
                            }
                        ?>
                        </td>
                        <td rowspan="3" >
                        <?php
                            if($alldata[$i]['shift2']){
                            echo $alldata[$i]['shift2'] . ' / ' . $alldata[$i]['group2'];
                            }
                            else{
                            echo "";
                            }
                        ?>
                        </td>
                    <td rowspan="3" ><?= $alldata[$i]['machno']; ?></td>
                    <td rowspan="3" ><?= $alldata[$i]['model']; ?></td>
                    <td rowspan="3" ><?= $alldata[$i]['lotno']; ?></td>
                    <td rowspan="3" ><?= date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
                    <td rowspan="3" ><?= (string)$alldata[$i]['par013']; ?></td>
                    <td rowspan="3" ><?= (string)$alldata[$i]['par014']; ?></td>
                    <td rowspan="3" ><?= (string)$alldata[$i]['par015']; ?></td>
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
                </tr>
                <tr>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par017'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par018'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par019'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par020'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par021'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par022'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par023'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par024'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par025'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par026'] : ''; ?></td>
                </tr>
                <tr>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par017'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par018'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par019'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par020'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par021'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par022'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par023'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par024'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par025'] : ''; ?></td>
                    <td rowspan="1" ><?= isset($alldata[$i+2]) ? (string)$alldata[$i+2]['par026'] : ''; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
