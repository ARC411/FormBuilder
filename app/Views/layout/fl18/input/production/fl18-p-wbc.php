<div class="table-responsive" id="fl18-p-wbc">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(!empty($alldata)){echo $number;} ?>>
      <table class="table table-striped-columns" 
        >
        <thead align="center">
          <tr>
            <th colspan="20" scope="col"><?php echo nl2br("CAPILLARY CHANGE RECORD SHEET")?></th>
          </tr>
        </thead>
        <thead align="center">
          <tr>
            <th class="input-form" rowspan="3">Equipment No</th>
            <th class="input-form" rowspan="3">Product</th>
            <th class="input-form" rowspan="3">Process</th>
            <th class="input-form" colspan="3" scope="col" >CAPILLARY USAGE QTY</th>
            <th class="input-form" rowspan="3">(3) Setting <br> Capillary <br> Limit <br> at <br> Machine</th>
            <th class="input-form" rowspan="3">(4) Setting <br> Value <br> of Torque <br> Screw <br> Driver</th>
            <th class="input-form" colspan="8">(5) TOOL HEIGHT RENEW</th>
            <th class="input-form" rowspan="3">(6) Centering <br> Gold Ball <br> (CTD) <br> RENEW</th>
            <th class="input-form" rowspan="3">US <br> Calibration</th>
            <th class="input-form" rowspan="3">PIC</th>
            <th class="input-form" rowspan="3">REMARK</th>
          </tr>
        
          <tr>            
            <th class="input-form" colspan="2" rowspan="2">(1) Usage Qty</th>
            <th class="input-form" rowspan="2">(2) Change <br> Yes/No</th>
            <th class="input-form" colspan="2">LD</th>
            <th class="input-form" colspan="2">MS1</th>
            <th class="input-form" colspan="2">MS1</th>
            <th class="input-form" colspan="2">PD</th>
          </tr>

          <tr>
            <th class="input-form">1st</th>
            <th class="input-form">2nd</th>
            <th class="input-form">1st</th>
            <th class="input-form">2nd</th>
            <th class="input-form">1st</th>
            <th class="input-form">2nd</th>
            <th class="input-form">1st</th>
            <th class="input-form">2nd</th>
          </tr>
        </thead>
        <tbody>
            <td>
              <input type="text" class="form-control" id="par002" name="par002" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par002'];} ?> >
            </td>
            <td>
              <input type="text" class="form-control" id="par003" name="par003" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par003'];} ?> >
            </td>
            <td>
              <input type="text" class="form-control" id="par004" name="par004" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par004'];} ?> >
            </td>
            <td class="input-form">
              <input type="number" step="0.01" class="form-control" id="par017" name="par017" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
            <td class="input-form">
                <select class="form-select" id="par035" name="par035">
                <?php if(!empty($alldata)){
                    if($alldata[0]["par035"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="Wires">Wires</option>
                        <option value="Point">Point</option>
                        ';
                    }
                    else if($alldata[0]["par035"]=="V"){
                        echo '
                        <option value="-">-</option>
                        <option value="Wires" selected>Wires</option>
                        <option value="Point">Point</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="Wires">Wires</option>
                        <option value="Point" selected>Point</option>
                        ';
                    }
                    }
                    else{
                    echo '
                        <option value="-">-</option>
                        <option value="Wires">Wires</option>
                        <option value="Point">Point</option>
                    ';
                } ?>
                </select>
            </td>
            <td >
                <select class="form-select" id="par036" name="par036">
                <?php if(!empty($alldata)){
                    if($alldata[0]["par036"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                        ';
                    }
                    else if($alldata[0]["par036"]=="V"){
                        echo '
                        <option value="-">-</option>
                        <option value="Yes" selected>Yes</option>
                        <option value="No">No</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="Yes">Yes</option>
                        <option value="No" selected>No</option>
                        ';
                    }
                    }
                    else{
                    echo '
                        <option value="-">-</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    ';
                } ?>
                </select>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par018" name="par018" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
            <td >
                <select class="form-select" id="par037" name="par037">
                <?php if(!empty($alldata)){
                    if($alldata[0]["par037"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else if($alldata[0]["par037"]=="V"){
                        echo '
                        <option value="-">-</option>
                        <option value="V" selected>V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X" selected>X</option>
                        ';
                    }
                    }
                    else{
                    echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                    ';
                } ?>
                </select>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par019" name="par019" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par020" name="par020" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par021" name="par021" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par022" name="par022" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par023" name="par023" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par024" name="par024" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par025" name="par025" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
            <td>
              <input type="number" step="0.01" class="form-control" id="par026" name="par026" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
            <td >
                <select class="form-select" id="par038" name="par038">
                <?php if(!empty($alldata)){
                    if($alldata[0]["par038"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else if($alldata[0]["par038"]=="V"){
                        echo '
                        <option value="-">-</option>
                        <option value="V" selected>V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X" selected>X</option>
                        ';
                    }
                    }
                    else{
                    echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                    ';
                } ?>
                </select>
            </td>
            <td >
                <select class="form-select" id="par039" name="par039">
                <?php if(!empty($alldata)){
                    if($alldata[0]["par039"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else if($alldata[0]["par039"]=="V"){
                        echo '
                        <option value="-">-</option>
                        <option value="V" selected>V</option>
                        <option value="X">X</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X" selected>X</option>
                        ';
                    }
                    }
                    else{
                    echo '
                        <option value="-">-</option>
                        <option value="V">V</option>
                        <option value="X">X</option>
                    ';
                } ?>
                </select>
            </td>
            <td>
              <input type="text" class="form-control" id="par005" name="par005" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par005'];} ?> >
            </td>
            <td>
              <input type="text" class="form-control" id="par006" name="par006" <?php if(!empty($alldata)){echo 'value='.$alldata[0]['par006'];} ?> >
            </td>
        </tbody>
    </table>
    <div class="note">
        <h3>Ket :</h3>
        <p>
            (1) Periksa dan tulis jumlah pemakaian capillary (tool) setiap akhir lot dan bila ada pergantian capillary, pilih unit dengan memilih WIRES atau POINT
        </p>
        <p>
            (2) Pilih YES atau NO, YES jika ada pergantian capillary, NO jika tidak ada pergantian capillary
        </p>
        <p>
            (3) Tulis setting capillary limit yang digunakan pada mesin
        </p>
        <p>
            (4) Diisi bila ada pergantian capillary (pilihan kolom (2) Change adl.YES). Beri tanda (V) jika sesuai standard dan (X) jika tidak sesuai standar
        </p>
        <p>
            (5) Lakukan Tool Height pada pad dan lead sesuai dengan spesifikasi device masing-masing
        </p>
        <p>
            (6) Lakukan Centering Gold Ball (CTD). Beri tanda (V) jika sudah dilakukan dan (X) jika tidak dilakukan
        </p>
        <p>
            (7) Lakukan US Kalibrasi, Pilih YES atau NO, YES melakukan kalibrasi, NO tidak melakukan kalibrasi
        </p>
    </div>

    <table class="table table-striped-colums">
    <thead align="center">
          <tr>
            <th colspan="3" scope="col"><?php echo nl2br("Standar Setting Nilai Torque Screwdriver")?></th>
          </tr>
    </thead>
        <thead align="center">
            <tr>
                <th scope="col">Category Product</th>
                <th scope="col">Nama Mesin</th>
                <th scope="col">Nilai</th>
            </tr>
            <tr>
                <td>SL 3.3, SL 5.6</td>
                <td>Shibaura</td>
                <td rowspan="2">14.7 cNm</td>
            </tr>
            <tr>
                <td>FL 1.8t</td>
                <td>Shinkawa</td>
            </tr>
            <tr>
                <td>SL 5.6t</td>
                <td>ASM</td>
                <td>19.6 cNm</td>
            </tr>
            <tr>
                <td>SL 5.6t</td>
                <td>Kaijo</td>
                <td>18 cNm</td>
            </tr>
            <tr>
                <td>FL 1.8t</td>
                <td>ASM</td>
                <td>20 cNm</td>
            </tr>
        </thead>
    </table>
</div>