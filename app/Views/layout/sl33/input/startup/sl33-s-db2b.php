<?php
/* ============================================================
 * Helper khusus form sl33 (di-guard supaya aman di-include berulang)
 * Aturan penyimpanan:
 *  - Satuan tunggal  -> DB hanya angka polos (mis. "0.42")
 *  - Satuan > 1      -> DB "nilai satuan" (mis. "0.8 N")
 *  - OK/NG           -> "✔" / "✖"
 * ============================================================ */
if (!function_exists('sl33NormOkNg')) {
    // Normalisasi data lama (V/X/OK/NG/√) ke ✔ / ✖
    function sl33NormOkNg($v) {
        $s = strtoupper(trim((string)$v));
        if (in_array($s, ['OK', 'V', '✔', '√'], true)) return '✔';
        if (in_array($s, ['NG', 'X', '✖'], true)) return '✖';
        return '';
    }
}

if (!function_exists('sl33OkNgSelect')) {
    function sl33OkNgSelect($id, $row, $extra = '') {
        $cur = sl33NormOkNg($row[$id] ?? '');
        echo '<select class="form-control sl33-okng" id="' . $id . '" name="' . $id . '" ' . $extra . '>';
        echo '<option value="">-- Pilih --</option>';
        echo '<option value="✔"' . ($cur === '✔' ? ' selected' : '') . '>✔ (OK)</option>';
        echo '<option value="✖"' . ($cur === '✖' ? ' selected' : '') . '>✖ (NG)</option>';
        echo '</select>';
    }
}

if (!function_exists('sl33NumInput')) {
    // Input angka dengan satuan TUNGGAL: satuan hanya label, tidak masuk DB
    function sl33NumInput($id, $unit, $min, $max, $row) {
        $val = htmlspecialchars((string)($row[$id] ?? ''), ENT_QUOTES);
        echo '<div class="input-group">';
        echo '<input class="form-control" type="number" step="any" data-min="' . $min . '" data-max="' . $max . '"'
           . ' id="' . $id . '" name="' . $id . '" value="' . $val . '"'
           . ' onchange="changeRange(\'' . $id . '\',' . $min . ',' . $max . ')">';
        echo '<span class="input-group-text">' . $unit . '</span>';
        echo '</div>';
    }
}

if (!function_exists('sl33SplitValUnit')) {
    function sl33SplitValUnit($raw) {
        $raw = trim((string)$raw);
        if ($raw === '') return ['val' => '', 'unit' => ''];
        $parts = preg_split('/\s+/', $raw, 2);
        return ['val' => $parts[0], 'unit' => $parts[1] ?? ''];
    }
}

if (!function_exists('sl33InferUnit')) {
    // Data lama tidak punya satuan -> tebak dari range mana yang paling cocok
    function sl33InferUnit($val, $units) {
        $keys = array_keys($units);
        if ($val === '' || !is_numeric($val)) return $keys[0];
        $v = (float)$val;
        $best = $keys[0];
        $bestDist = INF;
        foreach ($units as $u => $r) {
            $min = (float)$r[0];
            $max = (float)$r[1];
            $width = max(abs($max - $min), 1e-9);
            $dist = ($v < $min) ? ($min - $v) / $width : (($v > $max) ? ($v - $max) / $width : 0);
            if ($dist < $bestDist) {
                $bestDist = $dist;
                $best = $u;
            }
        }
        return $best;
    }
}

