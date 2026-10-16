<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>

  <?= $this->include('layout/navbar'); ?>

  <link rel="stylesheet" href="<?= base_url('assets/css/daterangepicker.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    @media print {
      .card-header, #menu, .btn, nav, header, footer { display: none !important; }
      .card { border: none !important; box-shadow: none !important; }
      .card-body, .container-fluid { padding: 0 !important; margin: 0 !important; }
    }
  </style>

  <div class="container-fluid">

    <div class="card shadow mb-5">

      <div class="card-header py-3">
        <h3 class="m-0 font-weight-bold">History Checksheet</h3>
      </div>

      <div class="card-body">
        <div class="container-fluid collapse show" id="menu">

          <div class="mb-3 row">
            <label for="date" class="col-sm-1 col-form-label">Date Range</label>
            <div class="col-sm-3">
              <div id="daterange" class="form-control" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
              </div>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="device" class="col-sm-1 col-form-label">Device</label>
            <div class="col-sm-3">
              <select class="form-control" id="device" name="device" onchange="changeDevice()" required>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="type-process" class="col-sm-1 col-form-label">Doc. Type</label>
            <div class="col-sm-3">
              <select class="form-control" id="type-process" name="type-process" onchange="changeDevice()" required>
                <option class="dropdown-item" value="">choose type document</option>
                <option value="production">Production Control Sheet</option>
                <option value="startup">Startup Control Sheet</option>
                <option value="foregoing">Foregoing Control Sheet</option>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="process" class="col-sm-1 col-form-label">Process</label>
            <div class="col-sm-3">
              <select class="form-control" id="process">
              </select>
            </div>
          </div>

          <!-- DROPDOWN VERSI REVISI DINAMIS -->
          <div class="mb-3 row">
            <label for="revisiSelect" class="col-sm-1 col-form-label fw-bold text-primary">Revisi</label>
            <div class="col-sm-3">
              <select class="form-control border-primary" id="revisiSelect" name="revisi">
                <option value="">-- Pilih Process Dulu --</option>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="ModelName" class="col-sm-1 col-form-label">Model Name</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="ModelName">
            </div>
          </div>

          <div class="mb-3 row">
            <label for="lotNo" class="col-sm-1 col-form-label">Lot Number</label>
            <div class="col-sm-3">
              <input type="text" class="form-control" id="lotNo">
            </div>
          </div>

          <!-- Select2 Dropdown -->
          <div class="mb-3 row">
            <label for="machno" class="col-sm-1 col-form-label">Machine Number</label>
            <div class="col-sm-3">
              <select class="form-control" id="machno" name="machno">
                <option value="">-- Semua Mesin (Tidak Dipilih) --</option>
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="docno" class="col-sm-1 col-form-label">Doc. No</label>
            <div class="col-sm-6">
              <input type="text" class="form-control-plaintext fw-bold" id="docno" value="-" readonly>
            </div>
          </div>
        </div>

        <div class="container-fluid">
          <button class="btn btn-secondary" id="menu-show" type="button" data-toggle="collapse" data-target="#menu" aria-expanded="false" aria-controls="menu">
            <i class="fa-solid fa-square-caret-down"></i>
            Menu Display ON/OFF
          </button>
          <button class="btn btn-success" onclick="download_table_as_excel();">
            <i class="fa-solid fa-file-arrow-down"></i>
            Download Table as excel file
          </button>
          <button type="button" class="btn btn-danger" onclick="download_table_as_pdf();" style="margin-left: 5px;">
            <i class="fas fa-file-pdf"></i> Download as PDF
          </button>
          <button class="btn btn-primary" onclick="copy_clipboard();" style="margin-left: 5px;">
            <i class="fa-solid fa-copy"></i>
            Copy to clipboard
          </button>

          <div id="table" class="table-responsive mt-3"></div>
        </div>

      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/tableToExcel.js') ?>"></script>
  <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/moment.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/daterangepicker.min.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script>
    var currentTableXhr = null;
    var updateTimer = null;

    $(document).ready(function() {$('#machno').select2({
        width: '100%'
      });

      var start = moment().subtract(29, 'days');
      var end = moment();

      function cb(start, end) {
        $('#daterange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        updateTable();
      }

      $('#daterange').daterangepicker({
        showDropdowns: true,
        minYear: 2020,
        maxYear: parseInt(moment().format('YYYY'), 10) + 1,
        startDate: start,
        endDate: end,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, cb);

      cb(start, end);

      $('#fldb1, #slcs, #submit').hide();

      $('#ModelName, #lotNo, #machno, #revisiSelect').change(function() {
        updateDocNo();
        updateTable();
      });

      $('#process').change(function() {
        updateRevisiList();
        updateMachine();
      });
    });

    // Tarik daftar versi Revisi setiap kali Process berganti
    function updateRevisiList() {
      var processCode = document.getElementById("process").value;
      var docType     = document.getElementById("type-process").value;
      var revSelect   = $('#revisiSelect');

      revSelect.empty();
      if (!processCode || processCode === '' || processCode === '-') {
        revSelect.append('<option value="">-- Pilih Process Dulu --</option>');
        return;
      }

      $.ajax({
        url: "<?= base_url('history/revisions') ?>",
        type: "GET",
        data: { process: processCode, type: docType },
        dataType: "JSON",
        success: function(data) {
          revSelect.empty();
          if (data && data.length > 0) {
            for (let i = 0; i < data.length; i++) {
              let revNum = data[i].revisi;
              let revPad = String(revNum).padStart(2, '0');
              let label  = (i === 0) ? `Revisi ${revPad} (Terbaru)` : `Revisi ${revPad}`;
              revSelect.append(new Option(label, revNum, i === 0, i === 0));
            }
            revSelect.append(new Option("-- Semua Revisi (Gabungan) --", "ALL", false, false));
          } else {
            revSelect.append(new Option("Revisi Default", "", true, true));
          }
          updateDocNo();
          updateTable();
        },
        error: function() {
          revSelect.append(new Option("Revisi Default", "", true, true));
          updateDocNo();
          updateTable();
        }
      });
    }

    function updateMachine() {
      var processCode = document.getElementById("process").value;
      var machineSelect = $('#machno');

      // Kosongkan dropdown dan pasang opsi default di urutan paling atas
      machineSelect.empty().append(new Option("-- Semua Mesin (Tidak Dipilih) --", "", true, true));

      if (!processCode || processCode === '' || processCode === '-' || processCode === 'null') {
        machineSelect.trigger('change');
        return;
      }

      // Kirim processCode langsung karena MachineModel::getMachineByProcess() sudah menangani mapping prosesnya
      $.ajax({
        url: "<?php echo base_url(); ?>machine/list?process=" + processCode,
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
          if (data && data.length > 0) {
            for (let i = 0; i < data.length; i++) {
              machineSelect.append(new Option(data[i].machine_name, data[i].machine_name, false, false));
            }
          }
          machineSelect.trigger('change');
        },
        error: function(xhr) {
          // Fallback jika response terbungkus warning/error 500 namun berisi JSON valid
          if (xhr.responseText) {
            try {
              var data = JSON.parse(xhr.responseText);
              if (data && data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                  machineSelect.append(new Option(data[i].machine_name, data[i].machine_name, false, false));
                }
              }
            } catch (e) {}
          }
          machineSelect.trigger('change');
        }
      });
    }

    function updateTable() {
      clearTimeout(updateTimer);
      updateTimer = setTimeout(function() { doUpdateTable(0); }, 400);
    }

    function doUpdateTable(retryCount) {
      retryCount = retryCount || 0;

      var checkProcess = document.getElementById("process").value;
      if (!checkProcess || checkProcess === "") {
        return;
      }

      let resDate = convertDate();
      let process = document.getElementById("process").value;
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if(!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = document.getElementById("type-process").value;

      if (process != null && process !== "") {
        device = process.split("-")[0];
      }

      if (!typeProcess || typeProcess === "") {
        return;
      }

      var seg = (process.split("-")[1] || "").toLowerCase();
      var expected = { production: 'p', startup: 's', foregoing: 'f' }[typeProcess];
      if (seg.charAt(0) !== expected) {
        return;
      }

      if (currentTableXhr) {
        currentTableXhr.abort();
      }

      const xhr = new XMLHttpRequest();
      currentTableXhr = xhr;
      let url = "<?= base_url('history/data') ?>?typeProcess=" + encodeURIComponent(typeProcess)
              + "&dateStart=" + encodeURIComponent(resDate.dateStart + " 00:00:00")
              + "&dateEnd=" + encodeURIComponent(resDate.dateEnd + " 23:59:59")
              + "&process=" + encodeURIComponent(process)
              + "&model=" + encodeURIComponent(model)
              + "&lotno=" + encodeURIComponent(lotNo)
              + "&machno=" + machno
              + "&device=" + encodeURIComponent(device)
              + "&revisi=" + encodeURIComponent(revisi);

      xhr.open("GET", url, true);
      xhr.onload = (e) => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          $('#submit').show();
          // Bersihkan script debugbar CI4 jika ada agar halaman tetap ringan
          let cleanHtml = xhr.responseText
              .replace(/<script\b[^>]*src=[^>]*debugbar[^>]*><\/script>/gi, '')
              .replace(/<script\b[^>]*id="debugbar_loader"[\s\S]*?<\/script>/gi, '');
          document.getElementById('table').innerHTML = cleanHtml;
        } else if (xhr.readyState === 4) {
          if (retryCount < 1) {
            setTimeout(function() { doUpdateTable(retryCount + 1); }, 800);
            return;
          }
          $('#submit').show();
          document.getElementById('table').innerHTML = "<div class='alert alert-danger'>Gagal menarik data dari server.</div>";
        }
      };
      xhr.onerror = () => {
        if (retryCount < 1) {
          setTimeout(function() { doUpdateTable(retryCount + 1); }, 800);
          return;
        }
        document.getElementById('table').innerHTML = "<div class='alert alert-danger'>Gagal menarik data dari server.</div>";
      };
      xhr.send(null);
    }

    function updateDocNo() {
      var docNo = document.getElementById("docno");
      var processValue = document.getElementById("process");
      var selectedRev = $('#revisiSelect').val();

      if (processValue.selectedIndex >= 0 && processValue.options[processValue.selectedIndex]) {
        var text = processValue.options[processValue.selectedIndex].text;
        var baseDoc = text.split(" ")[0];
        if (selectedRev && selectedRev !== '' && selectedRev !== 'ALL') {
          var parts = baseDoc.split('-');
          if (parts.length >= 4) {
            parts[parts.length - 1] = String(selectedRev).padStart(2, '0');
            baseDoc = parts.join('-');
          }
        }
        docNo.value = baseDoc;
      }
    }

    function convertDate() {
      const date = $('#daterange span').html().split(" - ");
      var map = { "January": 1, "February": 2, "March": 3, "April": 4, "May": 5, "June": 6, "July": 7, "August": 8, "September": 9, "October": 10, "November": 11, "December": 12 };
      const date0 = date[0]; const date1 = date[1];
      const splitDate0 = date0.split(" "); const splitDate1 = date1.split(" ");
      const dateStart = splitDate0[2] + "-" + map[splitDate0[1]] + "-" + splitDate0[0];
      const dateEnd = splitDate1[2] + "-" + map[splitDate1[1]] + "-" + splitDate1[0];
      return { dateStart, dateEnd };
    }

    function changeDevice() {
      var docType = document.getElementById('type-process').value;
      var device = document.getElementById("device").value;

      if (!docType || docType === "" || !device || device === "") {
          $('#process').empty().append('<option value="">-- Pilih Device & Doc Type --</option>');
          $('#revisiSelect').empty().append('<option value="">-- Pilih Process Dulu --</option>');
          updateMachine();
          return;
      }

      $('#process').empty();

      $.ajax({
        url: "<?php echo base_url(); ?>process/list?device=" + device + "&type=" + docType,
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
          var processValue = document.getElementById("process");
          processValue.innerHTML = "";
          for (let i = 0; i < data.length; i++) {
            var option = document.createElement("option");
            option.text = data[i].name;
            option.value = data[i].process_code;
            processValue.add(option);
          }
          updateRevisiList();
          updateMachine();
        }
      });
    }

    function copy_clipboard() {
      var table = document.getElementById(document.getElementById("process").value) || document.getElementById("table1");
      var range, selection;
      if (document.createRange && window.getSelection) {
        range = document.createRange(); selection = window.getSelection(); selection.removeAllRanges();
        try { range.selectNodeContents(table); selection.addRange(range); } catch (e) { range.selectNode(table); selection.addRange(range); }
        document.execCommand('copy'); selection.removeAllRanges(); alert('Table copied to clipboard.');
      } else {
        range = document.body.createTextRange(); range.moveToElementText(table); range.select(); range.execCommand('copy'); alert('Table copied to clipboard.');
      }
    }

    function download_table_as_excel() {
      let resDate = convertDate();
      let d1 = moment(resDate.dateStart);
      let d2 = moment(resDate.dateEnd);
      if (d2.diff(d1, 'days') > 62) {
        alert("Maaf, Export Data maksimal 2 bulan untuk menjaga stabilitas server!");
        return;
      }

      let process = document.getElementById("process").value;
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if (!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = document.getElementById("type-process").value;

      if (process != null && process !== "") {
        device = process.split("-")[0];
      }

      let exportUrl = "<?= base_url('history/exportExcel') ?>?typeProcess=" + typeProcess
                    + "&dateStart=" + resDate.dateStart
                    + "&dateEnd=" + resDate.dateEnd
                    + "&process=" + process
                    + "&model=" + encodeURIComponent(model)
                    + "&lotno=" + encodeURIComponent(lotNo)
                    + "&machno=" + machno
                    + "&device=" + device
                    + "&revisi=" + encodeURIComponent(revisi);
      window.open(exportUrl, '_blank');
    }

    function download_table_as_pdf() {
      let resDate = convertDate();
      let d1 = moment(resDate.dateStart);
      let d2 = moment(resDate.dateEnd);
      if (d2.diff(d1, 'days') > 62) {
          alert("Maaf, Export Data maksimal 2 bulan untuk menjaga stabilitas server!");
          return;
      }

      let process = document.getElementById("process").value;
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if(!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = document.getElementById("type-process").value;

      if (process != null && process !== "") {
        device = process.split("-")[0];
      }

      let exportUrl = "<?= base_url('history/exportPDF') ?>?typeProcess=" + typeProcess
                    + "&dateStart=" + resDate.dateStart
                    + "&dateEnd=" + resDate.dateEnd
                    + "&process=" + process
                    + "&model=" + encodeURIComponent(model)
                    + "&lotno=" + encodeURIComponent(lotNo)
                    + "&machno=" + machno
                    + "&device=" + device
                    + "&revisi=" + encodeURIComponent(revisi);
      window.open(exportUrl, '_blank');
    }

    document.getElementById("menu-show").addEventListener("click", function() {
      if (document.getElementById("menu").classList.contains('show')) {
        document.getElementById("menu").classList.remove('show');
      } else {
        document.getElementById("menu").classList.add('show');
      }
    });
  </script>
  <script src="<?= base_url('assets/js/dataTables.js') ?>"></script>
  <script src="<?= base_url('assets/js/dataTables.bootstrap4.js') ?>"></script>

  <script>
    $(document).ready(function() {$.ajax({
        url: "<?php echo base_url(); ?>device/list",
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
          var deviceValue = $('#device');
          deviceValue.empty();

          if(data && data.length > 0) {
              for (let i = 0; i < data.length; i++) {
                deviceValue.append(new Option(data[i].name, data[i].code));
              }
          } else {
              deviceValue.append(new Option("-- Device Kosong --", ""));
          }
          changeDevice();
        },
        error: function(xhr) {
          if (xhr.responseText) {
            try {
              var data = JSON.parse(xhr.responseText);
              if(data && data.length > 0) {
                var deviceValue = $('#device');
                deviceValue.empty();
                for (let i = 0; i < data.length; i++) {
                  deviceValue.append(new Option(data[i].name, data[i].code));
                }
                changeDevice();
                return;
              }
            } catch(e) {}
          }
          console.log("Gagal memuat Device: ", xhr.responseText);
        }
      });
    });
  </script>
</main>
<?= $this->endSection(); ?>