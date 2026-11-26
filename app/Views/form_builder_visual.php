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
        if (!empty($copy['children']) && !empty($copy['children'][0]['posisi_grid'])) {
            $firstCell = $copy['children'][0];
            $copy['tipe_input']     = $firstCell['tipe_input'] ?: 'number';
            $copy['batas_min']      = $firstCell['batas_min'];
            $copy['batas_max']      = $firstCell['batas_max'];
            $copy['satuan_opsi']    = $firstCell['satuan_opsi'];
            $copy['is_readonly']    = $firstCell['is_readonly'];
            $copy['is_required']    = $firstCell['is_required'] ?? 0;
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
        $isGridReq = (!empty($node['children'][0]['posisi_grid']) && !empty($node['children'][0]['is_required']) && empty($node['children'][0]['is_readonly']));
        $isLeafReq = (empty($node['children']) && !empty($node['is_required']) && empty($node['is_readonly']));
        if ($isLeafReq || $isGridReq) {
            $badges .= '<span class="badge bg-danger text-white d-inline-block mt-1 me-1" style="font-size:10px;"><i class="fas fa-asterisk"></i> Wajib</span>';
        }
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
    function renderActionButtons($node, $canMovePrev = false, $canMoveNext = false, $isVertical = false) {
        $modalTree = prepareNodeForModal($node);
        $rawJson = htmlspecialchars(json_encode($modalTree), ENT_QUOTES, 'UTF-8');

        $moveBtns = '';
        if ($canMovePrev) {
            $prevIcon  = $isVertical ? 'fa-arrow-up' : 'fa-arrow-left';
            $prevTitle = $isVertical ? 'Geser Urutan ke Atas' : 'Geser Urutan ke Kiri';
            $moveBtns .= '<button type="button" class="btn btn-sm btn-light border text-dark btn-move-param py-0 px-1 me-1" data-id="'.$node['id'].'" data-dir="prev" title="'.$prevTitle.'"><i class="fas '.$prevIcon.'" style="font-size:10px;"></i></button>';
        }
        if ($canMoveNext) {
            $nextIcon  = $isVertical ? 'fa-arrow-down' : 'fa-arrow-right';
            $nextTitle = $isVertical ? 'Geser Urutan ke Bawah' : 'Geser Urutan ke Kanan';
            $moveBtns .= '<button type="button" class="btn btn-sm btn-light border text-dark btn-move-param py-0 px-1 me-1" data-id="'.$node['id'].'" data-dir="next" title="'.$nextTitle.'"><i class="fas '.$nextIcon.'" style="font-size:10px;"></i></button>';
        }

        return '
        <div class="mt-1" style="white-space: nowrap;">
            '.$moveBtns.'<button type="button" class="btn btn-sm btn-success btn-add-child py-0 px-1" data-id="'.$node['id'].'" data-name="'.esc($node['nama_parameter']).'" data-table-index="'.($node['table_index'] ?? 1).'" title="Tambah Sub-Parameter di Bawah Ini"><i class="fas fa-plus" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-warning btn-edit-tree text-dark py-0 px-1" data-tree=\''.$rawJson.'\' title="Edit Parameter & Sub-Bagiannya"><i class="fas fa-edit" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-delete py-0 px-1" data-id="'.$node['id'].'" title="Hapus"><i class="fas fa-trash-alt" style="font-size:10px;"></i></button>
        </div>';
    }
}

