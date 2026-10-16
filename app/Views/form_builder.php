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

        /* PERBAIKAN SCROLL MODAL REKURSIF */
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
                        <select id="deviceSelect" class="form-select">
                            <option value="">-- Pilih Device --</option>
                            <?php foreach ($devices as $d): ?>
                                <option value="<?= $d['code'] ?>"><?= $d['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">2. Doc Type</label>
                        <select id="docTypeSelect" class="form-select" disabled>
                            <option value="">-- Pilih Device Dulu --</option>
                            <option value="startup">Startup</option>
                            <option value="production">Production</option>
                            <option value="foregoing">Foregoing</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">3. Process</label>
                        <select id="processSelect" class="form-select" disabled>
                            <option value="">-- Pilih Doc Type Dulu --</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="btnLoadForm" class="btn btn-primary w-100" disabled>
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
                        <small class="text-muted">Gunakan tombol di kanan untuk menambah kolom utama, mereset dari file asli, atau menaikkan revisi.</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-secondary btn-sm" id="btnResyncHtml" title="Reset mengikuti file form HTML bawaan">
                            <i class="fas fa-sync-alt"></i> Tarik Ulang dari File Asli
                        </button>
                        <button class="btn btn-success btn-sm" id="btnAddUtama">
                            <i class="fas fa-plus-circle"></i> Tambah Parameter Utama (Induk)
                        </button>
                        <button class="btn btn-warning btn-sm fw-bold text-dark" id="btnUpRevisi">
                            <i class="fas fa-level-up-alt"></i> NAIKKAN REVISI
                        </button>
                    </div>
                </div>

                <!-- 3. WADAH TABEL VISUAL BUILDER -->
                <div id="visualBuilderContainer" class="mt-3"></div>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- MODAL 1: BUILDER PARAMETER REKURSIF TANPA BATAS (LEVEL 1, 2, 3, DST.) -->
    <!-- ===================================================================== -->
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

                        <!-- Info Induk Saat Klik Tombol (+) pada Kolom Tertentu -->
                        <div id="infoIndukBox" class="alert alert-info py-2 d-none mb-3">
                            <i class="fas fa-info-circle"></i> Menambahkan sub-parameter di bawah induk: <strong id="lblNamaInduk">-</strong>
                        </div>

                        <!-- Dropdown Pindah Posisi Induk (Hanya Muncul Saat Mode Edit & Hanya Menampilkan Folder/Header) -->
                        <div class="card card-body border-success mb-3 d-none" id="boxPindahInduk">
                            <label class="form-label fw-bold text-success mb-1">
                                <i class="fas fa-sitemap"></i> Posisi Induk Parameter Ini
                            </label>
                            <select class="form-select form-select-sm border-success" id="root_parent_id" name="root_parent_id">
                                <!-- Diisi otomatis oleh JS (Bebas sampah baris matriks) -->
                            </select>
                            <small class="text-muted mt-1">Ubah pilihan ini hanya jika Anda ingin memindahkan seluruh blok parameter ini ke bawah kelompok lain.</small>
                        </div>

                        <!-- Wadah Pohon Parameter Rekursif (Tingkat 1 -> Tingkat 2 -> Tingkat 3 -> dst.) -->
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

    <!-- ===================================================================== -->
    <!-- MODAL 2: CRUD BARIS SAMPLE / LABEL FOREGOING (X, Y, Z, θ)             -->
    <!-- ===================================================================== -->
    <div class="modal fade" id="modalMatrixRow" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formMatrixRow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold" id="modalMatrixRowTitle">Tambah Baris Sample / Label</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="matrixRowMode" name="mode" value="add">
                        <input type="hidden" id="matrixOldLabel" name="old_label" value="">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Baris Sample / Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="matrixRowLabel" name="row_label" placeholder="Contoh: Y atau Sample 3" required>
                        </div>

                        <div class="mb-3" id="boxMatrixAfterRow">
                            <label class="form-label fw-bold">Letakkan Posisi Baris:</label>
                            <select class="form-select" id="matrixAfterRow" name="after_row">
                                <!-- Diisi otomatis oleh JS -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnSaveMatrixRow">
                            <i class="fas fa-check"></i> Simpan Baris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<script>
$(document).ready(function() {
    let currentRevisionId = null;
    window.currentParamsList = [];
    window.currentTreeParams = [];

    // Cegah warning aria-hidden focus Bootstrap
    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) document.activeElement.blur();
    });

    // =========================================================================
    // 1. LOGIKA CASCADING DROPDOWN FILTER
    // =========================================================================
    $('#deviceSelect').change(function() {
        if ($(this).val() !== '') {
            $('#docTypeSelect').prop('disabled', false).val('');
            $('#processSelect').empty().append('<option value="">-- Pilih Doc Type Dulu --</option>').prop('disabled', true);
            $('#btnLoadForm').prop('disabled', true);
        } else {
            $('#docTypeSelect').prop('disabled', true).val('');
            $('#processSelect').empty().append('<option value="">-- Pilih Device Dulu --</option>').prop('disabled', true);
        }
    });

    $('#docTypeSelect').change(function() {
        let device = $('#deviceSelect').val();
        let type   = $(this).val();

        if (type !== '') {
            $('#processSelect').html('<option>Loading...</option>').prop('disabled', true);
            $.ajax({
                url: "<?= base_url('form-builder/get-processes') ?>",
                type: "GET",
                data: { device_code: device, type: type },
                dataType: "JSON",
                success: function(res) {
                    $('#processSelect').empty();
                    if (res.length > 0) {
                        $('#processSelect').append('<option value="">-- Pilih Process --</option>');
                        $.each(res, function(i, item) {
                            $('#processSelect').append(`<option value="${item.process_code}">${item.name} (${item.process_code})</option>`);
                        });
                        $('#processSelect').prop('disabled', false);
                    } else {
                        $('#processSelect').append('<option value="">Data Kosong</option>');
                    }
                }
            });
        }
    });

    $('#processSelect').change(function() {
        $('#btnLoadForm').prop('disabled', $(this).val() === '');
    });

    // =========================================================================
    // 2. LOAD TABEL VISUAL BUILDER
    // =========================================================================
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
            url: '<?= base_url('form-builder/load') ?>',
            type: 'POST',
            data: {
                process: processCode,
                doc_type: docType,
                reparse: forceReparse ? 'true' : 'false'
            },
            dataType: 'json',
            success: function(response) {
                btn.html(originalText).prop('disabled', false);

                if (response.status === 'success') {
                    window.currentParamsList = response.params_list || [];
                    window.currentTreeParams = response.tree_params || [];
                    currentRevisionId = response.revision.id;

                    $('#lblRevisi').text('Revisi ' + response.revision.revisi);
                    $('#revisionHeader').removeClass('d-none');
                    $('#visualBuilderContainer').html(response.html);
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

    // =========================================================================
    // 3. HELPER DROPDOWN INDUK (BERSIH DARI SAMPAH BARIS MATRIKS X, Y, Z)
    // =========================================================================
    function collectSubtreeIds(node, arr) {
        if (!node) return;
        if (node.id) arr.push(parseInt(node.id));
        if (node.children && node.children.length > 0) {
            node.children.forEach(ch => collectSubtreeIds(ch, arr));
        }
    }

    function populateCleanParentDropdown(excludeIds = [], selectedParentId = '') {
        let dropdown = $('#root_parent_id');
        dropdown.empty();
        dropdown.append('<option value="">-- Jadikan Parameter Utama (Paling Atas / Kiri) --</option>');

        // Recursive walk pada tree_params agar menampilkan jalur hierarki yang rapi (contoh: Foregoing -> LD Chip)
        // dan mengabaikan sel baris posisi_grid (X, Y, Z, θ)
        function walkTree(nodes, prefix = '') {
            nodes.forEach(function(n) {
                if (n.posisi_grid) return; // Jangan pernah tampilkan sel baris matriks!
                if (excludeIds.includes(parseInt(n.id))) return;

                let fullPath = prefix ? (prefix + ' ➔ ' + n.nama_parameter) : n.nama_parameter;
                let isSelected = (String(n.id) === String(selectedParentId)) ? 'selected' : '';
                dropdown.append(`<option value="${n.id}" ${isSelected}>${fullPath}</option>`);

                if (n.children && n.children.length > 0) {
                    // Cek apakah anaknya bukan posisi_grid
                    if (!n.children[0].posisi_grid) {
                        walkTree(n.children, fullPath);
                    }
                }
            });
        }
        walkTree(window.currentTreeParams || []);
    }

    // Ambil daftar kolom input angka yang ada di form untuk pilihan referensi Rumus Otomatis (In & Out)
    function getAvailableNumberColumnsHtml(selectedCol = '') {
        let html = '<option value="">-- Pilih Kolom --</option>';
        let seen = {};
        (window.currentParamsList || []).forEach(function(p) {
            if (p.kolom_db && !p.posisi_grid && !seen[p.kolom_db]) {
                seen[p.kolom_db] = true;
                let sel = (p.kolom_db === selectedCol) ? 'selected' : '';
                html += `<option value="${p.kolom_db}" ${sel}>[${p.kolom_db}] ${p.nama_parameter}</option>`;
            }
        });
        return html;
    }

    // =========================================================================
    // 4. MESIN PEMBUAT KARTU REKURSIF TANPA BATAS (LEVEL 1 -> 2 -> 3 -> 4 DST.)
    // =========================================================================
    function createUnitRowHtml(unit = '', min = '', max = '') {
        return `
        <div class="row g-2 align-items-center mb-2 unit-option-row">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm inp-unit-name" placeholder="Nama Satuan (Cth: MPa / N / gr)" value="${unit}">
            </div>
            <div class="col-md-3">
                <input type="number" step="any" class="form-control form-control-sm inp-unit-min" placeholder="Batas Min (Opsional)" value="${min !== null && min !== undefined ? min : ''}">
            </div>
            <div class="col-md-3">
                <input type="number" step="any" class="form-control form-control-sm inp-unit-max" placeholder="Batas Max (Opsional)" value="${max !== null && max !== undefined ? max : ''}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-unit-row"><i class="fas fa-times"></i> Hapus</button>
            </div>
        </div>`;
    }

    function createDynamicStdRowHtml(label = '', value = '', min = '') {
        return `
        <div class="row g-2 align-items-center mb-2 dyn-std-option-row">
            <div class="col-md-5">
                <input type="text" class="form-control form-control-sm inp-dyn-label" placeholder="Pilihan Dropdown (Cth: 350 μm)" value="${label}">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm inp-dyn-val" placeholder="Kode (Cth: 350)" value="${value}">
            </div>
            <div class="col-md-2">
                <input type="number" step="any" class="form-control form-control-sm inp-dyn-min" placeholder="Min (Cth: 2.8)" value="${min !== null && min !== undefined ? min : ''}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 btn-remove-dyn-row"><i class="fas fa-times"></i></button>
            </div>
        </div>`;
    }

    function parseJsonSafe(val) {
        if (!val) return null;
        if (typeof val === 'object') return val;
        try { return JSON.parse(val); } catch (e) { return null; }
    }

    function createNodeCard(nodeData = {}, level = 1, isRootCard = false) {
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

        let card = $(`
        <div class="card shadow-sm mb-3 tree-node-card" data-level="${level}" data-id="${id}" data-kolom-db="${kolomDb}">
            <div class="card-header py-2 bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-${badgeColor} me-2">Tingkat ${level}</span>
                    <strong class="node-header-preview">${namaParam ? namaParam : 'Parameter Baru'}</strong>
                    ${kolomDb ? `<small class="text-primary ms-2">[${kolomDb}]</small>` : ''}
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success btn-add-subnode">
                        <i class="fas fa-plus"></i> Tambah Sub-Parameter (Tingkat ${level + 1})
                    </button>
                    ${!isRootCard ? `
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-subnode" title="Hapus Sub-Parameter Ini">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>` : ''}
                </div>
            </div>

            <div class="card-body py-3">
                <!-- BARIS UTAMA: NAMA & URUTAN -->
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

                <!-- =============================================================== -->
                <!-- BAGIAN A: PENGATURAN KELOMPOK / INDUK (MUNCUL JIKA PUNYA ANAK)  -->
                <!-- =============================================================== -->
                <div class="node-parent-settings p-2 rounded bg-light border mb-2 d-none">
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="form-check">
                            <input class="form-check-input chk-node-exclusive" type="checkbox" ${isExclusive ? 'checked' : ''}>
                            <label class="form-check-label fw-semibold small text-dark">
                                🔒 Hanya boleh diisi <u>salah satu</u> sub-parameter di bawahnya (Yang lain otomatis Gray Out)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input chk-node-dynstd" type="checkbox" ${hasDynStd ? 'checked' : ''}>
                            <label class="form-check-label fw-semibold small text-dark">
                                ⚙️ Gunakan Dropdown Penentu Standar Minimum (Seperti Jenis SM-Chip ➔ Min: - N)
                            </label>
                        </div>
                    </div>

                    <!-- Box Konfigurasi Dropdown Standar Dinamis -->
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

                <!-- =============================================================== -->
                <!-- BAGIAN B: PENGATURAN KOLOM ISIAN OPERATOR (JIKA TIDAK ADA ANAK) -->
                <!-- =============================================================== -->
                <div class="node-leaf-settings p-3 rounded bg-light border">
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">Bentuk Isian Operator</label>
                            <select class="form-select form-select-sm sel-node-tipe">
                                <option value="number" ${tipeInput === 'number' ? 'selected' : ''}>🔢 Angka (Ukuran / Qty / Tekanan)</option>
                                <option value="dropdown_ok_ng" ${tipeInput === 'dropdown_ok_ng' ? 'selected' : ''}>✅ Pilihan OK / NG (Dropdown)</option>
                                <option value="text" ${tipeInput === 'text' ? 'selected' : ''}>🔤 Teks Bebas (1 Baris)</option>
                                <option value="textarea" ${tipeInput === 'textarea' ? 'selected' : ''}>📝 Catatan Panjang (Kotak Besar)</option>
                                <option value="header_only" ${(!nodeData.tipe_input && !nodeData.kolom_db && id) ? 'selected' : ''}>📁 Hanya Teks / Label (Tanpa Input)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small mb-1">Keterangan / Acuan Tambahan (Opsional)</label>
                            <input type="text" class="form-control form-control-sm inp-node-standar" placeholder="Contoh: Harus bersih / Sesuai SOP" value="${standarNilai}">
                        </div>
                        <div class="col-md-3 pt-3">
                            <div class="form-check mt-1">
                                <input class="form-check-input chk-node-readonly" type="checkbox" ${isReadonly ? 'checked' : ''}>
                                <label class="form-check-label fw-bold small text-secondary">
                                    👁️ Read-Only (Terkunci)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- PENGATURAN KHUSUS TIPE ANGKA -->
                    <div class="box-number-extra mt-2 pt-2 border-top ${tipeInput === 'number' ? '' : 'd-none'}">
                        <div class="row g-2 mb-2">
                            <!-- Batas Min & Max Standar Tunggal -->
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Batas Minimum (Min)</label>
                                <input type="number" step="any" class="form-control form-control-sm inp-node-min" placeholder="Bebas" value="${batasMin}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1">Batas Maksimum (Max)</label>
                                <input type="number" step="any" class="form-control form-control-sm inp-node-max" placeholder="Bebas" value="${batasMax}">
                            </div>

                            <!-- Rumus Otomatis (NG / Yield %) -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1 text-primary"><i class="fas fa-calculator"></i> Hitung Otomatis (NG / Yield %)</label>
                                <select class="form-select form-select-sm sel-node-formula">
                                    <option value="" ${formulaType === '' ? 'selected' : ''}>-- Tidak Ada (Input Angka Manual) --</option>
                                    <option value="ng" ${formulaType === 'ng' ? 'selected' : ''}>⚡ Hitung NG Otomatis (In dikurangi Out)</option>
                                    <option value="yield" ${formulaType === 'yield' ? 'selected' : ''}>📊 Hitung Yield % Otomatis ((Out ÷ In) × 100)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Kolom In & Out Jika Rumus Otomatis Aktif -->
                        <div class="row g-2 mb-2 p-2 bg-white border border-primary rounded box-formula-cols ${formulaType !== '' ? '' : 'd-none'}">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Kolom Acuan "In" (Input):</label>
                                <input type="text" class="form-control form-control-sm inp-formula-in" value="${formulaInCol}" placeholder="Contoh: par013">
                                <small class="text-muted" style="font-size:10px;">Default Quantity In: <b>par013</b> (atau <b>par017</b> untuk AuSn)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold mb-1">Kolom Acuan "Out" (Output):</label>
                                <input type="text" class="form-control form-control-sm inp-formula-out" value="${formulaOutCol}" placeholder="Contoh: par014">
                                <small class="text-muted" style="font-size:10px;">Default Quantity Out: <b>par014</b> (atau <b>par018</b> untuk AuSn)</small>
                            </div>
                        </div>

                        <!-- Daftar Dropdown Satuan (MPa, kPa, N, gr, sec, ms) -->
                        <div class="mt-2 p-2 bg-white border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-dark">
                                    <i class="fas fa-ruler-combined text-success"></i> Dropdown Satuan Angka (Opsional — Tampil Menyatu di History)
                                </span>
                                <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 btn-add-unit-row">
                                    <i class="fas fa-plus"></i> Tambah Satuan
                                </button>
                            </div>
                            <small class="text-muted d-block mb-2" style="font-size:11px;">
                                Jika diisi (misal: <b>N</b> dan <b>gr</b>), Operator akan memilih satuan dari dropdown, dan di History otomatis digabung (contoh: <b>0.85 N</b>).
                            </small>
                            <div class="unit-rows-container"></div>
                        </div>
                    </div>
                </div>

                <!-- =============================================================== -->
                <!-- WADAH ANAK-ANAK SUB-PARAMETER (REKURSIF LEVEL BERIKUTNYA)       -->
                <!-- =============================================================== -->
                <div class="node-children-container mt-3 d-none"></div>
            </div>
        </div>`);

        // Isi baris satuan jika sudah ada
        let unitContainer = card.find('.unit-rows-container').first();
        if (Array.isArray(satuanOpsi) && satuanOpsi.length > 0) {
            satuanOpsi.forEach(u => {
                unitContainer.append(createUnitRowHtml(u.unit || '', u.min, u.max));
            });
        }

        // Isi baris standar dinamis jika sudah ada
        let dynContainer = card.find('.dynstd-rows-container').first();
        if (hasDynStd) {
            stdDinamis.options.forEach(opt => {
                dynContainer.append(createDynamicStdRowHtml(opt.label || '', opt.value || '', opt.min));
            });
        } else {
            // Default contoh 3 baris seperti SM-Chip
            dynContainer.append(createDynamicStdRowHtml('350 μm', '350', 2.8));
            dynContainer.append(createDynamicStdRowHtml('450 μm', '450', 3.6));
            dynContainer.append(createDynamicStdRowHtml('850 μm', '850', 8.7));
        }

        // Render anak-anaknya jika ada
        let childrenContainer = card.find('.node-children-container').first();
        if (children.length > 0) {
            children.forEach(ch => {
                childrenContainer.append(createNodeCard(ch, level + 1, false));
            });
        }

        syncCardMode(card);
        return card;
    }

    // Sinkronisasi tampilan Kartu: Apakah sedang punya Sub-Parameter (Anak) atau menjadi Kolom Input Ujung (Leaf)
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

    // Event Interaksi di Dalam Modal Rekursif
    $(document).on('input', '.inp-node-name', function() {
        let val = $(this).val().trim();$(this).closest('.tree-node-card').children('.card-header').find('.node-header-preview').text(val || 'Parameter Baru');
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

    // Tambah & Hapus Baris Satuan
    $(document).on('click', '.btn-add-unit-row', function() {$(this).closest('.border').find('.unit-rows-container').first().append(createUnitRowHtml());
    });
    $(document).on('click', '.btn-remove-unit-row', function() {$(this).closest('.unit-option-row').remove();
    });

    // Tambah & Hapus Baris Standar Dinamis
    $(document).on('click', '.btn-add-dynstd-row', function() {$(this).siblings('.dynstd-rows-container').append(createDynamicStdRowHtml());
    });
    $(document).on('click', '.btn-remove-dynstd-row, .btn-remove-dyn-row', function() {$(this).closest('.dyn-std-option-row').remove();
    });

    // Klik Tombol [+ Tambah Sub-Parameter (Tingkat X+1)]
    $(document).on('click', '.btn-add-subnode', function() {
        let parentCard = $(this).closest('.tree-node-card');
        let parentLevel = parseInt(parentCard.attr('data-level')) || 1;
        let childrenContainer = parentCard.children('.card-body').children('.node-children-container');
        let nextOrder = childrenContainer.children('.tree-node-card').length + 1;

        let newChildCard = createNodeCard({ urutan: nextOrder, tipe_input: 'number' }, parentLevel + 1, false);
        childrenContainer.append(newChildCard);
        syncCardMode(parentCard);
    });

    // Klik Tombol [✕ Hapus] pada Sub-Parameter (Bisa dibatalkan saat itu juga!)
    $(document).on('click', '.btn-remove-subnode', function() {
        let currentCard = $(this).closest('.tree-node-card');
        let parentCard  = currentCard.parent('.node-children-container').closest('.tree-node-card');
        currentCard.remove();
        if (parentCard.length > 0) {
            syncCardMode(parentCard);
        }
    });

    // Ekstrak Data JSON Rekursif dari Kartu DOM saat Tombol Simpan Ditekan
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

        // Standar Dinamis (Jika dicentang pada Induk)
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

        let selTipe    = leafSettings.find('.sel-node-tipe').first().val();
        let jenisElemen = (hasChildren || selTipe === 'header_only') ? 'header' : 'input';
        let tipeInput   = (jenisElemen === 'input') ? selTipe : null;
        let standarNilai = leafSettings.find('.inp-node-standar').first().val().trim();
        let isReadonly   = leafSettings.find('.chk-node-readonly').first().is(':checked') ? 1 : 0;
        let batasMin     = leafSettings.find('.inp-node-min').first().val();
        let batasMax     = leafSettings.find('.inp-node-max').first().val();

        // Satuan Opsi
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

        // Rumus Otomatis
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

    // =========================================================================
    // 5. BUKA MODAL: TAMBAH UTAMA, TAMBAH SUB-PARAMETER, & EDIT POHON PARAMETER
    // =========================================================================

    // A. Tambah Parameter Utama (Induk Tingkat 1)
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

    // B. Tambah Sub-Parameter langsung di bawah Parameter Tertentu
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

    // C. Edit Parameter & Seluruh Sub-Bagian di Bawahnya
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

    // D. Simpan Pohon Parameter ke Controller saveTreeParameter
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

    // E. Hapus Parameter
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

    // =========================================================================
    // 6. AKSI CRUD BARIS SAMPLE / LABEL FOREGOING (X, Y, Z, θ)
    // =========================================================================
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
            afterSelect.append(`<option value="${r}" selected>Setelah Baris "${r}"</option>`);
        });

        $('#boxMatrixAfterRow').removeClass('d-none');
        $('#modalMatrixRowTitle').text('Tambah Baris Sample / Label Baru');
        $('#modalMatrixRow').modal('show');
    });

    $(document).on('click', '.btn-edit-matrix-header', function() {
        let lbl = $(this).data('label') || 'Sample / Label';
        $('#matrixRowMode').val('edit_header');
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
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Simpan Baris');
                if (res.status === 'success') {
                    $('#modalMatrixRow').modal('hide');
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal menyimpan baris.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Simpan Baris');
                console.error(xhr.responseText);
                alert('Gagal menyimpan baris matriks.');
            }
        });
    });

    $(document).on('click', '.btn-delete-matrix-row', function() {
        let lbl = $(this).data('label');
        if (confirm(`Yakin ingin menghapus seluruh baris "${lbl}" beserta semua kotak input di baris tersebut?`)) {
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

    // =========================================================================
    // 7. AKSI NAIKKAN REVISI
    // =========================================================================
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
});
</script>
<?= $this->endSection(); ?>