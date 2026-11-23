<section class="container-fluid">
    <form name="checksheet" action="<?= base_url('device') ?>" method="POST">
      <?= csrf_field(); ?>
      <input type="hidden" name="type-input" value="manual">

      <div class="row">
      <div class="col-md-6">

          <!-- Device input -->
          <!-- <div class="mb-3 row">
            <label for="device" class="col-sm-2 col-form-label">Device</label>
            <div class="col-sm-6">
              <select id="device" name="device" class="form-select" aria-label="Default select example">
                <option selected>Pilih device</option>
                <option value="fl18">Frame Laser 1.8</option>
                <option value="sl33">Single Laser 3.3</option>
                <option value="sl38">Single Laser 3.8</option>
                <option value="sl56">Single Laser 5.6</option>
                <option value="sl90">Single Laser 9.0</option>
              </select>
            </div>
          </div> -->

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
      <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
    </form>
  </section>
  
  <link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
  <script src="<?= base_url('assets/js/jquery-3.7.1.js') ?>"></script>
  <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>