<?php
// --- HELPER REKURSIF UNTUK HALAMAN INPUT OPERATOR ---
if (!function_exists('opCountLeaves')) {
    function opCountLeaves($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $count = 0;
        foreach ($node['children'] as $child) {
            $count += opCountLeaves($child, $ignoreGridChildren);
        }
        return $count;
    }
}

if (!function_exists('opGetSubtreeDepth')) {
    function opGetSubtreeDepth($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $max = 0;
        foreach ($node['children'] as $child) {
            $d = opGetSubtreeDepth($child, $ignoreGridChildren);
            if ($d > $max) $max = $d;
        }
        return $max + 1;
    }
}

if (!function_exists('opRenderActiveInput')) {
    function opRenderActiveInput($leaf, $kategori_form, $alldata = null, $rowIdx = 0) {
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));
        if ($colDb === '') {
            return '<span class="text-muted">-</span>';
        }

        // Ambil nilai lama jika sedang dalam mode Edit (mendukung par021a -> alldata[0]['par021'])
        $val = '';
        $baseCol = preg_replace('/[a-z]$/i', '', $colDb);
        if (is_array($alldata)) {
            if (isset($alldata[$rowIdx][$colDb])) {
                $val = $alldata[$rowIdx][$colDb];
            } elseif (isset($alldata[$rowIdx][$baseCol])) {
                $val = $alldata[$rowIdx][$baseCol];
            } elseif (isset($alldata[0][$colDb])) {
                $val = $alldata[0][$colDb];
            }
        }

        $min = ($leaf['batas_min'] !== null && $leaf['batas_min'] !== '') ? (float)$leaf['batas_min'] : null;
        $max = ($leaf['batas_max'] !== null && $leaf['batas_max'] !== '') ? (float)$leaf['batas_max'] : null;

        $html = '';

        // 1. TIPE DROPDOWN (OK / NG)
        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            if ($kategori_form === 'startup') {
                $selOk = ($val === '✔' || $val === 'OK') ? 'selected' : '';
                $selNg = ($val === '✖' || $val === 'NG') ? 'selected' : '';
                $html .= '<select class="form-control form-select" id="'.esc($colDb).'" name="'.esc($colDb).'">
                            <option value="">-- Pilih --</option>
                            <option value="✔" '.$selOk.'>✔ (OK)</option>
                            <option value="✖" '.$selNg.'>✖ (NG)</option>
                          </select>';
            } else {
                $selMin = ($val === '-' || $val === '') ? 'selected' : '';
                $selOk  = ($val === 'OK') ? 'selected' : '';
                $selNg  = ($val === 'NG') ? 'selected' : '';
                $html .= '<select class="form-select" id="'.esc($colDb).'" name="'.esc($colDb).'" style="min-width: 85px;">
                            <option value="-" '.$selMin.'>-</option>
                            <option value="OK" '.$selOk.'>OK</option>
                            <option value="NG" '.$selNg.'>NG</option>
                          </select>';
            }
        }
        // 2. TIPE TEXTAREA (CATATAN PANJANG)
        elseif ($leaf['tipe_input'] === 'textarea') {
            $html .= '<textarea class="form-control" id="'.esc($colDb).'" name="'.esc($colDb).'" rows="4" style="min-width: 160px;">'.esc($val).'</textarea>';
        }
        // 3. TIPE ANGKA (DENGAN VALIDASI MIN/MAX & AUTO-YIELD PRODUCTION)
        elseif ($leaf['tipe_input'] === 'number') {
            $extraEvents = [];
            $readonlyAttr = '';

            if ($kategori_form === 'production') {
                if ($colDb === 'par013' || $colDb === 'par014') {
                    $extraEvents[] = "if(typeof quantityChange1==='function') quantityChange1();";
                }
                if ($colDb === 'par017' || $colDb === 'par018') {
                    $extraEvents[] = "if(typeof ausnSLDB1Change==='function') ausnSLDB1Change();";
                }
                $paramNameLower = strtolower($leaf['nama_parameter'] ?? '');
                if (($colDb === 'par015' || $colDb === 'par016') && (strpos($paramNameLower, 'ng') !== false || strpos($paramNameLower, 'yield') !== false || strpos($paramNameLower, 'other') !== false)) {
                    $readonlyAttr = 'readonly';
                }
            }

            $dataMinAttr = ($min !== null) ? 'data-min="'.$min.'"' : '';
            $dataMaxAttr = ($max !== null) ? 'data-max="'.$max.'"' : '';
            if ($min !== null || $max !== null) {
                $jsMin = ($min !== null) ? $min : -999999999;
                $jsMax = ($max !== null) ? $max : 999999999;
                $extraEvents[] = "validateDynamicRange(this, {$jsMin}, {$jsMax});";
            }

            $eventStr = !empty($extraEvents) ? implode(' ', $extraEvents) : '';
            $onInputAttr = $eventStr ? 'oninput="'.$eventStr.'" onchange="'.$eventStr.'" onkeyup="'.$eventStr.'"' : '';

            $html .= '<input type="number" step="any" class="form-control text-center" style="min-width: 95px;" '
                   . 'id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '
                   . $dataMinAttr . ' ' . $dataMaxAttr . ' ' . $onInputAttr . ' ' . $readonlyAttr . '>';

            if ($min !== null || $max !== null) {
                $minLbl = ($min !== null) ? $min : '-∞';
                $maxLbl = ($max !== null) ? $max : '∞';
                $html .= '<small class="text-muted d-block mt-1" style="font-size:10px;">Std: '.$minLbl.' ~ '.$maxLbl.'</small>';
            }
        }
        // 4. TIPE TEKS BEBAS
        else {
            $html .= '<input type="text" class="form-control" style="min-width: 110px;" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'">';
        }

        return $html;
    }
}

