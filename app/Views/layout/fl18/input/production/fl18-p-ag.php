<div class="table-responsive" id="fl18-p-ag">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="7" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET AG PASTE HARDENING\n(FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" rowspan="2" scope="col">Type</th>
            <th class="input-form" colspan="2" scope="col">Quantity</th>
            <th class="input-form" colspan="3" scope="col">Temperature (&deg;C)</th>
            <th class="input-form" rowspan="2" scope="col">Plan Time<br>Take Out</th>
        </tr>
        <tr>
            <th class="input-form" scope="col">Input</th>
            <th class="input-form" scope="col">Output</th>
            <th class="input-form" scope="col">Setting</th>
            <th class="input-form" scope="col">At Input</th>
            <th class="input-form" scope="col">At Take Out</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan="1">
                <select class="form-select" id="par035" name="par035" onchange="agTypeChange(this.value)">
                    <option value="Others (N)" <?= (isset($alldata) && $alldata[0]['par035'] == 'Others (N)') ? 'selected' : (!isset($alldata) ? 'selected' : '') ?>>Others (N)</option>
                    <option value="Green (Y)" <?= (isset($alldata) && $alldata[0]['par035'] == 'Green (Y)') ? 'selected' : '' ?>>Green (Y)</option>
                </select>
            </td>
            <td rowspan="1">
                <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange6()" onchange="quantityChange6()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?> required>
            </td>
            <td rowspan="1">
                <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange6()" onchange="quantityChange6()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input type="number" step="0.01" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} else { echo 'value="155"'; } ?>>
            </td>
            <td rowspan="1">
                <input type="number" step="0.01" class="form-control" id="par016" name="par016" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par016'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input type="number" step="0.01" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input type="time" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>>
            </td>
        </tr>
    </tbody>
  </table>
</div>

<script>
function agTypeChange(val) {
    var settingInput = document.getElementById('par015');
    if (settingInput && (!settingInput.value || settingInput.value === '155' || settingInput.value === '200')) {
        if (val === 'Green (Y)') {
            settingInput.value = '200';
        } else {
            settingInput.value = '155';
        }
    }
}
</script>
