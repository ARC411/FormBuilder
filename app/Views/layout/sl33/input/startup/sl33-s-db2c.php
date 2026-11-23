<div class="table-responsive" id="sl33-s-db2c">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("LD Die Bonding 2 for High Speed Auto Machine")?></th>
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
            <td rowspan="2">1</td>
            <td rowspan="2">@ Air Pressure</td>
            <td >Transfer</td>
            <td >0.4 ~ 0.49 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.4,0.49)" placeholder="" id="par001" name="par001" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
            </td>
        </tr>

        <tr>
            <td >CTBU</td>
            <td >0.08 ~ 0.12 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.08,0.12)" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">2</td>
            <td rowspan="2">@ Vacuum level</td>
            <td >Stem transfer</td>
            <td >-80 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par003',-89999999999,-80)" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
        </tr>
        
        <tr>
            <td >CTBU</td>
            <td >-90 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par004',-89999999999,-90)" placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="3">3</td>
            <td rowspan="3">@ Collet Vacum Level</td>
            <td >A collet</td>
            <td >-80 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005',-89999999999,-80)" placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
            </td>
        </tr>

        <tr>
            <td >B collet</td>
            <td >-80 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006',-89999999999,-80)" placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Middle stage</td>
            <td >-80 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007',-89999999999,-80)" placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="3">4</td>
            <td colspan="2" rowspan="3">* Collet Cleaning</td>
            <td >Collet A</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Collet B</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Middle Collet</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
            </td>
        </tr>

        <tr>
            <td >5</td>
            <td colspan="2">* Cek kondisi collet guide</td>
            <td >Cek kondisi collet guide goyang atau tidak</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2" colspan="2">@ Colled Load</td>
            <td >Collet A : 0.49 ~ 0.88 N (49~88 gr)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par012',0.49,0.88,49,88)" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Collet B : 0.69 ~ 1.47 N (69~147 gr)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par013',0.69,1.47,69,147)" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
        </tr>

        <tr>
            <td >7</td>
            <td colspan="2">@ Needle Push Up Level</td>
            <td >0.20 ~ 0.60 mm 0.40 ~ 0.80 mm</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par014',0.2,0.6,0.4,0.8)" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
        </tr>

        <tr>
            <td >8</td>
            <td colspan="2">* Cek posisi die bonding pada monitor</td>
            <td >Pergeseran LD maksimal 2 mm dari cursor</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="6">9</td>
            <td rowspan="6">Ag Paste Dispenser</td>
            <td rowspan="3">Digital</td>
            <td >@ Air Pressure : 0.005 ~ 0.1 MPa (5 ~ 100 kPa)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par016',0.0.005,0.1,5,100)" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
        </tr>

        <tr>
            <td >@ Dispense Time : 5 ~ 100 ms (0.005 ~ 0.100 s)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par017',5,100,0.005,0.1)" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
        </tr>

        <tr>
            <td >@ Suction force : -0.4 ~ 0 kPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par018',-4,0)" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="3">Analog</td>
            <td >@ Air Pressure : 0.01 ~ 0.10 MPa (10 ~ 100 kPa)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par019',0.01,0.1,10,100)" placeholder="" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
        </tr>

        <tr>
            <td >@ Dispense Time : 10 ~ 100 ms (0.010 ~ 0.100 s)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par020',10,100,0.01,0.1)" placeholder="" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
        </tr>

        <tr>
            <td >* Suction force : kurang dari 1/6 dari posisi closed <br> (putaran berlawanan arah jarum jam)</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">Ag Paste Condition</td>
            <td >* Posisi</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
        </tr>

        <tr>
            <td >@ Quantity</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">11</td>
            <td rowspan="2" colspan="2">* Direction Ion Blower Fan</td>
            <td >Posisi blower</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Putaran blower</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
        </tr>

        <tr>
            <td >12</td>
            <td colspan="2">* Cek Stem Nozzle Condition</td>
            <td >Tidak ada gompal atau crack pada stem nozzle</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="3">13</td>
            <td rowspan="3">@ Bonding Offset Parameter</td>
            <td >X</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par027',-20,20,65516,65556)" placeholder="" id="par027" name="par027" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Y</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par028',-20,20,65516,65556)" placeholder="" id="par028" name="par028" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
            </td>
        </tr>

        <tr>
            <td >θ</td>
            <td >-20 ~ 20 (65516 ~ 65556)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange2('par029',-20,20,65516,65556)" placeholder="" id="par029" name="par029" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
            </td>
        </tr>

        <tr>
            <td >14</td>
            <td colspan="2">* Cleaning Machine</td>
            <td >Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par030" name="par030" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par030'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <td >
                <input class="form-control" type="text" placeholder="" id="par031" name="par031" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par031'];} ?>>
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