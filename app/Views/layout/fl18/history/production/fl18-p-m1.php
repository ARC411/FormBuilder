<div class="table-responsive mt-3" id="fl18-p-m1">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">

    <table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
                <th colspan="17" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET HARDENING OF INK\n(FRAME LASER 1.8t)")?></th>
            </tr>
            <tr>
                <th rowspan="2" >No.</th>
                <th rowspan="2" >Operator Start</th>
                <th rowspan="2" >Shift/Group</th>
                <th rowspan="2" >Operator Finish</th>
                <th rowspan="2" >Shift/Group</th>
                <th rowspan="2" >Mch. No.</th>         
                <th rowspan="2" >Model</th>
                <th rowspan="2" >Lot No.</th> 
                <th colspan="2" >Operator</th>
                <th colspan="3" >Temperature</th>
                <th colspan="2" >Time</th>
                <th >Buka Tutup Damper</th>
                <th >Pencegah Over Heat</th>
            </tr>
            <tr>
                <th >Input to oven</th>
                <th >Take out from oven</th>
                <th >Setting</th>
                <th >At Input</th>
                <th >At take Out</th>
                <th>Input</th>
                <th>At take Out</th>
                <th>Setting</th>
                <th>Setting</th>
            </tr>
        </thead>
        <tbody>
            <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
                <tr>
                    <td ><?= $i+1; ?></td>
                    <td ><?= (string)$alldata[$i]['name'];?></td>
                    <td ><?= (string)$alldata[$i]['shift'] .' / '. (string)$alldata[$i]['group'];?></td>
                    <td >
                    <?php
                        if((string)$alldata[$i]['name2']){
                        echo  (string)$alldata[$i]['name2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                    <td >
                    <?php
                        if((string)$alldata[$i]['shift2']){
                        echo (string)$alldata[$i]['shift2'] . ' / ' . (string)$alldata[$i]['group2'];
                        }
                        else{
                        echo "";
                        }
                    ?>
                    </td>
                    <td ><?= (string)$alldata[$i]['machno']; ?></td>
                    <td ><?= (string)$alldata[$i]['model']; ?></td>
                    <td ><?= (string)$alldata[$i]['lotno']; ?></td>
                    <td ><?= (string)$alldata[$i]['name'] . "(".(string)$alldata[$i]['shift'].")"; ?></td>
                    <td ><?= (string)$alldata[$i]['name2'] . "(".(string)$alldata[$i]['shift2'].")"; ?></td>
                    <td ><?= (string)$alldata[$i]['par013']; ?></td>
                    <td ><?= (string)$alldata[$i]['par014']; ?></td>
                    <td ><?= (string)$alldata[$i]['par015']; ?></td>
                    <td ><?= date('H:i', strtotime((string)$alldata[$i]['par008'])); ?></td>
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
                    <td ><?= (string)$alldata[$i]['par002']; ?></td>
                    <td ><?= (string)$alldata[$i]['par003']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
