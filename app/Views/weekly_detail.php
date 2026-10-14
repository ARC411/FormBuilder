<?= $this->include('layout/header') ?>
<?= $this->include('layout/navbar') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Dokumen Mingguan #<?= $mingguan['id'] ?></h2>
        <a href="<?= base_url('approval/weekly') ?>" class="btn btn-secondary">Kembali</a>
    </div>
    
    <div class="alert alert-info">
        <strong>Periode:</strong> <?= date('d F Y', strtotime($mingguan['period_start'])) ?> - <?= date('d F Y', strtotime($mingguan['period_end'])) ?>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
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
                    <?php foreach($alldata as $row): ?>
                    <tr>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= $row['device_name'] ?></td>
                        <td><?= $row['name'] ?> <br><small class="text-muted">(<?= $row['process'] ?>)</small></td>
                        <td><?= $row['machno'] ?></td>
                        <td><?= $row['lotno'] ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= $row['status'] ?></span>
                        </td>
                        <td>
                            <!-- Menggunakan fungsi history yang sudah ada di sistem Anda untuk melihat form lengkapnya -->
                            <a href="<?= base_url('startup/startupByNumber/'.$row['number']) ?>" class="btn btn-sm btn-primary" target="_blank">Lihat Form</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada dokumen pengecekan pada minggu ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->include('layout/footer') ?>