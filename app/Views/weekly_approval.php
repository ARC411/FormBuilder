<?= $this->include('layout/header') ?>
<?= $this->include('layout/navbar') ?>

<div class="container mt-4">
    <h2>Weekly Approval Data</h2>
    
    <?php if(session()->getFlashdata('message')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Mingguan</th>
                <th>Periode (Senin - Minggu)</th>
                <th>Total Dokumen</th>
                <th>Status QC</th>
                <th>Status Production</th>
                <th>Aksi Anda</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($weekly_data as $row): ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><?= date('d M Y', strtotime($row['period_start'])) ?> - <?= date('d M Y', strtotime($row['period_end'])) ?></td>
                <td>
                    <!-- Link ini bisa diarahkan ke halaman detail yang memfilter startup berdasarkan weekly_id -->
                    <a href="<?= base_url('approval/detail_mingguan/'.$row['id']) ?>"><?= $row['total_docs'] ?> Dokumen</a>
                </td>
                
                <!-- Badge Status QC -->
                <td>
                    <span class="badge <?= $row['status_qc'] == 'Approve' ? 'bg-success' : 'bg-warning' ?>">
                        <?= $row['status_qc'] ?>
                    </span>
                </td>
                
                <!-- Badge Status Production -->
                <td>
                    <span class="badge <?= $row['status_production'] == 'Approve' ? 'bg-success' : 'bg-warning' ?>">
                        <?= $row['status_production'] ?>
                    </span>
                </td>

                <!-- Tombol Aksi -->
                <td>
                    <form action="<?= base_url('approval/processWeekly') ?>" method="post">
                        <input type="hidden" name="weekly_id" value="<?= $row['id'] ?>">
                        
                        <!-- Logika Form: Hanya munculkan tombol sesuai departemen user yang login -->
                        <?php if($user_role == 'qc' && $row['status_qc'] == 'Pending'): ?>
                            <input type="hidden" name="departemen" value="qc">
                            <button type="submit" name="action" value="Approve" class="btn btn-success btn-sm">Approve (QC)</button>
                        <?php elseif($user_role == 'production' && $row['status_production'] == 'Pending'): ?>
                            <input type="hidden" name="departemen" value="production">
                            <button type="submit" name="action" value="Approve" class="btn btn-success btn-sm">Approve (Prod)</button>
                        <?php else: ?>
                            <i>No Action Required</i>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->include('layout/footer') ?>