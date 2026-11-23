<div class="table-responsive" id="fl18-s-wb3">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-proses" name="cnt-proses" value=3>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("WIRE BOND MACHINE START UP")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th scope="col">Start up Check Item</th>
            <th colspan="3" scope="col" >Standard</th>
            <th colspan="3" scope="col" >Hasil Start Up</th>
        </tr>
    </thead>

    <tbody>
        <tr>
          <td >1</td>
          <td >* Checking of gold wire route <br> (Pemeriksaan rute gold wire)</td>
          <td colspan="3">Lap & bersihkan wire guide dan ring guide dengan bemcoat beralkohol</td>
          <td colspan="3">
            <input class="form-control" type="text" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>

        <tr>
          <td >2</td>
          <td >@ Checking of Air Pressure <br> (Memeriksa nilai air pressure)</td>
          <td colspan="3">0.35 ~ 0.55 Mpa</td>
          <td colspan="3">
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.35,0.55)" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          </td>
        </tr>

        <tr>
          <td >3</td>
          <td >* Cleaning of Machine <br> (Membersihkan Mesin)</td>
          <td colspan="3">Bersihkan mesin dari debu dan device yang terjatuh</td>
          <td colspan="3">
            <input class="form-control" type="text" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td colspan="3"></td>
            <td>A</td>
            <td>B</td>
            <td>C</td>
        </tr>

        <tr>
          <td >4</td>
          <td >@ Capilary usage count <br> Pemakaian capilary</td>
          <td colspan="3">600.000 wires</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par004a" name="par004a">
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par004b" name="par004b">
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par004c" name="par004c">
          </td>
        </tr>

        <tr>
          <td rowspan="12">5</td>
          <td rowspan="12">@ Checking of bonding parameter <br> (Memeriksa parameter bonding)</td>
          <td rowspan="6">LD</td>
          <td rowspan="2">Us-Pwr</td>
          <td >Pad : 75 ~ 115</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005a',75,115)" placeholder="" id="par005a" name="par005a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005b',75,115)" placeholder="" id="par005b" name="par005b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005c',75,115)" placeholder="" id="par005c" name="par005c">
          </td>
        </tr>

        <tr>
          <td >Lead : 40 ~ 80</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006a',40,80)" placeholder="" id="par006a" name="par006a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006b',40,80)" placeholder="" id="par006b" name="par006b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006c',40,80)" placeholder="" id="par006c" name="par006c">
          </td>
        </tr>

        <tr>
          <td rowspan="2">Time</td>
          <td >Pad : 20 ~ 40</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007a',20,40)" placeholder="" id="par007a" name="par007a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007b',20,40)" placeholder="" id="par007b" name="par007b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007c',20,40)" placeholder="" id="par007c" name="par007c">
          </td>
        </tr>

        <tr>
          <td >Lead : 10 ~ 30</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008a',10,30)" placeholder="" id="par008a" name="par008a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008b',10,30)" placeholder="" id="par008b" name="par008b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008c',10,30)" placeholder="" id="par008c" name="par008c">
          </td>
        </tr>

        <tr>
          <td rowspan="2">Force</td>
          <td >Pad : 20 ~ 40</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par009a',20,40)" placeholder="" id="par009a" name="par009a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par009b',20,40)" placeholder="" id="par009b" name="par009b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par009c',20,40)" placeholder="" id="par009c" name="par009c">
          </td>
        </tr>

        <tr>
          <td >Lead : 40 ~ 80</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010a',40,80)" placeholder="" id="par010a" name="par010a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010b',40,80)" placeholder="" id="par010b" name="par010b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010c',40,80)" placeholder="" id="par010c" name="par010c">
          </td>
        </tr>

        <tr>
          <td rowspan="6">Sic Mount</td>
          <td rowspan="2">Us-Pwr</td>
          <td >Pad : 50 ~ 90</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par011a',50,90)" placeholder="" id="par011a" name="par011a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par011b',50,90)" placeholder="" id="par011b" name="par011b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par011c',50,90)" placeholder="" id="par011c" name="par011c">
          </td>
        </tr>

        <tr>
          <td >Lead : 60 ~ 80</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par012a',60,80)" placeholder="" id="par012a" name="par012a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par012b',60,80)" placeholder="" id="par012b" name="par012b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par012c',60,80)" placeholder="" id="par012c" name="par012c">
          </td>
        </tr>

        <tr>
          <td rowspan="2">Time</td>
          <td >Pad : 10 ~ 30</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013a',10,30)" placeholder="" id="par013a" name="par013a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013b',10,30)" placeholder="" id="par013b" name="par013b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013c',10,30)" placeholder="" id="par013c" name="par013c">
          </td>
        </tr>

        <tr>
          <td >Lead : 10 ~ 30</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014a',10,30)" placeholder="" id="par014a" name="par014a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014b',10,30)" placeholder="" id="par014b" name="par014b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014c',10,30)" placeholder="" id="par014c" name="par014c">
          </td>
        </tr>

        <tr>
          <td rowspan="2">Force</td>
          <td >Pad : 50 ~ 70</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015a',50,70)" placeholder="" id="par015a" name="par015a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015b',50,70)" placeholder="" id="par015b" name="par015b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015c',50,70)" placeholder="" id="par015c" name="par015c">
          </td>
        </tr>

        <tr>
          <td >Lead : 40 ~ 80</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par016a',40,80)" placeholder="" id="par016a" name="par016a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par016b',40,80)" placeholder="" id="par016b" name="par016b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par016c',40,80)" placeholder="" id="par016c" name="par016c">
          </td>
        </tr>

        <tr>
          <td >6</td>
          <td >@ Checking of Temperature (PV) <br> (Memeriksa temperature) </td>
          <td colspan="3">Temperature PV <br>: 175 ~ 185 °C </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par017a',175,185)" placeholder="" id="par017a" name="par017a">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par017b',175,185)" placeholder="" id="par017b" name="par017b">
          </td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par017c',175,185)" placeholder="" id="par017c" name="par017c">
          </td>
        </tr>
        
        <tr>
          <td >7</td>
          <td ># Pemeriksaan grounding (Baut pengunci di conductive mat, banana plug, koneksi kabel grounding)</td>
          <td colspan="3">Terpasang bagus /tidak longgar, lepas, putus.</td>
          <td colspan="3">
            <input class="form-control" type="text" id="par018" name="par018" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
          </td>
        </tr>

        <tr>
          <td >8</td>
          <td >Kondisi Permukaan heater plate</td>
          <td colspan="3">Naik dan turunkan frame clamp kemudian periksa apakah ada sisa gold wire, paste, dll atau heater plate penyok.</td>
          <td colspan="3">
            <input class="form-control" type="text" id="par019" name="par019" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
          </td>
        </tr>

        <tr>
          <td >9</td>
          <td >Status frame clamping</td>
          <td colspan="3">Periksa frame pada bagian 1 & 2 bonding tidak mengambang (caranya dengan menekan-nekan frame dengan pinset frame tidak bergerak)</td>
          <td colspan="3">
            <input class="form-control" type="text" id="par020" name="par020" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="5">Note</td>
          <td colspan="3">
            <input class="form-control" type="text" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
          </td>
        </tr>

    </tbody>
    
    </table>
    <div class="note">
        <h3>Catatan</h3>
        <p>
            @: isi kolom dengan nilai (value) di mesin
        </p>
        <p>
            *: Beri tanda V jika OK/Good dan X jika NG
        </p>
        <p>
            #: isi bagian note apabila ada penggantian part    
        </p>
    </div>
</div>