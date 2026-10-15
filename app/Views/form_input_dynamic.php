<?php
// --- HELPER REKURSIF UNTUK HALAMAN INPUT OPERATOR (SUPPORT 7 FITUR BARU) ---
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

if (!function_exists('opCollectTreeMeta')) {
    function opCollectTreeMeta($nodes, &$exclusiveMap, &$dynamicStdList, &$dynamicStdTargetMap, $activeExclGroup = null, $activeDynStdId = null) {
        foreach ($nodes as $node) {
            $currentExcl = $activeExclGroup;
            if (!empty($node['is_exclusive'])) {
                $currentExcl = 'excl_' . $node['id'];
            }

            $currentDynId = $activeDynStdId;
            if (!empty($node['standar_dinamis'])) {
                $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
                if (is_array($dyn) && !empty($dyn['options'])) {
                    $currentDynId = 'dynstd_' . $node['id'];
                    $dyn['dom_id']  = $currentDynId;
                    $dyn['node_id'] = $node['id'];
                    $dynamicStdList[$currentDynId] = $dyn;
                }
            }

            if (empty($node['children'])) {
                if ($currentExcl) {
                    $exclusiveMap[$node['id']] = $currentExcl;
                }
                if ($currentDynId) {
                    $dynamicStdTargetMap[$node['id']] = $currentDynId;
                }
            } else {
                opCollectTreeMeta($node['children'], $exclusiveMap, $dynamicStdList, $dynamicStdTargetMap, $currentExcl, $currentDynId);
            }
        }
    }
}

if (!function_exists('opSplitValUnit')) {
    function opSplitValUnit($rawVal) {
        $rawVal = trim((string)$rawVal);
        if ($rawVal === '') return ['val' => '', 'unit' => ''];
        $parts = explode(' ', $rawVal, 2);
        return [
            'val'  => $parts[0] ?? '',
            'unit' => $parts[1] ?? ''
        ];
    }
}

