<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>
<script src="<?= base_url('assets/js/form/test.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<?= $this->include('layout/navbar'); ?>

  <section class="py-2 container">
    <div class="col">
      <h2 class="fw-light m-0 font-weight-bold">Edit Startup</h2>
    </div>
  </section>

  <section class="container-fluid">
    <form name="checksheet" action="<?= base_url('startup/update'); ?>" method="POST" novalidate>
      <?= csrf_field(); ?>
      <input type="hidden" name="type-input" value="manual">
      <input type="hidden" id="number-edit" name="number-edit" value="<?= htmlspecialchars($alldata[0]["number"] ?? '') ?>">
      <input type="hidden" id="process-form" name="process-form" value="<?= htmlspecialchars($alldata[0]["process"] ?? '') ?>">

      <div class="card shadow mb-5">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control-plaintext" id="date" name="date" value="<?= !empty($alldata[0]['created_at']) ? date('Y-m-d', strtotime($alldata[0]['created_at'])) : date('Y-m-d'); ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Device</label>
                <div class="col-sm-6">
                  <input type="hidden" id="device-txt" name="device-txt" value="<?= htmlspecialchars($alldata[0]["device"] ?? '') ?>">
                  <input type="text" class="form-control" value="<?= htmlspecialchars($alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '') ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Process</label>
                <div class="col-sm-6">
                  <input type="hidden" id="process-txt" name="process-txt" value="<?= htmlspecialchars($alldata[0]["process"] ?? '') ?>">
                  <input type="text" class="form-control" value="<?= htmlspecialchars($alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '') ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Model Name</label>
                <div class="col-sm-6">
                  <!-- DIKUNCI READONLY -->
                  <input type="text" class="form-control" id="model-txt" name="model-txt" value="<?= htmlspecialchars($alldata[0]["model"] ?? '') ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Doc. No</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control-plaintext" id="docno" name="docno" value="<?= htmlspecialchars($alldata[0]["docno"] ?? '-') ?>" readonly>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Lot Number</label>
                <div class="col-sm-6">
                  <!-- DIKUNCI READONLY -->
                  <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" value="<?= htmlspecialchars($alldata[0]["lotno"] ?? '') ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Machine Number <span style="color:red;">*</span></label>
                <div class="col-sm-6">
                  <select class="form-control" id="machno-txt" name="machno-txt" required>
                    <option value="<?= htmlspecialchars($alldata[0]["machno"] ?? '') ?>" selected><?= htmlspecialchars($alldata[0]["machno"] ?? '') ?></option>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Revisi</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="revisi-txt" name="revisi-txt" value="<?= htmlspecialchars($alldata[0]["revisi"] ?? '0') ?>" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Operator</label>
                <div class="col-sm-6">
                  <div class="input-group mb-1">
                    <span class="input-group-text">Emp. ID :</span>
                    <input type="text" class="form-control" id="empid-txt" name="empid-txt" value="<?= htmlspecialchars($alldata[0]["empid"] ?? '') ?>" readonly>           
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Name--:</span>
                    <input type="text" class="form-control" id="name-txt" name="name-txt" value="<?= htmlspecialchars($alldata[0]["name"] ?? '') ?>" readonly>
                  </div>
                  <!-- TAMBAHAN KOLOM POSITION READONLY -->
                  <div class="input-group mb-1">
                    <span class="input-group-text">Position:</span>
                    <input type="text" class="form-control" id="position-txt" name="position-txt" value="Operator" readonly>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Shift----:</span>
                    <input type="number" class="form-control" id="shift-txt" name="shift-txt" value="<?= htmlspecialchars($alldata[0]["shift"] ?? '') ?>" readonly>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Group--:</span>
                    <input type="text" class="form-control" id="group-txt" name="group-txt" value="<?= htmlspecialchars($alldata[0]["group"] ?? '') ?>" readonly>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div id="modal" class="mt-3"></div>

          <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" onclick="return confirm('Simpan perubahan?');">Submit</button>
          </div>
        </div>
