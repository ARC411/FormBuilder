<div class="table-responsive mt-3" id="sl56-s-fldb1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" name="cnt-proses" value="2">
<input type="hidden" id="cnt-table" value="2">
<table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
                <th colspan="14" scope="col"><?php echo nl2br("FOREGOING LD DIE BONDING 1 MACHINE")?></th>
            </tr>
            <tr>
                <th rowspan="7" >No.</th>
                <th rowspan="7" >Mch. No.</th>         
                <th rowspan="7" >Model</th>
                <th rowspan="7" >Lot No.</th>
                <th rowspan="7" >Sample</th>
                <th colspan="4" rowspan="6" scope="col" >Measuring of die bonding position (2pcs/lot)</th>
                <th>Die Bonding Strength (2pcs/lot)</th>
                <th scope="col">Kondisi Belakang I D chip (2pcs/lot)</th>
                <th rowspan="7">Status Approval</th>
                <th rowspan="7">Operator</th>
                <th rowspan="7">Time</th>

            </tr>
            <tr>
                <th>Model GH07825/30D2K : ≥ 40g (0.4N)</th>
                <th scope="col">Model GH06P25A1CH 1pcs/lot</th>
            </tr>
            <tr>
                <th>Model GH06P25series : ≥ 35g (0.35N)</th>
                <th rowspan="2" scope="col">Standard : 80% area good colour</th>
            </tr>
            <tr>
                <th>Model GH0633series, GH06315series, GH06610A2KSeries : ≥ 50g (0.5N)</th>
            </tr>
            <tr>
                <th>Model GH03, GH04 & GH05series </th>
                <th rowspan="2" scope="col">2 ch chip : 60% area good colour</th>
            </tr>
            <tr>
                <th>Model GH0832BA2K/BA1K/GH3Series & Othors Model: ≥ 150g (1.5N) </th>
            </tr>
            <tr>
                <th >Point 1 (0)</th>
                <th >Point 2 (X)</th>
                <th >Point 3 (Y)</th>
                <th >Point 4 </th>
                <th >Result</th>
                <th >Result</th>
            </tr>
        </thead>
        
        <tbody>
            <?php for($i = 0; $i < count($alldata); $i=$i+2) { ?>
            <tr>
                <td rowspan="2" ><?= $i/2+1; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['machno']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['model']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td rowspan="1" ><b>Sample 1</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par023']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par024']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par025']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par026']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['status']; ?></td>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td rowspan=2>'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td rowspan=2>  </td>';
                    }
                ?>
                <td rowspan="2" ><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            </tr>
            <tr>
                <td rowspan="1" ><b>Sample 2</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par023']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par024']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par025']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par026']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>      
</div>