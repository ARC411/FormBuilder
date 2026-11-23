<div class="table-responsive" id="slft3">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("FINAL TEST START UP (LD1360AHP Type)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th scope="col">Start up Check Item</th>
            <th colspan="2" scope="col" >Standard</th>
            <th scope="col" >Hasil Start Up</th>
        </tr>
    </thead>

    <tbody>
        <tr>
          <td >1</td>
          <td >@Air Pressure</td>
          <td colspan="2">0.4-0.6 MPa</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.4,0.6)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="9">2</td>
          <td rowspan="9">Standard Sample Measurement <br> (Pengukuran Standard Sample)</td>
          <td rowspan="9">Ukur standard sample pada setiap holder</td>
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
          <td >*Check of Contact pin<br>(Memeriksa Kontak Pin)</td>
          <td colspan="2">Check kondisi probe pin holder apakah terjadi pembengkokan, patah.</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">4</td>
          <td rowspan="2">*Check of Classification<br>*(Memeriksa Klasifikasi)</td>
          <td colspan="2">Good Product</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="2">NG Product</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
          </td>
        </tr>

        <tr>
          <td >5</td>
          <td >Cleaning of Machine<br>(Membersihkan Mesin)</td>
          <td colspan="2">Bersihkan mesin dari debu atau kotoran dengan Lap dan dari device yang terjatuh</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
          </td>
        </tr>

        <tr>
          <td >6</td>
          <td >Cek temperature <br> (terlihat pada digital thermometer)</td>
          <td colspan="2">24° ~ 30°C</td>
          <td >
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014',24,30)" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
          </td>
        </tr>

        <tr>
          <td >7</td>
          <td >* Check & Wear safety goggles <br> * (Memeriksa & memakai kacamatan pengaman)</td>
          <td colspan="2">Check kondisi kacamata pengaman <br> Ketika mengukur model High Power, harus menggunakan kacamata pengaman</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
          </td>
        </tr>

        <tr>
          <td >8</td>
          <td >* Check Over Ride Setting, Cover Panel & Door Lock <br> * (Memeriksa setting over ride, panel penutup & kunci pintu)</td>
          <td colspan="2">Check kondisi kacamata pengaman <br> Ketika mengukur model High Power, harus menggunakan kacamata pengaman</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
          </td>
        </tr>

        <tr>
          <td >9</td>
          <td >* Check movement of sensor at holder unit of supply position <br> * (Memeriksa pergerakan sensor pada unit holder)</td>
          <td colspan="2">Check pergerakan sensor pada holder stage open/close <br> Ketika pressing-down device tertutup dan output sensor terbuka</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="4">Note</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
          </td>
        </tr>

    </tbody>
    
    </table>
</div>