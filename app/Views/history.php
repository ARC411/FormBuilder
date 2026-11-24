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
            <label for="process-name" class="col-sm-1 col-form-label">Process</label>
            <div class="col-sm-3">
<select class="form-control" id="process-name" onchange="fillDocType()">
              </select>
            </div>
          </div>

          <div class="mb-3 row">
            <label for="process" class="col-sm-1 col-form-label">Doc. Type</label>
            <div class="col-sm-3">
              <!-- id="process" tetap berisi process_code, jadi updateMachine/export/clipboard tidak berubah -->
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

          <!-- Select2 Dropdown Model Name -->
          <div class="mb-3 row">
            <label for="ModelName" class="col-sm-1 col-form-label">Model Name</label>
            <div class="col-sm-3">
              <select class="form-control" id="ModelName" name="ModelName">
                <option value="">-- Semua Model (Tidak Dipilih) --</option>
              </select>
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

    $(document).ready(function() {$('#machno, #ModelName').select2({
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

      // perubahan Process/Doc. Type ditangani fillDocType() lalu memicu change pada #process
      $('#process').change(function() {
        updateRevisiList();
        updateMachine();
        updateModel();
      });

      $('#ModelName, #lotNo, #machno, #revisiSelect').change(function() {
        updateDocNo();
        updateTable();
      });
    });

    // Tarik daftar versi Revisi setiap kali Process berganti
    function updateRevisiList() {
      var processEl   = document.getElementById("process");
      var processCode = processEl ? processEl.value : '';
      var docType     = getDocType();
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
      var processEl   = document.getElementById("process");
      var processCode = processEl ? processEl.value : '';
      var machineSelect = $('#machno');

      // Kosongkan dropdown dan pasang opsi default di urutan paling atas
      machineSelect.empty().append(new Option("-- Semua Mesin (Tidak Dipilih) --", "", true, true));

      if (!processCode || processCode === '' || processCode === '-' || processCode === 'null') {
        machineSelect.trigger('change');
        return;
      }

      // Kirim processCode langsung karena MachineModel::getMachineByProcess() sudah menangani mapping prosesnya
      $.ajax({
        url: "<?php echo base_url(); ?>machine/list?process=" + encodeURIComponent(processCode),
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

    function updateModel() {
      var processEl   = document.getElementById("process");
      var processCode = processEl ? processEl.value : '';
      var docType     = getDocType();
      var modelSelect = $('#ModelName');

      modelSelect.empty();
      modelSelect.append(new Option("-- Semua Model (Tidak Dipilih) --", "", true, true));

      if (!processCode || processCode === '' || processCode === '-' || processCode === 'null') {
        modelSelect.val('').trigger('change');
        return;
      }

      $.ajax({
        url: "<?= base_url('model/list') ?>?process=" + encodeURIComponent(processCode) + "&type=" + encodeURIComponent(docType),
        dataType: 'JSON',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(data) {
          if (data && data.length > 0) {
            for (let i = 0; i < data.length; i++) {
              modelSelect.append(new Option(data[i].model_name, data[i].model_name, false, false));
            }
          }
          modelSelect.val('').trigger('change');
        },
        error: function() {
          modelSelect.val('').trigger('change');
        }
      });
    }

    function updateTable() {
      clearTimeout(updateTimer);
      updateTimer = setTimeout(function() { doUpdateTable(0); }, 400);
    }

    function doUpdateTable(retryCount) {
      retryCount = retryCount || 0;

      let processEl = document.getElementById("process");
      let process = processEl ? processEl.value : '';
      if (!process || process === "") {
        return;
      }

      let resDate = convertDate();
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if(!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = getDocType();

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
      var processValue = document.getElementById("process");
      var selectedRev = $('#revisiSelect').val();
      var selOpt = $('#process option:selected');
      var baseDoc = selOpt.data('docno') || selOpt.attr('data-docno') || '-';

      if (baseDoc !== '-' && selectedRev && selectedRev !== '' && selectedRev !== 'ALL') {
        var parts = baseDoc.split('-');
        if (parts.length >= 4) {
          parts[parts.length - 1] = String(selectedRev).padStart(2, '0');
          baseDoc = parts.join('-');
        }
      }
      $('#docno').val(baseDoc);
    }

    // Tipe dokumen (production/startup/foregoing) diambil dari opsi Doc. Type yang dipilih
    function getDocType() {
      var selOpt = $('#process option:selected');
      var type = selOpt.data('type') || selOpt.attr('data-type');
      if (type) return String(type).toLowerCase();
      var processCode = ($('#process').val() || '').toLowerCase();
      var seg = (processCode.split('-')[1] || '');
      if (seg.charAt(0) === 's') return 'startup';
      if (seg.charAt(0) === 'p') return 'production';
      if (seg.charAt(0) === 'f') return 'foregoing';
      return '';
    }

    function convertDate() {
      var drp = $('#daterange').data('daterangepicker');
      if (drp && drp.startDate && drp.endDate) {
        return {
          dateStart: drp.startDate.format('YYYY-MM-DD'),
          dateEnd: drp.endDate.format('YYYY-MM-DD')
        };
      }
      const rawSpan = $('#daterange span').html() || '';
      const date = rawSpan.split(" - ");
      var map = { "January": 1, "February": 2, "March": 3, "April": 4, "May": 5, "June": 6, "July": 7, "August": 8, "September": 9, "October": 10, "November": 11, "December": 12, "Januari": 1, "Februari": 2, "Maret": 3, "Mei": 5, "Juni": 6, "Juli": 7, "Agustus": 8, "Oktober": 10, "Desember": 12 };
      const date0 = date[0] || ''; const date1 = date[1] || '';
      const splitDate0 = date0.split(" "); const splitDate1 = date1.split(" ");
      const dateStart = (splitDate0[2] || '') + "-" + (map[splitDate0[1]] || '01') + "-" + (splitDate0[0] || '01');
      const dateEnd = (splitDate1[2] || '') + "-" + (map[splitDate1[1]] || '01') + "-" + (splitDate1[0] || '01');
      return { dateStart: dateStart, dateEnd: dateEnd };
    }

    var processRows = [];
    var TYPE_ORDER = { production: 1, startup: 2, foregoing: 3 };
    var TYPE_LABEL = { production: 'Production', startup: 'Startup', foregoing: 'Foregoing' };

    // "LD Die Bonding 1 Type GH16PSeries" -> base "LD Die Bonding 1", variant "Type GH16PSeries"
    function splitName(name) {
      name = $.trim(name || '');
      var i = name.indexOf(' Type ');
      if (i === -1) return { base: name, variant: '' };
      return { base: $.trim(name.substring(0, i)), variant: $.trim(name.substring(i + 1)) };
    }

    // "SL-D2-002-04" -> "SL-D2-002"; docno tanpa revisi dibiarkan
    function stripRevision(docno) {
      var p = (docno || '').split('-');
      return p.length >= 4 ? p.slice(0, -1).join('-') : (docno || '');
    }

    function changeDevice() {
      var device = document.getElementById("device").value;
      processRows = [];

      if (!device) {
        populateProcessName();
        return;
      }

      $.ajax({
        url: "<?php echo base_url(); ?>process/list?device=" + encodeURIComponent(device),
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
      var $sel = $('#process-name').empty();
      var names = [];
      $.each(processRows, function(i, r) {
        var base = splitName(r.process_name || r.name).base;
        if (base && names.indexOf(base) === -1) names.push(base);
      });
      names.sort();

      if (names.length === 0) {
        $sel.append(new Option($('#device').val() ? '-- Data Kosong --' : '-- Pilih Device --', ''));
      } else {
        $sel.append(new Option('-- Pilih Process --', ''));
        $.each(names, function(i, n) { $sel.append(new Option(n, n)); });
      }
      fillDocType();
    }

    // Dropdown Doc. Type: semua baris (production/startup/foregoing + variasi) dari proses terpilih
    function fillDocType() {
      var chosen = $('#process-name').val();
      var $sel = $('#process').empty();
      var rows = [];

      if (chosen) {
        rows = $.grep(processRows, function(r) {
          return splitName(r.process_name || r.name).base === chosen;
        });
        rows.sort(function(a, b) {
          var ta = TYPE_ORDER[a.type] || 9, tb = TYPE_ORDER[b.type] || 9;
          if (ta !== tb) return ta - tb;
          return String(a.docno).localeCompare(String(b.docno));
        });
      }

      if (rows.length !== 1) {
        $sel.append(new Option(chosen ? '-- Pilih Doc. Type --' : '-- Pilih Process --', ''));
      }
      $.each(rows, function(i, r) {
        var variant = splitName(r.process_name || r.name).variant;
        var label = stripRevision(r.docno) + ' (' + (TYPE_LABEL[r.type] || r.type) + ')' + (variant ? ' - ' + variant : '');
        var isSingle = (rows.length === 1);
        var opt = new Option(label, r.process_code, isSingle, isSingle);
        $(opt).attr('data-type', r.type).attr('data-docno', r.docno);
        $(opt).data('type', r.type).data('docno', r.docno);
        $sel.append(opt);
      });

      if (rows.length === 1) {
        $sel.val(rows[0].process_code);
      }

      if (rows.length === 0) {
        $('#table').empty();
      }
      // memicu updateTable, updateDocNo, updateMachine (kalau cuma 1 Doc. Type, otomatis terpilih)
      $sel.trigger('change');
    }

    function copy_clipboard() {
      let processEl = document.getElementById("process");
      let processVal = processEl ? processEl.value : '';
      var table = document.getElementById(processVal) || document.getElementById("table1");
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

      let processEl = document.getElementById("process");
      let process = processEl ? processEl.value : '';
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if (!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = getDocType();

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

      let processEl = document.getElementById("process");
      let process = processEl ? processEl.value : '';
      let model   = $('#ModelName').val();
      let lotNo   = $('#lotNo').val();
      let revisi  = $('#revisiSelect').val() || '';

      let machno = $('#machno').val();
      if(!machno || machno === 'null') { machno = ""; }
      machno = encodeURIComponent(machno);

      let device = "";
      let typeProcess = getDocType();

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