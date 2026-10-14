<?php
// --- MESIN PENGHITUNG COLSPAN & ROWSPAN OTOMATIS ---
// 1. Hitung Lebar (Colspan) = Jumlah anak cucu di ujung terbawah
function getColspan($node) {
    if (empty($node['children'])) return 1;
    $count = 0;
    foreach ($node['children'] as $child) {
        $count += getColspan($child);
    }
    return $count;
}

// 2. Susun Baris Header (Thead)
$headerRows = [];
function buildHeaders($nodes, $level, &$headerRows, $maxDepth) {
    if (!isset($headerRows[$level])) $headerRows[$level] = [];
    
    foreach ($nodes as $node) {
        $colspan = getColspan($node);
        // Jika dia adalah ujung hierarki (tidak punya anak), dia rentangkan kotaknya ke bawah (Rowspan)
        $rowspan = empty($node['children']) ? ($maxDepth - $level) : 1;
        
        $headerRows[$level][] = [
            'id'       => $node['id'],
            'name'     => $node['nama_parameter'],
            'colspan'  => $colspan,
            'rowspan'  => $rowspan,
            'is_leaf'  => empty($node['children']),
            'node_data'=> $node
        ];
        
        if (!empty($node['children'])) {
            buildHeaders($node['children'], $level + 1, $headerRows, $maxDepth);
        }
    }
}
buildHeaders($treeParams, 0, $headerRows, $maxTableDepth);

// 3. Ambil Ujung Daun (Untuk membuat kotak input di Tbody)
$leafNodes = [];
function getLeafNodes($nodes, &$leafNodes) {
    foreach ($nodes as $node) {
        if (empty($node['children'])) {
            $leafNodes[] = $node;
        } else {
            getLeafNodes($node['children'], $leafNodes);
        }
    }
}
getLeafNodes($treeParams, $leafNodes);
?>

<div class="card border-primary shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold">
            <i class="fas fa-table"></i> Visual Builder - <?= strtoupper($process_code) ?> 
            (Revisi <?= $revision['revisi'] ?>)
        </h5>
        <span class="badge bg-warning text-dark border border-dark">Kategori: <?= strtoupper($kategori_form) ?></span>
    </div>
    
    <div class="card-body">
        
        <?php if (empty($treeParams)): ?>
            <div class="alert alert-warning text-center">
                Belum ada parameter. Silakan klik tombol <b>"Tambah Parameter Utama"</b> di atas.
            </div>
        <?php else: ?>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center" style="white-space: nowrap; min-width: 100%;">
                    
                    <!-- HEADER TABEL (OTOMATIS BERLAPIS BAPAK-ANAK) -->
                    <thead class="table-dark">
                        <?php foreach ($headerRows as $level => $row): ?>
                            <tr>
                                <?php foreach ($row as $col): ?>
                                    <th colspan="<?= $col['colspan'] ?>" rowspan="<?= $col['rowspan'] ?>" class="p-2 align-middle">
                                        <?= esc($col['name']) ?>
                                        <br>
                                        <!-- Tombol Aksi Admin -->
                                        <div class="mt-2" style="white-space: nowrap;">
                                            <button class="btn btn-sm btn-success btn-add-child" data-id="<?= $col['id'] ?>" title="Tambah Anak"><i class="fas fa-plus"></i></button>
                                            
                                            <!-- Data untuk diumpankan ke Javascript pas diedit -->
                                            <button class="btn btn-sm btn-warning btn-edit text-dark" 
                                                    data-raw='<?= htmlspecialchars(json_encode($col['node_data']), ENT_QUOTES, 'UTF-8') ?>' 
                                                    title="Edit Parameter">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $col['id'] ?>" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </thead>

                    <!-- BODY TABEL (KOTAK INPUT UNTUK UJUNG PARAMETER) -->
                    <tbody>
                        <tr>
                            <?php foreach ($leafNodes as $leaf): ?>
                                <td class="p-3 bg-light">
                                    <small class="text-primary fw-bold d-block mb-1">[ <?= esc($leaf['kolom_db'] ?: 'No DB') ?> ]</small>
                                    
                                    <?php if ($leaf['tipe_input'] === 'dropdown_ok_ng'): ?>
                                        <select class="form-select form-select-sm" disabled>
                                            <option>-</option>
                                            <option>OK</option>
                                            <option>NG</option>
                                        </select>
                                    
                                    <?php elseif ($leaf['tipe_input'] === 'number'): ?>
                                        <input type="number" class="form-control form-control-sm text-center" placeholder="Angka" disabled>
                                        <?php if (!is_null($leaf['batas_min']) || !is_null($leaf['batas_max'])): ?>
                                            <small class="text-danger d-block mt-1">Min: <?= $leaf['batas_min'] ?> | Max: <?= $leaf['batas_max'] ?></small>
                                        <?php endif; ?>
                                    
                                    <?php else: ?>
                                        <input type="text" class="form-control form-control-sm text-center" placeholder="Teks" disabled>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        <?php endif; ?>
    </div>
</div>