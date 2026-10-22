<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>
    <link rel="stylesheet" href="assets/css/daterangepicker.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
        .lotno-link:hover {
            text-decoration : underline;
            color: #007bff;
            cursor: pointer;
        }
    </style>

    <?php
        $session = \Config\Services::session();
        $posStr = (string)$session->get('positionid');
        $isAdmin = (bool)$session->get('isadmin');
        $isOperator = ($isOperator ?? (stripos($posStr, 'operator') !== false && !$isAdmin));

        $userEmp = trim((string)$session->get('empid'));
        $userName = trim((string)$session->get('name'));

        $myRejectedForms = [];
        if (!empty($alldata)) {
            foreach ($alldata as $row) {
                if (($row['status'] ?? '') === 'Reject') {
                    if ($isOperator) {
                        $docEmp = trim((string)($row['empid'] ?? ''));
                        $docName = trim((string)($row['name'] ?? ''));
                        if (($userEmp !== '' && $userEmp === $docEmp) || ($userName !== '' && $userName === $docName)) {
                            $myRejectedForms[] = $row;
                        }
                    } else {
                        $myRejectedForms[] = $row;
                    }
                }
            }
        }
    ?>

    <div class="container-fluid px-4 mt-3 mb-5">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> <?= esc(session()->getFlashdata('message')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($myRejectedForms)): ?>
            <div class="alert alert-danger shadow-sm border-danger d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading m-0 font-weight-bold">
                        Perhatian: Terdapat <?= count($myRejectedForms) ?> Checksheet Startup Anda yang Ditolak (Reject)!
                    </h5>
                    <div class="small mt-1">
                        Silakan periksa catatan penolakan dari Approver pada tabel di bawah, lalu klik tombol 
                        <span class="badge bg-danger"><i class="fas fa-tools"></i> Perbaiki Form</span> untuk melakukan perbaikan data.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-5">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-rocket me-2"></i>Data Startup
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted small">Klik Lot No untuk melihat detail form checksheet</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mt-2" id="table-approval">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th style="width: 140px;">Waktu Input</th>
                                <th>Device</th>
                                <th>Process</th>
                                <th>Lot No</th>
                                <th style="width: 110px;">Emp ID</th>
                                <th style="width: 180px;">Status</th>
                                <th style="width: 160px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i < count($alldata); $i++) { 
                                $editStatus = (int)($alldata[$i]['edit_status'] ?? 0);
                                $num = $alldata[$i]['number'];
                                $rowStatus = $alldata[$i]['status'] ?? '';
                                $isRowReject = ($rowStatus === 'Reject');
                            ?>
                                <tr class="<?= $isRowReject ? 'table-danger border-start border-danger border-4' : '' ?>">
                                    <td class="text-center font-weight-bold"><?= $i+1; ?></td>
                                    <td class="text-center"><?= !empty($alldata[$i]['created_at']) ? date('d/m/Y H:i', strtotime($alldata[$i]['created_at'])) : '-'; ?></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= esc($alldata[$i]['device_name'] ?? ''); ?></span></td>
                                    <td>
                                        <strong><?= esc($alldata[$i]['process_name'] ?? $alldata[$i]['name'] ?? ''); ?></strong>
                                        <?php if(!empty($alldata[$i]['machno']) && $alldata[$i]['machno'] !== '-'): ?>
                                            <div class="small text-muted">Mesin: <strong><?= esc($alldata[$i]['machno']); ?></strong></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="lotno-link font-weight-bold text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-lotno="<?= esc($alldata[$i]['lotno'] ?? ''); ?>" data-number="<?= $num; ?>" data-process="<?= esc($alldata[$i]['process_name'] ?? $alldata[$i]['name'] ?? ''); ?>" data-machno="<?= !empty($alldata[$i]['machno']) ? esc($alldata[$i]['machno']) : '-'; ?>">
                                            <?= !empty($alldata[$i]['lotno']) ? esc($alldata[$i]['lotno']) : 'Lihat Detail'; ?>
                                        </a>
                                    </td>
                                    <td class="text-center font-monospace small"><?= esc($alldata[$i]['empid'] ?? ''); ?></td>
                                    <td>
                                        <?php if ($isRowReject): ?>
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                            <?php if (!empty($alldata[$i]['reject_reason'])): ?>
                                                <div class="small text-danger text-start mt-1 p-1 bg-white border border-danger rounded shadow-sm" style="font-size: 0.75rem;">
                                                    <strong>Alasan:</strong> <?= esc($alldata[$i]['reject_reason']); ?>
                                                    <?php if (!empty($alldata[$i]['reject_by'])): ?>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Oleh: <?= esc($alldata[$i]['reject_by']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($editStatus === 0): ?>
                                                <span class="badge bg-secondary"><i class="fa-solid fa-lock"></i> Terkunci</span>
                                            <?php elseif ($editStatus === 1): ?>
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i> Menunggu Izin Foreman</span>
                                            <?php elseif ($editStatus === 2): ?>
                                                <span class="badge bg-success"><i class="fa-solid fa-lock-open"></i> Diizinkan Edit<?= !empty($alldata[$i]['unlocked_by']) ? ' (' . esc($alldata[$i]['unlocked_by']) . ')' : ''; ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isRowReject): ?>
                                            <!-- Akses Edit Otomatis Terbuka (Unlocked) untuk Form Reject -->
                                            <button type="button" class="btn btn-sm btn-danger edit-button font-weight-bold shadow-sm" data-number="<?= $num; ?>" title="Perbaiki Form Ini">
                                                <i class="fas fa-tools me-1"></i> Perbaiki Form
                                            </button>
                                        <?php elseif ($isOperator): ?>
                                            <?php if ($editStatus === 0): ?>
                                                <button type="button" class="btn btn-sm btn-warning request-edit-btn" data-number="<?= $num; ?>">
                                                    <i class="fa-solid fa-key"></i> Minta Izin Edit
                                                </button>
                                            <?php elseif ($editStatus === 1): ?>
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Foreman
                                                </button>
                                            <?php elseif ($editStatus === 2): ?>
                                                <button type="button" class="btn btn-sm btn-primary edit-button" data-number="<?= $num; ?>">
                                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($editStatus === 1): ?>
                                                <button type="button" class="btn btn-sm btn-success unlock-edit-btn me-1" data-number="<?= $num; ?>" title="Izinkan Operator Edit">
                                                    <i class="fa-solid fa-unlock"></i> Izinkan Edit
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-primary edit-button" data-number="<?= $num; ?>">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            <?php if ($isAdmin): ?>
                                                <button type="button" class="btn btn-sm btn-danger delete-button" data-number="<?= $num; ?>">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Lotno -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Detail Checksheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <div id="loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/dataTable.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.min.js"></script>
