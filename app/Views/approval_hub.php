<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-4 mb-5">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="font-weight-bold text-dark m-0">
                <i class="fas fa-th-large text-primary me-2"></i>Pusat Approval Checksheet
            </h3>
            <p class="text-muted small mb-0 mt-1">
                Pilih modul dokumen checksheet yang ingin Anda tinjau, approve, atau cetak laporannya:
            </p>
        </div>
    </div>

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

    <!-- 3 Choice Cards -->
    <div class="row g-4 justify-content-center">
        <!-- 1. STARTUP CHECKSHEET -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-lift transition-all" style="border-radius: 12px; border-top: 4px solid #0d6efd !important;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-week fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Startup Checksheet</h4>
                        <span class="badge bg-primary mb-3">Weekly Approval (7 Hari)</span>
                        <p class="text-muted small mb-3">
                            Approval checksheet startup mingguan berbasis periode Senin s/d Minggu (23:59). Dilengkapi verifikasi kelengkapan Daily Approval serta Dual Sign-off (QC & Production).
                        </p>
                    </div>
                    <div>
                        <ul class="list-unstyled text-start small text-muted border-top pt-3 mb-3">
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Periode: Kalender Mingguan</li>
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Approver: QC & Production (Wajib Keduanya)</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Syarat: Semua Daily Check Selesai</li>
                        </ul>
                        <a href="<?= base_url('approval/weekly?type=startup') ?>" class="btn btn-primary w-100 font-weight-bold py-2 shadow-sm">
                            Buka Approval Startup <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PRODUCTION CHECKSHEET -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-lift transition-all" style="border-radius: 12px; border-top: 4px solid #198754 !important;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Production Checksheet</h4>
                        <span class="badge bg-success mb-3">Page Approval (20 Data Penuh)</span>
                        <p class="text-muted small mb-3">
                            Approval dokumen Production Process Control berbasis batch 1 lembar penuh (tepat 20 data per mesin). Dilengkapi fitur pengurutan tanggal terbaru/terlama.
                        </p>
                    </div>
                    <div>
                        <ul class="list-unstyled text-start small text-muted border-top pt-3 mb-3">
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Format: 1 Lembar Penuh (20 Data)</li>
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Approver: QC atau Production (Salah Satu)</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Syarat: Kuota 20 Data Terpenuhi</li>
                        </ul>
                        <a href="<?= base_url('approval/weekly?type=production') ?>" class="btn btn-success w-100 font-weight-bold py-2 shadow-sm">
                            Buka Approval Production <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. FOREGOING CHECKSHEET -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 hover-lift transition-all" style="border-radius: 12px; border-top: 4px solid #ffc107 !important;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-microscope fa-2x"></i>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Foregoing Checksheet</h4>
                        <span class="badge bg-warning text-dark mb-3">Sampling & Parameter Check</span>
                        <p class="text-muted small mb-3">
                            Pemeriksaan awal sampel produk dan penyesuaian parameter offset mesin (sumbu X, Z, Tetha) sebelum proses produksi masal dijalankan.
                        </p>
                    </div>
                    <div>
                        <ul class="list-unstyled text-start small text-muted border-top pt-3 mb-3">
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Format: Multi-Sumbu Sampling </li>
                            <li class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>Approval: Foreman / Leader / Supervisor</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Status: Integrasi Lanjutan</li>
                        </ul>
                        <a href="<?= base_url('approval/weekly?type=foregoing') ?>" class="btn btn-warning w-100 font-weight-bold py-2 shadow-sm text-dark">
                            Buka Approval Foregoing <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.12) !important;
}
</style>

<?= $this->endSection(); ?>