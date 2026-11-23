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
        
        <!-- SAMAKAN ALERT PERINGATAN DENGAN HALAMAN INPUT -->
        <div class="alert alert-danger shadow-sm border-0 mb-4" role="alert" style="border-left: 5px solid #dc3545; background-color: #fff5f5;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                <div>
                    <h6 class="alert-heading font-weight-bold mb-1 text-danger">PERINGATAN PENTING!</h6>
                    <p class="mb-0 text-dark small">Harap pastikan isian <b>Tanggal Input</b> dan pilihan <b>Shift Karyawan (A / B / C)</b> sudah benar sebelum submit. Kesalahan pada data ini dapat menyebabkan form hilang dari jadwal produksi Anda.</p>
                </div>
            </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Date</label>
              <div class="col-sm-6">
<<<<<<< HEAD
                <input type="text" class="form-control-plaintext" id="date" name="date" value="<?= !empty($alldata[0]['created_at']) ? date('Y-m-d', strtotime($alldata[0]['created_at'])) : date('Y-m-d'); ?>" readonly>
=======
                <!-- Lock data tanpa css custom agar natural seperti view input -->
                <input type="text" class="form-control" id="date" name="date" value="<?= !empty($alldata[0]['created_at']) ? date('Y-m-d', strtotime($alldata[0]['created_at'])) : date('Y-m-d'); ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Device</label>
              <div class="col-sm-6">
<<<<<<< HEAD
                <input type="hidden" id="device-txt" name="device-txt" value="<?= esc($alldata[0]["device"] ?? '') ?>">
                <input type="text" class="form-control" value="<?= esc($alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '') ?>" readonly>
=======
                <input type="hidden" id="device-txt" name="device-txt" value="<?php echo $alldata[0]["device"] ?? '' ?>" >
                <input type="text" class="form-control" id="device-txt-name" name="device-txt-name" value="<?php echo $alldata[0]["device_name"] ?? $alldata[0]["device"] ?? '' ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Process</label>
              <div class="col-sm-6">
<<<<<<< HEAD
                <input type="hidden" id="process-txt" name="process-txt" value="<?= esc($alldata[0]["process"] ?? '') ?>">
                <input type="text" class="form-control" value="<?= esc($alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '') ?>" readonly>
=======
                <input type="hidden" id="process-txt" name="process-txt" value="<?php echo $alldata[0]["process"] ?? '' ?>" >
                <input type="text" class="form-control" id="process-txt-name" name="process-txt-name" value="<?php echo $alldata[0]["process_name"] ?? $alldata[0]["name"] ?? $alldata[0]["process"] ?? '' ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Model Name</label>
              <div class="col-sm-6">
<<<<<<< HEAD
                <!-- Select2 Dropdown (Bisa diketik/diubah) -->
                <select class="form-control" id="model-txt" name="model-txt">
                    <option value="<?= esc($alldata[0]["model"] ?? '') ?>" selected><?= esc($alldata[0]["model"] ?? '') ?></option>
                </select>
=======
                <input type="hidden" id="model-txt" name="model-txt" value="<?php echo $alldata[0]["model"] ?? '' ?>" >
                <input type="text" class="form-control" id="model-txt-name" name="model-txt-name" value="<?php echo $alldata[0]["model"] ?? '' ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
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
<<<<<<< HEAD
                <!-- Bebas diedit -->
                <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" value="<?= esc($alldata[0]["lotno"] ?? '') ?>" onkeyup="this.value = this.value.toUpperCase()">
=======
                <!-- Lot & Machine di Complete dibuat readonly karena hanya melanjutkan -->
                <input type="text" class="form-control" id="lotno-txt" name="lotno-txt" value="<?php echo $alldata[0]["lotno"] ?? '' ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
              </div>
            </div>

            <div class="mb-3 row">
              <label class="col-sm-2 col-form-label">Machine Number <span style="color:red;">*</span></label>
              <div class="col-sm-6">
<<<<<<< HEAD
                <select class="form-control" id="machno-txt" name="machno-txt" required>
                    <option value="<?= esc($alldata[0]["machno"] ?? '') ?>" selected><?= esc($alldata[0]["machno"] ?? '') ?></option>
                </select>
=======
                <input type="text" class="form-control" id="machno-txt" name="machno-txt" value="<?php echo $alldata[0]["machno"] ?? '' ?>" readonly>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
              </div>
            </div>

            <div class="mb-3 row">
