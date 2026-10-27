<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
    <?= $this->include('layout/navbar'); ?>

    <link rel="stylesheet" href="assets/css/daterangepicker.css">
    <link rel="stylesheet" href="assets/css/global.css">

    <style>
        .lotno-link:hover {
            text-decoration: underline;
            color: #007bff;
            cursor: pointer;
        }
    </style>

    <div class="container-fluid">
        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Add Device</h3>
            </div>
            <div class="card-body">
                <button class="btn btn-primary lotno-link" data-bs-toggle="modal" data-bs-target="#modalInput">Add Device</button>
                <table class="table table-striped table-responsive mt-3" id="table-device">
                    <thead align="center">
                        <tr>
                            <th>No.</th>
                            <th>Device Code</th>
                            <th>Device Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($alldata as $index => $device): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= $device['code']; ?></td>
                                <td><?= $device['name']; ?></td>
                                <td>
                                    <button type="button" data-bs-target="#modalEdit" data-bs-toggle="modal" class="btn btn-primary edit-button" data-id="<?= $device['id']; ?>">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger delete-button" data-id="<?= $device['id']; ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Input -->
    <div class="modal fade" id="modalInput" tabindex="-1" aria-labelledby="modalInputLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInputLabel">Add Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-input">
                <form name="checksheet" action="/device" method="POST">
                <?= csrf_field(); ?>
                    <input type="hidden" name="type-input" value="manual">

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Code of Device -->
                            <div class="mb-3 row">
                                <label for="device-code" class="col-sm-2 col-form-label">Kode Device</label>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" id="device-code" name="device-code" placeholder="Contoh : sl56">
                                </div>
                            </div>

                            <!-- Name of Device -->
                            <div class="mb-3 row">
                                <label for="device-name" class="col-sm-2 col-form-label">Nama Device</label>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" id="device-name" name="device-name" placeholder="Contoh : Single Laser 5.6">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-edit">
                    <div id="loading">Loading...</div>
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
        if (session()->getFlashdata('message')){
            echo '<p id="msgsuccess" class="invisible">' . session()->getFlashdata('message').'</p>';
        } else {
            echo '<p id="msgsuccess" class="invisible">Kosong</p>';
        }
    ?>
</main>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/dataTable.js"></script>
<script src="assets/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = new DataTable('#table-device');

        if ($('#msgsuccess').html() != "Kosong") {
            alert($('#msgsuccess').html());
        }

        $('#modalEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var id = button.data('id'); 
            var modal = $(this);
            
            // Tampilkan pesan loading sebelum AJAX selesai
            modal.find('#modal-body-edit').html('<div id="loading">Loading data...</div>');
            
            $.ajax({
                // Menggunakan base_url agar URL-nya dinamis
                url: "<?= base_url('device/edit/') ?>" + id, 
                dataType: 'html',
                success: function(data) {
                    modal.find('#modal-body-edit').html(data);
                },
                error: function(data) {
                    modal.find('#modal-body-edit').html('<p>Error loading data</p>');
                },
            });
        });

        $('#table-device').on('click', '.delete-button', function() {
            var id = $(this).data('id');
            if (confirm('Apakah kamu yakin? you want to delete this device?')) {
                $.ajax({
                    url: '/device/' + id,
                    type: 'DELETE',
                    success: function(result) {
                        location.reload();
                    },
                    error: function(err) {
                        alert('Error deleting device.');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection(); ?>