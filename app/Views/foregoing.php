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
    ?>

    <div class="container-fluid">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('message')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Foregoing</h3>
            </div>
            <div class="card-body">
                <p>Klik Lotno untuk melihat data</p>
                <table class="table table-striped-columns table-responsive mt-3" id="table-approval">
                    <thead align="center">
                        <tr>
                            <th>No.</th>
                            <th>Date</th>         
                            <th>Device</th>
                            <th>Process</th>
                            <th>Lot No</th>
                            <th>EmpID</th>
                            <th>Status Edit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 0; $i < count($alldata); $i++) { 
                            $editStatus = (int)($alldata[$i]['edit_status'] ?? 0);
                            $num = $alldata[$i]['number'];
                        ?>
                            <tr>
                                <td><?= $i+1; ?></td>
                                <td><?= $alldata[$i]['created_at']; ?></td>
                                <td><?= $alldata[$i]['device_name']; ?></td>
                                <td><?= $alldata[$i]['process_name']; ?></td>
                                <td>
                                    <a class="lotno-link" data-bs-toggle="modal" data-bs-target="#exampleModal" data-lotno="<?= $alldata[$i]['lotno']; ?>" data-number="<?= $num; ?>" data-process="<?= $alldata[$i]['process_name']?>">
                                        <?= !empty($alldata[$i]['lotno']) ? $alldata[$i]['lotno'] : 'Lihat Detail'; ?>
                                    </a>
                                </td>
                                <td><?= $alldata[$i]['empid']; ?></td>
                                <td align="center">
                                    <?php if ($editStatus === 0): ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-lock"></i> Terkunci</span>
                                    <?php elseif ($editStatus === 1): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i> Menunggu Izin Foreman</span>
                                    <?php elseif ($editStatus === 2): ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-lock-open"></i> Diizinkan Edit<?= !empty($alldata[$i]['unlocked_by']) ? ' (' . esc($alldata[$i]['unlocked_by']) . ')' : ''; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isOperator): ?>
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Lot No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <div id="loading">Loading...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        new DataTable('#table-approval');

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
                    modal.find('.modal-body').html('<p>Error loading data</p>');
                }
            });
        });

        // Operator klik Minta Izin Edit
        $('#table-approval').on('click', '.request-edit-btn', function() {
            var number = $(this).data('number');
            if (confirm('Ajukan permintaan izin edit ke Foreman untuk data ini?')) {
                $.post("<?= base_url('foregoing/request-edit'); ?>/" + number, function(res) {
                    alert(res.message || 'Permintaan izin edit berhasil dikirim.');
                    location.reload();
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal mengirim permintaan izin edit.';
                    alert(msg);
                });
            }
        });

        // Foreman / Atasan klik Izinkan Edit
        $('#table-approval').on('click', '.unlock-edit-btn', function() {
            var number = $(this).data('number');
            if (confirm('Izinkan operator untuk mengedit data ini?')) {
                $.post("<?= base_url('foregoing/unlock-edit'); ?>/" + number, function(res) {
                    alert(res.message || 'Izin edit berhasil diberikan.');
                    location.reload();
                }).fail(function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal membuka kunci edit.';
                    alert(msg);
                });
            }
        });

        // Klik tombol Edit
        $('#table-approval').on('click', '.edit-button', function() {
            var number = $(this).data('number');
            window.open("<?= base_url('foregoing/edit'); ?>/" + number, '_blank');
        });

        // Klik tombol Delete (Khusus Admin)
        $('#table-approval').on('click', '.delete-button', function() {
            var number = $(this).data('number');
            if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: "<?= base_url('foregoing'); ?>/" + number,
                    type: 'DELETE',
                    success: function(result) {
                        alert('Berhasil menghapus');
                        location.reload();
                    },
                    error: function(err) {
                        alert('Error deleting data.');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>