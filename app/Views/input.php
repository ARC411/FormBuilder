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
<script src="<?= base_url('assets/js/form/test.js?v=' . (file_exists(FCPATH . 'assets/js/form/test.js') ? filemtime(FCPATH . 'assets/js/form/test.js') : '20261103')); ?>"></script>
<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <?= $this->include('layout/navbar'); ?>

  <form name="checksheet" id="form-action" action="<?= base_url('input') ?>" method="POST" novalidate>
  <?= csrf_field(); ?>
  <?php
    $session = \Config\Services::session();
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $initialDate = $today;
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
              <div class="mb-3 row">
                <label for="date" class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-6">
                  <input type="date" class="form-control font-weight-bold" id="date" name="date" 
                         value="<?= $initialDate ?>" 
                         min="<?= $yesterday ?>" 
                         max="<?= $today ?>" required>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="device-txt" class="col-sm-2 col-form-label">Device</label>
                <div class="col-sm-6">
                  <select class="form-control" id="device-txt" name="device-txt" onchange="updateDevice()" required>
                    <option class="dropdown-item" value="-">choose device</option>
                    <?php
                      $userRole = strtolower((string)$session->get('role'));
                      $isAdmin  = (bool)$session->get('isadmin');

                      if ($isAdmin || $userRole === 'all') {
                        echo '
                          <option value="sl33">Single Laser 3.3</option>
                          <option value="sl38">Single Laser 3.8</option>
                          <option value="sl56">Single Laser 5.6</option>
                          <option value="sl90">Single Laser 9.0</option>
                          <option value="fl18">Frame Laser 1.8</option>
                        ';
                      }
                      else if ($userRole === 'fl') {
                        echo '
                          <option value="fl18">Frame Laser 1.8</option>
                        ';
                      }
                      else if ($userRole === 'sl') {
                        echo '
                          <option value="sl33">Single Laser 3.3</option>
                          <option value="sl38">Single Laser 3.8</option>
                          <option value="sl56">Single Laser 5.6</option>
                          <option value="sl90">Single Laser 9.0</option>
                        ';
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="process-name-txt" class="col-sm-2 col-form-label">Process</label>
                <div class="col-sm-6">
                  <select class="form-control" id="process-name-txt">
                    <option value="-">choose process</option>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="type-process-txt" class="col-sm-2 col-form-label">Doc. Type</label>
                <div class="col-sm-6">
                  <!-- name="process-txt" tetap berisi process_code, jadi backend tidak berubah -->
                  <select class="form-control" id="process-txt" name="process-txt" required>
                    <option value="-">choose type document</option>
                  </select>
                  <!-- name="type-process-txt" tetap dikirim (production/startup/foregoing), diisi otomatis -->
                  <input type="hidden" id="type-process-txt" name="type-process-txt" value="-">
                </div>
              </div>

<!-- Model name (Select2 Dropdown + Auto-Register jika ketik model baru) -->
              <div class="mb-3 row">
                <label for="model-txt" class="col-sm-2 col-form-label">Model Name <span id="req-model" class="text-danger d-none">*</span></label>
                <div class="col-sm-6">
                  <select class="form-control" id="model-txt" name="model-txt">
                    <option value="">-- Ketik atau Pilih Model Name --</option>
                  </select>
                </div>
              </div>

              <div class="mb-3 row">
                <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control-plaintext" id="docno" name="docno" value="-" readonly>
                </div>
              </div>

              <button type="button" class="btn btn-lg btn-warning" data-bs-toggle="modal" data-bs-target="#myModal">
                Catatan
              </button>

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
              <div class="mb-3 row">
                <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number <span id="req-lot" class="text-danger d-none">*</span></label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" onkeyup="this.value = this.value.toUpperCase()">
                </div>
              </div>

              <div class="mb-3 row">
                <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number <span style="color:red;">*</span></label>
                <div class="col-sm-6">
                  <select class="form-control" id="machno-txt" name="machno-txt">
                    <option value="">-- Ketik atau Pilih Machine Number --</option>
                  </select>
                </div>
              </div>

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
                  
                  <div class="input-group mb-1 border rounded" style="border-color: #dc3545 !important; border-width: 2px !important;">
                    <span class="input-group-text text-danger font-weight-bold" id="basic-addon2" style="background-color: #fff5f5;">Shift----:</span>
                    <select class="form-control font-weight-bold" id="shift-txt" name="shift-txt" required>
                        <option value="">Pilih Shift (Wajib)</option>
                        <option value="A">A (Pagi)</option>
                        <option value="B">B (Sore)</option>
                        <option value="C">C (Malam)</option>
                    </select>
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
    $('#machno-txt').select2({
        placeholder: "-- Ketik atau Pilih Machine Number --",
        allowClear: true,
        width: '100%'
    });

    $('#device-txt').change(updateDevice);
    updateDevice();
    $('#submit').hide();
    if($('#msgsuccess').html()!="Kosong"){
        alert($('#msgsuccess').html());
    }

    $('#process-name-txt').change(fillDocType);

    $('#process-txt').change(function()
    {
        $('#type-process-txt').val($(this).find('option:selected').data('type') || '-');
        updateInput();
        updateDocNo();
        updateMachine();
        updateModelList();
    });


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
            // Auto-Register ke database jika model belum terdaftar + terapkan standar model
            $.post("<?= base_url('model/auto-register') ?>", {
                process_code: processCode,
                model_name: selectedModel
            }, function(res) {
                var stds = (res && res.data && res.data.standards) ? res.data.standards : null;
                applyDynamicModelStandards(stds, selectedModel);
            }, 'json');
        } else {
            applyDynamicModelStandards(null, '');
        }
    });
});

