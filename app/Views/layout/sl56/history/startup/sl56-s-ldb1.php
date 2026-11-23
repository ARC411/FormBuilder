<div class="table-responsive mt-3" id="sl56-s-ldb1">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 11+$lenData ?> scope="col"><?php echo nl2br("LD DIE BONDING 1 MACHINE START UP CHECK SHEET\n(Single Laser 5.6, All Type)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="2" rowspan="2" scope="col">Start up Check Item</th>
            <th colspan="8" rowspan="2" scope="col">Standard</th>
            <th scope="col" colspan=<?= $lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= getRecordWorkDate($alldata[$i], 'd/m/Y'); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <!-- 1.0 Air Pressure 1 -->
        <tr>
            <td>1</td>
            <td colspan="2">@Air Pressure 1</td>
            <td colspan="8">0.05 - 0.25 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <!-- 2.0 Air Pressure 2 -->
        <tr>
            <td>2</td>
            <td colspan="2">@Air Pressure 2</td>
            <td colspan="8">0.30 - 0.50 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <!-- 3.0 LD Collet Vacuum Level -->
        <tr>
            <td>3</td>
            <td colspan="2">@LD Collet Vacuum Level</td>
            <td colspan="8">- 80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <!-- 4.0 Vacum Pressure -->
        <tr>
            <td rowspan="3">4</td>
            <td rowspan="3">@Vacum Pressure</td>
            <td>SM collet</td>
            <td colspan="8">- 80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Middle collet</td>
            <td colspan="8">- 80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Die Bonding stage</td>
            <td colspan="8">- 80.0 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <!-- 5.0 Collet Load (N) -->
        <tr>
            <td rowspan="4">5</td>
            <td rowspan="4">@Collet Load (N)</td>
            <td>LD</td>
            <td>T5</td>
            <td>T9</td>
            <td>T12</td>
            <td>T13</td>
            <td>T14</td>
            <td>T15</td>
            <td>T16</td>
            <td>T17</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>L &le; 800 mm</td>
            <td rowspan="2">0.20-0.30</td>
            <td>0.20-0.40</td>
            <td rowspan="2">0.20-0.30</td>
            <td rowspan="2">0.20-0.30</td>
            <td>0.20-0.40</td>
            <td rowspan="2">0.20-0.30</td>
            <td rowspan="2">0.20-0.30</td>
            <td rowspan="2">0.20-0.30</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>L &gt; 800 mm</td>
            <td>0.05-0.15</td>
            <td>0.05-0.15</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>SM</td>
            <td colspan="8">0.60 - 0.80 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <!-- 6.0 Needle Push Up Level -->
        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2">@Needle Push Up Level</td>
            <td>LD</td>
            <td colspan="8">0.20 - 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par045'] ?? '-'); ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>SM</td>
            <td colspan="8">0.20 - 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par046'] ?? '-'); ?></td>
            <?php } ?>
        </tr>

        <!-- 7.0 Pengecekan posisi die bonding -->
        <tr>
            <td>7</td>
            <td colspan="10">* Pengecekan posisi die bonding (Check posisi die bonding pada monitor)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>

        <!-- 8.0 Collet Cleaning -->
        <tr>
            <td rowspan="3">8</td>
            <td colspan="2" rowspan="3">*Collet Cleaning <br> (Bersihkan dengan cotton bud yang dibasahi alkohol)</td>
            <td colspan="8">Collet A</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="8">Collet B</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="8">Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <!-- 9.0 Check arah ion blower fan -->
        <tr>
            <td rowspan="2">9</td>
            <td colspan="2" rowspan="2">*Check arah ion blower fan</td>
            <td colspan="8">Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="8">Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <!-- 10.0 Pengecekan kondisi collet guide -->
        <tr>
            <td>10</td>
            <td colspan="10">*Pengecekan kondisi collet guide (check apakah collet guide goyang)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>
        
        <!-- 11.0 Heater Setting -->
        <tr>
            <td rowspan="11">11</td>
            <td colspan="2">@Heater Setting (Setting)</td>
            <td>T5</td>
            <td>T9</td>
            <td>T12</td>
            <td>T13</td>
            <td>T14</td>
            <td>T15</td>
            <td>T16</td>
            <td>T17</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">STEP 1</td>
            <td>Temperature Setting, &deg;C</td>
            <td>285-325</td>
            <td>260-300</td>
            <td>295-335</td>
            <td>280-320</td>
            <td>260-300</td>
            <td>310-350</td>
            <td>280-320</td>
            <td>320-340</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Rise Time, s</td>
            <td>0.3-0.7</td>
            <td>0.5</td>
            <td>0.5</td>
            <td>0.5</td>
            <td>0.5</td>
            <td>0.5</td>
            <td>0.5</td>
            <td>0.5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Keep Time, s</td>
            <td>0.8-1.2</td>
            <td>1.0</td>
            <td>2.0</td>
            <td>1.0</td>
            <td>1.0</td>
            <td>4.0</td>
            <td>2.0</td>
            <td>1.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td rowspan="3">STEP 2</td>
            <td>Temperature Setting, &deg;C</td>
            <td>0.0</td>
            <td>360-400</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>330-370</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Rise Time, s</td>
            <td>0.0</td>
            <td>0.5</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.5</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Keep Time, s</td>
            <td>0.0</td>
            <td>1.5</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>1.5</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Temperature Idle, &deg;C</td>
            <td>180-220</td>
            <td>250-290</td>
            <td>180-220</td>
            <td>180-220</td>
            <td>250-290</td>
            <td>180-220</td>
            <td>180-220</td>
            <td>180-220</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Solid Point, &deg;C</td>
            <td>idle +10</td>
            <td>290-310</td>
            <td>idle +10</td>
            <td>idle +10</td>
            <td>290-310</td>
            <td>idle +10</td>
            <td>idle +10</td>
            <td>idle +10</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rise Point, &deg;C</td>
            <td>285-325</td>
            <td>260-300</td>
            <td>295-335</td>
            <td>280-320</td>
            <td>260-300</td>
            <td>310-350</td>
            <td>280-320</td>
            <td>320-340</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par027']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">Rising Detect, s</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <td>2.5</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par028']; ?></td>
            <?php } ?>
        </tr>

        <!-- 12.0 LD Collet Lower Position -->
        <tr>
            <td rowspan="3">12</td>
            <td rowspan="3">*LD Collet Lower Position</td>
            <td>Chip Sheet</td>
            <td colspan="8">0 - 30 mm dari atas permukaan chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par029']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Middle collet</td>
            <td colspan="8">20 - 60 mm dari atas permukaan tengah stage</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par030']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>D/B Stage</td>
            <td colspan="8">20 - 60 mm dari atas permukaan sub chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par031']; ?></td>
            <?php } ?>
        </tr>

        <!-- 13.0 SM Collet Lower Position -->
        <tr>
            <td rowspan="3">13</td>
            <td rowspan="3">*SM Collet Lower Position</td>
            <td>Chip Sheet</td>
            <td colspan="8">0 - 50 mm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par032']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>D/B Stage</td>
            <td colspan="8">50 - 100 mm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par033']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Receipt Part</td>
            <td colspan="8">50 - 100 mm di bawah chip</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par034']; ?></td>
            <?php } ?>
        </tr>

        <!-- 14.0 Parameter setting value -->
        <tr>
            <td rowspan="7">14</td>
            <td colspan="2">Parameter setting value @ :</td>
            <td>T5</td>
            <td>T9</td>
            <td>T12</td>
            <td>T13</td>
            <td>T14</td>
            <td>T15</td>
            <td>T16</td>
            <td>T17</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par035']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">a. No.42 Lower position time of collet BST part</td>
            <td>0-500</td>
            <td>0-500</td>
            <td>0-450</td>
            <td>0-40</td>
            <td>270-290</td>
            <td>0-40</td>
            <td>1000-1040</td>
            <td>1000-1040</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par036']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">b. No.216 Auto start frequency of LD collet BST slow</td>
            <td>300.0</td>
            <td>600.0</td>
            <td>300.0</td>
            <td>300.0</td>
            <td>600.0</td>
            <td>1200.0</td>
            <td>300.0</td>
            <td>300.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par037']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">c. No.217 Maximum frequency of LD collet BST slow</td>
            <td>300.0</td>
            <td>600.0</td>
            <td>300.0</td>
            <td>300.0</td>
            <td>600.0</td>
            <td>1200.0</td>
            <td>300.0</td>
            <td>300.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par038']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">d. No.287 Waiting time of BST collet descent</td>
            <td>10.0</td>
            <td>0.0</td>
            <td>10.0</td>
            <td>10.0</td>
            <td>0.0</td>
            <td>10.0</td>
            <td>10.0</td>
            <td>10.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par039']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">e. No.292 Waiting time of take out on take out PP BST</td>
            <td>0.0</td>
            <td>0-3000</td>
            <td>1000.0</td>
            <td>0.0</td>
            <td>0-3000</td>
            <td>3000.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par040']; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td colspan="2">f. No.293 On timing of heating</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <td>0.0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par041']; ?></td>
            <?php } ?>
        </tr>

        <!-- 15.0 Cleaning Machine -->
        <tr>
            <td>15</td>
            <td colspan="10">*Cleaning Machine (Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par042']; ?></td>
            <?php } ?>
        </tr>

        <!-- 16.0 N2 setting -->
        <tr>
            <td>16</td>
            <td colspan="2">@N2 setting, l/m</td>
            <td>5-6</td>
            <td>4-6</td>
            <td>5-6</td>
            <td>5-6</td>
            <td>4-6</td>
            <td>5-6</td>
            <td>5-6</td>
            <td>5-6</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par043']; ?></td>
            <?php } ?>
        </tr>

        <!-- Note # -->
        <tr>
            <td colspan="11">Note #</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par044']; ?></td>
            <?php } ?>
        </tr>

        <!-- Approval Details -->
        <tr>
            <td colspan="11">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="11">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="1"><?= esc(getOperatorName($alldata[$i] ?? null)); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="11">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
      </tbody>
    </table>
</div>
