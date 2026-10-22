<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-folder-open me-2"></i>Detail Dokumen Mingguan #<?= $mingguan['id'] ?>
            </h4>
            <a href="<?= base_url('approval/weekly') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Rekap Mingguan
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-3 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div>
                            <i class="far fa-calendar-alt text-primary me-1"></i>
                            <strong>Periode Minggu:</strong> <?= date('d F Y', strtotime($mingguan['period_start'])) ?> &mdash; <?= date('d F Y', strtotime($mingguan['period_end'])) ?> <span class="badge bg-dark">23:59</span>
                        </div>
                        <div class="mt-2">
                            <strong>Total Dokumen Mingguan:</strong> <span class="badge bg-primary fs-6"><?= count($grouped_data ?? []) ?> Dokumen</span>
                            &nbsp;&bull;&nbsp;
                            <span class="text-muted small">Total Input Harian: <?= count($alldata ?? []) ?> Inputan</span>
                            &nbsp;&bull;&nbsp;
                            <strong>Kelengkapan Daily:</strong>
                            <?php if(!empty($pending_daily) && $pending_daily > 0): ?>
                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i><?= $pending_daily ?> Belum Di-approve Daily</span>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lengkap (Semua Checked)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <div>
                            <strong>Status QC:</strong> 
                            <span class="badge <?= $mingguan['status_qc'] === 'Approve' ? 'bg-success' : ($mingguan['status_qc'] === 'Reject' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                <?= $mingguan['status_qc'] ?>
                            </span>
                            <?php if(!empty($mingguan['approver_qc'])): ?>
                                <small class="text-dark ms-1 font-weight-bold">(<?= esc($mingguan['approver_qc']) ?><?= !empty($mingguan['date_qc']) ? ' - ' . date('d/m/Y H:i', strtotime($mingguan['date_qc'])) : '' ?>)</small>
                            <?php endif; ?>
                        </div>
                        <div class="mt-1">
                            <strong>Status Production:</strong> 
                            <span class="badge <?= $mingguan['status_production'] === 'Approve' ? 'bg-success' : ($mingguan['status_production'] === 'Reject' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                <?= $mingguan['status_production'] ?>
                            </span>
                            <?php if(!empty($mingguan['approver_production'])): ?>
                                <small class="text-dark ms-1 font-weight-bold">(<?= esc($mingguan['approver_production']) ?><?= !empty($mingguan['date_production']) ? ' - ' . date('d/m/Y H:i', strtotime($mingguan['date_production'])) : '' ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Utama: Checksheet Mingguan Dikelompokkan per Form Unit Mesin 7 Hari -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="font-weight-bold text-dark m-0">
                    <i class="fas fa-calendar-check text-primary me-2"></i>Daftar Form Checksheet Mingguan (Senin &mdash; Minggu 23:59)
                </h5>
                <span class="badge bg-light text-dark border">
                    <?= count($grouped_data ?? []) ?> Form Terdaftar
                </span>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th style="width: 120px;">Device</th>
                            <th>Nama Proses</th>
                            <th style="width: 100px;">No. Mesin</th>
                            <th>Hari & Tanggal Terisi (Senin - Minggu)</th>
                            <th>Lot No</th>
                            <th style="width: 160px;">Status Daily Check</th>
                            <th style="width: 220px;">Aksi Form Mingguan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($grouped_data)): ?>
                            <?php $no = 1; foreach($grouped_data as $row): ?>
                            <tr class="<?= ($row['pending_count'] > 0) ? 'table-warning' : '' ?>">
                                <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= esc($row['device_name'] ?: $row['device']) ?></span></td>
                                <td>
                                    <strong><?= esc($row['process_name'] ?: $row['process']) ?></strong>
                                    <div class="small text-muted font-monospace"><?= esc($row['process']) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark fs-6"><?= esc($row['machno'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <div>
                                        <i class="far fa-calendar-check text-primary me-1"></i>
                                        <strong><?= $row['total_days'] ?> Hari Terisi</strong>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <?= implode(', ', $row['dates']) ?>
                                    </div>
                                </td>
                                <td><?= !empty($row['lots']) ? esc(implode(', ', $row['lots'])) : '-' ?></td>
                                <td class="text-center">
                                    <?php if($row['pending_count'] > 0): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i><?= $row['pending_count'] ?> Hari Pending
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Lengkap (<?= $row['approved_count'] ?> Hari)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <a href="<?= base_url('approval/weekly_form/' . $mingguan['id'] . '?device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-primary" target="_blank" title="Lihat Checksheet Lengkap 7 Hari">
                                            <i class="fas fa-eye me-1"></i> Lihat Form (7 Hari)
                                        </a>
                                        <a href="<?= base_url('history/exportExcel?typeProcess=startup&dateStart=' . $mingguan['period_start'] . '&dateEnd=' . $mingguan['period_end'] . '&device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-success" target="_blank" title="Export Excel 7 Hari (Fit to 1 Page A4 Landscape)">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                        <a href="<?= base_url('history/exportPDF?typeProcess=startup&dateStart=' . $mingguan['period_start'] . '&dateEnd=' . $mingguan['period_end'] . '&device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-danger" target="_blank" title="Export PDF 7 Hari">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Tidak ada dokumen checksheet startup pada minggu ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bagian Collapsible: Detail Riwayat Input Harian (Raw Daily Submissions) -->
            <div class="mt-4 pt-2 border-top">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDailyLogs" aria-expanded="false" aria-controls="collapseDailyLogs">
                    <i class="fas fa-list me-1"></i> Tampilkan / Sembunyikan Log Rincian Input Harian (<?= count($alldata ?? []) ?> Inputan)
                </button>

                <div class="collapse mt-3" id="collapseDailyLogs">
                    <div class="card card-body bg-light">
                        <h6 class="font-weight-bold text-secondary mb-3">
                            <i class="fas fa-history me-1"></i>Log Riwayat Seluruh Input Checksheet Harian
                        </h6>
                        <div class="table-responsive bg-white rounded border">
                            <table class="table table-sm table-bordered table-striped table-hover align-middle mb-0">
                                <thead class="table-light text-center small">
                                    <tr>
                                        <th style="width: 40px;">No.</th>
                                        <th>Waktu Pengecekan</th>
                                        <th>Device</th>
                                        <th>Nama Proses</th>
                                        <th>No. Mesin</th>
                                        <th>Lot No</th>
                                        <th>Status Harian</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    <?php if(!empty($alldata)): ?>
                                        <?php $no = 1; foreach($alldata as $doc): ?>
                                        <?php 
                                            $isPendingDoc = (empty($doc['status']) || $doc['status'] === 'Pending' || $doc['status'] === '-'); 
                                        ?>
                                        <tr class="<?= $isPendingDoc ? 'table-warning' : '' ?>">
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= date('d-m-Y H:i', strtotime($doc['created_at'])) ?></td>
                                            <td><span class="badge bg-secondary"><?= esc($doc['device_name'] ?? $doc['device']) ?></span></td>
                                            <td><?= esc($doc['process_name'] ?? $doc['name'] ?? $doc['process']) ?></td>
                                            <td class="text-center"><strong><?= esc($doc['machno'] ?: '-') ?></strong></td>
                                            <td><?= esc($doc['lotno']) ?></td>
                                            <td class="text-center">
                                                <?php if($isPendingDoc): ?>
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Daily</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i><?= esc($doc['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('startup/history/'.$doc['number']) ?>" class="btn btn-xs btn-outline-primary" style="font-size: 0.75rem; padding: 2px 6px;" target="_blank">
                                                    <i class="fas fa-external-link-alt me-1"></i>Form Hari Ini
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-2 text-muted">Tidak ada log rincian input harian.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</main>
<?= $this->endSection(); ?>