if (!function_exists('opRenderActiveInput')) {
    function opRenderActiveInput($leaf, $kategori_form, $alldata = null, $rowIdx = 0, $exclusiveMap = [], $dynamicStdTargetMap = []) {
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));
        if ($colDb === '') {
            return '<span class="text-muted">-</span>';
        }

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
        $isReadonly = !empty($leaf['is_readonly']);

        // Cek Exclusive Group & Dynamic Standard Target
        $exclGroup = $exclusiveMap[$leaf['id']] ?? '';
        $dynStdId  = $dynamicStdTargetMap[$leaf['id']] ?? '';

        $exclAttr   = $exclGroup ? 'data-exclusive-group="'.esc($exclGroup).'"' : '';
        $dynStdAttr = $dynStdId ? 'data-dynstd-target="'.esc($dynStdId).'"' : '';

        // Cek Rumus Otomatis
        $formula = null;
        if (!empty($leaf['rumus_otomatis'])) {
            $formula = is_string($leaf['rumus_otomatis']) ? json_decode($leaf['rumus_otomatis'], true) : $leaf['rumus_otomatis'];
        }
        $formulaAttr = '';
        if (!empty($formula['type'])) {
            $isReadonly = true;
            $inCol  = $formula['in_col'] ?? 'par013';
            $outCol = $formula['out_col'] ?? 'par014';
            // Jika berada di baris matriks Foregoing (punya suffix a/b/c/d), sesuaikan suffix kolom acuannya
            if (preg_match('/^par\d+([a-z])$/i', $colDb, $mSuf)) {
                $suf = $mSuf[1];
                if (!preg_match('/[a-z]$/i', $inCol))  $inCol  .= $suf;
                if (!preg_match('/[a-z]$/i', $outCol)) $outCol .= $suf;
            }
            $formulaAttr = 'data-formula-type="'.esc($formula['type']).'" data-in-col="'.esc($inCol).'" data-out-col="'.esc($outCol).'"';
        }

        // Cek Satuan Opsi (Dropdown Satuan)
        $units = [];
        if (!empty($leaf['satuan_opsi'])) {
            $decodedUnits = is_string($leaf['satuan_opsi']) ? json_decode($leaf['satuan_opsi'], true) : $leaf['satuan_opsi'];
            if (is_array($decodedUnits)) $units = $decodedUnits;
        }

        $roAttr  = $isReadonly ? 'readonly' : '';
        $roStyle = $isReadonly ? 'background-color: #e9ecef;' : '';

        $html = '';

        // 1. TIPE DROPDOWN (OK / NG)
        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            if ($kategori_form === 'startup') {
                $selOk = ($val === '✔' || $val === 'OK') ? 'selected' : '';
                $selNg = ($val === '✖' || $val === 'NG') ? 'selected' : '';
                $html .= '<select class="form-control form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" '.$exclAttr.'>
                            <option value="">-- Pilih --</option>
                            <option value="✔" '.$selOk.'>✔ (OK)</option>
                            <option value="✖" '.$selNg.'>✖ (NG)</option>
                          </select>';
            } else {
                $selMin = ($val === '-' || $val === '') ? 'selected' : '';
                $selOk  = ($val === 'OK') ? 'selected' : '';
                $selNg  = ($val === 'NG') ? 'selected' : '';
                $html .= '<select class="form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" style="min-width: 85px;" '.$exclAttr.'>
                            <option value="-" '.$selMin.'>-</option>
                            <option value="OK" '.$selOk.'>OK</option>
                            <option value="NG" '.$selNg.'>NG</option>
                          </select>';
            }
        }
        // 2. TIPE TEXTAREA
        elseif ($leaf['tipe_input'] === 'textarea') {
            $html .= '<textarea class="form-control dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" rows="4" style="min-width: 160px; '.$roStyle.'" '.$roAttr.' '.$exclAttr.'>'.esc($val).'</textarea>';
        }
        // 3. TIPE ANGKA (DENGAN SATUAN DROPDOWN, EXCLUSIVE, READONLY, RUMUS OTOMATIS, & MIN/MAX)
        elseif ($leaf['tipe_input'] === 'number') {
            // A. Jika memiliki Pilihan Satuan (Dropdown Satuan yang digabung ke Hidden Input parXXX)
            if (!empty($units)) {
                $parsed = opSplitValUnit($val);
                $numVal = $parsed['val'];
                $curUnit = $parsed['unit'] ?: ($units[0]['unit'] ?? '');

                $html .= '<div class="input-group" style="min-width: 165px;">';
                $html .= '<input type="hidden" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'">';
                $html .= '<input type="number" step="any" class="form-control text-center dyn-input-el dyn-unit-val" '
                       . 'id="'.esc($colDb).'_val" data-target-col="'.esc($colDb).'" value="'.esc($numVal).'" '
                       . 'style="'.$roStyle.'" '.$roAttr.' '.$exclAttr.' '.$dynStdAttr.'>';
                $html .= '<select class="form-select dyn-unit-sel" id="'.esc($colDb).'_unit" data-target-col="'.esc($colDb).'" style="max-width: 88px; padding-left: 6px; padding-right: 20px;">';
                foreach ($units as $u) {
                    $uName = $u['unit'] ?? '';
                    $uMin  = ($u['min'] !== null && $u['min'] !== '') ? $u['min'] : '';
                    $uMax  = ($u['max'] !== null && $u['max'] !== '') ? $u['max'] : '';
                    $sel   = ($uName === $curUnit) ? 'selected' : '';
                    $html .= '<option value="'.esc($uName).'" data-min="'.esc($uMin).'" data-max="'.esc($uMax).'" '.$sel.'>'.esc($uName).'</option>';
                }
                $html .= '</select></div>';
            }
            // B. Angka Standar Tanpa Dropdown Satuan
            else {
                $dataMinAttr = ($min !== null) ? 'data-min="'.$min.'"' : '';
                $dataMaxAttr = ($max !== null) ? 'data-max="'.$max.'"' : '';

                $legacyEvents = '';
                if ($kategori_form === 'production' && ($colDb === 'par013' || $colDb === 'par014')) {
                    $legacyEvents = 'if(typeof quantityChange1===\'function\') quantityChange1();';
                }

                $html .= '<input type="number" step="any" class="form-control text-center dyn-input-el dyn-number-input" '
                       . 'style="min-width: 95px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '
                       . $dataMinAttr . ' ' . $dataMaxAttr . ' ' . $exclAttr . ' ' . $dynStdAttr . ' ' . $formulaAttr . ' ' . $roAttr
                       . ($legacyEvents ? ' onkeyup="'.$legacyEvents.'" onchange="'.$legacyEvents.'"' : '') . '>';

                if ($min !== null || $max !== null) {
                    $minLbl = ($min !== null) ? $min : '-∞';
                    $maxLbl = ($max !== null) ? $max : '∞';
                    $html .= '<small class="text-muted d-block mt-1" style="font-size:10px;">Std: '.$minLbl.' ~ '.$maxLbl.'</small>';
                }
            }
        }
        // 4. TIPE TEKS BEBAS
        else {
            $html .= '<input type="text" class="form-control dyn-input-el" style="min-width: 110px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '.$roAttr.' '.$exclAttr.'>';
        }

        return $html;
    }
}

