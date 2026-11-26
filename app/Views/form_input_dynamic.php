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
    function opRenderActiveInput($leaf, $kategori_form, $alldata = null, $rowIdx = 0, $exclusiveMap = [], $dynamicStdTargetMap = [], $colHeaderName = '') {
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));

        // Jika ini adalah kolom teks statis pada tabel mendatar (Contoh: "Start up Checking Item" / "Standard" di Burn In)
        if ($colDb === '' || ($leaf['tipe_input'] ?? '') === 'header_only') {
            $staticTxt = trim((string)($leaf['standar_nilai'] ?? ''));
            return $staticTxt !== '' ? '<div class="fw-semibold text-wrap" style="min-width: 120px;">' . nl2br(esc($staticTxt)) . '</div>' : '<span class="text-muted">-</span>';
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

        $stepVal = (!empty($leaf['step_value']) && $leaf['step_value'] !== 'any') ? $leaf['step_value'] : 'any';
        $rgJson  = !empty($leaf['rentang_ganda']) ? (is_string($leaf['rentang_ganda']) ? $leaf['rentang_ganda'] : json_encode($leaf['rentang_ganda'], JSON_UNESCAPED_UNICODE)) : '';
        $rgAttr  = $rgJson ? 'data-rentang-ganda="'.esc($rgJson).'"' : '';

        $roAttr  = $isReadonly ? 'readonly' : '';
        $roStyle = $isReadonly ? 'background-color: #e9ecef;' : '';
        $isRequired = (!$isReadonly && !empty($leaf['is_required']));
        $reqAttr    = $isRequired ? 'required data-is-required="1"' : '';
        $leafNameLw = strtolower(trim((string)($leaf['nama_parameter'] ?? $colHeaderName)));
        $isNoteField = (strpos($leafNameLw, 'note') !== false || strpos($leafNameLw, 'catatan') !== false);
        $noteAttr   = $isNoteField ? 'data-is-note="1"' : '';
        $extraInputAttrs = trim($exclAttr . ' ' . $reqAttr . ' ' . $noteAttr);

        $html = '';

        // 1. TIPE DROPDOWN OK / NG (atau V / X)
        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            if ($kategori_form === 'startup') {
                $selOk = ($val === '✔' || $val === 'OK' || $val === 'V') ? 'selected' : '';
                $selNg = ($val === '✖' || $val === 'NG' || $val === 'X') ? 'selected' : '';
                $html .= '<select class="form-control form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" '.$extraInputAttrs.'>
                            <option value="">-- Pilih --</option>
                            <option value="✔" '.$selOk.'>✔ (OK)</option>
                            <option value="✖" '.$selNg.'>✖ (NG)</option>
                          </select>';
            } elseif ($kategori_form === 'startup_horizontal') {
                $selMin = ($val === '-' || (!$isRequired && $val === '')) ? 'selected' : '';
                $selOk  = ($val === 'V' || $val === 'OK' || $val === '✔') ? 'selected' : '';
                $selNg  = ($val === 'X' || $val === 'NG' || $val === '✖') ? 'selected' : '';
                $optFirst = $isRequired
                    ? '<option value="" '.($val === '' ? 'selected' : '').'>-- Pilih --</option>'.($val === '-' ? '<option value="-" selected>-</option>' : '')
                    : '<option value="-" '.$selMin.'>-</option>';
                $html .= '<select class="form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" style="min-width: 85px;" '.$extraInputAttrs.'>
                            '.$optFirst.'
                            <option value="V" '.$selOk.'>V</option>
                            <option value="X" '.$selNg.'>X</option>
                          </select>';
            } else {
                $selMin = ($val === '-' || (!$isRequired && $val === '')) ? 'selected' : '';
                $selOk  = ($val === 'OK') ? 'selected' : '';
                $selNg  = ($val === 'NG') ? 'selected' : '';
                $optFirst = $isRequired
                    ? '<option value="" '.($val === '' ? 'selected' : '').'>-- Pilih --</option>'.($val === '-' ? '<option value="-" selected>-</option>' : '')
                    : '<option value="-" '.$selMin.'>-</option>';
                $html .= '<select class="form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" style="min-width: 85px;" '.$extraInputAttrs.'>
                            '.$optFirst.'
                            <option value="OK" '.$selOk.'>OK</option>
                            <option value="NG" '.$selNg.'>NG</option>
                          </select>';
            }
        }
        // 2. TIPE DROPDOWN CUSTOM (Pilihan Bebas)
        elseif ($leaf['tipe_input'] === 'dropdown_custom') {
            $customOpts = [];
            if (!empty($leaf['opsi_dropdown'])) {
                $decoded = is_string($leaf['opsi_dropdown']) ? json_decode($leaf['opsi_dropdown'], true) : $leaf['opsi_dropdown'];
                if (is_array($decoded)) {
                    $customOpts = $decoded;
                } else {
                    $customOpts = array_filter(array_map('trim', explode(',', (string)$leaf['opsi_dropdown'])));
                }
            }
            $html .= '<select class="form-select dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" style="min-width: 95px;" '.$extraInputAttrs.'>';
            $html .= '<option value="">-- Pilih --</option>';
            foreach ($customOpts as $cOpt) {
                $optVal = is_array($cOpt) ? ($cOpt['value'] ?? $cOpt['label'] ?? '') : $cOpt;
                $optLbl = is_array($cOpt) ? ($cOpt['label'] ?? $cOpt['value'] ?? '') : $cOpt;
                $sel = ((string)$val === (string)$optVal) ? 'selected' : '';
                $html .= '<option value="'.esc($optVal).'" '.$sel.'>'.esc($optLbl).'</option>';
            }
            $html .= '</select>';
        }
        // 3. TIPE AUTO JUDGMENT (Ceklis ✔ / Silang ✖ Otomatis)
        elseif ($leaf['tipe_input'] === 'auto_judgment') {
            $targetEval = !empty($leaf['target_evaluasi']) ? $leaf['target_evaluasi'] : '*';
            $dispVal = ($val === '✔' || $val === 'OK') ? '✔' : (($val === '✖' || $val === 'NG') ? '✖' : ($val ?: ''));
            $bgCol = ($dispVal === '✔') ? '#d1e7dd' : (($dispVal === '✖') ? '#f8d7da' : '#f8f9fa');
            $txtCol = ($dispVal === '✔') ? '#0f5132' : (($dispVal === '✖') ? '#842029' : '#6c757d');
            $borderCol = ($dispVal === '✔') ? '#badbcc' : (($dispVal === '✖') ? '#f5c2c7' : '#ced4da');
            $html .= '<input type="text" class="form-control text-center fw-bold dyn-input-el dyn-auto-judgment" '
                   . 'id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($dispVal).'" '
                   . 'data-target-eval="'.esc($targetEval).'" readonly '
                   . 'style="min-width: 80px; font-size: 1.15rem; background-color: '.$bgCol.'; color: '.$txtCol.'; border-color: '.$borderCol.'; cursor: not-allowed;" '.$exclAttr.'>';
        }
        // 4. TIPE WAKTU (TIME)
        elseif ($leaf['tipe_input'] === 'time') {
            $html .= '<input type="time" class="form-control text-center dyn-input-el" style="min-width: 105px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '.$roAttr.' '.$extraInputAttrs.'>';
        }
        // 5. TIPE TANGGAL (DATE)
        elseif ($leaf['tipe_input'] === 'date') {
            $html .= '<input type="date" class="form-control text-center dyn-input-el" style="min-width: 130px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '.$roAttr.' '.$extraInputAttrs.'>';
        }
        // 6. TIPE TEXTAREA
        elseif ($leaf['tipe_input'] === 'textarea') {
            $html .= '<textarea class="form-control dyn-input-el" id="'.esc($colDb).'" name="'.esc($colDb).'" rows="4" style="min-width: 160px; '.$roStyle.'" '.$roAttr.' '.$extraInputAttrs.'>'.esc($val).'</textarea>';
        }
        // 7. TIPE ANGKA (NUMBER)
        elseif ($leaf['tipe_input'] === 'number') {
            if (!empty($units)) {
                $parsed = opSplitValUnit($val);
                $numVal = $parsed['val'];
                $curUnit = $parsed['unit'] ?: ($units[0]['unit'] ?? '');

                $html .= '<div class="input-group" style="min-width: 165px;">';
                $html .= '<input type="hidden" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'">';
                $html .= '<input type="number" step="'.esc($stepVal).'" class="form-control text-center dyn-input-el dyn-unit-val" '
                       . 'id="'.esc($colDb).'_val" data-target-col="'.esc($colDb).'" value="'.esc($numVal).'" '
                       . 'style="'.$roStyle.'" '.$roAttr.' '.$extraInputAttrs.' '.$dynStdAttr.' '.$rgAttr.'>';
                $html .= '<select class="form-select dyn-unit-sel" id="'.esc($colDb).'_unit" data-target-col="'.esc($colDb).'" style="max-width: 88px; padding-left: 6px; padding-right: 20px;">';
                foreach ($units as $u) {
                    $uName = $u['unit'] ?? '';
                    $uMin  = ($u['min'] !== null && $u['min'] !== '') ? $u['min'] : '';
                    $uMax  = ($u['max'] !== null && $u['max'] !== '') ? $u['max'] : '';
                    $sel   = ($uName === $curUnit) ? 'selected' : '';
                    $html .= '<option value="'.esc($uName).'" data-min="'.esc($uMin).'" data-max="'.esc($uMax).'" '.$sel.'>'.esc($uName).'</option>';
                }
                $html .= '</select></div>';
            } else {
                $dataMinAttr = ($min !== null) ? 'data-min="'.$min.'"' : '';
                $dataMaxAttr = ($max !== null) ? 'data-max="'.$max.'"' : '';
                $origMinAttr = ($min !== null) ? 'data-orig-min="'.$min.'"' : '';
                $origMaxAttr = ($max !== null) ? 'data-orig-max="'.$max.'"' : '';
                $dataColAttr = $colHeaderName ? 'data-col-name="'.esc($colHeaderName).'"' : '';
                $dataPosAttr = !empty($leaf['posisi_grid']) ? 'data-grid-pos="'.esc($leaf['posisi_grid']).'"' : '';
                $dataParamAttr = !empty($leaf['nama_parameter']) ? 'data-param-name="'.esc($leaf['nama_parameter']).'"' : '';

                $legacyEvents = '';
                if ($kategori_form === 'production' && ($colDb === 'par013' || $colDb === 'par014')) {
                    $legacyEvents = 'if(typeof quantityChange1===\'function\') quantityChange1();';
                }

                $html .= '<input type="number" step="'.esc($stepVal).'" class="form-control text-center dyn-input-el dyn-number-input" '
                       . 'style="min-width: 95px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '
                       . $dataMinAttr . ' ' . $dataMaxAttr . ' ' . $origMinAttr . ' ' . $origMaxAttr . ' ' . $dataColAttr . ' ' . $dataPosAttr . ' ' . $dataParamAttr . ' '
                       . $extraInputAttrs . ' ' . $dynStdAttr . ' ' . $formulaAttr . ' ' . $rgAttr . ' ' . $roAttr
                       . ($legacyEvents ? ' oninput="'.$legacyEvents.'" onchange="'.$legacyEvents.'"' : '') . '>';

                if (!empty($rgJson)) {
                    $decodedRg = json_decode($rgJson, true);
                    if (is_array($decodedRg)) {
                        $rgParts = [];
                        foreach ($decodedRg as $dr) {
                            $rgParts[] = ($dr['min'] ?? '') . ' ~ ' . ($dr['max'] ?? '');
                        }
                        $html .= '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;">Std: ' . implode(' / ', $rgParts) . '</small>';
                    }
                } elseif ($min !== null || $max !== null) {
                    $minLbl = ($min !== null) ? $min : '-∞';
                    $maxLbl = ($max !== null) ? $max : '∞';
                    $html .= '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;">Std: '.$minLbl.' ~ '.$maxLbl.'</small>';
                } else {
                    $html .= '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;"></small>';
                }
            }
        }
        // 8. TIPE TEKS BEBAS
        else {
            $html .= '<input type="text" class="form-control dyn-input-el" style="min-width: 110px; '.$roStyle.'" id="'.esc($colDb).'" name="'.esc($colDb).'" value="'.esc($val).'" '.$roAttr.' '.$extraInputAttrs.'>';
        }

        return $html;
    }
}

