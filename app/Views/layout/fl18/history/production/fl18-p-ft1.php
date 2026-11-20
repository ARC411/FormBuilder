<div class="table-responsive mt-3" id="fl18-p-ft1">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover"  id="table1">
        <thead align="center">
            <tr>
            <th colspan="22" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET OF FINAL TEST GH15SERIES MODEL\n(FRAME LASER 1.8t)")?></th>
            </tr>
        </thead>
        <thead align="center">
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
                <th >lfP/RdP/ldk1/lmp/mKi (00) </th>
                <th >lf.LD (01) </th>
                <th >LD-ir (02)</th>
                <th >Vop (03)</th>
                <th >lop (04)</th>
                <th >lth (05)</th>
                <th >Eta (06)</th>
                <th >L.Kink2 (07)</th>
                <th >L.Kink3 (08)</th>
                <th >L.Kink4 (09)</th>
            </tr>
        
            <tr>                
                <th rowspan="2">GO</th>
                <th rowspan="2">NG</th>
                <th>Pol (0A)</th>
                <th>PhPl (0B)</th>
                <th>ThV (0C)</th>
                <th>Thh (0D)</th>
                <th>Phv (0E)</th>
                <th>Phh (1F)</th>
                <th>Rpv2 (10)</th>
                <th>Rph2 (11)</th>
                <th>Lp (12)</th>
                <th>LD Short (13)</th>
            </tr>
            <tr>  
                <th scope="col">LD Open (14)</th>
                <th scope="col">lop.Err (15)</th>
                <th scope="col">Pol.Err (17)</th>
                <th scope="col">FFV.Err (18)</th>
                <th scope="col">FFh.Err (19)</th>
                <th scope="col">SP.Err (1A)</th>
                <th scope="col">Ldir.Err (1B)</th>
                <th scope="col">m.Err/PDIr.Err (1C)</th>
                <th scope="col">Rpv3 (1D)</th>
                <th scope="col">Rph3 (1E)</th>
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
