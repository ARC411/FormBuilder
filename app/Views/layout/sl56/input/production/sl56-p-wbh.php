<div class="table-responsive">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="2">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns">
        <thead align="center">
        <tr>
            <th colspan="29" scope="col"><?php echo nl2br("WIRE BONDING \n (High Power)")?></th>
        </tr>
        </thead>
        <thead align="center">
            <tr>
                <th class="input-form" rowspan="3"  width="250px">Gold Wire Lot No.</th>
                <th class="input-form" rowspan="3"  width="250px">Assy Lot No.</th>
                <th class="input-form" colspan="4" scope="col" >Quantity</th>
                <th class="input-form" colspan="4" scope="col" >Foregoing Inspection (40pcs/lot)</th>
            </tr>
        
            <tr>
                <th class="input-form" rowspan="2" >In</th>
                <th class="input-form" rowspan="2" >Out</th>
                <th class="input-form" rowspan="2" >NG</th>
                <th class="input-form" rowspan="2" >Yield</th>
                <th class="input-form" rowspan="2" >Ball Pos.</th>
                <th class="input-form" colspan="2" scope="col" >Ball Size</th>
                <th class="input-form" rowspan="2" >Loop Shape</th>
            </tr>

            <tr>
                <th class="input-form"> X </th>
                <th class="input-form"> Y </th>
            </tr>
        </thead>
        <tbody>
            <td>
                <input type="text" class="form-control" id="par002" name="par002" placeholder="Gold Wire Lot No" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par003" name="par003" placeholder="Assy Lot No." <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="1" class="form-control" id="par013" name="par013" onkeyup=quantityChange1() onchange=quantityChange1() <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="1" class="form-control" id="par014" name="par014" onkeyup=quantityChange1() onchange=quantityChange1() <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
            <td>
                <input type="number" class="form-control" id="par015" name="par015" readonly <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control" id="par016" name="par016" readonly <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
            <td>
                <select id="par004" name="par004" class="form-select">
                <?php if(isset($alldata)){
                  if($alldata[0]["par004"]=="-"){
                    echo '
                      <option value="-" selected>-</option>
                      <option value="OK">OK</option>
                      <option value="NG">NG</option>
                    ';
                  }
                  else if($alldata[0]["par004"]=="OK")
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
                <select id="par005" name="par005" class="form-select">
                <?php if(isset($alldata)){
                  if($alldata[0]["par005"]=="-"){
                    echo '
                      <option value="-" selected>-</option>
                      <option value="OK">OK</option>
                      <option value="NG">NG</option>
                    ';
                  }
                  else if($alldata[0]["par005"]=="OK")
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
                <select id="par006" name="par006" class="form-select">
                <?php if(isset($alldata)){
                  if($alldata[0]["par006"]=="-"){
                    echo '
                      <option value="-" selected>-</option>
                      <option value="OK">OK</option>
                      <option value="NG">NG</option>
                    ';
                  }
                  else if($alldata[0]["par006"]=="OK")
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
                <select id="par007" name="par007" class="form-select">
                <?php if(isset($alldata)){
                  if($alldata[0]["par007"]=="-"){
                    echo '
                      <option value="-" selected>-</option>
                      <option value="OK">OK</option>
                      <option value="NG">NG</option>
                    ';
                  }
                  else if($alldata[0]["par007"]=="OK")
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
            <th colspan="17" scope="col" >Lakukan wire pull test 4pcs/shift*</th>
        </tr>
    
        <tr>
            <th class="input-form" rowspan="3" > Sample </th>
            <th class="input-form" scope="col" colspan="8">LD</th>
            <th class="input-form" scope="col" colspan="8">SM</th>
        </tr>

        <tr> 
            <th class="input-form" scope="col" colspan="2">LD1</th>
            <th class="input-form" scope="col" colspan="2">LD2</th>
            <th class="input-form" scope="col" colspan="2">LD3</th>
            <th class="input-form" scope="col" colspan="2">LD4</th>
            <th class="input-form" scope="col" colspan="2">SM1</th>   
            <th class="input-form" scope="col" colspan="2">SM2</th>   
            <th class="input-form" scope="col" colspan="2">SM3</th>   
            <th class="input-form" scope="col" colspan="2">SM4</th>   
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

            <th class="input-form" scope="col">Val</th>
            <th class="input-form" scope="col">Pos</th>

            <th class="input-form" scope="col">Val</th>
            <th class="input-form" scope="col">Pos</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="row">Sample 1</th>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par017a" name="par017a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
            <td>
                <select id="par035a" name="par035a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par035"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par035"]=="A")
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
                    else if($alldata[0]["par035"]=="B")
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
                    else if($alldata[0]["par035"]=="C")
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
                    else if($alldata[0]["par035"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par018a" name="par018a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
            <td>
                <select id="par036a" name="par036a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par036"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par036"]=="A")
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
                    else if($alldata[0]["par036"]=="B")
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
                    else if($alldata[0]["par036"]=="C")
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
                    else if($alldata[0]["par036"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par019a" name="par019a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
            <td>
                <select id="par037a" name="par037a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par037"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par037"]=="A")
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
                    else if($alldata[0]["par037"]=="B")
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
                    else if($alldata[0]["par037"]=="C")
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
                    else if($alldata[0]["par037"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par020a" name="par020a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
            <td>
                <select id="par038a" name="par038a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par038"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par038"]=="A")
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
                    else if($alldata[0]["par038"]=="B")
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
                    else if($alldata[0]["par038"]=="C")
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
                    else if($alldata[0]["par038"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par021a" name="par021a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
            <td>
                <select id="par039a" name="par039a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par039"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par039"]=="A")
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
                    else if($alldata[0]["par039"]=="B")
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
                    else if($alldata[0]["par039"]=="C")
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
                    else if($alldata[0]["par039"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par022a" name="par022a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
            <td>
                <select id="par040a" name="par040a" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[0]["par040"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[0]["par040"]=="A")
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
                    else if($alldata[0]["par040"]=="B")
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
                    else if($alldata[0]["par040"]=="C")
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
                    else if($alldata[0]["par040"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par023a" name="par023a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
            <td>
                <select id="par041a" name="par041a" class="form-select">
                    <?php if(isset($alldata)){
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
                <input type="number" step="0.00000000001" class="form-control" id="par024a" name="par024a"  <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
            <td>
                <select id="par042a" name="par042a" class="form-select">
                    <?php if(isset($alldata)){
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
        </tr>

        <tr>
            <th scope="row">Sample 2</th>
            <td>
                <input type="number" step="0.00000000001" class="form-control" id="par017b" name="par017b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par017'];} ?>>
            </td>
            <td>
                <select id="par035b" name="par035b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par035"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par035"]=="A")
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
                    else if($alldata[1]["par035"]=="B")
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
                    else if($alldata[1]["par035"]=="C")
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
                    else if($alldata[1]["par035"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par018b" name="par018b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par018'];} ?>>
            </td>
            <td>
                <select id="par036b" name="par036b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par036"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par036"]=="A")
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
                    else if($alldata[1]["par036"]=="B")
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
                    else if($alldata[1]["par036"]=="C")
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
                    else if($alldata[1]["par036"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par019b" name="par019b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par019'];} ?>>
            </td>
            <td>
                <select id="par037b" name="par037b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par037"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par037"]=="A")
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
                    else if($alldata[1]["par037"]=="B")
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
                    else if($alldata[1]["par037"]=="C")
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
                    else if($alldata[1]["par037"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par020b" name="par020b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par020'];} ?>>
            </td>
            <td>
                <select id="par038b" name="par038b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par038"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par038"]=="A")
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
                    else if($alldata[1]["par038"]=="B")
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
                    else if($alldata[1]["par038"]=="C")
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
                    else if($alldata[1]["par038"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par021b" name="par021b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par021'];} ?>>
            </td>
            <td>
                <select id="par039b" name="par039b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par039"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par039"]=="A")
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
                    else if($alldata[1]["par039"]=="B")
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
                    else if($alldata[1]["par039"]=="C")
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
                    else if($alldata[1]["par039"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par022b" name="par022b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par022'];} ?>>
            </td>
            <td>
                <select id="par040b" name="par040b" class="form-select">
                    <?php if(isset($alldata)){
                    if($alldata[1]["par040"]=="-"){
                        echo '
                        <option value="-" selected>-</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        ';
                    }
                    else if($alldata[1]["par040"]=="A")
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
                    else if($alldata[1]["par040"]=="B")
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
                    else if($alldata[1]["par040"]=="C")
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
                    else if($alldata[1]["par040"]=="D")
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
                <input type="number" step="0.00000000001" class="form-control" id="par023b" name="par023b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par023'];} ?>>
            </td>
            <td>
                <select id="par041b" name="par041b" class="form-select">
                    <?php if(isset($alldata)){
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
                <input type="number" step="0.00000000001" class="form-control" id="par024b" name="par024b"  <?php if(isset($alldata)){echo 'value='.$alldata[1]['par024'];} ?>>
            </td>
            <td>
                <select id="par042b" name="par042b" class="form-select">
                    <?php if(isset($alldata)){
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
        </tr>
           
    </tbody>
    </table>
</div>
</div>