if (!function_exists('opRenderHeaderTitle')) {
    function opRenderHeaderTitle($node) {
        $reqStar = '';
        $isGridReq = (!empty($node['children'][0]['posisi_grid']) && !empty($node['children'][0]['is_required']) && empty($node['children'][0]['is_readonly']));
        $isLeafReq = (empty($node['children']) && !empty($node['is_required']) && empty($node['is_readonly']));
        if ($isLeafReq || $isGridReq) {
            $reqStar = ' <span class="text-danger fw-bold" title="Wajib diisi">*</span>';
        }

        if (!empty($node['standar_dinamis'])) {
            $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
            if (is_array($dyn) && !empty($dyn['options'])) {
                $prefix = $dyn['prefix_text'] ?? 'Min: ';
                $suffix = $dyn['suffix_text'] ?? ' N';
                $domId  = 'dynstd_' . $node['id'];
                return esc($prefix) . '<span id="lbl_'.$domId.'" class="fw-bold text-danger">-</span>' . esc($suffix) . $reqStar;
            }
        }
        return esc($node['nama_parameter']) . $reqStar;
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

// 1. Kumpulkan Metadata Pohon
$exclusiveMap = [];
$dynamicStdList = [];
$dynamicStdTargetMap = [];
if (!empty($treeParams)) {
    opCollectTreeMeta($treeParams, $exclusiveMap, $dynamicStdList, $dynamicStdTargetMap);
}

// 2. Hitung cnt-proses otomatis untuk Foregoing, Startup Mendatar, & Multi-Table Production
$cntProses = 1;
$allGridRowNames = [];
$hasSuffixCols = false;

if ($kategori_form !== 'startup' && !empty($treeParams)) {
    foreach ($treeParams as $node) {
        $checkQueue = [$node];
        while (!empty($checkQueue)) {
            $curr = array_shift($checkQueue);
            if (!empty($curr['children'])) {
                if (!empty($curr['children'][0]['posisi_grid'])) {
                    foreach ($curr['children'] as $gridChild) {
                        if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $allGridRowNames)) {
                            $allGridRowNames[] = $gridChild['posisi_grid'];
                        }
                        if (!empty($gridChild['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $gridChild['kolom_db'])) {
                            $hasSuffixCols = true;
                        }
                    }
                } else {
                    foreach ($curr['children'] as $ch) {
                        $checkQueue[] = $ch;
                    }
                }
            }
        }
    }
    if (!empty($allGridRowNames) && $hasSuffixCols) {
        $cntProses = count($allGridRowNames);
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
        <!-- TABEL INPUT OPERATOR: STARTUP MENURUN (VERTIKAL)           -->
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
        <!-- ========================================================================= -->
        <!-- TABEL INPUT OPERATOR: STARTUP MENDATAR, PRODUCTION, & FOREGOING           -->
        <!-- ========================================================================= -->
        <?php
        $tablesMap = [];
        foreach ($treeParams as $node) {
            $tIdx = !empty($node['table_index']) ? (int)$node['table_index'] : 1;
            $tablesMap[$tIdx][] = $node;
        }
        ksort($tablesMap);
        $isMultiTable = (count($tablesMap) > 1);
        ?>

        <?php foreach ($tablesMap as $tIdx => $currentTree): ?>
            <?php
            $headerMaxDepth = 0;
            foreach ($currentTree as $node) {
                $d = opGetSubtreeDepth($node, true);
                if ($d > $headerMaxDepth) $headerMaxDepth = $d;
            }
            $headerMaxDepth = max(1, $headerMaxDepth);

            $headerRows = [];
            $headerLeaves = [];
            opBuildHorizontalHeaders($currentTree, 0, $headerRows, $headerLeaves, $headerMaxDepth);
            ksort($headerRows);

            $tableGridRowNames = [];
            $firstGridLeafIdx = null;
            $gridHeaderLabel = 'Sample / Label';
            foreach ($headerLeaves as $hIdx => $hLeaf) {
                if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                    if ($firstGridLeafIdx === null) $firstGridLeafIdx = $hIdx;
                    foreach ($hLeaf['children'] as $gridChild) {
                        if (!empty($gridChild['standar_nilai'])) {
                            $gridHeaderLabel = $gridChild['standar_nilai'];
                        }
                        if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $tableGridRowNames)) {
                            $tableGridRowNames[] = $gridChild['posisi_grid'];
                        }
                    }
                }
            }
            $totalTableCols = count($headerLeaves) + (!empty($tableGridRowNames) ? 1 : 0);
            ?>

            <table class="table table-striped-columns table-bordered align-middle text-center mb-4" style="white-space: nowrap;">
                <thead align="center">
                    <?php if ($tIdx === 1): ?>
                        <tr>
                            <th colspan="<?= max(1, $totalTableCols) ?>" scope="col">
                                <?= strtoupper(esc($process_name ?? $process_code)) ?>
                            </th>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($headerRows as $level => $row): ?>
                        <tr>
                            <?php
                            $runningCol = 0;
                            $labelInserted = false;
                            foreach ($row as $col):
                                if (!empty($tableGridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx):
                                    $labelInserted = true;
                            ?>
                                    <th class="input-form align-middle" rowspan="<?= $headerMaxDepth ?>"><?= esc($gridHeaderLabel) ?></th>
                            <?php
                                endif;
                            ?>
                                <th class="input-form align-middle" colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>">
                                    <?= opRenderHeaderTitle($col['node_data']) ?>
                                    <?php if (!empty($col['node_data']['standar_nilai']) && ($col['node_data']['tipe_input'] ?? '') !== 'header_only'): ?>
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
                    <?php if (empty($tableGridRowNames)): ?>
                        <tr>
                            <?php foreach ($headerLeaves as $leaf): ?>
                                <td>
                                    <?= opRenderActiveInput($leaf, $kategori_form, $alldata ?? null, 0, $exclusiveMap, $dynamicStdTargetMap, $leaf['nama_parameter'] ?? '') ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tableGridRowNames as $rIdx => $rowName): ?>
                            <tr data-grid-row="<?= esc($rowName) ?>">
                                <?php foreach ($headerLeaves as $hIdx => $hLeaf): ?>
                                    <?php if ($hIdx === $firstGridLeafIdx): ?>
                                        <th scope="row" class="align-middle"><?= esc($rowName) ?></th>
                                    <?php endif; ?>

                                    <?php if (empty($hLeaf['children'])): ?>
                                        <?php if ($rIdx === 0): ?>
                                            <td rowspan="<?= count($tableGridRowNames) ?>" class="align-middle">
                                                <?= opRenderActiveInput($hLeaf, $kategori_form, $alldata ?? null, 0, $exclusiveMap, $dynamicStdTargetMap, $hLeaf['nama_parameter'] ?? '') ?>
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
                                            <?= $matchedCell ? opRenderActiveInput($matchedCell, $kategori_form, $alldata ?? null, $rIdx, $exclusiveMap, $dynamicStdTargetMap, $hLeaf['nama_parameter'] ?? '') : '-' ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($revision['catatan_sop'])): ?>
        <div class="note mt-3 p-3 bg-light border rounded">
            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Catatan SOP Pemeriksaan:</h6>
            <div class="small text-secondary" style="white-space: pre-line; line-height: 1.6;"><?= esc($revision['catatan_sop']) ?></div>
        </div>
    <?php endif; ?>