if (!function_exists('opBuildStartupRows')) {
    function opBuildStartupRows($nodes, $currentCol, $maxDepth, &$verticalRows, &$rowIndex, $isRootLevel = false) {
        $no = 1;
        foreach ($nodes as $node) {
            if (!isset($verticalRows[$rowIndex])) $verticalRows[$rowIndex] = [];

            $rowspan  = opCountLeaves($node);
            $subDepth = opGetSubtreeDepth($node);
            $colspan  = max(1, $maxDepth - $currentCol - ($subDepth - 1));

            if ($isRootLevel) {
                $verticalRows[$rowIndex][] = [
                    'type'    => 'no',
                    'no'      => $no++,
                    'rowspan' => $rowspan
                ];
            }

            $verticalRows[$rowIndex][] = [
                'type'      => 'param',
                'name'      => $node['nama_parameter'],
                'std'       => $node['standar_nilai'] ?? null,
                'rowspan'   => $rowspan,
                'colspan'   => $colspan,
                'node_data' => $node
            ];

            if (empty($node['children'])) {
                $verticalRows[$rowIndex][] = [
                    'type'      => 'input',
                    'node_data' => $node
                ];
                $rowIndex++;
            } else {
                opBuildStartupRows($node['children'], $currentCol + $colspan, $maxDepth, $verticalRows, $rowIndex, false);
            }
        }
    }
}

if (!function_exists('opBuildHorizontalHeaders')) {
    function opBuildHorizontalHeaders($nodes, $level, &$headerRows, &$headerLeaves, $maxDepth) {
        if (!isset($headerRows[$level])) $headerRows[$level] = [];
        foreach ($nodes as $node) {
            $hasGridChildren = (!empty($node['children']) && !empty($node['children'][0]['posisi_grid']));
            $isHeaderLeaf    = (empty($node['children']) || $hasGridChildren);

            $colspan  = opCountLeaves($node, true);
            $subDepth = opGetSubtreeDepth($node, true);
            $rowspan  = max(1, ($maxDepth - $level) - ($subDepth - 1));

            $headerRows[$level][] = [
                'name'      => $node['nama_parameter'],
                'colspan'   => $colspan,
                'rowspan'   => $rowspan,
                'node_data' => $node
            ];

            if ($isHeaderLeaf) {
                $headerLeaves[] = $node;
            } else {
                opBuildHorizontalHeaders($node['children'], $level + $rowspan, $headerRows, $headerLeaves, $maxDepth);
            }
        }
    }
}