if (!function_exists('renderInputPreview')) {
    function renderInputPreview($leaf) {
        // Jika kolom teks statis (header_only seperti "Start up Checking Item" / "Standard" di Burn In)
        if (($leaf['tipe_input'] ?? '') === 'header_only' || (empty($leaf['kolom_db']) && empty($leaf['tipe_input']))) {
            $txt = !empty($leaf['standar_nilai']) ? $leaf['standar_nilai'] : 'Teks Statis';
            return '<span class="badge bg-secondary mb-1">Teks Statis</span><div class="small fw-semibold text-wrap" style="min-width:120px;">' . nl2br(esc($txt)) . '</div>';
        }

        $html = '<small class="text-primary fw-bold d-block mb-1">[ ' . esc($leaf['kolom_db'] ?: 'Auto DB') . ' ]</small>';

        $units = [];
        if (!empty($leaf['satuan_opsi'])) {
            $decodedUnits = is_string($leaf['satuan_opsi']) ? json_decode($leaf['satuan_opsi'], true) : $leaf['satuan_opsi'];
            if (is_array($decodedUnits)) $units = $decodedUnits;
        }

        $formula = null;
        if (!empty($leaf['rumus_otomatis'])) {
            $formula = is_string($leaf['rumus_otomatis']) ? json_decode($leaf['rumus_otomatis'], true) : $leaf['rumus_otomatis'];
        }

        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            $html .= '<select class="form-select form-select-sm text-center" disabled><option>- Pilih -</option><option>OK / V</option><option>NG / X</option></select>';
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

        if (!empty($formula['type'])) {
            $fLbl = ($formula['type'] === 'ng') ? 'Rumus NG ('.($formula['in_col']??'In').' - '.($formula['out_col']??'Out').')' : 'Rumus Yield %';
            $html .= '<span class="badge bg-secondary d-block mt-1" style="font-size:10px;"><i class="fas fa-calculator"></i> '.esc($fLbl).'</span>';
        } elseif (!empty($leaf['is_readonly'])) {
            $html .= '<span class="badge bg-secondary d-block mt-1" style="font-size:10px;"><i class="fas fa-eye"></i> Read-Only</span>';
        }

        if (!empty($leaf['standar_nilai']) && empty($leaf['posisi_grid'])) {
            $html .= '<small class="text-muted d-block mt-1" style="font-size:10px;">' . esc($leaf['standar_nilai']) . '</small>';
        }
        return $html;
    }
}

