<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery hanya dimuat SATU kali -->
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>
<script src="<?= base_url('assets/js/form/test.js') ?>"></script>
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
        <input type="hidden" name="cnt-error-input" id="cnt-error-input" value="0">

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
                <label for="type-process-txt" class="col-sm-2 col-form-label">Doc. Type</label>
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

              <!-- Model name (Select2 Dropdown + Auto-Register jika ketik model baru) -->
              <div class="mb-3 row">
                <label for="model-txt" class="col-sm-2 col-form-label">Model Name</label>
                <div class="col-sm-6">
                  <select class="form-control" id="model-txt" name="model-txt">
                    <option value="">-- Ketik atau Pilih Model Name --</option>
                  </select>
                </div>
              </div>

              <!-- Doc No -->
              <div class="mb-3 row">
                <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control-plaintext" id="docno" name="docno" value="-" readonly>
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
                    <!-- Diubah dari type="number" ke type="text" agar value "admin" tidak error -->
                    <input type="text" class="form-control" id="empid-txt" value="<?= esc($session->get('empid')) ?>" name="empid-txt" readonly required>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon3">Name--:</span>
                    <input type="text" class="form-control" id="name-txt" name="name-txt" value="<?= esc($session->get('name')) ?>" readonly>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text" id="basic-addon-pos">Position:</span>
                    <input type="text" class="form-control" id="position-txt" name="position-txt" value="<?= esc($session->get('positionid')) ?>" readonly>
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
          <div id="form-input" class="mt-3">

          </div>
          <div class="d-flex justify-content-center mt-3">
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
        updateModelList();
    });

    $('#type-process-txt').change(function(){
        document.getElementById("form-action").action = "<?= rtrim(base_url(), '/') ?>/" + document.getElementById("type-process-txt").value;
    });

    // Auto-fill shift & group saat halaman dimuat
    empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','empid-lbl');

    // Aktifkan Select2 pada Model Name (Bisa pilih dari daftar ATAU ketik model baru untuk Auto-Register)
    $('#model-txt').select2({
        placeholder: "-- Ketik atau Pilih Model Name --",
        allowClear: true,
        tags: true,
        width: '100%',
        createTag: function (params) {
            var term = $.trim(params.term).toUpperCase();
            if (term === '') return null;
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    // Saat Operator memilih atau mengetik Model Name
    $('#model-txt').on('change', function() {
        var selectedModel = $(this).val();
        var processCode   = $('#process-txt').val();
        if (selectedModel && processCode && processCode !== '-') {
            // Auto-Register ke database jika model belum terdaftar + terapkan standar X, Y, Z, θ
            $.post("<?= base_url('model/auto-register') ?>", {
                process_code: processCode,
                model_name: selectedModel
            }, function(res) {
                if (res && res.data && res.data.standards) {
                    window.activeModelStandards = res.data.standards;
                } else {
                    window.activeModelStandards = null;
                }
                applyForegoingModelStandards();
            }, 'json');
        } else {
            window.activeModelStandards = null;
            applyForegoingModelStandards();
        }
    });
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
function empAuto(empIdTag, shiftTag, groupTag, nameTag, empIdlTag) {
    var empid = $.trim($(empIdTag).val());
    if (!empid) return;

    var warn = $('#' + empIdlTag);

    $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empid })
        .done(function (d) {
            if (d.found) {
                if (d.acc && !$(shiftTag).val()) $(shiftTag).val(d.acc);
                if (d.groupid && !$(groupTag).val()) $(groupTag).val(d.groupid);
                if (d.name && (!$(nameTag).val() || $(nameTag).val() === '-')) $(nameTag).val(d.name);
                if (d.acc && d.groupid) {
                    warn.removeClass('visible').addClass('invisible');
                } else {
                    warn.removeClass('invisible').addClass('visible');
                }
            } else {
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
        $('#form-input').empty();
        $('#submit').hide();
        $('#revisi-txt').val('');
        $('#docno').val('-');
        return;
    }

    $.ajax({
        url: "<?= base_url('process/list') ?>?device=" + device + "&type=" + docType,
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
    updateDocNo();
    updateInput();
    updateMachine();
    updateModelList();
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
        url: "<?= base_url('machine/list') ?>?process=" + processCode,
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

// FUNGSI LOGIKA REVISI & FORM INPUT DINAMIS (TERHUBUNG KE FORM BUILDER)
function updateInput() {
    let typeProcess, device, process;
    process = document.getElementById("process-txt").value;

    if(!process || process === 'null' || process === '-'){
        $('#submit').hide();
        $('#form-input').empty();
        document.getElementById('revisi-txt').value = "";
        return;
    }

    device = process.split("-")[0];
    let revField = document.getElementById('revisi-txt');
    let selectedDocType = $('#type-process-txt').val();

    if(selectedDocType === 'production' || process.split("-")[1] == 'p'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess = "production";
    }
    else if(selectedDocType === 'foregoing' || process.split("-")[1] == 'f'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess = "foregoing";
    }
    else{
        document.getElementById('model-txt').disabled = true;
        document.getElementById('lotno-txt').disabled = true;
        typeProcess = "startup";
    }

    document.getElementById("form-action").action = "<?= rtrim(base_url(), '/') ?>/" + typeProcess;

    let cnt_error = document.getElementById("cnt-error-input");
    let submit_button = document.getElementById("submit");
    submit_button.disabled = false;
    cnt_error.value = 0;

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
        docNo.value = text.split(" ")[0];
    }
}

// FUNGSI UPDATE DROPDOWN MODEL NAME PER PROSES
window.modelsCacheByProcess = {};
window.activeModelStandards = null;

function updateModelList() {
    var processCode = document.getElementById("process-txt").value;
    var modelSelect = $('#model-txt');
    var currentVal  = modelSelect.val();

    modelSelect.empty().append('<option value="">-- Ketik atau Pilih Model Name --</option>');

    if (!processCode || processCode === '-' || processCode === 'null') {
        modelSelect.trigger('change');
        return;
    }

    $.ajax({
        url: "<?= base_url('model/list') ?>?process=" + encodeURIComponent(processCode),
        dataType: 'JSON',
        success: function(data) {
            window.modelsCacheByProcess = {};
            if (data && data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    var mName = data[i].model_name;
                    window.modelsCacheByProcess[mName] = data[i].standards || null;
                    var isSel = (mName === currentVal);
                    modelSelect.append(new Option(mName, mName, isSel, isSel));
                }
            }
            modelSelect.trigger('change');
        }
    });
}

// TERAPKAN STANDAR BATAS MIN/MAX (X, Y, Z, θ) SESUAI MODEL YANG DIPILIH PADA FORM FOREGOING
function applyForegoingModelStandards() {
    var selectedModel = $('#model-txt').val();
    var stdMap = window.activeModelStandards || (selectedModel ? window.modelsCacheByProcess[selectedModel] : null);

    $('#form-input tr[data-grid-row]').each(function() {
        var rowName = $(this).attr('data-grid-row');
        var cfg = (stdMap && stdMap[rowName]) ? stdMap[rowName] : null;

        // Hanya ubah input sampel angka (jangan ubah kolom Offset Parameter From/To di sebelah kanan Judge)
        $(this).find('input.dyn-number-input').each(function() {
            var el = this;
            var $td = $(el).closest('td');$td.find('.std-model-hint').remove();

            if (!cfg) {
                // Kembalikan ke normal jika model belum dipilih / belum punya standar khusus
                if (!el.hasAttribute('readonly')) {
                    el.disabled = false;
                    el.style.backgroundColor = '';
                }
                return;
            }

            if (parseInt(cfg.active) === 0) {
                // Jika baris ini tidak diukur untuk model tersebut (Contoh: Baris Y pada GH16330A8C)
                el.value = '';
                el.disabled = true;
                el.style.backgroundColor = '#e2e8f0';
                el.removeAttribute('data-min');
                el.removeAttribute('data-max');
                $td.append('<small class="text-muted d-block mt-1 std-model-hint" style="font-size:10px;">Tidak Diukur</small>');
            } else {
                el.disabled = false;
                var minVal = (cfg.min !== null && cfg.min !== undefined && cfg.min !== '') ? cfg.min : '';
                var maxVal = (cfg.max !== null && cfg.max !== undefined && cfg.max !== '') ? cfg.max : '';
                el.setAttribute('data-min', minVal);
                el.setAttribute('data-max', maxVal);

                if (minVal !== '' || maxVal !== '') {
                    $td.append(`<small class="text-primary fw-semibold d-block mt-1 std-model-hint" style="font-size:10px;">Std: ${minVal !== '' ? minVal : '-∞'} ~ ${maxVal !== '' ? maxVal : '∞'}</small>`);
                }

                // Cek ulang warna validasi jika kotak sudah terisi angka
                if (el.value !== '') {
                    var v = parseFloat(el.value);
                    if ((minVal !== '' && v < parseFloat(minVal)) || (maxVal !== '' && v > parseFloat(maxVal))) {
                        el.style.backgroundColor = '#ffcccc';
                    } else {
                        el.style.backgroundColor = '';
                    }
                } else {
                    el.style.backgroundColor = '';
                }
            }
        });
    });
}
</script>
<?= $this->endSection(); ?>