function applyDynamicModelStandards(stds, modelName) {
    window.activeModelStandards = stds;
    var container = document.getElementById($('#process-txt').val()) || document.getElementById('form-input') || document.body;

    $(container).find('.dyn-number-input').each(function() {
        var el = this;
        var colName   = (el.getAttribute('data-col-name') || '').trim();
        var gridPos   = (el.getAttribute('data-grid-pos') || '').trim();
        var paramName = (el.getAttribute('data-param-name') || '').trim();

        var cfg = null;
        if (stds) {
            var matchedKey = Object.keys(stds).find(function(k) {
                return (colName && (colName === k || colName.startsWith(k) || k.startsWith(colName))) ||
                       (gridPos && (gridPos === k || gridPos.startsWith(k) || k.startsWith(gridPos))) ||
                       (paramName && (paramName === k || paramName.startsWith(k) || k.startsWith(paramName)));
            });
            if (matchedKey) {
                cfg = stds[matchedKey];
            }
        }

        var stdLabelEl = el.parentNode ? el.parentNode.querySelector('.dyn-std-label') : null;

        if (cfg) {
            var isAct = (cfg.active === undefined || parseInt(cfg.active) === 1);
            if (!isAct) {
                el.value = '-';
                el.readOnly = true;
                el.style.backgroundColor = '#e9ecef';
                el.dataset.hasError = "0";
                if (stdLabelEl) {
                    stdLabelEl.className = 'dyn-std-label badge bg-secondary d-block mt-1';
                    stdLabelEl.textContent = (modelName ? modelName + ': ' : '') + 'Tidak Diukur';
                }
            } else {
                el.readOnly = false;
                el.style.backgroundColor = '';
                if (cfg.min !== '' && cfg.min !== null && cfg.min !== undefined) {
                    el.setAttribute('data-min', cfg.min);
                } else {
                    el.removeAttribute('data-min');
                }
                if (cfg.max !== '' && cfg.max !== null && cfg.max !== undefined) {
                    el.setAttribute('data-max', cfg.max);
                } else {
                    el.removeAttribute('data-max');
                }

                var minLbl = (cfg.min !== '' && cfg.min !== null && cfg.min !== undefined) ? cfg.min : '-∞';
                var maxLbl = (cfg.max !== '' && cfg.max !== null && cfg.max !== undefined) ? cfg.max : '∞';
                if (stdLabelEl) {
                    stdLabelEl.className = 'dyn-std-label text-primary fw-semibold d-block mt-1';
                    stdLabelEl.style.fontSize = '10px';
                    stdLabelEl.textContent = 'Std: ' + minLbl + ' ~ ' + maxLbl;
                }
            }
        } else {
            var origMin = el.getAttribute('data-orig-min');
            var origMax = el.getAttribute('data-orig-max');
            if (origMin) el.setAttribute('data-min', origMin); else el.removeAttribute('data-min');
            if (origMax) el.setAttribute('data-max', origMax); else el.removeAttribute('data-max');
            if (stdLabelEl) {
                var defMin = origMin ? origMin : '-∞';
                var defMax = origMax ? origMax : '∞';
                stdLabelEl.className = 'dyn-std-label text-muted d-block mt-1';
                stdLabelEl.style.fontSize = '10px';
                stdLabelEl.textContent = (origMin || origMax) ? ('Std: ' + defMin + ' ~ ' + defMax) : '';
            }
        }

        if (typeof checkInputMinMax === 'function') {
            checkInputMinMax(el, false);
        }
    });
}

