<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <!-- Header Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-file-invoice me-2"></i>Page Approval Data &ndash; Production Checksheet
                </h4>
                <div class="small text-muted mt-1">
                    Approval lembar dokumen Production Process Control per batch 1 lembar penuh (tepat 20 data per mesin)
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="<?= base_url('approval/sync_production') ?>" class="btn btn-outline-success btn-sm shadow-sm" title="Pindai checksheet baru dan masukkan ke lembar approval">
                    <i class="fas fa-sync-alt me-1"></i> Sinkronkan Lembar Baru
                </a>
                <a href="<?= base_url('approval/weekly') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Approval
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Flash Notifications -->
            <?php if(session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Toolbar: Quick Search & Filter & Sorting Info -->
            <div class="row align-items-center mb-3 g-2">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="productionSearchInput" class="form-control" placeholder="Cari mesin, device, proses, nomor lembar, atau tanggal...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilterSelect" class="form-select form-select-sm">
                        <option value="">Semua Status Approval</option>
                        <option value="pending">Menunggu Approval (Pending)</option>
                        <option value="approved">Sudah Disetujui (Approved)</option>
                        <option value="incomplete">Belum Lengkap (< 20 Data)</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-inline-flex align-items-center gap-2">
                        <span class="small text-muted">Urutan Tanggal:</span>
                        <a href="<?= base_url('approval/weekly?type=production&sort=' . ($sort_dir === 'desc' ? 'asc' : 'desc')) ?>" 
                           id="btnSortToggle" 
                           class="btn btn-sm <?= ($sort_dir === 'asc') ? 'btn-outline-primary' : 'btn-primary' ?> shadow-sm">
                            <i class="fas <?= ($sort_dir === 'asc') ? 'fa-sort-amount-up-alt' : 'fa-sort-amount-down-alt' ?> me-1"></i>
                            <span id="sortToggleLabel"><?= ($sort_dir === 'asc') ? 'Terlama &rarr; Terbaru' : 'Terbaru &rarr; Terlama' ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table Production Batches -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="productionTable">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 105px;">Lembar</th>
                            <th>Device &amp; Proses</th>
                            <th style="width: 110px;">No. Mesin</th>
                            
                            <!-- KOLOM SORTING: RENTANG TANGGAL -->
                            <th style="width: 220px; background-color: #f0f7ff; cursor: pointer;" 
                                id="thRentangTanggal" 
                                class="user-select-none" 
                                title="Klik untuk mengubah urutan tanggal (Terbaru-Terlama / Lama-Terbaru)">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span class="font-weight-bold text-dark">Rentang Tanggal</span>
                                    <span id="rentangSortIconWrap" class="ms-1">
                                        <?php if($sort_dir === 'asc'): ?>
                                            <i class="fas fa-sort-amount-up-alt text-primary fs-6"></i>
                                            <span class="badge bg-primary text-white" style="font-size: 0.68rem;">Lama &rarr; Baru</span>
                                        <?php else: ?>
                                            <i class="fas fa-sort-amount-down-alt text-primary fs-6"></i>
                                            <span class="badge bg-primary text-white" style="font-size: 0.68rem;">Baru &rarr; Lama</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </th>

                            <th style="width: 160px;">Kelengkapan</th>
                            <th style="width: 230px;">Status Approval (QC / Prod)</th>
                            <th style="width: 240px;">Aksi Anda</th>
                        </tr>
                    </thead>
                    <tbody id="productionTableBody">
                        <?php if(!empty($production_data)): ?>
                            <?php foreach($production_data as $prow): ?>
                            <?php 
                                $isFull = ($prow['total_docs'] >= 20);
                                $isApproved = (!empty($prow['status']) && $prow['status'] === 'Approve');
                                $isRejected = (!empty($prow['status']) && $prow['status'] === 'Reject');
                                $roleKey = strtolower(trim((string)($user_role ?? '')));
                                $canQc = ($roleKey === 'qc' || $roleKey === 'qa' || $roleKey === 'all' || !empty($is_admin));
                                $canProd = ($roleKey === 'production' || in_array($roleKey, ['fl', 'sl', 'all']) || !empty($is_admin));
                                $canApproveProd = ($canQc || $canProd);

                                // Timestamps for fast JS sorting
                                $startTs = !empty($prow['period_start']) ? strtotime($prow['period_start']) : 0;
                                $endTs   = !empty($prow['period_end']) ? strtotime($prow['period_end']) : 0;
                                $statusCategory = $isApproved ? 'approved' : (!$isFull ? 'incomplete' : 'pending');
                            ?>
                            <tr class="prod-row" 
                                data-start-ts="<?= $startTs ?>" 
                                data-end-ts="<?= $endTs ?>" 
                                data-id="<?= $prow['id'] ?>"
                                data-status-cat="<?= $statusCategory ?>"
                                data-search-text="<?= strtolower(esc(($prow['page_no'] ?? '').' '.($prow['device_name'] ?: $prow['device']).' '.($prow['process_name'] ?: $prow['process']).' '.($prow['machno'] ?? '').' '.date('d/m/Y', $startTs).' '.date('d/m/Y', $endTs).' '.($prow['status'] ?? ''))) ?>">
                                
                                <!-- Lembar & ID -->
                                <td class="text-center font-weight-bold">
                                    <span class="badge bg-primary fs-6">Lembar <?= esc($prow['page_no'] ?? 1) ?></span>
                                    <div class="small text-muted mt-1 font-monospace">#<?= $prow['id'] ?></div>
                                </td>

                                <!-- Device & Proses -->
                                <td>
                                    <span class="badge bg-dark"><?= esc($prow['device_name'] ?: $prow['device']) ?></span>
                                    <div class="font-weight-bold text-dark mt-1">
                                        <?= esc($prow['process_name'] ?: $prow['process']) ?>
                                    </div>
                                    <div class="small text-muted font-monospace"><?= esc($prow['process']) ?></div>
                                </td>

                                <!-- No. Mesin -->
                                <td class="text-center">
                                    <span class="badge bg-secondary fs-6"><?= esc($prow['machno'] ?: '-') ?></span>
                                </td>

                                <!-- Rentang Tanggal -->
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1 font-weight-bold text-dark">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                        <span><?= !empty($prow['period_start']) ? date('d/m/Y', strtotime($prow['period_start'])) : '-' ?></span>
                                        <span class="text-muted">&mdash;</span>
                                        <span><?= !empty($prow['period_end']) ? date('d/m/Y', strtotime($prow['period_end'])) : '-' ?></span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <?= !empty($prow['period_end']) ? date('d M Y', strtotime($prow['period_end'])) : '' ?>
                                    </div>
                                </td>

                                <!-- Kelengkapan Lembar (20 Data) -->
                                <td class="text-center">
                                    <?php if($isFull): ?>
                                        <span class="badge bg-success py-2 px-2">
                                            <i class="fas fa-check-circle me-1"></i> 20/20 Lengkap
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark py-2 px-2" title="Belum mencapai 20 baris checksheet">
                                            <i class="fas fa-hourglass-half me-1"></i> <?= (int)$prow['total_docs'] ?> / 20 Data
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Approval (QC / Prod Single Sign-off) -->
                                <td>
                                    <?php if($isApproved): ?>
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-double me-1"></i> Approved
                                        </span>
                                        <div class="small text-dark mt-1">
                                            Oleh: <strong><?= esc($prow['approver'] ?: 'Approver') ?></strong>
                                            <span class="badge bg-info text-dark ms-1"><?= strtoupper(esc($prow['approver_role'] ?: 'QC/PROD')) ?></span>
                                        </div>
                                        <div class="small text-muted font-monospace">
                                            <?= !empty($prow['approval_date']) ? date('d/m/Y H:i', strtotime($prow['approval_date'])) : '-' ?>
                                        </div>
                                    <?php elseif($isRejected): ?>
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle me-1"></i> Rejected
                                        </span>
                                        <div class="small text-muted mt-1">
                                            Oleh: <?= esc($prow['approver'] ?: '-') ?> (<?= esc($prow['approver_role'] ?: '-') ?>)
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-clock me-1"></i> Menunggu Approval
                                        </span>
                                        <div class="small text-muted mt-1">
                                            Cukup 1 Approval (QC atau Prod)
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Anda -->
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <!-- Tombol Lihat Data 20 Baris -->
                                        <a href="<?= base_url('approval/detail_production/' . $prow['id']) ?>" 
                                           class="btn btn-outline-primary btn-sm w-100 shadow-sm">
                                            <i class="fas fa-list-ol me-1"></i> Lihat Data (<?= (int)$prow['total_docs'] ?>) &amp; Export
                                        </a>

                                        <?php if(!$isFull): ?>
                                            <button class="btn btn-sm btn-secondary w-100" disabled title="Lembar belum penuh 20 checksheet, approval belum dapat dilakukan.">
                                                <i class="fas fa-lock me-1"></i> Belum Lengkap (<?= (int)$prow['total_docs'] ?>/20)
                                            </button>
                                        <?php elseif(!$isApproved && $canApproveProd): ?>
                                            <form action="<?= base_url('approval/processWeekly') ?>" method="POST" class="w-100 d-flex gap-1 mt-1">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="weekly_id" value="<?= $prow['id'] ?>">
                                                <input type="hidden" name="doc_type" value="production">
                                                <input type="hidden" name="departemen" value="<?= $canQc ? 'qc' : 'production' ?>">
                                                
                                                <button type="submit" name="action" value="Approve" class="btn btn-sm btn-success flex-grow-1 shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui (Approve) lembar checksheet production ini?')">
                                                    <i class="fas fa-check me-1"></i> Approve (<?= $canQc ? 'QC' : 'Prod' ?>)
                                                </button>
                                                
                                                <button type="submit" name="action" value="Reject" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Apakah Anda yakin menolak (Reject) lembar checksheet production ini?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php elseif(!$isApproved && !$canApproveProd): ?>
                                            <span class="text-muted small">Hanya QC/Production</span>
                                        <?php else: ?>
                                            <!-- Export Shortcut Saat Sudah Approved -->
                                            <div class="btn-group btn-group-sm w-100 mt-1">
                                                <a href="<?= base_url('history/export_excel_by_batch?doc_type=production&weekly_id=' . $prow['id']) ?>" class="btn btn-outline-success" title="Download Excel">
                                                    <i class="fas fa-file-excel me-1"></i> Excel
                                                </a>
                                                <a href="<?= base_url('history/export_pdf_by_batch?doc_type=production&weekly_id=' . $prow['id']) ?>" class="btn btn-outline-danger" title="Download PDF">
                                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 text-secondary d-block"></i>
                                    Belum ada batch lembar checksheet production yang terbentuk.<br>
                                    Klik tombol <strong>"Sinkronkan Lembar Baru"</strong> di kanan atas untuk mengelompokkan data.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Client-side Interactive Filter & Sorting Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentSort = '<?= $sort_dir ?>'; // 'desc' atau 'asc'
    const thRentangTanggal = document.getElementById('thRentangTanggal');
    const sortToggleLabel = document.getElementById('sortToggleLabel');
    const btnSortToggle = document.getElementById('btnSortToggle');
    const rentangSortIconWrap = document.getElementById('rentangSortIconWrap');
    const tableBody = document.getElementById('productionTableBody');
    const searchInput = document.getElementById('productionSearchInput');
    const statusFilter = document.getElementById('statusFilterSelect');

    // Fungsi Sortir Interaktif Tabel Berdasarkan Rentang Tanggal
    function sortTableByDate(direction) {
        currentSort = direction;
        const rows = Array.from(tableBody.querySelectorAll('tr.prod-row'));
        if (rows.length === 0) return;

        rows.sort(function (a, b) {
            let startA = parseInt(a.getAttribute('data-start-ts') || '0', 10);
            let startB = parseInt(b.getAttribute('data-start-ts') || '0', 10);
            let endA = parseInt(a.getAttribute('data-end-ts') || '0', 10);
            let endB = parseInt(b.getAttribute('data-end-ts') || '0', 10);
            let idA = parseInt(a.getAttribute('data-id') || '0', 10);
            let idB = parseInt(b.getAttribute('data-id') || '0', 10);

            if (direction === 'asc') {
                // Terlama -> Terbaru: bandingkan tanggal awal ASC, lalu tanggal akhir ASC, lalu ID ASC
                if (startA !== startB) return startA - startB;
                if (endA !== endB) return endA - endB;
                return idA - idB;
            } else {
                // Terbaru -> Terlama: bandingkan tanggal akhir DESC, lalu tanggal awal DESC, lalu ID DESC
                if (endA !== endB) return endB - endA;
                if (startA !== startB) return startB - startA;
                return idB - idA;
            }
        });

        // Pasang kembali baris yang sudah terurut ke DOM
        rows.forEach(function (row) {
            tableBody.appendChild(row);
        });

        // Update indikator UI
        const nextSort = (direction === 'desc') ? 'asc' : 'desc';
        if (direction === 'asc') {
            rentangSortIconWrap.innerHTML = '<i class="fas fa-sort-amount-up-alt text-primary fs-6"></i> <span class="badge bg-primary text-white" style="font-size: 0.68rem;">Lama &rarr; Baru</span>';
            if (sortToggleLabel) sortToggleLabel.innerHTML = 'Terlama &rarr; Terbaru';
            if (btnSortToggle) {
                btnSortToggle.className = 'btn btn-sm btn-outline-primary shadow-sm';
                btnSortToggle.href = '<?= base_url("approval/weekly?type=production&sort=") ?>' + nextSort;
            }
        } else {
            rentangSortIconWrap.innerHTML = '<i class="fas fa-sort-amount-down-alt text-primary fs-6"></i> <span class="badge bg-primary text-white" style="font-size: 0.68rem;">Baru &rarr; Lama</span>';
            if (sortToggleLabel) sortToggleLabel.innerHTML = 'Terbaru &rarr; Terlama';
            if (btnSortToggle) {
                btnSortToggle.className = 'btn btn-sm btn-primary shadow-sm';
                btnSortToggle.href = '<?= base_url("approval/weekly?type=production&sort=") ?>' + nextSort;
            }
        }

        // Simpan state URL tanpa reload
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('sort', direction);
        window.history.replaceState({}, '', newUrl.toString());
    }

    // Event Klik Header "Rentang Tanggal" untuk Toggle Sortir (Terbaru <-> Terlama)
    if (thRentangTanggal) {
        thRentangTanggal.addEventListener('click', function (e) {
            e.preventDefault();
            const targetDirection = (currentSort === 'desc') ? 'asc' : 'desc';
            sortTableByDate(targetDirection);
        });
    }

    // Event Klik Tombol Toolbar Sortir
    if (btnSortToggle) {
        btnSortToggle.addEventListener('click', function (e) {
            e.preventDefault();
            const targetDirection = (currentSort === 'desc') ? 'asc' : 'desc';
            sortTableByDate(targetDirection);
        });
    }

    // Live Search & Status Filter
    function filterRows() {
        const query = (searchInput ? searchInput.value.toLowerCase().trim() : '');
        const selectedStatus = (statusFilter ? statusFilter.value : '');
        const rows = tableBody.querySelectorAll('tr.prod-row');

        rows.forEach(function (row) {
            const text = row.getAttribute('data-search-text') || '';
            const statusCat = row.getAttribute('data-status-cat') || '';

            const matchesQuery = (query === '' || text.includes(query));
            const matchesStatus = (selectedStatus === '' || statusCat === selectedStatus);

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterRows);
    }
});
</script>
<?= $this->endSection(); ?>