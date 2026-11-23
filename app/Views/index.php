<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>

<?= $this->include('layout/navbar'); ?>

  <section class="py-2 container">
    <div class="col">
      <h2 class="fw-light">Input Startup</h2>
      <!-- <p class="lead text-muted"><?php echo '<a href="history">startup record</a>'; ?></p> -->
    </div>
  </section>

  <section class="container-fluid">
    <form name="checksheet" id="form-input" action="<?= base_url('input') ?>" method="POST">
      <?= csrf_field(); ?>

      <div class="row">
        
        <div class="col-md-6">
          <!-- Date input automatic -->
          <div class="mb-3 row">
            <label for="date" class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-6">
              <input type="text" class="form-control-plaintext" id="date" name="date" value="<?php echo date('Y-m-d');?>" readonly>
              <!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
            </div>
          </div>

          <!-- Device input -->
          <div class="mb-3 row">
            <label for="device-txt" class="col-sm-2 col-form-label">Device</label>
            <div class="col-sm-6">
              <select class="form-control" id="device-txt" name="device-txt" onchange=changeDevice() required>
                <option class="dropdown-item" value="00">choose device</option>
                <option value="fl18">Frame Laser 1.8</option>
                <option value="sl56">Single Laser 5.6</option>
              </select>
            </div>
          </div>

          <!-- DocType -->
          <div class="mb-3 row">
            <label for="process-txt" class="col-sm-2 col-form-label">Doc. Type</label>
            <div class="col-sm-6">
              <select class="form-control" id="type-process-txt" name="type-process-txt" onchange=changeDevice() required>
                <option class="dropdown-item" value="00">choose type document</option>
                <option value="production">Production Control Sheet</option>
                <option value="startup">Start Up Control Sheet</option>
                <option value="foregoing">Foregoing Control Sheet</option>
              </select>
            </div>
          </div>

          <!-- Process of Device -->
          <div class="mb-3 row">
            <label for="process-txt" class="col-sm-2 col-form-label">Process</label>
            <div class="col-sm-6">
              <select class="form-control" id="process-txt" name="process-txt" required>
              
              </select>
            </div>
          </div>


          <!-- Model name -->
          <div class="mb-3 row">
            <label for="model-txt" class="col-sm-2 col-form-label">Model Name</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="model-txt" name="model-txt" required autofocus>
            </div>
          </div>

          <!-- Doc No -->
          <div class="mb-3 row">
            <label for="machno" class="col-sm-2 col-form-label">Doc. No</label>
            <div class="col-sm-6">
              <input type="text" class="form-control-plaintext" id="docno" value="-" readonly>
            </div>
          </div>

          <!-- Tipe Input -->
          <div class="mb-3 row">
            <label for="machno" class="col-sm-2 col-form-label">Tipe Input</label>
            <div class="col-sm-6">
              <select class="form-control" id="type-input" name="type-input">
                <option value="manual">Manual</option>
                <option value="otomatis">Otomatis</option>
              </select>
            </div>
          </div>

          
        </div>

        <div class="col-md-6">
          <!-- Lot Number -->
          <div class="mb-3 row">
            <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="lotno-txt" name="lotno-txt"required>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="machno-txt" name="machno-txt" required>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
            <div class="col-sm-6">
              <div class="input-group mb-1" id="empid-div1">
                <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                <input type="number" class="form-control" id="empid-txt" name="empid-txt" onkeyup="empAuto()" onkeydown="return (event.keyCode!=13);" placeholder="Please type in 6 digit" required>           
              </div>
              <!-- <div class="input-group mb-1" id="empid-div2">
                <span class="input-group-text" id="basic-addon1">Emp. ID</span>
                <input type="text" class="form-control" id="empid" name="empid" onkeydown="resetEmp()">           
              </div> -->
              <div class="input-group mb-1">
                <span class="input-group-text" id="basic-addon2">Shift----:</span>
                <input type="number" class="form-control" id="shift-txt" name="shift-txt" placeholder="1 OR 2 OR 3">
              </div>
              <div class="input-group mb-1">
                <span class="input-group-text" id="basic-addon3">Group--:</span>
                <input type="text" class="form-control" id="group-txt" name="group-txt" placeholder="Example: 1S2GW1">
              </div>
            </div>
          </div>

          <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl" >
            <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Tidak ditemukan data kehadiran untuk ID karyawan di atas. Mohon ketik dengan benar. Jika jadwal Anda saat ini tidak sesuai, silakan hubungi bagian GA/P atau masukkan shift & group di atas secara manual.</span>
          </div> 

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
      <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin??');">Submit</button>
    </form>
  </section>
  
  <link rel="stylesheet" href="assets/css/jquery-ui.css">
  <link rel="stylesheet" href="assets/css/global.css">
  <script src="assets/js/jquery-3.7.1.js"></script>
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script src="assets/js/jquery-ui.js"></script>
  <script src="assets/js/input.js"></script>
  <script src="assets/js/form/slvi.js"></script>
  <script src="assets/js/form/sldbm.js"></script>
  <script src="assets/js/form/sldb1.js"></script>
  <script src="assets/js/form/sldb2.js"></script>
  <script src="assets/js/form/slld2.js"></script>
  <script src="assets/js/form/slildsm.js"></script>
  <script src="assets/js/form/slwb.js"></script>
  <script src="assets/js/form/slwbh.js"></script>
  <script src="assets/js/form/slcsm.js"></script>
  <script src="assets/js/form/slcs.js"></script>
  <script src="assets/js/form/slht.js"></script>
  <script src="assets/js/form/sllt.js"></script>
  <script src="assets/js/form/slft.js"></script>
  <script src="assets/js/form/slft2.js"></script>
  <script src="assets/js/form/slbi.js"></script>
  <script src="assets/js/form/slpldsm.js"></script>
  <script src="assets/js/form/slpag.js"></script>
  <script src="assets/js/form/flwb.js"></script>
  <script src="assets/js/form/flcsa.js"></script>
  <script src="assets/js/form/fltbc.js"></script>
  <script src="assets/js/form/fllc.js"></script>
</main>
<?= $this->endSection(); ?>