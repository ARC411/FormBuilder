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
                    <!-- Banner & Toggle Mesin Stand By (Tampil khusus untuk Doc. Type: Startup) -->
          <div id="standby-wrapper" class="card border-warning mb-3 shadow-sm" style="display: none; background: #fffdf5; border-radius: 6px;">
            <div class="card-body py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
              <div class="d-flex align-items-center">
                <div class="form-check form-switch me-2 mb-0" style="min-height: auto;">
                  <input class="form-check-input" type="checkbox" id="is-standby" name="is_standby" value="1" style="cursor: pointer; width: 2em; height: 1.1em; margin-top: 0.15em;">
                </div>
                <div>
                  <label for="is-standby" class="form-check-label fw-bold text-dark mb-0 small" style="cursor: pointer;">
                    <i class="fas fa-power-off text-warning me-1"></i> Mesin Stand By
                  </label>
                  <span class="text-muted ms-2 d-none d-sm-inline" style="font-size: 0.78rem;">
                    (Aktifkan jika mesin idle/standby: parameter otomatis "-", Note wajib diisi)
                  </span>
                </div>
              </div>
              <div id="standby-badge" class="badge bg-warning text-dark px-2 py-1 fw-bold shadow-sm" style="display: none; font-size: 0.75rem;">
                <i class="fas fa-pause-circle me-1"></i> STAND BY AKTIF
              </div>
            </div>
          </div>

          <div id="form-input" class="">

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

    // Pengecekan khusus Mode Mesin Stand By
    if ($('#is-standby').is(':checked')) {
        var $noteInput = findNoteInput();
        if ($noteInput && (!$.trim($noteInput.val()) || $.trim($noteInput.val()) === '-')) {
            alert("GAGAL: Mesin dalam status STAND BY.\nKolom Catatan/Note WAJIB diisi keterangan/alasan standby!");
            $noteInput.focus();
            return false;
        }
        // Pastikan seluruh parameter non-note terisi strip '-'
        applyStandbyMode(true);
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

    if (!process || process === 'null' || process === '-') {
        $('#submit').hide();
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
        form_input.innerHTML = "";
        document.getElementById('revisi-txt').value = ""; // Kosongkan revisi
        return;
    }

    device = process.split("-")[0];
    let revField = document.getElementById('revisi-txt');
    let selectedDocType = $('#type-process-txt').val();

    if (selectedDocType === 'production' || process.split("-")[1] == 'p') {
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess="production";
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
    }
    else if(process.split("-")[1] == 'f'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('lotno-txt').disabled = false;
        typeProcess="foregoing";
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
    }
    else{
        document.getElementById('model-txt').disabled = true;
        document.getElementById('lotno-txt').disabled = true;
        typeProcess="startup";
        $('#standby-wrapper').slideDown();
    }

    document.getElementById("form-action").action = "<?= rtrim(base_url(), '/') ?>/" + typeProcess;

    let cnt_error = document.getElementById("cnt-error-input");
    let submit_button = document.getElementById("submit");
    submit_button.disabled = false;
    cnt_error.value = 0;

    updateSetting(process);

    // 1. Panggil Endpoint Dynamic Form Builder terlebih dahulu!
    $.ajax({
        url: "<?= base_url('form-builder/render-input') ?>",
        type: "GET",
        data: {
            device: device,
            process: process,
            doc_type: typeProcess
        },
        dataType: "JSON",
        success: function(res) {
            if (res && res.status === 'success' && res.html) {
                $('#submit').show();
                $('#form-input').html(res.html);

                // Isi otomatis kolom Revisi dari Revisi Aktif di Form Builder
                if (revField && res.revisi !== undefined) {
                    revField.value = res.revisi;
                }

                // Sinkronkan juga akhiran Doc. No dengan Revisi aktif (jika Doc. No memiliki 4 segmen seperti FF-D2-003-14)
                var docNoEl = document.getElementById("docno");
                if (docNoEl && docNoEl.value && res.revisi !== undefined) {
                    var dParts = docNoEl.value.split("-");
                    if (dParts.length >= 4) {
                        dParts[dParts.length - 1] = res.revisi;
                        docNoEl.value = dParts.join("-");
                    }
                }

                var processValue = document.getElementById("process-txt");
                var header_modal = document.getElementById('catatan-modal-header');
                if (processValue && processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]) {
                    header_modal.innerHTML = processValue.options[processValue.selectedIndex].text;
                }

                // Terapkan standar model jika Foregoing
                if (typeof applyForegoingModelStandards === 'function') {
                    applyForegoingModelStandards();
                }
            } else {
                loadStaticFallbackForm(typeProcess, device, process);
            }
        },
        error: function() {
            loadStaticFallbackForm(typeProcess, device, process);
        }
    });
}

