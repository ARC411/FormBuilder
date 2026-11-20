<?php require_once(dirname( __FILE__ ). '/'.'../../../fillHistory.php'); ?>
<div class="table-responsive mt-3" id="fl18-f-fdb1">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1" data-cols-width="10,15,15,15,10,10,20,21,10,10,10,10,10,10,10,10,10,10,10,10">
        <thead align="center">
        <tr>
            <th colspan="24" scope="col"><?php echo nl2br("DIE BONDING 1 FOREGOING CHECK SHEET\n(FRAME LASER 1.8t AND 1.3t)")?></th>
        </tr>
        </thead>
        <thead align="center">
            <tr>
                <th rowspan="3" >No.</th>
                <th rowspan="3" >Mch. No.</th>         
                <th rowspan="3" >Model</th>
                <th rowspan="3" >Lot No.</th> 
                <th rowspan="3" >Shift</th>
                <th rowspan="3" >Group</th>
                <th rowspan="3" >Date (dd/mm/yyyy)</th>
                <th rowspan="3" >Measure Die Bond Pos</th>
                <th colspan="2" rowspan="2" scope="col" >Foregoing Check Sample No (2pcs/lot)</th>
                <th rowspan="3" >Judge</th>
                <th colspan="6" >Change Bonding Offset Parameter</th>
                <th colspan="2" rowspan="2" scope="col" >Foregoing Check Sample No (2pcs/lot)</th>
                <th rowspan="3" >Judge</th>
                <th colspan="6" >Change Bonding Offset Parameter</th>
            </tr>
        
            <tr>
                <th scope="col" colspan="2">X</th>
                <th scope="col" colspan="2">Z</th>
                <th scope="col" colspan="2">&theta;</th>
                <th scope="col" colspan="2">X</th>
                <th scope="col" colspan="2">Z</th>
                <th scope="col" colspan="2">&theta;</th>
            </tr>

            <tr>
                <th >1</th>
                <th >2</th>
                <th >From</th>
                <th >To</th>
                <th >From</th>
                <th >To</th>
                <th >From</th>
                <th >To</th>
                <th >1</th>
                <th >2</th>
                <th >From</th>
                <th >To</th>
                <th >From</th>
                <th >To</th>
                <th >From</th>
                <th >To</th>
            </tr>
        
        
        </thead>
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+3) { ?>
            <tr>
                <td rowspan="3" ><?= $i/3+1; ?></td>
                <td rowspan="3" ><?= (string)$alldata[$i]['machno']; ?></td>
                <td rowspan="3" ><?= (string)$alldata[$i]['model']; ?></td>
                <td rowspan="3" ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td rowspan="3" ><?= (string)$alldata[$i]['shift']; ?></td>
                <td rowspan="3" ><?= (string)$alldata[$i]['group']; ?></td>
                <td rowspan="3" ><?= date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
                <td rowspan="1" ><b>X</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par035']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par036']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par023']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par024']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par025']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par026']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par027']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i]['par028']; ?></td>
            </tr>
            <tr>
                <td rowspan="1" ><b>Z</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par035']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par036']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par023']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par024']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par025']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par026']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par027']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+1]['par028']; ?></td>
            </tr>
            <tr>
                <td rowspan="1" ><b>Tetha</b></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par013']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par014']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par035']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par015']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par016']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par017']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par018']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par019']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par020']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par021']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par022']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par036']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par023']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par024']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par025']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par026']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par027']; ?></td>
                <td rowspan="1" ><?= (string)$alldata[$i+2]['par028']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
        </table>

        
    </div>
</div>