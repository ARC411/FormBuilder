<div class="table-responsive mt-3" id="fl18-s-wb2">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan=<?= 5+$lenData ?> scope="col"><?php echo nl2br("WIRE BOND MACHINE START UP (For ASM Machine)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th scope="col" rowspan="2">Start up Check Item</th>
            <th colspan="3" rowspan="2" scope="col" >Standard</th>
            <th scope="col" colspan=<?= $lenData ?>>Hasil Start Up Check</th>
        </tr>

        <tr>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td >* Checking of gold wire route <br> (Pemeriksaan rute gold wire)</td>
            <td colspan="3">Lap & bersihkan wire guide dan ring guide dengan bemcoat beralkohol</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par001']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >2</td>
            <td >@ Checking of Air Pressure <br> (Memeriksa nilai air pressure)</td>
            <td colspan="3">0.35 ~ 0.45 Mpa</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par002']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >3</td>
            <td >* Cleaning of Machine <br> (Membersihkan Mesin)</td>
            <td colspan="3">Bersihkan mesin dari debu dan device yang terjatuh</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par003']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5"></td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td>A</td>
                <td>B</td>
                <td>C</td>
            <?php } ?>
        </tr>

        <tr>
            <td >4</td>
            <td >@ Capilary usage count <br> Pemakaian capilary</td>
            <td colspan="3">600.000 wires</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par004']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par004']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par004']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="12">5</td>
            <td rowspan="12">@ Checking of bonding parameter <br> (Memeriksa parameter bonding)</td>
            <td rowspan="6">LD</td>
            <td rowspan="2">Us-Pwr</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par005']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par005']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par005']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par006']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par006']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par006']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Time</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par007']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par007']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par007']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par008']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par008']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par008']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Force</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par009']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par009']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par009']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par010']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par010']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par010']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="6">Sic Mount</td>
            <td rowspan="2">Us-Pwr</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par011']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par011']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par011']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par012']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par012']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par012']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Time</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par013']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par013']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par013']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par014']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par014']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par014']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Force</td>
            <td >Pad</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par015']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par015']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par015']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >Lead </td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par016']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par016']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par016']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >6</td>
            <td >@ Checking of Temperature (PV) <br> (Memeriksa temperature) </td>
            <td colspan="3">Temperature PV <br>: 175 ~ 185 °C </td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="col"><?= (string)$alldata[$i]['par017']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+1]['par017']; ?></td>
                <td colspan="col"><?= (string)$alldata[$i+2]['par017']; ?></td>
            <?php } ?>
        </tr>
        

        <tr>
            <td >7</td>
            <td >Kondisi Permukaan heater plate</td>
            <td colspan="3">Naik dan turunkan frame clamp kemudian periksa apakah ada sisa gold wire, paste, dll atau heater plate penyok.</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par018']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td >8</td>
            <td >Status frame clamping</td>
            <td colspan="3">Periksa frame pada bagian 1 & 2 bonding tidak mengambang (caranya dengan menekan-nekan frame dengan pinset frame tidak bergerak)</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par019']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Note</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['par020']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Status Approval</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= (string)$alldata[$i]['status']; ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Operator</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <?php
                    $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                        echo '<td colspan="3">'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                    } 
                    else{
                        echo '<td>  </td>';
                    }
                ?>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="5">Time</td>
            <?php for($i = 0; $i < $lenData; $i=$i+3) { ?>
                <td colspan="3"><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            <?php } ?>
        </tr>
    </tbody>
    
    </table>
</div>