// Fallback ke form statis lama HANYA jika proses belum bisa dirender oleh Form Builder
function loadStaticFallbackForm(typeProcess, device, process) {
    $.ajax({
        url: "<?= base_url() ?>" + typeProcess + "/form?device=" + encodeURIComponent(device) + "&process=" + encodeURIComponent(process),
        type: "GET",
        success: function(response) {
            if (response && response.trim() !== "") {
                $('#submit').show();
                $('#form-input').html(response);
                var processValue = document.getElementById("process-txt");
                var header_modal = document.getElementById('catatan-modal-header');
                if (processValue && processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]) {
                    header_modal.innerHTML = processValue.options[processValue.selectedIndex].text;
                }

                // Jika checkbox Stand By aktif saat form dimuat, langsung terapkan
                if ($('#is-standby').is(':checked')) {
                    applyStandbyMode(true);
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
// Helper untuk mencari input Note / Catatan di form startup apapun
function findNoteInput() {
    var $trs = $('#form-input table tbody tr');
    // 1. Cari baris yang kolom labelnya memiliki kata 'note' atau 'catatan'
    var $targetTr = $trs.filter(function() {
        var labelText = $(this).children('td:not(:last-child)').text().toLowerCase();
        return labelText.indexOf('note') !== -1 || labelText.indexOf('catatan') !== -1;
    }).last();

    if ($targetTr.length) {
        var $inp = $targetTr.find('input[type="text"], textarea');
        if ($inp.length) return $inp;
    }

    // 2. Baris terakhir di tbody tabel
    var $lastTr = $trs.last();
    var $lastInp = $lastTr.find('input[type="text"], textarea');
    if ($lastInp.length) return $lastInp;

    // 3. Fallback: input tipe teks terakhir
    return $('#form-input input[type="text"]').last();
}

// Fungsi aktivasi / deaktivasi Mode Mesin Stand By
function applyStandbyMode(isActive) {
    var $form = $('#form-input');
    if (!$form.length || !$form.children().length) return;

    var $note = findNoteInput();

    if (isActive) {
        $('#standby-badge').fadeIn();
        $('#standby-wrapper').css('background', 'linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%)');

        $form.find('input, select, textarea').each(function() {
            var $el = $(this);

            // Lewati token csrf & hidden non-parameter
            if ($el.attr('type') === 'hidden' && (!$el.attr('name') || $el.attr('name').indexOf('par') !== 0)) {
                return;
            }

            // Jika ini adalah input Note/Catatan
            if ($note && $el.is($note)) {
                $el.prop('readonly', false);
                $el.prop('required', true);
                if (!$.trim($el.val()) || $.trim($el.val()) === '-') {
                    $el.val('Mesin Stand By');
                }
                $el.css({
                    'border': '2px solid #ffc107',
                    'background-color': '#fffde7'
                });
                return;
            }

            // Input number / text parameter
            if ($el.is('input[type="number"], input[type="text"], textarea')) {
                if ($el.attr('type') === 'number') {
                    $el.data('orig-type', 'number');
                    $el.attr('type', 'text');
                }
                if ($el.prop('required')) {
                    $el.data('orig-required', true);
                    $el.prop('required', false);
                }
                $el.val('-');
                $el.prop('readonly', true);
                $el.css({
                    'background-color': '#f8f9fa',
                    'color': '#495057'
                });
                this.dataset.hasError = "0";
            } else if ($el.is('select')) {
                if ($el.prop('required')) {
                    $el.data('orig-required', true);
                    $el.prop('required', false);
                }
                if (!$el.find('option[value="-"]').length) {
                    $el.append('<option value="-" class="standby-temp-opt">-</option>');
                }
                $el.val('-');
                $el.css({
                    'pointer-events': 'none',
                    'background-color': '#f8f9fa'
                });
                this.dataset.hasError = "0";
            }
        });

        // Hilangkan semua indikator error validasi form
        $('#cnt-error-input').val(0);
        if (typeof changeDisableSubmit === 'function') {
            changeDisableSubmit(false);
        }
        $('#submit').prop('disabled', false);

        if ($note && $note.length) {
            $note.focus();
        }
    } else {
        // Matikan Mode Stand By
        $('#standby-badge').fadeOut();
        $('#standby-wrapper').css('background', 'linear-gradient(135deg, #fffcf0 0%, #fff3cd 100%)');

        $form.find('input, select, textarea').each(function() {
            var $el = $(this);

            if ($note && $el.is($note)) {
                $el.prop('required', false);
                if ($el.val() === 'Mesin Stand By') {
                    $el.val('');
                }
                $el.css({
                    'border': '',
                    'background-color': ''
                });
                return;
            }

            if ($el.data('orig-type') === 'number') {
                $el.attr('type', 'number');
                $el.removeData('orig-type');
            }
            if ($el.data('orig-required')) {
                $el.prop('required', true);
                $el.removeData('orig-required');
            }
            if ($el.val() === '-') {
                $el.val('');
            }
            $el.prop('readonly', false);
            $el.css({
                'background-color': '',
                'color': ''
            });

            if ($el.is('select')) {
                $el.find('.standby-temp-opt').remove();
                $el.css('pointer-events', '');
                if ($el.val() === '-') {
                    $el.val('');
                }
            }
        });

        if (typeof syncErrorCount === 'function') {
            syncErrorCount();
        }
    }
}

// Event listener toggle Mesin Stand By
$(document).on('change', '#is-standby', function() {
    applyStandbyMode($(this).is(':checked'));
});
</script>
<?= $this->endSection(); ?>