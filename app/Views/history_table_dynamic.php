<?php
// --- HELPER REKURSIF UNTUK TABEL HISTORY DINAMIS (WEB, EXCEL, & PDF) ---
if (!function_exists('histPrepareTree')) {
    function histPrepareTree(array $nodes): array {
        $result = [];
        foreach ($nodes as $node) {
            if (!empty($node['children'])) {
                $node['children'] = histPrepareTree($node['children']);
            }
            if (!empty($node['standar_dinamis']) && !empty($node['children'])) {
                $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
                if (is_array($dyn) && !empty($dyn['options'])) {
                    $stdCol = [
                        'id'              => 'std_' . $node['id'],
                        'parent_id'       => $node['id'],
                        'nama_parameter'  => 'Std (Min)',
                        'kolom_db'        => $dyn['db_col'] ?? 'std_die_bond',
                        'tipe_input'      => 'std_display',
                        'std_suffix'      => $dyn['suffix_text'] ?? ' N',
                        'batas_min'       => null,
                        'batas_max'       => null,
                        'satuan_opsi'     => null,
                        'standar_dinamis' => null,
                        'posisi_grid'     => null,
                        'children'        => []
                    ];
                    array_unshift($node['children'], $stdCol);
                }
            }
            $result[] = $node;
        }
        return $result;
    }
}

if (!function_exists('histCountLeaves')) {
    function histCountLeaves($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $count = 0;
        foreach ($node['children'] as $child) {
            $count += histCountLeaves($child, $ignoreGridChildren);
        }
        return $count;
    }
}

if (!function_exists('histGetSubtreeDepth')) {
    function histGetSubtreeDepth($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $max = 0;
        foreach ($node['children'] as $child) {
            $d = histGetSubtreeDepth($child, $ignoreGridChildren);
            if ($d > $max) $max = $d;
        }
        return $max + 1;
    }
}

if (!function_exists('histFormatCellValue')) {
    function histFormatCellValue($row, $leaf, $isExport = false) {
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));
        if ($colDb === '') return '-';

        if (($leaf['tipe_input'] ?? '') === 'std_display') {
            $rawStd = isset($row[$colDb]) ? trim((string)$row[$colDb]) : '';
            if ($rawStd === '' || $rawStd === '-') return '-';
            $suffix = $leaf['std_suffix'] ?? ' N';
            if (stripos($rawStd, trim($suffix)) === false) {
                return esc($rawStd . $suffix);
            }
            return esc($rawStd);
        }

        $rawVal = isset($row[$colDb]) ? trim((string)$row[$colDb]) : '';
        if ($rawVal === '') return '-';

        if (!$isExport && ($leaf['tipe_input'] ?? '') === 'number' && $rawVal !== '-') {
            $min = ($leaf['batas_min'] !== null && $leaf['batas_min'] !== '') ? (float)$leaf['batas_min'] : null;
            $max = ($leaf['batas_max'] !== null && $leaf['batas_max'] !== '') ? (float)$leaf['batas_max'] : null;

            $parts = explode(' ', $rawVal, 2);
            $numPart = $parts[0] ?? '';
            $unitPart = isset($parts[1]) ? trim($parts[1]) : '';

            if ($unitPart !== '' && !empty($leaf['satuan_opsi'])) {
                $units = is_string($leaf['satuan_opsi']) ? json_decode($leaf['satuan_opsi'], true) : $leaf['satuan_opsi'];
                if (is_array($units)) {
                    foreach ($units as $u) {
                        if (isset($u['unit']) && strtolower(trim($u['unit'])) === strtolower($unitPart)) {
                            $min = ($u['min'] !== null && $u['min'] !== '') ? (float)$u['min'] : $min;
                            $max = ($u['max'] !== null && $u['max'] !== '') ? (float)$u['max'] : $max;
                            break;
                        }
                    }
                }
            }

            if (is_numeric($numPart)) {
                $fVal = (float)$numPart;
                if (($min !== null && $fVal < $min) || ($max !== null && $fVal > $max)) {
                    return '<span style="color: #dc3545; font-weight: bold;">' . esc($rawVal) . '</span>';
                }
            }
        }

        return esc($rawVal);
    }
}