<<<<<<< HEAD
=======

        <div class="col-md-6">
          <div class="mb-3 row">
            <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" value="<?= htmlspecialchars($alldata[0]["lotno"] ?? '') ?>" onkeyup="this.value = this.value.toUpperCase()">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="machno-txt" name="machno-txt" value="<?= htmlspecialchars($alldata[0]["machno"] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
            <div class="col-sm-6">
              <div class="input-group mb-1" id="empid-div1">
                <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                <input type="text" class="form-control" id="empid-txt" value="<?= htmlspecialchars($alldata[0]["empid"] ?? '') ?>" name="empid-txt" onkeyup="empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','empid-lbl')" onkeydown="return (event.keyCode!=13);" placeholder="Masukkan ID dalam 6 digit" required>           
              </div>
              <div class="input-group mb-1">
                <span class="input-group-text" id="basic-addon3">Name--:</span>
                <input type="text" class="form-control" id="name-txt" name="name-txt" value="<?= htmlspecialchars($alldata[0]["name"] ?? '') ?>" placeholder="Masukkan nama anda" onkeyup="this.value = this.value.toUpperCase()">
              </div>
              <div class="input-group mb-1">
                <span class="input-group-text" id="basic-addon2">Shift----:</span>
                <input type="number" class="form-control" id="shift-txt" name="shift-txt" value="<?= htmlspecialchars($alldata[0]["shift"] ?? '') ?>" placeholder="1 atau 2 atau 3">
              </div>
              <div class="input-group mb-1">
                <span class="input-group-text" id="basic-addon3">Group--:</span>
                <input type="text" class="form-control" id="group-txt" name="group-txt" value="<?= htmlspecialchars($alldata[0]["group"] ?? '') ?>" placeholder="Contoh: 1S2GW1" onkeyup="this.value = this.value.toUpperCase()">
              </div>
            </div>
          </div>

          <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl">
            <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Tidak ditemukan data kehadiran untuk ID karyawan di atas. Mohon ketik dengan benar. Jika jadwal Anda saat ini tidak sesuai, silakan hubungi bagian GA/P atau masukkan shift & group di atas secara manual.</span>
          </div> 
        </div>

        <?php 
          if (session()->getFlashdata('message')){ 
            echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
          }else{ 
            echo '<p id=msgsuccess class="invisible">Kosong</p>';
          }
        ?>

        <!-- Input Form inject using javascript -->
        <div id="modal" class=""></div>
      </div>

      <!-- Tombol Submit di Tengah -->
      <div class="w-100 text-center d-flex justify-content-center my-4" style="width: 100%; text-align: center;">
        <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" style="display: block; margin: 0 auto; min-width: 140px;" onclick="return checkEditSubmit();">Submit</button>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
      </div>
    </form>
  </section>
  
  <script>
    $(document).ready(function() {
        $('#machno-txt').select2({ width: '100%' });
        updateMachineList();

        var num = $('#number-edit').val();
        if (num) {
            $.ajax({
                url: "<?= base_url('startup/edit/form'); ?>",
                type: "GET",
                data: { number: num },
                dataType: "html",
                success: function(responseHtml) {
                    $('#modal').html(responseHtml);
                    $('#submit').show();
                }
            });
        }
    });

<<<<<<< HEAD
    function updateMachineList() {
        var processCode = $('#process-txt').val();
        if(!processCode) return;
        $.getJSON("<?= base_url('machine/list') ?>?process=" + processCode, function(data) {
            var mSel = $('#machno-txt');
            var curVal = mSel.val();
            mSel.empty().append('<option value="">-- Pilih Mesin --</option>');
            if(data && data.length > 0) {
                $.each(data, function(i, item) {
                    var isSel = (item.machine_name === curVal);
                    mSel.append(new Option(item.machine_name, item.machine_name, isSel, isSel));
                });
=======
    function checkEditSubmit()
    {
        $('input[id$="_val"]').each(function() {
            var baseId = this.id.replace(/_val$/, '');
            if (typeof combineData === 'function') {
                combineData(baseId);
            }
        });
        return confirm('Apakah kamu yakin ingin menyimpan perubahan?');
    }

    function injectForm()
    {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "<?= base_url('startup/edit/form'); ?>?number=" + document.getElementById('number-edit').value, true);
        xhr.onload = (e) => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var modal = document.getElementById('modal');
                    modal.innerHTML = xhr.responseText;
                    $('#submit').show().css({'display': 'block', 'margin': '0 auto'});
                } else {
                    var modal = document.getElementById('modal');
                    $('#submit').hide();
                    modal.innerHTML = "";
                    console.error(xhr.statusText);
                }
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
            }
            if (curVal && mSel.find("option[value='" + curVal + "']").length === 0) {
                mSel.append(new Option(curVal, curVal, true, true));
            }
        });
    }
  </script>
<<<<<<< HEAD
=======
  <script src="<?= base_url('assets/js/form/test.js?v=' . (file_exists(FCPATH . 'assets/js/form/test.js') ? filemtime(FCPATH . 'assets/js/form/test.js') : '20261103')); ?>"></script>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
</main>
<?= $this->endSection(); ?>