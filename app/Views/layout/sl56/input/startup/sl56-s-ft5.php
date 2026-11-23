<div class="table-responsive" id="slft1">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("FINAL TEST")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th scope="col">Start up Check Item</th>
            <th colspan="3" scope="col" >Standard</th>
            <th colspan="2" scope="col" >Data</th>
        </tr>
    </thead>

    <tbody>
        <tr>
          <td >1</td>
          <td >@Air Pressure</td>
          <td colspan="3">0.4-0.6 MPa</td>
          <td colspan="2">
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.4,0.6)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="9">2</td>
          <td rowspan="9">Standard Sample Measurement <br> (Pengukuran Standard Sample)</td>
          <td rowspan="9">Ukur standard sample pada setiap holder</td>
          <td >In side</td>
          <td >Out side</td>
          <td >In side</td>
          <td >Out side</td>
        </tr>

        <tr>
          <td >1</td>
          <td >1</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>
        <tr>
          <td >2</td>
          <td >2</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
          </td>
        </tr>
        <tr>
          <td >3</td>
          <td >3</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
          </td>
        </tr>
        <tr>
          <td >4</td>
          <td >4</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
          </td>
        </tr>
        <tr>
          <td >5</td>
          <td >5</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
          </td>
        </tr>
        <tr>
          <td >6</td>
          <td >6</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
          </td>
        </tr>
        <tr>
          <td >7</td>
          <td >7</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
          </td>
        </tr>
        <tr>
          <td >8</td>
          <td >8</td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
          </td>
          <td >
            <input class="form-control" type="text" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
          </td>
        </tr>

        <tr>
          <td >3</td>
          <td >*Check of Contact pin<br>(Memeriksa Kontak Pin)</td>
          <td colspan="3">Check kondisi probe pin holder apakah terjadi pembengkokan, patah</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="3">4</td>
          <td rowspan="3">*Check of Classification<br>*(Memeriksa Klasifikasi)</td>
          <td colspan="3">Good Product</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="3">LD Open</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="3">Ld short</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="5">5</td>
          <td rowspan="5">@Check of Gain light detector<br>(Memeriksa Gain Light detector)</td>
          <td colspan="1">FFP-V</td>
          <td colspan="2">5000-28000</td>
          <td colspan="2">
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par022',5000,28000)" placeholder="" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
          </td>
        </tr>

        <tr>
            <td colspan="1">FFP-H</td>
            <td colspan="2">5000-28000</td>
            <td colspan="2">
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par023',5000,28000)" placeholder="" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="1">FFP-V</td>
            <td colspan="2">5000-28000</td>
            <td colspan="2">
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par024',5000,28000)" placeholder="" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="1">FFP-H</td>
            <td colspan="2">5000-28000</td>
            <td colspan="2">
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par025',5000,28000)" placeholder="" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
        </tr>

        <tr>
            <td colspan="1">POL</td>
            <td colspan="2">5000-28000</td>
            <td colspan="2">
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par026',5000,28000)" placeholder="" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
        </tr>

        <tr>
          <td >6</td>
          <td >Cleaning of Machine<br>(Membersihkan Mesin)</td>
          <td colspan="3">Bersihkan mesin dari debu atau kotoran dengan Lap dan dari device yang terjatuh</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par027" name="par027" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="5">Note</td>
          <td colspan="2">
            <input class="form-control" type="text" placeholder="" id="par028" name="par028" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
          </td>
        </tr>

    </tbody>
    
    </table>
</div>