if (!function_exists('histBuildStartupRows')) {
    function histBuildStartupRows($nodes, $currentCol, $maxDepth, &$verticalRows, &$rowIndex, $isRootLevel = false) {
        $no = 1;
        foreach ($nodes as $node) {
            if (!isset($verticalRows[$rowIndex])) $verticalRows[$rowIndex] = [];

            $isNoteRow = ($isRootLevel && empty($node['children']) && stripos($node['nama_parameter'], 'note') !== false);
            if ($isNoteRow) {
                $verticalRows[$rowIndex][] = [
                    'type'      => 'note_label',
                    'name'      => 'Note',
                    'colspan'   => $maxDepth + 1,
                    'node_data' => $node
                ];
                $verticalRows[$rowIndex][] = [
                    'type'      => 'input',
                    'node_data' => $node
                ];
                $rowIndex++;
                continue;
            }

            $rowspan  = histCountLeaves($node);
            $subDepth = histGetSubtreeDepth($node);
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
                histBuildStartupRows($node['children'], $currentCol + $colspan, $maxDepth, $verticalRows, $rowIndex, false);
            }
        }
    }
}

if (!function_exists('histBuildHorizontalHeaders')) {
    function histBuildHorizontalHeaders($nodes, $level, &$headerRows, &$headerLeaves, $maxDepth) {
        if (!isset($headerRows[$level])) $headerRows[$level] = [];
        foreach ($nodes as $node) {
            $hasGridChildren = (!empty($node['children']) && !empty($node['children'][0]['posisi_grid']));
            $isHeaderLeaf    = (empty($node['children']) || $hasGridChildren);

            $colspan  = histCountLeaves($node, true);
            $subDepth = histGetSubtreeDepth($node, true);
            $rowspan  = max(1, ($maxDepth - $level) - ($subDepth - 1));

            $headerDisplay = $node['nama_parameter'];
            if (!empty($node['standar_dinamis'])) {
                $headerDisplay = 'OK:✓ / NG:X';
            }

            $headerRows[$level][] = [
                'name'      => $headerDisplay,
                'colspan'   => $colspan,
                'rowspan'   => $rowspan,
                'node_data' => $node
            ];

            if ($isHeaderLeaf) {
                $headerLeaves[] = $node;
            } else {
                histBuildHorizontalHeaders($node['children'], $level + $rowspan, $headerRows, $headerLeaves, $maxDepth);
            }
        }
    }
}

$isExportMode = !empty($is_export);
$preparedTree = histPrepareTree($treeParams ?? []);
$lenData      = count($alldata ?? []);
?>

