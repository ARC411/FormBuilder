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

    <div class="container-fluid">
        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Startup</h3>
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
                            <th>Emp ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 0; $i < count($alldata); $i++) { ?>
                            <tr>
                                <td><?= $i+1; ?></td>
                                <td><?= $alldata[$i]['created_at']; ?></td>
                                <td><?= $alldata[$i]['device_name']; ?></td>
                                <td><?= $alldata[$i]['name']; ?></td>
                                <td>
                                    <a class="lotno-link" data-bs-toggle="modal" data-bs-target="#exampleModal" data-lotno="<?= $alldata[$i]['lotno']; ?>" data-number="<?= $alldata[$i]['number']; ?>" data-process="<?= $alldata[$i]['name']; ?>">
                                        <?= $alldata[$i]['lotno']; ?>
                                    </a>
                                </td>
                                <td><?= $alldata[$i]['empid']; ?></td>
                                <td>
                                    <!-- Tag <a> edit ganda sudah dihapus agar tidak duplikat dengan icon -->
                                    <button type="button" class="btn btn-primary edit-button" data-number="<?= $alldata[$i]['number']; ?>">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger delete-button" data-number="<?= $alldata[$i]['number']; ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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
                    <!-- Lot No will be displayed here -->
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
        // Cek apakah user adalah admin berdasarkan session
        var isAdmin = <?= session()->get('isadmin') ? 'true' : 'false'; ?>;

        new DataTable('#table-approval');

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var lotno = button.data('lotno');
            var process = button.data('process');
            var modal = $(this);
            var loading = $('#loading');
            $('#modalTitle').text(process + ' - ' + lotno);

            loading.show();

            $.ajax({
                url: "<?= base_url('startup/history'); ?>/" + number,
                dataType: 'html',
                success: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html(data);
                },
                error: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html('<p>Error loading data</p>');
                    console.log(data);
                }
            });
        });

        $('#table-approval').on('click', '.edit-button', function(e) {
            if (!isAdmin) {
                e.preventDefault();
                alert('Button ini hanya bisa di lakukan oleh admin, silahkan hubungi admin');
                return false;
            }
            var number = $(this).data('number');
            window.open("<?= base_url('startup/edit'); ?>/" + number, '_blank');
        });

        $('#table-approval').on('click', '.delete-button', function(e) {
            if (!isAdmin) {
                e.preventDefault();
                alert('Button ini hanya bisa di lakukan oleh admin, silahkan hubungi admin');
                return false;
            }
            var number = $(this).data('number');
            if (confirm('Apakah kamu yakin? you want to delete this device?')) {
                $.ajax({
                    url: "<?= base_url('startup'); ?>/" + number,
                    type: 'DELETE',
                    success: function(result) {
                        alert('Berhasil menghapus')
                        location.reload();
                    },
                    error: function(err) {
                        console.log(err);
                        alert('Error deleting device.');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>