<?php
// --- FUNGSI HELPER REKURSIF VISUAL BUILDER ---
if (!function_exists('countLeaves')) {
    function countLeaves($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $count = 0;
        foreach ($node['children'] as $child) {
            $count += countLeaves($child, $ignoreGridChildren);
        }
        return $count;
    }
}

if (!function_exists('getSubtreeDepth')) {
    function getSubtreeDepth($node, $ignoreGridChildren = false) {
        if (empty($node['children'])) return 1;
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) return 1;
        $max = 0;
        foreach ($node['children'] as $child) {
            $d = getSubtreeDepth($child, $ignoreGridChildren);
            if ($d > $max) $max = $d;
        }
        return $max + 1;
    }
}

if (!function_exists('prepareNodeForModal')) {
    function prepareNodeForModal($node) {
        $copy = $node;
        // Jika anak-anaknya adalah baris matriks posisi_grid (X, Y, Z, θ),
        // angkat pengaturan input dari sel pertama ke header kolomnya dan sembunyikan anak posisi_grid dari modal pohon
        if (!empty($copy['children']) && !empty($copy['children'][0]['posisi_grid'])) {
            $firstCell = $copy['children'][0];
            $copy['tipe_input']     = $firstCell['tipe_input'] ?: 'number';
            $copy['batas_min']      = $firstCell['batas_min'];
            $copy['batas_max']      = $firstCell['batas_max'];
            $copy['satuan_opsi']    = $firstCell['satuan_opsi'];
            $copy['is_readonly']    = $firstCell['is_readonly'];
            $copy['rumus_otomatis'] = $firstCell['rumus_otomatis'];
            $copy['is_grid_column'] = true;
            $copy['children']       = [];
        } elseif (!empty($copy['children'])) {
            $cleanChildren = [];
            foreach ($copy['children'] as $ch) {
                $cleanChildren[] = prepareNodeForModal($ch);
            }
            $copy['children'] = $cleanChildren;
        }
        return $copy;
    }
}

if (!function_exists('renderRuleBadges')) {
    function renderRuleBadges($node) {
        $badges = '';
        if (!empty($node['is_exclusive'])) {
            $badges .= '<span class="badge bg-info text-dark d-inline-block mt-1 me-1" style="font-size:10px;"><i class="fas fa-lock"></i> Isi Salah Satu</span>';
        }
        if (!empty($node['standar_dinamis'])) {
            $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
            $lbl = $dyn['label_dropdown'] ?? 'Standar Dinamis';
            $badges .= '<span class="badge bg-warning text-dark d-inline-block mt-1 me-1" style="font-size:10px;"><i class="fas fa-sliders-h"></i> Dropdown: '.esc($lbl).'</span>';
        }
        return $badges ? '<div>'.$badges.'</div>' : '';
    }
}

if (!function_exists('renderActionButtons')) {
    function renderActionButtons($node) {
        $modalTree = prepareNodeForModal($node);
        $rawJson = htmlspecialchars(json_encode($modalTree), ENT_QUOTES, 'UTF-8');
        return '
        <div class="mt-1" style="white-space: nowrap;">
            <button type="button" class="btn btn-sm btn-success btn-add-child py-0 px-1" data-id="'.$node['id'].'" data-name="'.esc($node['nama_parameter']).'" title="Tambah Sub-Parameter di Bawah Ini"><i class="fas fa-plus" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-warning btn-edit-tree text-dark py-0 px-1" data-tree=\''.$rawJson.'\' title="Edit Parameter & Sub-Bagiannya"><i class="fas fa-edit" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-delete py-0 px-1" data-id="'.$node['id'].'" title="Hapus"><i class="fas fa-trash-alt" style="font-size:10px;"></i></button>
        </div>';
    }
}

