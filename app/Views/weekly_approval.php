<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-check me-2"></i>Weekly & Page Approval Data
            </h4>
        </div>
        <div class="card-body">
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

            <!-- Nav Tabs Pemisah Dokumen Startup vs Production -->
            <ul class="nav nav-tabs mb-4" id="approvalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= ($active_tab !== 'production') ? 'active font-weight-bold' : '' ?>" id="startup-tab" data-bs-toggle="tab" data-bs-target="#startup-content" type="button" role="tab" aria-controls="startup-content" aria-selected="<?= ($active_tab !== 'production') ? 'true' : 'false' ?>">
                        <i class="fas fa-calendar-week me-1"></i> Startup Checksheet (Mingguan)
                        <span class="badge bg-secondary ms-1"><?= count($weekly_data ?? []) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= ($active_tab === 'production') ? 'active font-weight-bold' : '' ?>" id="production-tab" data-bs-toggle="tab" data-bs-target="#production-content" type="button" role="tab" aria-controls="production-content" aria-selected="<?= ($active_tab === 'production') ? 'true' : 'false' ?>">
                        <i class="fas fa-file-invoice me-1"></i> Production Checksheet (Per 20 Data / 1 Lembar Penuh)
                        <span class="badge bg-primary ms-1"><?= count($production_data ?? []) ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="approvalTabsContent">
                <!-- TAB 1: STARTUP CHECKSHEET (MINGGUAN - EXISTING) -->
                <div class="tab-pane fade <?= ($active_tab !== 'production') ? 'show active' : '' ?>" id="startup-content" role="tabpanel" aria-labelledby="startup-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Periode (Senin - Minggu)</th>
                                    <th style="width: 140px;">Total Dokumen</th>
                                    <th style="width: 170px;">Status Daily Check</th>
                                    <th style="width: 170px;">Status QC</th>
                                    <th style="width: 170px;">Status Production</th>
                                    <th style="width: 190px;">Aksi Anda</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($weekly_data)): ?>
                                    <?php foreach($weekly_data as $row): ?>
                                    <tr>
                                        <td class="text-center font-weight-bold">#<?= $row['id'] ?></td>
                                        <td>
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            <strong><?= date('d M Y', strtotime($row['period_start'])) ?> &mdash; <?= date('d M Y', strtotime($row['period_end'])) ?></strong>
                                            <?php if(!empty($row['device_name']) || !empty($row['device'])): ?>
                                                <div class="mt-1">
                                                    <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                                        <?= !empty($row['device_name']) ? esc($row['device_name']) : esc($row['device']) ?>
                                                        <?= !empty($row['process_name']) ? ' &bull; ' . esc($row['process_name']) : (!empty($row['process']) ? ' &bull; ' . esc($row['process']) : '') ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('approval/detail_mingguan/'.$row['id']) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-file-alt me-1"></i> <?= $row['total_docs'] ?> Dokumen
                                            </a>
                                        </td>

                                        <!-- Status Kelengkapan Daily Check -->
                                        <td class="text-center">
                                            <?php if($row['pending_daily'] > 0): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i><?= $row['pending_daily'] ?> Belum Di-approve
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Lengkap (Semua Checked)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Badge Status QC -->
                                        <td class="text-center">
                                            <?php if($row['status_qc'] === 'Approve'): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Approved</span>
                                                <?php if(!empty($row['approver_qc'])): ?>
                                                    <div class="mt-1 small text-dark font-weight-bold">
                                                        <i class="fas fa-user-check text-success me-1"></i><?= esc($row['approver_qc']) ?>
                                                    </div>
                                                    <?php if(!empty($row['date_qc'])): ?>
                                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                                            <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($row['date_qc'])) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
                                                <?php if(!empty($row['approver_production'])): ?>
                                                    <div class="mt-1 small text-dark font-weight-bold">
                                                        <i class="fas fa-user-check text-primary me-1"></i><?= esc($row['approver_production']) ?>
                                                    </div>
                                                    <?php if(!empty($row['date_production'])): ?>
                                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                                            <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($row['date_production'])) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php elseif($row['status_production'] === 'Reject'): ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tombol Aksi Startup -->
                                        <td class="text-center">
                                            <?php 
                                            $roleKey = strtolower(trim((string)($user_role ?? '')));
                                            $canQc = ($roleKey === 'qc' || $roleKey === 'qa' || $roleKey === 'all' || !empty($is_admin));
                                            $canProd = ($roleKey === 'production' || in_array($roleKey, ['fl', 'sl', 'all']) || !empty($is_admin));
                                            $hasAction = false;
                                            $dailyNotComplete = ($row['pending_daily'] > 0);
                                            ?>
                                            
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                <?php if($dailyNotComplete && (($canQc && $row['status_qc'] === 'Pending') || ($canProd && $row['status_production'] === 'Pending'))): ?>
                                                    <button type="button" class="btn btn-secondary btn-sm" disabled title="Terdapat <?= $row['pending_daily'] ?> checksheet startup yang belum di-approve harian oleh Foreman/Leader/Spv. Selesaikan daily approval terlebih dahulu.">
                                                        <i class="fas fa-lock me-1"></i>Daily Belum Lengkap
                                                    </button>
                                                <?php else: ?>
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
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada data periode mingguan startup.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: PRODUCTION CHECKSHEET (PER 20 DATA / 1 LEMBAR PENUH) -->
                <div class="tab-pane fade <?= ($active_tab === 'production') ? 'show active' : '' ?>" id="production-content" role="tabpanel" aria-labelledby="production-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 100px;">Lembar</th>
                                    <th>Device & Proses</th>
                                    <th style="width: 110px;">No. Mesin</th>
                                    <th style="width: 180px;">Rentang Tanggal</th>
                                    <th style="width: 160px;">Kelengkapan 1 Lembar</th>
                                    <th style="width: 220px;">Status Approval (QC / Prod)</th>
                                    <th style="width: 240px;">Aksi Anda</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                    ?>
                                    <tr>
                                        <td class="text-center font-weight-bold">
                                            <span class="badge bg-primary fs-6">Lembar <?= esc($prow['page_no'] ?? 1) ?></span>
                                            <div class="small text-muted mt-1">#<?= $prow['id'] ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark"><?= esc($prow['device_name'] ?: $prow['device']) ?></span>
                                            <div class="font-weight-bold text-dark mt-1">
                                                <?= esc($prow['process_name'] ?: $prow['process']) ?>
                                            </div>
                                            <div class="small text-muted font-monospace"><?= esc($prow['process']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary fs-6"><?= esc($prow['machno'] ?: '-') ?></span>
                                        </td>
                                        <td>
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            <?= date('d/m/Y', strtotime($prow['period_start'])) ?> &mdash; <?= date('d/m/Y', strtotime($prow['period_end'])) ?>
                                        </td>
                                        <!-- Kelengkapan Lembar (20 Data) -->
                                        <td class="text-center">
                                            <?php if($isFull): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>20 / 20 Data (Penuh)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-hourglass-half me-1"></i><?= $prow['total_docs'] ?> / 20 Data
                                                </span>
                                                <div class="small text-muted mt-1">Kurang <?= 20 - (int)$prow['total_docs'] ?> data lagi</div>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Status Approval (Single Check QC atau Prod salah satu) -->
                                        <td class="text-center">
                                            <?php if($isApproved): ?>
                                                <span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Approved</span>
                                                <?php 
                                                    $approverName = !empty($prow['approver']) ? $prow['approver'] : (!empty($prow['approver_qc']) ? $prow['approver_qc'] : $prow['approver_production']);
                                                    $approverRole = !empty($prow['approver_role']) ? $prow['approver_role'] : (!empty($prow['approver_qc']) ? 'QC' : 'Production');
                                                    $rawAppDate   = !empty($prow['approval_date']) ? $prow['approval_date'] : (!empty($prow['date_qc']) ? $prow['date_qc'] : $prow['date_production']);
                                                ?>
                                                <div class="mt-1 small text-dark font-weight-bold">
                                                    <i class="fas fa-user-check text-success me-1"></i><?= esc($approverName) ?> (<?= esc($approverRole) ?>)
                                                </div>
                                                <?php if(!empty($rawAppDate)): ?>
                                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rawAppDate)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php elseif($isRejected): ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Approval</span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Tombol Aksi Production -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                <a href="<?= base_url('approval/detail_production/' . $prow['id']) ?>" class="btn btn-outline-info btn-sm" title="Lihat 20 Data & Export Form">
                                                    <i class="fas fa-eye me-1"></i>Lihat & Export
                                                </a>

                                                <?php if(!$isApproved): ?>
                                                    <?php if(!$isFull): ?>
                                                        <button type="button" class="btn btn-secondary btn-sm" disabled title="Lembar ini belum mencapai 20 data. Tombol approval akan aktif otomatis saat 20 data terpenuhi.">
                                                            <i class="fas fa-lock me-1"></i>Belum 20 Data
                                                        </button>
                                                    <?php elseif($canApproveProd): ?>
                                                        <?php if($canQc): ?>
                                                            <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="weekly_id" value="<?= $prow['id'] ?>">
                                                                <input type="hidden" name="departemen" value="qc">
                                                                <input type="hidden" name="action" value="Approve">
                                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui 1 lembar (20 data) checksheet production ini sebagai QC?');">
                                                                    <i class="fas fa-check me-1"></i>Approve (QC)
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                        <?php if($canProd): ?>
                                                            <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                                <?= csrf_field() ?>
                                                                <input type="hidden" name="weekly_id" value="<?= $prow['id'] ?>">
                                                                <input type="hidden" name="departemen" value="production">
                                                                <input type="hidden" name="action" value="Approve">
                                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Setujui 1 lembar (20 data) checksheet production ini sebagai Production?');">
                                                                    <i class="fas fa-check me-1"></i>Approve (Prod)
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small"><i>Menunggu QC / Prod</i></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-success border border-success"><i class="fas fa-check me-1"></i>Approved</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Belum ada batch lembar production. Input data checksheet production akan otomatis dikelompokkan per 20 data.
                                        </td>
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
</main>
<?= $this->endSection(); ?>