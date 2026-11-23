<div class="table-responsive" id="fl18-p-vi">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(!empty($alldata)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="28" scope="col"><?php echo nl2br("VISUAL INSPECTION CONTROL SHEET (FRAME LASER 1.8t AND FL 1.3t)")?></th>
      </tr>
      <tr>
        <th class="input-form" colspan="3" scope="col">Quantity</th>
        <th class="input-form" colspan="24" scope="col">Reject Item</th>
        <th class="input-form" rowspan="2">Note</th>
      </tr>
      <tr>
        <th class="input-form" scope="col">Input</th>
        <th class="input-form" scope="col">Output</th>
        <th class="input-form" scope="col">Reject</th>
        <th class="input-form" scope="col">DUST</th>
        <th class="input-form" scope="col">PAINT NG</th>
        <th class="input-form" scope="col">FRAME NG</th>
        <th class="input-form" scope="col">GOLDWIRE</th>
        <th class="input-form" scope="col">CAP CRACK</th>
        <th class="input-form" scope="col">RSN CRACK</th>
        <th class="input-form" scope="col">RSN CHIPING</th>
        <th class="input-form" scope="col">LEAD NG</th>
        <th class="input-form" scope="col">LD LACK</th>
        <th class="input-form" scope="col">LD SCRATCH</th>
        <th class="input-form" scope="col">CAP NG</th>
        <th class="input-form" scope="col">LEAD BEND</th>
        <th class="input-form" scope="col">MARK NG</th>
        <th class="input-form" scope="col">@ AG PST NG</th>
        <th class="input-form" scope="col">LD CHIPING</th>
        <th class="input-form" scope="col">CAP PEEL</th>
        <th class="input-form" scope="col">LD DIRT</th>
        <th class="input-form" scope="col">LD COAT</th>
        <th class="input-form" scope="col">DISCOLORATION</th>
        <th class="input-form" scope="col">@ WIRE FALL</th>
        <th class="input-form" scope="col">@ WIRE BENDING</th>
        <th class="input-form" scope="col"># Electrode Peel Off</th>
        <th class="input-form" scope="col"># Slight wave</th>
        <th class="input-form" scope="col">OTHERS</th>
    </tr>
    </thead>

    <tbody>
      <tr>
        <td>
          <input type="number" step="1" class="form-control" id="par013" name="par013" onchange="quantityChange4()" onkeyup="quantityChange4()" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par014" name="par014" onchange="quantityChange4()" onkeyup="quantityChange4()" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?> readonly>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par017" name="par017" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par018" name="par018" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par019" name="par019" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par019'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par020" name="par020" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par020'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par021" name="par021" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par021'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par022" name="par022" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par022'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par023" name="par023" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par023'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par024" name="par024" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par024'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par025" name="par025" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par025'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par026" name="par026" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par026'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par027" name="par027" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par027'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par028" name="par028" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par028'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par029" name="par029" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par029'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par030" name="par030" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par030'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par031" name="par031" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par031'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par032" name="par032" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par032'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par033" name="par033" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par033'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par034" name="par034" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par034'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par035" name="par035" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par035'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par036" name="par036" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par036'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par037" name="par037" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par037'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par038" name="par038" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par038'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par039" name="par039" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par039'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par040" name="par040" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par040'].'"';} ?>>
        </td>
        <td>
          <input type="text" class="form-control" id="par002" name="par002" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
        </td>
      </tr>
    </tbody>
  </table>
</div>