if (!function_exists('opRenderHeaderTitle')) {
    function opRenderHeaderTitle($node) {
        if (!empty($node['standar_dinamis'])) {
            $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
            if (is_array($dyn) && !empty($dyn['options'])) {
                $prefix = $dyn['prefix_text'] ?? 'Min: ';
                $suffix = $dyn['suffix_text'] ?? ' N';
                $domId  = 'dynstd_' . $node['id'];
                return esc($prefix) . '<span id="lbl_'.$domId.'" class="fw-bold text-danger">-</span>' . esc($suffix);
            }
        }
        return esc($node['nama_parameter']);
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

// 1. Kumpulkan Metadata Pohon (Exclusive Group & Standar Dinamis seperti Jenis SM-Chip)
$exclusiveMap = [];
$dynamicStdList = [];
$dynamicStdTargetMap = [];
if (!empty($treeParams)) {
    opCollectTreeMeta($treeParams, $exclusiveMap, $dynamicStdList, $dynamicStdTargetMap);
}

// 2. Hitung cnt-proses otomatis untuk Foregoing
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

    <!-- ========================================================================= -->
    <!-- DROPDOWN PENENTU STANDAR DINAMIS (CONTOH: JENIS SM-CHIP ➔ MIN: 8.7 N)     -->
    <!-- ========================================================================= -->
    <?php if (!empty($dynamicStdList)): ?>
        <?php foreach ($dynamicStdList as $domId => $dyn): ?>
            <?php
            $dbColName  = $dyn['db_col'] ?? 'std_die_bond';
            $currentStd = (isset($alldata) && isset($alldata[0][$dbColName])) ? $alldata[0][$dbColName] : '';
            ?>
            <div class="mb-3 row align-items-center bg-light p-2 rounded border mx-0">
                <label class="col-sm-2 col-form-label fw-bold"><?= esc($dyn['label_dropdown'] ?? 'Pilih Standar') ?></label>
                <div class="col-sm-4">
                    <select class="form-select dyn-std-selector" data-dom-id="<?= esc($domId) ?>" data-db-col="<?= esc($dbColName) ?>" required>
                        <option value="" data-min="">-- Pilih <?= esc($dyn['label_dropdown'] ?? 'Standar') ?> --</option>
                        <?php foreach ($dyn['options'] as $opt): ?>
                            <?php
                            $isSelected = ($currentStd !== '' && ($currentStd == $opt['value'] || $currentStd == $opt['min'])) ? 'selected' : '';
                            ?>
                            <option value="<?= esc($opt['value']) ?>" data-min="<?= esc($opt['min']) ?>" <?= $isSelected ?>>
                                <?= esc($opt['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" id="<?= esc($dbColName) ?>" name="<?= esc($dbColName) ?>" value="<?= esc($currentStd) ?>">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($treeParams)): ?>
        <div class="alert alert-warning text-center my-3">
            Form untuk proses <b><?= strtoupper(esc($process_code)) ?></b> belum memiliki parameter.
        </div>

    <?php elseif ($kategori_form === 'startup'): ?>
        <!-- ========================================================== -->
        <!-- TABEL INPUT OPERATOR: STARTUP (VERTIKAL)                   -->
        <!-- ========================================================== -->
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
                    <th scope="col" width="24%">Data</th>
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
                                    <?= opRenderHeaderTitle($cell['node_data']) ?>
                                    <?php if (!empty($cell['std'])): ?>
                                        <div class="text-muted small"><?= esc($cell['std']) ?></div>
                                    <?php endif; ?>
                                </td>
                            <?php elseif ($cell['type'] === 'input'): ?>
                                <td>
                                    <?= opRenderActiveInput($cell['node_data'], $kategori_form, $alldata ?? null, 0, $exclusiveMap, $dynamicStdTargetMap) ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <!-- ========================================================== -->
        <!-- TABEL INPUT OPERATOR: PRODUCTION & FOREGOING               -->
        <!-- ========================================================== -->
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
                                <?= opRenderHeaderTitle($col['node_data']) ?>
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
                                <?= opRenderActiveInput($leaf, $kategori_form, $alldata ?? null, 0, $exclusiveMap, $dynamicStdTargetMap) ?>
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
                                            <?= opRenderActiveInput($hLeaf, $kategori_form, $alldata ?? null, 0, $exclusiveMap, $dynamicStdTargetMap) ?>
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
                                        <?= $matchedCell ? opRenderActiveInput($matchedCell, $kategori_form, $alldata ?? null, $rIdx, $exclusiveMap, $dynamicStdTargetMap) : '-' ?>
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
(function() {
    let container = document.getElementById('<?= esc($process_code) ?>');
    if (!container) return;

    // 1. FUNGSI VALIDASI RENTANG MIN / MAX (MERAH JIKA NG)
    function checkInputMinMax(el) {
        if (!el || el.value === '') {
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
            return;
        }
        let val = parseFloat(el.value);
        let minAttr = el.getAttribute('data-min');
        let maxAttr = el.getAttribute('data-max');
        let min = (minAttr !== null && minAttr !== '') ? parseFloat(minAttr) : -999999999;
        let max = (maxAttr !== null && maxAttr !== '') ? parseFloat(maxAttr) : 999999999;

        if (isNaN(val) || val < min || val > max) {
            el.style.backgroundColor = '#ffcccc';
        } else {
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
        }

        if (typeof changeRange === 'function' && el.id && (min > -999999999 || max < 999999999)) {
            changeRange(el.id, min, max);
        }
    }

    // 2. FUNGSI GABUNG ANGKA + DROPDOWN SATUAN (UNTUK DISIMPAN KE DB & TAMPIL DI HISTORY)
    function syncUnitInputGroup(colId) {
        let valEl    = document.getElementById(colId + '_val');
        let unitEl   = document.getElementById(colId + '_unit');
        let hiddenEl = document.getElementById(colId);
        if (!valEl || !unitEl || !hiddenEl) return;

        let selectedOpt = unitEl.options[unitEl.selectedIndex];
        if (selectedOpt) {
            valEl.setAttribute('data-min', selectedOpt.getAttribute('data-min') || '');
            valEl.setAttribute('data-max', selectedOpt.getAttribute('data-max') || '');
        }

        if (valEl.value !== '') {
            hiddenEl.value = valEl.value + ' ' + unitEl.value;
        } else {
            hiddenEl.value = '';
        }

        checkInputMinMax(valEl);
    }

    // 3. FUNGSI SALING MENGUNCI / GRAY OUT (HANYA BOLEH DIISI SALAH SATU)
    function enforceExclusiveGroups() {
        let groups = {};
        container.querySelectorAll('[data-exclusive-group]').forEach(function(el) {
            let grp = el.getAttribute('data-exclusive-group');
            if (!groups[grp]) groups[grp] = [];
            groups[grp].push(el);
        });

        Object.keys(groups).forEach(function(grp) {
            let items = groups[grp];
            let filledItem = items.find(function(el) {
                return el.value !== '' && el.value !== '-';
            });

            items.forEach(function(el) {
                let unitSibling = el.id.endsWith('_val') ? document.getElementById(el.id.replace('_val', '_unit')) : null;
                if (filledItem && el !== filledItem) {
                    el.disabled = true;
                    el.style.backgroundColor = '#e2e8f0';
                    if (unitSibling) unitSibling.disabled = true;
                } else {
                    el.disabled = false;
                    if (!el.hasAttribute('readonly')) {
                        checkInputMinMax(el);
                    }
                    if (unitSibling) unitSibling.disabled = false;
                }
            });
        });
    }

    // 4. FUNGSI HITUNG OTOMATIS NG (IN - OUT) & YIELD % ((OUT / IN) * 100)
    function recalculateFormulas() {
        container.querySelectorAll('[data-formula-type]').forEach(function(targetEl) {
            let fType  = targetEl.getAttribute('data-formula-type');
            let inCol  = targetEl.getAttribute('data-in-col');
            let outCol = targetEl.getAttribute('data-out-col');

            let inEl  = document.getElementById(inCol);
            let outEl = document.getElementById(outCol);
            if (!inEl || !outEl) return;

            let inVal  = parseFloat(inEl.value);
            let outVal = parseFloat(outEl.value);

            if (isNaN(inVal) || isNaN(outVal)) {
                targetEl.value = '';
                targetEl.style.backgroundColor = '#e9ecef';
                return;
            }

            if (fType === 'ng') {
                let ng = inVal - outVal;
                targetEl.value = Number.isInteger(ng) ? ng : parseFloat(ng.toFixed(4));
                targetEl.style.backgroundColor = (ng < 0) ? '#ffcccc' : '#e9ecef';
            } else if (fType === 'yield') {
                if (inVal <= 0) {
                    targetEl.value = 0;
                } else {
                    let y = (outVal / inVal) * 100;
                    targetEl.value = parseFloat(y.toFixed(2));
                }
                targetEl.style.backgroundColor = (outVal > inVal || outVal < 0) ? '#ffcccc' : '#e9ecef';
            }
        });
    }

    // 5. FUNGSI DROPDOWN STANDAR DINAMIS (CONTOH: JENIS SM-CHIP ➔ MIN: 8.7 N)
    container.querySelectorAll('.dyn-std-selector').forEach(function(sel) {
        function updateStdGroup() {
            let domId     = sel.getAttribute('data-dom-id');
            let dbCol     = sel.getAttribute('data-db-col');
            let opt       = sel.options[sel.selectedIndex];
            let minVal    = opt ? opt.getAttribute('data-min') : '';
            let lblSpan   = document.getElementById('lbl_' + domId);
            let hiddenStd = document.getElementById(dbCol);

            if (lblSpan)   lblSpan.textContent = (minVal !== '' && minVal !== null) ? minVal : '-';
            if (hiddenStd) hiddenStd.value     = (minVal !== '' && minVal !== null) ? minVal : '';

            container.querySelectorAll('[data-dynstd-target="' + domId + '"]').forEach(function(inp) {
                inp.setAttribute('data-min', minVal || '');
                inp.setAttribute('min', minVal || '');
                if (inp.value !== '' && minVal !== '' && parseFloat(inp.value) < parseFloat(minVal)) {
                    inp.style.backgroundColor = '#ffcccc';
                } else {
                    checkInputMinMax(inp);
                }
            });
        }

        sel.addEventListener('change', updateStdGroup);
        if (sel.value !== '') updateStdGroup();
    });

    // Pasang Event Listener ke Semua Input di Dalam Tabel
    container.querySelectorAll('.dyn-unit-val, .dyn-unit-sel').forEach(function(el) {
        let colId = el.getAttribute('data-target-col');
        el.addEventListener('input', function() {
            syncUnitInputGroup(colId);
            enforceExclusiveGroups();
        });
        el.addEventListener('change', function() {
            syncUnitInputGroup(colId);
            enforceExclusiveGroups();
        });
        syncUnitInputGroup(colId);
    });

    container.querySelectorAll('.dyn-number-input').forEach(function(el) {
        el.addEventListener('input', function() {
            checkInputMinMax(el);
            enforceExclusiveGroups();
            recalculateFormulas();
        });
        el.addEventListener('change', function() {
            checkInputMinMax(el);
            enforceExclusiveGroups();
            recalculateFormulas();
        });
    });

    container.querySelectorAll('.dyn-input-el:not(.dyn-number-input):not(.dyn-unit-val)').forEach(function(el) {
        el.addEventListener('change', enforceExclusiveGroups);
        el.addEventListener('input', enforceExclusiveGroups);
    });

    enforceExclusiveGroups();
    recalculateFormulas();
})();
</script>