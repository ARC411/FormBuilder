<div class="table-responsive" id="fl18-p-m1">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="5" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET HARDENING OF INK\n(FRAME LASER 1.8t)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" colspan="3" scope="col">Temperature</th>
            <th class="input-form">Buka Tutup Damper</th>
            <th class="input-form">Pencegah Over Heat</th>
        </tr>
        <tr>
            <th class="input-form">Setting</th>
            <th class="input-form">At Input</th>
            <th class="input-form">At take Out</th>
            <th class="input-form">Setting</th>
            <th class="input-form">Setting</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par013" name="par013" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par013'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par014" name="par014" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
            </td>
            <td>
                <input type="number" step="0.00000001" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par002" name="par002" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par003" name="par003" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par003'].'"';} ?>>
            </td>
        </tr>
    </tbody>
  </table>
</div>
