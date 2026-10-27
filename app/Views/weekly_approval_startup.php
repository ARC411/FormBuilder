<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-week me-2"></i>Weekly Approval Data &ndash; Startup Checksheet
                </h4>
                <div class="small text-muted mt-1">Rekap dokumen startup per periode mingguan (Senin s/d Minggu 23:59) dengan fitur Granular Day-Unlock Reject</div>
            </div>
            <a href="<?= base_url('approval/weekly') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Approval
            </a>
        </div>
        <div class="card-body">
            <!-- Flash Message Alerts -->
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Periode &amp; Mesin</th>
                            <th style="width: 140px;">Total Dokumen</th>
                            <th style="width: 180px;">Status Daily Check</th>
                            <th style="width: 170px;">Status QC</th>
                            <th style="width: 170px;">Status Production</th>
                            <th style="width: 230px;">Aksi Anda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($weekly_data)): ?>
                            <?php foreach($weekly_data as $row): ?>
                            <?php 
                                $isWeeklyRejected = (!empty($row['status']) && $row['status'] === 'Rejected');
                                $pendingDailyCount = (int)($row['pending_daily'] ?? 0);
                                $rejectedDailyCount = (int)($row['rejected_daily'] ?? 0);
                                $dailyNotComplete = ($pendingDailyCount > 0);

                                $roleKey = strtolower(trim((string)($user_role ?? '')));
                                $canQc = ($roleKey === 'qc' || $roleKey === 'qa' || $roleKey === 'all' || !empty($is_admin));
                                $canProd = ($roleKey === 'production' || in_array($roleKey, ['fl', 'sl', 'all']) || !empty($is_admin));
                                $hasAction = false;
                                $isFullApproved = ($row['status_qc'] === 'Approve' && $row['status_production'] === 'Approve');
                            ?>
                            <tr class="<?= ($rejectedDailyCount > 0) ? 'table-danger-subtle' : '' ?>">
                                <!-- ID -->
                                <td class="text-center font-weight-bold">#<?= $row['id'] ?></td>
                                
                                <!-- Periode, Info Mesin & Alert Granular Reject -->
                                <td>
                                    <div class="d-flex align-items-center gap-1 font-weight-bold text-dark">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                        <?= date('d M Y', strtotime($row['period_start'])) ?> &mdash; <?= date('d M Y', strtotime($row['period_end'])) ?>
                                    </div>
                                    
                                    <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                        <?php if(!empty($row['device'])): ?>
                                            <span class="badge bg-dark" style="font-size: 0.72rem;">
                                                <?= esc($row['device']) ?>
                                            </span>
                                            <?php if(!empty($row['process'])): ?>
                                                <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                                    <?= esc($row['process']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if(!empty($row['machno']) && $row['machno'] !== '-'): ?>
                                                <span class="badge bg-info text-dark" style="font-size: 0.72rem;">
                                                    Mesin: <?= esc($row['machno']) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php elseif(!empty($row['device_names'])): ?>
                                            <span class="badge bg-dark" style="font-size: 0.72rem;" title="Device aktif minggu ini">
                                                <i class="fas fa-microchip me-1"></i><?= $row['device_names'] ?>
                                            </span>
                                            <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                                <i class="fas fa-industry me-1"></i><?= (int)$row['total_docs'] ?> Form Mesin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                                <i class="fas fa-industry me-1"></i>Multi-Device (<?= (int)$row['total_docs'] ?> Form)
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Panel Informasi Granular Reject: HANYA tampil jika sedang ada form harian yang berstatus Reject -->
                                    <?php if($rejectedDailyCount > 0): ?>
                                        <div class="alert alert-danger py-1 px-2 mt-2 mb-0 small text-start border-start border-danger border-4 shadow-sm" style="font-size: 0.78rem;">
                                            <div class="font-weight-bold text-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Dalam Status Revisi Harian:
                                            </div>
                                            <div class="mt-1 text-dark">
                                                <strong>Hari Di-reject:</strong> 
                                                <span class="badge bg-danger"><?= esc($row['rejected_days'] ?? '-') ?></span>
                                            </div>
                                            <?php if(!empty($row['reject_note'])): ?>
                                                <div class="text-danger fst-italic mt-1">
                                                    "<?= esc($row['reject_note']) ?>"
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-muted small mt-1" style="font-size: 0.7rem;">
                                                Oleh: <strong><?= esc($row['rejected_by'] ?? '-') ?></strong>
                                                <?= !empty($row['rejected_at']) ? ' &bull; ' . date('d/m/Y H:i', strtotime($row['rejected_at'])) : '' ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Total Dokumen -->
                                <td class="text-center">
                                    <a href="<?= base_url('approval/detail_mingguan/'.$row['id']) ?>" class="btn btn-outline-primary btn-sm shadow-sm" title="Lihat rincian checksheet per mesin">
                                        <i class="fas fa-file-alt me-1"></i> <?= (int)$row['total_docs'] ?> Dokumen
                                    </a>
                                </td>

                                <!-- Status Kelengkapan Daily Check -->
                                <td class="text-center">
                                    <?php if($rejectedDailyCount > 0): ?>
                                        <span class="badge bg-danger py-2 px-2 shadow-sm" title="<?= $rejectedDailyCount ?> dokumen checksheet berstatus Reject">
                                            <i class="fas fa-times-circle me-1"></i><?= $rejectedDailyCount ?> Hari Di-reject
                                        </span>
                                        <?php if(($pendingDailyCount - $rejectedDailyCount) > 0): ?>
                                            <div class="badge bg-warning text-dark mt-1">
                                                <?= ($pendingDailyCount - $rejectedDailyCount) ?> Pending
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif($pendingDailyCount > 0): ?>
                                        <span class="badge bg-warning text-dark py-2 px-2 shadow-sm">
                                            <i class="fas fa-clock me-1"></i><?= $pendingDailyCount ?> Belum Di-approve
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success py-2 px-2 shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i>Lengkap (Semua Checked)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Badge Status QC -->
                                <td class="text-center">
                                    <?php if($row['status_qc'] === 'Approve'): ?>
                                        <span class="badge bg-success fs-6"><i class="fas fa-check-double me-1"></i>Approved</span>
                                        <?php if(!empty($row['approver_qc'])): ?>
                                            <div class="mt-1 small text-dark font-weight-bold">
                                                <i class="fas fa-user-check text-success me-1"></i><?= esc($row['approver_qc']) ?>
                                            </div>
                                            <?php if(!empty($row['date_qc'])): ?>
                                                <div class="small text-muted font-monospace" style="font-size: 0.72rem;">
                                                    <?= date('d/m/Y H:i', strtotime($row['date_qc'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php elseif($rejectedDailyCount > 0): ?>
                                        <span class="badge bg-danger"><i class="fas fa-undo me-1"></i>Rejected (Revisi)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Badge Status Production -->
                                <td class="text-center">
                                    <?php if($row['status_production'] === 'Approve'): ?>
                                        <span class="badge bg-success fs-6"><i class="fas fa-check-double me-1"></i>Approved</span>
                                        <?php if(!empty($row['approver_production'])): ?>
                                            <div class="mt-1 small text-dark font-weight-bold">
                                                <i class="fas fa-user-check text-primary me-1"></i><?= esc($row['approver_production']) ?>
                                            </div>
                                            <?php if(!empty($row['date_production'])): ?>
                                                <div class="small text-muted font-monospace" style="font-size: 0.72rem;">
                                                    <?= date('d/m/Y H:i', strtotime($row['date_production'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php elseif($rejectedDailyCount > 0): ?>
                                        <span class="badge bg-danger"><i class="fas fa-undo me-1"></i>Rejected (Revisi)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="text-center">
                                    <div class="d-flex flex-column justify-content-center align-items-center gap-1">
                                        <?php if($isFullApproved): ?>
                                            <span class="badge bg-success py-2 px-3 shadow-sm">
                                                <i class="fas fa-check-double me-1"></i> Full Approved
                                            </span>
                                            <!-- Tombol Reject Koreksi Opsional jika Admin/Spv memerlukan perbaikan -->
                                            <?php if(!empty($is_admin)): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-xs mt-1 btn-open-reject-weekly"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalRejectWeekly"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-period-start="<?= $row['period_start'] ?>"
                                                        data-period-end="<?= $row['period_end'] ?>"
                                                        data-device="<?= !empty($row['device']) ? esc($row['device']) : (!empty($row['device_names']) ? esc(strip_tags($row['device_names'])) : 'Multi-Device') ?>"
                                                        data-process="<?= !empty($row['process']) ? esc($row['process']) : 'Multi-Proses' ?>"
                                                        data-machno="<?= !empty($row['machno']) ? esc($row['machno']) : '-' ?>"
                                                        data-dept="qc"
                                                        data-active-dates='<?= json_encode($row['active_dates'] ?? []) ?>'>
                                                    <i class="fas fa-undo me-1"></i>Koreksi / Reject Hari
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif($dailyNotComplete): ?>
                                            <!-- Daily Check Belum Lengkap -->
                                            <button type="button" class="btn btn-secondary btn-sm w-100" disabled title="Terdapat <?= $pendingDailyCount ?> checksheet startup yang belum selesai di-approve daily oleh Foreman/Leader.">
                                                <i class="fas fa-lock me-1"></i>Daily Belum Lengkap (<?= $pendingDailyCount ?>)
                                            </button>

                                            <!-- Tombol Granular Reject tetap bisa ditekan oleh QC/Prod untuk meng-unlock hari yang salah lebih awal -->
                                            <?php if($canQc || $canProd): $hasAction = true; ?>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm w-100 mt-1 shadow-sm btn-open-reject-weekly"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalRejectWeekly"
                                                        data-id="<?= $row['id'] ?>"
                                                        data-period-start="<?= $row['period_start'] ?>"
                                                        data-period-end="<?= $row['period_end'] ?>"
                                                        data-device="<?= !empty($row['device']) ? esc($row['device']) : (!empty($row['device_names']) ? esc(strip_tags($row['device_names'])) : 'Multi-Device') ?>"
                                                        data-process="<?= !empty($row['process']) ? esc($row['process']) : 'Multi-Proses' ?>"
                                                        data-machno="<?= !empty($row['machno']) ? esc($row['machno']) : '-' ?>"
                                                        data-dept="<?= $canQc ? 'qc' : 'production' ?>"
                                                        data-active-dates='<?= json_encode($row['active_dates'] ?? []) ?>'
                                                        >
                                                    <i class="fas fa-times-circle me-1"></i>Reject Hari...
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- Daily Check Sudah Lengkap (Semua Checked) -->
                                            <div class="d-flex flex-wrap gap-1 justify-content-center w-100">
                                                <?php if($canQc && $row['status_qc'] !== 'Approve'): $hasAction = true; ?>
                                                    <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="weekly_id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="departemen" value="qc">
                                                        <input type="hidden" name="action" value="Approve">
                                                        <button type="submit" class="btn btn-success btn-sm shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui periode mingguan ini untuk QC?');">
                                                            <i class="fas fa-check me-1"></i><?= ($row['status_qc'] === 'Rejected (Revisi)') ? 'Re-Approve (QC)' : 'Approve (QC)' ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if($canProd && $row['status_production'] !== 'Approve'): $hasAction = true; ?>
                                                    <form action="<?= base_url('approval/processWeekly') ?>" method="post" class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="weekly_id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="departemen" value="production">
                                                        <input type="hidden" name="action" value="Approve">
                                                        <button type="submit" class="btn btn-primary btn-sm shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui periode mingguan ini untuk Production?');">
                                                            <i class="fas fa-check me-1"></i><?= ($row['status_production'] === 'Rejected (Revisi)') ? 'Re-Approve (Prod)' : 'Approve (Prod)' ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Tombol Granular Reject -->
                                                <?php if($canQc || $canProd): $hasAction = true; ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm shadow-sm btn-open-reject-weekly"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalRejectWeekly"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-period-start="<?= $row['period_start'] ?>"
                                                            data-period-end="<?= $row['period_end'] ?>"
                                                            data-device="<?= !empty($row['device']) ? esc($row['device']) : (!empty($row['device_names']) ? esc(strip_tags($row['device_names'])) : 'Multi-Device') ?>"
                                                            data-process="<?= !empty($row['process']) ? esc($row['process']) : 'Multi-Proses' ?>"
                                                            data-machno="<?= !empty($row['machno']) ? esc($row['machno']) : '-' ?>"
                                                            data-dept="<?= $canQc ? 'qc' : 'production' ?>"
                                                            data-active-dates='<?= json_encode($row['active_dates'] ?? []) ?>'
                                                            >
                                                        <i class="fas fa-times me-1"></i>Reject Hari...
                                                    </button>
                                                <?php endif; ?>
                                            </div>

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
                                    <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>
                                    Belum ada data periode mingguan. Jalankan cronjob mingguan untuk menutup periode data startup.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL GRANULAR DAY-UNLOCK REJECT (STARTUP WEEKLY) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRejectWeekly" tabindex="-1" aria-labelledby="modalRejectWeeklyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <form action="<?= base_url('approval/rejectWeeklyStartup') ?>" method="POST" id="formRejectWeekly">
                <?= csrf_field() ?>
                <input type="hidden" name="weekly_id" id="modal_reject_weekly_id" value="">
                <input type="hidden" name="departemen" id="modal_reject_departemen" value="">
                <input type="hidden" name="device" id="modal_hidden_device" value="">
                <input type="hidden" name="process" id="modal_hidden_process" value="">
                <input type="hidden" name="redirect_to" value="weekly">

                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalRejectWeeklyLabel">
                        <i class="fas fa-calendar-times me-2"></i>Granular Day-Unlock Reject &ndash; Startup Weekly
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Callout Info Target Dokumen -->
                    <div class="alert alert-light border p-3 mb-3 rounded shadow-sm">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Periode Mingguan:</span>
                                <strong class="text-dark" id="modal_info_period">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Approver Role:</span>
                                <span class="badge bg-danger fs-6" id="modal_info_dept">QC</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Device &amp; Proses:</span>
                                <strong class="text-dark" id="modal_info_device_process">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">No. Mesin Terpilih:</span>
                                <span class="badge bg-dark fs-6" id="modal_info_machno">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Pilih Mesin Spesifik -->
                    <div class="mb-3">
                        <label for="modal_select_machine" class="form-label font-weight-bold text-dark">
                            <i class="fas fa-microchip text-primary me-1"></i>Pilih Mesin / Dokumen yang Bermasalah: <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm" id="modal_select_machine" name="machno">
                            <!-- Diisi lewat JavaScript -->
                        </select>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                Pilih mesin spesifik untuk me-reject hari hanya pada mesin tersebut.
                            </span>
                            <a href="#" id="modal_link_detail_form" class="small text-primary text-decoration-none font-weight-bold" target="_blank">
                                <i class="fas fa-folder-open me-1"></i>Buka Halaman Detail Dokumen &raquo;
                            </a>
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 px-3 small border-start border-warning border-4 mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Pilih <strong>tanggal/hari spesifik</strong> yang memiliki kesalahan. Status daily check Foreman pada hari yang dicentang akan di-unlock menjadi <strong>'Reject'</strong> agar dapat diperbaiki. Hari lain yang tidak dicentang akan <strong>tetap berstatus Approved</strong>.
                    </div>

                    <!-- Toolbar Pilihan Hari -->
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <label class="form-label font-weight-bold text-dark m-0">
                            <i class="far fa-calendar-check text-primary me-1"></i>Pilih Hari yang Di-Reject: <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAllDays">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua Hari
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearDays">
                                <i class="fas fa-square me-1"></i>Hapus Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Container Daftar Hari Checkbox (Senin s/d Minggu) -->
                    <div class="border rounded p-3 bg-light mb-3" id="daysCheckboxContainer" style="max-height: 250px; overflow-y: auto;">
                        <div class="text-muted text-center py-3">Memuat daftar tanggal...</div>
                    </div>

                    <!-- Textarea Catatan / Alasan Reject -->
                    <div class="mb-3">
                        <label for="reject_note" class="form-label font-weight-bold text-dark">
                            <i class="fas fa-comment-alt text-danger me-1"></i>Catatan / Alasan Reject Perbaikan: <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="reject_note" 
                                  name="reject_note" 
                                  rows="3" 
                                  required 
                                  placeholder="Contoh: Parameter suhu pada hari Rabu salah input (melebihi batas toleransi), mohon Foreman &amp; Operator perbaiki form dan re-approve."></textarea>
                        <div class="form-text text-muted">
                            Catatan ini akan langsung tampil pada halaman Daily / Startup Approval Foreman dan Operator untuk memandu perbaikan.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger font-weight-bold shadow-sm" id="btnSubmitReject">
                        <i class="fas fa-paper-plane me-1"></i>Kirim Granular Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT LOGIC UNTUK GRANULAR REJECT MODAL -->
<!-- ========================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalRejectWeekly');
    const formReject = document.getElementById('formRejectWeekly');
    const daysContainer = document.getElementById('daysCheckboxContainer');
    const btnSelectAll = document.getElementById('btnSelectAllDays');
    const btnClearAll = document.getElementById('btnClearDays');

    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const selectMachine = document.getElementById('modal_select_machine');
    const linkDetail = document.getElementById('modal_link_detail_form');

    let currentWeeklyId = '';
    let currentPeriodStart = '';
    let currentPeriodEnd = '';
    let currentActiveDates = {};
    let currentMachines = [];
    let currentDevice = '';
    let currentProcess = '';
    let currentMachno = '-';

    function populateMachineSelect() {
        selectMachine.innerHTML = '';
        if (currentMachines.length > 0) {
            const optAll = document.createElement('option');
            optAll.value = 'ALL';
            optAll.textContent = '[Semua Mesin] - Reject hari terpilih untuk seluruh mesin (' + currentMachines.length + ' Mesin)';
            selectMachine.appendChild(optAll);

            currentMachines.forEach(function (m, idx) {
                const opt = document.createElement('option');
                opt.value = m.machno;
                opt.dataset.index = idx;
                opt.dataset.device = m.device;
                opt.dataset.process = m.process;
                opt.textContent = 'Mesin ' + m.machno + ' (' + m.device_name + ' - ' + m.process_name + ')';
                selectMachine.appendChild(opt);
            });
        } else {
            populateMachineSelectFallback();
            return;
        }
        onMachineSelectionChange();
    }

    function populateMachineSelectFallback() {
        selectMachine.innerHTML = '';
        const optDefault = document.createElement('option');
        optDefault.value = currentMachno !== '-' ? currentMachno : 'ALL';
        optDefault.textContent = currentMachno !== '-' ? ('Mesin ' + currentMachno) : 'Semua Mesin';
        selectMachine.appendChild(optDefault);
        onMachineSelectionChange();
    }

    // Pasang Event Listener saat tombol Reject Hari diklik
    document.querySelectorAll('.btn-open-reject-weekly').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentWeeklyId = this.getAttribute('data-id');
            currentPeriodStart = this.getAttribute('data-period-start');
            currentPeriodEnd = this.getAttribute('data-period-end');
            currentDevice = this.getAttribute('data-device') || '-';
            currentProcess = this.getAttribute('data-process') || '-';
            currentMachno = this.getAttribute('data-machno') || '-';
            const dept = this.getAttribute('data-dept') || 'qc';

            try {
                currentActiveDates = JSON.parse(this.getAttribute('data-active-dates') || '{}');
            } catch (e) {
                currentActiveDates = {};
            }

            // Set Form Data
            document.getElementById('modal_reject_weekly_id').value = currentWeeklyId;
            document.getElementById('modal_reject_departemen').value = dept;
            document.getElementById('modal_info_period').innerText = currentPeriodStart + ' s/d ' + currentPeriodEnd;
            document.getElementById('modal_info_dept').innerText = dept.toUpperCase();
            document.getElementById('reject_note').value = '';

            if (linkDetail) {
                linkDetail.href = '<?= base_url("approval/detail_mingguan") ?>/' + currentWeeklyId;
            }

            // Tampilkan loading sebelum AJAX selesai
            selectMachine.innerHTML = '<option value="">â³ Memuat daftar mesin...</option>';
            daysContainer.innerHTML = '<div class="col-12 text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary d-block"></i>Memuat rincian mesin & tanggal...</div>';

            // Ambil rincian mesin & tanggal aktif secara on-demand via AJAX (sangat hemat memori server)
            fetch('<?= base_url("approval/getWeeklyMachines") ?>?weekly_id=' + encodeURIComponent(currentWeeklyId))
                .then(function(res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(function(res) {
                    if (res && res.status === 'success' && res.machines && res.machines.length > 0) {
                        currentMachines = res.machines;
                        populateMachineSelect();
                    } else {
                        currentMachines = [];
                        populateMachineSelectFallback();
                    }
                })
                .catch(function(err) {
                    console.warn('Gagal memuat rincian mesin mingguan:', err);
                    currentMachines = [];
                    populateMachineSelectFallback();
                });
        });
    });

    selectMachine.addEventListener('change', onMachineSelectionChange);

    function onMachineSelectionChange() {
        const selectedVal = selectMachine.value;
        const selectedOpt = selectMachine.options[selectMachine.selectedIndex];

        if (selectedVal === 'ALL' || selectedVal === '') {
            document.getElementById('modal_info_machno').innerText = 'Semua Mesin';
            document.getElementById('modal_info_device_process').innerText = currentDevice + ' &bull; ' + currentProcess;
            document.getElementById('modal_hidden_device').value = '';
            document.getElementById('modal_hidden_process').value = '';

            generateDaysList(currentPeriodStart, currentPeriodEnd, currentActiveDates);
        } else {
            const idx = selectedOpt.dataset.index;
            const machData = (idx !== undefined && currentMachines[idx]) ? currentMachines[idx] : null;

            if (machData) {
                document.getElementById('modal_info_machno').innerText = machData.machno;
                document.getElementById('modal_info_device_process').innerText = machData.device_name + ' &bull; ' + machData.process_name;
                document.getElementById('modal_hidden_device').value = machData.device;
                document.getElementById('modal_hidden_process').value = machData.process;

                generateDaysListForMachine(machData.dates);
            } else {
                document.getElementById('modal_info_machno').innerText = selectedVal;
                document.getElementById('modal_info_device_process').innerText = currentDevice + ' &bull; ' + currentProcess;
                generateDaysList(currentPeriodStart, currentPeriodEnd, currentActiveDates);
            }
        }
    }

    // Helper untuk generate tanggal spesifik mesin
    function generateDaysListForMachine(datesObj) {
        daysContainer.innerHTML = '';
        if (!datesObj || Object.keys(datesObj).length === 0) {
            daysContainer.innerHTML = '<div class="text-muted text-center py-2">Tidak ada data hari terisi untuk mesin ini.</div>';
            return;
        }

        let listHtml = '<div class="row g-2">';
        for (const ymd in datesObj) {
            const item = datesObj[ymd];
            const label = item.label || ymd;
            const rawSt = String(item.status || item.raw_status || 'Pending').trim().toLowerCase();
            const isRej = (rawSt === 'reject');
            const isPend = (rawSt === 'pending' || rawSt === '' || rawSt === '-');
            const isAppr = (!isRej && !isPend);

            let badge = '';
            if (isRej) {
                badge = '<span class="badge bg-danger ms-2"><i class="fas fa-times-circle me-1"></i>Sedang Di-reject</span>';
            } else if (isAppr) {
                badge = '<span class="badge bg-success ms-2"><i class="fas fa-check-circle me-1"></i>Approved</span>';
            } else {
                badge = '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-clock me-1"></i>Pending</span>';
            }

            listHtml += `
                <div class="col-md-6">
                    <div class="form-check p-2 rounded border bg-white h-100 d-flex align-items-center">
                        <input class="form-check-input ms-1 me-2 day-reject-checkbox" 
                               type="checkbox" 
                               name="rejected_days[]" 
                               value="${ymd}" 
                               id="chk_${ymd}">
                        <label class="form-check-label w-100 cursor-pointer d-flex justify-content-between align-items-center" for="chk_${ymd}">
                            <span class="font-weight-bold text-dark">${label}</span>
                            <div>${badge}</div>
                        </label>
                    </div>
                </div>
            `;
        }
        listHtml += '</div>';
        daysContainer.innerHTML = listHtml;
    }

    // Helper untuk generate 7 hari
    function generateDaysList(startStr, endStr, activeDates) {
        daysContainer.innerHTML = '';
        if (!startStr || !endStr) {
            daysContainer.innerHTML = '<div class="text-danger">Tanggal periode tidak valid.</div>';
            return;
        }

        const startParts = startStr.split('-');
        const endParts = endStr.split('-');
        const startDate = new Date(parseInt(startParts[0], 10), parseInt(startParts[1], 10) - 1, parseInt(startParts[2], 10));
        const endDate = new Date(parseInt(endParts[0], 10), parseInt(endParts[1], 10) - 1, parseInt(endParts[2], 10));

        let cur = new Date(startDate);
        let listHtml = '<div class="row g-2">';

        while (cur <= endDate) {
            const y = cur.getFullYear();
            const m = String(cur.getMonth() + 1).padStart(2, '0');
            const d = String(cur.getDate()).padStart(2, '0');
            const ymd = `${y}-${m}-${d}`;
            const dayName = dayNames[cur.getDay()];
            const formattedDate = `${dayName}, ${d}/${m}/${y}`;

            const info = activeDates[ymd] || null;
            const docCount = info ? (info.count || 0) : 0;
            const isAlreadyReject = info ? (info.reject_count > 0) : false;
            const hasPending = info ? ((info.pending_count || 0) > 0) : false;

            let badgeHtml = '';
            if (docCount > 0) {
                if (isAlreadyReject) {
                    badgeHtml = `<span class="badge bg-danger ms-1" style="font-size: 0.72rem;"><i class="fas fa-times-circle me-1"></i>Di-reject</span>`;
                } else if (hasPending) {
                    badgeHtml = `<span class="badge bg-warning text-dark ms-1" style="font-size: 0.72rem;"><i class="fas fa-clock me-1"></i>Pending</span>`;
                } else {
                    badgeHtml = `<span class="badge bg-success ms-1" style="font-size: 0.72rem;"><i class="fas fa-check-circle me-1"></i>Approved</span>`;
                }
                badgeHtml += ` <span class="badge bg-secondary ms-1" style="font-size: 0.72rem;">${docCount} Form</span>`;
            } else {
                badgeHtml = `<span class="badge bg-light text-muted border ms-2" style="font-size: 0.72rem;">Tidak ada data</span>`;
            }

            listHtml += `
                <div class="col-md-6">
                    <div class="form-check p-2 rounded border bg-white h-100 d-flex align-items-center">
                        <input class="form-check-input ms-1 me-2 day-reject-checkbox" 
                               type="checkbox" 
                               name="rejected_days[]" 
                               value="${ymd}" 
                               id="chk_${ymd}">
                        <label class="form-check-label w-100 cursor-pointer d-flex justify-content-between align-items-center" for="chk_${ymd}">
                            <span class="font-weight-bold text-dark">${formattedDate}</span>
                            <div>${badgeHtml}</div>
                        </label>
                    </div>
                </div>
            `;

            cur.setDate(cur.getDate() + 1);
        }

        listHtml += '</div>';
        daysContainer.innerHTML = listHtml;
    }

    // Tombol Pilih Semua Hari
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            document.querySelectorAll('.day-reject-checkbox').forEach(function (chk) {
                chk.checked = true;
            });
        });
    }

    // Tombol Hapus Pilihan
    if (btnClearAll) {
        btnClearAll.addEventListener('click', function () {
            document.querySelectorAll('.day-reject-checkbox').forEach(function (chk) {
                chk.checked = false;
            });
        });
    }

    // Validasi Submit Form
    if (formReject) {
        formReject.addEventListener('submit', function (e) {
            const checkedCount = document.querySelectorAll('.day-reject-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Peringatan: Anda wajib memilih minimal 1 tanggal/hari yang bermasalah untuk di-reject!');
                return false;
            }

            const noteVal = document.getElementById('reject_note').value.trim();
            if (noteVal === '') {
                e.preventDefault();
                alert('Peringatan: Catatan / alasan reject wajib diisi!');
                document.getElementById('reject_note').focus();
                return false;
            }

            const machVal = (selectMachine && selectMachine.value !== "ALL" && selectMachine.value !== "") ? ("Mesin " + selectMachine.value) : "Semua Mesin";
            return confirm(`Apakah Anda yakin ingin me-reject ${checkedCount} hari pada ${machVal}? Daily check pada hari tersebut akan di-unlock.`);
        });
    }
});
</script>

<style>
.table-danger-subtle {
    background-color: #fff5f5 !important;
}
.cursor-pointer {
    cursor: pointer;
}
.btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
}
</style>
</main>
<?= $this->endSection(); ?>
