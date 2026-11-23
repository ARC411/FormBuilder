<div class="table-responsive" id="fl18-p-bi">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="2">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="5" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET BURN IN MANUAL\n(FL 1.8t and FL 1.3t)")?></th>
      </tr>
    </thead>
    <thead align="center">
      <tr>
        <th class="input-form" colspan="1">Setting Machine</th>
        <th class="input-form" rowspan="3" scope="col">Test File Name</th>
        <th class="input-form" rowspan="3" scope="col">PC No. and Box ID</th>
        <th class="input-form" colspan="2" scope="col">Resetting Machine</th>
      </tr>
      <tr>
        <th class="input-form" rowspan="2">Input Quantity</th>
        <th class="input-form" colspan="2">Quantity</th>
      </tr>
      <tr>
        <th class="input-form" scope="col">Pass</th>
        <th class="input-form" scope="col">Reject</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?> required>
        </td>
        <td>
          <input type="text" class="form-control" id="par002" name="par002" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
        </td>
        <td>
          <input type="text" class="form-control" id="par003" name="par003" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par003'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?>>
        </td>
      </tr>
    </tbody>
  </table>

  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="11" scope="col">Failure Item (NG)</th>
      </tr>
      <tr>
        <th class="input-form">A1</th>
        <th class="input-form">A3-A5</th>
        <th class="input-form">B1-B5</th>
        <th class="input-form">E1</th>
        <th class="input-form">E2</th>
        <th class="input-form">E3</th>
        <th class="input-form">E4</th>
        <th class="input-form">E5</th>
        <th class="input-form">E7</th>
        <th class="input-form">RE</th>
        <th class="input-form">Others</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><input type="number" step="1" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par019" name="par019" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par019'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par020" name="par020" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par020'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par021" name="par021" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par021'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par022" name="par022" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par022'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par023" name="par023" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par023'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par024" name="par024" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par024'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par025" name="par025" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par025'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par026" name="par026" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par026'].'"';} ?>></td>
        <td><input type="number" step="1" class="form-control" id="par027" name="par027" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par027'].'"';} ?>></td>
      </tr>
    </tbody>
  </table>
</div>
