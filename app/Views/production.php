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
                <h3 class="m-0 font-weight-bold">Production </h3>
            </div>
            <div class="card-body">
                <p>Klik Lotno untuk melihat data</p>
                <table class="table table-striped-columns table-responsive mt-3" id="table-approval" style="width:100%">
                    <thead align="center">
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Device</th>
                            <th>Process</th>
                            <th>Lot No</th>
                            <th>EmpID Start</th>
                            <th>EmpID Complete</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
        // Cek apakah user adalah admin berdasarkan session
        var isAdmin = <?= session()->get('isadmin') ? 'true' : 'false'; ?>;

        // Escape HTML agar data aman ditampilkan
        function esc(v) {
            return $('<div>').text(v == null ? '' : v).html();
        }

        new DataTable('#table-approval', {
            processing: true,
            serverSide: true,
            ajax: "<?= base_url('production/datatable'); ?>",
            order: [[1, 'desc']],
            columns: [
                {
                    data: null,
                    orderable: false,
                    render: function(d, t, r, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'created_at' },
                { data: 'device', render: esc },
                { data: 'name', render: esc },
                {
                    data: 'lotno',
                    render: function(d, t, r) {
                        return '<a class="lotno-link" data-bs-toggle="modal" data-bs-target="#exampleModal"'
                            + ' data-lotno="' + esc(d) + '"'
                            + ' data-number="' + esc(r.number) + '"'
                            + ' data-process="' + esc(r.name) + '">' + esc(d) + '</a>';
                    }
                },
                { data: 'empid', render: esc },
                { data: 'empid2', render: esc },
                {
                    data: 'number',
                    orderable: false,
                    render: function(d) {
                        return '<button type="button" class="btn btn-primary edit-button" data-number="' + esc(d) + '">'
                            + '<i class="fa-regular fa-pen-to-square"></i></button> '
                            + '<button type="button" class="btn btn-danger delete-button" data-number="' + esc(d) + '">'
                            + '<i class="fa-solid fa-trash"></i></button>';
                    }
                }
            ]
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var lotno = button.data('lotno');
            var process = button.data('process');
            var modal = $(this);

            $('#modalTitle').text(process + ' - ' + lotno);
            modal.find('.modal-body').html('<div id="loading">Loading...</div>');

            $.ajax({
                url: "<?= base_url('productions/history'); ?>/" + number,
                dataType: 'html',
                success: function(data) {
                    modal.find('.modal-body').html(data);
                },
                error: function(data) {
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
            window.open("<?= base_url('production/edit'); ?>/" + number, '_blank');
        });

        $('#table-approval').on('click', '.delete-button', function(e) {
            if (!isAdmin) {
                e.preventDefault();
                alert('Button ini hanya bisa di lakukan oleh admin, silahkan hubungi admin');
                return false;
            }
            var number = $(this).data('number');
            if (confirm('Apakah kamu yakin?')) {
                $.ajax({
                    url: "<?= base_url('production'); ?>/" + number,
                    type: 'DELETE',
                    success: function(result) {
                        alert('Berhasil menghapus');
                        $('#table-approval').DataTable().ajax.reload(null, false);
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