if (!function_exists('sl33UnitInput')) {
    // Input angka + dropdown satuan (satuan > 1). Pola sama dengan sl56:
    // hidden parXXX (gabungan), parXXX_val, parXXX_unit
    function sl33UnitInput($id, $units, $row, $pair = null) {
        $p = sl33SplitValUnit($row[$id] ?? '');
        $sel = ($p['unit'] !== '' && isset($units[$p['unit']])) ? $p['unit'] : sl33InferUnit($p['val'], $units);
        $hidden = ($p['val'] !== '') ? $p['val'] . ' ' . $sel : '';
        $pairJs = $pair ? " togglePairLock('{$pair[0]}','{$pair[1]}');" : '';

        echo '<div class="input-group">';
        echo '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . htmlspecialchars($hidden, ENT_QUOTES) . '">';
        echo '<input class="form-control" type="number" step="any"'
           . ' data-min="' . $units[$sel][0] . '" data-max="' . $units[$sel][1] . '"'
           . ' id="' . $id . '_val" name="' . $id . '_val" value="' . htmlspecialchars($p['val'], ENT_QUOTES) . '"'
           . ' oninput="combineData(\'' . $id . '\');' . $pairJs . '"'
           . ' onchange="combineData(\'' . $id . '\');' . $pairJs . " validateDynamicUnit('" . $id . '\')">';
        echo '<select class="form-control" id="' . $id . '_unit" name="' . $id . '_unit"'
           . ' onchange="combineData(\'' . $id . '\');' . $pairJs . " validateDynamicUnit('" . $id . "')\""
           . ' style="max-width: 90px; padding: 0.375rem;">';
        foreach ($units as $u => $r) {
            echo '<option value="' . $u . '" data-min="' . $r[0] . '" data-max="' . $r[1] . '"'
               . ($u === $sel ? ' selected' : '') . '>' . $u . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }
}

$row = (isset($alldata) && !empty($alldata[0])) ? $alldata[0] : [];

// Konfigurasi satuan ganda [min, max] per satuan
$U_COLLET_A = ['N' => ['0.60', '1.00'], 'gr' => ['60', '100']];
$U_COLLET_B = ['N' => ['0.60', '1.40'], 'gr' => ['60', '140']];
$U_DIG_AIR  = ['MPa' => ['0.005', '0.1'], 'kPa' => ['5', '100']];
$U_DIG_TIME = ['s' => ['0.005', '0.15'], 'ms' => ['5', '150']];
$U_DIG_SUC  = ['kPa' => ['-0.3', '-0.1'], 'mmHg' => ['-3', '-1']];
$U_ANA_AIR  = ['MPa' => ['0.01', '0.10'], 'kPa' => ['10', '100']];
$U_ANA_TIME = ['s' => ['0.01', '0.15'], 'ms' => ['10', '150']];
$U_OFFSET   = ['dec' => ['-20', '20'], 'raw' => ['65516', '65556']];
?>
<div class="table-responsive" id="sl33-s-db2b">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value="<?= isset($alldata) ? htmlspecialchars((string)($number ?? ''), ENT_QUOTES) : '' ?>">
    <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="5" scope="col">LD Die Bonding 2 Auto Machine<br>(Single Laser 3.3)</th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col">No</th>
            <th colspan="2" scope="col">Start up Check Item</th>
            <th scope="col">Standard</th>
            <th scope="col">Hasil Start Up Check</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>1</td>
            <td colspan="2">@ Transfer Air Pressure</td>
            <td>0.34 ~ 0.49 MPa</td>
            <td><?php sl33NumInput('par001', 'MPa', '0.34', '0.49', $row); ?></td>
        </tr>

        <tr>
            <td>2</td>
            <td colspan="2">@ Chip-clean Air Pressure</td>
            <td>0.06 ~ 0.15 MPa</td>
            <td><?php sl33NumInput('par002', 'MPa', '0.06', '0.15', $row); ?></td>
        </tr>

        <tr>
            <td>3</td>
            <td colspan="2">@ Stem-clean Air Pressure</td>
            <td>0.34 ~ 0.49 MPa</td>
            <td><?php sl33NumInput('par003', 'MPa', '0.34', '0.49', $row); ?></td>
        </tr>

        <tr>
            <td>4</td>
            <td colspan="2">@ Chip Vacuum Pressure</td>
            <td>-80 kPa atau kurang</td>
            <td><?php sl33NumInput('par004', 'kPa', '-99999999999', '-80', $row); ?></td>
        </tr>

        <tr>
            <td>5</td>
            <td colspan="2">@ Stem Vacuum Pressure</td>
            <td>-70 kPa atau kurang</td>
            <td><?php sl33NumInput('par005', 'kPa', '-99999999999', '-70', $row); ?></td>
        </tr>

        <tr>
            <td rowspan="3">6</td>
            <td rowspan="3" colspan="2">* Bersihkan Collet</td>
            <td>Collet A</td>
            <td><?php sl33OkNgSelect('par006', $row); ?></td>
        </tr>

        <tr>
            <td>Collet B</td>
            <td><?php sl33OkNgSelect('par007', $row); ?></td>
        </tr>

        <tr>
            <td>Middle Collet</td>
            <td><?php sl33OkNgSelect('par008', $row); ?></td>
        </tr>

        <tr>
            <td>7</td>
            <td colspan="2">* Cek kondisi collet guide</td>
            <td>Cek kondisi collet guide goyang atau tidak</td>
            <td><?php sl33OkNgSelect('par009', $row); ?></td>
        </tr>

        <tr>
            <td rowspan="2">8</td>
            <td rowspan="2" colspan="2">@ Colled Load</td>
            <td>Collet A : 0.6 ~ 1.0 N (60 gr ~ 100 gr)</td>
            <td><?php sl33UnitInput('par010', $U_COLLET_A, $row); ?></td>
        </tr>

        <tr>
            <td>Collet B : 0.6 ~ 1.4 N (60 gr ~ 140 gr)</td>
            <td><?php sl33UnitInput('par011', $U_COLLET_B, $row); ?></td>
        </tr>

        <tr>
            <td>9</td>
            <td colspan="2">@ Cek Needle Push Up Level</td>
            <td>0.20 ~ 0.60 mm</td>
            <td><?php sl33NumInput('par012', 'mm', '0.2', '0.6', $row); ?></td>
        </tr>

        <tr>
            <td>10</td>
            <td colspan="2">* Cek Posisi die bonding pada monitor</td>
            <td>Pergeseran LD maksimal 2 mm dari cursor</td>
            <td><?php sl33OkNgSelect('par013', $row); ?></td>
        </tr>

        <tr>
            <td rowspan="6">11</td>
            <td rowspan="6">Ag Paste Dispenser</td>
            <td rowspan="3">Digital</td>
            <td>@ Air Pressure : 0.005 ~ 0.1 MPa (5 ~ 100 kPa)</td>
            <td><?php sl33UnitInput('par014', $U_DIG_AIR, $row, ['par014', 'par017']); ?></td>
        </tr>

        <tr>
            <td>@ Dispense Time : 0.005 ~ 0.15 s (5 ~ 150 ms)</td>
            <td><?php sl33UnitInput('par015', $U_DIG_TIME, $row, ['par015', 'par018']); ?></td>
        </tr>

        <tr>
            <td>@ Suction force : -0.3 ~ -0.1 kPa (-3 ~ -1 mmHg)</td>
            <td><?php sl33UnitInput('par016', $U_DIG_SUC, $row, ['par019', 'par016']); ?></td>
        </tr>

        <tr>
            <td rowspan="3">Analog</td>
            <td>@ Air Pressure : 0.01 ~ 0.10 MPa (10 ~ 100 kPa)</td>
            <td><?php sl33UnitInput('par017', $U_ANA_AIR, $row, ['par014', 'par017']); ?></td>
        </tr>

        <tr>
            <td>@ Dispense Time : 0.01 ~ 0.15 s (10 ~ 150 ms)</td>
            <td><?php sl33UnitInput('par018', $U_ANA_TIME, $row, ['par015', 'par018']); ?></td>
        </tr>

        <tr>
            <td>* Suction force : kurang dari 1/6 dari posisi closed <br> (putaran berlawanan arah jarum jam)</td>
            <td><?php sl33OkNgSelect('par019', $row, 'data-pair="1" onchange="togglePairLock(\'par019\',\'par016\')"'); ?></td>
        </tr>

        <tr>
            <td rowspan="2">12</td>
            <td rowspan="2" colspan="2">Ag Paste Condition</td>
            <td>* Posisi</td>
            <td><?php sl33OkNgSelect('par020', $row); ?></td>
        </tr>

        <tr>
            <td>@ Quantity (Minimal Ag paste terlihat 3 dari sisi)</td>
            <td><?php sl33OkNgSelect('par021', $row); ?></td>
        </tr>

        <tr>
            <td rowspan="2">13</td>
            <td rowspan="2" colspan="2">* Direction Ion Blower Fan</td>
            <td>Posisi blower</td>
            <td><?php sl33OkNgSelect('par022', $row); ?></td>
        </tr>

        <tr>
            <td>Putaran blower</td>
            <td><?php sl33OkNgSelect('par023', $row); ?></td>
        </tr>

        <tr>
            <td>14</td>
            <td colspan="2">* Cek Stem Nozzle Condition</td>
            <td>Tidak ada gompal atau crack pada stem nozzle</td>
            <td><?php sl33OkNgSelect('par024', $row); ?></td>
        </tr>

        <tr>
            <td rowspan="3">15</td>
            <td rowspan="3">@ Bonding Offset Parameter</td>
            <td>X</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <td><?php sl33UnitInput('par025', $U_OFFSET, $row); ?></td>
        </tr>

        <tr>
            <td>Y</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <td><?php sl33UnitInput('par026', $U_OFFSET, $row); ?></td>
        </tr>

        <tr>
            <td>θ</td>
            <td>-20 ~ 20 (65516 ~ 65556)</td>
            <td><?php sl33UnitInput('par027', $U_OFFSET, $row); ?></td>
        </tr>

        <tr>
            <td>16</td>
            <td colspan="2">* Cleaning Machine</td>
            <td>Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
            <td><?php sl33OkNgSelect('par028', $row); ?></td>
        </tr>

        <tr>
            <td colspan="4">Judgment Start Up *</td>
            <td>
                <input class="form-control text-center font-weight-bold" type="text" id="par030" name="par030" readonly placeholder="✔ / ✖"
                       style="font-size: 1.25rem; font-weight: bold;"
                       value="<?= htmlspecialchars((string)($row['par030'] ?? ''), ENT_QUOTES) ?>">
            </td>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <td>
                <input class="form-control" type="text" placeholder="Tambahkan catatan jika ada" id="par029" name="par029"
                       value="<?= htmlspecialchars((string)($row['par029'] ?? ''), ENT_QUOTES) ?>">
            </td>
        </tr>
    </tbody>
    </table>

    <div class="note mt-3">
        <h6><b>Keterangan:</b></h6>
        <p class="mb-1 small">@ : Isi kolom dengan nilai (value) di mesin.</p>
        <p class="mb-1 small">* : Pilih ✔ jika hasil sesuai standar (OK), atau ✖ jika tidak sesuai standar (NG).</p>
        <p class="mb-1 small">Isi bagian Note apabila melakukan penggantian part, adjustment, atau terjadi kondisi yang tidak sesuai standard.</p>
    </div>
</div>

<script>
(function () {
    var C = document.getElementById('sl33-s-db2b');
    var J = document.getElementById('par030');
    function $(id) { return document.getElementById(id); }
    function active(el) { return el && !el.disabled; }
    function filled(el) {
        if (!active(el)) return false;
        var v = (el.value || '').trim();
        return v !== '' && v !== '-';
    }
    function outOfRange(el, min, max) {
        var v = parseFloat(el.value);
        if (isNaN(v)) return false;
        return v < min || v > max;
    }

    // Input angka satuan tunggal: range dari data-min / data-max
    function singleState(id) {
        var el = $(id), f = filled(el), ng = false;
        if (f) ng = outOfRange(el, parseFloat(el.dataset.min), parseFloat(el.dataset.max));
        return { f: f, ng: ng };
    }
    // Input angka satuan ganda: range dari option satuan yang sedang dipilih
    function unitState(id) {
        var el = $(id + '_val'), u = $(id + '_unit'), f = filled(el), ng = false;
        if (f && u) {
            var o = u.options[u.selectedIndex];
            ng = outOfRange(el, parseFloat(o.dataset.min), parseFloat(o.dataset.max));
        }
        return { f: f, ng: ng };
    }
    function selState(id) {
        var el = $(id);
        var f = !!active(el) && (el.value || '').trim() !== '';
        return { f: f, ng: f && el.value === '✖' };
    }

    function paint(text, bg, color, border) {
        J.value = text;
        J.style.backgroundColor = bg;
        J.style.color = color;
        J.style.borderColor = border;
    }

    function evaluate() {
        if (!J) return;

        var sb = $('is-standby');
        if (sb && sb.checked) { paint('-', '#e9ecef', '#495057', '#ced4da'); return; }

        var hasNg = false, allFilled = true;
        function take(s) { if (s.ng) hasNg = true; if (!s.f) allFilled = false; }
        function either(a, b) {            // salah satu dari pasangan cukup terisi
            if (!a.f && !b.f) allFilled = false;
            if (a.ng || b.ng) hasNg = true;
        }

        ['par001', 'par002', 'par003', 'par004', 'par005', 'par012'].forEach(function (id) { take(singleState(id)); });
        ['par010', 'par011', 'par025', 'par026', 'par027'].forEach(function (id) { take(unitState(id)); });

        // Select OK/NG (kecuali par019 yang berpasangan dengan par016)
        C.querySelectorAll('select.sl33-okng:not([data-pair])').forEach(function (sel) { take(selState(sel.id)); });

        // Digital <-> Analog: mesin hanya punya salah satu
        either(unitState('par014'), unitState('par017'));
        either(unitState('par015'), unitState('par018'));
        either(selState('par019'), unitState('par016'));

        if (hasNg)          paint('✖', '#f8d7da', '#842029', '#f5c2c7');
        else if (allFilled) paint('✔', '#d1e7dd', '#0f5132', '#badbcc');
        else                paint('', '', '', '');
    }
    window.evaluateStartupJudgmentSl33 = evaluate;

    if (typeof togglePairLock === 'function') {
        togglePairLock('par014', 'par017');
        togglePairLock('par015', 'par018');
        togglePairLock('par019', 'par016');
    }

    if (C) {
        C.addEventListener('input',  function () { setTimeout(evaluate, 40); });
        C.addEventListener('change', function () { setTimeout(evaluate, 40); });
    }
    setTimeout(evaluate, 100);

    var formEl = C ? C.closest('form') : null;
    if (formEl) formEl.addEventListener('submit', evaluate, true);
})();
</script>