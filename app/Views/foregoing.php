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
        $userName = trim((string)$session->get('name'));

        $myRejectedForms = [];
        if (!empty($alldata)) {
            foreach ($alldata as $row) {
                if (($row['status'] ?? '') === 'Reject') {
                    $myRejectedForms[] = $row;
                }
            }
        }
    ?>

    <div class="container-fluid px-4 mt-3 mb-5">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-1"></i> <?= esc(session()->getFlashdata('message')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($myRejectedForms)): ?>
            <div class="alert alert-danger shadow-sm border-danger d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading m-0 font-weight-bold">
                        Perhatian: Terdapat <?= count($myRejectedForms) ?> Checksheet Foregoing yang Ditolak (Reject)!
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
                    <i class="fas fa-clipboard-check me-2"></i>Data Foregoing
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
                                <th style="width: 110px;">EmpID</th>
                                <th style="width: 190px;">Status</th>
                                <th style="width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i < count($alldata); $i++) { 
                                $editStatus = (int)($alldata[$i]['edit_status'] ?? 0);
                                $num = $alldata[$i]['number'];
                                $rowStatus = $alldata[$i]['status'] ?? 'Pending';
                                $isRowReject = ($rowStatus === 'Reject');
                                $lotVal = isset($alldata[$i]['lotno']) ? trim((string)$alldata[$i]['lotno']) : '';
                                $procLabel = $alldata[$i]['process_name'] ?? '';
                                $machVal = !empty($alldata[$i]['machno']) ? $alldata[$i]['machno'] : '-';
                                $dateVal = !empty($alldata[$i]['created_at']) ? date('d/m/Y H:i', strtotime($alldata[$i]['created_at'])) : '-';
                                $opName = !empty($alldata[$i]['name']) ? $alldata[$i]['name'] : $userName;
                            ?>
                                <tr class="<?= $isRowReject ? 'table-danger border-start border-danger border-4' : '' ?>">
                                    <td class="text-center font-weight-bold"><?= $i+1; ?></td>
                                    <td class="text-center"><?= $dateVal; ?></td>
                                    <td class="text-center"><span class="badge bg-secondary"><?= esc($alldata[$i]['device_name'] ?? ''); ?></span></td>
                                    <td>
                                        <strong><?= esc($procLabel); ?></strong>
                                        <?php if($machVal !== '-'): ?>
                                            <div class="small text-muted">Mesin: <strong><?= esc($machVal); ?></strong></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="lotno-link font-weight-bold text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-lotno="<?= esc($lotVal); ?>" data-number="<?= $num; ?>" data-process="<?= esc($procLabel); ?>">
                                            <?= $lotVal !== '' ? esc($lotVal) : '-'; ?>
                                        </a>
                                    </td>
                                    <td class="text-center font-monospace small"><?= esc($alldata[$i]['empid'] ?? ''); ?></td>
                                    <td class="text-center">
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
                                        <?php elseif ($editStatus === 1): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-envelope-open-text me-1"></i>Menunggu Reject Foreman</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fa-solid fa-lock me-1"></i><?= esc($rowStatus ?: 'Terkunci'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isRowReject): ?>
                                            <button type="button" class="btn btn-sm btn-danger edit-button font-weight-bold shadow-sm" data-number="<?= $num; ?>" title="Perbaiki Form Ini">
                                                <i class="fas fa-tools me-1"></i> Perbaiki Form
                                            </button>
                                        <?php elseif ($isOperator): ?>
                                            <button type="button" class="btn btn-sm btn-warning request-reject-btn"
                                                data-number="<?= $num; ?>"
                                                data-date="<?= esc($dateVal); ?>"
                                                data-name="<?= esc($opName); ?>"
                                                data-empid="<?= esc($alldata[$i]['empid'] ?? ''); ?>"
                                                data-machno="<?= esc($machVal); ?>"
                                                data-process="<?= esc($procLabel); ?>"
                                                data-lotno="<?= esc($lotVal ?: '-'); ?>">
                                                <i class="fas fa-envelope me-1"></i> <?= $editStatus === 1 ? 'Kirim Ulang Reject' : 'Minta Reject'; ?>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-primary edit-button" data-number="<?= $num; ?>" title="Edit Form">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            <?php if ($isAdmin): ?>
                                                <button type="button" class="btn btn-sm btn-danger delete-button" data-number="<?= $num; ?>" title="Hapus">
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
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Lot No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <div id="loading" class="text-center py-4">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pengajuan Reject via Gmail ke Foreman -->
    <div class="modal fade" id="requestRejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="fas fa-envelope me-2"></i>Ajukan Permintaan Reject ke Foreman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="req-number">
                    <div class="mb-3 p-2 bg-light border rounded small">
                        <div><strong>Tanggal Input:</strong> <span id="req-date"></span></div>
                        <div><strong>Nama / Emp ID:</strong> <span id="req-name"></span> (<span id="req-empid"></span>)</div>
                        <div><strong>Process:</strong> <span id="req-process"></span></div>
                        <div><strong>Machine No:</strong> <span id="req-machno"></span> | <strong>Lot No:</strong> <span id="req-lotno"></span></div>
                    </div>
                    <div class="mb-3">
                        <label for="req-reason" class="form-label font-weight-bold">Catatan / Alasan Minta Reject <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="req-reason" rows="3" placeholder="Tuliskan alasan mengapa form ini perlu di-reject..." required></textarea>
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>Email akan dikirimkan secara otomatis ke seluruh alamat email <strong>Foreman</strong>. Setelah Foreman melakukan Reject di halaman Approval, tombol <strong>Perbaiki Form</strong> akan terbuka.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning font-weight-bold" id="btn-submit-reject-req">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Email ke Foreman
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/dataTable.js"></script>
<script src="assets/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        new DataTable('#table-approval', {
            order: []
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var lotno = button.data('lotno');
            var process = button.data('process');
            var modal = $(this);
            var loading = $('#loading');
            $('#modalTitle').text(process + ' - ' + (lotno || '-'));

            loading.show();

            $.ajax({
                url: "<?= base_url('foregoing/history'); ?>/" + number,
                dataType: 'html',
                success: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html(data);
                },
                error: function() {
                    loading.hide();
                    modal.find('.modal-body').html('<p class="text-danger">Error loading data</p>');
                }
            });
        });

        // Klik Perbaiki Form / Edit
        $(document).on('click', '.edit-button', function() {
            var number = $(this).data('number');
            window.location.href = "<?= base_url('foregoing/edit'); ?>/" + number;
        });

        // Buka Modal Minta Reject
        $(document).on('click', '.request-reject-btn', function() {
            var btn = $(this);
            $('#req-number').val(btn.data('number'));
            $('#req-date').text(btn.data('date'));
            $('#req-name').text(btn.data('name'));
            $('#req-empid').text(btn.data('empid'));
            $('#req-process').text(btn.data('process'));
            $('#req-machno').text(btn.data('machno'));
            $('#req-lotno').text(btn.data('lotno'));
            $('#req-reason').val('');
            $('#requestRejectModal').modal('show');
        });

        // Kirim Email Permintaan Reject ke Foreman di Latar Belakang
        $('#btn-submit-reject-req').on('click', function() {
            var number = $('#req-number').val();
            var reason = $.trim($('#req-reason').val());
            if (!reason) {
                alert('Silakan isi alasan minta reject terlebih dahulu!');
                $('#req-reason').focus();
                return;
            }

            var btn = $(this);
            var oldHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengirim Email...');

            $.ajax({
                url: "<?= base_url('foregoing/request-reject/') ?>" + number,
                method: 'POST',
                data: { reason: reason },
                dataType: 'JSON',
                success: function(res) {
                    btn.prop('disabled', false).html(oldHtml);
                    $('#requestRejectModal').modal('hide');
                    alert(res.message || 'Permintaan Reject berhasil dikirim ke Foreman.');
                    location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(oldHtml);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal mengirim email permintaan reject.';
                    alert(msg);
                }
            });
        });

        // Klik tombol Delete (Khusus Admin)
        $(document).on('click', '.delete-button', function() {
            var number = $(this).data('number');
            if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: "<?= base_url('foregoing'); ?>/" + number,
                    type: 'DELETE',
                    success: function() {
                        alert('Berhasil menghapus');
                        location.reload();
                    },
                    error: function() {
                        alert('Error deleting data.');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>