if (!function_exists('renderInputPreview')) {
    function renderInputPreview($leaf) {
        $html = '<small class="text-primary fw-bold d-block mb-1">[ ' . esc($leaf['kolom_db'] ?: 'Auto DB') . ' ]</small>';

        // Cek satuan_opsi
        $units = [];
        if (!empty($leaf['satuan_opsi'])) {
            $decodedUnits = is_string($leaf['satuan_opsi']) ? json_decode($leaf['satuan_opsi'], true) : $leaf['satuan_opsi'];
            if (is_array($decodedUnits)) $units = $decodedUnits;
        }

        // Cek rumus_otomatis
        $formula = null;
        if (!empty($leaf['rumus_otomatis'])) {
            $formula = is_string($leaf['rumus_otomatis']) ? json_decode($leaf['rumus_otomatis'], true) : $leaf['rumus_otomatis'];
        }

        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            $html .= '<select class="form-select form-select-sm text-center" disabled><option>- Pilih -</option><option>OK</option><option>NG</option></select>';
        } elseif ($leaf['tipe_input'] === 'textarea') {
            $html .= '<textarea class="form-control form-control-sm" rows="2" placeholder="Catatan..." disabled></textarea>';
        } elseif ($leaf['tipe_input'] === 'number') {
            if (!empty($units)) {
                $html .= '<div class="input-group input-group-sm">';
                $html .= '<input type="number" class="form-control text-center" placeholder="Angka" disabled>';
                $html .= '<select class="form-select" style="max-width:75px;" disabled>';
                foreach ($units as $u) {
                    $html .= '<option>'.esc($u['unit'] ?? '').'</option>';
                }
                $html .= '</select></div>';
                foreach ($units as $u) {
                    if (($u['min'] ?? '') !== '' || ($u['max'] ?? '') !== '') {
                        $html .= '<small class="text-danger d-block mt-1" style="font-size:10px;">'.esc($u['unit']).': '.($u['min'] ?? '-∞').' ~ '.($u['max'] ?? '∞').'</small>';
                    }
                }
            } else {
                $html .= '<input type="number" class="form-control form-control-sm text-center" placeholder="Angka" disabled>';
                if (!is_null($leaf['batas_min']) || !is_null($leaf['batas_max'])) {
                    $minTxt = !is_null($leaf['batas_min']) ? (float)$leaf['batas_min'] : 'Bebas';
                    $maxTxt = !is_null($leaf['batas_max']) ? (float)$leaf['batas_max'] : 'Bebas';
                    $html .= '<small class="text-danger fw-semibold d-block mt-1" style="font-size:11px;">Min: '.$minTxt.' | Max: '.$maxTxt.'</small>';
                }
            }
        } else {
            $html .= '<input type="text" class="form-control form-control-sm text-center" placeholder="Data" disabled>';
        }

        // Badge Read-Only & Rumus Otomatis
        if (!empty($formula['type'])) {
            $fLbl = ($formula['type'] === 'ng') ? 'Rumus NG ('.($formula['in_col']??'In').' - '.($formula['out_col']??'Out').')' : 'Rumus Yield %';
            $html .= '<span class="badge bg-secondary d-block mt-1" style="font-size:10px;"><i class="fas fa-calculator"></i> '.esc($fLbl).'</span>';
        } elseif (!empty($leaf['is_readonly'])) {
            $html .= '<span class="badge bg-secondary d-block mt-1" style="font-size:10px;"><i class="fas fa-eye"></i> Read-Only</span>';
        }

        if (!empty($leaf['standar_nilai'])) {
            $html .= '<small class="text-muted d-block mt-1" style="font-size:10px;">' . esc($leaf['standar_nilai']) . '</small>';
        }
        return $html;
    }
}

if (!function_exists('buildStartupRows')) {
    function buildStartupRows($nodes, $currentCol, $maxDepth, &$verticalRows, &$rowIndex, $isRootLevel = false) {
        $no = 1;
        foreach ($nodes as $node) {
            if (!isset($verticalRows[$rowIndex])) $verticalRows[$rowIndex] = [];

            $rowspan  = countLeaves($node);
            $subDepth = getSubtreeDepth($node);
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
                buildStartupRows($node['children'], $currentCol + $colspan, $maxDepth, $verticalRows, $rowIndex, false);
            }
        }
    }
}