if (!function_exists('buildStartupRows')) {
    function buildStartupRows($nodes, $currentCol, $maxDepth, &$verticalRows, &$rowIndex, $isRootLevel = false) {
        $no = 1;
        $sibCount = count($nodes);
        $nodesValues = array_values($nodes);
        foreach ($nodesValues as $nIdx => $node) {
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
                'can_prev'  => ($nIdx > 0),
                'can_next'  => ($nIdx < $sibCount - 1),
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
        $sibCount = count($nodes);
        $nodesValues = array_values($nodes);
        foreach ($nodesValues as $nIdx => $node) {
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
                'can_prev'  => ($nIdx > 0),
                'can_next'  => ($nIdx < $sibCount - 1),
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
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="m-0 font-weight-bold">
            <i class="fas fa-table"></i> Visual Builder - <?= strtoupper($process_code) ?> 
            (Revisi <?= esc($revision['revisi']) ?>)
            <?php if (isset($isLatestRevision) && !$isLatestRevision): ?>
                <span class="badge bg-secondary ms-1">ARSIP</span>
            <?php endif; ?>
        </h5>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <?php if (!empty($slotStats)): ?>
                <?php $slotBadgeCls = ($slotStats['remaining'] <= 3) ? 'bg-danger text-white' : 'bg-light text-dark'; ?>
                <span class="badge <?= $slotBadgeCls ?> border" title="Kapasitas kolom DB maks <?= esc($slotStats['max_par_label']) ?> (Kolom sistem terproteksi: <?= esc($slotStats['reserved_label']) ?>)">
                    <i class="fas fa-database text-primary"></i> Slot DB: <?= (int)$slotStats['used_slots'] ?> / <?= (int)$slotStats['max_slots'] ?> Terpakai
                </span>
            <?php endif; ?>
            <span class="badge bg-warning text-dark border border-dark">
                Kategori: <?= strtoupper($kategori_form === 'startup_horizontal' ? 'STARTUP (MENDATAR)' : ($kategori_form === 'startup' ? 'STARTUP (MENURUN)' : $kategori_form)) ?>
            </span>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (isset($isLatestRevision) && !$isLatestRevision): ?>
            <div class="alert alert-warning border-warning d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <i class="fas fa-history me-1"></i>
                    <b>Mode Lihat Revisi Arsip (Revisi <?= esc($revision['revisi']) ?>):</b>
                    Anda sedang membuka versi revisi terdahulu. Revisi aktif terbaru saat ini adalah <b>Revisi <?= esc($latestRevision['revisi'] ?? '-') ?></b>.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-warning fw-bold text-dark btn-restore-revision" data-source-id="<?= (int)$revision['id'] ?>" data-rev="<?= esc($revision['revisi']) ?>">
                        <i class="fas fa-undo"></i> Salin Struktur Ini ke Revisi Aktif
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark btn-back-latest-rev" data-latest-id="<?= (int)($latestRevision['id'] ?? 0) ?>">
                        <i class="fas fa-arrow-right"></i> Kembali ke Revisi Aktif
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($treeParams)): ?>
            <div class="alert alert-warning text-center">
                Belum ada parameter. Silakan klik tombol <b>"Tambah Parameter Utama"</b>.
            </div>

        <?php elseif ($kategori_form === 'startup'): ?>
            <!-- ========================================================== -->
            <!-- ENGINE 1: STARTUP MENURUN (TABEL VERTIKAL)                 -->
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
                                            <?= renderActionButtons($cell['node_data'], !empty($cell['can_prev']), !empty($cell['can_next']), true) ?>
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
            <!-- ========================================================================= -->
            <!-- ENGINE 2 & 3: STARTUP MENDATAR, PRODUCTION, & FOREGOING (MATRIKS GRID)    -->
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
                    $d = getSubtreeDepth($node, true);
                    if ($d > $headerMaxDepth) $headerMaxDepth = $d;
                }
                $headerMaxDepth = max(1, $headerMaxDepth);

                $headerRows = [];
                $headerLeaves = [];
                buildHorizontalHeaders($currentTree, 0, $headerRows, $headerLeaves, $headerMaxDepth);
                ksort($headerRows);

                $gridRowNames = [];
                $firstGridLeafIdx = null;
                $gridHeaderLabel = 'Sample / Label';
                foreach ($headerLeaves as $hIdx => $hLeaf) {
                    if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                        if ($firstGridLeafIdx === null) {
                            $firstGridLeafIdx = $hIdx;
                        }
                        foreach ($hLeaf['children'] as $gridChild) {
                            if (!empty($gridChild['standar_nilai'])) {
                                $gridHeaderLabel = $gridChild['standar_nilai'];
                            }
                            if (!empty($gridChild['posisi_grid']) && !in_array($gridChild['posisi_grid'], $gridRowNames)) {
                                $gridRowNames[] = $gridChild['posisi_grid'];
                            }
                        }
                    }
                }
                ?>

                <?php if ($isMultiTable): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-4 pt-2 border-top">
                        <h6 class="fw-bold text-dark m-0">
                            <i class="fas fa-table text-primary"></i> Tabel <?= $tIdx ?>: <?= esc($currentTree[0]['nama_parameter'] ?? ('Bagian ' . $tIdx)) ?>
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success btn-add-child py-0 px-2" data-id="" data-name="" data-table-index="<?= $tIdx ?>">
                                <i class="fas fa-plus"></i> Tambah Parameter di Tabel <?= $tIdx ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-table py-0 px-2" data-table-index="<?= $tIdx ?>" data-table-name="<?= esc($currentTree[0]['nama_parameter'] ?? ('Tabel ' . $tIdx)) ?>" title="Hapus Seluruh Tabel <?= $tIdx ?>">
                                <i class="fas fa-trash-alt"></i> Hapus Tabel <?= $tIdx ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($kategori_form === 'foregoing' && empty($gridRowNames)): ?>
                    <div class="mb-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-dark btn-add-matrix-row" data-rows='[]' data-table-index="<?= $tIdx ?>">
                            <i class="fas fa-list-ol"></i> + Tambah Baris Posisi (X, Y, Z, θ)
                        </button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive mb-4">
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
                                                <div class="d-flex justify-content-center align-items-center gap-1">
                                                    <span><?= esc($gridHeaderLabel) ?></span>
                                                    <button type="button" class="btn btn-sm btn-warning text-dark py-0 px-1 btn-edit-matrix-header" data-label="<?= esc($gridHeaderLabel) ?>" title="Ubah Nama Kolom Ini (Misal: Pos)">
                                                        <i class="fas fa-edit" style="font-size:10px;"></i>
                                                    </button>
                                                </div>
                                                <button type="button" class="btn btn-xs btn-success mt-2 py-0 px-2 btn-add-matrix-row" data-rows='<?= $rowsJson ?>' data-table-index="<?= $tIdx ?>" title="Tambah Baris Baru (Misal: Y)">
                                                    <i class="fas fa-plus"></i> Tambah Baris
                                                </button>
                                            </th>
                                    <?php
                                        endif;
                                    ?>
                                        <th colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>" class="p-2 align-middle">
                                            <?= esc($col['name']) ?>
                                            <?= renderRuleBadges($col['node_data']) ?>
                                            <?= renderActionButtons($col['node_data'], !empty($col['can_prev']), !empty($col['can_next']), false) ?>
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
                                        <td class="p-3 bg-light">
                                            <?= renderInputPreview($leaf) ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php else: ?>
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
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>