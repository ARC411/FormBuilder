<div class="table-responsive mt-3" id="sl56-s-lt">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
            <th colspan=<?= 5+$lenData ?> scope="col"><?php echo nl2br("LEAK TESTING MACHINE START UP")?></th>
            </tr>
        </thead>
        <thead align="center">
            <tr>
                <th scope="col" rowspan="2">No</th>
                <th colspan="3" rowspan="2" scope="col" >Start Up Check Item</th>
                <th scope="col" rowspan="2">Standard</th>
                <th scope="col" colspan=<?= $lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
        </thead>

        <tbody>
            <tr>
                <td>1</td>
                <td colspan="3">Checking air pressure (Mengecek air pressure)</td>
                <td >0.49MPa ± 0.05MPa</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td>2</td>
                <td colspan="3">Checking He gas level (Mengecek He gas level)</td>
                <td >Kurang dari 5.0 x 10 Pa.m/s</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td rowspan="3">3</td>
                <td rowspan="3">Calibration of Machine (Kalibrasi mesin)</td>
                <td rowspan="2">Fine Leak *</td>
                <td >SLT 01</td>
                <td >3.8 ± 0.5 Pa.m/s</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td >SLT 02</td>
                <td >3.6 ± 0.5 Pa.m/s</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="2">Gross Leak</td>
                <td >Menggunakan 40 pcs good sample <br> Result: OK</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td>4</td>
                <td colspan="3">Judgment accuracy (Akurasi judgment)</td>
                <td >Menggunakan masing-masing 40 pcs OK & NG <br> Result: All OK & All NG</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td rowspan="2">5</td>
                <td rowspan="2">Checking Oil (Pengecekkan Oil)</td>
                <td colspan="2">EDWARDS</td>
                <td >Level oil antara Min dan Max</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="2">ULVAC</td>
                <td >Level oil antara Min dan Max</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="5">Note</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="5">Status Approval</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="5">Operator</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <?php
                        $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                            echo '<td colspan="col">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                        } 
                        else{
                            echo '<td>  </td>';
                        }
                    ?>
                <?php } ?>
            </tr>

            <tr>
                <td colspan="5">Time</td>
                <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                    <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
                <?php } ?>
            </tr>
        </tbody>   
    </table>
</div>