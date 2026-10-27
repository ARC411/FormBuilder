<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-check me-2"></i>Detail Dokumen Checksheet #<?= esc($number) ?>
                </h4>
                <div class="text-muted small mt-1">
                    <span><i class="far fa-calendar-alt me-1"></i> Tanggal Input: <strong><?= !empty($created_at) ? date('d-m-Y H:i', strtotime($created_at)) : '-' ?></strong></span>
                    <span class="ms-3"><i class="fas fa-tag me-1"></i> Status: <span class="badge bg-<?= (stripos($status, 'approved') !== false || stripos($status, 'check by') !== false) ? 'success' : 'warning text-dark' ?>"><?= esc($status) ?></span></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-print me-1"></i> Cetak Form
                </button>
                <button type="button" onclick="if(window.history.length > 1) { window.history.back(); } else { window.close(); }" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali / Tutup
                </button>
            </div>
        </div>
        
        <div class="card-body bg-light py-2 px-3 border-bottom">
            <div class="row g-2 text-dark small align-items-center">
                <div class="col-sm-6 col-md-3">
                    <strong>Device:</strong> <span class="badge bg-dark"><?= esc(strtoupper($device)) ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Process:</strong> <span class="badge bg-info text-dark"><?= esc(strtoupper($process)) ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Machine No:</strong> <span class="badge bg-secondary"><?= esc($machno ?: '-') ?></span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <strong>Lot No:</strong> <span class="badge bg-light text-dark border"><?= esc($lotno ?: '-') ?></span>
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
/* Styling agar form tampil rapi dan profesional */
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
    .navbar, .btn, footer, .card-header button {
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