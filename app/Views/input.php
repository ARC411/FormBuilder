<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<link rel="stylesheet" href="assets/css/jquery-ui.css">
<link rel="stylesheet" href="assets/css/global.css">
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery hanya dimuat SATU kali -->
<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/jquery-ui.js"></script>
<script src="assets/js/form/test.js"></script>
<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <?= $this->include('layout/navbar'); ?>

  <form name="checksheet" id="form-action" action="/input" method="POST">
  <?= csrf_field(); ?>
  <?php
    $session = \Config\Services::session();
  ?>

    <div class="container-fluid">

      <div class="card shadow mb-5">
        <div class="card-header py-3">
          <h3 class="m-0 font-weight-bold">Input Checksheet</h3>
        </div>
        <input type="hidden" name="cnt-error-input" id="cnt-error-input" value=0>

        <div class="card-body">
          <div class="row">

            <div class="col-md-6">
              <!-- Date input automatic -->
              <div class="mb-3 row">
                <label for="date" class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-6">
                  <input type="date" class="form-control" id="date" name="date" value="<?php echo date('Y-m-d');?>">
                </div>
              </div>

              <!-- Device input -->
              <div class="mb-3 row">
                <label for="device-txt" class="col-sm-2 col-form-label">Device</label>
                <div class="col-sm-6">
                  <select class="form-control" id="device-txt" name="device-txt" onchange="updateDevice()" required>
                    <option class="dropdown-item" value="-">choose device</option>
                    <?php
                      if($session->get('role')=="sl"){
                        echo '
                          <option value="sl33">Single Laser 3.3</option>
                          <option value="sl38">Single Laser 3.8</option>
                          <option value="sl56">Single Laser 5.6</option>
                          <option value="sl90">Single Laser 9.0</option>
                        ';
                      }
                      else if($session->get('role')=="fl"){
                        echo '
                          <option value="fl18">Frame Laser 1.8</option>
                        ';
                      }
                      else if($session->get('role')=="all"){
                        echo '
                          <option value="sl33">Single Laser 3.3</option>
                          <option value="sl38">Single Laser 3.8</option>
                          <option value="sl56">Single Laser 5.6</option>
                          <option value="sl90">Single Laser 9.0</option>
                          <option value="fl18">Frame Laser 1.8</option>
                        ';
                      }
                    ?>
                  </select>
                </div>
              </div>

              <!-- DocType -->
              <div class="mb-3 row">
                <label for="process-txt" class="col-sm-2 col-form-label">Doc. Type</label>
                <div class="col-sm-6">
                  <select class="form-control" id="type-process-txt" name="type-process-txt" onchange="updateDevice()" required>
                    <option class="dropdown-item" value="-">choose type document</option>
                    <option value="production">Production Control Sheet</option>
                    <option value="startup">Startup Control Sheet</option>
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
                  <input type="text" class="form-control" id="model-txt" name="model-txt" onkeyup="this.value = this.value.toUpperCase()" autofocus>
                </div>
              </div>

              <!-- Doc No -->
              <div class="mb-3 row">
                <label for="machno" class="col-sm-2 col-form-label">Doc. No</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control-plaintext" id="docno" value="-" readonly>
                </div>
              </div>

              <!-- Button to Open the Modal -->
              <button type="button" class="btn btn-lg btn-warning" data-bs-toggle="modal" data-bs-target="#myModal">
                Catatan
              </button>

              <!-- The Modal -->
              <div class="modal" id="myModal">
                <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="catatan-modal-header">Catatan</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="catatan-modal-body">
                      Modal body..
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <!-- Lot Number -->
              <div class="mb-3 row">
                <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" onkeyup="this.value = this.value.toUpperCase()">
                </div>
              </div>

              <!-- Machine Number (Select2) -->
              <div class="mb-3 row">
                <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number <span style="color:red;">*</span></label>
                <div class="col-sm-6">
                  <select class="form-control" id="machno-txt" name="machno-txt">
                    <option value="">-- Ketik atau Pilih Machine Number --</option>
                  </select>
                </div>
              </div>

              <!-- Parameter Revisi -->
              <div class="mb-3 row">
                <label for="revisi-txt" class="col-sm-2 col-form-label">Revisi</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="revisi-txt" name="revisi-txt" readonly>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
                <div class="col-sm-6">
                  <div class="input-group mb-1" id="empid-div1">
                    <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                    <input type="number" class="form-control" id="empid-txt" value="<?= $session->get('empid') ?>" name="empid-txt" readonly required>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon3">Name--:</span>
                    <input type="text" class="form-control" id="name-txt" name="name-txt" value="<?= $session->get('name') ?>" readonly>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon-pos">Position:</span>
                    <input type="text" class="form-control" id="position-txt" name="position-txt" value="<?= $session->get('positionid') ?>" readonly>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon2">Shift----:</span>
                    <input type="number" class="form-control" id="shift-txt" name="shift-txt" placeholder="1 atau 2 atau 3">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon3">Group--:</span>
                    <input type="text" class="form-control" id="group-txt" name="group-txt" placeholder="Contoh: 1S2GW1" onkeyup="this.value = this.value.toUpperCase()">
                  </div>
                  <input type="hidden" name="name-operator" id="name-operator">
                </div>
              </div>

              <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl">
                <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Data shift/group untuk ID karyawan di atas tidak ditemukan atau belum lengkap. Silakan isi shift &amp; group di atas secara manual, atau hubungi bagian GA/P jika jadwal Anda tidak sesuai.</span>
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
          <div id="form-input" class="">

          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-lg btn-primary btn-block mx-auto" id="submit" onclick="return checkSubmit();">Submit</button>
          </div>

        </div>
      </div>
