<div class="table-responsive" id="fl18-p-wb">
    <?= csrf_field(); ?>
    <input type="hidden" name="cnt-proses" value="4">
    <input type="hidden" id="number-edit" name="number-edit" value="<?php if(!empty($alldata[0])){echo $number;} ?>">
    <table class="table table-striped-columns">
        <thead align="center">
            <tr>
                <th colspan="13" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET WIRE BONDING (FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
            </tr>
            <tr>
                <th class="input-form" rowspan="3"  width="200px">Gold Wire Lot No.</th>
                <th class="input-form" rowspan="3"  width="200px">Assy Lot No.</th>
                <th class="input-form" scope="col" colspan="3">Quantity</th>
                <th class="input-form" scope="col" colspan="6">Foregoing Inspection (40pcs/Magazine)</th>
            </tr>
    
            <tr>
                <th class="input-form"  rowspan="2">Input</th>
                <th class="input-form"  rowspan="2">Output</th>
                <th class="input-form"  rowspan="2">NG</th>
                <th class="input-form"  scope="col" colspan="3">Magazine 1</th>
                <th class="input-form"  scope="col" colspan="3">Magazine 2</th>
            </tr>
        
            <tr>
                <th class="input-form"  scope="col">Ball Pos</th>
                <th class="input-form"  scope="col">Ball Size</th>
                <th class="input-form"  scope="col">Loop Shape</th>
                <th class="input-form"  scope="col">Ball Pos</th>
                <th class="input-form"  scope="col">Ball Size</th>
                <th class="input-form"  scope="col">Loop Shape</th>
            </tr>
        </thead>
        <tbody>
            <td>
                <input type="text" class="form-control" id="par002" name="par002" placeholder="Gold Wire Lot No" <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par003" name="par003" placeholder="Assy Lot No." <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
            <td>
                <input type="number" oninput="" step="1" class="form-control" id="par013" name="par013" placeholder="Input" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
            <td>
                <input type="number" oninput="" step="1" class="form-control" id="par014" name="par014" placeholder="Output" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
            <td>
                <input type="number" oninput="" step="1" class="form-control" id="par015" name="par015" placeholder="NG" readonly <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
            <td>
                <select id="par035" name="par035" class="form-select">
                    <?php if(!empty($alldata[0])){
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
            <select id="par036" name="par036" class="form-select">
                    <?php if(!empty($alldata[0])){
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
                <select id="par037" name="par037" class="form-select">
                    <?php if(!empty($alldata[0])){
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
            <td>
                <select id="par038" name="par038" class="form-select">
                    <?php if(!empty($alldata[0])){
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
            <td>
                <select id="par039" name="par039" class="form-select">
                    <?php if(!empty($alldata[0])){
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
            <td>
                <select id="par040" name="par040" class="form-select">
                    <?php if(!empty($alldata[0])){
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
        </tbody>
        </table>

    <table class="table table-striped-columns">
        <thead align="center">
            <tr>
                <th class="input-form" scope="col" colspan="24">Lakukan wire pull test 4pcs/shift*</th>
            </tr>
    
            <tr>
                <th class="input-form"  scope="col" colspan="6">Sample 1</th>
                <th class="input-form"  scope="col" colspan="6">Sample 2</th>
            </tr>
        
            <tr>
                <th class="input-form"  scope="col" colspan="2">SM</th>
                <th class="input-form"  scope="col" colspan="2">LD1</th>
                <th class="input-form"  scope="col" colspan="2">LD2</th>

                <th class="input-form"  scope="col" colspan="2">SM</th>
                <th class="input-form"  scope="col" colspan="2">LD1</th>
                <th class="input-form"  scope="col" colspan="2">LD2</th>
            </tr>

            <tr>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>

                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
            </tr>
        </thead>
        <tbody>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par017a" name="par017a"  <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
            <td>
                <select id="par041a" name="par041a" class="form-select">
                    <?php if(!empty($alldata[0])){
                        if($alldata[0]["par041"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par041"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par041"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par041"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par041"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par018a" name="par018a"  <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
            <td>
                <select id="par042a" name="par042a" class="form-select">
                    <?php if(!empty($alldata[0])){
                        if($alldata[0]["par042"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par042"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par042"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par042"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par042"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par019a" name="par019a"  <?php if(!empty($alldata[0])){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
            <td>
                <select id="par043a" name="par043a" class="form-select">
                    <?php if(!empty($alldata[0])){
                        if($alldata[0]["par043"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par043"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par043"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par043"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[0]["par043"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>


            <!-- batas  -->
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par017b" name="par017b"  <?php if(!empty($alldata[1])){echo 'value='.$alldata[1]['par017'];} ?>>
            </td>
            <td>
                <select id="par041b" name="par041b" class="form-select">
                    <?php if(!empty($alldata[1])){
                        if($alldata[1]["par041"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par041"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par041"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par041"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par041"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par018b" name="par018b"  <?php if(!empty($alldata[1])){echo 'value='.$alldata[1]['par018'];} ?>>
            </td>
            <td>
                <select id="par042b" name="par042b" class="form-select">
                    <?php if(!empty($alldata[1])){
                        if($alldata[1]["par042"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par042"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par042"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par042"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par042"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par019b" name="par019b"  <?php if(!empty($alldata[1])){echo 'value='.$alldata[1]['par019'];} ?>>
            </td>
            <td>
                <select id="par043b" name="par043b" class="form-select">
                    <?php if(!empty($alldata[1])){
                        if($alldata[1]["par043"]=="-"){
                            echo '
                            <option value="-" selected>-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par043"]=="A")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A" selected>A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par043"]=="B")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B" selected>B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par043"]=="C")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C" selected>C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else if($alldata[1]["par043"]=="D")
                        {
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" selected>D</option>
                            <option value="E">E</option>
                            ';
                        }
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D" >D</option>
                            <option value="E" selected>E</option>
                            ';
                        }}
                        else{
                            echo '
                            <option value="-">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            ';
                        }?>
                </select>
            </td>
        </tbody>
    </table>


    <table class="table table-striped-columns">
        <thead align="center">
            <tr>
                <th class="input-form" scope="col" colspan="24">Lakukan wire pull test 4pcs/shift*</th>
            </tr>
    
            <tr>
                <th class="input-form"  scope="col" colspan="6">Sample 3</th>
                <th class="input-form"  scope="col" colspan="6">Sample 4</th>
            </tr>
        
            <tr>
                <th class="input-form"  scope="col" colspan="2">SM</th>
                <th class="input-form"  scope="col" colspan="2">LD1</th>
                <th class="input-form"  scope="col" colspan="2">LD2</th>

                <th class="input-form"  scope="col" colspan="2">SM</th>
                <th class="input-form"  scope="col" colspan="2">LD1</th>
                <th class="input-form"  scope="col" colspan="2">LD2</th>
            </tr>

            <tr>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>

                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
                <th class="input-form" scope="col">Val</th>
                <th class="input-form" scope="col">Pos</th>
            </tr>
        </thead>
    <tbody>

        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par017c" name="par017c"  <?php if(!empty($alldata[2])){echo 'value='.$alldata[2]['par017'];} ?>>
        </td>
        <td>
            <select id="par041c" name="par041c" class="form-select">
                 <?php if(!empty($alldata[2])){
                    if($alldata[2]["par041"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par041"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par041"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par041"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par041"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>
        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par018c" name="par018c"  <?php if(!empty($alldata[2])){echo 'value='.$alldata[2]['par018'];} ?>>
        </td>
        <td>
            <select id="par042c" name="par042c" class="form-select">
                 <?php if(!empty($alldata[2])){
                    if($alldata[2]["par042"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par042"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par042"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par042"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par042"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>
        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par019c" name="par019c"  <?php if(!empty($alldata[2])){echo 'value='.$alldata[2]['par019'];} ?>>
        </td>
        <td>
            <select id="par043c" name="par043c" class="form-select">
                 <?php if(!empty($alldata[2])){
                    if($alldata[2]["par043"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par043"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par043"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par043"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[2]["par043"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>


        <!-- batas  -->
        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par017d" name="par017d"  <?php if(!empty($alldata[3])){echo 'value='.$alldata[3]['par017'];} ?>>
        </td>
        <td>
            <select id="par041d" name="par041d" class="form-select">
                 <?php if(!empty($alldata[3])){
                    if($alldata[3]["par041"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par041"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par041"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par041"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par041"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>
        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par018d" name="par018d"  <?php if(!empty($alldata[3])){echo 'value='.$alldata[3]['par018'];} ?>>
        </td>
        <td>
            <select id="par042d" name="par042d" class="form-select">
                 <?php if(!empty($alldata[3])){
                    if($alldata[3]["par042"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par042"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par042"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par042"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par042"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>
        <td>
            <input type="number" step="0.00000000001" class="form-control" id="par019d" name="par019d"  <?php if(!empty($alldata[3])){echo 'value='.$alldata[3]['par019'];} ?>>
        </td>
        <td>
            <select id="par043d" name="par043d" class="form-select">
                 <?php if(!empty($alldata[3])){
                    if($alldata[3]["par043"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par043"]=="A")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A" selected>A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par043"]=="B")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par043"]=="C")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C" selected>C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[3]["par043"]=="D")
                    {
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" selected>D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D" >D</option>
                        <option value="E" selected>E</option>
                        ';
                    }}
                    else{
                        echo '
                        <option value="-">-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }?>
            </select>
        </td>
    </tbody>
    </table>
    </div>
</div>