if (!function_exists('buildHorizontalHeaders')) {
    function buildHorizontalHeaders($nodes, $level, &$headerRows, &$headerLeaves, $maxDepth) {
        if (!isset($headerRows[$level])) $headerRows[$level] = [];
        foreach ($nodes as $node) {
            $hasGridChildren = (!empty($node['children']) && !empty($node['children'][0]['posisi_grid']));
            $isHeaderLeaf    = (empty($node['children']) || $hasGridChildren);

            $colspan  = countLeaves($node, true);
            $subDepth = getSubtreeDepth($node, true);
            $rowspan  = max(1, ($maxDepth - $level) - ($subDepth - 1));

            $headerRows[$level][] = [
                'name'      => $node['nama_parameter'],
                'colspan'   => $colspan,
                'rowspan'   => $rowspan,
                'is_grid'   => $hasGridChildren,
                'node_data' => $node
            ];

            if ($isHeaderLeaf) {
                $headerLeaves[] = $node;
            } else {
                buildHorizontalHeaders($node['children'], $level + $rowspan, $headerRows, $headerLeaves, $maxDepth);
            }
        }
    }
}
?>

<div class="card border-primary shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold">
            <i class="fas fa-table"></i> Visual Builder - <?= strtoupper($process_code) ?> 
            (Revisi <?= $revision['revisi'] ?>)
        </h5>
        <div>
            <span class="badge bg-warning text-dark border border-dark me-2">
                Kategori: <?= strtoupper($kategori_form) ?>
            </span>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($treeParams)): ?>
            <div class="alert alert-warning text-center">
                Belum ada parameter. Silakan klik tombol <b>"Tambah Parameter Utama"</b>.
            </div>

        <?php elseif ($kategori_form === 'startup'): ?>
            <!-- ========================================================== -->
            <!-- ENGINE 1: STARTUP (TABEL VERTIKAL: NO | ITEM | STD | DATA) -->
            <!-- ========================================================== -->
            <?php
            $verticalRows = [];
            $rowIndex = 0;
            buildStartupRows($treeParams, 0, $maxTableDepth, $verticalRows, $rowIndex, true);
            ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th colspan="<?= max(1, $maxTableDepth - 1) ?>">Start up Check Item</th>
                            <?php if ($maxTableDepth > 1): ?>
                                <th>Standard</th>
                            <?php endif; ?>
                            <th width="24%">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($verticalRows as $r): ?>
                            <tr>
                                <?php foreach ($r as $cell): ?>
                                    <?php if ($cell['type'] === 'no'): ?>
                                        <td rowspan="<?= $cell['rowspan'] ?>" class="bg-light fw-bold"><?= $cell['no'] ?></td>
                                    <?php elseif ($cell['type'] === 'param'): ?>
                                        <td rowspan="<?= $cell['rowspan'] ?>" colspan="<?= $cell['colspan'] ?>" class="p-2">
                                            <div class="fw-semibold"><?= esc($cell['name']) ?></div>
                                            <?= renderRuleBadges($cell['node_data']) ?>
                                            <?= renderActionButtons($cell['node_data']) ?>
                                        </td>
                                    <?php elseif ($cell['type'] === 'input'): ?>
                                        <td class="p-2 bg-light">
                                            <?= renderInputPreview($cell['node_data']) ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <!-- ================================================================ -->
            <!-- ENGINE 2 & 3: PRODUCTION (HORIZONTAL) & FOREGOING (MATRIKS GRID) -->
            <!-- ================================================================ -->
            <?php
            $headerMaxDepth = 0;
            foreach ($treeParams as $node) {
                $d = getSubtreeDepth($node, true);
                if ($d > $headerMaxDepth) $headerMaxDepth = $d;
            }
            $headerMaxDepth = max(1, $headerMaxDepth);

            $headerRows = [];
            $headerLeaves = [];
            buildHorizontalHeaders($treeParams, 0, $headerRows, $headerLeaves, $headerMaxDepth);
            ksort($headerRows);

            $gridRowNames = [];
            $firstGridLeafIdx = null;
            foreach ($headerLeaves as $hIdx => $hLeaf) {
                if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                    if ($firstGridLeafIdx === null) {
                        $firstGridLeafIdx = $hIdx;
                    }
                    foreach ($hLeaf['children'] as $gridChild) {
                        if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $gridRowNames)) {
                            $gridRowNames[] = $gridChild['posisi_grid'];
                        }
                    }
                }
            }
            ?>

            <?php if ($kategori_form === 'foregoing' && empty($gridRowNames)): ?>
                <div class="mb-2 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-dark btn-add-matrix-row" data-rows='[]'>
                        <i class="fas fa-list-ol"></i> + Tambah Baris Sample / Label (X, Y, Z, θ)
                    </button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center" style="white-space: nowrap; min-width: 100%;">
                    <thead class="table-dark">
                        <?php foreach ($headerRows as $level => $row): ?>
                            <tr>
                                <?php
                                $runningCol = 0;
                                $labelInserted = false;
                                foreach ($row as $col):
                                    if (!empty($gridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx):
                                        $labelInserted = true;
                                        $rowsJson = htmlspecialchars(json_encode($gridRowNames), ENT_QUOTES, 'UTF-8');
                                ?>
                                        <th rowspan="<?= $headerMaxDepth ?>" class="align-middle bg-secondary p-2">
                                            <div>Sample / Label</div>
                                            <button type="button" class="btn btn-xs btn-success mt-2 py-0 px-2 btn-add-matrix-row" data-rows='<?= $rowsJson ?>' title="Tambah Baris Baru (Misal: Y)">
                                                <i class="fas fa-plus"></i> Tambah Baris
                                            </button>
                                        </th>
                                <?php
                                    endif;
                                ?>
                                    <th colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>" class="p-2 align-middle">
                                        <?= esc($col['name']) ?>
                                        <?= renderRuleBadges($col['node_data']) ?>
                                        <?= renderActionButtons($col['node_data']) ?>
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
                            <!-- BARIS TUNGGAL (PRODUCTION) -->
                            <tr>
                                <?php foreach ($headerLeaves as $leaf): ?>
                                    <td class="p-3 bg-light">
                                        <?= renderInputPreview($leaf) ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php else: ?>
                            <!-- BARIS MATRIKS (FOREGOING: SAMPLE / LABEL AKTIF BISA DI-CRUD) -->
                            <?php foreach ($gridRowNames as $rIdx => $rowName): ?>
                                <tr>
                                    <?php foreach ($headerLeaves as $hIdx => $hLeaf): ?>
                                        <?php if ($hIdx === $firstGridLeafIdx): ?>
                                            <td class="fw-bold bg-secondary text-white px-3 py-2 align-middle">
                                                <div class="fs-6 mb-1"><?= esc($rowName) ?></div>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($rIdx > 0): ?>
                                                        <button type="button" class="btn btn-light py-0 px-1 btn-move-matrix-row" data-label="<?= esc($rowName) ?>" data-dir="up" title="Geser Naik"><i class="fas fa-arrow-up" style="font-size:10px;"></i></button>
                                                    <?php endif; ?>
                                                    <?php if ($rIdx < count($gridRowNames) - 1): ?>
                                                        <button type="button" class="btn btn-light py-0 px-1 btn-move-matrix-row" data-label="<?= esc($rowName) ?>" data-dir="down" title="Geser Turun"><i class="fas fa-arrow-down" style="font-size:10px;"></i></button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-warning py-0 px-1 btn-edit-matrix-row" data-label="<?= esc($rowName) ?>" title="Ubah Nama Baris"><i class="fas fa-edit" style="font-size:10px;"></i></button>
                                                    <button type="button" class="btn btn-danger py-0 px-1 btn-delete-matrix-row" data-label="<?= esc($rowName) ?>" title="Hapus Baris Ini"><i class="fas fa-trash-alt" style="font-size:10px;"></i></button>
                                                </div>
                                            </td>
                                        <?php endif; ?>

                                        <?php if (empty($hLeaf['children'])): ?>
                                            <?php if ($rIdx === 0): ?>
                                                <td rowspan="<?= count($gridRowNames) ?>" class="p-2 bg-light align-middle">
                                                    <?= renderInputPreview($hLeaf) ?>
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
                                            <td class="p-2 bg-light align-middle">
                                                <?php if ($matchedCell): ?>
                                                    <?= renderInputPreview($matchedCell) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>