</div>
  </form>

</main>
<script>
$(document).ready(function()
{
    // Aktifkan Select2
    $('#machno-txt').select2({
        placeholder: "-- Ketik atau Pilih Machine Number --",
        allowClear: true,
        width: '100%'
    });

    updateDevice();
    $('#submit').hide();
    if($('#msgsuccess').html()!="Kosong"){
        alert($('#msgsuccess').html());
    }

    $('#process-txt').change(function()
    {
        updateInput();
        updateDocNo();
        updateMachine();
    });

    $('#type-process-txt').change(function(){
        document.getElementById("form-action").action = "/checkseet/" + document.getElementById("type-process-txt").value;
    });

    // Auto-fill shift & group saat halaman dimuat (tetap bisa diedit)
    empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','empid-lbl');
});

// FUNGSI PENJAGA FORM
function checkSubmit() {
    var machno = $('#machno-txt').val();
    if (!machno || machno === "") {
        alert("GAGAL: Machine Number WAJIB dipilih atau diisi sebelum Submit!");
        return false;
    }
    $('#form-input input[name="machno-txt"]').remove();
    return confirm('Apakah kamu yakin ingin menyimpan data ini?');
}

// FUNGSI AUTO-FILL SHIFT & GROUP
// Hanya mengisi field yang masih kosong, sehingga input manual user tidak tertimpa
function empAuto(empIdTag, shiftTag, groupTag, nameTag, empIdlTag) {
    var empid = $.trim($(empIdTag).val());
    if (!empid) return;

    var warn = $('#' + empIdlTag);

    $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empid })
        .done(function (d) {
            if (d.found && d.acc && d.groupid) {
                if (!$(shiftTag).val()) $(shiftTag).val(d.acc);
                if (!$(groupTag).val()) $(groupTag).val(d.groupid);
                warn.removeClass('visible').addClass('invisible');
            } else {
                if (d.found) { // terisi sebagian
                    if (d.acc && !$(shiftTag).val()) $(shiftTag).val(d.acc);
                    if (d.groupid && !$(groupTag).val()) $(groupTag).val(d.groupid);
                }
                warn.removeClass('invisible').addClass('visible');
            }
        })
        .fail(function (xhr) {
            console.error('autofill gagal', xhr.status, xhr.responseText);
            warn.removeClass('invisible').addClass('visible');
        });
}

// FUNGSI UPDATE DEVICE & PROCESS
function updateDevice() {
    var docType = $('#type-process-txt').val();
    var device = $('#device-txt').val();

    if(device === '-' || docType === '-') {
        $('#process-txt').empty().append('<option value="-">choose process</option>');
        return;
    }

    $.ajax({
        url: "<?php echo base_url();?>process/list?device="+device+"&type="+docType,
        dataType: 'JSON',
        success: function(data) {
            populateProcess(data);
        },
        error: function(xhr) {
            if (xhr.responseText) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if(data) { populateProcess(data); return; }
                } catch(e) {}
            }
        }
    });
}

function populateProcess(data) {
    var processDropdown = $('#process-txt');
    processDropdown.empty();
    if(data && data.length > 0) {
        $.each(data, function(index, item) {
            processDropdown.append(new Option(item.name, item.process_code));
        });
    } else {
        processDropdown.append(new Option("-- Data Kosong --", "-"));
    }
    updateInput();
    updateDocNo();
    updateMachine();
}

// FUNGSI UPDATE MACHINE
function updateMachine() {
    var processCode = document.getElementById("process-txt").value;
    var machineSelect = $('#machno-txt');

    machineSelect.empty().append('<option value="">-- Ketik atau Pilih Machine Number --</option>');

    if(!processCode || processCode === '-' || processCode === 'null'){
        machineSelect.trigger('change');
        return;
    }

    $.ajax({
        url: "<?php echo base_url();?>machine/list?process=" + processCode,
        dataType: 'JSON',
        success: function(data) {
            populateMachine(data, machineSelect);
        },
        error: function(xhr) {
            if(xhr.responseText) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if(data) { populateMachine(data, machineSelect); return; }
                } catch(e) {}
            }
            machineSelect.empty().append('<option value="">-- Terjadi Kesalahan --</option>');
            machineSelect.trigger('change');
        }
    });
}