<<<<<<< HEAD
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
=======
              <?php
                $empid = $session->get('empid');
                $nameSession = $session->get('name') ?? '';
                $posSession  = $session->get('positionid') ?? '';
                if(empty($alldata[0]["empid2"])){
                  // BLOK 1: OPERATOR FINISH BELUM ADA (Kasus Form Biasa)
                  $html = <<< "EOT"
                    <label for="empid-txt" class="col-sm-2 col-form-label">Operator</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div1">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <input type="text" class="form-control" value="$empid" id="empid-txt" name="empid-txt" readonly required>            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" class="form-control" value="$nameSession" id="name-txt" name="name-txt" readonly>
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" value="$posSession" id="position-txt" name="position-txt" readonly>
                        </div>
                        
                        <div class="input-group mb-1 border rounded" style="border-color: #dc3545 !important; border-width: 2px !important;">
                            <span class="input-group-text text-danger font-weight-bold" id="basic-addon4" style="background-color: #fff5f5;">Shift----:</span>
                            <select class="form-control font-weight-bold" id="shift-txt" name="shift-txt" required>
                                <option value="">Pilih Shift (Wajib)</option>
                                <option value="A">A (Pagi)</option>
                                <option value="B">B (Sore)</option>
                                <option value="C">C (Malam)</option>
                            </select>
                        </div>

                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <input type="text" class="form-control" id="group-txt" name="group-txt" placeholder="Contoh: 1S2GW1" onkeyup="this.value = this.value.toUpperCase()">
                        </div>
                        <input type="hidden" name="name-operator" id="name-operator">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Data shift/group untuk ID karyawan di atas tidak ditemukan atau belum lengkap. Silakan isi shift & group di atas secara manual, atau hubungi bagian GA/P jika jadwal Anda tidak sesuai.</span>
                    </div> 
                  EOT;
                  echo $html;
                }
                else{
                  // BLOK 2: KASUS COMPLETED FORM DENGAN OPERATOR FINISH
                  $empid=$alldata[0]['empid'] ?? '';
                  $name=$alldata[0]['name'] ?? '';
                  $shift=$alldata[0]['shift'] ?? '';
                  $group=$alldata[0]['group'] ?? '';
                  $empid2=$alldata[0]['empid2'] ?? '';
                  $name2=$alldata[0]['name2'] ?? '';
                  $shift2=$alldata[0]['shift2'] ?? '';
                  $group2=$alldata[0]['group2'] ?? '';
                  
                  // Operator 1 harus readonly mutlak di blok Complete, sementara Dropdown kita berikan attribut default agar shiftnya terpilih
                  $selA = ($shift === 'A' || $shift == '1') ? 'selected' : '';
                  $selB = ($shift === 'B' || $shift == '2') ? 'selected' : '';
                  $selC = ($shift === 'C' || $shift == '3') ? 'selected' : '';
                  
                  // Sama untuk Operator 2 Dropdown
                  $sel2A = ($shift2 === 'A' || $shift2 == '1') ? 'selected' : '';
                  $sel2B = ($shift2 === 'B' || $shift2 == '2') ? 'selected' : '';
                  $sel2C = ($shift2 === 'C' || $shift2 == '3') ? 'selected' : '';

                  $html = <<< "EOT"
                    <label for="empid-txt" class="col-sm-2 col-form-label">Operator Start</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div1">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <input type="text" value="$empid" class="form-control" id="empid-txt" name="empid-txt" readonly required>            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" value="$name" class="form-control" id="name-txt" name="name-txt" readonly>
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" id="position-txt" name="position-txt" readonly>
                        </div>

                        <div class="input-group mb-1 border rounded" style="border-color: #dc3545 !important; border-width: 2px !important;">
                            <span class="input-group-text text-danger font-weight-bold" id="basic-addon4" style="background-color: #fff5f5;">Shift----:</span>
                            <!-- Dibuat readonly untuk Operator Start saat Edit/Complete -->
                            <select class="form-control font-weight-bold" id="shift-txt" name="shift-txt" required style="pointer-events:none; background-color:#e9ecef;">
                                <option value="A" $selA>A (Pagi)</option>
                                <option value="B" $selB>B (Sore)</option>
                                <option value="C" $selC>C (Malam)</option>
                            </select>
                        </div>

                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <!-- Dibekukan untuk Operator Start -->
                          <input type="text" value="$group" class="form-control" id="group-txt" name="group-txt" readonly>
                        </div>
                        <input type="hidden" name="name-operator" id="name-operator">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl" name="empid-lbl" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Data shift/group untuk ID karyawan di atas tidak ditemukan atau belum lengkap. Silakan isi shift & group di atas secara manual, atau hubungi bagian GA/P jika jadwal Anda tidak sesuai.</span>
                    </div> 

                    <div class="mb-3 row">
                      <label for="empid-txt" class="col-sm-2 col-form-label">Operator Finish</label>
                      <div class="col-sm-6">
                        <div class="input-group mb-1" id="empid-div2">
                          <span class="input-group-text" id="basic-addon1">Emp. ID :</span>
                          <!-- Input EmpID Operator Finish bisa diinput (Autofill trigger onkeyup) -->
                          <input type="number" value="$empid2" class="form-control" id="empid2-txt" name="empid2-txt" onkeyup="empAuto('#empid2-txt','#shift2-txt','#group2-txt','#name2-txt','#position2-txt','empid-lbl2')" onkeydown="return (event.keyCode!=13);" placeholder="Ketik 6 digit EmpID">            
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon2">Name---:</span>
                          <input type="text" value="$name2" class="form-control" id="name2-txt" name="name2-txt" readonly>          
                        </div>
                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon3">Position:</span>
                          <input type="text" class="form-control" id="position2-txt" name="position2-txt" readonly>
                        </div>
                        
                        <div class="input-group mb-1 border rounded" style="border-color: #dc3545 !important; border-width: 2px !important;">
                            <span class="input-group-text text-danger font-weight-bold" id="basic-addon4" style="background-color: #fff5f5;">Shift----:</span>
                            <select class="form-control font-weight-bold" id="shift2-txt" name="shift2-txt" required>
                                <option value="">Pilih Shift (Wajib)</option>
                                <option value="A" $sel2A>A (Pagi)</option>
                                <option value="B" $sel2B>B (Sore)</option>
                                <option value="C" $sel2C>C (Malam)</option>
                            </select>
                        </div>

                        <div class="input-group mb-1">
                          <span class="input-group-text" id="basic-addon5">Group---:</span>
                          <input type="text" value="$group2" class="form-control" id="group2-txt" name="group2-txt" placeholder="Contoh: 1S2GW1" onkeyup="this.value = this.value.toUpperCase()">
                        </div>
                        <input type="hidden" name="name-operator2" id="name-operator2">
                      </div>
                    </div>
          
                    <div class="form-control alert-success invisible" id="empid-lbl2" name="empid-lbl2" >
                      <span style="color:#dc143c;text-align:justify;">Perhatian!<br>Data shift/group untuk ID karyawan di atas tidak ditemukan atau belum lengkap. Silakan isi shift & group di atas secara manual, atau hubungi bagian GA/P jika jadwal Anda tidak sesuai.</span>
                    </div>
                  EOT;
                  echo $html;
                }
              ?>
          </div>
          <?php 
            if (session()->getFlashdata('message')){ 
              echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
            }else{ 
              echo '<p id=msgsuccess class="invisible">Kosong</p>';
            }
          ?>

          <div id="modal" class=""></div>
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
        </div>

        <div class="w-100 text-center d-flex justify-content-center my-4">
          <button type="submit" class="btn btn-lg btn-primary mx-auto" id="submit" style="min-width: 140px;" onclick="return confirm('Simpan perubahan?');">Submit</button>
        </div>
      </div>
    </div>
  </div>
