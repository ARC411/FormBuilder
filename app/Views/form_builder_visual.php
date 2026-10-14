<?php
// --- FUNGSI HELPER REKURSIF ---
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

if (!function_exists('renderActionButtons')) {
    function renderActionButtons($node) {
        $cleanNode = $node;
        unset($cleanNode['children']);
        $rawJson = htmlspecialchars(json_encode($cleanNode), ENT_QUOTES, 'UTF-8');
        return '
        <div class="mt-1" style="white-space: nowrap;">
            <button type="button" class="btn btn-sm btn-success btn-add-child py-0 px-1" data-id="'.$node['id'].'" title="Tambah Sub-Bagian"><i class="fas fa-plus" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-warning btn-edit text-dark py-0 px-1" data-raw=\''.$rawJson.'\' title="Edit Parameter"><i class="fas fa-edit" style="font-size:10px;"></i></button>
            <button type="button" class="btn btn-sm btn-danger btn-delete py-0 px-1" data-id="'.$node['id'].'" title="Hapus"><i class="fas fa-trash-alt" style="font-size:10px;"></i></button>
        </div>';
    }
}

if (!function_exists('renderInputPreview')) {
    function renderInputPreview($leaf) {
        $html = '<small class="text-primary fw-bold d-block mb-1">[ ' . esc($leaf['kolom_db'] ?: 'No DB') . ' ]</small>';
        if ($leaf['tipe_input'] === 'dropdown_ok_ng') {
            $html .= '<select class="form-select form-select-sm text-center" disabled><option>- Pilih -</option><option>OK</option><option>NG</option></select>';
        } elseif ($leaf['tipe_input'] === 'textarea') {
            $html .= '<textarea class="form-control form-control-sm" rows="2" placeholder="Catatan..." disabled></textarea>';
        } elseif ($leaf['tipe_input'] === 'number') {
            $html .= '<input type="number" class="form-control form-control-sm text-center" placeholder="Angka" disabled>';
            if (!is_null($leaf['batas_min']) || !is_null($leaf['batas_max'])) {
                $minTxt = !is_null($leaf['batas_min']) ? (float)$leaf['batas_min'] : 'Bebas';
                $maxTxt = !is_null($leaf['batas_max']) ? (float)$leaf['batas_max'] : 'Bebas';
                $html .= '<small class="text-danger fw-semibold d-block mt-1" style="font-size:11px;">Min: '.$minTxt.' | Max: '.$maxTxt.'</small>';
            }
        } else {
            $html .= '<input type="text" class="form-control form-control-sm text-center" placeholder="Data" disabled>';
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
            // Rumus presisi agar judul induk merentang ke bawah jika cabangnya lebih pendek dari maxDepth
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
                            <th width="22%">Data</th>
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

            // Cari baris matriks (Sample 1, Sample 2 / X, Y, Z, θ / X., Y., r., s.)
            // dan catat indeks kolom matriks pertama ($firstGridLeafIdx)
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
                                ?>
                                        <th rowspan="<?= $headerMaxDepth ?>" class="align-middle bg-secondary">Sample / Label</th>
                                <?php
                                    endif;
                                ?>
                                    <th colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>" class="p-2 align-middle">
                                        <?= esc($col['name']) ?>
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
                            <!-- BARIS MATRIKS (FOREGOING) -->
                            <?php foreach ($gridRowNames as $rIdx => $rowName): ?>
                                <tr>
                                    <?php foreach ($headerLeaves as $hIdx => $hLeaf): ?>
                                        <?php if ($hIdx === $firstGridLeafIdx): ?>
                                            <td class="fw-bold bg-secondary text-white px-3"><?= esc($rowName) ?></td>
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
                                            <td class="p-2 bg-light">
                                                <?php if ($matchedCell): ?>
                                                    <?= renderInputPreview($matchedCell) ?>
                                                    <?= renderActionButtons($matchedCell) ?>
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