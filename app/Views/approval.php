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
                <h3 class="m-0 font-weight-bold">Approval </h3>
            </div>
            <div class="card-body">
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
        

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var process = button.data('process');
            var modal = $(this);
            var loading = $('#loading');
            $('#modalTitle').text(process)

            loading.show();

            $.ajax({
                url: "<?= base_url('startup/history/') ?>" + number,
                dataType: 'html',
                success: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html(data);
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