function populateMachine(data, machineSelect) {
    if(data && data.length > 0) {
        for (let i = 0; i < data.length; i++) {
            var newOption = new Option(data[i].machine_name, data[i].machine_name, false, false);
            machineSelect.append(newOption);
        }
    } else {
         machineSelect.empty().append('<option value="">-- Mesin Belum Didaftarkan --</option>');
    }
    machineSelect.trigger('change');
}

// FUNGSI LOGIKA REVISI & FORM INPUT
function updateInput() {
    let typeProcess, device, process;
    process = document.getElementById("process-txt").value;

    if(!process || process === 'null' || process === '-'){
        var form_input = document.getElementById('form-input');
        $('#submit').hide();
        form_input.innerHTML = "";
        document.getElementById('revisi-txt').value = ""; // Kosongkan revisi
        return;
    }

    device = process.split("-")[0];
    let revField = document.getElementById('revisi-txt');

    if(process.split("-")[1] == 'p'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess="production";
    }
    else if(process.split("-")[1] == 'f'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess="foregoing";
    }
    else{
        document.getElementById('model-txt').disabled = true;
        document.getElementById('lotno-txt').disabled = true;
        typeProcess="startup";
    }

    document.getElementById("form-action").action = "/checkseet/" + typeProcess;

    let cnt_error = document.getElementById("cnt-error-input");
    let submit_button = document.getElementById("submit");
    submit_button.disabled = false;
    cnt_error.value = 0;

    // --- TARIK NILAI REVISI DINAMIS DARI DATABASE ---
    // Karena kita sudah punya fungsi getAll() di ProcessModel, kita bisa memanfaatkannya
    // Atau bisa menggunakan endpoint API yang sudah Abang siapkan untuk Process
    $.ajax({
        url: "<?php echo base_url();?>process/detail?process_code=" + process, 
        // Catatan: Pastikan endpoint 'process/detail' ada di controller Process Abang
        // Jika tidak ada, kita bisa gunakan trik sederhana dengan mengambil nilai dari dropdown text
        dataType: 'JSON',
        success: function(res) {
            if(res && res.revisi) {
                if (revField) revField.value = res.revisi;
            } else {
                if (revField) revField.value = "-"; // Default jika kosong
            }
        },
        error: function() {
            // JURUS ALTERNATIF: Ambil revisi dari ujung string DocNo di dropdown!
            // Mengingat Abang tadi bilang "angka revisi itu ditunjukan sama angka setelah tanda '-' terakhir di kolom docno"
            var processValue = document.getElementById("process-txt");
            if(processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]){
                var text = processValue.options[processValue.selectedIndex].text; // Cth: FF-D1-004-18 (Nama Proses)
                var docno = text.split(" ")[0]; // Mengambil FF-D1-004-18
                var parts = docno.split("-");
                if (parts.length > 1) {
                    var lastPart = parts[parts.length - 1]; // Mengambil 18
                    if (revField) revField.value = lastPart;
                } else {
                    if (revField) revField.value = "-";
                }
            }
        }
    });
    // ------------------------------------------------

    updateSetting(process);

    $.ajax({
        url: "<?php echo base_url();?>" + typeProcess + "/form?device=" + encodeURIComponent(device) + "&process=" + encodeURIComponent(process),
        type: "GET",
        success: function(response) {
            if (response && response.trim() !== "") {
                $('#submit').show();
                $('#form-input').html(response);
                var processValue = document.getElementById("process-txt");
                var header_modal = document.getElementById('catatan-modal-header');
                if(processValue && processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]){
                    header_modal.innerHTML = processValue.options[processValue.selectedIndex].text;
                }
            } else {
                $('#submit').hide();
                $('#form-input').empty();
            }
        },
        error: function() {
            $('#submit').hide();
            $('#form-input').empty();
        }
    });
}

function updateSetting(processCode) {
    if(!processCode || processCode === 'null' || processCode === '-'){
        $('#catatan-modal-body').html("Pilih proses terlebih dahulu.");
        return;
    }
    $.ajax({
        url: "<?php echo base_url();?>input/note-form/" + encodeURIComponent(processCode),
        type: "GET",
        success: function(response) {
            $('#catatan-modal-body').html(response);
        },
        error: function() {
            $('#catatan-modal-body').html("Catatan tidak ditemukan untuk proses ini.");
        }
    });
}

function updateDocNo() {
    var docNo = document.getElementById("docno");
    var processValue = document.getElementById("process-txt");
    if(processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]){
        var text = processValue.options[processValue.selectedIndex].text;
        docNo.value=text.split(" ")[0];
    }
}
</script>
<?= $this->endSection(); ?>