function checkSubmit() {
    var machno = $('#machno-txt').val();
    if (!machno || machno === "") {
        alert("GAGAL: Machine Number WAJIB dipilih atau diisi sebelum Submit!");
        return false;
    }

    var docType = $('#type-process-txt').val();
    if(docType === 'production' || docType === 'foregoing') {
        if(!$.trim($('#model-txt').val())) {
            alert("GAGAL: Model Name WAJIB diisi untuk tipe dokumen " + (docType === 'production' ? 'Production' : 'Foregoing') + "!");
            $('#model-txt').focus();
            return false;
        }
        if(!$.trim($('#lotno-txt').val())) {
            alert("GAGAL: Lot Number WAJIB diisi untuk tipe dokumen " + (docType === 'production' ? 'Production' : 'Foregoing') + "!");
            $('#lotno-txt').focus();
            return false;
        }
    }

    if ($('#is-standby').is(':checked')) {
        var $noteInput = findNoteInput();
        if ($noteInput && (!$.trim($noteInput.val()) || $.trim($noteInput.val()) === '-')) {
            alert("GAGAL: Mesin dalam status STAND BY.\nKolom Catatan/Note WAJIB diisi keterangan/alasan standby!");
            $noteInput.focus();
            return false;
        }
        applyStandbyMode(true);
    }

    $('input[id$="_val"]').each(function() {
        var baseId = this.id.replace(/_val$/, '');
        if (typeof combineData === 'function') {
            combineData(baseId);
        }
    });

    $('#form-input input[name="machno-txt"]').remove();
    return confirm('Apakah kamu yakin data Shift Karyawan & Tanggal Input sudah BENAR?');
}

// FUNGSI AUTO-FILL SHIFT & GROUP
function empAuto(empIdTag, shiftTag, groupTag, nameTag, empIdlTag) {
    var empid = $.trim($(empIdTag).val());
    if (!empid) return;

    var warn = $('#' + empIdlTag);

    $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empid })
        .done(function (d) {
            if (d.found) {
                if (d.acc && !$(shiftTag).val()) {
                    var shiftVal = d.acc.toString();
                    if(shiftVal === '1') $(shiftTag).val('A');
                    else if(shiftVal === '2') $(shiftTag).val('B');
                    else if(shiftVal === '3') $(shiftTag).val('C');
                    else $(shiftTag).val(shiftVal);
                }
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
            warn.removeClass('invisible').addClass('visible');
        });
}

