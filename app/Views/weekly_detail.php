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
            <?php if(session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('message') ?: session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($mingguan['reject_note']) || !empty($mingguan['rejected_days'])): ?>
                <div class="alert alert-danger py-2 px-3 mb-4 border-start border-danger border-4 shadow-sm">
                    <div class="font-weight-bold text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>Catatan Penolakan Mingguan (Perlu Revisi Foreman / Operator):
                    </div>
                    <div class="mt-1 small text-dark">
                        <strong>Hari/Mesin Di-reject:</strong> <span class="badge bg-danger"><?= esc($mingguan['rejected_days'] ?? '-') ?></span>
                        &bull; Oleh: <strong><?= esc($mingguan['rejected_by'] ?? '-') ?></strong>
                        <?= !empty($mingguan['rejected_at']) ? ' (' . date('d/m/Y H:i', strtotime($mingguan['rejected_at'])) . ')' : '' ?>
                    </div>
                    <div class="fst-italic text-danger mt-1 small">
                        "<?= esc($mingguan['reject_note']) ?>"
                    </div>
                </div>
            <?php endif; ?>
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
                                    <?php if(!empty($row['rejected_count']) && $row['rejected_count'] > 0): ?>
                                        <span class="badge bg-danger shadow-sm">
                                            <i class="fas fa-times-circle me-1"></i><?= $row['rejected_count'] ?> Hari Di-reject
                                        </span>
                                        <?php if($row['pending_count'] > 0): ?>
                                            <div class="badge bg-warning text-dark mt-1">
                                                <i class="fas fa-clock me-1"></i><?= $row['pending_count'] ?> Hari Pending
                                            </div>
                                        <?php endif; ?>
                                    <?php elseif($row['pending_count'] > 0): ?>
                                        <span class="badge bg-warning text-dark shadow-sm">
                                            <i class="fas fa-clock me-1"></i><?= $row['pending_count'] ?> Hari Pending
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i>Lengkap (<?= $row['approved_count'] ?> Hari)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <a href="<?= base_url('approval/weekly_form/' . $mingguan['id'] . '?device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-primary shadow-sm" target="_blank" title="Lihat Checksheet Lengkap 7 Hari">
                                            <i class="fas fa-eye me-1"></i> Lihat Form (7 Hari)
                                        </a>
                                        <a href="<?= base_url('history/exportExcel?typeProcess=startup&dateStart=' . $mingguan['period_start'] . '&dateEnd=' . $mingguan['period_end'] . '&device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-success shadow-sm" target="_blank" title="Export Excel 7 Hari (Fit to 1 Page A4 Landscape)">
                                            <i class="fas fa-file-excel"></i>
                                        </a>
                                        <a href="<?= base_url('history/exportPDF?typeProcess=startup&dateStart=' . $mingguan['period_start'] . '&dateEnd=' . $mingguan['period_end'] . '&device=' . urlencode($row['device']) . '&process=' . urlencode($row['process']) . '&machno=' . urlencode($row['machno'])) ?>" 
                                           class="btn btn-sm btn-danger shadow-sm" target="_blank" title="Export PDF 7 Hari">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        <?php if(!empty($canQc) || !empty($canProd)): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger btn-open-reject-machine shadow-sm"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalRejectMachine"
                                                    data-weekly-id="<?= $mingguan['id'] ?>"
                                                    data-period-start="<?= $mingguan['period_start'] ?>"
                                                    data-period-end="<?= $mingguan['period_end'] ?>"
                                                    data-device="<?= esc($row['device']) ?>"
                                                    data-device-name="<?= esc($row['device_name'] ?: $row['device']) ?>"
                                                    data-process="<?= esc($row['process']) ?>"
                                                    data-process-name="<?= esc($row['process_name'] ?: $row['process']) ?>"
                                                    data-machno="<?= esc($row['machno'] ?: '-') ?>"
                                                    data-dept="<?= !empty($canQc) ? 'qc' : 'production' ?>"
                                                    data-dates='<?= htmlspecialchars(json_encode(array_values($row['dates_detail'] ?? [])), ENT_QUOTES, 'UTF-8') ?>'
                                                    title="Reject hari tertentu khusus untuk mesin <?= esc($row['machno'] ?: '-') ?>">
                                                <i class="fas fa-times-circle me-1"></i>Reject Hari...
                                            </button>
                                        <?php endif; ?>
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
<!-- ========================================================================= -->
<!-- MODAL GRANULAR REJECT KHUSUS FORM MESIN SPESIFIK -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalRejectMachine" tabindex="-1" aria-labelledby="modalRejectMachineLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <form action="<?= base_url('approval/rejectWeeklyStartup') ?>" method="POST" id="formRejectMachine">
                <?= csrf_field() ?>
                <input type="hidden" name="weekly_id" id="modal_m_weekly_id" value="">
                <input type="hidden" name="departemen" id="modal_m_departemen" value="">
                <input type="hidden" name="device" id="modal_m_device" value="">
                <input type="hidden" name="process" id="modal_m_process" value="">
                <input type="hidden" name="machno" id="modal_m_machno" value="">
                <input type="hidden" name="redirect_to" value="detail">

                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="modalRejectMachineLabel">
                        <i class="fas fa-calendar-times me-2"></i>Granular Day-Unlock Reject &ndash; <span id="modal_m_header_title">Mesin</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Callout Info Target Dokumen Mesin -->
                    <div class="alert alert-light border p-3 mb-3 rounded shadow-sm">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Periode Mingguan:</span>
                                <strong class="text-dark" id="modal_m_info_period">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">No. Mesin:</span>
                                <span class="badge bg-dark fs-6" id="modal_m_info_machno">-</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Device:</span>
                                <strong class="text-dark" id="modal_m_info_device">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nama Proses:</span>
                                <strong class="text-dark" id="modal_m_info_process">-</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Approver Role:</span>
                                <span class="badge bg-danger fs-6" id="modal_m_info_dept">QC</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 px-3 small border-start border-warning border-4 mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Pilih <strong>hari/tanggal spesifik</strong> pada mesin ini yang memiliki kesalahan. Status daily check Foreman pada hari yang dicentang akan di-unlock menjadi <strong>'Reject'</strong> agar dapat diperbaiki. Mesin lain atau hari lain yang tidak dicentang akan <strong>tetap berstatus Approved</strong>.
                    </div>

                    <!-- Toolbar Pilihan Hari -->
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <label class="form-label font-weight-bold text-dark m-0">
                            <i class="far fa-calendar-check text-primary me-1"></i>Pilih Hari yang Di-Reject pada Mesin Ini: <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnSelectAllMachineDays">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearMachineDays">
                                <i class="fas fa-square me-1"></i>Hapus Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Container Daftar Hari Checkbox Mesin Ini -->
                    <div class="border rounded p-3 bg-light mb-3" id="machineDaysCheckboxContainer" style="max-height: 250px; overflow-y: auto;">
                        <div class="text-muted text-center py-3">Memuat daftar hari...</div>
                    </div>

                    <!-- Textarea Catatan / Alasan Reject -->
                    <div class="mb-3">
                        <label for="modal_m_reject_note" class="form-label font-weight-bold text-dark">
                            <i class="fas fa-comment-alt text-danger me-1"></i>Catatan / Alasan Reject Perbaikan: <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="modal_m_reject_note" 
                                  name="reject_note" 
                                  rows="3" 
                                  required 
                                  placeholder="Contoh: Parameter suhu pada hari Senin 19/10 out of spec, mohon perbaiki form dan re-approve."></textarea>
                        <div class="form-text text-muted">
                            Catatan ini akan langsung tampil di halaman Daily Approval Foreman & Operator untuk memandu perbaikan khusus mesin ini.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger font-weight-bold shadow-sm" id="btnSubmitMachineReject">
                        <i class="fas fa-paper-plane me-1"></i>Kirim Reject Mesin Ini
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formReject = document.getElementById('formRejectMachine');
    const container = document.getElementById('machineDaysCheckboxContainer');
    const btnSelectAll = document.getElementById('btnSelectAllMachineDays');
    const btnClearAll = document.getElementById('btnClearMachineDays');

    document.querySelectorAll('.btn-open-reject-machine').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const weeklyId    = this.getAttribute('data-weekly-id');
            const periodStart = this.getAttribute('data-period-start');
            const periodEnd   = this.getAttribute('data-period-end');
            const device      = this.getAttribute('data-device') || '';
            const deviceName  = this.getAttribute('data-device-name') || device;
            const process     = this.getAttribute('data-process') || '';
            const processName = this.getAttribute('data-process-name') || process;
            const machno      = this.getAttribute('data-machno') || '-';
            const dept        = this.getAttribute('data-dept') || 'qc';

            let datesList = [];
            try {
                datesList = JSON.parse(this.getAttribute('data-dates') || '[]');
            } catch (e) {
                datesList = [];
            }

            document.getElementById('modal_m_weekly_id').value = weeklyId;
            document.getElementById('modal_m_departemen').value = dept;
            document.getElementById('modal_m_device').value = device;
            document.getElementById('modal_m_process').value = process;
            document.getElementById('modal_m_machno').value = machno;

            document.getElementById('modal_m_header_title').innerText = 'Mesin ' + machno;
            document.getElementById('modal_m_info_period').innerText = periodStart + ' s/d ' + periodEnd;
            document.getElementById('modal_m_info_machno').innerText = machno;
            document.getElementById('modal_m_info_device').innerText = deviceName;
            document.getElementById('modal_m_info_process').innerText = processName;
            document.getElementById('modal_m_info_dept').innerText = dept.toUpperCase();
            document.getElementById('modal_m_reject_note').value = '';

            // Render Checkboxes
            container.innerHTML = '';
            if (datesList.length === 0) {
                container.innerHTML = '<div class="text-muted text-center py-2">Tidak ada tanggal checksheet yang terdaftar pada mesin ini.</div>';
                return;
            }

            let html = '<div class="row g-2">';
            datesList.forEach(function (item) {
                const ymd = item.date;
                const label = item.date_label || ymd;
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

                html += `
                    <div class="col-md-6">
                        <div class="form-check p-2 rounded border bg-white h-100 d-flex align-items-center">
                            <input class="form-check-input ms-1 me-2 m-day-checkbox" 
                                   type="checkbox" 
                                   name="rejected_days[]" 
                                   value="${ymd}" 
                                   id="m_chk_${ymd}">
                            <label class="form-check-label w-100 cursor-pointer d-flex justify-content-between align-items-center" for="m_chk_${ymd}">
                                <span class="font-weight-bold text-dark">${label}</span>
                                <div>${badge}</div>
                            </label>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        });
    });

    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function () {
            document.querySelectorAll('.m-day-checkbox').forEach(function (chk) {
                chk.checked = true;
            });
        });
    }

    if (btnClearAll) {
        btnClearAll.addEventListener('click', function () {
            document.querySelectorAll('.m-day-checkbox').forEach(function (chk) {
                chk.checked = false;
            });
        });
    }

    if (formReject) {
        formReject.addEventListener('submit', function (e) {
            const checkedCount = document.querySelectorAll('.m-day-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Peringatan: Anda wajib memilih minimal 1 hari/tanggal pada mesin ini untuk di-reject!');
                return false;
            }

            const noteVal = document.getElementById('modal_m_reject_note').value.trim();
            if (noteVal === '') {
                e.preventDefault();
                alert('Peringatan: Catatan / alasan reject perbaikan wajib diisi!');
                document.getElementById('modal_m_reject_note').focus();
                return false;
            }

            const machVal = document.getElementById('modal_m_machno').value;
            return confirm(`Apakah Anda yakin ingin me-reject ${checkedCount} hari pada Mesin ${machVal}? Daily check Foreman pada tanggal tersebut akan di-unlock untuk perbaikan.`);
        });
    }
});
</script>
</main>
<?= $this->endSection(); ?>