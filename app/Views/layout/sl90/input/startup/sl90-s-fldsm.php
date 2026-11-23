<div class="table-responsive" id="slcs">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="4">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>

          <table class="table table-striped-columns">
            <thead align="center">
              <tr>
                <th class="input-form" colspan="6" scope="col">@Die Bonding Strength (2x Bonding Stage number (Back stage & Front stage) / Lot)</th>
                <th class="input-form" colspan="2" >@Wettability area > 90%</th>
                <th class="input-form" rowspan="3" >Remark</th>
              </tr>
              <tr>
                <th class="input-form"  rowspan="2">Sample</th>
                <th class="input-form"  colspan="3" scope="col">LD Chip & SM</th>
                <th class="input-form"  colspan="2" scope="col">Submount & Stem</th>
                <th class="input-form"  rowspan="2" scope="col">LD Chip & SM</th>
                <th class="input-form"  rowspan="2" scope="col">Submount & Stem</th>
               </tr>
                <tr>
                    <th class="input-form" scope="col">Result</th>
                    <th class="input-form" scope="col">Remain % LD</th>
                    <th class="input-form" scope="col">Judge (OK/NG)</th>
                    <th class="input-form" scope="col">Result</th>
                    <th class="input-form" scope="col">Judge (OK/NG)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <th scope="row">Front 1</th>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par017a" name="par017a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
                    </td>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par018a" name="par018a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
                    </td>
                    <td>
                        <select id="par035a" name="par035a" class="form-select">
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
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par019a" name="par019a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
                    </td>
                    <td>
                        <select id="par036a" name="par036a" class="form-select">
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
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par020a" name="par020a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
                    </td>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par021a" name="par021a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
                    </td>
                    
                    <td >
                        <input type="text" class="form-control" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
                    </td>
                </tr>


                <tr>
                    <th scope="row">Front 2</th>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par017b" name="par017b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par017'];} ?>>
                    </td>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par018b" name="par018b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par018'];} ?>>
                    </td>
                    <td>
                        <select id="par035b" name="par035b" class="form-select">
                            <?php if(isset($alldata)){
                                if($alldata[1]["par035"]=="-"){
                                echo '
                                    <option value="-" selected>-</option>
                                    <option value="OK">OK</option>
                                    <option value="NG">NG</option>
                                ';
                                }
                                else if($alldata[1]["par035"]=="OK")
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
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par019b" name="par019b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par019'];} ?>>
                    </td>
                    <td>
                        <select id="par036b" name="par036b" class="form-select">
                            <?php if(isset($alldata)){
                                if($alldata[1]["par036"]=="-"){
                                echo '
                                    <option value="-" selected>-</option>
                                    <option value="OK">OK</option>
                                    <option value="NG">NG</option>
                                ';
                                }
                                else if($alldata[1]["par036"]=="OK")
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
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par020b" name="par020b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par020'];} ?>>
                    </td>
                    <td>
                        <input type="number" step="0.00000001" class="form-control" id="par021b" name="par021b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par021'];} ?>>
                    </td>
                </tr>

                <tr>
                <th scope="row">Back 1</th>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par017c" name="par017c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par017'];} ?>>
                </td>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par018c" name="par018c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par018'];} ?>>
                </td>
                <td>
                    <select id="par035c" name="par035c" class="form-select">
                        <?php if(isset($alldata)){
                            if($alldata[2]["par035"]=="-"){
                            echo '
                                <option value="-" selected>-</option>
                                <option value="OK">OK</option>
                                <option value="NG">NG</option>
                            ';
                            }
                            else if($alldata[2]["par035"]=="OK")
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
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par019c" name="par019c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par019'];} ?>>
                </td>
                <td>
                    <select id="par036c" name="par036c" class="form-select">
                        <?php if(isset($alldata)){
                            if($alldata[2]["par036"]=="-"){
                            echo '
                                <option value="-" selected>-</option>
                                <option value="OK">OK</option>
                                <option value="NG">NG</option>
                            ';
                            }
                            else if($alldata[2]["par036"]=="OK")
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
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par020c" name="par020c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par020'];} ?>>
                </td>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par021c" name="par021c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par021'];} ?>>
                </td>
                
                
                
                </tr>
                <tr>
                <th scope="row">Back 2</th>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par017d" name="par017d" <?php if(isset($alldata)){echo 'value='.$alldata[3]['par017'];} ?>>
                </td>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par018d" name="par018d" <?php if(isset($alldata)){echo 'value='.$alldata[3]['par018'];} ?>>
                </td>
                <td>
                    <select id="par035d" name="par035d" class="form-select">
                        <?php if(isset($alldata)){
                            if($alldata[3]["par035"]=="-"){
                            echo '
                                <option value="-" selected>-</option>
                                <option value="OK">OK</option>
                                <option value="NG">NG</option>
                            ';
                            }
                            else if($alldata[3]["par035"]=="OK")
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
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par019d" name="par019d" <?php if(isset($alldata)){echo 'value='.$alldata[3]['par019'];} ?>>
                </td>
                <td>
                    <select id="par036d" name="par036d" class="form-select">
                        <?php if(isset($alldata)){
                            if($alldata[3]["par036"]=="-"){
                            echo '
                                <option value="-" selected>-</option>
                                <option value="OK">OK</option>
                                <option value="NG">NG</option>
                            ';
                            }
                            else if($alldata[3]["par036"]=="OK")
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
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par020d" name="par020d" <?php if(isset($alldata)){echo 'value='.$alldata[3]['par020'];} ?>>
                </td>
                <td>
                    <input type="number" step="0.00000001" class="form-control" id="par021d" name="par021d" <?php if(isset($alldata)){echo 'value='.$alldata[3]['par021'];} ?>>
                </td>
                
                </tr>
            </tbody>
         </table>

         
        </div>