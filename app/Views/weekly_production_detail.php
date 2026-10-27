<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-invoice me-2"></i>Checksheet Production (20 Data / 1 Lembar) &ndash; <?= esc($process_name ?: $process) ?>
                </h4>
                <div class="text-muted small mt-1">
                    <span><i class="fas fa-layer-group text-primary me-1"></i> <strong>Lembar <?= esc($batch['page_no'] ?? 1) ?></strong> (Batch #<?= esc($weekly_id) ?>)</span>
                    <span class="ms-3"><i class="far fa-calendar-alt text-primary me-1"></i> Rentang Tanggal Data: <strong><?= date('d M Y', strtotime($period_start)) ?> &mdash; <?= date('d M Y', strtotime($period_end)) ?></strong></span>
                    <span class="ms-3"><i class="fas fa-check-circle text-success me-1"></i> Status Lembar: <strong><?= (count($alldata) >= 20) ? '20 / 20 Data (Penuh)' : count($alldata) . ' / 20 Data' ?></strong></span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('history/exportExcel?typeProcess=production&weekly_id='.$weekly_id.'&device='.urlencode($device).'&process='.urlencode($process).'&machno='.urlencode($machno)) ?>" 
                   class="btn btn-success btn-sm shadow-sm" target="_blank" title="Download Excel 1 Lembar (20 Data Penuh)">
                    <i class="fas fa-file-excel me-1"></i> Export Excel (20 Data)
                </a>
                <a href="<?= base_url('history/exportPDF?typeProcess=production&weekly_id='.$weekly_id.'&device='.urlencode($device).'&process='.urlencode($process).'&machno='.urlencode($machno)) ?>" 
                   class="btn btn-danger btn-sm shadow-sm" target="_blank" title="Download PDF 1 Lembar (20 Data Penuh)">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF (20 Data)
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-print me-1"></i> Cetak Form
                </button>
                <a href="<?= base_url('approval/weekly?tab=production') ?>" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Rekap Approval
                </a>
            </div>
        </div>
        
        <div class="card-body bg-light py-2 px-3 border-bottom">
            <div class="row g-2 text-dark small align-items-center">
                <div class="col-sm-6 col-md-3">
                    <strong>Device:</strong> <span class="badge bg-dark"><?= esc(strtoupper($device_name ?: $device)) ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Process:</strong> <span class="badge bg-info text-dark"><?= esc(strtoupper($process_name ?: $process)) ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Machine No:</strong> <span class="badge bg-secondary"><?= esc($machno ?: '-') ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Status Approval:</strong> 
                    <?php if(!empty($batch['status']) && $batch['status'] === 'Approve'): ?>
                        <span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Approved</span>
                        <small class="text-dark ms-1">by <b><?= esc($batch['approver'] ?: ($batch['approver_qc'] ?: $batch['approver_production'])) ?></b> (<?= esc($batch['approver_role'] ?: 'QC/Prod') ?>)</small>
                    <?php elseif(!empty($batch['status']) && $batch['status'] === 'Reject'): ?>
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejected</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            <?= $machnoHidden ?>
            <div class="table-responsive bg-white p-2 border rounded shadow-sm checksheet-detail-wrapper" style="overflow-x: auto; max-width: 100%;">
                <?= $tableHtml ?>
            </div>
        </div>
    </div>
</div>
</main>

<style>
/* Styling form checksheet agar profesional dan rapi */
.checksheet-detail-wrapper table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    font-size: 13px !important;
}
.checksheet-detail-wrapper table th, 
.checksheet-detail-wrapper table td {
    padding: 6px 8px !important;
    vertical-align: middle !important;
}
.checksheet-detail-wrapper thead th {
    background-color: #f1f4f9 !important;
    color: #212529 !important;
    font-weight: 600 !important;
}
</style>
<?= $this->endSection(); ?>