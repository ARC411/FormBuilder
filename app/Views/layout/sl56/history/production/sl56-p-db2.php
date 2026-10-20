<div class="table-responsive mt-3" id="sl56-p-db2">
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
            <th colspan="31" scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
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
                <th colspan="3" scope="col" >Quantity</th>
                <th colspan="2" scope="col" >Time</th>
                <th colspan="4" scope="col" >Foregoing (5 pcs/head/lot)</th>
                <th colspan="4" scope="col" >Vis Insp (10%/lot)</th>
                <th colspan="8" rowspan="2" scope="col" >Die Bonding Strength (1 pcs/head/shift) <br> Standard >= 500g (4.9 N) <br> Standart Model GH04Series & GH05Series* <br> GH0523AD2G >= 6.0 N</th>
                <th rowspan="3" >Judge</th>
            </tr>
            <tr>
                <th rowspan="2">Input</th>
                <th rowspan="2">Output</th>
                <th rowspan="2">NG</th>
                <th rowspan="2">IN</th>
                <th rowspan="2">OUT</th>
                <th colspan="2" scope="col">MSP Chip</th>
                <th colspan="2" scope="col">LD Chip</th>
                <th colspan="2" scope="col">MSP Chip</th>
                <th colspan="2" scope="col">LD Chip</th>
            </tr>
            <tr>
                <th scope="col">Crack</th>
                <th scope="col">Chipping</th>
                <th scope="col">LD Pos</th>
                <th scope="col">Miss</th>
                <th scope="col">Crack</th>
                <th scope="col">Chipping</th>
                <th scope="col">LD Pos</th>
                <th scope="col">Miss</th>
                <th scope="col">1</th>
                <th scope="col">2</th>
                <th scope="col">3</th>
                <th scope="col">4</th>
                <th scope="col">5</th>
                <th scope="col">6</th>
                <th scope="col">7</th>
                <th scope="col">8</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; ?>
            <?php foreach($alldata as $c): ?>
            <tr>
            <td rowspan="1" ><?= $i++; ?></td>
            <td rowspan="1" ><?= (string)$c['name'];?></td>
            <td rowspan="1" ><?= (string)$c['shift'] .' / '. (string)$c['group'];?></td>
            <td rowspan="1" >
            <?php
                if((string)$c['name2']){
                echo  (string)$c['name2'];
                }
                else{
                echo "";
                }
            ?>
            </td>
            <td rowspan="1" >
            <?php
                if((string)$c['shift2']){
                echo (string)$c['shift2'] . ' / ' . (string)$c['group2'];
                }
                else{
                echo "";
                }
            ?>
            </td>
            <td rowspan="1" ><?= (string)$c['machno']; ?></td>
            <td rowspan="1" ><?= (string)$c['model']; ?></td>
            <td rowspan="1" ><?= (string)$c['lotno']; ?></td>
            <td rowspan="1" ><?= date('d/m/Y', strtotime((string)$c['created_at'])); ?></td>
            <td rowspan="1" ><?= (string)$c['par013']; ?></td>
            <td rowspan="1" ><?= (string)$c['par014']; ?></td>
            <td rowspan="1" ><?= (string)$c['par015']; ?></td>
            <td rowspan="1" ><?= date('H:i', strtotime((string)$c['par008'])); ?></td>
            <td rowspan="1" >
                <?php 
                    if(!(string)$c['par009']) echo "";
                    else echo date('H:i', strtotime((string)$c['par009'])); 
                ?>
            </td>
            <td rowspan="1" ><?= (string)$c['par035']; ?></td>
            <td rowspan="1" ><?= (string)$c['par036']; ?></td>
            <td rowspan="1" ><?= (string)$c['par037']; ?></td>
            <td rowspan="1" ><?= (string)$c['par038']; ?></td>
            <td rowspan="1" ><?= (string)$c['par039']; ?></td>
            <td rowspan="1" ><?= (string)$c['par040']; ?></td>
            <td rowspan="1" ><?= (string)$c['par041']; ?></td>
            <td rowspan="1" ><?= (string)$c['par042']; ?></td>
            <td rowspan="1" ><?= (string)$c['par017']; ?></td>
            <td rowspan="1" ><?= (string)$c['par018']; ?></td>
            <td rowspan="1" ><?= (string)$c['par019']; ?></td>
            <td rowspan="1" ><?= (string)$c['par020']; ?></td>
            <td rowspan="1" ><?= (string)$c['par021']; ?></td>
            <td rowspan="1" ><?= (string)$c['par022']; ?></td>
            <td rowspan="1" ><?= (string)$c['par023']; ?></td>
            <td rowspan="1" ><?= (string)$c['par024']; ?></td>
            <td rowspan="1" ><?= (string)$c['par043']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>