</form>

<<<<<<< HEAD
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
=======
<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.css'); ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/global.css'); ?>">
<script src="<?= base_url('assets/js/jquery-3.7.1.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jquery-ui.js'); ?>"></script>
<script src="<?= base_url('assets/js/edit.js?v=' . time()); ?>"></script>
<script src="<?= base_url('assets/js/form/test.js'); ?>"></script>
<script>
$(document).ready(function() {
    $('#submit').show().css({'display': 'block', 'margin': '0 auto'});
    $(document).ajaxComplete(function() {
        $('#submit').show().css({'display': 'block', 'margin': '0 auto'});
    });

    var empStart = $('#empid-txt').val();
    if(empStart && empStart !== '') {
        $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empStart })
            .done(function (d) {
                if (d.found) {
                    if (d.name && (!$("#name-txt").val() || $("#name-txt").val() === '-')) $("#name-txt").val(d.name);
                    if (d.positionname && (!$("#position-txt").val() || $("#position-txt").val() === '-')) $("#position-txt").val(d.positionname);
                    if (d.acc && !$("#shift-txt").val()) {
                        var shiftVal = d.acc.toString();
                        if(shiftVal === '1') $("#shift-txt").val('A');
                        else if(shiftVal === '2') $("#shift-txt").val('B');
                        else if(shiftVal === '3') $("#shift-txt").val('C');
                        else $("#shift-txt").val(shiftVal);
                    }
                    if (d.groupid && !$("#group-txt").val()) $("#group-txt").val(d.groupid);
                }
            });
    }

    var empFinish = $('#empid2-txt').val();
    if(empFinish && empFinish !== '') {
        $.getJSON("<?= base_url('home/ajaxAutofill') ?>", { 'empid-txt': empFinish })
            .done(function (d) {
                if (d.found) {
                    if (d.name && (!$("#name2-txt").val() || $("#name2-txt").val() === '-')) $("#name2-txt").val(d.name);
                    if (d.positionname && (!$("#position2-txt").val() || $("#position2-txt").val() === '-')) $("#position2-txt").val(d.positionname);
                }
            });
    }
});

// Fungsi autofill yang sinkron dengan input.php untuk Operator Finish
function empAuto(empIdTag, shiftTag, groupTag, nameTag, positionTag, empIdlTag) {
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
                if (d.name) $(nameTag).val(d.name);
                if (d.positionname || d.position) $(positionTag).val(d.positionname || d.position);
                if (d.acc && d.groupid) warn.removeClass('visible').addClass('invisible');
                else warn.removeClass('invisible').addClass('visible');
            } else {
                warn.removeClass('invisible').addClass('visible');
            }
        })
        .fail(function () {
            warn.removeClass('invisible').addClass('visible');
        });
>>>>>>> 7a8ea7c7ca4e9a10c73b8a481160381304f54df2
}
</script>
</main>
<?= $this->endSection(); ?>