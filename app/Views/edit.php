<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?php
  $session   = \Config\Services::session();
  $sessEmp   = (string)($session->get('empid') ?? '');
  $sessName  = (string)($session->get('name') ?? '');
  $sessPos   = (string)($session->get('positionid') ?? 'Operator');
  $sessShift = (string)($session->get('shift') ?? '');
  $sessGroup = (string)($session->get('groupid') ?? '');
?>

<?= $this->include('layout/navbar'); ?>
<form name="checksheet" action="<?= base_url('production') ?>" method="POST">
  <?= csrf_field(); ?>
  <input type="hidden" id="process-form" name="process-form" value="<?= esc($alldata[0]["process"] ?? '') ?>">
  <input type="hidden" id="number-form" name="number-form" value="<?= esc($alldata[0]["number"] ?? '') ?>">
  <input type="hidden" id="number-edit" name="number-edit" value="<?= esc($alldata[0]["number"] ?? '') ?>">

  <div class="container-fluid">
    <div class="card shadow mb-5">
      <div class="card-header py-3">
        <h3 class="m-0 font-weight-bold">Input Checksheet Complete</h3>
      </div>

      <div class="card-body">
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
                <input type="hidden" class="form-control" id="device-txt" name="device-txt" value="<?= esc($alldata[0]["device"] ?? '') ?>">
                <input type="text" class="form-control" id="device-txt-name" name="device-txt-name" value="<?= esc($alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="process-txt" class="col-sm-2 col-form-label">Process</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" id="process-txt" name="process-txt" value="<?= esc($alldata[0]["process"] ?? '') ?>">
                <input type="text" class="form-control" id="process-txt-name" name="process-txt-name" value="<?= esc($alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="model-txt" class="col-sm-2 col-form-label">Model Name</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" id="model-txt" name="model-txt" value="<?= esc($alldata[0]["model"] ?? '') ?>">
                <input type="text" class="form-control" id="model-txt-name" name="model-txt-name" oninput="document.getElementById('model-txt').value = this.value.toUpperCase(); this.value = this.value.toUpperCase();" value="<?= esc($alldata[0]["model"] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3 row">
              <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="docno" name="docno" value="<?= esc($alldata[0]["docno"] ?? '-') ?>" readonly>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3 row">
              <label for="lotno-txt" class="col-sm-2 col-form-label">Lot Number</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" onkeyup="this.value = this.value.toUpperCase()" value="<?= esc($alldata[0]["lotno"] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3 row">
              <label for="machno-txt" class="col-sm-2 col-form-label">Machine Number</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="machno-txt" name="machno-txt" onkeyup="this.value = this.value.toUpperCase()" value="<?= esc($alldata[0]["machno"] ?? '') ?>">
              </div>
            </div>

            <div class="mb-3 row">
              <label for="revisi-txt" class="col-sm-2 col-form-label">Revisi</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="revisi-txt" name="revisi-txt" value="<?= esc($alldata[0]["revisi"] ?? '0') ?>" readonly>
              </div>
            </div>

            <?php if (empty($alldata[0]["empid2"])): ?>
              <div class="mb-3 row">
                <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
                <div class="col-sm-6">
                  <div class="input-group mb-1" id="empid-div1">
                    <span class="input-group-text">Emp. ID :</span>
                    <input type="text" class="form-control" value="<?= esc($sessEmp ?: ($alldata[0]['empid'] ?? '')) ?>" id="empid-txt" name="empid-txt" onkeyup="empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','#position-txt','empid-lbl')" onkeydown="return (event.keyCode!=13);" required>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Name---:</span>
                    <input type="text" class="form-control" value="<?= esc($sessName ?: ($alldata[0]['name'] ?? '')) ?>" id="name-txt" name="name-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Position:</span>
                    <input type="text" class="form-control" value="<?= esc($sessPos) ?>" id="position-txt" name="position-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Shift----:</span>
                    <input type="number" class="form-control" value="<?= esc($sessShift ?: ($alldata[0]['shift'] ?? '')) ?>" id="shift-txt" name="shift-txt" placeholder="1 atau 2 atau 3">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Group---:</span>
                    <input type="text" class="form-control" value="<?= esc($sessGroup ?: ($alldata[0]['group'] ?? '')) ?>" id="group-txt" name="group-txt" placeholder="Contoh: 1S2GW1" onkeyup="this.value = this.value.toUpperCase()">
                  </div>
                  <input type="hidden" name="name-operator" id="name-operator" value="<?= esc($sessName ?: ($alldata[0]['name'] ?? '')) ?>">
                </div>
              </div>
              <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl">
                <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Silakan periksa atau lengkapi shift & group di atas secara manual jika diperlukan.</span>
              </div>
            <?php else: ?>
              <div class="mb-3 row">
                <label for="empid-txt" class="col-sm-2 col-form-label">Operator Start</label>
                <div class="col-sm-6">
                  <div class="input-group mb-1">
                    <span class="input-group-text">Emp. ID :</span>
                    <input type="text" value="<?= esc($alldata[0]['empid'] ?? '') ?>" class="form-control" id="empid-txt" name="empid-txt" onkeyup="empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','#position-txt','empid-lbl')" onkeydown="return (event.keyCode!=13);" required>
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Name---:</span>
                    <input type="text" value="<?= esc($alldata[0]['name'] ?? '') ?>" class="form-control" id="name-txt" name="name-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Position:</span>
                    <input type="text" value="<?= esc($sessPos) ?>" class="form-control" id="position-txt" name="position-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Shift----:</span>
                    <input type="number" value="<?= esc($alldata[0]['shift'] ?? '') ?>" class="form-control" id="shift-txt" name="shift-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Group---:</span>
                    <input type="text" value="<?= esc($alldata[0]['group'] ?? '') ?>" class="form-control" id="group-txt" name="group-txt">
                  </div>
                </div>
              </div>
              <div class="form-control alert-success invisible" id="empid-lbl"></div>

              <div class="mb-3 row">
                <label for="empid2-txt" class="col-sm-2 col-form-label">Operator Finish</label>
                <div class="col-sm-6">
                  <div class="input-group mb-1">
                    <span class="input-group-text">Emp. ID :</span>
                    <input type="text" value="<?= esc($alldata[0]['empid2'] ?? '') ?>" class="form-control" id="empid2-txt" name="empid2-txt" onkeyup="empAuto('#empid2-txt','#shift2-txt','#group2-txt','#name2-txt','#position2-txt','empid-lbl2')" onkeydown="return (event.keyCode!=13);">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Name---:</span>
                    <input type="text" value="<?= esc($alldata[0]['name2'] ?? '') ?>" class="form-control" id="name2-txt" name="name2-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Position:</span>
                    <input type="text" value="<?= esc($sessPos) ?>" class="form-control" id="position2-txt" name="position2-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Shift----:</span>
                    <input type="number" value="<?= esc($alldata[0]['shift2'] ?? '') ?>" class="form-control" id="shift2-txt" name="shift2-txt">
                  </div>
                  <div class="input-group mb-1">
                    <span class="input-group-text">Group---:</span>
                    <input type="text" value="<?= esc($alldata[0]['group2'] ?? '') ?>" class="form-control" id="group2-txt" name="group2-txt">
                  </div>
                </div>
              </div>
              <div class="form-control alert-success invisible" id="empid-lbl2"></div>
            <?php endif; ?>
          </div>

          <div id="modal" class="mt-3"></div>
        </div>

        <div class="w-100 text-center d-flex justify-content-center my-4">
          <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" style="min-width: 140px;" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
        </div>
      </div>
    </div>
  </div>
</form>

<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/global.css'); ?>">
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.js'); ?>"></script>
<script src="<?= base_url('assets/js/form/test.js'); ?>"></script>
<script>
function empAuto(empIdTag, shiftTag, groupTag, nameTag, posTag, empIdlTag) {
    var empid = $.trim($(empIdTag).val());
    if (!empid) return;

    $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empid })
        .done(function(d) {
            if (d && d.found) {
                if (d.acc) $(shiftTag).val(d.acc);
                if (d.groupid) $(groupTag).val(d.groupid);
                if (d.name) $(nameTag).val(d.name);
                if (posTag && d.positionid) $(posTag).val(d.positionid);
            }
        });
}

$(document).ready(function() {
    var num = $('#number-form').val();
    if (num) {
        $.get("<?= base_url('production/edit/form') ?>", { number: num }, function(html) {
            $('#modal').html(html);
            $('#submit').show();
        });
    }
    if (!$('#name-txt').val() || !$('#shift-txt').val()) {
        empAuto('#empid-txt', '#shift-txt', '#group-txt', '#name-txt', '#position-txt', 'empid-lbl');
    }
});
</script>
</main>
<?= $this->endSection(); ?>