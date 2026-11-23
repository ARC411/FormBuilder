<div class="table-responsive" id="slht1">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="10" scope="col"><?php echo nl2br("HOT TEST MACHINE START UP (LD1220TE Type)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th colspan="2" scope="col">Start up Check Item</th>
            <th colspan="3" scope="col" >Standard</th>
            <th scope="col" >Data</th>
        </tr>
    </thead>

    <tbody>
        <tr>
          <td >1</td>
          <td colspan="2">@Air Pressure</td>
          <td colspan="3">0.4 ~ 0.6 MPa</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.4,0.6)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="9">2</td>
          <td rowspan="9" colspan="2">Standard Sample Measurement <br> (Pengukuran Standard Sample)</td>
          <td rowspan="9" colspan="2">Ukur standard sample pada setiap holder</td>
        </tr>

        <tr>
          <td >1</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          </td>
        </tr>
        <tr>
          <td >2</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>
        <tr>
          <td >3</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
          </td>
        </tr>
        <tr>
          <td >4</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
          </td>
        </tr>
        <tr>
          <td >5</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
          </td>
        </tr>
        <tr>
          <td >6</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
          </td>
        </tr>
        <tr>
          <td >7</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
          </td>
        </tr>
        <tr>
          <td >8</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
          </td>
        </tr>

        <tr>
          <td >3</td>
          <td colspan="2">*Check of Contact pin<br>(Memeriksa Kontak Pin)</td>
          <td colspan="3">Check kondisi probe pin holder apakah terjadi pembengkokan, patah.</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">4</td>
          <td rowspan="2" colspan="2">*Check of Classification<br>*(Memeriksa Klasifikasi)</td>
          <td colspan="3">Good Product</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
          </td>
        </tr>

        <tr>
            <td colspan="3">NG Product</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
          </td>
        </tr>

        <tr>
          <td >5</td>
          <td colspan="2">Cleaning of Machine<br>(Membersihkan Mesin)</td>
          <td colspan="3">Bersihkan mesin dari debu atau kotoran dengan Lap dan dari device yang terjatuh.</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="9">6</td>
          <td rowspan="9" colspan="2">@Cek temperature holder <br> (Memeriksa temperatur holder)</td>
        </tr>

        <tr>
          <td colspan="3">1</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">2</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">3</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">4</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">5</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">6</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">7</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="3">8</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">7</td>
          <td rowspan="2" colspan="2">* Check & Wear safety goggles <br> (Memeriksa & memakai kacamata pengaman)</td>
          <td rowspan="2" colspan="3">Check kondisi kacamata pengaman <br> Ketika mengukur model High Power, harus menggunakan kacamata pengaman</td>
          <td rowspan="2">
            <input class="form-control" type="text" placeholder="" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
          </td>
        </tr>

        <tr>
        <tr>
          <td >8</td>
          <td colspan="2">* Check Over Ride Setting, Cover Panel & Door Lock <br> (Memeriksa setting over ride, panel penutup & kunci pintu)</td>
          <td colspan="3">Confirm setting Over Ride "OFF" <br>* Confirm semua bagian panel penutup mesin <br> * Confirm kunci pintu saat auto running</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
          </td>
        </tr>
        </tr>

        <tr>
          <td rowspan="2">9</td>
          <td rowspan="2" colspan="2">* Check movement of sensor at holder unit of supply position <br> (Memeriksa pergerakan sensor pada unit holder)</td>
          <td rowspan="2" colspan="3">Check pergerakan sensor pada holder stage open/close <br> Ketika pressing-down device tertutup dan output sensor terbuka</td>
          <td rowspan="2">
            <input class="form-control" type="text" placeholder="" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
          </td>
        </tr>

        <tr>
        <tr>
          <td >10</td>
          <td colspan="2">* Check Hot blower temperature</td>
          <td colspan="3">Pastikan bahwa temperaature (A sensor) dari hot blower berada dibawah ± 1 °C dari setting temperature</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
          </td>
        </tr>
        </tr>

        <tr>
          <td colspan="6">Note</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
          </td>
        </tr>

        
        

    </tbody>
    
    </table>
</div>