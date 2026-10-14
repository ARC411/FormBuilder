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
                <h3 class="m-0 font-weight-bold">Add Process </h3>
            </div>
            <div class="card-body">
                <button class="btn btn-primary lotno-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Process</button>
                <table class="table table-striped-columns table-responsive mt-3" id="table-process">
                    <thead align="center">
                        <tr>
                            <th>No.</th>   
                            <th>Device Name</th>  
                            <th>Process Code</th>
                            <th>Name</th>
                            <th>Doc. No</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 0; $i < count($alldata); $i++) { ?>
                            <tr>
                                <td><?= $i+1; ?></td>
                                <td><?= $alldata[$i]['device_name']; ?></td>
                                <td><?= $alldata[$i]['process_code']; ?></td>
                                <td><?= $alldata[$i]['process_name']; ?></td>
                                <td><?= $alldata[$i]['docno']; ?></td>
                                <td>
                                    <button type="button" data-bs-target="#modalEdit" data-bs-toggle="modal" class="btn btn-primary edit-button" data-id="<?= $alldata[$i]['id']; ?>">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger delete-button" data-id="<?= $alldata[$i]['id']; ?>">
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

    <!-- Modal Input Process -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Lot No</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <form name="checksheet" action="/process" method="POST">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="type-input" value="manual">

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Device input -->
                                <div class="mb-3 row">
                                    <label for="device" class="col-sm-2 col-form-label">Device</label>
                                    <div class="col-sm-9">
                                        <select id="device" name="device" class="form-select" aria-label="Default select example">
                                        </select>
                                    </div>
                                </div>

                                <!-- Type input -->
                                <div class="mb-3 row">
                                    <label for="Type" class="col-sm-2 col-form-label">Tipe</label>
                                    <div class="col-sm-9">
                                        <select id="type" name="type" class="form-select" aria-label="Default select example">
                                            <option selected>Pilih tipe</option>
                                            <option value="production">Production</option>
                                            <option value="startup">Startup</option>
                                            <option value="foregoing">Foregoing</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Process of Device -->
                                <div class="mb-3 row">
                                    <label for="process-name" class="col-sm-2 col-form-label">Nama Process</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="process-name" name="process-name" placeholder="Contoh : Wire Bonding">
                                    </div>
                                </div>

                                <!-- Code of Process -->
                                <div class="mb-3 row">
                                    <label for="process-code" class="col-sm-2 col-form-label">Kode Process</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="process-code" name="process-code" placeholder="Contoh : sl90-p-Wire Bonding">
                                    </div>
                                </div>

                                <!-- Doc No -->
                                <div class="mb-3 row">
                                    <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="docno" name="docno" placeholder="Contoh : SM-BI-001">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
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
            echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
        }else{ 
            echo '<p id=msgsuccess class="invisible">Kosong</p>';
        }
    ?>
</main>
<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/dataTable.js"></script>
<script src="assets/js/dataTables.bootstrap4.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = new DataTable('#table-process');

        if($('#msgsuccess').html() != "Kosong"){
            alert($('#msgsuccess').html());
        }

        // Use event delegation for delete button
        $('#table-process').on('click', '.delete-button', function() {
            var id = $(this).data('id');
            if (confirm('Apakah kamu yakin? you want to delete this device?')) {
                $.ajax({
                    url: '/process/' + id,
                    type: 'DELETE',
                    success: function(result) {
                        alert('Berhasil Menghapus');
                        location.reload();
                    },
                    error: function(err) {
                        console.log(err);
                        alert('Error deleting device.');
                    }
                });
            }
        });

        $('#modalEdit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var id = button.data('id'); 
            var modal = $(this);
            $.ajax({
                url: "/process/edit/" + id, 
                dataType: 'html',
                success: function(data) {
                    modal.find('#modal-body-edit').html(data);
                },
                error: function(data) {
                    modal.find('#modal-body-edit').html('<p>Error loading data</p>');
                },
            });
        });
    });
</script>
<!-- Script add Proses -->
<script>
$(document).ready(function()
{
    $.ajax(
        {
            // Ubah baris ini menggunakan base_url bawaan PHP CodeIgniter
            url: "<?= base_url('device/list'); ?>", 
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data)
            {
                console.log(data);
                var deviceValue = document.getElementById("device")
                var deviceValueLen = deviceValue.length
                for(let i=0;i<deviceValueLen;i++){
                    deviceValue.remove(0);
                }
                for (let i=0;i<data.length;i++) {
                    var option = document.createElement("option");
                    option.text = data[i].name
                    option.value = data[i].code
                    if(deviceValue.length==0){
                        deviceValue.add(option,deviceValue[0]);
                    }
                    else{
                        deviceValue.add(option, deviceValue[deviceValue.length]);
                    }
                }
            },
            error: function(data)
            {
                console.log(data)
            },
        }
    );
});
</script>
<?= $this->endSection(); ?>