// Hitung cnt-proses otomatis (Deteksi apakah menggunakan akhiran a/b/c/d seperti sl56-f-fldb1 & sl56-f-fldb2)
$cntProses = 1;
$gridRowNames = [];
$firstGridLeafIdx = null;
$headerMaxDepth = 1;
$headerRows = [];
$headerLeaves = [];
$hasSuffixCols = false;

if ($kategori_form !== 'startup' && !empty($treeParams)) {
    $headerMaxDepth = 0;
    foreach ($treeParams as $node) {
        $d = opGetSubtreeDepth($node, true);
        if ($d > $headerMaxDepth) $headerMaxDepth = $d;
    }
    $headerMaxDepth = max(1, $headerMaxDepth);
    opBuildHorizontalHeaders($treeParams, 0, $headerRows, $headerLeaves, $headerMaxDepth);
    ksort($headerRows);

    foreach ($headerLeaves as $hIdx => $hLeaf) {
        if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
            if ($firstGridLeafIdx === null) $firstGridLeafIdx = $hIdx;
            foreach ($hLeaf['children'] as $gridChild) {
                if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $gridRowNames)) {
                    $gridRowNames[] = $gridChild['posisi_grid'];
                }
                if (!empty($gridChild['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $gridChild['kolom_db'])) {
                    $hasSuffixCols = true;
                }
            }
        }
    }
    if (!empty($gridRowNames) && $hasSuffixCols) {
        $cntProses = count($gridRowNames);
    }
}
?>

