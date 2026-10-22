<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-week me-2"></i>Checksheet Mingguan (7 Hari) - <?= esc($process_name ?: $process) ?>
                </h4>
                <div class="text-muted small mt-1">
                    <span><i class="far fa-calendar-alt text-primary me-1"></i> Periode Minggu: <strong><?= date('d M Y', strtotime($period_start)) ?> &mdash; <?= date('d M Y', strtotime($period_end)) ?> (23:59)</strong></span>
                    <span class="ms-3"><i class="fas fa-calendar-check text-success me-1"></i> Terisi: <strong><?= count($alldata) ?> Hari</strong></span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('history/exportExcel?typeProcess=startup&dateStart='.$period_start.'&dateEnd='.$period_end.'&device='.urlencode($device).'&process='.urlencode($process).'&machno='.urlencode($machno)) ?>" 
                   class="btn btn-success btn-sm shadow-sm" target="_blank" title="Download Excel 7 Hari (Fit 1 Page A4 Landscape)">
                    <i class="fas fa-file-excel me-1"></i> Export Excel (7 Hari)
                </a>
                <a href="<?= base_url('history/exportPDF?typeProcess=startup&dateStart='.$period_start.'&dateEnd='.$period_end.'&device='.urlencode($device).'&process='.urlencode($process).'&machno='.urlencode($machno)) ?>" 
                   class="btn btn-danger btn-sm shadow-sm" target="_blank" title="Download PDF 7 Hari">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF (7 Hari)
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-print me-1"></i> Cetak Form
                </button>
                <a href="<?= base_url('approval/detail_mingguan/' . $weekly_id) ?>" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Mingguan
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
                    <strong>Weekly Approval:</strong> <span class="badge bg-primary">#<?= esc($weekly_id) ?></span>
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
    background-color: #fff !important;
}
.checksheet-detail-wrapper table th,
.checksheet-detail-wrapper table td {
    border: 1px solid #dee2e6 !important;
    padding: 6px 8px !important;
    vertical-align: middle !important;
    text-align: center !important;
}
.checksheet-detail-wrapper table td:first-child,
.checksheet-detail-wrapper table td[colspan="3"],
.checksheet-detail-wrapper table td[colspan="4"],
.checksheet-detail-wrapper table td[colspan="5"],
.checksheet-detail-wrapper table td[colspan="6"],
.checksheet-detail-wrapper table td[colspan="10"] {
    text-align: left !important;
    background-color: #f8f9fa !important;
    font-weight: 500 !important;
}
@media print {
    .navbar, .btn, footer, .card-header button, .card-header a {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .checksheet-detail-wrapper {
        border: none !important;
        padding: 0 !important;
    }
}
</style>
<?= $this->endSection(); ?>