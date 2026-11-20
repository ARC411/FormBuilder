<div class="table-responsive" id="fl18-p-m2">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(!empty($alldata) && isset($number)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="11" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET MARKING\n(FRAME LASER 1.8t AND 1.3t)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" colspan="3" scope="col">Quantity</th>
            <th class="input-form" colspan="2" scope="col">Marking Item</th>
            <th class="input-form" colspan="6" scope="col">Foregoing Inspection (OK, NG)</th>
        </tr>
        <tr>
            <th class="input-form" rowspan="2">Input</th>
            <th class="input-form" rowspan="2">Output</th>
            <th class="input-form" rowspan="2">Reject</th>
            <th class="input-form" rowspan="2">Model Code</th>
            <th class="input-form" rowspan="2">Factory (FTI)</th>
            <th class="input-form" colspan="3">Visual check 1 frame/lot</th>
            <th class="input-form" colspan="3">Vic Inspection sampling 1 frame/lot</th>
        </tr>
        <tr>
            <th class="input-form">Pecah</th>
            <th class="input-form">Tipis & Pecah</th>
            <th class="input-form">Bentuk</th>
            <th class="input-form">Pecah</th>
            <th class="input-form">Tipis & Pecah</th>
            <th class="input-form">Bentuk</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?>>
            </td>
            <td>
                <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?> readonly>
            </td>
            <td>
                <input type="text" class="form-control" id="par002" name="par002" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par003" name="par003" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par003'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par017" name="par017" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par018" name="par018" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par019" name="par019" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par019'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par020" name="par020" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par020'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par021" name="par021" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par021'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par022" name="par022" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par022'].'"';} ?>>
            </td>
        </tr>
    </tbody>
  </table>
</div>
