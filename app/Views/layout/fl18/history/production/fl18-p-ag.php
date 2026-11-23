<div class="table-responsive mt-3" id="fl18-p-ag">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="16" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET AG PASTE HARDENING\n(FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th rowspan="2">No.</th>    
            <th rowspan="2">Nomor Oven</th>         
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Shift / Group</th>
            <th rowspan="2">Nama Model</th>
            <th rowspan="2">Type</th>
            <th rowspan="2">Lot No</th> 
            <th colspan="2">Quantity</th>
            <th colspan="2">Nama Operator</th>
            <th colspan="3">Temperatur &deg;C</th>
            <th colspan="2">Time</th>
        </tr>
        <tr>
            <th>Input</th>
            <th>Output</th>
            <th>Input to oven</th>
            <th>Take out from oven</th>
            <th>Setting</th>
            <th>At Input</th>
            <th>At take out</th>
            <th>Input</th>
            <th>Take Out (Plan / Actual)</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < (!empty($alldata) ? count($alldata) : 0); $i=$i+1) { ?>
            <tr>
                <td><?= $i + 1; ?></td>
                <td><?= (string)$alldata[$i]['machno']; ?></td>
                <td><?= !empty($alldata[$i]['par008']) ? date('d/m/Y', strtotime($alldata[$i]['par008'])) : date('d/m/Y', strtotime($alldata[$i]['created_at'])); ?></td>
                <td><?= (string)$alldata[$i]['shift'] . (!empty($alldata[$i]['group']) ? ' / ' . (string)$alldata[$i]['group'] : ''); ?></td>
                <td><?= (string)$alldata[$i]['model']; ?></td>
                <td><?= !empty($alldata[$i]['par035']) ? (string)$alldata[$i]['par035'] : 'Others (N)'; ?></td>
                <td><?= (string)$alldata[$i]['lotno']; ?></td>
                <td><?= (string)$alldata[$i]['par013']; ?></td>
                <td><?= (string)$alldata[$i]['par014']; ?></td>
                <td><?= (string)$alldata[$i]['name'] . (!empty($alldata[$i]['shift']) ? ' (' . (string)$alldata[$i]['shift'] . ')' : ''); ?></td>
                <td>
                  <?php
                    if(!empty($alldata[$i]['name2'])){
                      echo (string)$alldata[$i]['name2'] . (!empty($alldata[$i]['shift2']) ? ' (' . (string)$alldata[$i]['shift2'] . ')' : '');
                    } else {
                      echo "-";
                    }
                  ?>
                </td>
                <td><?= (string)$alldata[$i]['par015']; ?></td>
                <td><?= (string)$alldata[$i]['par016']; ?></td>
                <td><?= (string)$alldata[$i]['par017']; ?></td>
                <td><?= !empty($alldata[$i]['par008']) ? date('H:i', strtotime((string)$alldata[$i]['par008'])) : '-'; ?></td>
                <td>
                  <?php
                    $planTime = !empty($alldata[$i]['par018']) ? (string)$alldata[$i]['par018'] : '-';
                    $actualTime = !empty($alldata[$i]['par009']) ? date('H:i', strtotime((string)$alldata[$i]['par009'])) : '-';
                    echo "Plan: " . $planTime . " / Act: " . $actualTime;
                  ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
  </table>
</div>
