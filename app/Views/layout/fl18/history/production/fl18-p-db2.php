<div class="table-responsive mt-3" id="fl18-p-db2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
  <table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan="32" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL DIE BONDING 2")?></th>
      </tr>
      <tr>
        <th rowspan="4">No.</th>   
        <th rowspan="4">Operator Start</th>
        <th rowspan="4">Shift/Group</th>
        <th rowspan="4">Operator Finish</th>
        <th rowspan="4">Shift/Group</th>      
        <th rowspan="4">Mch. No.</th>         
        <th rowspan="4">Model</th>
        <th rowspan="4">Lot No.</th>
        <th rowspan="4">Date (dd/mm/yyyy)</th>
        <th colspan="3" scope="col">Quantity</th>
        <th colspan="2" scope="col">Time</th>
        <th colspan="6" scope="col">Foregoing Result (40 pcs/lot)</th>
        <th colspan="3">@ Die Bond Strength</th>
        <th colspan="2" rowspan="2">* Check Mark 2Pcs/shift</th>
        <th colspan="2"># Check of Ag Paste</th>
        <th colspan="2"># Check ketebalan Ag Paste (nilai h)</th>
        <th rowspan="4">**Disp. Air Pressure</th>
        <th rowspan="4">**Disp. time</th>
        <th rowspan="4">**Vacuum pressure</th>
      </tr>
      <tr>
        <th rowspan="3">In</th>
        <th rowspan="3">Out</th>
        <th rowspan="3">NG</th>
        <th rowspan="3">IN</th>
        <th rowspan="3">OUT</th>
        <th colspan="2" scope="col">SM chip</th>
        <th colspan="4" scope="col">LD chip</th>
        <th colspan="3" scope="col">(2pcs/shift)</th>
        <th colspan="2" scope="col">(2pcs/Lot)</th>
        <th colspan="2" scope="col">(2pcs/Lot)</th>
      </tr>
      <tr>
        <th rowspan="2">Crack</th>
        <th rowspan="2">Chipping</th>
        <th rowspan="2">LD pos</th>
        <th rowspan="2">Miss LD chip</th>
        <th rowspan="2">Crack</th>
        <th rowspan="2">Chipping</th>
        <th rowspan="2">Std (Min)</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th colspan="2" scope="col">OK:✓ <br> NG:X</th>
      </tr>
      <tr>
        <th>1st</th>
        <th>2nd</th>
        <th>1st</th>
        <th>2nd</th>
        <th>1st</th>
        <th>2nd</th>
        <th>1st</th>
        <th>2nd</th>
      </tr>
    </thead>

    <tbody>
    <?php for($i = 0; $i < count($alldata); $i++) { 
        $row = $alldata[$i];
        $name2Val  = trim((string)($row['name2'] ?? ''));
        $emp2Val   = trim((string)($row['empid2'] ?? ''));
        $shift2Val = trim((string)($row['shift2'] ?? ''));
        $group2Val = trim((string)($row['group2'] ?? ''));
        $hasOpFinish = (($name2Val !== '' && $name2Val !== '-') || ($emp2Val !== '' && $emp2Val !== '-'));

        $timeInRaw  = trim((string)($row['par008'] ?? ''));
        $tsIn       = ($timeInRaw !== '' && $timeInRaw !== '-') ? strtotime($timeInRaw) : false;
        $timeInStr  = $tsIn ? date('H:i', $tsIn) : '-';

        $timeOutRaw = trim((string)($row['par009'] ?? ''));
        $tsOut      = ($hasOpFinish && $timeOutRaw !== '' && $timeOutRaw !== '-') ? strtotime($timeOutRaw) : false;
        $timeOutStr = ($tsOut && date('H:i', $tsOut) !== '07:00') ? date('H:i', $tsOut) : ($tsOut && strpos($timeOutRaw, '07:00') !== false ? '07:00' : '-');

        $createdRaw = trim((string)($row['created_at'] ?? ''));
        $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
        $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';
    ?>
        <tr>
          <td rowspan="1"><?= $i + 1; ?></td>
          <td rowspan="1"><?= $row['name'] ?: '-'; ?></td>
          <td rowspan="1"><?= ($row['shift'] && $row['shift'] !== '-') ? ($row['shift'] . ' / ' . $row['group']) : '-'; ?></td>
          <td rowspan="1"><?= $hasOpFinish ? ($name2Val !== '' && $name2Val !== '-' ? $name2Val : $emp2Val) : '-'; ?></td>
          <td rowspan="1"><?= ($hasOpFinish && $shift2Val !== '' && $shift2Val !== '-') ? ($shift2Val . ' / ' . $group2Val) : '-'; ?></td>
          <td rowspan="1"><?= (string)($row['machno'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['model'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['lotno'] ?? '-'); ?></td>
          <td rowspan="1"><?= $dateStr; ?></td>
          <td rowspan="1"><?= (string)($row['par013'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par014'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par015'] ?? '-'); ?></td>
          <td rowspan="1"><?= $timeInStr; ?></td>
          <td rowspan="1"><?= $timeOutStr; ?></td>
          <td rowspan="1"><?= (string)($row['par035'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par036'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par037'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par038'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par039'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par040'] ?? '-'); ?></td>
          <td rowspan="1"><?= (!empty($row['std_die_bond']) && $row['std_die_bond'] !== '-') ? $row['std_die_bond'] . ' N' : '-'; ?></td>
          <td rowspan="1"><?= (string)($row['par017'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par018'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par041'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par042'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par043'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par002'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par003'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par004'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par005'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par006'] ?? '-'); ?></td>
          <td rowspan="1"><?= (string)($row['par007'] ?? '-'); ?></td>
        </tr>
    <?php } ?>
    </tbody>
  </table>
</div>