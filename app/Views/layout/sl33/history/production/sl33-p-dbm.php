<div class="table-responsive mt-3" id="sl33-p-dbm">
<?= csrf_field(); ?>
<?php
if (!function_exists('renderForegoingVal')) {
    function renderForegoingVal($val) {
        $clean = trim((string)$val);
        $v = strtoupper($clean);
        if ($v === 'V' || $v === 'OK' || $clean === '✔') {
            return '<span class="text-success fw-bold">&#10004;</span>';
        }
        if ($v === 'X' || $v === 'NG' || $clean === '✖') {
            return '<span class="text-danger fw-bold">&#10008;</span>';
        }
        return esc($clean !== '' ? $clean : '-');
    }
}
?>
<input type="hidden" id="cnt-table" value="1">
<input type="hidden" name="cnt-proses" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="21" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET LD DIE BONDING 2 MACHINE\n(SINGLE LASER Ø 3.3)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th rowspan="3">No.</th>
            <th rowspan="3">Operator Start</th>         
            <th rowspan="3">Shift/Group</th>
            <th rowspan="3">Operator Finish</th>         
            <th rowspan="3">Shift/Group</th>
            <th rowspan="3">Mch. No.</th>         
            <th rowspan="3">Model</th>
            <th rowspan="3">Lot No.</th>
            <th rowspan="3">Date (dd/mm/yyyy)</th>
            <th colspan="2" scope="col">Time</th>
            <th colspan="3" scope="col">Quantity</th>
            <th rowspan="3">Head No.</th>
            <th colspan="6" scope="col">Foregoing Result (5 pcs/head/lot) <br> (✔: OK, ✖: NG)</th>
        </tr>
        <tr>
            <th class="input-form" rowspan="2">IN</th>
            <th class="input-form" rowspan="2">OUT</th>
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
        <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+4) { ?>
            <tr>
                <td rowspan="8"><?= $i/4+1; ?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['name'] ?? '');?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['shift'] ?? '') .' / '. (string)($alldata[$i]['group'] ?? '');?></td>
                <td rowspan="8">
                <?php
                    if(!empty($alldata[$i]['name2'])){
                        echo (string)$alldata[$i]['name2'];
                    } else {
                        echo "";
                    }
                ?>
                </td>
                <td rowspan="8">
                <?php
                    if(!empty($alldata[$i]['shift2'])){
                        echo (string)$alldata[$i]['shift2'] . ' / ' . (string)($alldata[$i]['group2'] ?? '');
                    } else {
                        echo "";
                    }
                ?>
                </td>
                <td rowspan="8"><?= (string)($alldata[$i]['machno'] ?? ''); ?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['model'] ?? ''); ?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['lotno'] ?? ''); ?></td>
                <td rowspan="8"><?= !empty($alldata[$i]['created_at']) ? date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])) : (!empty($alldata[$i]['work_date']) ? date('d/m/Y', strtotime((string)$alldata[$i]['work_date'])) : '-'); ?></td>
                <td rowspan="8"><?= !empty($alldata[$i]['par008']) ? date('H:i', strtotime((string)$alldata[$i]['par008'])) : (!empty($alldata[$i]['time_in']) ? date('H:i', strtotime((string)$alldata[$i]['time_in'])) : '-'); ?></td>
                <td rowspan="8">
                <?php
                    if(!empty($alldata[$i]['par009'])){
                        echo date('H:i', strtotime((string)$alldata[$i]['par009'])); 
                    } elseif (!empty($alldata[$i]['time_out'])) {
                        echo date('H:i', strtotime((string)$alldata[$i]['time_out']));
                    } else {
                        echo "";
                    }
                ?>
                </td>
                <td rowspan="8"><?= (string)($alldata[$i]['par013'] ?? ''); ?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['par014'] ?? ''); ?></td>
                <td rowspan="8"><?= (string)($alldata[$i]['par015'] ?? ''); ?></td>

                <td><b>1</b></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par002'] ?? ''); ?></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par003'] ?? ''); ?></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par004'] ?? ''); ?></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par005'] ?? ''); ?></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par006'] ?? ''); ?></td>
                <td class="text-center"><?= renderForegoingVal($alldata[$i]['par007'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>2</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par035'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par036'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par037'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par038'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par039'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i]['par040'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>3</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par002'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par003'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par004'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par005'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par006'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par007'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>4</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par035'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par036'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par037'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par038'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par039'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+1]['par040'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>5</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par002'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par003'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par004'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par005'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par006'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par007'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>6</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par035'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par036'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par037'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par038'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par039'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+2]['par040'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>7</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par002'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par003'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par004'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par005'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par006'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par007'] ?? ''); ?></td>
            </tr>
            <tr>
              <td><b>8</b></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par035'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par036'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par037'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par038'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par039'] ?? ''); ?></td>
              <td class="text-center"><?= renderForegoingVal($alldata[$i+3]['par040'] ?? ''); ?></td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
