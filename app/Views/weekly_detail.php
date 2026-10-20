<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-folder-open me-2"></i>Detail Dokumen Mingguan #<?= $mingguan['id'] ?>
            </h4>
            <a href="<?= base_url('approval/weekly') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Rekap Mingguan
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div>
                            <i class="far fa-calendar-alt text-primary me-1"></i>
                            <strong>Periode Minggu:</strong> <?= date('d F Y', strtotime($mingguan['period_start'])) ?> &mdash; <?= date('d F Y', strtotime($mingguan['period_end'])) ?>
                        </div>
                        <div class="mt-1">
                            <strong>Total Dokumen Startup:</strong> <span class="badge bg-primary"><?= count($alldata) ?> Dokumen</span>
                            &nbsp;&bull;&nbsp;
                            <strong>Kelengkapan Daily:</strong>
                            <?php if(!empty($pending_daily) && $pending_daily > 0): ?>
                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i><?= $pending_daily ?> Belum Di-approve Daily</span>
                            <?php else: ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lengkap (Semua Checked)</span>
                            <?php endif; ?>
                        </div>
                        <?php if(!empty($device_info['name']) || !empty($process_info['name'])): ?>
                            <div class="mt-1 small text-muted">
                                Scope Terdaftar: <?= esc($device_info['name'] ?? $mingguan['device']) ?> &bull; <?= esc($process_info['name'] ?? $mingguan['process']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
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

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th>Waktu Pengecekan</th>
                            <th>Device</th>
                            <th>Nama Proses</th>
                            <th>No. Mesin</th>
                            <th>Lot No</th>
                            <th>Status Harian</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($alldata)): ?>
                            <?php $no = 1; foreach($alldata as $row): ?>
                            <?php 
                                $isPendingDaily = (empty($row['status']) || $row['status'] === 'Pending' || $row['status'] === '-'); 
                            ?>
                            <tr class="<?= $isPendingDaily ? 'table-warning' : '' ?>">
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($row['device_name'] ?? $row['device']) ?></span></td>
                                <td><?= esc($row['name'] ?? $row['process']) ?> <br><small class="text-muted">(<?= esc($row['process']) ?>)</small></td>
                                <td class="text-center"><strong><?= esc($row['machno']) ?></strong></td>
                                <td><?= esc($row['lotno']) ?></td>
                                <td class="text-center">
                                    <?php if($isPendingDaily): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Daily</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i><?= esc($row['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('startup/history/'.$row['number']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-external-link-alt me-1"></i> Lihat Form
                                    </a>
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
        </div>
    </div>
</div>
</main>
<?= $this->endSection(); ?>