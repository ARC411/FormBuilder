<!-- FLDB2 -->
<div class="table-responsive">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="3">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns">
        <thead align="center">
            <tr>
                <th colspan="13" scope="col"><?php echo nl2br("DIE BONDING 2 \n PAGE 1")?></th>
            </tr>
            <tr>
                <th class="input-form" rowspan="3" scope="col">Pos</th>
                <th class="input-form" colspan="5" rowspan="2" scope="col">Foregoing Check Sample 1</th>
                <th class="input-form" rowspan="2" scope="col">Judge1</th>
                <th class="input-form" colspan="6" scope="col">Change Bonding Offset Parameter #1</th>
            </tr>
            <tr>
                <th class="input-form" colspan="2" scope="col">X</th>
                <th class="input-form" colspan="2" scope="col">Z</th>
                <th class="input-form" colspan="2" scope="col">q</th>
            </tr>
            <tr>
                <th class="input-form" scope="col">1</th>
                <th class="input-form" scope="col">2</th>
                <th class="input-form" scope="col">3</th>
                <th class="input-form" scope="col">4</th>
                <th class="input-form" scope="col">5</th>
                <th class="input-form" scope="col">OK/NG</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
            </tr>
        </thead>

        <tbody>
            <tr>
            <th scope="row">X</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par013a" name="par013a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par014a" name="par014a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par015a" name="par015a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par016a" name="par016a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par017a" name="par017a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par035a" name="par035a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par035"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[0]["par035"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par018a" name="par018a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par019a" name="par019a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par020a" name="par020a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par021a" name="par021a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par022a" name="par022a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par023a" name="par023a"<?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
            </tr>
            <tr>
            <th scope="row">Z</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par013b" name="par013b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par013'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par014b" name="par014b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par014'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par015b" name="par015b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par015'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par016b" name="par016b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par016'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par017b" name="par017b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par017'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par035b" name="par035b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par035"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[1]["par035"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par018b" name="par018b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par018'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par019b" name="par019b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par019'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par020b" name="par020b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par020'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par021b" name="par021b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par021'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par022b" name="par022b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par022'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par023b" name="par023b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par023'];} ?>>
            </td>
            </tr>
            <tr>
            <th scope="row">&theta;</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par013c" name="par013c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par013'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par014c" name="par014c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par014'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par015c" name="par015c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par015'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par016c" name="par016c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par016'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par017c" name="par017c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par017'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par035c" name="par035c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par035"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[2]["par035"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par018c" name="par018c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par018'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par019c" name="par019c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par019'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par020c" name="par020c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par020'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par021c" name="par021c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par021'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par022c" name="par022c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par022'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par023c" name="par023c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par023'];} ?>>
            </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-striped-columns">
        <thead align="center">
            <tr>
                <th colspan="13" scope="col"><?php echo nl2br("DIE BONDING 2 \n PAGE 2")?></th>
            </tr>
            <tr>
                <th class="input-form" rowspan="3" scope="col">Pos</th>
                <th class="input-form" colspan="5" rowspan="2" scope="col">Foregoing Check Sample 2</th>
                <th class="input-form" rowspan="2" scope="col">Judge2</th>
                <th class="input-form" colspan="6" scope="col">Change Bonding Offset Parameter #2</th>
            </tr>
            <tr>
                <th class="input-form" colspan="2" scope="col">X</th>
                <th class="input-form" colspan="2" scope="col">Z</th>
                <th class="input-form" colspan="2" scope="col">q</th>
            </tr>
            <tr>
                <th class="input-form" scope="col">1</th>
                <th class="input-form" scope="col">2</th>
                <th class="input-form" scope="col">3</th>
                <th class="input-form" scope="col">4</th>
                <th class="input-form" scope="col">5</th>
                <th class="input-form" scope="col">OK/NG</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
                <th class="input-form" scope="col">From</th>
                <th class="input-form" scope="col">to</th>
            </tr>
        </thead>

        <tbody>
            <tr>
            <th scope="row">X</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par024a" name="par024a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par025a" name="par025a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par026a" name="par026a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par027a" name="par027a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par028a" name="par028a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par036a" name="par036a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par036"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[0]["par036"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par029a" name="par029a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par030a" name="par030a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par030'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par031a" name="par031a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par031'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par032a" name="par032a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par032'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par033a" name="par033a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par033'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par034a" name="par034a" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par034'];} ?>>
            </td>
            </tr>
            <tr>
            <th scope="row">Z</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par024b" name="par024b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par024'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par025b" name="par025b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par025'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par026b" name="par026b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par026'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par027b" name="par027b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par027'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par028b" name="par028b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par028'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par036b" name="par036b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par036"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[1]["par036"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par029b" name="par029b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par029'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par030b" name="par030b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par030'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par031b" name="par031b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par031'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par032b" name="par032b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par032'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par033b" name="par033b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par033'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par034b" name="par034b" <?php if(isset($alldata)){echo 'value='.$alldata[1]['par034'];} ?>>
            </td>
            </tr>
            <tr>
            <th scope="row">&theta;</th>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par024c" name="par024c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par024'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par025c" name="par025c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par025'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par026c" name="par026c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par026'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par027c" name="par027c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par027'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par028c" name="par028c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par028'];} ?>>
            </td>
            <td>
                <select class="form-select" id="par036c" name="par036c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par036"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                            ';
                        }
                        else if($alldata[2]["par036"]=="OK"){
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
                        echo '
                            <option value="-">-</option>
                            <option value="OK">OK</option>
                            <option value="NG">NG</option>
                        ';
                    } ?>
                </select>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par029c" name="par029c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par029'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par030c" name="par030c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par030'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par031c" name="par031c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par031'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par032c" name="par032c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par032'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par033c" name="par033c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par033'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control"  id="par034c" name="par034c" <?php if(isset($alldata)){echo 'value='.$alldata[2]['par034'];} ?>>
            </td>
            </tr>
        </tbody>
    </table>
</div>