var processRows = [];
var TYPE_ORDER = {production: 1, startup: 2, foregoing: 3};
var TYPE_LABEL = {production: 'Production', startup: 'Startup', foregoing: 'Foregoing'};

// "LD Die Bonding 1 Type GH16PSeries" -> base "LD Die Bonding 1", variant "Type GH16PSeries"
function splitName(name) {
    name = $.trim(name || '');
    var i = name.indexOf(' Type ');
    if (i === -1) return { base: name, variant: '' };
    return { base: $.trim(name.substring(0, i)), variant: $.trim(name.substring(i + 1)) };
}

// "SL-D2-002-04" -> "SL-D2-002" (revisi dibuang); docno tanpa revisi dibiarkan
function stripRevision(docno) {
    var p = (docno || '').split('-');
    return p.length >= 4 ? p.slice(0, -1).join('-') : (docno || '');
}

function updateDevice() {
    var device = $('#device-txt').val();
    processRows = [];
    if (device === '-') {
        populateProcessName();
        return;
    }

    $.ajax({
        url: "<?= base_url('process/list') ?>?device=" + encodeURIComponent(device),
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            processRows = data || [];
            populateProcessName();
        },
        error: function(xhr) {
            if (xhr.responseText) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data) { processRows = data; populateProcessName(); return; }
                } catch (e) {}
            }
            processRows = [];
            populateProcessName();
        }
    });
}

// Dropdown Process: nama proses unik (tanpa variasi "Type ...") untuk device terpilih
function populateProcessName() {
    var $sel = $('#process-name-txt').empty();
    var names = [];
    $.each(processRows, function(i, r) {
        var base = splitName(r.process_name || r.name).base;
        if (base && names.indexOf(base) === -1) names.push(base);
    });
    names.sort();

    if (names.length === 0) {
        $sel.append(new Option($('#device-txt').val() === '-' ? 'choose process' : '-- Data Kosong --', '-'));
    } else {
        $sel.append(new Option('choose process', '-'));
        $.each(names, function(i, n) { $sel.append(new Option(n, n)); });
    }
}

function fillDocType() {
    var chosen = $('#process-name-txt').val();
    var $sel = $('#process-txt').empty();
    $sel.append(new Option('choose type document', '-'));

    if (chosen && chosen !== '-') {
        var rows = $.grep(processRows, function(r) {
            return splitName(r.process_name || r.name).base === chosen;
        });
        rows.sort(function(a, b) {
            var ta = TYPE_ORDER[a.type] || 9, tb = TYPE_ORDER[b.type] || 9;
            if (ta !== tb) return ta - tb;
            return String(a.docno).localeCompare(String(b.docno));
        });
        $.each(rows, function(i, r) {
            var variant = splitName(r.process_name || r.name).variant;
            var label = stripRevision(r.docno) + ' (' + (TYPE_LABEL[r.type] || r.type) + ')' + (variant ? ' - ' + variant : '');
            var opt = new Option(label, r.process_code);
            $(opt).attr('data-type', r.type)
                  .attr('data-docno', r.docno)
                  .attr('data-name', r.process_name || r.name);
            $sel.append(opt);
        });
    }
    updateDocNo();
    updateInput();
    updateMachine();
    updateModelList();
    $sel.trigger('change');
}

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

