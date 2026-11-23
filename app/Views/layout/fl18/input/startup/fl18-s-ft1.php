<div class="table-responsive" id="fl18-s-ft1">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-proses" name="cnt-proses" value="3">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="9" scope="col"><?php echo nl2br("VERIFICATION START UP OF FINAL TEST MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th rowspan="3">No</th>
            <th rowspan="3">Start up Check Item</th>
            <th colspan="3" rowspan="3"></th>
            <th rowspan="3">Standard</th>
            <th colspan="3" scope="col">Hasil Start Up</th>
        </tr>
        <tr>
            <th colspan="3">Check</th>
        </tr>
        <tr>
            <th >Actual</th>
            <th >Adjust <br> YES / NO</th>
            <th >After Adjust</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td >Pemeriksaan Air Pressure</td>
            <td colspan="3"></td>
            <td >0.4 ~ 0.6 Mpa</td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par001',0.4,0.6)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
            </td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par002',0.4,0.6)" id="par002" name="par002" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par003',0.4,0.6)" id="par003" name="par003" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="8">2</td>
            <td rowspan="8">Start Up Sample Measurement</td>
            <td rowspan="8">Ukur start up <br> sample pada <br> setiap holder</td>
            <td rowspan="8">H <br> O <br> L <br> D <br> E <br> R </td>
            <td >1</td>
            <td></td>
            <td>
                <select class="form-select" id="par004a" name="par004a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par004"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par004"]=="V"){
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
                <select class="form-select" id="par004b" name="par004b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par004"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par004"]=="V"){
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
                <select class="form-select" id="par004c" name="par004c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par004"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par004"]=="V"){
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
        </tr>

       <tr>
            <td >2</td>
            <td></td>
            <td>
                <select class="form-select" id="par005a" name="par005a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par005"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par005"]=="V"){
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
                <select class="form-select" id="par005b" name="par005b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par005"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par005"]=="V"){
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
                <select class="form-select" id="par005c" name="par005c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par005"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par005"]=="V"){
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
       </tr>

       <tr>
            <td >3</td>
            <td></td>
            <td>
                <select class="form-select" id="par006a" name="par006a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par006"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par006"]=="V"){
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
                <select class="form-select" id="par006b" name="par006b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par006"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par006"]=="V"){
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
                <select class="form-select" id="par006c" name="par006c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par006"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par006"]=="V"){
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
       </tr>

       <tr>
            <td >4</td>
            <td></td>
            <td>
                <select class="form-select" id="par007a" name="par007a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par007"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par007"]=="V"){
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
                <select class="form-select" id="par007b" name="par007b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par007"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par007"]=="V"){
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
                <select class="form-select" id="par007c" name="par007c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par007"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par007"]=="V"){
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
       </tr>

       <tr>
            <td >5</td>
            <td></td>
            <td>
                <select class="form-select" id="par008a" name="par008a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par008"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par008"]=="V"){
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
                <select class="form-select" id="par008b" name="par008b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par008"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par008"]=="V"){
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
                <select class="form-select" id="par008c" name="par008c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par008"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par008"]=="V"){
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
       </tr>

       <tr>
            <td >6</td>
            <td></td>
            <td>
                <select class="form-select" id="par009a" name="par009a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par009"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par009"]=="V"){
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
                <select class="form-select" id="par009b" name="par009b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par009"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par009"]=="V"){
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
                <select class="form-select" id="par009c" name="par009c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par009"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par009"]=="V"){
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
       </tr>

       <tr>
            <td >7</td>
            <td></td>
            <td>
                <select class="form-select" id="par010a" name="par010a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par010"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par010"]=="V"){
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
                <select class="form-select" id="par010b" name="par010b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par010"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par010"]=="V"){
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
                <select class="form-select" id="par010c" name="par010c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par010"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par010"]=="V"){
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
       </tr>

       <tr>
            <td >8</td>
            <td></td>
            <td>
                <select class="form-select" id="par011a" name="par011a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par011"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par011"]=="V"){
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
                <select class="form-select" id="par011b" name="par011b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par011"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par011"]=="V"){
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
                <select class="form-select" id="par011c" name="par011c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par011"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par011"]=="V"){
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
       </tr>

       <tr>
            <td >3</td>
            <td >Pengukuran sample OK dan NG</td>
            <td colspan="3">Pastikan sample OK dijudgement OK <br> (masuk ke tray rank 1) dan sample NG <br> dijudgement NG (masuk ke box NG)</td>
            <td></td>
            <td>
                <select class="form-select" id="par012a" name="par012a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par012"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par012"]=="V"){
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
                <select class="form-select" id="par012b" name="par012b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par012"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par012"]=="V"){
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
                <select class="form-select" id="par012c" name="par012c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par012"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par012"]=="V"){
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
       </tr>
       
       <tr>
            <td >4</td>
            <td >Pemeriksaan Contact Pin</td>
            <td colspan="3">Cek kondisi probe pin holder apakah <br> terjadi pembengkokan, patah</td>
            <td></td>
            <td>
                <select class="form-select" id="par013a" name="par013a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par013"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par013"]=="V"){
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
                <select class="form-select" id="par013b" name="par013b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par013"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par013"]=="V"){
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
                <select class="form-select" id="par013c" name="par013c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par013"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par013"]=="V"){
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
       </tr>

       <tr>
            <td >5</td>
            <td >Cleaning Machine</td>
            <td colspan="3">Bersihkan mesin dari debu atau <br> kotoran dengan lap dan dari device <br> yang terjatuh</td>
            <td></td>
            <td>
                <select class="form-select" id="par014a" name="par014a">
                    <?php if(isset($alldata)){
                        if($alldata[0]["par014"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[0]["par014"]=="V"){
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
                <select class="form-select" id="par014b" name="par014b">
                    <?php if(isset($alldata)){
                        if($alldata[1]["par014"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[1]["par014"]=="V"){
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
                <select class="form-select" id="par014c" name="par014c">
                    <?php if(isset($alldata)){
                        if($alldata[2]["par014"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="V">V</option>
                            <option value="X">X</option>
                            ';
                        }
                        else if($alldata[2]["par014"]=="V"){
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
       </tr>

       <tr>
            <td >6</td>
            <td >Cek Temperature dalam mesin Final <br> Test</td>
            <td colspan="3">Lihat temperature pada digital <br> thermometer di dalam mesin FT <br> Standard : 22 ~ 28°C</td>
            <td></td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par015',22,28)" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par016',22,28)" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
            <td >
                <input class="form-control" type="number" step="0.00000001" onchange="changeRange('par017',22,28)" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
       </tr>

       <tr>
            <td colspan="6">Confirmation Lot No.</td>
            <td colspan="3">
                <input type="text" step="1" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
       </tr>

       <tr>
            <td colspan="6">NOTE</td>
            <td colspan="3">
                <input type="text" step="1" class="form-control" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
       </tr>


    </tbody>
    <div class="note">
        <h3>KETERANGAN :</h3>
        <p>
            - Beri tanda (V) jika kondisi OK /Good dan beri tanda (X) jika hasil pemeriksaan NG
        </p>
        <p>
            - Tulis nilai hasil pemeriksaan sesuai dengan angka yang ditunjukkan pada masing-masing alat ukur
        </p>
        <p>
            - Apabila nilai hasil pemeriksaan tidak sesuai dengan standard, maka lakukan adjusment sampai mendapatkan nilai yang sesuai dengan standar   
        </p>
        <p>
            - Isi bagian NOTE (Catatan) pada kolom bagian bawah apabila melakukan penggantian part, adjusment, atau kondisi tidak sesuai dengan standar
        </p>
    </div>
    </table>
</div>