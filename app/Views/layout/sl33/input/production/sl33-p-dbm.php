<div class="table-responsive text-nowrap" id="sl33-p-dbm">
<?= csrf_field(); ?>
<?php
if (!function_exists('renderForegoingSelect')) {
    function renderForegoingSelect($id, $currentVal = '') {
        $clean = trim((string)$currentVal);
        $isOk = in_array($clean, ['✔', 'V', 'OK', 'v', 'ok']);
        $isNg = in_array($clean, ['✖', 'X', 'NG', 'x', 'ng']);
        return '<select class="form-select form-select-sm text-center foregoing-select" id="' . $id . '" name="' . $id . '" style="padding: 0.25rem 0.4rem; min-width: 60px;">
            <option value="">-</option>
            <option value="✔"' . ($isOk ? ' selected' : '') . '>✔ (OK)</option>
            <option value="✖"' . ($isNg ? ' selected' : '') . '>✖ (NG)</option>
        </select>';
    }
}
?>
<input type="hidden" name="cnt-proses" value="4">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(!empty($alldata) && isset($number)){echo esc($number);} ?>">
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="10" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET LD DIE BONDING 2 MACHINE\n(SINGLE LASER Ø 3.3)")?></th>
      </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" colspan="3" scope="col">Quantity</th>
            <th class="input-form" rowspan="3">Head No.</th>
            <th class="input-form" colspan="6" scope="col">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Foregoing Result (5 pcs/head/lot) (✔: OK, ✖: NG)</span>
                    <button type="button" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;" onclick="setAllForegoingOk()" title="Set semua Foregoing Result ke ✔ OK">
                        <i class="bi bi-check-all"></i> Semua ✔ OK
                    </button>
                </div>
            </th>
        </tr>
        <tr>
            <th class="input-form" rowspan="2">Input</th>
            <th class="input-form" rowspan="2">Output</th>
            <th class="input-form" rowspan="2">NG</th>
            <th class="input-form" colspan="2">SM Chip</th>
            <th class="input-form" colspan="4">LD Chip</th>
        </tr>
        <tr>      
            <th class="input-form">Crack</th>
            <th class="input-form">Chipping</th>
            <th class="input-form">LD Position</th>
            <th class="input-form">Miss LD Chip</th>
            <th class="input-form">Crack</th>
            <th class="input-form">Chipping</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <td rowspan="8">
            <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange4()" onchange="quantityChange4()" placeholder="Input" <?php if(!empty($alldata[0])){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?>>
          </td>
          <td rowspan="8">
            <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange4()" onchange="quantityChange4()" placeholder="Output" <?php if(!empty($alldata[0])){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
          </td>
          <td rowspan="8">
            <input type="number" step="1" class="form-control" id="par015" name="par015" placeholder="NG" readonly <?php if(!empty($alldata[0])){echo 'value="'.$alldata[0]['par015'].'"';} ?>>
          </td>
          <td>1</td>
          <td><?= renderForegoingSelect('par002', $alldata[0]['par002'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par003', $alldata[0]['par003'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par004', $alldata[0]['par004'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par005', $alldata[0]['par005'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par006', $alldata[0]['par006'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par007', $alldata[0]['par007'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>2</td>
          <td><?= renderForegoingSelect('par035', $alldata[0]['par035'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par036', $alldata[0]['par036'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par037', $alldata[0]['par037'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par038', $alldata[0]['par038'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par039', $alldata[0]['par039'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par040', $alldata[0]['par040'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>3</td>
          <td><?= renderForegoingSelect('par002b', $alldata[1]['par002'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par003b', $alldata[1]['par003'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par004b', $alldata[1]['par004'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par005b', $alldata[1]['par005'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par006b', $alldata[1]['par006'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par007b', $alldata[1]['par007'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>4</td>
          <td><?= renderForegoingSelect('par035b', $alldata[1]['par035'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par036b', $alldata[1]['par036'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par037b', $alldata[1]['par037'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par038b', $alldata[1]['par038'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par039b', $alldata[1]['par039'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par040b', $alldata[1]['par040'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>5</td>
          <td><?= renderForegoingSelect('par002c', $alldata[2]['par002'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par003c', $alldata[2]['par003'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par004c', $alldata[2]['par004'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par005c', $alldata[2]['par005'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par006c', $alldata[2]['par006'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par007c', $alldata[2]['par007'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>6</td>
          <td><?= renderForegoingSelect('par035c', $alldata[2]['par035'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par036c', $alldata[2]['par036'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par037c', $alldata[2]['par037'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par038c', $alldata[2]['par038'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par039c', $alldata[2]['par039'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par040c', $alldata[2]['par040'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>7</td>
          <td><?= renderForegoingSelect('par002d', $alldata[3]['par002'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par003d', $alldata[3]['par003'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par004d', $alldata[3]['par004'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par005d', $alldata[3]['par005'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par006d', $alldata[3]['par006'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par007d', $alldata[3]['par007'] ?? ''); ?></td>
        </tr>
        <tr>
          <td>8</td>
          <td><?= renderForegoingSelect('par035d', $alldata[3]['par035'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par036d', $alldata[3]['par036'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par037d', $alldata[3]['par037'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par038d', $alldata[3]['par038'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par039d', $alldata[3]['par039'] ?? ''); ?></td>
          <td><?= renderForegoingSelect('par040d', $alldata[3]['par040'] ?? ''); ?></td>
        </tr>
    </tbody>
  </table>
</div>

<script>
function setAllForegoingOk() {
    var selects = document.querySelectorAll('#sl33-p-dbm .foregoing-select');
    selects.forEach(function(sel) {
        sel.value = '✔';
    });
}
</script>
