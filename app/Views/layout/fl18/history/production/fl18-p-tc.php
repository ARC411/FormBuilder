<div class="table-responsive mt-3" id="fl18-p-tc">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1" data-cols-width="10,15,15,15,10,10,20,10,10,10,10,10,10,20,20,10,20">
        <thead align="center">
            <tr>
            <th colspan="31" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET TIE BAR CUTTING (FRAME LASER 1.8t AND 1.3t)")?></th>
            </tr>
        </thead>
        <thead align="center">
            <tr>
                <th rowspan="3" >No.</th>
                <th rowspan="3" >Operator Start</th>
                <th rowspan="3" >Shift/Group</th>
                <th rowspan="3" >Operator Finish</th>
                <th rowspan="3" >Shift/Group</th>
                <th rowspan="3" >Mch. No.</th>         
                <th rowspan="3" >Model</th>
                <th rowspan="3" >Lot No.</th> 
                <th rowspan="3" >Date (dd/mm/yyyy)</th>
                <th colspan="3" rowspan="2" scope="col">Quantity</th>
                <th colspan="2" rowspan="2" scope="col">Time</th>
                <th colspan="3" scope="col">*Foregoing Result</th>
                <th rowspan="3" >Cutter Usage Time</th>
                <th rowspan="3" >Sample</th>
                <th colspan="6" scope="col">@Foregoing <br>Pengukuran panjang lead & bari <br>(2 pcs/hari)</th>
                <th colspan="6" scope="col">@Pengukuran panjang lead & bari <br>setelah penggantian cutter <br>(2 pcs pada lot saat penggantian)</th>
            </tr>
            <tr>
                <th colspan="3" scope="col">(20 pcs / hari)</th>
                <th colspan="3" scope="col">Panjang Lead FL 1.8t & FL 1.3t <br>Standard 3.5 ~ 4.5mm</th>
                <th colspan="3" scope="col">Panjang Bari FL 1.8t & FL 1.3t <br>Standard -0.08 ~ 0.08mm</th>
                <th colspan="3" scope="col">Panjang Lead FL 1.8t & FL 1.3t <br>Standard 3.5 ~ 4.5mm</th>
                <th colspan="3" scope="col">Panjang Bari FL 1.8t & FL 1.3t <br>Standard -0.08 ~ 0.08mm</th>
            </tr>
            <tr>
                <th>Input</th>
                <th>Output</th>
                <th>NG</th>
                <th>IN</th>
                <th>OUT</th>
                <th>Scratch At Outer Lead</th>
                <th>Scratch At Resin Part</th>
                <th>Cap NG</th>
                <th>Lead 1</th>
                <th>Lead 2</th>
                <th>Lead 3</th>
                <th>Bari 1</th>
                <th>Bari 2</th>
                <th>Bari 3</th>
                <th>Lead 1</th>
                <th>Lead 2</th>
                <th>Lead 3</th>
                <th>Bari 1</th>
                <th>Bari 2</th>
                <th>Bari 3</th>
            </tr>
        </thead>
    
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+2) { ?>
            <tr>
                <td rowspan="2" ><?= $i/2+1; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['name'];?></td>
                    <td rowspan="2" ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                    <td rowspan="2" >
                    <?php
                        if((string)$alldata[$i]['name2']){
                        echo  (string)$alldata[$i]['name2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                    <td rowspan="2" >
                    <?php
                        if((string)$alldata[$i]['shift2']){
                        echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                <td rowspan="2" ><?= (string)$alldata[$i]['machno']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['model']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['lotno']; ?></td>
                <td rowspan="2" ><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par013']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par014']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par015']; ?></td>
                <td rowspan="2" ><?= date('H:i', strtotime((string)$alldata[$i]['par008'])); ?></td>
                <td rowspan="2">
                  <?php
                    if((string)$alldata[$i]['par009']){
                      echo date('H:i', strtotime((string)$alldata[$i]['par009'])); 
                    }
                    else{
                      echo "";
                    }
                  ?>
                </td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par035']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par036']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par037']; ?></td>
                <td rowspan="2" ><?= (string)$alldata[$i]['par017']; ?></td>
                <td rowspan="1" ><b>1</b></td>
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
            </tr>
            <tr>
                <td rowspan="1" ><b>2</b></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par018'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par019'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par020'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par021'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par022'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par023'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par024'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par025'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par026'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par027'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par028'] : ''; ?></td>
                <td rowspan="1" ><?= isset($alldata[$i+1]) ? (string)$alldata[$i+1]['par029'] : ''; ?></td>
                
            </tr>
            <?php } ?>
        </tbody>
    </table>      
</div>