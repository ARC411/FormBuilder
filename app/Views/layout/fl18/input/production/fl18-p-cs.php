<div class="table-responsive" id="fl18-p-cs">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="13" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET CAP SET AUTO\n(FRAME LASER 1.8t)")?></th>
      </tr>
      <tr>
        <th class="input-form" colspan="3" scope="col">Quantity</th>
        <th class="input-form" colspan="7" scope="col">Foregoing Check (2 frame/lot)</th>
        <th class="input-form" colspan="2" scope="col">@ Cap Strength</th>
      </tr>
      <tr>
        <th class="input-form" rowspan="2">Input</th>
        <th class="input-form" rowspan="2">Output</th>
        <th class="input-form" rowspan="2">NG</th>
        <th class="input-form" rowspan="2">*Float</th>
        <th class="input-form" rowspan="2">*Direction Surface</th>
        <th class="input-form" rowspan="2">*Direction 180&deg;</th>
        <th class="input-form" rowspan="2">*Scratch Cap</th>
        <th class="input-form" rowspan="2">*Scratch Frame</th>
        <th class="input-form" rowspan="2">*No Cap</th>
        <th class="input-form" rowspan="2">*Burr</th>
        <th class="input-form" colspan="2" scope="col">0.6 N atau lebih (2pcs/hari)</th>
      </tr>
      <tr>
        <th class="input-form" scope="col">A</th>
        <th class="input-form" scope="col">B</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?> required>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?> readonly>
        </td>
        <td>
          <select class="form-select" id="par035" name="par035">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par035"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par035"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par035"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par036" name="par036">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par036"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par036"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par036"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par037" name="par037">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par037"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par037"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par037"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par038" name="par038">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par038"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par038"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par038"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par039" name="par039">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par039"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par039"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par039"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par040" name="par040">
            <option value="-" <?= (isset($alldata) && $alldata[0]["par040"]=="-") ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && $alldata[0]["par040"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && $alldata[0]["par040"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <select class="form-select" id="par041" name="par041">
            <option value="-" <?= (isset($alldata) && (!isset($alldata[0]["par041"]) || $alldata[0]["par041"]=="-")) ? 'selected' : '' ?>>-</option>
            <option value="OK" <?= (isset($alldata) && isset($alldata[0]["par041"]) && $alldata[0]["par041"]=="OK") ? 'selected' : '' ?>>OK</option>
            <option value="NG" <?= (isset($alldata) && isset($alldata[0]["par041"]) && $alldata[0]["par041"]=="NG") ? 'selected' : '' ?>>NG</option>
          </select>
        </td>
        <td>
          <input type="number" step="0.00000001" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="0.00000001" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>>
        </td>
      </tr>
    </tbody>
  </table>
</div>