<script src="assets/js/dataTables.bootstrap4.js"></script>
<script>
    $(document).ready(function() {
        $('#table-approval').DataTable({
            order: []
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var process = button.data('process');
            var machno = button.data('machno');
            var modal = $(this);
            var loading = $('#loading');
            
            var title = process;
            if (machno && machno !== '-') {
                title += ' - Machine: ' + machno;
            }
            $('#modalTitle').text(title);

            loading.show();

            $.ajax({
                url: "<?= base_url('startup/history/') ?>" + number,
                dataType: 'html',
                success: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html(data);
                    
                    var fetchedMachno = modal.find('#detail-machno').val();
                    if (fetchedMachno && (!machno || machno === '-')) {
                        $('#modalTitle').text(process + ' - Machine: ' + fetchedMachno);
                    }
                },
                error: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html('<p class="text-danger">Error loading data.</p>');
                },
            });
        });

        $(document).on('click', '.edit-button', function() {
            var number = $(this).data('number');
            window.location.href = "<?= base_url('startup/edit/') ?>" + number;
        });

        $(document).on('click', '.request-edit-btn', function() {
            var number = $(this).data('number');
            if (confirm('Ajukan izin edit untuk form checksheet #' + number + ' ke Foreman?')) {
                $.ajax({
                    url: "<?= base_url('startup/requestEdit/') ?>" + number,
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(res) {
                        alert(res.message || 'Permintaan izin edit berhasil dikirim.');
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal mengirim permintaan izin edit.');
                    }
                });
            }
        });

        $(document).on('click', '.unlock-edit-btn', function() {
            var number = $(this).data('number');
            if (confirm('Izinkan operator mengedit form checksheet #' + number + '?')) {
                $.ajax({
                    url: "<?= base_url('startup/unlockEdit/') ?>" + number,
                    method: 'POST',
                    dataType: 'JSON',
                    success: function(res) {
                        alert(res.message || 'Izin edit diberikan kepada operator.');
                        location.reload();
                    },
                    error: function() {
                        alert('Gagal memberikan izin edit.');
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection(); ?>