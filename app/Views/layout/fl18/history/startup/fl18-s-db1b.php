<?php require_once(dirname( __FILE__ ). '/'.'../../../fillHistory.php'); ?>
<div class="table-responsive mt-3" id="fl18-s-db1b">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
        <th colspan=<?= 4+$lenData ?> scope="col"><?php echo nl2br("LD DIE BONDING 1 MACHINE Model GH15*30*8C* / GH33*****C*")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start Up Check Item</th>
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
            <td >1</td>
            <td >@ Air Pressure 1</td>
            <td >0.05 ~ 0.25 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >2</td>
            <td >@ Air Pressure 2</td>
            <td >0.30 ~ 0.50 MPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >@ Sub Mount (SM) Collet Vacum Pressure</td>
            <td >-80 kPa ~ -95.0 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td >@ Middle Collet Vacum Pressure</td>
            <td >-80 kPa ~ -95.0 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >5</td>
            <td >@ Die Bonding stage Vacum Pressure</td>
            <td >-80 kPa ~ -95.0 kPa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td >@ LD collet Load</td>
            <td >0.2 ~ 0.30 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >7</td>
            <td >@ SM collet Load</td>
            <td >0.60 ~ 0.80 N</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >8</td>
            <td >@ LD Needle Push Up Level</td>
            <td >0.20 ~ 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008'];?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >9</td>
            <td >@ SM Needle Push Up Level</td>
            <td >0.20 ~ 0.60 mm</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >10</td>
            <td >* Pengecekan bentuk needle ejector</td>
            <td >Jika bentuk needle sudah rusak (gompal) segera lakukan pergantian dengan yang baru. Jika OK lanjutkan proses</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >11</td>
            <td >* Pengecekan posisi Die Bonding</td>
            <td >Cek posisi die bonding pada tampilan monitor</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
            <?php } ?>
        </tr>
       
        <tr>
            <td >12</td>
            <td >* Bersihkan Collet, Middle collet dan D/B stage</td>
            <td >Bersihkan menggunakan cotton bud kering</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >13</td>
            <td >* Arah lonizer Blower</td>
            <td >Arahkan lonizer ke chip sheet dan check apakah lonizer bekerja normal</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >14</td>
            <td >* Cek kondisi collet guide</td>
            <td >Pindahkan collet guide dari posisi atas ke bawah dan cek apakah collet guide goyang atau tidak</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >15</td>
            <td >@ Idle temperature heater</td>
            <td >180 ~ 220 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">16</td>
            <td rowspan="2">@ Setting temperature heater</td>
            <td >Step 1 : 280 ~ 320 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>Step 2 : 0 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { 
                if($alldata[$i]['par017']){
                    echo "ini isi";
                }
                else{
                    echo "apa ini ".(string)$alldata[$i]['par017']." batas";
                }
                ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">17</td>
            <td rowspan="2">@ Rise Time heater</td>
            <td>Step 1 : 0.5 s (UP)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>
        </tr>

        <tr>
            <td>Step 2 : 0 s (UP)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">18</td>
            <td rowspan="2">@ Hold Time heater</td>
            <td >Step 1 : 1.0 s (HT)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Step 2 : 0 s (HT)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par021']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >19</td>
            <td >@ Solid Point Heater</td>
            <td >Sama dengan idle temperature + 10 °C</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par022']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >20</td>
            <td >@Rise Point Heater</td>
            <td >Sama dengan Setting Temperature Step 1</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par023'];?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >21</td>
            <td >@ Profile Detect</td>
            <td > 2.5 s</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par024']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="8">22</td>
            <td >@ Parameter setting value : </td>
            <td >T13</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par025']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >a. No.42 Lower position time of collet BST part</td>
            <td >10 ~ 40</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par026']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >b. No.216 Auto start frequency of LD collet BST slow</td>
            <td > 300</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par027'];?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >c. No.217 Maximum frequency of LD collet BST slow</td>
            <td > 300</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par028'];?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >d. No.287 Waiting time of BST collet descent</td>
            <td >10</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par029']; ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td >e. No.292 Waiting time of take out on take out PP BST</td>
            <td >0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par030']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">f. No.293 On timing of heating</td>
            <td rowspan="2">0</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par031']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par032']; ?></td>
            <?php } ?>
        </tr>


        <tr>
            <td rowspan="3">23</td>
            <td rowspan="3">Pengecekan N2</td>
            <td > @ Nilai aliran N2 : 5 - 6 l/min
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par033']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >* Posisi N2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par034']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >* Hembusan N2</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par035']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >24</td>
            <td >* Pembersihan mesin dan lingkungan sekitarnya</td>
            <td >Bersihkan dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par036']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >25</td>
            <td >* Pengecekan connector wrist strap dan grounding</td>
            <td >Tidak kendor /tidak putus (Jika tidak ada tambahkan)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par037']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >26</td>
            <td >* Cek Kondisi Collet</td>
            <td >Jika terjadi kerusakan atau diluar standard, lakukan pergantian dengan yang baru. Jika OK lanjutkan proses</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par038']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par039']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="3">Operator</td>
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
            <td colspan="3">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+1) { ?>
                <td colspan="col"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>   
    </table>
</div>