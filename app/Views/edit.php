<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?php
  $session = \Config\Services::session();
?>

<?= $this->include('layout/navbar'); ?>
<form name="checksheet" action="<?php echo base_url();?>production" method="POST">
<input type="hidden" id="process-form" name="process-form" value=<?php echo '"'.($alldata[0]["process"] ?? '').'"' ?>>
<input type="hidden" id="number-form" name="number-form" value=<?php echo '"'.($alldata[0]["number"] ?? '').'"' ?>>
  <div class="container-fluid">

    <div class="card shadow mb-5">
      <div class="card-header py-3">
        <h3 class="m-0 font-weight-bold">Input Checksheet Complete</h5>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <!-- Date input automatic -->
            <div class="mb-3 row">
              <label for="date" class="col-sm-2 col-form-label">Date</label>
              <div class="col-sm-6">
                <input type="text" class="form-control-plaintext" id="date" name="date" value="<?php echo date('Y-m-d');?>" readonly>
              </div>
            </div>

            <!-- Device input -->
            <div class="mb-3 row">
              <label for="device-txt" class="col-sm-2 col-form-label">Device</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" id="device-txt" name="device-txt" value="<?php echo $alldata[0]["device"] ?? '' ?>" >
                <input type="text" class="form-control" id="device-txt-name" name="device-txt-name" value="<?php echo $alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '' ?>" >
              </div>
            </div>

            <!-- Process of Device -->
            <div class="mb-3 row">
              <label for="process-txt" class="col-sm-2 col-form-label">Process</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" id="process-txt" name="process-txt" value="<?php echo $alldata[0]["process"] ?? '' ?>" >
                <input type="text" class="form-control" id="process-txt-name" name="process-txt-name" value="<?php echo $alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '' ?>" >
              </div>
            </div>

            <!-- Model name -->
            <div class="mb-3 row">
              <label for="model-txt" class="col-sm-2 col-form-label">Model Name</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" id="model-txt" name="model-txt" onkeyup="this.value = this.value.toUpperCase()" value="<?php echo $alldata[0]["model"] ?? '' ?>" >
                <input type="text" class="form-control" id="model-txt-name" name="model-txt-name" onkeyup="this.value = this.value.toUpperCase()" value="<?php echo $alldata[0]["model"] ?? '' ?>" >
              </div>
            </div>

            <!-- Doc No -->
            <div class="mb-3 row">
              <label for="machno" class="col-sm-2 col-form-label">Doc. No</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="docno" value="<?php echo $alldata[0]["docno"] ?? '' ?>" readonly>
              </div>
            </div>

            
          </div>
          <div class="col-md-6">
            <!-- Lot Number -->
            <div class="mb-3 row">
              <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" onkeyup="this.value = this.value.toUpperCase()" value="<?php echo $alldata[0]["lotno"] ?? '' ?>" >
              </div>
            </div>

            <div class="mb-3 row">
              <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="machno-txt" name="machno-txt" onkeyup="this.value = this.value.toUpperCase()" value="<?php echo $alldata[0]["machno"] ?? '' ?>" >
              </div>
            </div>


            <div class="mb-3 row">
              <?php
                $empid = $session->get('empid');
                if(empty($alldata[0]["empid2"])){
                  $html = <<< "EOT"
                    <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div1">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <input type="text" class="form-control" value="$empid" id="empid-txt" name="empid-txt" onkeyup="empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','#position-txt','empid-lbl')" onkeydown="return (event.keyCode!=13);" placeholder="Masukkan EmpID dengan 6 digit" required>            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" class="form-control" id="name-txt" name="name-txt" placeholder="Masukkan nama">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" id="position-txt" name="position-txt" placeholder="Masukkan posisi">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon4">Shift----:</span>
                          <input type="number" class="form-control" id="shift-txt" name="shift-txt" placeholder="1 atau 2 atau 3">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <input type="text" class="form-control" id="group-txt" name="group-txt" placeholder="Contoh: 1S2GW1">
                        </div>
                        <input type="hidden" name="name-operator" id="name-operator">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Tidak ditemukan data kehadiran untuk ID karyawan di atas. Mohon ketik dengan benar. Jika jadwal Anda saat ini tidak sesuai, silakan hubungi bagian GA/P atau masukkan shift & group di atas secara manual.</span>
                    </div> 
                  EOT;
                  echo $html;
                }
                else{
                  $empid=$alldata[0]['empid'] ?? '';
                  $name=$alldata[0]['name'] ?? '';
                  $shift=$alldata[0]['shift'] ?? '';
                  $group=$alldata[0]['group'] ?? '';
                  $empid2=$alldata[0]['empid2'] ?? '';
                  $name2=$alldata[0]['name2'] ?? '';
                  $shift2=$alldata[0]['shift2'] ?? '';
                  $group2=$alldata[0]['group2'] ?? '';
                  
                  $html = <<< "EOT"
                    <label for="empid-txt" class="col-sm-2 col-form-label">Operator Start</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div1">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <input type="text" value="$empid" class="form-control" id="empid-txt" name="empid-txt" onkeyup="empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','#position-txt','empid-lbl')" onkeydown="return (event.keyCode!=13);" placeholder="Masukkan EmpID dengan 6 digit" required>            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" value="$name" class="form-control" id="name-txt" name="name-txt" placeholder="Masukkan nama">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" id="position-txt" name="position-txt" placeholder="Masukkan posisi">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon4">Shift----:</span>
                          <input type="number" value="$shift" class="form-control" id="shift-txt" name="shift-txt" placeholder="1 atau 2 atau 3">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <input type="text" value="$group" class="form-control" id="group-txt" name="group-txt" placeholder="Contoh: 1S2GW1">
                        </div>
                        <input type="hidden" name="name-operator" id="name-operator">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Tidak ditemukan data kehadiran untuk ID karyawan di atas. Mohon ketik dengan benar. Jika jadwal Anda saat ini tidak sesuai, silakan hubungi bagian GA/P atau masukkan shift & group di atas secara manual.</span>
                    </div> 

                    <div class="mb-3 row">
                      <label for="empid-txt" class="col-sm-2 col-form-label">Operator Finish</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div2">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <input type="text" value="$empid2" class="form-control" id="empid2-txt" name="empid2-txt" onkeyup="empAuto('#empid2-txt','#shift2-txt','#group2-txt','#name2-txt','#position2-txt','empid-lbl2')" onkeydown="return (event.keyCode!=13);" placeholder="Masukkan EmpID dengan 6 digit">            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" value="$name2" class="form-control" id="name2-txt" name="name2-txt" placeholder="Masukkan nama">          
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" id="position2-txt" name="position2-txt" placeholder="Masukkan posisi">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon4">Shift----:</span>
                          <input type="number" value="$shift2" class="form-control" id="shift2-txt" name="shift2-txt" placeholder="1 atau 2 atau 3">
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <input type="text" value="$group2" class="form-control" id="group2-txt" name="group2-txt" placeholder="Contoh: 1S2GW1">
                        </div>
                        <input type="hidden" name="name-operator2" id="name-operator2">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl2" name="empid-lbl2" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Tidak ditemukan data kehadiran untuk ID karyawan di atas. Mohon ketik dengan benar. Jika jadwal Anda saat ini tidak sesuai, silakan hubungi bagian GA/P atau masukkan shift & group di atas secara manual.</span>
                    </div>
                  EOT;
                  echo $html;
                }
              ?>
          </div>
          <?php 
            if (session()->getFlashdata('message')){ 
              echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
            }else{ 
              echo '<p id=msgsuccess class="invisible">Kosong</p>'; ;}
          ?>

          <!-- Input Form inject using javascript -->
          <div id="modal" class="">
            
          </div>

          </div>

          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-lg btn-primary btn-block mx-auto" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
          </div>
          
        </div>
      </div>

    </div>

  </div>
  <link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/global.css'); ?>">
  <script src="<?= base_url('assets/js/jquery-3.7.1.js'); ?>"></script>
  <script src="<?= base_url('assets/js/jquery-3.7.1.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/jquery-ui.js'); ?>"></script>
  <script src="<?= base_url('assets/js/edit.js?v=' . time()); ?>"></script>
  <script src="<?= base_url('assets/js/form/test.js'); ?>"></script>
</main>
<?= $this->endSection(); ?>