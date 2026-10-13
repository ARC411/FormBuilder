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
            <div class="alert alert-info py-2 mb-3">
                <i class="far fa-calendar-alt me-1"></i>
                <strong>Periode:</strong> <?= date('d F Y', strtotime($mingguan['period_start'])) ?> &mdash; <?= date('d F Y', strtotime($mingguan['period_end'])) ?>
                &nbsp;|&nbsp;
                <strong>Status QC:</strong> 
                <span class="badge <?= $mingguan['status_qc'] === 'Approve' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= $mingguan['status_qc'] ?>
                </span>
                &nbsp;|&nbsp;
                <strong>Status Production:</strong> 
                <span class="badge <?= $mingguan['status_production'] === 'Approve' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= $mingguan['status_production'] ?>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No.</th>
                            <th>Waktu Pengecekan</th>
                            <th>Device</th>
                            <th>Nama Proses</th>
                            <th>No. Mesin</th>
                            <th>Lot No</th>
                            <th>Status Harian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($alldata)): ?>
                            <?php $no = 1; foreach($alldata as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= $row['device_name'] ?></td>
                                <td><?= $row['name'] ?> <br><small class="text-muted">(<?= $row['process'] ?>)</small></td>
                                <td class="text-center"><?= $row['machno'] ?></td>
                                <td><?= $row['lotno'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $row['status'] ?></span>
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
                                    Tidak ada dokumen pengecekan pada minggu ini.
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