// FUNGSI UPDATE MODEL LIST PER PROCESS (TERHUBUNG KE MASTER MODEL & AUTO-REGISTER)
function updateModelList() {
    var processCode = document.getElementById("process-txt") ? document.getElementById("process-txt").value : '';
    var typeProcess = $('#process-txt option:selected').data('type') || '';
    var modelSelect = $('#model-txt');

    modelSelect.empty().append('<option value="">-- Ketik atau Pilih Model Name --</option>');

    if (!processCode || processCode === '-' || processCode === 'null') {
        modelSelect.val('').trigger('change');
        return;
    }

    $.ajax({
        url: "<?= base_url('model/list') ?>?process=" + encodeURIComponent(processCode) + "&type=" + encodeURIComponent(typeProcess),
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
            if (data && data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    var mName = data[i].model_name;
                    modelSelect.append(new Option(mName, mName, false, false));
                }
            }
            modelSelect.val('').trigger('change');
        },
        error: function(xhr) {
            console.error("Gagal memuat Model List:", xhr.responseText);
            modelSelect.val('').trigger('change');
        }
    });
}
function updateInput() {
    let typeProcess, device, process;
    process = document.getElementById("process-txt").value;

    if (!process || process === 'null' || process === '-') {
        $('#submit').hide();
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
        form_input.innerHTML = "";
        document.getElementById('revisi-txt').value = "";
        
        $('#req-model, #req-lot').addClass('d-none');
        document.getElementById('model-txt').required = false;
        document.getElementById('lotno-txt').required = false;
        return;
    }

    device = process.split("-")[0];
    let revField = document.getElementById('revisi-txt');
    let selectedDocType = $('#type-process-txt').val();

    if (selectedDocType === 'production' || process.split("-")[1] == 'p') {
        document.getElementById('model-txt').disabled = false;
        document.getElementById('model-txt').required = true;
        document.getElementById('lotno-txt').disabled = false;
        document.getElementById('lotno-txt').required = true;
        $('#req-model, #req-lot').removeClass('d-none');
        
        typeProcess="production";
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
    }
    else if(process.split("-")[1] == 'f'){
        document.getElementById('model-txt').disabled = false;
        document.getElementById('model-txt').required = true;
        document.getElementById('lotno-txt').disabled = false;
        document.getElementById('lotno-txt').required = true;
        $('#req-model, #req-lot').removeClass('d-none');
        
        typeProcess="foregoing";
        $('#standby-wrapper').hide();
        $('#is-standby').prop('checked', false);
    }
    else{
        document.getElementById('model-txt').disabled = true;
        document.getElementById('model-txt').required = false;
        document.getElementById('lotno-txt').disabled = true;
        document.getElementById('lotno-txt').required = false;
        $('#req-model, #req-lot').addClass('d-none');
        
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
                $('#form-input').html(res.html);
                $('#submit').show();
                if (revField) {
                    revField.value = res.revisi !== undefined ? res.revisi : '0';
                }
                var machSelect = $('#machno-txt');
                if (machSelect.length && machSelect.val()) {
                    machSelect.trigger('change');
                }
                if ($('#is-standby').is(':checked')) {
                    applyStandbyMode(true);
                }
                if ($('#model-txt').val()) {
                    $('#model-txt').trigger('change');
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

// Fallback ke form statis jika proses belum dikonfigurasi di Form Builder
function loadStaticFallbackForm(typeProcess, device, process) {
    $.ajax({
        url: "<?= rtrim(base_url(), '/') ?>/" + typeProcess + "/form?device=" + encodeURIComponent(device) + "&process=" + encodeURIComponent(process),
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
                var machSelect = $('#machno-txt');
                if (machSelect.length && machSelect.val()) {
                    machSelect.trigger('change');
                }
                if ($('#is-standby').is(':checked')) {
                    applyStandbyMode(true);
                }
                if ($('#model-txt').val()) {
                    $('#model-txt').trigger('change');
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
        url: "<?= base_url('input/note-form') ?>/" + encodeURIComponent(processCode),
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
    // Doc. No tetap menampilkan docno lengkap (dengan revisi) dari baris yang dipilih
    $('#docno').val($('#process-txt option:selected').data('docno') || '-');
}

function findNoteInput() {
    var $explicit = $('#form-input [data-is-note="1"]').first();
    if ($explicit.length) return $explicit;

    var $trs = $('#form-input table tbody tr');
    var $targetTr = $trs.filter(function() {
        var labelText = $(this).children('td:not(:last-child)').text().toLowerCase();
        return labelText.indexOf('note') !== -1 || labelText.indexOf('catatan') !== -1;
    }).last();

    if ($targetTr.length) {
        var $inp = $targetTr.find('input[type="text"], textarea').not('.dyn-auto-judgment');
        if ($inp.length) return $inp;
    }

    var $lastTr = $trs.last();
    var $lastInp = $lastTr.find('input[type="text"], textarea').not('.dyn-auto-judgment');
    if ($lastInp.length) return $lastInp;

    return $('#form-input input[type="text"], #form-input textarea').not('.dyn-auto-judgment').last();
}

function applyStandbyMode(isActive) {
    var $form = $('#form-input');
    if (!$form.length || !$form.children().length) return;

    var $note = findNoteInput();

    if (isActive) {
        $('#standby-badge').fadeIn();
        $('#standby-wrapper').css('background', 'linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%)');

        $form.find('input, select, textarea').each(function() {
            var $el = $(this);

            if ($el.attr('type') === 'hidden') {
                if ($el.attr('name') && $el.attr('name').indexOf('par') === 0) {
                    $el.val('-');
                }
                return;
            }

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

            if ($el.is('input[type="number"], input[type="text"], input[type="time"], input[type="date"], textarea')) {
                if ($el.data('orig-readonly') === undefined) {
                    $el.data('orig-readonly', $el.prop('readonly') ? true : false);
                }
                var curType = ($el.attr('type') || '').toLowerCase();
                if (curType === 'number' || curType === 'time' || curType === 'date') {
                    $el.data('orig-type', curType);
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

        $('#cnt-error-input').val(0);
        if (typeof changeDisableSubmit === 'function') {
            changeDisableSubmit(false);
        }
        $('#submit').prop('disabled', false);

        if ($note && $note.length) {
            $note.focus();
        }
    } else {
        $('#standby-badge').fadeOut();
        $('#standby-wrapper').css('background', 'linear-gradient(135deg, #fffcf0 0%, #fff3cd 100%)');

        $form.find('input, select, textarea').each(function() {
            var $el = $(this);

            if ($el.attr('type') === 'hidden') {
                if ($el.attr('name') && $el.attr('name').indexOf('par') === 0 && $el.val() === '-') {
                    $el.val('');
                }
                return;
            }

            if ($note && $el.is($note)) {
                if ($el.attr('data-is-required') !== '1') {
                    $el.prop('required', false);
                }
                if ($el.val() === 'Mesin Stand By') {
                    $el.val('');
                }
                $el.css({
                    'border': '',
                    'background-color': ''
                });
                return;
            }

            if ($el.val() === '-' && !$el.is('select')) {
                $el.val('');
            }
            if ($el.data('orig-type')) {
                $el.attr('type', $el.data('orig-type'));
                $el.removeData('orig-type');
            }
            var wasOrigRo = ($el.data('orig-readonly') === true || $el.hasClass('dyn-auto-judgment') || $el.attr('data-formula-type'));
            $el.prop('readonly', !!wasOrigRo);
            if (!wasOrigRo && ($el.data('orig-required') || $el.attr('data-is-required') === '1')) {
                $el.prop('required', true);
                $el.removeData('orig-required');
            }
            $el.css({
                'background-color': wasOrigRo ? '#e9ecef' : '',
                'color': ''
            });

            if ($el.is('select')) {
                $el.find('.standby-temp-opt').remove();
                $el.css('pointer-events', '');
                if ($el.val() === '-' && !$el.find('option[value="-"]').length) {
                    $el.val('');
                }
                if ($el.data('orig-required') || $el.attr('data-is-required') === '1') {
                    $el.prop('required', true);
                    $el.removeData('orig-required');
                }
            }
        });

        if (typeof syncErrorCount === 'function') {
            syncErrorCount();
        }
    }
}

$(document).on('change', '#is-standby', function() {
    applyStandbyMode($(this).is(':checked'));
});
</script>
<?= $this->endSection(); ?>