<div class="table-responsive" id="<?= esc($process_code) ?>">
    <?= csrf_field(); ?>
    <input type="hidden" id="cnt-proses" name="cnt-proses" value="<?= $cntProses ?>">
    <input type="hidden" id="revision_id" name="revision_id" value="<?= esc($revision['id']) ?>">
    <?php if (isset($alldata) && isset($number)): ?>
        <input type="hidden" id="number-edit" name="number-edit" value="<?= esc($number) ?>">
    <?php endif; ?>

    <?php if (empty($treeParams)): ?>
        <div class="alert alert-warning text-center my-3">
            Form untuk proses <b><?= strtoupper(esc($process_code)) ?></b> belum memiliki parameter.
        </div>

    <?php elseif ($kategori_form === 'startup'): ?>
        <?php
        $verticalRows = [];
        $rowIndex = 0;
        opBuildStartupRows($treeParams, 0, $maxTableDepth, $verticalRows, $rowIndex, true);
        ?>
        <table class="table table-striped-columns table-bordered align-middle text-center">
            <thead align="center">
                <tr>
                    <th colspan="<?= $maxTableDepth + 2 ?>" scope="col">
                        <?= strtoupper(esc($process_name ?? $process_code)) ?>
                    </th>
                </tr>
                <tr>
                    <th scope="col" width="5%">No</th>
                    <th colspan="<?= max(1, $maxTableDepth - 1) ?>" scope="col">Start up Check Item</th>
                    <?php if ($maxTableDepth > 1): ?>
                        <th scope="col">Standard</th>
                    <?php endif; ?>
                    <th scope="col" width="22%">Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($verticalRows as $r): ?>
                    <tr>
                        <?php foreach ($r as $cell): ?>
                            <?php if ($cell['type'] === 'no'): ?>
                                <td rowspan="<?= $cell['rowspan'] ?>" class="fw-bold"><?= $cell['no'] ?></td>
                            <?php elseif ($cell['type'] === 'param'): ?>
                                <td rowspan="<?= $cell['rowspan'] ?>" colspan="<?= $cell['colspan'] ?>">
                                    <?= esc($cell['name']) ?>
                                    <?php if (!empty($cell['std'])): ?>
                                        <div class="text-muted small"><?= esc($cell['std']) ?></div>
                                    <?php endif; ?>
                                </td>
                            <?php elseif ($cell['type'] === 'input'): ?>
                                <td>
                                    <?= opRenderActiveInput($cell['node_data'], $kategori_form, $alldata ?? null, 0) ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <?php
        $totalTableCols = count($headerLeaves) + (!empty($gridRowNames) ? 1 : 0);
        ?>
        <table class="table table-striped-columns table-bordered align-middle text-center" style="white-space: nowrap;">
            <thead align="center">
                <tr>
                    <th colspan="<?= max(1, $totalTableCols) ?>" scope="col">
                        <?= strtoupper(esc($process_name ?? $process_code)) ?>
                    </th>
                </tr>
                <?php foreach ($headerRows as $level => $row): ?>
                    <tr>
                        <?php
                        $runningCol = 0;
                        $labelInserted = false;
                        foreach ($row as $col):
                            if (!empty($gridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx):
                                $labelInserted = true;
                        ?>
                                <th class="input-form align-middle" rowspan="<?= $headerMaxDepth ?>">Sample / Label</th>
                        <?php
                            endif;
                        ?>
                            <th class="input-form align-middle" colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>">
                                <?= esc($col['name']) ?>
                                <?php if (!empty($col['node_data']['standar_nilai'])): ?>
                                    <br><small class="fw-normal"><?= esc($col['node_data']['standar_nilai']) ?></small>
                                <?php endif; ?>
                            </th>
                        <?php
                            $runningCol += $col['colspan'];
                        endforeach;
                        ?>
                    </tr>
                <?php endforeach; ?>
            </thead>
            <tbody>
                <?php if (empty($gridRowNames)): ?>
                    <tr>
                        <?php foreach ($headerLeaves as $leaf): ?>
                            <td>
                                <?= opRenderActiveInput($leaf, $kategori_form, $alldata ?? null, 0) ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php else: ?>
                    <?php foreach ($gridRowNames as $rIdx => $rowName): ?>
                        <tr>
                            <?php foreach ($headerLeaves as $hIdx => $hLeaf): ?>
                                <?php if ($hIdx === $firstGridLeafIdx): ?>
                                    <th scope="row" class="align-middle"><?= esc($rowName) ?></th>
                                <?php endif; ?>

                                <?php if (empty($hLeaf['children'])): ?>
                                    <?php if ($rIdx === 0): ?>
                                        <td rowspan="<?= count($gridRowNames) ?>" class="align-middle">
                                            <?= opRenderActiveInput($hLeaf, $kategori_form, $alldata ?? null, 0) ?>
                                        </td>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php
                                    $matchedCell = null;
                                    foreach ($hLeaf['children'] as $gChild) {
                                        if ($gChild['posisi_grid'] === $rowName) {
                                            $matchedCell = $gChild;
                                            break;
                                        }
                                    }
                                    ?>
                                    <td>
                                        <?= $matchedCell ? opRenderActiveInput($matchedCell, $kategori_form, $alldata ?? null, $rIdx) : '-' ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function validateDynamicRange(el, min, max) {
    if (!el || el.value === '') {
        el.style.backgroundColor = '';
        return;
    }
    let val = parseFloat(el.value);
    if (isNaN(val) || val < min || val > max) {
        el.style.backgroundColor = '#ffcccc';
    } else {
        el.style.backgroundColor = '';
    }
    if (typeof changeRange === 'function' && el.id) {
        changeRange(el.id, min, max);
    }
}
</script>