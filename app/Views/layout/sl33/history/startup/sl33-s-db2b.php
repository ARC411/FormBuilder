<?php
/* ============================================================
 * Helper (identik dengan sl56 + guard, aman bila di-include bersama)
 * ============================================================ */
if (!function_exists('renderOkNg')) {
    function renderOkNg($val) {
        $valStr = strtoupper(trim((string)$val));
        if ($valStr === 'OK' || $val === '✔' || $valStr === '✔' || $valStr === 'V' || $valStr === '√') return '<strong>&#10004;</strong>';
        if ($valStr === 'NG' || $val === '✖' || $valStr === '✖' || $valStr === 'X') return '<strong>&#10008;</strong>';
        return $val !== '' ? $val : '-';
    }
}

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

if (!function_exists('sl33WorkDate')) {
    function sl33WorkDate($row) {
        if (function_exists('getRecordWorkDate')) {
            return getRecordWorkDate($row, 'd/m/Y');
        }
        return date('d/m/Y', strtotime((string)($row['created_at'] ?? 'now')));
    }
}

if (!function_exists('sl33HistCells')) {
    // Cetak satu <td> per record untuk parameter $param
    //  mode 'okng' : tampil ✔ / ✖
    //  mode 'val'  : tampil apa adanya ("0.42" untuk satuan tunggal, "0.8 N" untuk satuan ganda)
    function sl33HistCells($alldata, $lenData, $param, $mode = 'val') {
        for ($i = 0; $i < $lenData; $i++) {
            $v = $alldata[$i][$param] ?? '';
            if ($mode === 'okng') {
                echo '<td>' . renderOkNg($v) . '</td>';
            } else {
                $t = trim((string)$v);
                echo '<td>' . ($t !== '' ? esc($t) : '-') . '</td>';
            }
        }
    }
}

