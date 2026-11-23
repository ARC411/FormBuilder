<div class="table-responsive" id="sl33-s-db1b">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("LD Die Bonding 1 Machine (Type : GH07P28F4C2)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th colspan="2" scope="col">Start up Check Item</th>
            <th scope="col" >Standard</th>
            <th scope="col" >Hasil Start Up Check</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td colspan="2">@ Air Pressure 1</td>
            <td >0.05 - 0.25 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.05,0.25)" placeholder="" id="par001" name="par001" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
            </td>
        </tr>

        <tr>
            <td >2</td>
            <td colspan="2">@ Air Pressure 2</td>
            <td >0.30 - 0.50 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.3,0.5)" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
        </tr>
        
        <tr>
            <td >3</td>
            <td colspan="2">@ Sic Sub Mount (SM) Vacum Pressure</td>
            <td >-80.0 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par003',-99999999,-80)" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
        </tr>

        <tr>
            <td >4</td>
            <td colspan="2">@ Middle Collet Vacum Pressure</td>
            <td >-80.0 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par004',-99999999,-80)" placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
            </td>
        </tr>

        <tr>
            <td >5</td>
            <td colspan="2">@ Die Bonding Collet Vasum Pressure</td>
            <td >-80.0 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005',-99999999,-80)" placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2">@ LD collet Load</td>
            <td >CLX 2478 (p = 800 μm)</td>
            <td >0.20 - 0.40 N</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006',0.2,0.4)" placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
            </td>
        </tr>

        <tr>
            <td >CLX 2878 (p = 1080 μm)</td>
            <td >0.05 - 0.15 N</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007',0.05,0.15)" placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
            </td>
        </tr>

        <tr>
            <td >7</td>
            <td colspan="2">@ SM collet Load</td>
            <td >0.60 - 0.80 N</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008',0.6,0.8)" placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
            </td>
        </tr>

        <tr>
            <td >8</td>
            <td colspan="2">@ LD Neeedle Push Up Level</td>
            <td >0.20- 0.60 mm</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par009',0.2,0.6)" placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
            </td>
        </tr>

        <tr>
            <td >9</td>
            <td colspan="2">@ SM Neeedle Push Up Level</td>
            <td >0.20- 0.60 mm</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010',0.2,0.6)" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
            </td>
        </tr>

        <tr>
            <td >10</td>
            <td colspan="2">*Posisi die bonding</td>
            <td >Lihat posisi die bonding pada monitor</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
            </td>
        </tr>

        <tr>
            <td >11</td>
            <td colspan="2">*Bersihkan collet, middle collet dan D/B stage</td>
            <td >Bersihkan menggunakan cotton bud kering</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
            </td>
        </tr>

        <tr>
            <td >12</td>
            <td colspan="2">@ Idle temperature heater</td>
            <td >180 - 220 °C</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013',180,220)" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">13</td>
            <td rowspan="2" colspan="2">@ Setting temperature heater</td>
            <td > Step 1 : (295 - 335) °C</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014',295,335)" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Step 2 : 0 °C</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015',-1,1)" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">14</td>
            <td rowspan="2" colspan="2">@ Rise Time heater</td>
            <td > Step 1 : 0.5 s (UP)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par016',0,1)" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
        </tr>

        <tr>
            <td > Step 2 : 0 s (UP)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par017',-1,1)" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">15</td>
            <td rowspan="2" colspan="2">@ Keep Time heater</td>
            <td > Step 1 : 2.0 s (HT)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par018',1,3)" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
        </tr>

        <tr>
            <td > Step 2 : 0 s (HT)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par019',-1,1)" placeholder="" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
        </tr>

        <tr>
            <td >16</td>
            <td colspan="2">@ Solid Point Heater</td>
            <td >Sama dengan idle temperature + 10 °C</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par020',180,230)" placeholder="" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
        </tr>

        <tr>
            <td >17</td>
            <td colspan="2">@ Rise Point Heater</td>
            <td >Sama dengan Setting Temperature Step 1</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par021',295,335)" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
        </tr>

        <tr>
            <td >18</td>
            <td colspan="2">@ Rising Detect</td>
            <td >2.5 s</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par022',2,3)" placeholder="" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
        </tr>

        <tr>
            <td >19</td>
            <td colspan="2">*Direction lonizer Blower</td>
            <td >Arahkan lonizer ke chip sheet dan check apakah bekerja normal</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
        </tr>

        <tr>
            <td >20</td>
            <td colspan="2">*Checking of the collet guide condition</td>
            <td >Pindahkan collet guide dari posisi atas ke bawah dan cek apakah collet guide goyang atau tidak</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="7">21</td>
            <td colspan="2">@ Parameter setting value :</td>
            <td rowspan="2">50</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par025',45,55)" placeholder="" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">a. No.42 Lower position time of collet BST part</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par026',45,55)" placeholder="" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">b. No.216 Auto start frequency of LD collet BST slow</td>
            <td >300</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par027',280,320)" placeholder="" id="par027" name="par027" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">c. No.217 Maximum frequency of LD collet BST slow</td>
            <td >300</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par028',280,320)" placeholder="" id="par028" name="par028" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">d. No.287 Waiting time of BST collet descent</td>
            <td >10</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par029',8,12)" placeholder="" id="par029" name="par029" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">e. No.292 Waiting time of take out on take out PP BST</td>
            <td >1000</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par030',900,1100)" placeholder="" id="par030" name="par030" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par030'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="2">f. No.293 On timing of heating</td>
            <td >0</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par031',-1,1)" placeholder="" id="par031" name="par031" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par031'];} ?>>
            </td>
        </tr>

        <tr>
            <td >22</td>
            <td colspan="2">Nitrogen flow</td>
            <td >5 - 6 l /min</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par032',5,6)" placeholder="" id="par032" name="par032" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par032'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par033" name="par033" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par033'];} ?>>
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