<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>

<?= $this->include('layout/navbar'); ?>

  <section class="py-2 container">
    <div class="col">
      <h2 class="fw-light">Edit Foregoing</h2>
      <input type="hidden" id="process-form" name="process-form" value="<?= htmlspecialchars($alldata[0]["process"] ?? '') ?>">
      <input type="hidden" id="number-edit" name="number-edit" value="<?= htmlspecialchars($alldata[0]["number"] ?? '') ?>">
    </div>
  </section>

  <section class="container-fluid">
    <form name="checksheet" action="<?= base_url('foregoing/update'); ?>" method="POST">
      <?= csrf_field(); ?>
      <input type="hidden" name="type-input" value="manual">
      <input type="hidden" name="number-edit" value="<?= htmlspecialchars($alldata[0]["number"] ?? '') ?>">

      <div class="row">
        <div class="col-md-6">
          <div class="mb-3 row">
            <label for="date" class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-6">
              <input type="text" class="form-control-plaintext" id="date" name="date" value="<?= !empty($alldata[0]['created_at']) ? date('Y-m-d', strtotime($alldata[0]['created_at'])) : date('Y-m-d'); ?>" readonly>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="device-txt" class="col-sm-2 col-form-label">Device</label>
            <div class="col-sm-6">
              <input type="hidden" class="form-control" id="device-txt" name="device-txt" value="<?= htmlspecialchars($alldata[0]["device"] ?? '') ?>" readonly>
              <input type="text" class="form-control" id="device-txt-name" name="device-txt-name" value="<?= htmlspecialchars($alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '') ?>" readonly>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="process-txt" class="col-sm-2 col-form-label">Process</label>
            <div class="col-sm-6">
              <input type="hidden" class="form-control" id="process-txt" name="process-txt" value="<?= htmlspecialchars($alldata[0]["process"] ?? '') ?>" readonly>
              <input type="text" class="form-control" id="process-txt-name" name="process-txt-name" value="<?= htmlspecialchars($alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '') ?>" readonly>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="model-txt" class="col-sm-2 col-form-label">Model Name</label>
            <div class="col-sm-6">
              <input type="hidden" class="form-control" id="model-txt" name="model-txt" value="<?= htmlspecialchars($alldata[0]["model"] ?? '') ?>">
              <input type="text" class="form-control" id="model-txt-name" name="model-txt-name" value="<?= htmlspecialchars($alldata[0]["model"] ?? '') ?>" oninput="document.getElementById('model-txt').value = this.value.toUpperCase(); this.value = this.value.toUpperCase();">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="docno" value="<?= htmlspecialchars($alldata[0]["docno"] ?? '-') ?>" readonly>
            </div>
          </div>
        </div>

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
        <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" style="display: block; margin: 0 auto; min-width: 140px;" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
      </div>
    </form>
  </section>
  
  <link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/global.css'); ?>">
  <script src="<?= base_url('assets/js/jquery-3.7.1.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/jquery-ui.js'); ?>"></script>
  <script src="<?= base_url('assets/js/form/test.js'); ?>"></script>
  <script>
    function empAuto(empIdTag, shiftTag, groupTag, nameTag, empIdlTag) {
        var empid = $.trim($(empIdTag).val());
        if (empid.length > 0) {
            $.getJSON("<?= base_url('home/ajaxAutofill'); ?>", { "empid-txt": empid })
                .done(function(data) {
                    if (data && data.found) {
                        if (data.acc) $(shiftTag).val(data.acc);
                        if (data.groupid) $(groupTag).val(data.groupid);
                        if (data.name) $(nameTag).val(data.name);
                        $('#' + empIdlTag).removeClass("visible").addClass("invisible");
                    } else {
                        $('#' + empIdlTag).removeClass("invisible").addClass("visible");
                    }
                })
                .fail(function() {
                    // Jangan bersihkan input jika AJAX gagal agar isian manual tidak hilang
                    $('#' + empIdlTag).removeClass("invisible").addClass("visible");
                });
        }
    }

    function injectForm() {
        var num = $('#number-edit').val();
        if (!num) return;

        $.ajax({
            url: "<?= base_url('foregoing/edit/form'); ?>",
            type: "GET",
            data: { number: num },
            dataType: "html",
            success: function(responseHtml) {
                $('#modal').html(responseHtml);
                $('#submit').show().css({'display': 'block', 'margin': '0 auto'});
            },
            error: function(xhr) {
                $('#modal').html("<div class='alert alert-danger'>Gagal memuat form edit.</div>");
                $('#submit').hide();
                console.error(xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        injectForm();

        if ($('#msgsuccess').html() && $('#msgsuccess').html() !== "Kosong") {
            alert($('#msgsuccess').html());
        }

        // Cek apakah data shift/name sudah terisi
        if (!$('#name-txt').val() || !$('#shift-txt').val()) {
            empAuto('#empid-txt', '#shift-txt', '#group-txt', '#name-txt', 'empid-lbl');
        }
    });
  </script>
</main>
<?= $this->endSection(); ?>