<div class="table-responsive mt-3" id="<?= esc($process_code) ?>">
    <?= csrf_field(); ?>
    <input type="hidden" id="cnt-table" value="1">

    <?php if ($kategori_form === 'startup'): ?>
        <!-- ================================================================= -->
        <!-- TABEL HISTORY 1: STARTUP (VERTIKAL, TANGGAL MENYAMPING KE KANAN)  -->
        <!-- ================================================================= -->
        <?php
        $maxDepth = 0;
        foreach ($preparedTree as $n) {
            $d = histGetSubtreeDepth($n);
            if ($d > $maxDepth) $maxDepth = $d;
        }
        $maxDepth = max(2, $maxDepth);
        $identityCols = $maxDepth + 1;
        $dataColsSpan = max(1, $lenData);

        $verticalRows = [];
        $rowIndex = 0;
        histBuildStartupRows($preparedTree, 0, $maxDepth, $verticalRows, $rowIndex, true);

        // Gabungkan sel Standard yang kembar berurutan dalam 1 nomor item yang sama
        $totalVRows = count($verticalRows);
        for ($r = 0; $r < $totalVRows; $r++) {
            if (isset($verticalRows[$r][0]['type']) && $verticalRows[$r][0]['type'] === 'no' && $verticalRows[$r][0]['rowspan'] > 1) {
                $grpEnd = min($totalVRows - 1, $r + $verticalRows[$r][0]['rowspan'] - 1);
                $anchorR = null;
                $anchorIdx = null;
                $anchorName = null;

                for ($sr = $r; $sr <= $grpEnd; $sr++) {
                    $lastParamIdx = null;
                    foreach ($verticalRows[$sr] as $cIdx => $cellItem) {
                        if ($cellItem['type'] === 'param') $lastParamIdx = $cIdx;
                    }
                    if ($lastParamIdx === null) continue;
                    $pCell = $verticalRows[$sr][$lastParamIdx];

                    if ($pCell['rowspan'] === 1 && $pCell['colspan'] === 1 && strlen(trim($pCell['name'])) > 12) {
                        if ($anchorR !== null && trim($pCell['name']) === $anchorName) {
                            $verticalRows[$anchorR][$anchorIdx]['rowspan']++;
                            unset($verticalRows[$sr][$lastParamIdx]);
                            $verticalRows[$sr] = array_values($verticalRows[$sr]);
                        } else {
                            $anchorR = $sr;
                            $anchorIdx = $lastParamIdx;
                            $anchorName = trim($pCell['name']);
                        }
                    } else {
                        $anchorR = null;
                    }
                }
            }
        }

        $hasNoteRow = false;
        foreach ($verticalRows as $vr) {
            if (isset($vr[0]['type']) && $vr[0]['type'] === 'note_label') {
                $hasNoteRow = true;
                break;
            }
        }
        ?>
        <table class="table table-bordered table-hover" id="table1">
            <thead align="center">
                <tr>
                    <th colspan="<?= $identityCols + $dataColsSpan ?>" scope="col">
                        <?= strtoupper(esc($judul_proses ?? $process_code)) ?> (REVISI <?= esc($revisi_label ?? '00') ?>)
                    </th>
                </tr>
                <tr>
                    <th scope="col" rowspan="2">No</th>
                    <th scope="col" colspan="<?= max(1, $maxDepth - 1) ?>" rowspan="2">Start Up Check Item</th>
                    <th scope="col" rowspan="2">Standard</th>
                    <th scope="col" colspan="<?= $dataColsSpan ?>">Hasil Start Up Check</th>
                </tr>
                <tr>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): ?>
                            <td><?= !empty($alldata[$i]['created_at']) && $alldata[$i]['created_at'] !== '-' ? date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])) : '-' ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td>-</td>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($verticalRows as $r): ?>
                    <tr>
                        <?php foreach ($r as $cell): ?>
                            <?php if ($cell['type'] === 'no'): ?>
                                <td rowspan="<?= $cell['rowspan'] ?>" align="center"><?= $cell['no'] ?></td>
                            <?php elseif ($cell['type'] === 'note_label'): ?>
                                <td colspan="<?= $cell['colspan'] ?>"><?= esc($cell['name']) ?></td>
                            <?php elseif ($cell['type'] === 'param'): ?>
                                <td rowspan="<?= $cell['rowspan'] ?>" colspan="<?= $cell['colspan'] ?>">
                                    <?= esc($cell['name']) ?>
                                </td>
                            <?php elseif ($cell['type'] === 'input'): ?>
                                <?php if ($lenData > 0): ?>
                                    <?php for ($i = 0; $i < $lenData; $i++): ?>
                                        <td align="center"><?= histFormatCellValue($alldata[$i], $cell['node_data'], $isExportMode) ?></td>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <td align="center">-</td>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$hasNoteRow): ?>
                    <tr>
                        <td colspan="<?= $identityCols ?>">Note</td>
                        <?php if ($lenData > 0): ?>
                            <?php for ($i = 0; $i < $lenData; $i++): ?>
                                <td align="center"><?= esc($alldata[$i]['par030'] ?? $alldata[$i]['par023'] ?? '-') ?></td>
                            <?php endfor; ?>
                        <?php else: ?>
                            <td align="center">-</td>
                        <?php endif; ?>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td colspan="<?= $identityCols ?>">Status Approval</td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): ?>
                            <td align="center"><?= esc((string)($alldata[$i]['status'] ?? '-')) ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td colspan="<?= $identityCols ?>">Operator</td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): ?>
                            <?php
                            $opDisplay = '-';
                            $roleKey = strtolower(trim((string)($alldata[$i]['role'] ?? '')));
                            if ($roleKey && $roleKey !== '-' && !empty($alldata[$i][$roleKey]) && $alldata[$i][$roleKey] !== '-') {
                                $opDisplay = (string)$alldata[$i][$roleKey];
                            } elseif (!empty($alldata[$i]['operator']) && $alldata[$i]['operator'] !== '-') {
                                $opDisplay = (string)$alldata[$i]['operator'];
                            } elseif (!empty($alldata[$i]['name']) && $alldata[$i]['name'] !== '-') {
                                $opDisplay = (string)$alldata[$i]['name'];
                            } elseif (!empty($alldata[$i]['nama']) && $alldata[$i]['nama'] !== '-') {
                                $opDisplay = (string)$alldata[$i]['nama'];
                            }
                            ?>
                            <td align="center"><?= esc($opDisplay) ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td colspan="<?= $identityCols ?>">Time</td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): ?>
                            <td align="center"><?= (!empty($alldata[$i]['updated_at']) && $alldata[$i]['updated_at'] !== '-') ? date('H:i', strtotime((string)$alldata[$i]['updated_at'])) : '-' ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>

    <?php else: ?>
        <!-- ================================================================= -->
        <!-- TABEL HISTORY 2 & 3: PRODUCTION & FOREGOING (HORIZONTAL)          -->
        <!-- ================================================================= -->
        <?php
        $headerMaxDepth = 0;
        foreach ($preparedTree as $node) {
            $d = histGetSubtreeDepth($node, true);
            if ($d > $headerMaxDepth) $headerMaxDepth = $d;
        }
        $headerMaxDepth = max(1, $headerMaxDepth);

        $headerRows = [];
        $headerLeaves = [];
        histBuildHorizontalHeaders($preparedTree, 0, $headerRows, $headerLeaves, $headerMaxDepth);
        ksort($headerRows);

        // Deteksi baris matriks Foregoing (X, Y, Z, θ) & Nama Kolom Header Dinamis (Misal: "Pos")
        $gridRowNames     = [];
        $firstGridLeafIdx = null;
        $hasSuffixCols    = false;
        $gridHeaderLabel  = 'Sample / Label';

        foreach ($headerLeaves as $hIdx => $hLeaf) {
            if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                if ($firstGridLeafIdx === null) $firstGridLeafIdx = $hIdx;
                foreach ($hLeaf['children'] as $gridChild) {
                    if (!empty($gridChild['standar_nilai'])) {
                        $gridHeaderLabel = $gridChild['standar_nilai'];
                    }
                    if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $gridRowNames)) {
                        $gridRowNames[] = $gridChild['posisi_grid'];
                    }
                    if (!empty($gridChild['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $gridChild['kolom_db'])) {
                        $hasSuffixCols = true;
                    }
                }
            }
        }

        $hasTimeInTree = false;
        foreach ($headerLeaves as $hl) {
            if (in_array($hl['kolom_db'] ?? '', ['par008', 'par009'])) {
                $hasTimeInTree = true;
                break;
            }
        }
        $showAutoTimeCols = ($kategori_form === 'production' && !$hasTimeInTree);

        $metaColCount   = ($kategori_form === 'production') ? 9 : 7;
        $extraTimeCols  = $showAutoTimeCols ? 2 : 0;
        $extraLabelCol  = !empty($gridRowNames) ? 1 : 0;
        $totalTableCols = $metaColCount + $extraTimeCols + $extraLabelCol + count($headerLeaves);
        ?>
        <table class="table table-bordered table-hover" id="table1">
            <thead align="center">
                <tr>
                    <th colspan="<?= $totalTableCols ?>" scope="col">
                        <?= strtoupper(esc($judul_proses ?? $process_code)) ?> (REVISI <?= esc($revisi_label ?? '00') ?>)
                    </th>
                </tr>
                <?php foreach ($headerRows as $level => $row): ?>
                    <tr>
                        <?php if ($level === 0): ?>
                            <?php if ($kategori_form === 'production'): ?>
                                <th rowspan="<?= $headerMaxDepth ?>">No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Operator Start</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Operator Finish</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Mch. No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Date</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Model</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Lot No.</th>
                                <?php if ($showAutoTimeCols): ?>
                                    <th colspan="2" rowspan="<?= max(1, $headerMaxDepth - 1) ?>">Time</th>
                                <?php endif; ?>
                            <?php else: ?>
                                <th rowspan="<?= $headerMaxDepth ?>">No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Date</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Mch. No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Model</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Lot No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Operator</th>
                            <?php endif; ?>
                        <?php elseif ($showAutoTimeCols && $level === $headerMaxDepth - 1): ?>
                            <th>IN</th>
                            <th>OUT</th>
                        <?php endif; ?>

                        <?php
                        $runningCol = 0;
                        $labelInserted = false;
                        foreach ($row as $col):
                            if (!empty($gridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx):
                                $labelInserted = true;
                        ?>
                                <th rowspan="<?= $headerMaxDepth ?>"><?= esc($gridHeaderLabel) ?></th>
                        <?php
                            endif;
                        ?>
                            <th colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>">
                                <?= esc($col['name']) ?>
                            </th>
                        <?php
                            $runningCol += $col['colspan'];
                        endforeach;
                        ?>
                    </tr>
                <?php endforeach; ?>
            </thead>

            <tbody>
                <?php if ($lenData === 0): ?>
                    <tr>
                        <?php for ($c = 0; $c < $totalTableCols; $c++): ?>
                            <td align="center">-</td>
                        <?php endfor; ?>
                    </tr>
                <?php elseif (empty($gridRowNames)): ?>
                    <!-- BARIS TUNGGAL PER TRANSAKSI (PRODUCTION) -->
                    <?php for ($i = 0; $i < $lenData; $i++): ?>
                        <?php $row = $alldata[$i]; ?>
                        <tr>
                            <td align="center"><?= $i + 1 ?></td>
                            <?php if ($kategori_form === 'production'): ?>
                                <td><?= esc($row['name'] ?? '-') ?></td>
                                <td align="center"><?= esc(($row['shift'] ?? '-') . ' / ' . ($row['group'] ?? '-')) ?></td>
                                <td><?= !empty($row['name2']) && $row['name2'] !== '-' ? esc($row['name2']) : '-' ?></td>
                                <td align="center"><?= (!empty($row['shift2']) && $row['shift2'] !== '-') ? esc($row['shift2'] . ' / ' . ($row['group2'] ?? '-')) : '-' ?></td>
                                <td align="center"><?= esc((string)($row['machno'] ?? '-')) ?></td>
                                <td align="center"><?= (!empty($row['created_at']) && $row['created_at'] !== '-') ? date('d/m/Y', strtotime((string)$row['created_at'])) : '-' ?></td>
                                <td align="center"><?= esc((string)($row['model'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['lotno'] ?? '-')) ?></td>
                                <?php if ($showAutoTimeCols): ?>
                                    <td align="center"><?= (!empty($row['par008']) && $row['par008'] !== '-') ? date('H:i', strtotime((string)$row['par008'])) : '-' ?></td>
                                    <td align="center"><?= (!empty($row['par009']) && $row['par009'] !== '-') ? date('H:i', strtotime((string)$row['par009'])) : '-' ?></td>
                                <?php endif; ?>
                            <?php else: ?>
                                <td align="center"><?= (!empty($row['created_at']) && $row['created_at'] !== '-') ? date('d/m/Y', strtotime((string)$row['created_at'])) : '-' ?></td>
                                <td align="center"><?= esc((string)($row['machno'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['model'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['lotno'] ?? '-')) ?></td>
                                <td align="center"><?= esc(($row['shift'] ?? '-') . ' / ' . ($row['group'] ?? '-')) ?></td>
                                <td><?= esc($row['name'] ?? $row['operator'] ?? '-') ?></td>
                            <?php endif; ?>

                            <?php foreach ($headerLeaves as $leaf): ?>
                                <td align="center"><?= histFormatCellValue($row, $leaf, $isExportMode) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>

                <?php else: ?>
                    <!-- BARIS MATRIKS (FOREGOING) -->
                    <?php
                    $gridCount = count($gridRowNames);
                    $isSplitRowsInDb = ($hasSuffixCols && !isset($alldata[0]['par001a']) && !isset($alldata[0]['par013a']) && !isset($alldata[0]['par021a']));
                    $step = $isSplitRowsInDb ? max(1, $gridCount) : 1;
                    $noUrut = 1;
                    ?>
                    <?php for ($i = 0; $i < $lenData; $i += $step): ?>
                        <?php $baseRow = $alldata[$i]; ?>
                        <?php foreach ($gridRowNames as $rIdx => $rowName): ?>
                            <?php
                            $activeRow = ($isSplitRowsInDb && isset($alldata[$i + $rIdx])) ? $alldata[$i + $rIdx] : $baseRow;
                            ?>
                            <tr>
                                <?php if ($rIdx === 0): ?>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= $noUrut++ ?></td>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= (!empty($baseRow['created_at']) && $baseRow['created_at'] !== '-') ? date('d/m/Y', strtotime((string)$baseRow['created_at'])) : '-' ?></td>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['machno'] ?? '-')) ?></td>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['model'] ?? '-')) ?></td>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['lotno'] ?? '-')) ?></td>
                                    <td rowspan="<?= $gridCount ?>" align="center"><?= esc(($baseRow['shift'] ?? '-') . ' / ' . ($baseRow['group'] ?? '-')) ?></td>
                                    <td rowspan="<?= $gridCount ?>"><?= esc($baseRow['name'] ?? $baseRow['operator'] ?? '-') ?></td>
                                <?php endif; ?>

                                <?php foreach ($headerLeaves as $hIdx => $hLeaf): ?>
                                    <?php if ($hIdx === $firstGridLeafIdx): ?>
                                        <th scope="row" align="center"><?= esc($rowName) ?></th>
                                    <?php endif; ?>

                                    <?php if (empty($hLeaf['children'])): ?>
                                        <?php if ($rIdx === 0): ?>
                                            <td rowspan="<?= $gridCount ?>" align="center">
                                                <?= histFormatCellValue($baseRow, $hLeaf, $isExportMode) ?>
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
                                        if ($matchedCell) {
                                            $cellCopy = $matchedCell;
                                            $colKey = $cellCopy['kolom_db'] ?? '';
                                            if (!isset($activeRow[$colKey]) && preg_match('/^(par\d+)[a-z]$/i', $colKey, $mBase)) {
                                                $cellCopy['kolom_db'] = strtolower($mBase[1]);
                                            }
                                            $valOut = histFormatCellValue($activeRow, $cellCopy, $isExportMode);
                                        } else {
                                            $valOut = '-';
                                        }
                                        ?>
                                        <td align="center"><?= $valOut ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>