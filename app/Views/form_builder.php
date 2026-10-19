<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
    <?= $this->include('layout/navbar'); ?>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <style>
        .tree-node-card {
            border-left: 4px solid #0d6efd;
            background-color: #ffffff;
            transition: all 0.15s ease-in-out;
        }
        .tree-node-card[data-level="2"] { border-left-color: #198754; background-color: #f8fff9; }
        .tree-node-card[data-level="3"] { border-left-color: #fd7e14; background-color: #fffdf9; }
        .tree-node-card[data-level="4"] { border-left-color: #6f42c1; background-color: #fcfaff; }
        .tree-node-card[data-level="5"] { border-left-color: #dc3545; background-color: #fffafa; }
        .node-children-container {
            padding-left: 22px;
            border-left: 2px dashed #cbd5e1;
            margin-left: 10px;
        }
        #modalForm .modal-body {
            max-height: 72vh;
            overflow-y: auto !important;
        }
    </style>

    <div class="container-fluid mt-4">
        <div class="card shadow mb-5">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold"><i class="fas fa-hammer"></i> Form Builder & Revision Manager</h4>
            </div>

            <div class="card-body">
                <!-- 1. CASCADING DROPDOWN FILTER -->
                <div class="row mb-4 bg-light p-3 rounded border">
                    <div class="col-md-3">
                        <label class="font-weight-bold">1. Device</label>
                        <select id="deviceSelect" class="form-select" onchange="onFbDeviceChange()">
                            <option value="">-- Pilih Device --</option>
                            <?php foreach ($devices as $d): ?>
                                <?php $devCode = $d['code'] ?? $d['device_code'] ?? ''; ?>
                                <option value="<?= esc($devCode) ?>"><?= esc($d['name'] ?? $devCode) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">2. Doc Type</label>
                        <select id="docTypeSelect" class="form-select" onchange="onFbDocTypeChange()" disabled>
                            <option value="">-- Pilih Device Dulu --</option>
                            <option value="startup">Startup (Menurun / Vertikal)</option>
                            <option value="startup_horizontal">Startup (Mendatar / Horizontal)</option>
                            <option value="production">Production</option>
                            <option value="foregoing">Foregoing</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">3. Process</label>
                        <select id="processSelect" class="form-select" onchange="onFbProcessChange()" disabled>
                            <option value="">-- Pilih Doc Type Dulu --</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="btnLoadForm" class="btn btn-primary w-100" disabled>
                            <i class="fas fa-search"></i> Load Form
                        </button>
                    </div>
                </div>

                <!-- 2. REVISION HEADER & ACTION -->
                <div id="revisionHeader" class="d-none mb-3 p-3 bg-white border rounded shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="m-0 font-weight-bold">
                            Status Dokumen: <span id="lblRevisi" class="badge bg-danger fs-6">Revisi -</span>
                        </h5>
                        <small class="text-muted">Gunakan tombol di kanan untuk mengatur arah form, standar model, menambah kolom, atau menaikkan revisi.</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <!-- PENGUBAH KATEGORI ARAH STARTUP (MENURUN VS MENDATAR) -->
                        <div id="startupLayoutBar" class="d-none d-flex align-items-center gap-1 bg-light p-1 rounded border border-info me-1">
                            <span class="badge bg-info text-dark"><i class="fas fa-arrows-alt"></i> Arah Startup:</span>
                            <select id="fbStartupLayoutSelect" class="form-select form-select-sm" style="min-width: 180px;">
                                <option value="startup">Menurun (Vertikal)</option>
                                <option value="startup_horizontal">Mendatar (Horizontal)</option>
                            </select>
                        </div>

                        <!-- FILTER & PENGATUR STANDAR MODEL (KHUSUS FOREGOING) -->
                        <div id="foregoingModelBar" class="d-none d-flex align-items-center gap-1 bg-light p-1 rounded border border-primary me-1">
                            <span class="badge bg-primary"><i class="fas fa-microchip"></i> Model:</span>
                            <select id="fbModelSelect" class="form-select form-select-sm" style="min-width: 165px;">
                                <option value="">-- Pilih Model --</option>
                            </select>
                            <button type="button" class="btn btn-primary btn-sm" id="btnOpenModelStd" title="Atur Standar Min/Max X, Y, Z, θ untuk Model ini">
                                <i class="fas fa-sliders-h"></i> Standar Model
                            </button>
                            <button type="button" class="btn btn-outline-dark btn-sm" id="btnManageModels" title="Tambah / Edit / Hapus Daftar Model">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>

                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResyncHtml" title="Reset mengikuti file form HTML bawaan">
                            <i class="fas fa-sync-alt"></i> Tarik Ulang dari File Asli
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btnAddUtama">
                            <i class="fas fa-plus-circle"></i> Tambah Parameter Utama (Induk)
                        </button>
                        <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" id="btnUpRevisi">
                            <i class="fas fa-level-up-alt"></i> NAIKKAN REVISI
                        </button>
                    </div>
                </div>

                <!-- 3. WADAH TABEL VISUAL BUILDER -->
                <div id="visualBuilderContainer" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: BUILDER PARAMETER REKURSIF TANPA BATAS -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="formParamTree">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalTitle">Atur Struktur Parameter</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <input type="hidden" id="revisionId" name="revision_id">

                        <div id="infoIndukBox" class="alert alert-info py-2 d-none mb-3">
                            <i class="fas fa-info-circle"></i> Menambahkan sub-parameter di bawah induk: <strong id="lblNamaInduk">-</strong>
                        </div>

                        <div class="card card-body border-success mb-3 d-none" id="boxPindahInduk">
                            <label class="form-label fw-bold text-success mb-1">
                                <i class="fas fa-sitemap"></i> Posisi Induk Parameter Ini
                            </label>
                            <select class="form-select form-select-sm border-success" id="root_parent_id" name="root_parent_id"></select>
                            <small class="text-muted mt-1">Ubah pilihan ini hanya jika Anda ingin memindahkan seluruh blok parameter ini ke bawah kelompok lain.</small>
                        </div>

                        <div id="treeBuilderRoot"></div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSimpanParam">
                            <i class="fas fa-save"></i> Simpan Seluruh Struktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: CRUD BARIS & HEADER SAMPLE / LABEL (POS) FOREGOING -->
    <div class="modal fade" id="modalMatrixRow" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formMatrixRow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold" id="modalMatrixRowTitle">Tambah Baris Posisi</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="matrixRowMode" name="mode" value="add">
                        <input type="hidden" id="matrixOldLabel" name="old_label" value="">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="matrixRowLabel" name="row_label" placeholder="Contoh: Pos atau Y" required>
                        </div>

                        <div class="mb-3" id="boxMatrixAfterRow">
                            <label class="form-label fw-bold">Letakkan Posisi Baris:</label>
                            <select class="form-select" id="matrixAfterRow" name="after_row"></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnSaveMatrixRow">
                            <i class="fas fa-check"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: PENGATUR STANDAR (X, Y, Z, θ) PER MODEL FOREGOING -->
    <div class="modal fade" id="modalModelStd" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formModelStd">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-sliders-h"></i> Standar Foregoing Model: <span id="lblModalModelName" class="badge bg-warning text-dark fs-6">-</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="stdModelId">
                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded border">
                            <small class="text-muted">Atur batas <b>Min</b> dan <b>Max</b> untuk setiap baris posisi pada model ini. Jika tidak diukur (seperti baris Y pada model tertentu), matikan centang <b>Aktif</b>.</small>
                            <div class="d-flex align-items-center gap-1" style="min-width: 240px;">
                                <select id="copyFromModelSelect" class="form-select form-select-sm">
                                    <option value="">-- Copy Standar dari Model Lain --</option>
                                </select>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="20%">Baris Posisi (Pos)</th>
                                    <th width="20%">Status Ukur</th>
                                    <th width="30%">Batas Minimum (Min)</th>
                                    <th width="30%">Batas Maksimum (Max)</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyModelStdRows"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveModelStd">
                            <i class="fas fa-save"></i> Simpan Standar Model Ini
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: KELOLA DAFTAR MODEL (TAMBAH / EDIT / HAPUS MODEL) -->
    <div class="modal fade" id="modalManageModels" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-microchip"></i> Kelola Daftar Model (<span id="lblManageProc">-</span>)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrudModel" class="row g-2 mb-3 pb-3 border-bottom">
                        <input type="hidden" id="crudModelId" value="">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm" id="crudModelName" placeholder="Ketik Nama Model (Cth: GH15130D8C)" onkeyup="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div class="col-4 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-success w-100" id="btnSubmitCrudModel">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary d-none" id="btnCancelEditModel">Batal</button>
                        </div>
                    </form>

                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-bordered align-middle text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Model Name</th>
                                    <th>Standar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCrudModels"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- SCRIPT 1: FUNGSI GLOBAL DROPDOWN FILTER (TERPISAH AGAR SELALU AKTIF) -->
<script>
function onFbDeviceChange() {
    var devEl  = document.getElementById('deviceSelect');
    var docEl  = document.getElementById('docTypeSelect');
    var procEl = document.getElementById('processSelect');
    var btnEl  = document.getElementById('btnLoadForm');

    if (devEl && devEl.value !== '') {
        docEl.disabled = false;
        docEl.options[0].text = '-- Pilih Doc Type --';
        docEl.value = '';
        procEl.innerHTML = '<option value="">-- Pilih Doc Type Dulu --</option>';
        procEl.disabled = true;
        btnEl.disabled = true;
    } else if (docEl && procEl && btnEl) {
        docEl.disabled = true;
        docEl.options[0].text = '-- Pilih Device Dulu --';
        docEl.value = '';
        procEl.innerHTML = '<option value="">-- Pilih Device Dulu --</option>';
        procEl.disabled = true;
        btnEl.disabled = true;
    }
}

function onFbDocTypeChange() {
    var devEl  = document.getElementById('deviceSelect');
    var docEl  = document.getElementById('docTypeSelect');
    var procEl = document.getElementById('processSelect');
    var btnEl  = document.getElementById('btnLoadForm');

    var device = devEl ? devEl.value : '';
    var type   = docEl ? docEl.value : '';

    btnEl.disabled = true;

    if (!device || !type) {
        procEl.innerHTML = '<option value="">-- Pilih Doc Type Dulu --</option>';
        procEl.disabled = true;
        return;
    }

    procEl.innerHTML = '<option value="">Loading...</option>';
    procEl.disabled = true;

    fetch("<?= base_url('form-builder/get-processes') ?>?device_code=" + encodeURIComponent(device) + "&type=" + encodeURIComponent(type))
        .then(function(res) { return res.json(); })
        .then(function(data) {
            procEl.innerHTML = '';
            if (data && data.length > 0) {
                procEl.innerHTML = '<option value="">-- Pilih Process --</option>';
                data.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.process_code;
                    opt.textContent = item.name + ' (' + item.process_code + ')';
                    procEl.appendChild(opt);
                });
                procEl.disabled = false;
            } else {
                procEl.innerHTML = '<option value="">Data Kosong</option>';
                procEl.disabled = true;
            }
        })
        .catch(function(err) {
            console.error('Gagal mengambil daftar proses:', err);
            procEl.innerHTML = '<option value="">Gagal memuat proses</option>';
        });
}

function onFbProcessChange() {
    var procEl = document.getElementById('processSelect');
    var btnEl  = document.getElementById('btnLoadForm');
    if (btnEl && procEl) {
        btnEl.disabled = (procEl.value === '');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var devEl = document.getElementById('deviceSelect');
    if (devEl && devEl.value !== '') {
        onFbDeviceChange();
    }
});
</script>

<!-- SCRIPT 2: LOGIKA UTAMA FORM BUILDER -->
<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<script>
$(document).ready(function() {
    let currentRevisionId = null;
    window.currentParamsList = [];
    window.currentTreeParams = [];
    window.currentModelsList = [];

    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) document.activeElement.blur();
    });

    // LOAD TABEL VISUAL BUILDER
    function loadTableData(forceReparse = false) {
        let processCode = $('#processSelect').val();
        let docType     = $('#docTypeSelect').val();

        if (!processCode) {
            alert("Pilih proses mesinnya dulu, Bang!");
            return;
        }

        let btn = $('#btnLoadForm');
        let originalText = '<i class="fas fa-search"></i> Load Form';
        btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

        $.ajax({
            url: "<?= base_url('form-builder/load') ?>",
            type: "POST",
            data: {
                process: processCode,
                doc_type: docType,
                reparse: forceReparse ? 'true' : 'false'
            },
            dataType: "json",
            success: function(response) {
                btn.html(originalText).prop('disabled', false);

                if (response.status === 'success') {
                    window.currentParamsList = response.params_list || [];
                    window.currentTreeParams = response.tree_params || [];
                    currentRevisionId = response.revision.id;

                    $('#lblRevisi').text('Revisi ' + response.revision.revisi);
                    $('#revisionHeader').removeClass('d-none');
                    $('#visualBuilderContainer').html(response.html);

                    let kat = response.revision.kategori_form || docType;
                    if (kat === 'startup' || kat === 'startup_horizontal') {
                        $('#startupLayoutBar').removeClass('d-none');
                        $('#fbStartupLayoutSelect').val(kat);
                        $('#docTypeSelect').val(kat);
                    } else {
                        $('#startupLayoutBar').addClass('d-none');
                    }

                    loadModelsForFormBuilder();
                } else {
                    alert(response.message || "Gagal memuat form.");
                }
            },
            error: function(xhr) {
                btn.html(originalText).prop('disabled', false);
                console.error("Detail Error Server:", xhr.responseText);
                alert("Gagal memuat form! Cek tab Console.");
            }
        });
    }

    $('#btnLoadForm').on('click', function(e) {
        e.preventDefault();
        loadTableData(false);
    });

    $('#btnResyncHtml').on('click', function() {
        if (confirm('Tarik ulang struktur dari file HTML asli? Perubahan manual pada revisi ini akan di-reset mengikuti file asli.')) {
            loadTableData(true);
        }
    });

    $('#fbStartupLayoutSelect').on('change', function() {
        let newKat = $(this).val();
        $('#docTypeSelect').val(newKat);
        $.ajax({
            url: "<?= base_url('form-builder/load') ?>",
            type: "POST",
            data: {
                process: $('#processSelect').val(),
                doc_type: newKat,
                switch_layout: 'true',
                reparse: 'false'
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    window.currentParamsList = response.params_list || [];
                    window.currentTreeParams = response.tree_params || [];
                    $('#visualBuilderContainer').html(response.html);
                }
            }
        });
    });

    function collectSubtreeIds(node, arr) {
        if (!node) return;
        if (node.id) arr.push(parseInt(node.id));
        if (node.children && node.children.length > 0) {
            node.children.forEach(function(ch) { collectSubtreeIds(ch, arr); });
        }
    }

    function populateCleanParentDropdown(excludeIds, selectedParentId) {
        excludeIds = excludeIds || [];
        selectedParentId = selectedParentId || '';
        let dropdown = $('#root_parent_id');
        dropdown.empty();
        dropdown.append('<option value="">-- Jadikan Parameter Utama (Paling Atas / Kiri) --</option>');

        function walkTree(nodes, prefix) {
            prefix = prefix || '';
            nodes.forEach(function(n) {
                if (n.posisi_grid) return;
                if (excludeIds.includes(parseInt(n.id))) return;

                let fullPath = prefix ? (prefix + ' -> ' + n.nama_parameter) : n.nama_parameter;
                let isSelected = (String(n.id) === String(selectedParentId)) ? 'selected' : '';
                dropdown.append('<option value="' + n.id + '" ' + isSelected + '>' + fullPath + '</option>');

                if (n.children && n.children.length > 0 && !n.children[0].posisi_grid) {
                    walkTree(n.children, fullPath);
                }
            });
        }
        walkTree(window.currentTreeParams || [], '');
    }

    function createUnitRowHtml(unit, min, max) {
        unit = unit || '';
        let minVal = (min !== null && min !== undefined) ? min : '';
        let maxVal = (max !== null && max !== undefined) ? max : '';
        return '<div class="row g-2 align-items-center mb-2 unit-option-row">' +
            '<div class="col-md-4"><input type="text" class="form-control form-control-sm inp-unit-name" placeholder="Nama Satuan (Cth: MPa / N / gr)" value="' + unit + '"></div>' +
            '<div class="col-md-3"><input type="number" step="any" class="form-control form-control-sm inp-unit-min" placeholder="Batas Min (Opsional)" value="' + minVal + '"></div>' +
            '<div class="col-md-3"><input type="number" step="any" class="form-control form-control-sm inp-unit-max" placeholder="Batas Max (Opsional)" value="' + maxVal + '"></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-unit-row"><i class="fas fa-times"></i> Hapus</button></div>' +
            '</div>';
    }

    function createDynamicStdRowHtml(label, value, min) {
        label = label || '';
        value = value || '';
        let minVal = (min !== null && min !== undefined) ? min : '';
        return '<div class="row g-2 align-items-center mb-2 dyn-std-option-row">' +
            '<div class="col-md-5"><input type="text" class="form-control form-control-sm inp-dyn-label" placeholder="Pilihan Dropdown (Cth: 350 um)" value="' + label + '"></div>' +
            '<div class="col-md-3"><input type="text" class="form-control form-control-sm inp-dyn-val" placeholder="Kode (Cth: 350)" value="' + value + '"></div>' +
            '<div class="col-md-2"><input type="number" step="any" class="form-control form-control-sm inp-dyn-min" placeholder="Min (Cth: 2.8)" value="' + minVal + '"></div>' +
            '<div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-dyn-row"><i class="fas fa-times"></i></button></div>' +
            '</div>';
    }

    function parseJsonSafe(val) {
        if (!val) return null;
        if (typeof val === 'object') return val;
        try { return JSON.parse(val); } catch (e) { return null; }
    }

    function createNodeCard(nodeData, level, isRootCard) {
        nodeData = nodeData || {};
        level = level || 1;
        isRootCard = !!isRootCard;

        let id            = nodeData.id || '';
        let namaParam     = nodeData.nama_parameter || '';
        let kolomDb       = nodeData.kolom_db || '';
        let urutan        = nodeData.urutan || 1;
        let tipeInput     = nodeData.tipe_input || 'number';
        let standarNilai  = nodeData.standar_nilai || '';
        let batasMin      = (nodeData.batas_min !== null && nodeData.batas_min !== undefined) ? nodeData.batas_min : '';
        let batasMax      = (nodeData.batas_max !== null && nodeData.batas_max !== undefined) ? nodeData.batas_max : '';
        let isReadonly    = parseInt(nodeData.is_readonly || 0) === 1;
        let isExclusive   = parseInt(nodeData.is_exclusive || 0) === 1;
        let satuanOpsi    = parseJsonSafe(nodeData.satuan_opsi) || [];
        let rumusOtomatis = parseJsonSafe(nodeData.rumus_otomatis) || {};
        let stdDinamis    = parseJsonSafe(nodeData.standar_dinamis) || null;
        let children      = Array.isArray(nodeData.children) ? nodeData.children : [];

        let badgeColors = ['primary', 'success', 'warning text-dark', 'info text-dark', 'danger', 'dark'];
        let badgeColor  = badgeColors[(level - 1) % badgeColors.length];

        let formulaType   = rumusOtomatis.type || '';
        let formulaInCol  = rumusOtomatis.in_col || 'par013';
        let formulaOutCol = rumusOtomatis.out_col || 'par014';

        let hasDynStd     = (stdDinamis && Array.isArray(stdDinamis.options) && stdDinamis.options.length > 0);
        let dynLabelDrop  = hasDynStd ? (stdDinamis.label_dropdown || 'Jenis SM-Chip') : 'Jenis SM-Chip';
        let dynPrefix     = hasDynStd ? (stdDinamis.prefix_text || 'Min: ') : 'Min: ';
        let dynSuffix     = hasDynStd ? (stdDinamis.suffix_text || ' N') : ' N';

        let dbBadgeHtml   = kolomDb ? '<small class="text-primary ms-2">[' + kolomDb + ']</small>' : '';
        let removeBtnHtml = !isRootCard ? '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-subnode" title="Hapus Sub-Parameter Ini"><i class="fas fa-trash-alt"></i> Hapus</button>' : '';
        let isHeaderOnly  = (tipeInput === 'header_only' || (!nodeData.tipe_input && !nodeData.kolom_db && id));

        let cardHtml = `
        <div class="card shadow-sm mb-3 tree-node-card" data-level="${level}" data-id="${id}" data-kolom-db="${kolomDb}">
            <div class="card-header py-2 bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-${badgeColor} me-2">Tingkat ${level}</span>
                    <strong class="node-header-preview">${namaParam ? namaParam : 'Parameter Baru'}</strong>
                    ${dbBadgeHtml}
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success btn-add-subnode">
                        <i class="fas fa-plus"></i> Tambah Sub-Parameter (Tingkat ${level + 1})
                    </button>
                    ${removeBtnHtml}
                </div>
            </div>

            <div class="card-body py-3">
                <div class="row g-2 mb-2">
                    <div class="col-md-9">
                        <label class="form-label fw-bold small mb-1">Nama Kolom / Parameter (Tingkat ${level}) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm inp-node-name" placeholder="Contoh: Quantity / @ Transfer Air Pressure / In / Out" value="${namaParam}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small mb-1">Urutan</label>
                        <input type="number" class="form-control form-control-sm inp-node-urutan" value="${urutan}">
                    </div>
                </div>

                <div class="node-parent-settings p-2 rounded bg-light border mb-2 d-none">
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input chk-node-exclusive" type="checkbox" ${isExclusive ? 'checked' : ''}>
                            <label class="form-check-label fw-semibold small text-dark">
                                Hanya boleh diisi <u>salah satu</u> sub-parameter di bawahnya (Yang lain otomatis Gray Out)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input chk-node-dynstd" type="checkbox" ${hasDynStd ? 'checked' : ''}>
                            <label class="form-check-label fw-semibold small text-dark">
                                Gunakan Dropdown Penentu Standar Minimum (Seperti Jenis SM-Chip -> Min: - N)
                            </label>
                        </div>
                    </div>

                    <div class="box-dynstd-config mt-2 p-2 bg-white border border-warning rounded ${hasDynStd ? '' : 'd-none'}">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Judul Dropdown di Atas Tabel</label>
                                <input type="text" class="form-control form-control-sm inp-dynstd-title" value="${dynLabelDrop}" placeholder="Contoh: Jenis SM-Chip">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Teks Awalan</label>
                                <input type="text" class="form-control form-control-sm inp-dynstd-prefix" value="${dynPrefix}" placeholder="Min: ">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Satuan Akhiran</label>
                                <input type="text" class="form-control form-control-sm inp-dynstd-suffix" value="${dynSuffix}" placeholder=" N">
                            </div>
                        </div>
                        <label class="form-label small fw-bold mb-1">Daftar Pilihan Dropdown & Nilai Batas Minimumnya:</label>
                        <div class="dynstd-rows-container"></div>
                        <button type="button" class="btn btn-xs btn-outline-warning text-dark mt-1 btn-add-dynstd-row">
                            <i class="fas fa-plus"></i> Tambah Pilihan Standar
                        </button>
                    </div>
                </div>

                <div class="node-leaf-settings p-3 rounded bg-light border">
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">Bentuk Isian Operator</label>
                            <select class="form-select form-select-sm sel-node-tipe">
                                <option value="number" ${tipeInput === 'number' ? 'selected' : ''}>Angka (Ukuran / Qty / Tekanan)</option>
                                <option value="dropdown_ok_ng" ${tipeInput === 'dropdown_ok_ng' ? 'selected' : ''}>Pilihan OK / NG (Dropdown)</option>
                                <option value="text" ${tipeInput === 'text' ? 'selected' : ''}>Teks Bebas (1 Baris)</option>
                                <option value="textarea" ${tipeInput === 'textarea' ? 'selected' : ''}>Catatan Panjang (Kotak Besar)</option>
                                <option value="header_only" ${isHeaderOnly ? 'selected' : ''}>Hanya Teks Statis / Label (Tanpa Input)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small mb-1">Isi Teks Statis / Keterangan Standar (Opsional)</label>
                            <input type="text" class="form-control form-control-sm inp-node-standar" placeholder="Contoh: 7 ~ 10V / Measure battery Voltage" value="${standarNilai}">
                        </div>
                        <div class="col-md-3 pt-3">
                            <div class="form-check mt-1">
                                <input class="form-check-input chk-node-readonly" type="checkbox" ${isReadonly ? 'checked' : ''}>
                                <label class="form-check-label fw-bold small text-secondary">
                                    Read-Only (Terkunci)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="box-number-extra mt-2 pt-2 border-top ${tipeInput === 'number' ? '' : 'd-none'}">
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Batas Minimum (Min)</label>
                                <input type="number" step="any" class="form-control form-control-sm inp-node-min" placeholder="Bebas" value="${batasMin}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Batas Maksimum (Max)</label>
                                <input type="number" step="any" class="form-control form-control-sm inp-node-max" placeholder="Bebas" value="${batasMax}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1 text-primary"><i class="fas fa-calculator"></i> Hitung Otomatis (NG / Yield %)</label>
                                <select class="form-select form-select-sm sel-node-formula">
                                    <option value="" ${formulaType === '' ? 'selected' : ''}>-- Tidak Ada (Input Angka Manual) --</option>
                                    <option value="ng" ${formulaType === 'ng' ? 'selected' : ''}>Hitung NG Otomatis (In dikurangi Out)</option>
                                    <option value="yield" ${formulaType === 'yield' ? 'selected' : ''}>Hitung Yield % Otomatis ((Out / In) x 100)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-2 p-2 bg-white border border-primary rounded box-formula-cols ${formulaType !== '' ? '' : 'd-none'}">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Kolom Acuan "In" (Input):</label>
                                <input type="text" class="form-control form-control-sm inp-formula-in" value="${formulaInCol}" placeholder="Contoh: par013">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Kolom Acuan "Out" (Output):</label>
                                <input type="text" class="form-control form-control-sm inp-formula-out" value="${formulaOutCol}" placeholder="Contoh: par014">
                            </div>
                        </div>

                        <div class="mt-2 p-2 bg-white border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark">
                                    <i class="fas fa-ruler-combined text-success"></i> Dropdown Satuan Angka (Opsional - Tampil Menyatu di History)
                                </span>
                                <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 btn-add-unit-row">
                                    <i class="fas fa-plus"></i> Tambah Satuan
                                </button>
                            </div>
                            <div class="unit-rows-container"></div>
                        </div>
                    </div>
                </div>

                <div class="node-children-container mt-3 d-none"></div>
            </div>
        </div>`;

        let card = $(cardHtml);

        let unitContainer = card.find('.unit-rows-container').first();
        if (Array.isArray(satuanOpsi) && satuanOpsi.length > 0) {
            satuanOpsi.forEach(function(u) {
                unitContainer.append(createUnitRowHtml(u.unit || '', u.min, u.max));
            });
        }

        let dynContainer = card.find('.dynstd-rows-container').first();
        if (hasDynStd) {
            stdDinamis.options.forEach(function(opt) {
                dynContainer.append(createDynamicStdRowHtml(opt.label || '', opt.value || '', opt.min));
            });
        } else {
            dynContainer.append(createDynamicStdRowHtml('350 um', '350', 2.8));
            dynContainer.append(createDynamicStdRowHtml('450 um', '450', 3.6));
            dynContainer.append(createDynamicStdRowHtml('850 um', '850', 8.7));
        }

        let childrenContainer = card.find('.node-children-container').first();
        if (children.length > 0) {
            children.forEach(function(ch) {
                childrenContainer.append(createNodeCard(ch, level + 1, false));
            });
        }

        syncCardMode(card);
        return card;
    }

    function syncCardMode(card) {
        let childrenContainer = card.children('.card-body').children('.node-children-container');
        let parentSettings    = card.children('.card-body').children('.node-parent-settings');
        let leafSettings      = card.children('.card-body').children('.node-leaf-settings');
        let childCount        = childrenContainer.children('.tree-node-card').length;

        if (childCount > 0) {
            childrenContainer.removeClass('d-none');
            parentSettings.removeClass('d-none');
            leafSettings.addClass('d-none');
        } else {
            childrenContainer.addClass('d-none');
            parentSettings.addClass('d-none');
            leafSettings.removeClass('d-none');
        }
    }

    $(document).on('input', '.inp-node-name', function() {
        let val = $(this).val().trim();
        $(this).closest('.tree-node-card').children('.card-header').find('.node-header-preview').text(val || 'Parameter Baru');
    });

    $(document).on('change', '.sel-node-tipe', function() {
        let card = $(this).closest('.tree-node-card');
        let boxNum = card.children('.card-body').children('.node-leaf-settings').children('.box-number-extra');
        if ($(this).val() === 'number') {
            boxNum.removeClass('d-none');
        } else {
            boxNum.addClass('d-none');
        }
    });

    $(document).on('change', '.sel-node-formula', function() {
        let card = $(this).closest('.tree-node-card');
        let leafBox = card.children('.card-body').children('.node-leaf-settings');
        let formulaColsBox = leafBox.find('.box-formula-cols').first();
        if ($(this).val() !== '') {
            formulaColsBox.removeClass('d-none');
            leafBox.find('.chk-node-readonly').first().prop('checked', true);
        } else {
            formulaColsBox.addClass('d-none');
        }
    });

    $(document).on('change', '.chk-node-dynstd', function() {
        let parentBox = $(this).closest('.node-parent-settings');
        if ($(this).is(':checked')) {
            parentBox.find('.box-dynstd-config').first().removeClass('d-none');
        } else {
            parentBox.find('.box-dynstd-config').first().addClass('d-none');
        }
    });

    $(document).on('click', '.btn-add-unit-row', function() {
        $(this).closest('.border').find('.unit-rows-container').first().append(createUnitRowHtml('', '', ''));
    });
    $(document).on('click', '.btn-remove-unit-row', function() {
        $(this).closest('.unit-option-row').remove();
    });

    $(document).on('click', '.btn-add-dynstd-row', function() {
        $(this).siblings('.dynstd-rows-container').append(createDynamicStdRowHtml('', '', ''));
    });
    $(document).on('click', '.btn-remove-dynstd-row, .btn-remove-dyn-row', function() {
        $(this).closest('.dyn-std-option-row').remove();
    });

    $(document).on('click', '.btn-add-subnode', function() {
        let parentCard = $(this).closest('.tree-node-card');
        let parentLevel = parseInt(parentCard.attr('data-level')) || 1;
        let childrenContainer = parentCard.children('.card-body').children('.node-children-container');
        let nextOrder = childrenContainer.children('.tree-node-card').length + 1;

        let newChildCard = createNodeCard({ urutan: nextOrder, tipe_input: 'number' }, parentLevel + 1, false);
        childrenContainer.append(newChildCard);
        syncCardMode(parentCard);
    });

    $(document).on('click', '.btn-remove-subnode', function() {
        let currentCard = $(this).closest('.tree-node-card');
        let parentCard  = currentCard.parent('.node-children-container').closest('.tree-node-card');
        currentCard.remove();
        if (parentCard.length > 0) {
            syncCardMode(parentCard);
        }
    });

    function serializeNodeCard(card) {
        let id          = card.attr('data-id') || null;
        let kolomDb     = card.attr('data-kolom-db') || null;
        let body        = card.children('.card-body');
        let namaParam   = body.find('.inp-node-name').first().val().trim();
        let urutan      = parseInt(body.find('.inp-node-urutan').first().val()) || 1;

        let childrenContainer = body.children('.node-children-container');
        let childCards        = childrenContainer.children('.tree-node-card');
        let hasChildren       = childCards.length > 0;

        let parentSettings = body.children('.node-parent-settings');
        let leafSettings   = body.children('.node-leaf-settings');

        let isExclusive = hasChildren && parentSettings.find('.chk-node-exclusive').first().is(':checked') ? 1 : 0;

        let standarDinamis = null;
        if (hasChildren && parentSettings.find('.chk-node-dynstd').first().is(':checked')) {
            let opts = [];
            parentSettings.find('.dynstd-rows-container').first().children('.dyn-std-option-row').each(function() {
                let lbl = $(this).find('.inp-dyn-label').val().trim();
                let val = $(this).find('.inp-dyn-val').val().trim();
                let min = $(this).find('.inp-dyn-min').val();
                if (lbl !== '') {
                    opts.push({
                        label: lbl,
                        value: val || lbl,
                        min: min !== '' ? parseFloat(min) : 0
                    });
                }
            });
            if (opts.length > 0) {
                standarDinamis = {
                    label_dropdown: parentSettings.find('.inp-dynstd-title').first().val().trim() || 'Jenis SM-Chip',
                    db_col: 'std_die_bond',
                    prefix_text: parentSettings.find('.inp-dynstd-prefix').first().val() || 'Min: ',
                    suffix_text: parentSettings.find('.inp-dynstd-suffix').first().val() || ' N',
                    options: opts
                };
            }
        }

        let selTipe      = leafSettings.find('.sel-node-tipe').first().val();
        let jenisElemen  = (hasChildren || selTipe === 'header_only') ? 'header' : 'input';
        let tipeInput    = hasChildren ? null : selTipe;
        let standarNilai = leafSettings.find('.inp-node-standar').first().val().trim();
        let isReadonly   = leafSettings.find('.chk-node-readonly').first().is(':checked') ? 1 : 0;
        let batasMin     = leafSettings.find('.inp-node-min').first().val();
        let batasMax     = leafSettings.find('.inp-node-max').first().val();

        let satuanOpsi = [];
        if (jenisElemen === 'input' && tipeInput === 'number') {
            leafSettings.find('.unit-rows-container').first().children('.unit-option-row').each(function() {
                let uName = $(this).find('.inp-unit-name').val().trim();
                let uMin  = $(this).find('.inp-unit-min').val();
                let uMax  = $(this).find('.inp-unit-max').val();
                if (uName !== '') {
                    satuanOpsi.push({
                        unit: uName,
                        min: uMin !== '' ? parseFloat(uMin) : null,
                        max: uMax !== '' ? parseFloat(uMax) : null
                    });
                }
            });
        }

        let rumusOtomatis = null;
        let formulaType = leafSettings.find('.sel-node-formula').first().val();
        if (jenisElemen === 'input' && tipeInput === 'number' && formulaType !== '') {
            rumusOtomatis = {
                type: formulaType,
                in_col: leafSettings.find('.inp-formula-in').first().val().trim() || 'par013',
                out_col: leafSettings.find('.inp-formula-out').first().val().trim() || 'par014'
            };
            isReadonly = 1;
        }

        let childrenData = [];
        childCards.each(function() {
            childrenData.push(serializeNodeCard($(this)));
        });

        return {
            id: id,
            nama_parameter: namaParam,
            kolom_db: kolomDb,
            urutan: urutan,
            jenis_elemen: jenisElemen,
            tipe_input: tipeInput,
            standar_nilai: standarNilai,
            batas_min: batasMin,
            batas_max: batasMax,
            is_readonly: isReadonly,
            is_exclusive: isExclusive,
            satuan_opsi: satuanOpsi.length > 0 ? satuanOpsi : null,
            rumus_otomatis: rumusOtomatis,
            standar_dinamis: standarDinamis,
            children: childrenData
        };
    }

    $('#btnAddUtama').click(function() {
        $('#revisionId').val(currentRevisionId);
        populateCleanParentDropdown([], '');
        $('#boxPindahInduk').addClass('d-none');
        $('#infoIndukBox').addClass('d-none');

        let nextOrder = (window.currentTreeParams ? window.currentTreeParams.length : 0) + 1;
        $('#treeBuilderRoot').empty().append(createNodeCard({ urutan: nextOrder, tipe_input: 'number' }, 1, true));

        $('#modalTitle').html('<i class="fas fa-plus-circle"></i> Tambah Parameter Baru (Bisa Berjenjang Tanpa Batas)');
        $('#modalForm').modal('show');
    });

    $(document).on('click', '.btn-add-child', function() {
        let parentId   = $(this).data('id');
        let parentName = $(this).data('name') || 'Parameter Terpilih';

        $('#revisionId').val(currentRevisionId);
        populateCleanParentDropdown([], parentId);
        $('#boxPindahInduk').addClass('d-none');

        $('#lblNamaInduk').text(parentName);
        $('#infoIndukBox').removeClass('d-none');

        $('#treeBuilderRoot').empty().append(createNodeCard({ urutan: 1, tipe_input: 'number' }, 1, true));

        $('#modalTitle').html('<i class="fas fa-sitemap"></i> Tambah Sub-Parameter');
        $('#modalForm').modal('show');
    });

    $(document).on('click', '.btn-edit-tree', function() {
        let treeData = $(this).data('tree');
        if (typeof treeData === 'string') {
            treeData = JSON.parse(treeData);
        }

        $('#revisionId').val(currentRevisionId);

        let excludeIds = [];
        collectSubtreeIds(treeData, excludeIds);
        populateCleanParentDropdown(excludeIds, treeData.parent_id || '');

        $('#boxPindahInduk').removeClass('d-none');
        $('#infoIndukBox').addClass('d-none');

        $('#treeBuilderRoot').empty().append(createNodeCard(treeData, 1, true));

        $('#modalTitle').html('<i class="fas fa-edit"></i> Edit Struktur & Aturan Parameter');
        $('#modalForm').modal('show');
    });

    $('#formParamTree').submit(function(e) {
        e.preventDefault();
        let rootCard = $('#treeBuilderRoot').children('.tree-node-card').first();
        if (rootCard.length === 0) return;

        let treePayload = serializeNodeCard(rootCard);
        let btn = $('#btnSimpanParam');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: "<?= base_url('form-builder/save-tree') ?>",
            type: "POST",
            data: {
                revision_id: $('#revisionId').val(),
                root_parent_id: $('#root_parent_id').val(),
                tree_json: JSON.stringify(treePayload)
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Seluruh Struktur');
                if (res.status === 'success') {
                    $('#modalForm').modal('hide');
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal menyimpan struktur parameter.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Seluruh Struktur');
                console.error("Save Tree Error:", xhr.responseText);
                alert("Gagal menyimpan! Cek tab Console.");
            }
        });
    });

    $(document).on('click', '.btn-delete', function() {
        if (confirm('Yakin ingin menghapus parameter ini? Jika ini adalah Induk, semua sub-bagian di bawahnya juga akan ikut terhapus!')) {
            let id = $(this).data('id');$.ajax({
                url: "<?= base_url('form-builder/delete') ?>",
                type: "POST",
                data: { id: id },
                dataType: "json",
                success: function() {
                    loadTableData(false);
                }
            });
        }
    });

    $(document).on('click', '.btn-add-matrix-row', function() {
        let existingRows = $(this).data('rows') || [];
        if (typeof existingRows === 'string') existingRows = JSON.parse(existingRows);

        $('#matrixRowMode').val('add');
        $('#matrixOldLabel').val('');
        $('#matrixRowLabel').val('');

        let afterSelect = $('#matrixAfterRow');
        afterSelect.empty();
        afterSelect.append('<option value="__FIRST__">-- Paling Atas (Baris Pertama) --</option>');
        existingRows.forEach(function(r) {
            afterSelect.append('<option value="' + r + '" selected>Setelah Baris "' + r + '"</option>');
        });

        $('#boxMatrixAfterRow').removeClass('d-none');
        $('#modalMatrixRowTitle').text('Tambah Baris Posisi Baru');
        $('#modalMatrixRow').modal('show');
    });

    $(document).on('click', '.btn-edit-matrix-row', function() {
        let lbl = $(this).data('label');$('#matrixRowMode').val('edit');
        $('#matrixOldLabel').val(lbl);
        $('#matrixRowLabel').val(lbl);
        $('#boxMatrixAfterRow').addClass('d-none');

        $('#modalMatrixRowTitle').text('Ubah Nama Baris: ' + lbl);
        $('#modalMatrixRow').modal('show');
    });

    $(document).on('click', '.btn-edit-matrix-header', function() {
        let lbl = $(this).data('label') || 'Sample / Label';$('#matrixRowMode').val('edit_header');
        $('#matrixOldLabel').val(lbl);
        $('#matrixRowLabel').val(lbl);
        $('#boxMatrixAfterRow').addClass('d-none');

        $('#modalMatrixRowTitle').text('Ubah Nama Kolom Header: ' + lbl);
        $('#modalMatrixRow').modal('show');
    });

    $('#formMatrixRow').submit(function(e) {
        e.preventDefault();
        let btn = $('#btnSaveMatrixRow');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "<?= base_url('form-builder/row-save') ?>",
            type: "POST",
            data: {
                revision_id: currentRevisionId,
                mode: $('#matrixRowMode').val(),
                old_label: $('#matrixOldLabel').val(),
                row_label: $('#matrixRowLabel').val(),
                after_row: $('#matrixAfterRow').val()
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Simpan');
                if (res.status === 'success') {
                    $('#modalMatrixRow').modal('hide');
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal menyimpan baris.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Simpan');
                console.error(xhr.responseText);
                alert('Gagal menyimpan baris matriks.');
            }
        });
    });

    $(document).on('click', '.btn-delete-matrix-row', function() {
        let lbl = $(this).data('label');
        if (confirm('Yakin ingin menghapus seluruh baris "' + lbl + '" beserta semua kotak input di baris tersebut?')) {
            $.ajax({
                url: "<?= base_url('form-builder/row-delete') ?>",
                type: "POST",
                data: { revision_id: currentRevisionId, row_label: lbl },
                dataType: "json",
                success: function() {
                    loadTableData(false);
                }
            });
        }
    });

    $(document).on('click', '.btn-move-matrix-row', function() {
        let lbl = $(this).data('label');
        let dir = $(this).data('dir');$.ajax({
            url: "<?= base_url('form-builder/row-move') ?>",
            type: "POST",
            data: { revision_id: currentRevisionId, row_label: lbl, direction: dir },
            dataType: "json",
            success: function() {
                loadTableData(false);
            }
        });
    });

    $('#btnUpRevisi').click(function() {
        if (confirm('PERINGATAN ISO: Anda yakin ingin menaikkan Revisi? Template revisi saat ini akan dikunci untuk riwayat dokumen lama, dan Anda akan mengedit salinan Revisi terbaru.')) {
            $.ajax({
                url: "<?= base_url('form-builder/up-revisi') ?>",
                type: "POST",
                data: { revision_id: currentRevisionId },
                dataType: "json",
                success: function(res) {
                    alert(res.message);
                    loadTableData(false);
                }
            });
        }
    });

    function getActiveForegoingRowNames() {
        let rows = [];
        (window.currentParamsList || []).forEach(function(p) {
            if (p.posisi_grid && !rows.includes(p.posisi_grid)) {
                rows.push(p.posisi_grid);
            }
        });
        return rows.length > 0 ? rows : ['X', 'Y', 'Z', 'θ'];
    }

    function loadModelsForFormBuilder(selectedId) {
        selectedId = selectedId || null;
        let processCode = $('#processSelect').val();
        let docType     = $('#docTypeSelect').val();

        if (docType !== 'foregoing' || !processCode) {
            $('#foregoingModelBar').addClass('d-none');
            return;
        }

        $('#foregoingModelBar').removeClass('d-none');

        $.getJSON("<?= base_url('model/list') ?>", { process: processCode }, function(models) {
            window.currentModelsList = models || [];
            let sel = $('#fbModelSelect');
            let copySel = $('#copyFromModelSelect');
            let prevVal = selectedId || sel.val();

            sel.empty().append('<option value="">-- Pilih Model (' + models.length + ') --</option>');
            copySel.empty().append('<option value="">-- Copy Standar dari Model Lain --</option>');

            let tbodyCrud = $('#tbodyCrudModels');
            tbodyCrud.empty();

            models.forEach(function(m, idx) {
                let hasStd = m.standards && Object.keys(m.standards).length > 0;
                let badgeTxt = hasStd ? 'Sudah Diatur' : 'Belum Diatur';
                let isSel = (String(m.id) === String(prevVal)) ? 'selected' : '';
                sel.append('<option value="' + m.id + '" ' + isSel + '>' + m.model_name + '</option>');
                if (hasStd) {
                    copySel.append('<option value="' + m.id + '">Copy dari: ' + m.model_name + '</option>');
                }

                tbodyCrud.append(
                    '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td class="fw-bold">' + m.model_name + '</td>' +
                    '<td><span class="badge ' + (hasStd ? 'bg-success' : 'bg-secondary') + '">' + badgeTxt + '</span></td>' +
                    '<td>' +
                    '<button type="button" class="btn btn-xs btn-warning py-0 px-1 me-1 btn-edit-model-item" data-id="' + m.id + '" data-name="' + m.model_name + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-danger py-0 px-1 btn-del-model-item" data-id="' + m.id + '" data-name="' + m.model_name + '"><i class="fas fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>'
                );
            });

            previewSelectedModelStandardOnTable();
        });
    }

    function previewSelectedModelStandardOnTable() {
        let modelId = $('#fbModelSelect').val();
        let modelObj = (window.currentModelsList || []).find(function(m) { return String(m.id) === String(modelId); });
        $('.model-std-preview-badge').remove();

        if (!modelObj || !modelObj.standards) return;

        $('#visualBuilderContainer table tbody tr').each(function() {
            let posCell = $(this).find('td.bg-secondary .fs-6').first();
            if (posCell.length === 0) return;
            let rowName = posCell.text().trim();
            let cfg = modelObj.standards[rowName];
            if (!cfg) return;

            if (parseInt(cfg.active) === 0) {
                posCell.after('<div class="model-std-preview-badge badge bg-dark text-warning mt-1 d-block" style="font-size:10px;">' + modelObj.model_name + ': OFF (Tidak Diukur)</div>');
            } else {
                let minLbl = (cfg.min !== null && cfg.min !== '') ? cfg.min : '-∞';
                let maxLbl = (cfg.max !== null && cfg.max !== '') ? cfg.max : '∞';
                posCell.after('<div class="model-std-preview-badge badge bg-warning text-dark mt-1 d-block" style="font-size:10px;">' + modelObj.model_name + ': ' + minLbl + ' ~ ' + maxLbl + '</div>');
            }
        });
    }

    $('#fbModelSelect').on('change', function() {
        previewSelectedModelStandardOnTable();
    });

    function renderModelStdRows(standardsObj) {
        standardsObj = standardsObj || {};
        let rows = getActiveForegoingRowNames();
        let tbody = $('#tbodyModelStdRows');
        tbody.empty();

        rows.forEach(function(rName) {
            let cfg = standardsObj[rName] || { min: '', max: '', active: 1 };
            let isAct = (cfg.active === undefined || parseInt(cfg.active) === 1);
            let minV = (cfg.min !== null && cfg.min !== undefined) ? cfg.min : '';
            let maxV = (cfg.max !== null && cfg.max !== undefined) ? cfg.max : '';
            tbody.append(
                '<tr class="model-std-row" data-pos="' + rName + '">' +
                '<td class="fw-bold fs-6 bg-light">' + rName + '</td>' +
                '<td><div class="form-check d-flex justify-content-center">' +
                '<input class="form-check-input chk-pos-active" type="checkbox" ' + (isAct ? 'checked' : '') + '>' +
                '<label class="form-check-label ms-1 small">Aktif</label></div></td>' +
                '<td><input type="number" step="any" class="form-control form-control-sm text-center inp-pos-min" placeholder="Min" value="' + minV + '" ' + (isAct ? '' : 'disabled') + '></td>' +
                '<td><input type="number" step="any" class="form-control form-control-sm text-center inp-pos-max" placeholder="Max" value="' + maxV + '" ' + (isAct ? '' : 'disabled') + '></td>' +
                '</tr>'
            );
        });
    }

    $(document).on('change', '.chk-pos-active', function() {
        let tr = $(this).closest('tr');
        let active = $(this).is(':checked');
        tr.find('.inp-pos-min, .inp-pos-max').prop('disabled', !active);
        if (!active) {
            tr.find('.inp-pos-min, .inp-pos-max').val('');
        }
    });

    $('#btnOpenModelStd').on('click', function() {
        let modelId = $('#fbModelSelect').val();
        if (!modelId) {
            alert('Pilih Model Name terlebih dahulu di dropdown sebelah kiri tombol ini!');
            return;
        }
        let modelObj = (window.currentModelsList || []).find(function(m) { return String(m.id) === String(modelId); });
        if (!modelObj) return;

        $('#stdModelId').val(modelObj.id);
        $('#lblModalModelName').text(modelObj.model_name);
        $('#copyFromModelSelect').val('');
        renderModelStdRows(modelObj.standards || {});
        $('#modalModelStd').modal('show');
    });

    $('#copyFromModelSelect').on('change', function() {
        let srcId = $(this).val();
        if (!srcId) return;
        let srcModel = (window.currentModelsList || []).find(function(m) { return String(m.id) === String(srcId); });
        if (srcModel && srcModel.standards) {
            renderModelStdRows(srcModel.standards);
        }
    });

    $('#formModelStd').on('submit', function(e) {
        e.preventDefault();
        let modelId = $('#stdModelId').val();
        let stdPayload = {};

        $('#tbodyModelStdRows tr.model-std-row').each(function() {
            let pos = $(this).attr('data-pos');
            let act = $(this).find('.chk-pos-active').is(':checked') ? 1 : 0;
            let min = $(this).find('.inp-pos-min').val();
            let max = $(this).find('.inp-pos-max').val();
            stdPayload[pos] = {
                active: act,
                min: (act && min !== '') ? parseFloat(min) : null,
                max: (act && max !== '') ? parseFloat(max) : null
            };
        });

        let btn = $('#btnSaveModelStd');
        btn.prop('disabled', true).text('Menyimpan...');

        $.post("<?= base_url('model/save-standards') ?>", {
            model_id: modelId,
            standards_json: JSON.stringify(stdPayload)
        }, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Standar Model Ini');
            if (res.status === 'success') {
                $('#modalModelStd').modal('hide');
                loadModelsForFormBuilder(modelId);
            } else {
                alert(res.message || 'Gagal menyimpan standar model.');
            }
        }, 'json');
    });

    $('#btnManageModels').on('click', function() {
        $('#lblManageProc').text($('#processSelect').val().toUpperCase());
        $('#crudModelId').val('');
        $('#crudModelName').val('');
        $('#btnCancelEditModel').addClass('d-none');
        $('#btnSubmitCrudModel').html('<i class="fas fa-plus"></i> Tambah');
        $('#modalManageModels').modal('show');
    });

    $(document).on('click', '.btn-edit-model-item', function() {$('#crudModelId').val($(this).data('id'));$('#crudModelName').val($(this).data('name'));$('#btnSubmitCrudModel').html('<i class="fas fa-save"></i> Update');
        $('#btnCancelEditModel').removeClass('d-none');
    });

    $('#btnCancelEditModel').on('click', function() {
        $('#crudModelId').val('');
        $('#crudModelName').val('');
        $('#btnSubmitCrudModel').html('<i class="fas fa-plus"></i> Tambah');
        $(this).addClass('d-none');
    });

    $('#formCrudModel').on('submit', function(e) {
        e.preventDefault();
        let proc = $('#processSelect').val();
        let id   = $('#crudModelId').val();
        let name = $('#crudModelName').val().trim().toUpperCase();

        $.post("<?= base_url('model/save') ?>", {
            id: id,
            process_code: proc,
            model_name: name
        }, function(res) {
            if (res.status === 'success') {
                $('#crudModelId').val('');
                $('#crudModelName').val('');
                $('#btnSubmitCrudModel').html('<i class="fas fa-plus"></i> Tambah');
                $('#btnCancelEditModel').addClass('d-none');
                loadModelsForFormBuilder(res.id || id);
            } else {
                alert(res.message || 'Gagal menyimpan model.');
            }
        }, 'json');
    });

    $(document).on('click', '.btn-del-model-item', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        if (confirm('Yakin ingin menghapus Model "' + name + '"?')) {
            $.post("<?= base_url('model/delete') ?>", { id: id }, function() {
                loadModelsForFormBuilder();
            }, 'json');
        }
    });
});
</script>
<?= $this->endSection(); ?>