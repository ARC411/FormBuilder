<div class="table-responsive" id="sldb1">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
<input type="hidden" id="par016" name="par016" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par016'].'"';} ?>>
          <table class="table table-striped-columns">
            <thead align="center">
              <tr>
                <th colspan="20" scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
              </tr>
            </thead>
            <thead align="center">
            <tr>
                <th class="input-form" colspan="3" scope="col">Quantity</th>
                <th class="input-form" colspan="4" scope="col">Foregoing (5 pcs/head/lot)</th>
                <th class="input-form" colspan="4" scope="col">Vis Insp (10%/lot)</th>
                <th class="input-form" colspan="8" rowspan="2" scope="col">Die Bonding Strength (1 pcs/head/shift) <br> Standard >= 500g (4.9 N) <br> Standart Model GH04Series & GH05Series* <br> GH0523AD2G >= 6.0 N</th>
                <th class="input-form" rowspan="3">Judge</th>
            </tr>
            <tr>
                <th class="input-form" rowspan="2">Input</th>
                <th class="input-form" rowspan="2">Output</th>
                <th class="input-form" rowspan="2">NG</th>
                <th class="input-form" colspan="2" scope="col">MSP Chip</th>
                <th class="input-form" colspan="2" scope="col">LD Chip</th>
                <th class="input-form" colspan="2" scope="col">MSP Chip</th>
                <th class="input-form" colspan="2" scope="col">LD Chip</th>
            </tr>
            <tr>
                <th class="input-form" scope="col">Crack</th>
                <th class="input-form" scope="col">Chipping</th>
                <th class="input-form" scope="col">LD Pos</th>
                <th class="input-form" scope="col">Miss</th>
                <th class="input-form" scope="col">Crack</th>
                <th class="input-form" scope="col">Chipping</th>
                <th class="input-form" scope="col">LD Pos</th>
                <th class="input-form" scope="col">Miss</th>
                <th class="input-form" scope="col">1</th>
                <th class="input-form" scope="col">2</th>
                <th class="input-form" scope="col">3</th>
                <th class="input-form" scope="col">4</th>
                <th class="input-form" scope="col">5</th>
                <th class="input-form" scope="col">6</th>
                <th class="input-form" scope="col">7</th>
                <th class="input-form" scope="col">8</th>
            </tr>
        </thead>
            <tbody>
              <tr>
                <td width="120px">
                  <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange1()" onchange="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
                </td>
                <td width="120px">
                  <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange1()" onchange="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
                </td>
                <td width="120px">
                  <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?> readonly>
                </td>
                <td style="min-width: 90px;">
                    <select id="par035" name="par035" class="form-select">
                      <?php if(isset($alldata)){
                        if($alldata[0]["par035"]=="-"){
                          echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                          ';
                        }
                        else if($alldata[0]["par035"]=="OK")
                        {
                          echo '
                            <option value="-">-</option>
                            <option value="OK" selected>OK</option>
                            <option value="NG">NG</option>
                          ';
                        }
                        else{
                          echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG" selected>NG</option>
                          ';
                        }
                      }
                      else{
                        echo '<option value="-">-</option>
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>';
                      } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par036" name="par036" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par036"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par036"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par037" name="par037" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par037"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par037"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par038" name="par038" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par038"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par038"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par039" name="par039" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par039"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par039"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par040" name="par040" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par040"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par040"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par041" name="par041" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par041"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par041"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td style="min-width: 90px;">
                    <select id="par042" name="par042" class="form-select">
                      <?php if(isset($alldata)){
                          if($alldata[0]["par042"]=="-"){
                            echo '
                              <option value="-" selected>-</option>
                              <option value="OK">OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else if($alldata[0]["par042"]=="OK")
                          {
                            echo '
                              <option value="-">-</option>
                              <option value="OK" selected>OK</option>
                              <option value="NG">NG</option>
                            ';
                          }
                          else{
                            echo '
                              <option value="-">-</option>
                              <option value="OK">OK</option>
                              <option value="NG" selected>NG</option>
                            ';
                          }
                        }
                        else{
                          echo '<option value="-">-</option>
                          <option value="OK">OK</option>
                          <option value="NG">NG</option>';
                        } ?>
                     </select>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
                </td>
                <td width="100px">
                  <input type="number" step="0.00000001" class="form-control" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
                </td>
                <td style="min-width: 90px;">
                    <select class="form-select" id="par043" name="par043" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par043'];} ?>>
                    <?php if(isset($alldata)){
                        if($alldata[0]["par043"]=="-"){
                          echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                          ';
                        }
                        else if($alldata[0]["par043"]=="OK")
                        {
                          echo '
                            <option value="-">-</option>
                            <option value="OK" selected>OK</option>
                            <option value="NG">NG</option>
                          ';
                        }
                        else{
                          echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG" selected>NG</option>
                          ';
                        }
                      }
                      else{
                        echo '<option value="-">-</option>
                        <option value="OK">OK</option>
                        <option value="NG">NG</option>';
                      } ?>
                     </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>