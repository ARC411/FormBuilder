<div class="table-responsive mt-3" id="sl56-s-ldb1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 10+$lenData ?> scope="col"><?php echo nl2br("LD DIE BONDING 1 MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="2" rowspan="2" scope="col">Start up Check Item</th>
            <th colspan="7" rowspan="2" scope="col" >Standard</th>
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
            <td >1</td>
            <td colspan="2">@Air Pressure 1</td>
            <td colspan="7">0.05 - 0.25 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >2</td>
            <td colspan="2">@Air Pressure 2</td>
            <td colspan="7">0.30 - 0.50 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >3</td>
            <td colspan="2">@LD Collet Vacuum Level</td>
            <td colspan="7">-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">4</td>
            <td rowspan="3">@Vacum Pressure</td>
            <td >SM collet</td>
            <td colspan="7">-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >Middle collet</td>
            <td colspan="7">-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >Die Bonding stage</td>
            <td colspan="7">-80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="4">5</td>
            <td rowspan="4">@Collet Load (N)</td>
            <td >LD</td>
            <td >T5</td>
            <td >T9</td>
            <td >T12</td>
            <td >T13</td>
            <td >T14</td>
            <td >T15</td>
            <td >T16</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >L < 800 um</td>
            <td rowspan="2">0.20 - 0.30</td>
            <td >0.20 - 0.40</td>
            <td rowspan="2">0.20 - 0.30</td>
            <td rowspan="2">0.20 - 0.30</td>
            <td >0.20-0.40</td>
            <td rowspan="2">0.20-0.30</td>
            <td rowspan="2">0.20-0.30</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >L > 800 um</td>
            <td >0.05 - 0.15</td>
            <td >0.05 - 0.15</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >SM</td>
            <td colspan="7">0.60 - 0.80 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >7</td>
            <td colspan="9">* Pengecekan posisi die bonding (Check posisi die bonding pada monitor)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">8</td>
            <td colspan="2" rowspan="3">*Collet Cleaning <br> (Bersihkan dengan cotton bud yang dibasahi alkohol)</td>
            <td colspan="7">Collet A</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="7">Collet B</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="7">Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="2">9 </td>
            <td colspan="2" rowspan="2">Check arah ion blower fan </td>
            <td colspan="7">Posisi blower </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="7">Putaran Blower </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >10 </td>
            <td colspan="9">*Pengecekan kondisi collet guide (check apakah collet guide goyang) </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="11">11 </td>
            <td colspan="2">Setting</td>
            <td >T5</td>
            <td >T9</td>
            <td >T12</td>
            <td >T13</td>
            <td >T14</td>
            <td >T15</td>
            <td >T16</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Temperature Setting</td>
            <td >285-325</td>
            <td >260-300</td>
            <td >295-335</td>
            <td >280-320</td>
            <td >260-300</td>
            <td >310-350</td>
            <td >280-320</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rise Time, s</td>
            <td >0.3 - 0.7</td>
            <td >0.5</td>
            <td >0.5</td>
            <td >0.5</td>
            <td >0.5</td>
            <td >0.5</td>
            <td >0.5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Keep Time, s</td>
            <td >0.8 - 1.2</td>
            <td >1</td>
            <td >2</td>
            <td >1</td>
            <td >1</td>
            <td >4</td>
            <td >2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Temperature Setting</td>
            <td >0</td>
            <td >360-400</td>
            <td >0</td>
            <td >0</td>
            <td >330-370</td>
            <td >0</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rise Time, s</td>
            <td >0</td>
            <td >0.5</td>
            <td >0</td>
            <td >0</td>
            <td >0.5</td>
            <td >0</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Keep Time, s</td>
            <td >0</td>
            <td >1.5</td>
            <td >0</td>
            <td >0</td>
            <td >1.5</td>
            <td >0</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Temperature Idle</td>
            <td >180-220</td>
            <td >250-290</td>
            <td >180-220</td>
            <td >180-220</td>
            <td >250-290</td>
            <td >180-220</td>
            <td >180-220</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Solid Point</td>
            <td >idle +10</td>
            <td >290-310</td>
            <td >idle +10</td>
            <td >idle +10</td>
            <td >290-310</td>
            <td >idle +10</td>
            <td >idle +10</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rise Point</td>
            <td >285-325</td>
            <td >260-300</td>
            <td >295-335</td>
            <td >280-320</td>
            <td >260-300</td>
            <td >310-350</td>
            <td >280-320</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par027']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rising Detect, s</td>
            <td >2.5</td>
            <td >2.5</td>
            <td >2.5</td>
            <td >2.5</td>
            <td >2.5</td>
            <td >2.5</td>
            <td >2.5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par028']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">12</td>
            <td rowspan="3">* LD Collet Lower Position</td>
            <td >Chip Sheet</td>
            <td colspan="7">0 - 30 µm dari atas permukaan chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par029']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >Middle collet</td>
            <td colspan="7">20- 60 µm dari atas permukaan tengah stage</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par030']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >D/B Stage</td>
            <td colspan="7">20- 60 µm dari atas permukaan sub chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par031']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">13</td>
            <td rowspan="3">* SM Collet Lower Position</td>
            <td >Chip Sheet</td>
            <td colspan="7">0 - 50 µm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par032']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >D/B Stage</td>
            <td colspan="7">50 - 100 µm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par033']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >Receipt Part</td>
            <td colspan="7">50 - 100 µm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par034']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="7">14 </td>
            <td colspan="2">Parameter setting value @ :</td>
            <td >T5</td>
            <td >T9</td>
            <td >T12</td>
            <td >T13</td>
            <td >T14</td>
            <td >T15</td>
            <td >T16</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par035']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">a. No.42 Lower position time of collet BST part</td>
            <td >0-500</td>
            <td >0-500</td>
            <td >0-450</td>
            <td >0-40</td>
            <td >270-290</td>
            <td >0-40</td>
            <td >000-1040</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par036']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">b. No.216 Auto start frequency of LD collet BST low</td>
            <td >300</td>
            <td >600</td>
            <td >300</td>
            <td >300</td>
            <td >600</td>
            <td >1200</td>
            <td >300</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par037']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">c. No.217 Maximum frequency of LD collet BST low</td>
            <td >300</td>
            <td >600</td>
            <td >300</td>
            <td >300</td>
            <td >600</td>
            <td >1200</td>
            <td >300</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par038']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">d. No.287 Waiting time of BST collet descent</td>
            <td >10</td>
            <td >0</td>
            <td >10</td>
            <td >10</td>
            <td >0</td>
            <td >10</td>
            <td >10</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par039']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">e. No.292 Waiting time of take out on take out PP BST</td>
            <td >0</td>
            <td >0-3000</td>
            <td >1000</td>
            <td >0</td>
            <td >0-3000</td>
            <td >0</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par040']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">f. No.293 On timing of heating</td>
            <td >0</td>
            <td >0</td>
            <td >0</td>
            <td >0</td>
            <td >0</td>
            <td >0</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par041']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>15</td>
            <td colspan="9">* Cleaning Machine (Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran)
            </td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par042']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td >16 </td>
            <td colspan="2">@N2 setting, l/m</td>
            <td >5-6</td>
            <td >4-6</td>
            <td >5-6</td>
            <td >5-6</td>
            <td >4-6</td>
            <td >5-6</td>
            <td >5-6</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par043']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="10">Note #</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par044']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="10">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="10">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="1">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td colspan="1">  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="10">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
      </tbody>
    </table>
</div>