</div>

<script>
(function() {
    let container = document.getElementById('<?= esc($process_code) ?>');
    if (!container) return;

    // 1. FUNGSI VALIDASI RENTANG MIN / MAX & RENTANG GANDA (changeRange / changeRange2)
    function checkInputMinMax(el, triggerAlert = false) {
        if (!el || el.value === '' || el.value === '-' || el.disabled) {
            el.dataset.hasError = "0";
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
            if (typeof syncErrorCount === 'function') syncErrorCount();
            return;
        }
        let val = parseFloat(el.value);
        let rentangGandaAttr = el.getAttribute('data-rentang-ganda');

        // Validasi Rentang Ganda (Dual Range)
        if (rentangGandaAttr && rentangGandaAttr.trim() !== '') {
            let parts = [];
            try {
                if (rentangGandaAttr.startsWith('[') || rentangGandaAttr.startsWith('{')) {
                    let parsed = JSON.parse(rentangGandaAttr);
                    if (Array.isArray(parsed) && parsed.length >= 2) {
                        parts = [
                            parseFloat(parsed[0].min), parseFloat(parsed[0].max),
                            parseFloat(parsed[1].min), parseFloat(parsed[1].max)
                        ];
                    }
                } else {
                    parts = rentangGandaAttr.split(',').map(s => parseFloat(s.trim()));
                }
            } catch(e) {}

            if (parts.length === 4 && !parts.some(isNaN)) {
                let [min1, max1, min2, max2] = parts;
                let isValid = (!isNaN(val) && ((val >= min1 && val <= max1) || (val >= min2 && val <= max2)));
                if (!isValid) {
                    el.dataset.hasError = "1";
                    el.style.backgroundColor = '#ffcccc';
                    if (triggerAlert && typeof changeRange2 === 'function' && el.id) {
                        changeRange2(el.id, min1, max1, min2, max2);
                    }
                } else {
                    el.dataset.hasError = "0";
                    el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
                }
                if (typeof syncErrorCount === 'function') syncErrorCount();
                return;
            }
        }

        // Validasi Rentang Tunggal Standar
        let minAttr = el.getAttribute('data-min');
        let maxAttr = el.getAttribute('data-max');
        let min = (minAttr !== null && minAttr !== '') ? parseFloat(minAttr) : -999999999;
        let max = (maxAttr !== null && maxAttr !== '') ? parseFloat(maxAttr) : 999999999;

        if (isNaN(val) || val < min || val > max) {
            el.dataset.hasError = "1";
            el.style.backgroundColor = '#ffcccc';
        } else {
            el.dataset.hasError = "0";
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
        }

        if (typeof syncErrorCount === 'function') syncErrorCount();

        // Hanya panggil changeRange jika user selesai ketik (change)
        if (triggerAlert && typeof changeRange === 'function' && el.id && (min > -999999999 || max < 999999999)) {
            changeRange(el.id, min, max);
        }
    }

    // 2. FUNGSI GABUNG ANGKA + DROPDOWN SATUAN
    function syncUnitInputGroup(colId, isFinishedTyping = false) {
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

        // Jangan munculkan alert saat sedang mengetik! Hanya validasi saat selesai (change)
        if (isFinishedTyping) {
            checkInputMinMax(valEl, true);
        } else if (valEl.value === '') {
            valEl.style.backgroundColor = valEl.hasAttribute('readonly') ? '#e9ecef' : '';
        }
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
                    if (el.value === '' && !el.hasAttribute('readonly')) {
                        el.style.backgroundColor = '';
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
                if (inp.value !== '') {
                    checkInputMinMax(inp, false);
                }
            });
            evaluateAutoJudgments();
        }

        sel.addEventListener('change', updateStdGroup);
        if (sel.value !== '') updateStdGroup();
    });

    // 6. FUNGSI EVALUASI JUDGMENT OTOMATIS (✔ OK / ✖ NG)
    function evaluateAutoJudgments() {
        let autoJudgInputs = container.querySelectorAll('.dyn-auto-judgment');
        if (autoJudgInputs.length === 0) return;

        let isStandby = (document.getElementById('is-standby') && document.getElementById('is-standby').checked);

        autoJudgInputs.forEach(function(judgInput) {
            if (isStandby) {
                judgInput.value = '-';
                judgInput.style.backgroundColor = '#e9ecef';
                judgInput.style.color = '#495057';
                judgInput.style.borderColor = '#ced4da';
                return;
            }

            let targetFilter = (judgInput.getAttribute('data-target-eval') || '*').trim();
            let targetCols = (targetFilter === '*' || targetFilter === '') ? null : targetFilter.split(',').map(s => s.trim().toLowerCase());

            let hasNg = false;
            let allFilled = true;

            let inputsToEvaluate = [];
            container.querySelectorAll('.dyn-input-el').forEach(function(inp) {
                if (inp.classList.contains('dyn-auto-judgment')) return;
                let colName = (inp.getAttribute('name') || inp.id || '').toLowerCase();
                if (colName.endsWith('_unit')) return;
                if (targetCols && !targetCols.includes(colName)) return;
                inputsToEvaluate.push(inp);
            });

            let exclusiveGroups = {};
            inputsToEvaluate.forEach(function(inp) {
                let grp = inp.getAttribute('data-exclusive-group');
                if (grp) {
                    if (!exclusiveGroups[grp]) exclusiveGroups[grp] = [];
                    exclusiveGroups[grp].push(inp);
                }
            });

            inputsToEvaluate.forEach(function(inp) {
                if (inp.disabled) return;
                let val = (inp.value || '').trim();

                // Cek flag error atau styling merah
                if (inp.dataset.hasError === '1' || inp.style.backgroundColor === 'red' || inp.style.backgroundColor === 'rgb(255, 204, 204)' || inp.style.backgroundColor === '#ffcccc') {
                    hasNg = true;
                }

                // Cek nilai teks/dropdown NG
                if (val === '✖' || val === 'X' || val.toUpperCase().indexOf('NG') !== -1) {
                    hasNg = true;
                }

                // Cek kelengkapan input umum (jika bukan anggota grup eksklusif & bukan kolom Note opsional)
                if (!inp.getAttribute('data-exclusive-group')) {
                    let isOptionalNote = (inp.getAttribute('data-is-note') === '1' && inp.getAttribute('data-is-required') !== '1');
                    if (val === '' && !inp.hasAttribute('readonly') && !isOptionalNote) {
                        allFilled = false;
                    }
                }
            });

            // Cek kelengkapan grup eksklusif (minimal salah satu terisi)
            Object.keys(exclusiveGroups).forEach(function(grp) {
                let items = exclusiveGroups[grp];
                let anyFilled = items.some(el => !el.disabled && el.value.trim() !== '' && el.value.trim() !== '-');
                if (!anyFilled) {
                    allFilled = false;
                }
            });

            if (hasNg) {
                judgInput.value = '✖';
                judgInput.style.backgroundColor = '#f8d7da';
                judgInput.style.color = '#721c24';
                judgInput.style.borderColor = '#f5c6cb';
            } else if (allFilled && inputsToEvaluate.length > 0) {
                judgInput.value = '✔';
                judgInput.style.backgroundColor = '#d1e7dd';
                judgInput.style.color = '#0f5132';
                judgInput.style.borderColor = '#badbcc';
            } else {
                judgInput.value = '';
                judgInput.style.backgroundColor = '#f8f9fa';
                judgInput.style.color = '#6c757d';
                judgInput.style.borderColor = '#ced4da';
            }
        });
    }

    // Pasang Event Listener:
    // - Saat mengetik ('input'): HANYA gabung teks / hitung rumus / kunci pasangan (TANPA alert validasi)
    // - Saat selesai mengetik ('change'): BARU jalankan checkInputMinMax & changeRange!
    container.querySelectorAll('.dyn-unit-val, .dyn-unit-sel').forEach(function(el) {
        let colId = el.getAttribute('data-target-col');
        el.addEventListener('input', function() {
            syncUnitInputGroup(colId, false);
            enforceExclusiveGroups();
            evaluateAutoJudgments();
        });
        el.addEventListener('change', function() {
            syncUnitInputGroup(colId, true);
            enforceExclusiveGroups();
            evaluateAutoJudgments();
        });
        syncUnitInputGroup(colId, false);
    });

    container.querySelectorAll('.dyn-number-input').forEach(function(el) {
        el.addEventListener('input', function() {
            if (el.value === '' && !el.hasAttribute('readonly')) {
                el.style.backgroundColor = '';
                el.dataset.hasError = '0';
            }
            enforceExclusiveGroups();
            recalculateFormulas();
            evaluateAutoJudgments();
        });
        el.addEventListener('change', function() {
            checkInputMinMax(el, true);
            enforceExclusiveGroups();
            recalculateFormulas();
            evaluateAutoJudgments();
        });
    });

    container.querySelectorAll('.dyn-input-el:not(.dyn-number-input):not(.dyn-unit-val)').forEach(function(el) {
        el.addEventListener('change', function() {
            enforceExclusiveGroups();
            evaluateAutoJudgments();
        });
        el.addEventListener('input', function() {
            enforceExclusiveGroups();
            evaluateAutoJudgments();
        });
    });

    // Sinkronisasi penuh Mode Mesin Stand By (mengisi "-" ke parameter & "Mesin Stand By" ke kolom Note)
    function findContainerNoteElement() {
        let explicit = container.querySelector('[data-is-note="1"]');
        if (explicit) return explicit;

        let rows = container.querySelectorAll('table tbody tr');
        for (let i = rows.length - 1; i >= 0; i--) {
            let cells = rows[i].querySelectorAll('td:not(:last-child), th');
            let txt = '';
            cells.forEach(function(c) { txt += ' ' + (c.textContent || '').toLowerCase(); });
            if (txt.indexOf('note') !== -1 || txt.indexOf('catatan') !== -1) {
                let candidate = rows[i].querySelector('textarea, input[type="text"]');
                if (candidate && !candidate.classList.contains('dyn-auto-judgment')) return candidate;
            }
        }
        return null;
    }

    function applyContainerStandbyMode(isActive) {
        let noteEl = findContainerNoteElement();
        let fallbackNoteBanner = container.querySelector('.dyn-standby-note-fallback');

        if (isActive) {
            container.querySelectorAll('input, select, textarea').forEach(function(el) {
                let elType = (el.getAttribute('type') || '').toLowerCase();
                let elName = el.getAttribute('name') || '';
                if (elType === 'hidden') {
                    if (elName.indexOf('par') === 0) {
                        el.value = '-';
                    }
                    return;
                }

                if (noteEl && el === noteEl) {
                    el.readOnly = false;
                    el.disabled = false;
                    el.required = true;
                    if (!el.value.trim() || el.value.trim() === '-') {
                        el.value = 'Mesin Stand By';
                    }
                    el.style.border = '2px solid #ffc107';
                    el.style.backgroundColor = '#fffde7';
                    return;
                }

                if (el.tagName === 'SELECT') {
                    if (el.required) {
                        el.dataset.origRequired = '1';
                        el.required = false;
                    }
                    let hasDash = Array.from(el.options).some(function(o) { return o.value === '-'; });
                    if (!hasDash) {
                        let opt = document.createElement('option');
                        opt.value = '-';
                        opt.textContent = '-';
                        opt.className = 'standby-temp-opt';
                        el.appendChild(opt);
                    }
                    el.value = '-';
                    el.style.pointerEvents = 'none';
                    el.style.backgroundColor = '#f8f9fa';
                    el.dataset.hasError = '0';
                } else {
                    if (!el.dataset.origReadonlySaved) {
                        el.dataset.origReadonly = el.readOnly ? '1' : '0';
                        el.dataset.origReadonlySaved = '1';
                    }
                    if (elType === 'number' || elType === 'time' || elType === 'date') {
                        el.dataset.origType = elType;
                        el.setAttribute('type', 'text');
                    }
                    if (el.required) {
                        el.dataset.origRequired = '1';
                        el.required = false;
                    }
                    el.value = '-';
                    el.readOnly = true;
                    el.style.backgroundColor = '#f8f9fa';
                    el.style.color = '#495057';
                    el.dataset.hasError = '0';
                }
            });

            // Jika form Startup ini belum punya parameter Note di tabel, tampilkan simulasi catatan otomatis
            if (!noteEl) {
                if (!fallbackNoteBanner) {
                    fallbackNoteBanner = document.createElement('div');
                    fallbackNoteBanner.className = 'dyn-standby-note-fallback alert alert-warning py-2 px-3 mt-2 mb-0 small';
                    fallbackNoteBanner.style.border = '2px solid #ffc107';
                    fallbackNoteBanner.style.backgroundColor = '#fffde7';
                    fallbackNoteBanner.innerHTML = '<i class="fas fa-pen-nib text-warning me-1"></i> <b>Note / Catatan Otomatis (Mesin Stand By):</b> <code>Mesin Stand By</code> <span class="text-muted">(Tambahkan parameter bernama <b>Note #</b> jika ingin dapat diedit langsung oleh operator di dalam tabel)</span>';
                    container.appendChild(fallbackNoteBanner);
                } else {
                    fallbackNoteBanner.classList.remove('d-none');
                }
            }
        } else {
            if (fallbackNoteBanner) {
                fallbackNoteBanner.classList.add('d-none');
            }
            container.querySelectorAll('input, select, textarea').forEach(function(el) {
                let elType = (el.getAttribute('type') || '').toLowerCase();
                let elName = el.getAttribute('name') || '';
                if (elType === 'hidden') {
                    if (elName.indexOf('par') === 0 && el.value === '-') {
                        el.value = '';
                    }
                    return;
                }

                if (noteEl && el === noteEl) {
                    if (el.getAttribute('data-is-required') !== '1') {
                        el.required = false;
                    }
                    if (el.value === 'Mesin Stand By') {
                        el.value = '';
                    }
                    el.style.border = '';
                    el.style.backgroundColor = '';
                    return;
                }

                if (el.tagName === 'SELECT') {
                    el.querySelectorAll('.standby-temp-opt').forEach(function(o) { o.remove(); });
                    el.style.pointerEvents = '';
                    el.style.backgroundColor = '';
                    if (el.value === '-') {
                        let dashOpt = Array.from(el.options).find(function(o) { return o.value === '-'; });
                        el.value = dashOpt ? '-' : '';
                    }
                    if (el.dataset.origRequired === '1' || el.getAttribute('data-is-required') === '1') {
                        el.required = true;
                        delete el.dataset.origRequired;
                    }
                } else {
                    if (el.dataset.origType) {
                        if (el.value === '-') el.value = '';
                        el.setAttribute('type', el.dataset.origType);
                        delete el.dataset.origType;
                    } else if (el.value === '-') {
                        el.value = '';
                    }
                    let wasOrigRo = (el.dataset.origReadonly === '1' || el.classList.contains('dyn-auto-judgment') || el.hasAttribute('data-formula-type'));
                    el.readOnly = wasOrigRo;
                    el.style.backgroundColor = wasOrigRo ? '#e9ecef' : '';
                    el.style.color = '';
                    if (!wasOrigRo && (el.dataset.origRequired === '1' || el.getAttribute('data-is-required') === '1')) {
                        el.required = true;
                        delete el.dataset.origRequired;
                    }
                }
            });
            enforceExclusiveGroups();
            recalculateFormulas();
        }
        syncStandbyRequired();
        evaluateAutoJudgments();
    }

    // Sinkronisasi atribut required saat mode Mesin Stand By aktif/nonaktif
    function syncStandbyRequired() {
        let isStandby = !!(document.getElementById('is-standby') && document.getElementById('is-standby').checked);
        container.querySelectorAll('[data-is-required="1"]').forEach(function(el) {
            if (isStandby) {
                el.removeAttribute('required');
            } else if (!el.disabled && !el.readOnly) {
                el.setAttribute('required', 'required');
            }
        });
    }

    let standbyEl = document.getElementById('is-standby');
    if (standbyEl) {
        standbyEl.addEventListener('change', function() {
            if (!document.body.contains(container)) return;
            if (!document.getElementById('form-input')) {
                applyContainerStandbyMode(standbyEl.checked);
            } else {
                syncStandbyRequired();
                evaluateAutoJudgments();
            }
        });
        if (standbyEl.checked && !document.getElementById('form-input')) {
            applyContainerStandbyMode(true);
        }
    }

    enforceExclusiveGroups();
    syncStandbyRequired();
    recalculateFormulas();
    evaluateAutoJudgments();
})();
</script>