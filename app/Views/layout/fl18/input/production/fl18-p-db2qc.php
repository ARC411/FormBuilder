<div class="table-responsive" id="fl18-p-db2qc">
  <?= csrf_field(); ?>
  <input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="24" scope="col"><?php echo nl2br("INSPECTION LD D/B 2 CONTROL CHECK SHEET\n(FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
      </tr>
      <tr>
        <th class="input-form" colspan="4" scope="col">Quantity</th>
        <th class="input-form" colspan="20" scope="col">Reject Item</th>
      </tr>
      <tr>
        <th class="input-form">Input</th>
        <th class="input-form">Output</th>
        <th class="input-form">Reject</th>
        <th class="input-form">Yield</th>

        <th class="input-form">Sic Slant</th>
        <th class="input-form">Sic X Diff</th>
        <th class="input-form">Sic Y Diff</th>
        <th class="input-form">Sic Crack</th>
        <th class="input-form">Sic Dirt</th>
        <th class="input-form">Sic Nothing</th>
        <th class="input-form">LD X Diff</th>
        <th class="input-form">LD Y Diff</th>
        <th class="input-form">LD TH Slant</th>
        <th class="input-form">LD Crack</th>
        <th class="input-form">LD Dirt</th>
        <th class="input-form">LD Nothing</th>
        <th class="input-form">AG Few</th>
        <th class="input-form">AG Much</th>
        <th class="input-form">AG NG</th>
        <th class="input-form">Dust NG</th>
        <th class="input-form">LD Chipping</th>
        <th class="input-form">Sic Float</th>
        <th class="input-form">LD Scratch</th>
        <th class="input-form">Others</th>
      
    </tr>
    </thead>

    <tbody>
      <tr>
        <td>
          <input type="number" step="1" class="form-control" id="par013" name="par013" onchange="quantityChange1()" onkeyup="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par014" name="par014" onchange="quantityChange1()" onkeyup="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par027" name="par027" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par028" name="par028" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par029" name="par029" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par030" name="par030" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par030'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par031" name="par031" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par031'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par032" name="par032" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par032'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par033" name="par033" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par033'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par034" name="par034" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par034'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par035" name="par035" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par035'];} ?>>
        </td>
        <td>
          <input type="number" step="1" class="form-control" id="par036" name="par036" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par036'];} ?>>
        </td>
    </tbody>
  </table>
</div>