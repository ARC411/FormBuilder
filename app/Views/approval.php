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
        <!-- BANNER NOTIFIKASI PERMINTAAN REJECT (KHUSUS FOREMAN/LEADER/SUPERVISOR) -->
    <?php if (isset($isOperator) && !$isOperator && !empty($list_request_reject)): ?>
        <div class="alert alert-warning shadow-sm mb-4 mt-3" role="alert" style="border-left: 5px solid #ffc107; background-color: #fff8e5;">
            <h5 class="alert-heading text-dark mb-3"><i class="fas fa-exclamation-circle text-danger"></i> <b>Perhatian: Ada Permintaan Reject!</b></h5>
            <p class="mb-2 text-dark">Terdapat <b><?= count($list_request_reject) ?></b> form checksheet yang diajukan untuk di-reject oleh Operator:</p>
            <hr class="border-warning">
            <ul class="mb-0 text-dark" style="list-style-type: none; padding-left: 0;">
                <?php foreach ($list_request_reject as $req): ?>
                    <li class="mb-3 p-3 border rounded shadow-sm" style="background-color: #ffffff;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>Form #<?= esc($req['number']) ?> (<?= esc($req['process_name'] ?? $req['process']) ?>)</strong>
                            <span class="badge bg-secondary">Lot: <?= esc($req['lotno']) ?></span>
                        </div>
                        <small class="text-muted d-block mb-2">Diajukan oleh: <?= esc($req['empid']) ?> - <?= esc($req['name']) ?></small>
                        <span class="text-danger d-block p-2" style="background-color: #fff5f5; border-left: 3px solid #dc3545; font-size: 14px;">
                            <b>Alasan Minta Reject:</b> <i>"<?= nl2br(esc($req['reject_reason'])) ?>"</i>
                        </span>
                        
                        <!-- TOMBOL ACTION LANGSUNG DI DALAM BANNER -->
                        <div class="mt-3">
                            <form action="<?= base_url('approve') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="number" value="<?= esc($req['number']) ?>">
                                <input type="hidden" name="action" value="Reject">
                                <input type="hidden" name="reject_reason" value="Disetujui dari permintaan: <?= esc($req['reject_reason']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger font-weight-bold shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui permintaan Reject form ini agar operator bisa mengeditnya?');">
                                    <i class="fas fa-check-circle me-1"></i> Setujui Reject Ini
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <!-- END BANNER NOTIFIKASI -->

        <div class="card shadow mb-5">
            <div class="card-header py-3">
                <h3 class="m-0 font-weight-bold">Approval </h3>
            </div>
                        <div class="card-body">
                <?php if(session()->getFlashdata('message')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('message') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-1"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="container-fluid collapse show" id="menu">
                    <!-- Date input automatic -->
                    <div class="mb-3 row">
                        <label for="date" class="col-sm-1 col-form-label">Date Range</label>
                        <div class="col-sm-3">
                        <div id="daterange" class="form-control" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                        </div>
                    </div>
                </div>
                <p >Klik Process untuk melihat data</p>
                <div id="table" class="table-responsive">
            
                </div>
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
    <!-- Modal Reject Checksheet -->
    <div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow border-0">
                <form action="<?= base_url('approve') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="number" id="reject-number" value="">
                    <input type="hidden" name="name" value="<?= esc(session()->get('name')) ?>">
                    <input type="hidden" name="level" value="<?= esc(session()->get('level')) ?>">
                    <input type="hidden" name="action" value="Reject">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold" id="modalRejectLabel">
                            <i class="fas fa-times-circle me-2"></i>Reject Checksheet Harian
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning py-2 small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Anda akan menolak checksheet <strong id="reject-info-text">#0</strong>. Operator akan melihat notifikasi badge merah pada navbar <strong>Operations</strong> dan dapat langsung memperbaiki form ini.
                        </div>
                        <div class="mb-3">
                            <label for="reject-reason" class="form-label font-weight-bold">
                                Alasan Penolakan / Catatan Perbaikan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="reject-reason" name="reject_reason" rows="4" required placeholder="Contoh: Parameter transfer air pressure di bawah standar toleransi. Harap cek dan ukur ulang."></textarea>
                            <div class="form-text text-muted">Catatan ini akan langsung ditampilkan kepada operator di form edit.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger font-weight-bold">
                            <i class="fas fa-times me-1"></i> Konfirmasi Reject
                        </button>
                    </div>
                </form>
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
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end)
        {
            $('#daterange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            updateTable()
        }

        $('#daterange').daterangepicker(
            {
            startDate: start,
            endDate: end,
            ranges:
            {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
            }, cb);

        cb(start, end);

        $('#modalReject').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var process = button.data('process');
            var machno = button.data('machno');
            var modal = $(this);
            modal.find('#reject-number').val(number);
            modal.find('#reject-info-text').text('#' + number + ' (' + process + (machno && machno !== '-' ? ' - Mesin ' + machno : '') + ')');
            modal.find('#reject-reason').val('');
        });

        $(document).on('click', '.btn-open-reject', function() {
            var number = $(this).data('number');
            var process = $(this).data('process');
            var machno = $(this).data('machno');
            $('#reject-number').val(number);
            $('#reject-info-text').text('#' + number + ' (' + process + (machno && machno !== '-' ? ' - Mesin ' + machno : '') + ')');
            $('#reject-reason').val('');
        });

        $('#modalReject form').on('submit', function() {
            var num = $('#reject-number').val();
            if (!num || num === '' || num === '0') {
                alert('Nomor checksheet tidak ditemukan. Silakan klik ulang tombol Reject pada baris tabel.');
                return false;
            }
            return true;
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
                    modal.find('.modal-body').html('<p>Error loading data</p>');
                    console.log(data);
                },
            });
        });
    });

    function changeDevice()
    {
        var device = document.getElementById("device").value
        
        
        $.ajax(
            {
                url: "/process-list?device="+device+"&type=startup",
                dataType:'JSON',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                success: function(data)
                {
                    var processValue = document.getElementById("process")
                    var processValueLen = processValue.length
                    for(let i=0;i<processValueLen;i++){
                        processValue.remove(0);
                    }
                    var option = document.createElement("option");
                    option.text = "All"
                    option.value = ""
                    processValue.add(option,processValue[0])
                    for (let i=0;i<data.length;i++) {
                        var option = document.createElement("option");
                        option.text = data[i].name
                        option.value = data[i].process_code
                        if(processValue.length==0){
                            processValue.add(option,processValue[0]);
                        }
                        else{
                            processValue.add(option, processValue[processValue.length]);
                        }
                    }
                    updateTable()

                },
                error: function(data)
                {
                    console.log(data)
                },
            }
        );
    }
    function convertDate()
    {
        const date = $('#daterange span').html().split(" - ");
        var map = {
            "January":1,
            "February":2,
            "March":3,
            "April":4,
            "May":5,
            "June":6,
            "July":7,
            "August":8,
            "September":9,
            "October":10,
            "November":11,
            "December":12
        }
        const date0=date[0]
        const date1=date[1]

        const splitDate0=date0.split(" ")
        const splitDate1=date1.split(" ")

        const dateStart=splitDate0[2]+"-"+map[splitDate0[1]]+"-"+splitDate0[0]
        const dateEnd=splitDate1[2]+"-"+map[splitDate1[1]]+"-"+splitDate1[0]
        return {dateStart,dateEnd}
    }

    function updateTable()
    {
        let resDate,dateStart,dateEnd,process,model,lotNo,machno,device,typeProcess;

        resDate=convertDate()
        dateStart=resDate.dateStart+' 00:00:00'
        dateEnd=resDate.dateEnd+' 23:59:59'

        console.log(dateStart,dateEnd,process,model,lotNo,machno);
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo base_url();?>approve-form?dateStart="+dateStart+"&dateEnd="+dateEnd, true);
        xhr.onload = (e) => {
            if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var table = document.getElementById('table')
                table.innerHTML=xhr.responseText;
                new DataTable('#table-approval');
            } else {
                var table = document.getElementById('table')
                table.innerHTML="";
                console.error(xhr.statusText);
            }
            }
        };
        xhr.onerror = (e) => {
            console.error(xhr.statusText);
        };
        xhr.send(null);
    }
</script>

<?= $this->endSection(); ?>