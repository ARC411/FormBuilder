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
    <form name="checksheet" action="<?= base_url('startup/update'); ?>" method="POST">
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