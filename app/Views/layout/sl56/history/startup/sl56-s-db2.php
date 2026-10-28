<?php
if (!function_exists('resolveStartupEmpId')) {
    function resolveStartupEmpId($val) {
        static $empMap = null;
        $clean = trim((string)$val);
        if ($clean === '' || $clean === '-') {
            return '-';
        }
        if (preg_match('/^[0-9A-Za-z_-]{3,12}$/', $clean) && preg_match('/\d/', $clean)) {
            return $clean;
        }
        if ($empMap === null) {
            $empMap = [];
            try {
                $db2 = \Config\Database::connect('second');
                $rows = $db2->query("SELECT empid, firstname, midname, lastname FROM tbempinfa")->getResultArray();
                foreach ($rows as $r) {
                    $eid = trim((string)($r['empid'] ?? ''));
                    if ($eid === '') continue;
                    $full = strtoupper(trim(preg_replace('/\s+/', ' ', ($r['firstname'] ?? '') . ' ' . ($r['midname'] ?? '') . ' ' . ($r['lastname'] ?? ''))));
                    if ($full !== '') $empMap[$full] = $eid;
                    $fl = strtoupper(trim(preg_replace('/\s+/', ' ', ($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? ''))));
                    if ($fl !== '' && !isset($empMap[$fl])) $empMap[$fl] = $eid;
                }
            } catch (\Throwable $e) {
                // Abaikan jika gagal koneksi
            }
        }
        $key = strtoupper((string)preg_replace('/\s+/', ' ', $clean));
        return $empMap[$key] ?? $clean;
    }
}

if (!function_exists('formatStartupCheckedEmpId')) {
    function formatStartupCheckedEmpId($row) {
        if (!is_array($row)) return '-';
        $rawStatus = strtolower(trim((string)($row['status'] ?? '')));
        if ($rawStatus === '' || $rawStatus === '-' || (strpos($rawStatus, 'approve') === false && strpos($rawStatus, 'bypass') === false && strpos($rawStatus, 'check') === false)) {
            return '-';
        }
        $spv = trim((string)($row['supervisor'] ?? ''));
        $ldr = trim((string)($row['leader'] ?? ''));
        $frm = trim((string)($row['foreman'] ?? ''));
        if ($spv !== '' && $spv !== '-') {
            return resolveStartupEmpId($spv) . ' (Supervisor)';
        }
        if ($ldr !== '' && $ldr !== '-') {
            return resolveStartupEmpId($ldr) . ' (Leader)';
        }
        if ($frm !== '' && $frm !== '-') {
            return resolveStartupEmpId($frm) . ' (Foreman)';
        }
        return '-';
    }
}
?>
<div class="table-responsive mt-3" id="sl56-s-db2">
<?= csrf_field(); ?>
<?php $lenData = is_array($alldata) ? count($alldata) : 0; ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="<?= 4 + $lenData ?>" scope="col"><?php echo nl2br("LD Die Bonding 2")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" rowspan="2">No</th>
            <th colspan="2" rowspan="2" scope="col">Start up Check Item</th>
            <th scope="col" rowspan="2">Standard</th>
            <th scope="col" colspan="<?= $lenData > 0 ? $lenData : 1 ?>">Hasil Start Up Check</th>
        </tr>

        <!-- BARIS TANGGAL -->
        <tr>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td><?= !empty($alldata[$i]['created_at']) ? date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])) : '-'; ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td rowspan="2">1</td>
            <td rowspan="2" colspan="2">@ Transfer Air Pressure</td>
            <td rowspan="1">A: 340-490 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par001'] ?? ''); ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="1">B: 0.34-0.49 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par002'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="4">2</td>
            <td rowspan="4">@ Chip-clean Air Pressure</td>
            <td rowspan="2">Chip</td>
            <td>A: 60 - 150 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par003'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>B: 0.06 - 0.150 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par004'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Stem</td>
            <td>A: 340 - 490 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par005'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>B: 0.340 - 0.490 MPa</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par006'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="4">3</td>
            <td rowspan="4">@ Vacuum Pressure</td>
            <td rowspan="2">Chip</td>
            <td>A: -80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par007'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>B: -80 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par008'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Stem</td>
            <td>A: -70 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par009'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>B: -70 kPa atau kurang</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par010'] ?? ''); ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td rowspan="3">4</td>
            <td rowspan="3" colspan="2">* Collet Cleaning <br> (Bersihkan dengan Cotton Bud yang dibasahi dengan alkohol)</td>
            <td>Collet A</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par011'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>Collet B</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par012'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>Middle Collet</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par013'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>5</td>
            <td colspan="3">* Pengecekan kondisi collet guide (Check apakah collet guide goyang)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par014'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">6</td>
            <td rowspan="2" colspan="2">@Colled Load</td>
            <td>Collet A : 0.60 - 1.00 N (60-100 gr)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par015'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>Collet B : 0.60 - 1.40 N (60-140 gr)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par016'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>7</td>
            <td colspan="3">* Pengecekan die bonding monitor <br>(Check posisi cursor pada LD chip dengan melihat monitor)</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par017'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="6">8</td>
            <td rowspan="6">@ Dispenser indication</td>
            <td rowspan="2">Air Pressure</td>
            <td>Analog : 0.01-0.10 MPa (10-100 kPa) / 0.10 - 1 kgf/cm^2</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par018'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td>Digital : 0.005-0.10 MPa (5-100 kPa) / 0.05 - 1 kgf/cm^2</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par019'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Dispense Time</td>
            <td>Analog : 0.01-0.15 sec / 10-150 ms</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par020'] ?? ''); ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td>Digital : 0.005-0.15 sec / 5-150 ms</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par021'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">Suction Pressure Force</td>
            <td>Analog : kurang dari 1/6 dari posisi Off</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par022'] ?? ''); ?></td>
            <?php } ?>
        </tr>
        
        <tr>
            <td>Digital : -0.3 ~ -0.1 kPa / -0.03 ~ -0.01 kgf/cm^2 / -3 ~ -1 mmHg</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par023'] ?? ''); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td rowspan="2">9</td>
            <td rowspan="2" colspan="2">* Check kondisi Ag Paste</td>
            <td>Posisi</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par024'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td>Quantity</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par025'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">* Check arah Ion Blower Fan</td>
            <td>Posisi blower</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par026'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td>Putaran blower</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par027'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td rowspan="1">11</td>
            <td rowspan="1" colspan="2">Cleaning Machine</td>
            <td>Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par028'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td rowspan="1">12</td>
            <td rowspan="1" colspan="2">@ Needle Push Up Level</td>
            <td>0.2 ~ 0.6 mm</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par029'] ?? ''); ?></td>
            <?php } ?>
        </tr> 

        <tr>
            <td colspan="4">Operator</td>
            <?php for($i = 0; $i < $lenData; $i++) { 
                $opEmp = !empty($alldata[$i]['empid']) ? resolveStartupEmpId($alldata[$i]['empid']) : resolveStartupEmpId($alldata[$i]['name'] ?? '-');
            ?>
                <td colspan="col"><?= esc($opEmp); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Checked</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= esc(formatStartupCheckedEmpId($alldata[$i] ?? null)); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <?php for($i = 0; $i < $lenData; $i++) { ?>
                <td colspan="col"><?= (string)($alldata[$i]['par030'] ?? ''); ?></td>
            <?php } ?>
        </tr>

    </tbody>   
</table>
</div>