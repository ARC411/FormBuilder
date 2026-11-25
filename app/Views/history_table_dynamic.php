<?php
// --- HELPER REKURSIF UNTUK TABEL HISTORY DINAMIS (WEB, EXCEL, & PDF) ---
if (!function_exists('histResolveEmpId')) {
    /**
     * Resolusi nama karyawan atau raw ID menjadi EmpID resmi dari database secondary (.130 tbempinfa)
     */
    function histResolveEmpId($val) {
        static $empMap = null;
        $clean = trim((string)$val);
        if ($clean === '' || $clean === '-') {
            return '-';
        }

        $roleSuffix = '';
        if (preg_match('/^(.*?)\s*(\(.*?\))$/', $clean, $m)) {
            $clean = trim($m[1]);
            $roleSuffix = ' ' . trim($m[2]);
        }

        // Jika nilai sudah berupa Employee ID numerik/kode (misal: 001955, S106458, OP001)
        if (preg_match('/^[0-9A-Za-z_-]{3,12}$/', $clean) && preg_match('/\d/', $clean)) {
            return $clean . $roleSuffix;
        }

        if ($empMap === null) {
            $empMap = [];
            // 1. Coba ambil dari Secondary Database (SQL Server 192.168.132.130 tbempinfa)
            try {
                if (extension_loaded('sqlsrv') && function_exists('sqlsrv_connect')) {
                    $db2 = \Config\Database::connect('second');
                    $rows = $db2->query("SELECT empid, firstname, midname, lastname FROM tbempinfa")->getResultArray();
                    foreach ($rows as $r) {
                        $eid = trim((string)($r['empid'] ?? ''));
                        if ($eid === '') continue;
                        $fn = strtoupper(trim((string)($r['firstname'] ?? '')));
                        $mn = strtoupper(trim((string)($r['midname'] ?? '')));
                        $ln = strtoupper(trim((string)($r['lastname'] ?? '')));
                        $full = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $mn $ln")));
                        if ($full !== '') $empMap[$full] = $eid;
                        $fl = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $ln")));
                        if ($fl !== '' && !isset($empMap[$fl])) $empMap[$fl] = $eid;
                        if ($fn !== '' && !isset($empMap[$fn])) $empMap[$fn] = $eid;
                    }
                }
            } catch (\Throwable $e) {}

            // 2. Fallback dari MySQL default jika ada tbempinfa atau tabel users lokal
            try {
                $db = \Config\Database::connect('default');
                if ($db->tableExists('tbempinfa')) {
                    $rows = $db->table('tbempinfa')->select('empid, firstname, midname, lastname')->get()->getResultArray();
                    foreach ($rows as $r) {
                        $eid = trim((string)($r['empid'] ?? ''));
                        if ($eid === '') continue;
                        $fn = strtoupper(trim((string)($r['firstname'] ?? '')));
                        $mn = strtoupper(trim((string)($r['midname'] ?? '')));
                        $ln = strtoupper(trim((string)($r['lastname'] ?? '')));
                        $full = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $mn $ln")));
                        if ($full !== '') $empMap[$full] = $eid;
                        $fl = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $ln")));
                        if ($fl !== '' && !isset($empMap[$fl])) $empMap[$fl] = $eid;
                        if ($fn !== '' && !isset($empMap[$fn])) $empMap[$fn] = $eid;
                    }
                }
                if ($db->tableExists('users')) {
                    $uRows = $db->table('users')->select('username, name')->get()->getResultArray();
                    foreach ($uRows as $ur) {
                        $uname = trim((string)($ur['username'] ?? ''));
                        $fn = strtoupper(trim((string)($ur['name'] ?? '')));
                        if ($uname !== '' && $fn !== '' && !isset($empMap[$fn])) {
                            $empMap[$fn] = $uname;
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        $key = strtoupper((string)preg_replace('/\s+/', ' ', $clean));
        $resolved = $empMap[$key] ?? $clean;
        return $resolved . $roleSuffix;
    }
}

if (!function_exists('histFormatCheckedEmpId')) {
    /**
     * Resolusi siapa yang menyetujui (Supervisor / Leader / Foreman) ke dalam format EmpID (Jabatan)
     */
    function histFormatCheckedEmpId($row) {
        if (!is_array($row)) return '-';
        $rawStatus = strtolower(trim((string)($row['status'] ?? '')));
        if ($rawStatus === '' || $rawStatus === '-' || (strpos($rawStatus, 'approve') === false && strpos($rawStatus, 'bypass') === false && strpos($rawStatus, 'check') === false)) {
            return '-';
        }
        $spv = trim((string)($row['supervisor'] ?? ''));
        $ldr = trim((string)($row['leader'] ?? ''));
        $frm = trim((string)($row['foreman'] ?? ''));
        if ($spv !== '' && $spv !== '-') {
            return histResolveEmpId($spv) . ' (Supervisor)';
        }
        if ($ldr !== '' && $ldr !== '-') {
            return histResolveEmpId($ldr) . ' (Leader)';
        }
        if ($frm !== '' && $frm !== '-') {
            return histResolveEmpId($frm) . ' (Foreman)';
        }
        return '-';
    }
}

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
        // Jika kolom teks statis (header_only seperti pada Burn In)
        if (($leaf['tipe_input'] ?? '') === 'header_only') {
            return !empty($leaf['standar_nilai']) ? nl2br(esc($leaf['standar_nilai'])) : '-';
        }

        $colDb = trim((string)($leaf['kolom_db'] ?? ''));
        if ($colDb === '') {
            return !empty($leaf['standar_nilai']) ? nl2br(esc($leaf['standar_nilai'])) : '-';
        }

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

        // Format Tampilan Auto Judgment di History
        if (($leaf['tipe_input'] ?? '') === 'auto_judgment') {
            if ($rawVal === '✔' || $rawVal === 'OK') {
                return $isExport ? 'OK (✔)' : '<span class="badge bg-success text-white" style="font-size: 0.95rem;">✔</span>';
            } elseif ($rawVal === '✖' || $rawVal === 'NG' || $rawVal === 'X') {
                return $isExport ? 'NG (✖)' : '<span class="badge bg-danger text-white" style="font-size: 0.95rem;">✖</span>';
            }
            return esc($rawVal);
        }

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
                $isOutOfRange = false;

                // Cek jika ada rentang ganda
                if (!empty($leaf['rentang_ganda'])) {
                    $rgParts = [];
                    try {
                        $rgRaw = $leaf['rentang_ganda'];
                        if (is_string($rgRaw) && (str_starts_with($rgRaw, '[') || str_starts_with($rgRaw, '{'))) {
                            $parsedRg = json_decode($rgRaw, true);
                            if (is_array($parsedRg) && count($parsedRg) >= 2) {
                                $rgParts = [
                                    (float)$parsedRg[0]['min'], (float)$parsedRg[0]['max'],
                                    (float)$parsedRg[1]['min'], (float)$parsedRg[1]['max']
                                ];
                            }
                        } elseif (is_string($rgRaw)) {
                            $rgParts = array_map('floatval', explode(',', $rgRaw));
                        }
                    } catch(\Throwable $e) {}

                    if (count($rgParts) === 4) {
                        if (!(($fVal >= $rgParts[0] && $fVal <= $rgParts[1]) || ($fVal >= $rgParts[2] && $fVal <= $rgParts[3]))) {
                            $isOutOfRange = true;
                        }
                    }
                } elseif (($min !== null && $fVal < $min) || ($max !== null && $fVal > $max)) {
                    $isOutOfRange = true;
                }

                if ($isOutOfRange) {
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

            $isNoteRow = (empty($node['children']) && (stripos($node['nama_parameter'], 'note') !== false || stripos($node['nama_parameter'], 'catatan') !== false));
            if ($isNoteRow) {
                // Note diposisikan selalu di baris paling bawah tabel, jangan dirender di tengah-tengah parameter
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
        <!-- TABEL HISTORY 1: STARTUP MENURUN (VERTIKAL)                       -->
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

        $noteDbCol = null;
        foreach ($preparedTree as $n) {
            if ((stripos($n['nama_parameter'] ?? '', 'note') !== false || stripos($n['nama_parameter'] ?? '', 'catatan') !== false) && !empty($n['kolom_db'])) {
                $noteDbCol = $n['kolom_db'];
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

                <!-- 1. Judgment Start Up * -->
                <tr>
                    <td colspan="<?= $identityCols ?>"><strong>Judgment Start Up *</strong></td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): 
                            $judg = $alldata[$i]['par025'] ?? $alldata[$i]['judgment'] ?? '';
                            if ($judg === '' || $judg === '-') {
                                $hasNg = false;
                                foreach ($alldata[$i] as $k => $v) {
                                    if (str_starts_with($k, 'par')) {
                                        $vUpper = strtoupper(trim((string)$v));
                                        if ($vUpper === 'NG' || $vUpper === '✖' || $vUpper === 'X') {
                                            $hasNg = true;
                                            break;
                                        }
                                    }
                                }
                                $judg = $hasNg ? '✖' : '✔';
                            }
                            $judgDisplay = ($judg === '✔' || strtoupper($judg) === 'OK') ? ($isExportMode ? 'OK (✔)' : '<strong style="color: #198754; font-size: 1.1rem;">&#10004;</strong>') : ($isExportMode ? 'NG (✖)' : '<strong style="color: #dc3545; font-size: 1.1rem;">&#10008;</strong>');
                        ?>
                            <td align="center"><?= $judgDisplay ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>

                <!-- 2. Operator (by EmpID dari Secondary Database .130) -->
                <tr>
                    <td colspan="<?= $identityCols ?>"><strong>Operator</strong></td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): 
                            $rawOp = !empty($alldata[$i]['empid']) ? $alldata[$i]['empid'] : ($alldata[$i]['name'] ?? $alldata[$i]['operator'] ?? $alldata[$i]['nama'] ?? '-');
                        ?>
                            <td align="center"><?= esc(histResolveEmpId($rawOp)) ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>

                <!-- 3. Checked (by EmpID + Role) -->
                <tr>
                    <td colspan="<?= $identityCols ?>"><strong>Checked</strong></td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): ?>
                            <td align="center"><?= esc(histFormatCheckedEmpId($alldata[$i] ?? null)) ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>

                <!-- 4. Note # (selalu berada di baris paling bawah) -->
                <tr>
                    <td colspan="<?= $identityCols ?>">Note #</td>
                    <?php if ($lenData > 0): ?>
                        <?php for ($i = 0; $i < $lenData; $i++): 
                            $noteVal = $alldata[$i]['note'] ?? $alldata[$i]['par030'] ?? $alldata[$i]['par023'] ?? $alldata[$i]['catatan'] ?? '-';
                            if (($noteVal === '' || $noteVal === '-') && !empty($noteDbCol)) {
                                $noteVal = $alldata[$i][$noteDbCol] ?? '-';
                            }
                        ?>
                            <td align="center"><?= esc($noteVal !== '' ? $noteVal : '-') ?></td>
                        <?php endfor; ?>
                    <?php else: ?>
                        <td align="center">-</td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>

    <?php else: ?>
        <!-- ========================================================================= -->
        <!-- TABEL HISTORY 2 & 3: STARTUP MENDATAR, PRODUCTION, & FOREGOING            -->
        <!-- ========================================================================= -->
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
        $gridHeaderLabel  = ($kategori_form === 'foregoing' || str_contains($process_code ?? '', '-f-')) ? 'Measure Die Bond Pos' : 'Sample / Label';

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
        $showFinishCols   = !empty($show_finish_cols) && ($kategori_form === 'production');

        $metaColCount   = ($kategori_form === 'production') ? ($showFinishCols ? 9 : 7) : (($kategori_form === 'startup_horizontal') ? 6 : 7);
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
                                <?php if ($showFinishCols): ?>
                                    <th rowspan="<?= $headerMaxDepth ?>">Operator Start</th>
                                    <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                    <th rowspan="<?= $headerMaxDepth ?>">Operator Finish</th>
                                    <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <?php else: ?>
                                    <th rowspan="<?= $headerMaxDepth ?>">Operator</th>
                                    <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <?php endif; ?>
                                <th rowspan="<?= $headerMaxDepth ?>">Mch. No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Model</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Lot No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Date (dd/mm/yyyy)</th>
                                <?php if ($showAutoTimeCols): ?>
                                    <th colspan="2" rowspan="<?= max(1, $headerMaxDepth - 1) ?>">Time</th>
                                <?php endif; ?>
                            <?php elseif ($kategori_form === 'startup_horizontal'): ?>
                                <th rowspan="<?= $headerMaxDepth ?>">No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Date</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Mch. No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Operator</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Status Approval</th>
                            <?php else: ?>
                                <th rowspan="<?= $headerMaxDepth ?>">No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Mch. No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Model</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Lot No.</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Operator</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Shift/Group</th>
                                <th rowspan="<?= $headerMaxDepth ?>">Date (dd/mm/yyyy)</th>
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
                    <!-- BARIS TUNGGAL PER TRANSAKSI (PRODUCTION & STARTUP MENDATAR) -->
                    <?php for ($i = 0; $i < $lenData; $i++): ?>
                        <?php $row = $alldata[$i]; ?>
                        <tr>
                            <td align="center"><?= $i + 1 ?></td>
                            <?php if ($kategori_form === 'production'): ?>
                                <?php
                                $op1Raw = !empty($row['empid']) ? $row['empid'] : (!empty($row['name']) ? $row['name'] : ($row['operator'] ?? '-'));
                                $op1Emp = histResolveEmpId($op1Raw);

                                // Pertahankan logic Operator Finish & Shift/Group Finish
                                $op2Raw = !empty($row['empid2']) ? $row['empid2'] : ($row['name2'] ?? '');
                                $op2Emp = ($op2Raw !== '' && $op2Raw !== '-') ? histResolveEmpId($op2Raw) : '';
                                $hasOpFinish = ($op2Emp !== '' && $op2Emp !== '-');

                                $shiftVal = trim((string)($row['shift'] ?? ''));
                                $groupVal = trim((string)($row['group'] ?? ''));
                                $shiftGroupStartStr = ($shiftVal !== '' && $shiftVal !== '-')
                                    ? ($shiftVal . ' / ' . ($groupVal !== '' ? $groupVal : '-'))
                                    : ($groupVal !== '' ? $groupVal : '-');

                                $shift2Val = trim((string)($row['shift2'] ?? ''));
                                $group2Val = trim((string)($row['group2'] ?? ''));
                                if ($shift2Val === '1') $shift2Val = 'A';
                                elseif ($shift2Val === '2') $shift2Val = 'B';
                                elseif ($shift2Val === '3') $shift2Val = 'C';

                                $shiftGroupFinishStr = '-';
                                if ($hasOpFinish && (($shift2Val !== '' && $shift2Val !== '-') || ($group2Val !== '' && $group2Val !== '-'))) {
                                    $shiftGroupFinishStr = ($shift2Val !== '' && $shift2Val !== '-')
                                        ? ($shift2Val . ($group2Val !== '' && $group2Val !== '-' ? ' / ' . $group2Val : ''))
                                        : $group2Val;
                                }

                                $createdRaw = trim((string)($row['created_at'] ?? ''));
                                $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
                                $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';

                                $timeInStr  = (!empty($row['par008']) && $row['par008'] !== '-') ? date('H:i', strtotime((string)$row['par008'])) : '-';
                                $timeOutStr = ($hasOpFinish && !empty($row['par009']) && $row['par009'] !== '-') ? date('H:i', strtotime((string)$row['par009'])) : '-';
                                ?>
                                <td align="center"><?= esc($op1Emp) ?></td>
                                <td align="center"><?= esc($shiftGroupStartStr) ?></td>
                                <?php if ($showFinishCols): ?>
                                    <td align="center"><?= esc($hasOpFinish ? $op2Emp : '-') ?></td>
                                    <td align="center"><?= esc($shiftGroupFinishStr) ?></td>
                                <?php endif; ?>
                                <td align="center"><?= esc((string)($row['machno'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['model'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['lotno'] ?? '-')) ?></td>
                                <td align="center"><?= $dateStr ?></td>
                                <?php if ($showAutoTimeCols): ?>
                                    <td align="center"><?= $timeInStr ?></td>
                                    <td align="center"><?= $timeOutStr ?></td>
                                <?php endif; ?>
                            <?php elseif ($kategori_form === 'startup_horizontal'): ?>
                                <td align="center"><?= (!empty($row['created_at']) && $row['created_at'] !== '-') ? date('d/m/Y', strtotime((string)$row['created_at'])) : '-' ?></td>
                                <td align="center"><?= esc((string)($row['machno'] ?? '-')) ?></td>
                                <td align="center"><?= esc(($row['shift'] ?? '-') . ' / ' . ($row['group'] ?? '-')) ?></td>
                                <td><?= esc(histResolveEmpId($row['empid'] ?? $row['name'] ?? $row['operator'] ?? $row['nama'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['status'] ?? '-')) ?></td>
                            <?php else: ?>
                                <?php
                                $opRaw = !empty($row['empid']) ? $row['empid'] : (!empty($row['name']) ? $row['name'] : ($row['operator'] ?? '-'));
                                $opEmp = histResolveEmpId($opRaw);

                                $shiftVal = trim((string)($row['shift'] ?? ''));
                                $groupVal = trim((string)($row['group'] ?? ''));
                                $shiftGroupStr = ($shiftVal !== '' && $shiftVal !== '-') 
                                    ? ($shiftVal . ' / ' . ($groupVal !== '' ? $groupVal : '-')) 
                                    : ($groupVal !== '' ? $groupVal : '-');

                                $createdRaw = trim((string)($row['created_at'] ?? ''));
                                $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
                                $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';
                                ?>
                                <td align="center"><?= esc((string)($row['machno'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['model'] ?? '-')) ?></td>
                                <td align="center"><?= esc((string)($row['lotno'] ?? '-')) ?></td>
                                <td align="center"><?= esc($opEmp) ?></td>
                                <td align="center"><?= esc($shiftGroupStr) ?></td>
                                <td align="center"><?= $dateStr ?></td>
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
                                    <?php if ($kategori_form === 'production'): ?>
                                        <?php
                                        $op1Raw = !empty($baseRow['empid']) ? $baseRow['empid'] : (!empty($baseRow['name']) ? $baseRow['name'] : ($baseRow['operator'] ?? '-'));
                                        $op1Emp = histResolveEmpId($op1Raw);

                                        // Pertahankan logic Operator Finish & Shift/Group Finish
                                        $op2Raw = !empty($baseRow['empid2']) ? $baseRow['empid2'] : ($baseRow['name2'] ?? '');
                                        $op2Emp = ($op2Raw !== '' && $op2Raw !== '-') ? histResolveEmpId($op2Raw) : '';
                                        $hasOpFinish = ($op2Emp !== '' && $op2Emp !== '-');

                                        $shiftVal = trim((string)($baseRow['shift'] ?? ''));
                                        $groupVal = trim((string)($baseRow['group'] ?? ''));
                                        $shiftGroupStartStr = ($shiftVal !== '' && $shiftVal !== '-')
                                            ? ($shiftVal . ' / ' . ($groupVal !== '' ? $groupVal : '-'))
                                            : ($groupVal !== '' ? $groupVal : '-');

                                        $shift2Val = trim((string)($baseRow['shift2'] ?? ''));
                                        $group2Val = trim((string)($baseRow['group2'] ?? ''));
                                        if ($shift2Val === '1') $shift2Val = 'A';
                                        elseif ($shift2Val === '2') $shift2Val = 'B';
                                        elseif ($shift2Val === '3') $shift2Val = 'C';

                                        $shiftGroupFinishStr = '-';
                                        if ($hasOpFinish && (($shift2Val !== '' && $shift2Val !== '-') || ($group2Val !== '' && $group2Val !== '-'))) {
                                            $shiftGroupFinishStr = ($shift2Val !== '' && $shift2Val !== '-')
                                                ? ($shift2Val . ($group2Val !== '' && $group2Val !== '-' ? ' / ' . $group2Val : ''))
                                                : $group2Val;
                                        }

                                        $createdRaw = trim((string)($baseRow['created_at'] ?? ''));
                                        $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
                                        $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';

                                        $timeInStr  = (!empty($baseRow['par008']) && $baseRow['par008'] !== '-') ? date('H:i', strtotime((string)$baseRow['par008'])) : '-';
                                        $timeOutStr = ($hasOpFinish && !empty($baseRow['par009']) && $baseRow['par009'] !== '-') ? date('H:i', strtotime((string)$baseRow['par009'])) : '-';
                                        ?>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc($op1Emp) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc($shiftGroupStartStr) ?></td>
                                        <?php if ($showFinishCols): ?>
                                            <td rowspan="<?= $gridCount ?>" align="center"><?= esc($hasOpFinish ? $op2Emp : '-') ?></td>
                                            <td rowspan="<?= $gridCount ?>" align="center"><?= esc($shiftGroupFinishStr) ?></td>
                                        <?php endif; ?>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['machno'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['model'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['lotno'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= $dateStr ?></td>
                                        <?php if ($showAutoTimeCols): ?>
                                            <td rowspan="<?= $gridCount ?>" align="center"><?= $timeInStr ?></td>
                                            <td rowspan="<?= $gridCount ?>" align="center"><?= $timeOutStr ?></td>
                                        <?php endif; ?>
                                    <?php elseif ($kategori_form === 'startup_horizontal'): ?>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= (!empty($baseRow['created_at']) && $baseRow['created_at'] !== '-') ? date('d/m/Y', strtotime((string)$baseRow['created_at'])) : '-' ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['machno'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc(($baseRow['shift'] ?? '-') . ' / ' . ($baseRow['group'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>"><?= esc(histResolveEmpId($baseRow['empid'] ?? $baseRow['name'] ?? $baseRow['operator'] ?? $baseRow['nama'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['status'] ?? '-')) ?></td>
                                    <?php else: ?>
                                        <?php
                                        $opRaw = !empty($baseRow['empid']) ? $baseRow['empid'] : (!empty($baseRow['name']) ? $baseRow['name'] : ($baseRow['operator'] ?? '-'));
                                        $opEmp = histResolveEmpId($opRaw);

                                        $shiftVal = trim((string)($baseRow['shift'] ?? ''));
                                        $groupVal = trim((string)($baseRow['group'] ?? ''));
                                        $shiftGroupStr = ($shiftVal !== '' && $shiftVal !== '-') 
                                            ? ($shiftVal . ' / ' . ($groupVal !== '' ? $groupVal : '-')) 
                                            : ($groupVal !== '' ? $groupVal : '-');

                                        $createdRaw = trim((string)($baseRow['created_at'] ?? ''));
                                        $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
                                        $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';
                                        ?>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['machno'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['model'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc((string)($baseRow['lotno'] ?? '-')) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc($opEmp) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= esc($shiftGroupStr) ?></td>
                                        <td rowspan="<?= $gridCount ?>" align="center"><?= $dateStr ?></td>
                                    <?php endif; ?>
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

    <?php if (!empty($revision['catatan_sop'])): ?>
        <div class="note mt-3 p-3 bg-light border rounded">
            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Catatan SOP Pemeriksaan:</h6>
            <div class="small text-secondary" style="white-space: pre-line; line-height: 1.6;"><?= esc($revision['catatan_sop']) ?></div>
        </div>
    <?php endif; ?>
</div>