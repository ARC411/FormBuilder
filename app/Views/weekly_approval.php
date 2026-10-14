<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-week me-2"></i>Weekly Approval Data
            </h4>
        </div>
        <div class="card-body">
            <?php if(session()->getFlashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('message') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Periode (Senin - Minggu)</th>
                            <th>Total Dokumen</th>
                            <th>Status QC</th>
                            <th>Status Production</th>
                            <th style="width: 200px;">Aksi Anda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($weekly_data)): ?>
                            <?php foreach($weekly_data as $row): ?>
                            <tr>
                                <td class="text-center font-weight-bold">#<?= $row['id'] ?></td>
                                <td>
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    <?= date('d M Y', strtotime($row['period_start'])) ?> &mdash; <?= date('d M Y', strtotime($row['period_end'])) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('approval/detail_mingguan/'.$row['id']) ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-file-alt me-1"></i> <?= $row['total_docs'] ?> Dokumen
                                    </a>
                                </td>
                                
                                <!-- Badge Status QC -->
                                <td class="text-center">
                                    <?php if($row['status_qc'] === 'Approve'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>
                                    <?php elseif($row['status_qc'] === 'Reject'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Badge Status Production -->
                                <td class="text-center">
                                    <?php if($row['status_production'] === 'Approve'): ?>
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>
                                    <?php elseif($row['status_production'] === 'Reject'): ?>
                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="text-center">
                                    <?php 
                                    $roleKey = strtolower(trim((string)$user_role));
                                    $canQc = ($roleKey === 'qc' || $roleKey === 'qa' || $roleKey === 'all' || !empty($is_admin));
                                    $canProd = ($roleKey === 'production' || in_array($roleKey, ['fl', 'sl', 'all']) || !empty($is_admin));
                                    $hasAction = false;
                                    ?>
                                    
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <?php if($canQc && $row['status_qc'] === 'Pending'): $hasAction = true; ?>
                                            <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="weekly_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="departemen" value="qc">
                                                <input type="hidden" name="action" value="Approve">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Apakah Anda yakin menyetujui periode mingguan ini untuk QC?');">
                                                    <i class="fas fa-check me-1"></i>Approve (QC)
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if($canProd && $row['status_production'] === 'Pending'): $hasAction = true; ?>
                                            <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="weekly_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="departemen" value="production">
                                                <input type="hidden" name="action" value="Approve">
                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Apakah Anda yakin menyetujui periode mingguan ini untuk Production?');">
                                                    <i class="fas fa-check me-1"></i>Approve (Prod)
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if(!$hasAction): ?>
                                            <span class="text-muted small"><i>Selesai / No Action</i></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada data periode mingguan. Data akan otomatis terbuat saat cronjob mingguan dijalankan.
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