$lenData = (isset($alldata) && is_array($alldata)) ? count($alldata) : 0;
?>
<!-- sl33 history v2 (pola sl56: Judgment / Operator emp id / Checked / Note) -->
<div class="table-responsive mt-3" id="sl33-s-db2b">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
        <tr>
            <th colspan="<?= 4 + max($lenData, 1) ?>" scope="col">LD Die Bonding 2 Auto Machine<br>(Single Laser 3.3)</th>
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
            <?php for ($i = 0; $i < $lenData; $i++) { ?>
                <td><?= sl33WorkDate($alldata[$i]); ?></td>
            <?php } ?>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>1</td>
            <td colspan="2">@ Transfer Air Pressure</td>
            <td>0.34 ~ 0.49 MPa</td>
            <?php sl33HistCells($alldata, $lenData, 'par001'); ?>
        </tr>

        <tr>
            <td>2</td>
            <td colspan="2">@ Chip-clean Air Pressure</td>
            <td>0.06 ~ 0.15 MPa</td>
            <?php sl33HistCells($alldata, $lenData, 'par002'); ?>
        </tr>

        <tr>
            <td>3</td>
            <td colspan="2">@ Stem-clean Air Pressure</td>
            <td>0.34 ~ 0.49 MPa</td>
            <?php sl33HistCells($alldata, $lenData, 'par003'); ?>
        </tr>

        <tr>
            <td>4</td>
            <td colspan="2">@ Chip Vacuum Pressure</td>
            <td>-80 kPa atau kurang</td>
            <?php sl33HistCells($alldata, $lenData, 'par004'); ?>
        </tr>

        <tr>
            <td>5</td>
            <td colspan="2">@ Stem Vacuum Pressure</td>
            <td>-70 kPa atau kurang</td>
            <?php sl33HistCells($alldata, $lenData, 'par005'); ?>
        </tr>

        <tr>
            <td rowspan="3">6</td>
            <td rowspan="3" colspan="2">* Bersihkan Collet</td>
            <td>Collet A</td>
            <?php sl33HistCells($alldata, $lenData, 'par006', 'okng'); ?>
        </tr>

        <tr>
            <td>Collet B</td>
            <?php sl33HistCells($alldata, $lenData, 'par007', 'okng'); ?>
        </tr>

        <tr>
            <td>Middle Collet</td>
            <?php sl33HistCells($alldata, $lenData, 'par008', 'okng'); ?>
        </tr>

        <tr>
            <td>7</td>
            <td colspan="2">* Cek kondisi collet guide</td>
            <td>Cek kondisi collet guide goyang atau tidak</td>
            <?php sl33HistCells($alldata, $lenData, 'par009', 'okng'); ?>
        </tr>

        <tr>
            <td rowspan="2">8</td>
            <td rowspan="2" colspan="2">@ Colled Load</td>
            <td>Collet A : 0.6 ~ 1.0 N (60 gr ~ 100 gr)</td>
            <?php sl33HistCells($alldata, $lenData, 'par010'); ?>
        </tr>

        <tr>
            <td>Collet B : 0.6 ~ 1.4 N (60 gr ~ 140 gr)</td>
            <?php sl33HistCells($alldata, $lenData, 'par011'); ?>
        </tr>

        <tr>
            <td>9</td>
            <td colspan="2">@ Cek Needle Push Up Level</td>
            <td>0.20 ~ 0.60 mm</td>
            <?php sl33HistCells($alldata, $lenData, 'par012'); ?>
        </tr>

        <tr>
            <td>10</td>
            <td colspan="2">* Cek Posisi die bonding pada monitor</td>
            <td>Pergeseran LD maksimal 2 mm dari cursor</td>
            <?php sl33HistCells($alldata, $lenData, 'par013', 'okng'); ?>
        </tr>

        <tr>
            <td rowspan="6">11</td>
            <td rowspan="6">Ag Paste Dispenser</td>
            <td rowspan="3">Digital</td>
            <td>@ Air Pressure : 0.005 ~ 0.1 MPa (5 ~ 100 kPa)</td>
            <?php sl33HistCells($alldata, $lenData, 'par014'); ?>
        </tr>

        <tr>
            <td>@ Dispense Time : 0.005 ~ 0.15 s (5 ~ 150 ms)</td>
            <?php sl33HistCells($alldata, $lenData, 'par015'); ?>
        </tr>

        <tr>
            <td>@ Suction force : -0.3 ~ -0.1 kPa (-3 ~ -1 mmHg)</td>
            <?php sl33HistCells($alldata, $lenData, 'par016'); ?>
        </tr>

        <tr>
            <td rowspan="3">Analog</td>
            <td>@ Air Pressure : 0.01 ~ 0.10 MPa (10 ~ 100 kPa)</td>
            <?php sl33HistCells($alldata, $lenData, 'par017'); ?>
        </tr>

        <tr>
            <td>@ Dispense Time : 0.01 ~ 0.15 s (10 ~ 150 ms)</td>
            <?php sl33HistCells($alldata, $lenData, 'par018'); ?>
        </tr>

        <tr>
            <td>* Suction force : kurang dari 1/6 dari posisi closed <br> (putaran berlawanan arah jarum jam)</td>
            <?php sl33HistCells($alldata, $lenData, 'par019', 'okng'); ?>
        </tr>

        <tr>
            <td rowspan="2">12</td>
            <td rowspan="2" colspan="2">Ag Paste Condition</td>
            <td>* Posisi</td>
            <?php sl33HistCells($alldata, $lenData, 'par020', 'okng'); ?>
        </tr>

        <tr>
            <td>@ Quantity (Minimal Ag paste terlihat 3 dari sisi)</td>
            <?php sl33HistCells($alldata, $lenData, 'par021', 'okng'); ?>
        </tr>

        <tr>
            <td rowspan="2">13</td>
            <td rowspan="2" colspan="2">* Direction Ion Blower Fan</td>
            <td>Posisi blower</td>
            <?php sl33HistCells($alldata, $lenData, 'par022', 'okng'); ?>
        </tr>

        <tr>
            <td>Putaran blower</td>
            <?php sl33HistCells($alldata, $lenData, 'par023', 'okng'); ?>
        </tr>

        <tr>
            <td>14</td>
            <td colspan="2">* Cek Stem Nozzle Condition</td>
            <td>Tidak ada gompal atau crack pada stem nozzle</td>
            <?php sl33HistCells($alldata, $lenData, 'par024', 'okng'); ?>
        </tr>

        <tr>
            <td rowspan="3">15</td>
            <td rowspan="3">@ Bonding Offset Parameter</td>
            <td>X</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <?php sl33HistCells($alldata, $lenData, 'par025'); ?>
        </tr>

        <tr>
            <td>Y</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <?php sl33HistCells($alldata, $lenData, 'par026'); ?>
        </tr>

        <tr>
            <td>θ</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <?php sl33HistCells($alldata, $lenData, 'par027'); ?>
        </tr>

        <tr>
            <td>16</td>
            <td colspan="2">* Cleaning Machine</td>
            <td>Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
            <?php sl33HistCells($alldata, $lenData, 'par028', 'okng'); ?>
        </tr>

        <tr>
            <td colspan="4">Judgment Start Up *</td>
            <?php for ($i = 0; $i < $lenData; $i++) {
                $judg = $alldata[$i]['par030'] ?? '';
                if ($judg === '') {
                    // Fallback untuk record lama yang belum punya judgment: cek kolom OK/NG
                    $chkPars = ['par006', 'par007', 'par008', 'par009', 'par013', 'par019', 'par020', 'par021', 'par022', 'par023', 'par024', 'par028'];
                    $hasNg = false;
                    foreach ($chkPars as $cp) {
                        $v = strtoupper(trim((string)($alldata[$i][$cp] ?? '')));
                        if ($v === 'NG' || $v === '✖' || $v === 'X') { $hasNg = true; break; }
                    }
                    $judg = $hasNg ? '✖' : '✔';
                }
            ?>
                <td><?= renderOkNg($judg); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Operator</td>
            <?php for ($i = 0; $i < $lenData; $i++) {
                $r = $alldata[$i];
                if (!empty($r['empid'])) {
                    $opEmp = resolveStartupEmpId($r['empid']);
                } elseif (!empty($r['name'])) {
                    $opEmp = resolveStartupEmpId($r['name']);
                } elseif (function_exists('getOperatorName')) {
                    // getOperatorName mengembalikan NAMA -> petakan ke emp id (tbempinfa)
                    $opEmp = resolveStartupEmpId(getOperatorName($r));
                } else {
                    $opEmp = '-';
                }
            ?>
                <td><?= esc($opEmp); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Checked</td>
            <?php for ($i = 0; $i < $lenData; $i++) { ?>
                <td><?= esc(formatStartupCheckedEmpId($alldata[$i] ?? null)); ?></td>
            <?php } ?>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <?php sl33HistCells($alldata, $lenData, 'par029'); ?>
        </tr>
    </tbody>
</table>
</div>