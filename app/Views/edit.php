<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
<!-- CSS Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.js') ?>"></script>
<script src="<?= base_url('assets/js/form/test.js') ?>"></script>
<!-- JS Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        <h3 class="m-0 font-weight-bold">Input Checksheet Complete (Production)</h3>
      </div>

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
                <input type="hidden" id="device-txt" name="device-txt" value="<?= esc($alldata[0]["device"] ?? '') ?>">
                <input type="text" class="form-control" value="<?= esc($alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Process</label>
              <div class="col-sm-6">
                <input type="hidden" id="process-txt" name="process-txt" value="<?= esc($alldata[0]["process"] ?? '') ?>">
                <input type="text" class="form-control" value="<?= esc($alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Model Name</label>
              <div class="col-sm-6">
                <!-- Select2 Dropdown (Bisa diketik/diubah) -->
                <select class="form-control" id="model-txt" name="model-txt">
                    <option value="<?= esc($alldata[0]["model"] ?? '') ?>" selected><?= esc($alldata[0]["model"] ?? '') ?></option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Doc. No</label>
              <div class="col-sm-6">
                <input type="text" class="form-control-plaintext" id="docno" name="docno" value="<?= esc($alldata[0]["docno"] ?? '-') ?>" readonly>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Lot Number</label>
              <div class="col-sm-6">
                <!-- Bebas diedit -->
                <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" value="<?= esc($alldata[0]["lotno"] ?? '') ?>" onkeyup="this.value = this.value.toUpperCase()">
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Machine Number <span style="color:red;">*</span></label>
              <div class="col-sm-6">
                <select class="form-control" id="machno-txt" name="machno-txt" required>
                    <option value="<?= esc($alldata[0]["machno"] ?? '') ?>" selected><?= esc($alldata[0]["machno"] ?? '') ?></option>
                </select>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Revisi</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" id="revisi-txt" name="revisi-txt" value="<?= esc($alldata[0]["revisi"] ?? '0') ?>" readonly>
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Operator Start</label>
              <div class="col-sm-6">
                <div class="input-group mb-1">
                  <span class="input-group-text">Emp. ID :</span>
                  <!-- Readonly -->
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['empid'] ?? '') ?>" id="empid-txt" name="empid-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Name---:</span>
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['name'] ?? '') ?>" id="name-txt" name="name-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Position:</span>
                  <input type="text" class="form-control" value="Operator" id="position-txt" name="position-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Shift----:</span>
                  <input type="number" class="form-control" value="<?= esc($alldata[0]['shift'] ?? '') ?>" id="shift-txt" name="shift-txt">
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Group---:</span>
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['group'] ?? '') ?>" id="group-txt" name="group-txt" onkeyup="this.value = this.value.toUpperCase()">
                </div>
              </div>
            </div>

            <?php if (empty($alldata[0]["empid2"])): ?>
            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Operator Finish</label>
              <div class="col-sm-6">
                <div class="input-group mb-1">
                  <span class="input-group-text">Emp. ID :</span>
                  <input type="text" class="form-control" value="<?= esc($sessEmp) ?>" id="empid2-txt" name="empid2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Name---:</span>
                  <input type="text" class="form-control" value="<?= esc($sessName) ?>" id="name2-txt" name="name2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Position:</span>
                  <input type="text" class="form-control" value="<?= esc($sessPos) ?>" id="position2-txt" name="position2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Shift----:</span>
                  <input type="number" class="form-control" value="<?= esc($sessShift) ?>" id="shift2-txt" name="shift2-txt" placeholder="Ketik shift Anda">
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Group---:</span>
                  <input type="text" class="form-control" value="<?= esc($sessGroup) ?>" id="group2-txt" name="group2-txt" placeholder="Ketik group Anda" onkeyup="this.value = this.value.toUpperCase()">
                </div>
              </div>
            </div>
            
            <?php else: ?>
            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Operator Finish</label>
              <div class="col-sm-6">
                <div class="input-group mb-1">
                  <span class="input-group-text">Emp. ID :</span>
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['empid2'] ?? '') ?>" id="empid2-txt" name="empid2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Name---:</span>
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['name2'] ?? '') ?>" id="name2-txt" name="name2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Position:</span>
                  <input type="text" class="form-control" value="Operator" id="position2-txt" name="position2-txt" readonly>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Shift----:</span>
                  <input type="number" class="form-control" value="<?= esc($alldata[0]['shift2'] ?? '') ?>" id="shift2-txt" name="shift2-txt">
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">Group---:</span>
                  <input type="text" class="form-control" value="<?= esc($alldata[0]['group2'] ?? '') ?>" id="group2-txt" name="group2-txt" onkeyup="this.value = this.value.toUpperCase()">
                </div>
              </div>
            </div>
            <?php endif; ?>

          </div>
          <div id="modal" class="mt-3"></div>
        </div>

        <div class="w-100 text-center d-flex justify-content-center my-4">
          <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" style="min-width: 140px;" onclick="return confirm('Simpan perubahan?');">Submit</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
$(document).ready(function() {
    $('#model-txt').select2({ tags: true, width: '100%' });
    $('#machno-txt').select2({ width: '100%' });
    updateMachineList();

    var num = $('#number-edit').val();
    if (num) {
        $.ajax({
            url: "<?= base_url('production/edit/form'); ?>",
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
        }
        if (curVal && mSel.find("option[value='" + curVal + "']").length === 0) {
            mSel.append(new Option(curVal, curVal, true, true));
        }
    });
}
</script>
</main>
<?= $this->endSection(); ?>