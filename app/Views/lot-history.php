<?= $this->extend('layout/templ-home'); ?>
<?= $this->section('content'); ?>
<main>
  <?= $this->include('layout/navbar'); ?>
  <link rel="stylesheet" href="assets/css/daterangepicker.css">
  <link rel="stylesheet" href="assets/css/global.css">

  <section class="container-fluid">
    <div class="card shadow mb-5">

      <div class="card-header py-3">
        <h3 class="m-0 font-weight-bold">Lot Pending History</h5>
      </div>
      <!-- <div class="card-text">
      </div> -->
        
      <div class="card-body">
        <p >Klik status untuk melengkapi data</p>
        <div class="table-responsive">
          <table class="table table-responsive table-striped table-bordered mt-3" id="table-lot-history">
            <thead align="center">
                <tr>
                    <th class="sorting">No.</th>
                    <th>Start Date</th>     
                    <th>Device</th>         
                    <th>Lot No</th>
                    <th>Process</th>
                    <th>Status</th>
                </tr>
            </thead>
              
            <tbody>
                <?php for($i = 0; $i < count($alldata); $i=$i+1) { ?>
                    <tr>
                        <td rowspan="1" ><?= $i+1; ?></td>
                        <td rowspan="1" ><?= $alldata[$i]['created_at']; ?></td>
                        <td rowspan="1" ><?= $alldata[$i]['device_name']; ?></td>
                        <td rowspan="1" ><?= $alldata[$i]['lotno']; ?></td>
                        <td rowspan="1" >
                            <?=$alldata[$i]['name']; ?>
                        </td>
                        <td rowspan="1" >
                            <?php 
                            if($alldata[$i]['par045']=="Process Start"){
                                echo '<a href="' . base_url('production/edit/' . $alldata[$i]['number']) . '">' . $alldata[$i]['par045'] . '</a>';
                            }
                            else{
                                echo $alldata[$i]['par045'];
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
          </table> 
        </div>
      </div>
      <?php 
          if (session()->getFlashdata('message')){ 
            echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
          }else{ 
            echo '<p id=msgsuccess class="invisible">Kosong</p>'; ;}
        ?>
    </div>
  </section>
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/moment.min.js"></script>
  <script src="assets/js/dataTable.js"></script>
  <script src="assets/js/dataTables.bootstrap4.js"></script>
  <script type="text/javascript">
    // Call the dataTables jQuery plugin
    $(document).ready(function() {
      new DataTable('#table-lot-history', {
          responsive: true
      });
    });
    if($('#msgsuccess').html()!="Kosong"){
        alert($('#msgsuccess').html());
    }
  </script>
</main>

    
<?= $this->endSection(); ?>