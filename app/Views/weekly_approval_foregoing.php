<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>

<div class="container-fluid px-4 mt-3 mb-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-microscope me-2"></i>Approval Data &ndash; Foregoing Checksheet
                </h4>
                <div class="small text-muted mt-1">
                    Approval lembar checksheet Foregoing (Pemeriksaan awal sampel &amp; parameter offset sebelum produksi)
                </div>
            </div>
            <a href="<?= base_url('approval/weekly') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Pusat Approval
            </a>
        </div>
        <div class="card-body py-5 text-center">
            <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-microscope fa-3x"></i>
            </div>
            <h4 class="font-weight-bold text-dark mb-2">Modul Foregoing Approval</h4>
            <p class="text-muted mx-auto" style="max-width: 550px;">
                Saat ini modul approval Foregoing Checksheet disiapkan untuk alur kerja batch. Anda dapat segera mengaktifkannya sesuai spesifikasi lembar sampling selanjutnya.
            </p>
            <div class="mt-4">
                <a href="<?= base_url('approval/weekly?type=startup') ?>" class="btn btn-primary btn-sm me-2 shadow-sm">
                    <i class="fas fa-calendar-week me-1"></i> Buka Startup Approval
                </a>
                <a href="<?= base_url('approval/weekly?type=production') ?>" class="btn btn-success btn-sm shadow-sm">
                    <i class="fas fa-file-invoice me-1"></i> Buka Production Approval
                </a>
            </div>
        </div>
    </div>
</div>
</main>
<?= $this->endSection(); ?>