<div class="table-responsive w-auto mt-3" id="sl56-p-db1">
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover"  id="table1" data-cols-width="10,15,15,15,10,10,20,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,15">
        <thead align="center">
            <tr>
                <th colspan="26" scope="col"><?php echo nl2br("LD DIE BONDING 1 MACHINE")?></th>
            </tr>
            <tr>
                <th rowspan="3" >No.</th>
                <th rowspan="3" >Operator Start</th>         
                <th rowspan="3" >Shift/Group </th>
                <th rowspan="3" >Operator Finish</th>         
                <th rowspan="3" >Shift/Group </th>
                <th rowspan="3" >Mch. No.</th>         
                <th rowspan="3" >Model</th>
                <th rowspan="3" >Lot No.</th> 
                <th rowspan="3" >Date (dd/mm/yyyy)</th>
                <th colspan="2" scope="col">Time</th>
                <th colspan="3" scope="col">Quantity</th>
                <th colspan="11" scope="col">Foregoing Result*(Chip = 20 pcs/lot & *AuSn=1 ring(1st sheet)/lot, AuSn Ball=5pcs/lot)</th>
                <th rowspan="3" >Remark</th>
            </tr>
            <tr>
                <th  rowspan="2">In</th>
                <th  rowspan="2">Out</th>
                <th  rowspan="2">Input</th>
                <th  rowspan="2">Output</th>
                <th  rowspan="2">Others</th>
                <th  colspan="2" scope="col">SM</th>
                <th  colspan="4" scope="col">LD</th>
                <th  colspan="4" scope="col">AuSn</th>
                <th  rowspan="2" >AuSn Ball</th>
            </tr>
            <tr>
                <th scope="col">Crack</th>
                <th scope="col">Chipping</th>
                <th scope="col">LD Pos</th>
                <th scope="col">Miss</th>
                <th scope="col">Crack</th>
                <th scope="col">Chipping</th>
                <th scope="col">Input</th>
                <th scope="col">Output</th>
                <th scope="col">NG</th>
                <th scope="col">%</th>
            </tr>
        </thead>
        
        <tbody>
            <?php for($i = 0; $i < count($alldata); $i=$i+2) { ?>
            <tr>
            <td rowspan="1" ><?= $i/2+1; ?></td>
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
            <td rowspan="1" ><?= date('H:i', strtotime((string)$alldata[$i]['par008'])); ?></td>
            <td rowspan="1" >
                <?php 
                    if(!(string)$alldata[$i]['par009']) echo "";
                    else echo date('H:i', strtotime((string)$alldata[$i]['par009'])); 
                ?>
            </td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par035']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par036']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par037']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par038']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par039']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par040']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par019']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par020']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par041']; ?></td>
            <td rowspan="1" ><?= (string)$alldata[$i]['par002']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>      
    
</div>