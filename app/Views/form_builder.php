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
                <!-- 1. CASCADING DROPDOWN FILTER (Seragam dengan Input & History) -->
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
                    <div class="col-md-4">
                        <label class="font-weight-bold">2. Process</label>
                        <select id="processNameSelect" class="form-select" onchange="onFbProcessNameChange()" disabled>
                            <option value="">-- Pilih Device Dulu --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold">3. Doc. Type</label>
                        <select id="processSelect" class="form-select" onchange="onFbProcessChange()" disabled>
                            <option value="">-- Pilih Process Dulu --</option>
                        </select>
                        <input type="hidden" id="docTypeSelect" value="">
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
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <h5 class="m-0 font-weight-bold">
                                Status Dokumen: <span id="lblRevisi" class="badge bg-danger fs-6">Revisi -</span>
                            </h5>
                            <select id="fbRevisionSelect" class="form-select form-select-sm border-secondary fw-semibold" style="width: auto; min-width: 175px;" title="Ganti / Lihat Versi Revisi Terdahulu"></select>
                        </div>
                        <small class="text-muted">Gunakan tombol panah pada tabel untuk menggeser urutan kolom, atau tombol di kanan untuk preview, clone, standar model, &amp; naik revisi.</small>
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

                        <!-- FILTER & PENGATUR STANDAR MODEL UNIVERSAL -->
                        <div id="foregoingModelBar" class="d-none d-flex align-items-center gap-1 bg-light p-1 rounded border border-primary me-1">
                            <span class="badge bg-primary"><i class="fas fa-microchip"></i> Model:</span>
                            <select id="fbModelSelect" class="form-select form-select-sm" style="min-width: 165px;">
                                <option value="">-- Pilih Model --</option>
                            </select>
                            <button type="button" class="btn btn-primary btn-sm" id="btnOpenModelStd" title="Atur Standar Min/Max untuk Model ini">
                                <i class="fas fa-sliders-h"></i> Standar Model
                            </button>
                            <button type="button" class="btn btn-outline-dark btn-sm" id="btnManageModels" title="Tambah / Edit / Hapus Daftar Model">
                                <i class="fas fa-cog"></i> Kelola Model
                            </button>
                        </div>

                        <button type="button" class="btn btn-info text-white btn-sm fw-bold" id="btnOpenLivePreview" title="Simulasi Langsung Tampilan Form Input Operator & Tabel History">
                            <i class="fas fa-eye"></i> Live Preview
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnOpenCloneForm" title="Salin Struktur Parameter & SOP dari Proses Lain">
                            <i class="fas fa-clone"></i> Clone dari Proses Lain
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" id="btnOpenSopNote" title="Atur Catatan SOP & Mode Layout Form">
                            <i class="fas fa-sticky-note"></i> Catatan SOP & Layout
                        </button>
                        <button type="button" class="btn btn-dark btn-sm" id="btnOpenExportHardcode" title="Lihat, Salin, atau Download Hard Code PHP (Input & History) tanpa mengubah file project">
                            <i class="fas fa-code text-warning"></i> Export / Salin Hard Code
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btnExportCsv" title="Download Template CSV atau Export Parameter Saat Ini">
                            <i class="fas fa-file-csv"></i> Download CSV
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="btnOpenImportCsv" title="Upload CSV untuk meregenerate parameter secara otomatis">
                            <i class="fas fa-file-upload"></i> Upload CSV (Regenerate)
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResyncHtml" title="Reset mengikuti file form HTML bawaan">
                            <i class="fas fa-sync-alt"></i> Tarik Ulang dari File Asli
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btnAddNewTable" title="Tambah Bagian / Tabel Baru (Pilih bentuk Mendatar, Menurun, atau Matriks seperti Foregoing)">
                            <i class="fas fa-table"></i> + Tambah Tabel Baru
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

    <!-- MODAL 3: PENGATUR STANDAR PER MODEL (UNIVERSAL) -->
    <div class="modal fade" id="modalModelStd" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formModelStd">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-sliders-h"></i> Standar Model: <span id="lblModalModelName" class="badge bg-warning text-dark fs-6">-</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="stdModelId">
                        <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-2 rounded border">
                            <small class="text-muted">Atur batas <b>Min</b> dan <b>Max</b> untuk setiap parameter/posisi pada model ini. Jika tidak diukur (seperti baris Y atau Point tertentu pada model tertentu), matikan centang <b>Aktif</b>.</small>
                            <div class="d-flex align-items-center gap-1" style="min-width: 240px;">
                                <select id="copyFromModelSelect" class="form-select form-select-sm">
                                    <option value="">-- Copy Standar dari Model Lain --</option>
                                </select>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="30%">Item / Parameter</th>
                                    <th width="15%">Status Ukur</th>
                                    <th width="27%">Batas Minimum (Min)</th>
                                    <th width="28%">Batas Maksimum (Max)</th>
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
    <!-- MODAL 5: PENGATURAN CATATAN SOP & MODE LAYOUT -->
    <div class="modal fade" id="modalSopNote" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formSopNote">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-sticky-note"></i> Pengaturan Catatan SOP & Mode Layout
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mode Layout Tampilan:</label>
                            <select class="form-select" id="sopLayoutModeSelect" name="layout_mode">
                                <option value="standard">Standard (Tampilan Normal Tunggal)</option>
                                <option value="matrix_grid">Matriks 2D Grid (Multiple Rows seperti Front/Back pada LDSM SL56)</option>
                            </select>
                            <small class="text-muted">Pilih <b>Matriks 2D Grid</b> jika form ini membutuhkan baris bertingkat banyak (seperti Front 1, Front 2, Back 1, Back 2).</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan SOP / Kriteria Inspeksi (Tampil di Bawah Form Input & History):</label>
                            <textarea class="form-control" id="sopCatatanText" name="catatan_sop" rows="6" placeholder="Contoh:&#10;@ : Tulis hasil pemeriksaan sesuai angka alat ukur.&#10;* : Beri tanda V jika OK dan X jika NG.&#10;# : Isi note apabila ada penggantian part."></textarea>
                            <small class="text-muted">Catatan ini akan otomatis muncul pada form input operator dan lembar history pemeriksaan.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white px-4" id="btnSaveSopNote">
                            <i class="fas fa-save"></i> Simpan Catatan & Mode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL 6: IMPORT & REGENERATE PARAMETER DARI CSV -->
    <div class="modal fade" id="modalImportCsv" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formImportCsv" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-file-csv"></i> Upload CSV & Regenerate Parameter
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="fas fa-info-circle me-1"></i> <b>Fitur Cepat Revisi:</b> Jika Anda memiliki revisi hard file atau daftar parameter di Excel, Anda dapat langsung menyimpannya sebagai file <b>.csv</b> dan menguploadnya di sini. Sistem akan otomatis meregenerate seluruh struktur parameter form.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih File CSV <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="csvFileInput" name="csv_file" accept=".csv,text/csv" required>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted">Mendukung format koma (,) maupun titik koma (;) dari Microsoft Excel.</small>
                                <a href="javascript:void(0)" id="btnDownloadTemplateInModal" class="small text-decoration-none fw-bold">
                                    <i class="fas fa-download"></i> Download Template CSV
                                </a>
                            </div>
                        </div>

                        <div class="card p-3 border mb-3 bg-white">
                            <label class="form-label fw-bold mb-2">Metode Import:</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="import_mode" id="modeReplace" value="replace" checked>
                                <label class="form-check-label fw-bold text-danger" for="modeReplace">
                                    <i class="fas fa-sync-alt"></i> Regenerate Total (Rekomendasi)
                                </label>
                                <div class="text-muted small ps-4">
                                    Menghapus parameter lama pada revisi ini dan menggantikannya secara bersih dengan isi file CSV.
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="import_mode" id="modeAppend" value="append">
                                <label class="form-check-label fw-bold text-success" for="modeAppend">
                                    <i class="fas fa-plus"></i> Tambah ke Bawah (Append)
                                </label>
                                <div class="text-muted small ps-4">
                                    Mempertahankan parameter yang sudah ada dan menambahkan parameter dari CSV ke bagian bawah.
                                </div>
                            </div>
                        </div>

                        <div class="accordion" id="accCsvGuide">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingGuide">
                                    <button class="accordion-button collapsed py-2 small fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuide">
                                        <i class="fas fa-question-circle text-primary me-2"></i> Petunjuk Kolom Header CSV
                                    </button>
                                </h2>
                                <div id="collapseGuide" class="accordion-collapse collapse" data-bs-parent="#accCsvGuide">
                                    <div class="accordion-body small bg-light" style="line-height: 1.6;">
                                        <ul>
                                            <li><b>urutan</b>: Nomor urut baris (1, 2, 3, ...).</li>
                                            <li><b>parent_urutan</b>: Kosongkan jika parameter utama. Jika merupakan sub-kolom, isi dengan nomor <b>urutan</b> induknya.</li>
                                            <li><b>nama_parameter</b>: Nama parameter / header (Wajib diisi).</li>
                                            <li><b>kolom_db</b>: Nama kolom database (opsional, contoh: <code>par001</code>). Jika dikosongkan, sistem akan otomatis membuat kode berikutnya.</li>
                                            <li><b>tipe_input</b>: <code>number</code>, <code>dropdown_ok_ng</code>, <code>dropdown_custom</code>, <code>auto_judgment</code>, <code>text</code>, <code>textarea</code>, <code>time</code>, <code>date</code>, <code>header_only</code>.</li>
                                            <li><b>standar_nilai</b>: Keterangan standar teks (contoh: <code>0.2 ~ 0.6 mm</code>).</li>
                                            <li><b>batas_min</b> &amp; <b>batas_max</b>: Nilai numerik untuk validasi (contoh: <code>0.2</code> dan <code>0.6</code>).</li>
                                            <li><b>step_value</b>: Presisi desimal (contoh: <code>any</code>, <code>1</code>, <code>0.1</code>, <code>0.001</code>, <code>0.00000001</code>).</li>
                                            <li><b>rentang_ganda</b>: Toleransi dua rentang (contoh: <code>40,60,25,45</code>).</li>
                                            <li><b>satuan_opsi</b>: Pilihan dropdown satuan (contoh: <code>N:0.6:1.0 | gr:60:100</code>).</li>
                                            <li><b>opsi_dropdown</b>: Opsi kustom dipisahkan koma untuk dropdown (contoh: <code>V,A,CP,WP</code>).</li>
                                            <li><b>target_evaluasi</b>: Target auto-judgment (<code>*</code> untuk semua, atau <code>par001,par002</code>).</li>
                                            <li><b>rumus_otomatis</b>: Hitung otomatis (contoh: <code>ng:par013,par014</code> atau <code>yield:par013,par014</code>).</li>
                                            <li><b>is_readonly</b>: Isi <code>1</code> jika readonly, <code>0</code> jika normal.</li>
                                            <li><b>is_exclusive</b>: Isi <code>1</code> jika sub-parameternya saling mengunci (gray out).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmitImportCsv">
                            <i class="fas fa-upload"></i> Mulai Regenerate Parameter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL 6: TAMBAH BAGIAN / TABEL BARU -->
    <div class="modal fade" id="modalNewTable" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formNewTable">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-table"></i> Tambah Bagian / Tabel Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama / Judul Tabel Baru: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newTableName" placeholder="Contoh: Quantity, Visual Inspection, Dimensi, Die Bonding Position" required>
                            <small class="text-muted">Nama ini akan menjadi judul bagian tabel terpisah di form.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Bentuk / Tipe Layout Tabel: <span class="text-danger">*</span></label>
                            <select class="form-select" id="newTableLayoutType" required>
                                <option value="horizontal">1. Tabel Mendatar (Single Row / Horizontal Card) - Cocok untuk Quantity (IN/OUT), Ringkasan Hasil, Parameter Tunggal</option>
                                <option value="matrix">2. Tabel Matriks Grid 2D (seperti Foregoing / Production Precision) - Pengukuran Berulang per Baris Sampel</option>
                                <option value="vertical">3. Tabel Menurun (Vertikal Checklist) - Item Pemeriksaan Startup ke Bawah</option>
                            </select>
                        </div>

                        <!-- OPSI KHUSUS MATRIKS GRID 2D -->
                        <div id="boxMatrixOptions" class="card p-3 border mb-3 bg-white d-none">
                            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-th"></i> Konfigurasi Baris Matriks Grid 2D</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilihan Template Baris Sampel:</label>
                                <select class="form-select" id="selMatrixTemplate">
                                    <option value="front_back">Front 1, Front 2, Back 1, Back 2 (Standar Production LDSM / WB)</option>
                                    <option value="xyzt">X, Y, Z, θ (Standar Foregoing Die Bond)</option>
                                    <option value="samples_5">Sample 1, Sample 2, Sample 3, Sample 4, Sample 5</option>
                                    <option value="custom">Kustom Sendiri (Ketik manual)</option>
                                </select>
                            </div>
                            <div class="mb-3" id="boxCustomMatrixRows">
                                <label class="form-label fw-bold">Daftar Baris Sampel (Pisahkan dengan koma):</label>
                                <input type="text" class="form-control" id="inpCustomMatrixRows" value="Front 1, Front 2, Back 1, Back 2">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Nama Kolom Parameter Pertama:</label>
                                <input type="text" class="form-control" id="inpMatrixFirstCol" placeholder="Contoh: Point 1 (θc) atau Result">
                            </div>
                        </div>

                        <!-- OPSI KHUSUS MENDATAR / MENURUN -->
                        <div id="boxSimpleOptions" class="card p-3 border mb-3 bg-white">
                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sliders-h"></i> Parameter Awal</h6>
                            <div class="row">
                                <div class="col-md-7 mb-2">
                                    <label class="form-label fw-bold">Nama Parameter / Kolom Pertama:</label>
                                    <input type="text" class="form-control" id="inpSimpleParamName" placeholder="Contoh: IN atau Start up Check Item">
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label class="form-label fw-bold">Tipe Input:</label>
                                    <select class="form-select" id="selSimpleTipeInput">
                                        <option value="number">Angka (Number)</option>
                                        <option value="dropdown_ok_ng">Dropdown OK / NG (V / X)</option>
                                        <option value="text">Teks Bebas</option>
                                        <option value="textarea">Catatan / Keterangan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-4" id="btnSubmitNewTable">
                            <i class="fas fa-plus"></i> Buat Tabel Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 7: EXPORT / SALIN HARD CODE PHP (INPUT & HISTORY) -->
    <div class="modal fade" id="modalExportHardcode" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-code text-warning me-2"></i>Export / Salin Hard Code PHP &mdash;
                        <span id="lblHardcodeProc" class="badge bg-warning text-dark">-</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="alert alert-secondary py-2 px-3 small mb-3 border-start border-4 border-dark">
                        <i class="fas fa-shield-alt text-dark me-1"></i>
                        <b>Aman (Read-Only):</b> Fitur ini <b>tidak mengubah atau menimpa file apa pun</b> di dalam folder project Anda. Anda dapat menyalin kode atau mendownload file <code>.php</code> secara manual jika dibutuhkan.
                    </div>

                    <ul class="nav nav-tabs" id="hardcodeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="tab-hc-input" data-bs-toggle="tab" data-bs-target="#pane-hc-input" type="button" role="tab">
                                <i class="fas fa-keyboard text-primary me-1"></i> 1. Hard Code Form Input
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="tab-hc-history" data-bs-toggle="tab" data-bs-target="#pane-hc-history" type="button" role="tab">
                                <i class="fas fa-table text-success me-1"></i> 2. Hard Code Tabel History
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content bg-white border border-top-0 p-3 rounded-bottom shadow-sm">
                        <!-- TAB 1: INPUT CODE -->
                        <div class="tab-pane fade show active" id="pane-hc-input" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="small text-muted">
                                    Referensi lokasi file: <code id="lblInputRefPath" class="fw-bold text-dark">-</code>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold" id="btnCopyInputCode">
                                        <i class="fas fa-copy"></i> Salin Kode Input
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm fw-bold" id="btnDownloadInputCode">
                                        <i class="fas fa-download"></i> Download File Input (.php)
                                    </button>
                                </div>
                            </div>
                            <textarea id="txtHardcodeInput" class="form-control font-monospace bg-dark text-light" rows="17" readonly spellcheck="false" style="font-size: 12px; line-height: 1.45;"></textarea>
                        </div>

                        <!-- TAB 2: HISTORY CODE -->
                        <div class="tab-pane fade" id="pane-hc-history" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="small text-muted">
                                    Referensi lokasi file: <code id="lblHistoryRefPath" class="fw-bold text-dark">-</code>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnCopyHistoryCode">
                                        <i class="fas fa-copy"></i> Salin Kode History
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm fw-bold" id="btnDownloadHistoryCode">
                                        <i class="fas fa-download"></i> Download File History (.php)
                                    </button>
                                </div>
                            </div>
                            <textarea id="txtHardcodeHistory" class="form-control font-monospace bg-dark text-light" rows="17" readonly spellcheck="false" style="font-size: 12px; line-height: 1.45;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 8: LIVE PREVIEW INTERAKTIF (SIMULASI INPUT OPERATOR & TABEL HISTORY) -->
    <div class="modal fade" id="modalLivePreview" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-eye me-2"></i>Live Preview Form &mdash;
                        <span id="lblLivePreviewProc" class="badge bg-warning text-dark">-</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <ul class="nav nav-tabs" id="livePreviewTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="tab-lp-input" data-bs-toggle="tab" data-bs-target="#pane-lp-input" type="button" role="tab">
                                <i class="fas fa-keyboard text-primary me-1"></i> 1. Simulasi Form Input Operator
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="tab-lp-history" data-bs-toggle="tab" data-bs-target="#pane-lp-history" type="button" role="tab">
                                <i class="fas fa-table text-success me-1"></i> 2. Simulasi Tabel History (Data Contoh)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content bg-white border border-top-0 p-3 rounded-bottom shadow-sm">
                        <div class="tab-pane fade show active" id="pane-lp-input" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <div class="alert alert-info py-1 px-2 small mb-0 flex-grow-1">
                                    <i class="fas fa-bolt me-1"></i> Anda dapat langsung mencoba mengetik angka (menguji warna merah Min/Max, rumus NG/Yield, kuncian <i>Isi Salah Satu</i>, hingga <i>Auto-Judgment</i>) tanpa menyimpan ke database produksi.
                                </div>
                                <div id="lpStandbyWrapper" class="form-check form-switch bg-warning bg-opacity-10 px-3 py-1 rounded border border-warning shadow-sm d-none">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="is-standby">
                                    <label class="form-check-label small fw-bold text-dark" for="is-standby">
                                        Simulasi Mode Mesin Stand By
                                    </label>
                                </div>
                            </div>
                            <div id="livePreviewInputContainer" class="overflow-auto"></div>
                        </div>
                        <div class="tab-pane fade" id="pane-lp-history" role="tabpanel">
                            <div class="alert alert-success py-1 px-2 small mb-2">
                                <i class="fas fa-check-circle me-1"></i> Pratinjau keselarasan kolom header dan baris data pada halaman History, Export Excel, &amp; PDF.
                            </div>
                            <div id="livePreviewHistoryContainer" class="overflow-auto"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup Preview</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 9: CLONE / SALIN STRUKTUR PARAMETER DARI PROSES LAIN -->
    <div class="modal fade" id="modalCloneForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCloneProcess">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-clone me-2"></i>Clone Struktur dari Proses Lain
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="alert alert-warning py-2 px-3 small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <b>Perhatian:</b> Menyalin struktur akan menggantikan seluruh parameter &amp; Catatan SOP pada <b id="lblCloneTargetRev">Revisi Ini</b> dengan salinan dari proses sumber yang Anda pilih.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">1. Device: <span class="text-danger">*</span></label>
                            <select id="cloneDeviceSelect" class="form-select" required>
                                <option value="">-- Pilih Device --</option>
                                <?php foreach ($devices as $d): ?>
                                    <?php $devCode = $d['code'] ?? $d['device_code'] ?? ''; ?>
                                    <option value="<?= esc($devCode) ?>"><?= esc($d['name'] ?? $devCode) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">2. Process: <span class="text-danger">*</span></label>
                            <select id="cloneProcessNameSelect" class="form-select" required disabled>
                                <option value="">-- Pilih Device Dulu --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">3. Doc. Type: <span class="text-danger">*</span></label>
                            <select id="cloneProcessSelect" class="form-select" required disabled>
                                <option value="">-- Pilih Process Dulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmitCloneForm" disabled>
                            <i class="fas fa-copy"></i> Salin Struktur Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- SCRIPT 1: FUNGSI GLOBAL DROPDOWN FILTER (Seragam dengan Input & History) -->
<script>
var fbProcessRows = [];
var TYPE_ORDER = {production: 1, startup: 2, foregoing: 3};
var TYPE_LABEL = {production: 'Production', startup: 'Startup', foregoing: 'Foregoing'};

function splitName(name) {
    name = (name || '').trim();
    var i = name.indexOf(' Type ');
    if (i === -1) return { base: name, variant: '' };
    return { base: name.substring(0, i).trim(), variant: name.substring(i + 1).trim() };
}

function stripRevision(docno) {
    var p = (docno || '').split('-');
    return p.length >= 4 ? p.slice(0, -1).join('-') : (docno || '');
}

function onFbDeviceChange() {
    var devEl     = document.getElementById('deviceSelect');
    var procName  = document.getElementById('processNameSelect');
    var procEl    = document.getElementById('processSelect');
    var docEl     = document.getElementById('docTypeSelect');
    var btnEl     = document.getElementById('btnLoadForm');

    fbProcessRows = [];
    if (docEl) docEl.value = '';
    if (btnEl) btnEl.disabled = true;

    if (!devEl || devEl.value === '') {
        if (procName) {
            procName.innerHTML = '<option value="">-- Pilih Device Dulu --</option>';
            procName.disabled = true;
        }
        if (procEl) {
            procEl.innerHTML = '<option value="">-- Pilih Process Dulu --</option>';
            procEl.disabled = true;
        }
        return;
    }

    var device = devEl.value;
    if (procName) {
        procName.innerHTML = '<option value="">Loading daftar proses...</option>';
        procName.disabled = true;
    }
    if (procEl) {
        procEl.innerHTML = '<option value="">-- Pilih Process Dulu --</option>';
        procEl.disabled = true;
    }

    fetch("<?= base_url('process/list') ?>?device=" + encodeURIComponent(device), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        fbProcessRows = data || [];
        populateFbProcessNames();
    })
    .catch(function(err) {
        console.error('Gagal memuat proses:', err);
        if (procName) {
            procName.innerHTML = '<option value="">Gagal memuat proses</option>';
            procName.disabled = true;
        }
    });
}

function populateFbProcessNames() {
    var procName = document.getElementById('processNameSelect');
    if (!procName) return;

    var names = [];
    fbProcessRows.forEach(function(r) {
        var base = splitName(r.process_name || r.name).base;
        if (base && names.indexOf(base) === -1) names.push(base);
    });
    names.sort();

    procName.innerHTML = '';
    if (names.length === 0) {
        procName.innerHTML = '<option value="">-- Data Kosong --</option>';
        procName.disabled = true;
    } else {
        procName.innerHTML = '<option value="">-- Pilih Process --</option>';
        names.forEach(function(n) {
            var opt = document.createElement('option');
            opt.value = n;
            opt.textContent = n;
            procName.appendChild(opt);
        });
        procName.disabled = false;
    }
}

function onFbProcessNameChange() {
    var procName = document.getElementById('processNameSelect');
    var procEl   = document.getElementById('processSelect');
    var docEl    = document.getElementById('docTypeSelect');
    var btnEl    = document.getElementById('btnLoadForm');

    if (docEl) docEl.value = '';
    if (btnEl) btnEl.disabled = true;

    if (!procName || procName.value === '') {
        if (procEl) {
            procEl.innerHTML = '<option value="">-- Pilih Process Dulu --</option>';
            procEl.disabled = true;
        }
        return;
    }

    var chosen = procName.value;
    var rows = fbProcessRows.filter(function(r) {
        return splitName(r.process_name || r.name).base === chosen;
    });

    rows.sort(function(a, b) {
        var ta = TYPE_ORDER[a.type] || 9, tb = TYPE_ORDER[b.type] || 9;
        if (ta !== tb) return ta - tb;
        return String(a.docno).localeCompare(String(b.docno));
    });

    if (!procEl) return;
    procEl.innerHTML = '';

    if (rows.length === 0) {
        procEl.innerHTML = '<option value="">-- Dokumen Tidak Ditemukan --</option>';
        procEl.disabled = true;
    } else {
        procEl.innerHTML = '<option value="">-- Pilih Dokumen / Doc Type --</option>';
        rows.forEach(function(r) {
            var variant = splitName(r.process_name || r.name).variant;
            var label = stripRevision(r.docno) + ' (' + (TYPE_LABEL[r.type] || r.type) + ')' + (variant ? ' - ' + variant : '');
            var opt = document.createElement('option');
            opt.value = r.process_code;
            opt.textContent = label;
            opt.setAttribute('data-type', r.type);
            opt.setAttribute('data-docno', r.docno);
            opt.setAttribute('data-name', r.process_name || r.name);
            procEl.appendChild(opt);
        });
        procEl.disabled = false;
    }
}

function onFbProcessChange() {
    var procEl = document.getElementById('processSelect');
    var docEl  = document.getElementById('docTypeSelect');
    var btnEl  = document.getElementById('btnLoadForm');

    if (procEl && procEl.value !== '') {
        var selectedOpt = procEl.options[procEl.selectedIndex];
        var docType = selectedOpt ? selectedOpt.getAttribute('data-type') : '';
        if (docEl) docEl.value = docType || '';
        if (btnEl) btnEl.disabled = false;
    } else {
        if (docEl) docEl.value = '';
        if (btnEl) btnEl.disabled = true;
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
    let sessionAlertShown = false;
    window.currentParamsList = [];
    window.currentTreeParams = [];
    window.currentModelsList = [];

    // Proteksi Global Sesi Berakhir (HTTP 401 JSON) pada seluruh request AJAX Form Builder
    $(document).ajaxError(function(event, xhr) {
        if (xhr && xhr.status === 401 && !sessionAlertShown) {
            sessionAlertShown = true;
            let msg = 'Sesi login Admin Anda telah berakhir. Halaman akan diarahkan ke halaman Login.';
            let redirectUrl = "<?= base_url('login') ?>";
            try {
                let res = JSON.parse(xhr.responseText);
                if (res && res.message) msg = res.message;
                if (res && res.redirect) redirectUrl = res.redirect;
            } catch (e) {}
            alert(msg);
            window.location.href = redirectUrl;
        }
    });

    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) document.activeElement.blur();
    });

    // LOAD TABEL VISUAL BUILDER
    function loadTableData(forceReparse = false, specificRevisionId = null) {
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
                reparse: forceReparse ? 'true' : 'false',
                revision_id: specificRevisionId || ''
            },
            dataType: "json",
            success: function(response) {
                btn.html(originalText).prop('disabled', false);

                if (response.status === 'success') {
                    window.currentParamsList = response.params_list || [];
                    window.currentTreeParams = response.tree_params || [];
                    window.currentRevision   = response.revision || null;
                    currentRevisionId = response.revision.id;

                    let isLatest = (response.is_latest_revision !== false);
                    $('#lblRevisi')
                        .text('Revisi ' + response.revision.revisi + (isLatest ? '' : ' (Arsip)'))
                        .toggleClass('bg-danger', isLatest)
                        .toggleClass('bg-secondary', !isLatest);

                    // Isi dropdown riwayat revisi
                    let revSel = $('#fbRevisionSelect');
                    revSel.empty();
                    let revsList = response.revisions_list || [response.revision];
                    revsList.forEach(function(r, idx) {
                        let tag = (idx === 0) ? ' (Aktif Terbaru)' : ' (Arsip)';
                        let sel = (String(r.id) === String(currentRevisionId)) ? 'selected' : '';
                        revSel.append('<option value="' + r.id + '" ' + sel + '>Revisi ' + r.revisi + tag + '</option>');
                    });

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
                if (xhr && xhr.status === 401) return;
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

    function getEvaluatableLeafOptions(excludeId) {
        excludeId = excludeId ? String(excludeId) : '';
        let items = [];
        let seenCols = {};

        function walk(nodes, prefix) {
            (nodes || []).forEach(function(n) {
                if (excludeId && String(n.id) === excludeId) return;
                let curName = (n.nama_parameter || '').trim();
                let fullPath = prefix ? (prefix + ' ➔ ' + curName) : curName;

                let hasGridKids = (Array.isArray(n.children) && n.children.length > 0 && n.children[0].posisi_grid);
                if (hasGridKids) {
                    n.children.forEach(function(gc) {
                        let cDb = (gc.kolom_db || '').trim().toLowerCase();
                        if (cDb && !seenCols[cDb]) {
                            seenCols[cDb] = true;
                            items.push({
                                col: cDb,
                                label: fullPath + ' (' + gc.posisi_grid + ')'
                            });
                        }
                    });
                    return;
                }

                if (Array.isArray(n.children) && n.children.length > 0) {
                    walk(n.children, fullPath);
                    return;
                }

                let tipe = (n.tipe_input || '').toLowerCase();
                let cDb  = (n.kolom_db || '').trim().toLowerCase();
                let nmLw = curName.toLowerCase();
                if (!cDb || tipe === 'header_only' || tipe === 'auto_judgment') return;
                if (nmLw.indexOf('note') !== -1 || nmLw.indexOf('catatan') !== -1) return;

                if (!seenCols[cDb]) {
                    seenCols[cDb] = true;
                    items.push({
                        col: cDb,
                        label: fullPath
                    });
                }
            });
        }

        walk(window.currentTreeParams || [], '');
        return items;
    }

    function syncEvalTargetBox(evalBox) {
        let modeSel = evalBox.find('.sel-node-eval-mode').first();
        let customWrap = evalBox.find('.box-eval-custom-picker').first();
        let hiddenInp = evalBox.find('.inp-node-target-eval').first();
        let countBadge = evalBox.find('.lbl-eval-selected-count').first();

        if (modeSel.val() === '*') {
            customWrap.addClass('d-none');
            hiddenInp.val('*');
        } else {
            customWrap.removeClass('d-none');
            let checkedCols = [];
            customWrap.find('.chk-eval-col-item:checked').each(function() {
                checkedCols.push($(this).val());
            });
            countBadge.text(checkedCols.length + ' parameter dipilih');
            hiddenInp.val(checkedCols.length > 0 ? checkedCols.join(',') : '*');
        }
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
        let isRequired    = parseInt(nodeData.is_required || 0) === 1;
        let isExclusive   = parseInt(nodeData.is_exclusive || 0) === 1;
        let satuanOpsi    = parseJsonSafe(nodeData.satuan_opsi) || [];
        let rumusOtomatis = parseJsonSafe(nodeData.rumus_otomatis) || {};
        let stdDinamis    = parseJsonSafe(nodeData.standar_dinamis) || null;
        let children      = Array.isArray(nodeData.children) ? nodeData.children : [];
        let opsiDropdown   = nodeData.opsi_dropdown || '';
        let stepValue      = nodeData.step_value || 'any';
        let rentangGanda   = nodeData.rentang_ganda || '';
        let targetEvaluasi = nodeData.target_evaluasi || '*';
        let tableIndex     = nodeData.table_index || 1;

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
        <div class="card shadow-sm mb-3 tree-node-card" data-level="${level}" data-id="${id}" data-table-index="${tableIndex}" data-kolom-db="${kolomDb}">
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
                                <option value="dropdown_custom" ${tipeInput === 'dropdown_custom' ? 'selected' : ''}>Pilihan Dropdown Khusus (Custom Options)</option>
                                <option value="auto_judgment" ${tipeInput === 'auto_judgment' ? 'selected' : ''}>Judgment Otomatis (✔ OK / ✖ NG)</option>
                                <option value="time" ${tipeInput === 'time' ? 'selected' : ''}>Waktu (Jam:Menit)</option>
                                <option value="date" ${tipeInput === 'date' ? 'selected' : ''}>Tanggal (Date Picker)</option>
                                <option value="text" ${tipeInput === 'text' ? 'selected' : ''}>Teks Bebas (1 Baris)</option>
                                <option value="textarea" ${tipeInput === 'textarea' ? 'selected' : ''}>Catatan Panjang (Kotak Besar)</option>
                                <option value="header_only" ${isHeaderOnly ? 'selected' : ''}>Hanya Teks Statis / Label (Tanpa Input)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small mb-1">Isi Teks Statis / Keterangan Standar</label>
                            <input type="text" class="form-control form-control-sm inp-node-standar" placeholder="Contoh: 7 ~ 10V / Measure Voltage" value="${standarNilai}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small mb-1" title="Kosongkan agar sistem memilih slot parXXX kosong secara otomatis">Kode Kolom DB</label>
                            <input type="text" class="form-control form-control-sm font-monospace inp-node-kolomdb" placeholder="Auto (parXXX)" value="${kolomDb}">
                        </div>
                        <div class="col-md-2 pt-2">
                            <div class="form-check mb-1">
                                <input class="form-check-input chk-node-required" type="checkbox" ${(!isReadonly && !isHeaderOnly && isRequired) ? 'checked' : ''} ${(isReadonly || isHeaderOnly) ? 'disabled' : ''}>
                                <label class="form-check-label fw-bold small text-danger">
                                    Wajib Diisi (*)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input chk-node-readonly" type="checkbox" ${isReadonly ? 'checked' : ''}>
                                <label class="form-check-label fw-bold small text-secondary">
                                    Read-Only
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

                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1">Presisi Desimal (Step)</label>
                                <select class="form-select form-select-sm inp-node-step">
                                    <option value="any" ${stepValue === 'any' ? 'selected' : ''}>Bebas (any)</option>
                                    <option value="1" ${stepValue === '1' ? 'selected' : ''}>Bilangan Bulat (1)</option>
                                    <option value="0.1" ${stepValue === '0.1' ? 'selected' : ''}>1 Desimal (0.1)</option>
                                    <option value="0.01" ${stepValue === '0.01' ? 'selected' : ''}>2 Desimal (0.01)</option>
                                    <option value="0.001" ${stepValue === '0.001' ? 'selected' : ''}>3 Desimal (0.001)</option>
                                    <option value="0.0001" ${stepValue === '0.0001' ? 'selected' : ''}>4 Desimal (0.0001)</option>
                                    <option value="0.00000001" ${stepValue === '0.00000001' ? 'selected' : ''}>8 Desimal (0.00000001)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold mb-1">Rentang Ganda (Dual Range / Toleransi Pecah)</label>
                                <input type="text" class="form-control form-control-sm inp-node-rentang-ganda" placeholder="Contoh: 40,60,25,45 (Min1, Max1, Min2, Max2)" value="${rentangGanda}">
                                <small class="text-muted" style="font-size:10px;">Jika diisi, validasi akan memanggil changeRange2 jika nilai di luar kedua rentang.</small>
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

                    <!-- BOX KHUSUS DROPDOWN CUSTOM -->
                    <div class="box-dropdown-custom-extra mt-2 pt-2 border-top ${tipeInput === 'dropdown_custom' ? '' : 'd-none'}">
                        <label class="form-label small fw-bold mb-1">Daftar Pilihan Dropdown (Pisahkan dengan tanda koma):</label>
                        <input type="text" class="form-control form-control-sm inp-node-dropdown-opsi" placeholder="Contoh: ✔ (OK), ✖ (NG), - atau V = GOOD, A = Adjust, CP = Change Part, WP = Waiting Part" value="${opsiDropdown}">
                        <small class="text-muted">Operator hanya dapat memilih dari opsi-opsi yang Anda daftarkan di sini.</small>
                    </div>

                    <!-- BOX KHUSUS AUTO JUDGMENT -->
                    <div class="box-auto-judgment-extra mt-2 pt-2 border-top ${tipeInput === 'auto_judgment' ? '' : 'd-none'}">
                        <div class="alert alert-warning py-1 px-2 mb-2 small">
                            <i class="fas fa-magic"></i> Kolom ini otomatis menentukan kelulusan: Jika ada parameter yang merah/NG maka otomatis <b>✖ (NG)</b>, jika semua parameter terisi sesuai standar maka otomatis <b>✔ (OK)</b>.
                        </div>
                        <label class="form-label small fw-bold mb-1">Cakupan Target Parameter Yang Dievaluasi:</label>
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <select class="form-select form-select-sm sel-node-eval-mode">
                                <option value="*" ${(targetEvaluasi === '*' || !targetEvaluasi) ? 'selected' : ''}>Semua Parameter Input pada Form Ini (Otomatis — Default)</option>
                                <option value="custom" ${(targetEvaluasi && targetEvaluasi !== '*') ? 'selected' : ''}>Pilih Parameter Tertentu Saja (Checklist)...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary text-nowrap btn-eval-set-all" title="Gunakan semua parameter di form ini">
                                <i class="fas fa-globe"></i> Semua Parameter
                            </button>
                        </div>
                        <input type="hidden" class="inp-node-target-eval" value="${targetEvaluasi || '*'}">

                        <div class="box-eval-custom-picker p-2 bg-white border rounded ${(targetEvaluasi && targetEvaluasi !== '*') ? '' : 'd-none'}">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2 pb-1 border-bottom">
                                <span class="badge bg-primary lbl-eval-selected-count">0 parameter dipilih</span>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 btn-eval-check-all">
                                        <i class="fas fa-check-double"></i> Centang Semua
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 btn-eval-uncheck-all">
                                        <i class="fas fa-eraser"></i> Reset Pilihan
                                    </button>
                                </div>
                            </div>
                            <div class="eval-checklist-container small" style="max-height: 180px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>

                <div class="node-children-container mt-3 d-none"></div>
            </div>
        </div>`;

        let card = $(cardHtml);

        // Isi daftar parameter untuk fitur Target Evaluasi Auto-Judgment
        let evalBox = card.find('.box-auto-judgment-extra').first();
        let evalChecklist = evalBox.find('.eval-checklist-container').first();
        let availTargets = getEvaluatableLeafOptions(id);
        let preSelectedCols = (targetEvaluasi && targetEvaluasi !== '*')
            ? targetEvaluasi.split(',').map(function(s) { return s.trim().toLowerCase(); }).filter(Boolean)
            : [];

        if (availTargets.length === 0) {
            evalChecklist.html('<div class="text-muted fst-italic py-1">Belum ada parameter lain yang tersimpan di revisi ini. Mode "Semua Parameter" akan otomatis mengevaluasi semua parameter nantinya.</div>');
        } else {
            availTargets.forEach(function(item, idx) {
                let chkId = 'chk_eval_' + (id || 'new') + '_' + idx + '_' + Math.floor(Math.random() * 10000);
                let isChecked = preSelectedCols.includes(item.col) ? 'checked' : '';
                evalChecklist.append(
                    '<div class="form-check py-1 border-bottom border-light">' +
                    '<input class="form-check-input chk-eval-col-item" type="checkbox" id="' + chkId + '" value="' + item.col + '" ' + isChecked + '>' +
                    '<label class="form-check-label d-flex justify-content-between align-items-center w-100" for="' + chkId + '" style="cursor:pointer;">' +
                    '<span>' + $('<div>').text(item.label).html() + '</span>' +
                    '<code class="ms-2 text-primary">[' + item.col + ']</code>' +
                    '</label></div>'
                );
            });
        }
        syncEvalTargetBox(evalBox);

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
        let leaf = card.children('.card-body').children('.node-leaf-settings');
        let val  = $(this).val();

        leaf.children('.box-number-extra').toggleClass('d-none', val !== 'number');
        leaf.children('.box-dropdown-custom-extra').toggleClass('d-none', val !== 'dropdown_custom');
        leaf.children('.box-auto-judgment-extra').toggleClass('d-none', val !== 'auto_judgment');

        if (val === 'auto_judgment') {
            leaf.find('.chk-node-readonly').prop('checked', true);
            leaf.find('.chk-node-required').prop('checked', false).prop('disabled', true);
        } else if (val === 'header_only') {
            leaf.find('.chk-node-required').prop('checked', false).prop('disabled', true);
        } else if (!leaf.find('.chk-node-readonly').is(':checked')) {
            leaf.find('.chk-node-required').prop('disabled', false);
        }
    });

    $(document).on('change', '.chk-node-readonly', function() {
        let leaf = $(this).closest('.node-leaf-settings');
        let isRo = $(this).is(':checked');
        let isHdr = (leaf.find('.sel-node-tipe').val() === 'header_only');
        if (isRo || isHdr) {
            leaf.find('.chk-node-required').prop('checked', false).prop('disabled', true);
        } else {
            leaf.find('.chk-node-required').prop('disabled', false);
        }
    });

    $(document).on('change', '.sel-node-formula', function() {
        let card = $(this).closest('.tree-node-card');
        let leafBox = card.children('.card-body').children('.node-leaf-settings');
        let formulaColsBox = leafBox.find('.box-formula-cols').first();
        if ($(this).val() !== '') {
            formulaColsBox.removeClass('d-none');
            leafBox.find('.chk-node-readonly').first().prop('checked', true).trigger('change');
        } else {
            formulaColsBox.addClass('d-none');
        }
    });

    $(document).on('change', '.sel-node-eval-mode', function() {
        let evalBox = $(this).closest('.box-auto-judgment-extra');
        if ($(this).val() === 'custom' && evalBox.find('.chk-eval-col-item:checked').length === 0) {
            evalBox.find('.chk-eval-col-item').prop('checked', true);
        }
        syncEvalTargetBox(evalBox);
    });

    $(document).on('click', '.btn-eval-set-all', function() {
        let evalBox = $(this).closest('.box-auto-judgment-extra');
        evalBox.find('.sel-node-eval-mode').val('*');
        syncEvalTargetBox(evalBox);
    });

    $(document).on('click', '.btn-eval-check-all', function() {
        let evalBox = $(this).closest('.box-auto-judgment-extra');
        evalBox.find('.chk-eval-col-item').prop('checked', true);
        syncEvalTargetBox(evalBox);
    });

    $(document).on('click', '.btn-eval-uncheck-all', function() {
        let evalBox = $(this).closest('.box-auto-judgment-extra');
        evalBox.find('.chk-eval-col-item').prop('checked', false);
        syncEvalTargetBox(evalBox);
    });

    $(document).on('change', '.chk-eval-col-item', function() {
        syncEvalTargetBox($(this).closest('.box-auto-judgment-extra'));
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
        let parentTableIndex = parseInt(parentCard.attr('data-table-index')) || 1;
        let childrenContainer = parentCard.children('.card-body').children('.node-children-container');
        let nextOrder = childrenContainer.children('.tree-node-card').length + 1;

        let newChildCard = createNodeCard({ urutan: nextOrder, tipe_input: 'number', table_index: parentTableIndex }, parentLevel + 1, false);
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
        let tableIndex  = parseInt(card.attr('data-table-index')) || 1;
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

        let selTipe       = leafSettings.find('.sel-node-tipe').first().val();
        let jenisElemen   = (hasChildren || selTipe === 'header_only') ? 'header' : 'input';
        let tipeInput     = hasChildren ? null : selTipe;
        let standarNilai  = leafSettings.find('.inp-node-standar').first().val().trim();
        let manualKolomDb = (leafSettings.find('.inp-node-kolomdb').first().val() || '').trim();
        if (jenisElemen === 'input') {
            kolomDb = manualKolomDb !== '' ? manualKolomDb : null;
        } else {
            kolomDb = null;
        }
        let isReadonly    = leafSettings.find('.chk-node-readonly').first().is(':checked') ? 1 : 0;
        let isRequired    = (!isReadonly && jenisElemen === 'input' && leafSettings.find('.chk-node-required').first().is(':checked')) ? 1 : 0;
        let batasMin      = leafSettings.find('.inp-node-min').first().val();
        let batasMax      = leafSettings.find('.inp-node-max').first().val();

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

        let opsiDropdown   = (tipeInput === 'dropdown_custom') ? (leafSettings.find('.inp-node-dropdown-opsi').first().val() || '').trim() : null;
        let stepValue      = (tipeInput === 'number') ? (leafSettings.find('.inp-node-step').first().val() || 'any').trim() : 'any';
        let rentangGanda   = (tipeInput === 'number') ? (leafSettings.find('.inp-node-rentang-ganda').first().val() || '').trim() : null;
        let targetEvaluasi = (tipeInput === 'auto_judgment') ? (leafSettings.find('.inp-node-target-eval').first().val() || '*').trim() : null;
        if (tipeInput === 'auto_judgment') isReadonly = 1;

        let childrenData = [];
        childCards.each(function() {
            childrenData.push(serializeNodeCard($(this)));
        });

        return {
            id: id,
            nama_parameter: namaParam,
            kolom_db: kolomDb,
            table_index: tableIndex,
            urutan: urutan,
            jenis_elemen: jenisElemen,
            tipe_input: tipeInput,
            standar_nilai: standarNilai,
            batas_min: batasMin,
            batas_max: batasMax,
            is_readonly: isReadonly,
            is_required: isReadonly ? 0 : isRequired,
            is_exclusive: isExclusive,
            satuan_opsi: satuanOpsi.length > 0 ? satuanOpsi : null,
            rumus_otomatis: rumusOtomatis,
            standar_dinamis: standarDinamis,
            opsi_dropdown: opsiDropdown,
            step_value: stepValue,
            rentang_ganda: rentangGanda,
            target_evaluasi: targetEvaluasi,
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
        let tableIndex = parseInt($(this).data('table-index')) || 1;

        $('#revisionId').val(currentRevisionId);
        populateCleanParentDropdown([], parentId);
        $('#boxPindahInduk').addClass('d-none');

        $('#lblNamaInduk').text(parentName);
        $('#infoIndukBox').removeClass('d-none');

        $('#treeBuilderRoot').empty().append(createNodeCard({ urutan: 1, tipe_input: 'number', table_index: tableIndex }, 1, true));

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
                    loadTableData(false, currentRevisionId);
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
            let id = $(this).data('id');
            $.ajax({
                url: "<?= base_url('form-builder/delete') ?>",
                type: "POST",
                data: { id: id },
                dataType: "json",
                success: function() {
                    loadTableData(false, currentRevisionId);
                }
            });
        }
    });

    // ==========================================================
    // FITUR BARU: GESER PANAH CEPAT, REVISION SWITCHER, PREVIEW, & CLONE
    // ==========================================================

    // 1. Geser urutan parameter 1-klik (Panah Kiri/Kanan atau Atas/Bawah)
    $(document).on('click', '.btn-move-param', function() {
        let btn = $(this);
        let id  = btn.data('id');
        let dir = btn.data('dir');
        if (!id || !dir) return;

        btn.prop('disabled', true);
        $.ajax({
            url: "<?= base_url('form-builder/param-move') ?>",
            type: "POST",
            data: { id: id, direction: dir },
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    loadTableData(false, currentRevisionId);
                } else {
                    btn.prop('disabled', false);
                    alert(res.message || 'Gagal menggeser urutan parameter.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                if (xhr && xhr.status === 401) return;
                alert('Gagal menggeser parameter!');
            }
        });
    });

    // 2. Dropdown Pengganti Versi Revisi & Tombol Pulihkan Revisi Arsip
    $('#fbRevisionSelect').on('change', function() {
        let targetRevId = $(this).val();
        if (targetRevId) {
            loadTableData(false, targetRevId);
        }
    });

    $(document).on('click', '.btn-back-latest-rev', function() {
        let latestId = $(this).data('latest-id');
        loadTableData(false, latestId || null);
    });

    $(document).on('click', '.btn-restore-revision', function() {
        let srcId  = $(this).data('source-id');
        let revLbl = $(this).data('rev');
        if (!srcId) return;

        if (!confirm('Salin seluruh struktur parameter & Catatan SOP dari Revisi ' + revLbl + ' ke Revisi Aktif terbaru?\n\n(Data riwayat pada Revisi ' + revLbl + ' tetap aman dan tidak berubah.)')) {
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyalin...');

        $.ajax({
            url: "<?= base_url('form-builder/restore-revisi') ?>",
            type: "POST",
            data: { source_revision_id: srcId },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-undo"></i> Salin Struktur Ini ke Revisi Aktif');
                if (res.status === 'success') {
                    alert(res.message);
                    loadTableData(false, res.latest_revision_id || null);
                } else {
                    alert(res.message || 'Gagal memulihkan revisi.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-undo"></i> Salin Struktur Ini ke Revisi Aktif');
                if (xhr && xhr.status === 401) return;
                alert('Gagal memulihkan revisi!');
            }
        });
    });

    // 3. Live Preview Modal (Simulasi Input Operator & Tabel History)
    $('#btnOpenLivePreview').on('click', function() {
        if (!currentRevisionId) {
            alert('Pilih dan muat form terlebih dahulu!');
            return;
        }

        $('#is-standby').prop('checked', false);
        let curKat = (window.currentRevision && window.currentRevision.kategori_form) ? window.currentRevision.kategori_form : $('#docTypeSelect').val();
        let isStartupKat = (curKat === 'startup' || curKat === 'startup_horizontal');
        $('#lpStandbyWrapper').toggleClass('d-none', !isStartupKat);

        $('#lblLivePreviewProc').text(($('#processSelect').val() || '-').toUpperCase());
        $('#livePreviewInputContainer').html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Menyiapkan simulasi Form Input Operator...</div>');
        $('#livePreviewHistoryContainer').html('<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Menyiapkan simulasi Tabel History...</div>');
        $('#modalLivePreview').modal('show');

        $.ajax({
            url: "<?= base_url('form-builder/preview') ?>",
            type: "GET",
            data: { revision_id: currentRevisionId },
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    let resKat = res.kategori_form || curKat;
                    $('#lpStandbyWrapper').toggleClass('d-none', !(resKat === 'startup' || resKat === 'startup_horizontal'));
                    $('#lblLivePreviewProc').text((res.process_code || '').toUpperCase() + ' - Revisi ' + (res.revisi || '0'));
                    $('#livePreviewInputContainer').html(res.input_html || '');
                    $('#livePreviewHistoryContainer').html(res.history_html || '');
                } else {
                    $('#livePreviewInputContainer').html('<div class="alert alert-danger">' + (res.message || 'Gagal memuat preview.') + '</div>');
                }
            },
            error: function(xhr) {
                if (xhr && xhr.status === 401) return;
                $('#livePreviewInputContainer').html('<div class="alert alert-danger">Gagal memuat Live Preview dari server.</div>');
            }
        });
    });

    // 4. Clone / Salin Struktur dari Proses Lain (Filter 3 Tingkat: Device -> Process -> Doc. Type)
    let cloneProcessRows = [];

    $('#btnOpenCloneForm').on('click', function() {
        if (!currentRevisionId) {
            alert('Pilih dan muat form tujuan terlebih dahulu!');
            return;
        }
        let revNum = (window.currentRevision && window.currentRevision.revisi) ? window.currentRevision.revisi : '-';
        let procCd = ($('#processSelect').val() || '').toUpperCase();
        $('#lblCloneTargetRev').text(procCd + ' (Revisi ' + revNum + ')');
        cloneProcessRows = [];
        $('#cloneDeviceSelect').val('');
        $('#cloneProcessNameSelect').html('<option value="">-- Pilih Device Dulu --</option>').prop('disabled', true);
        $('#cloneProcessSelect').html('<option value="">-- Pilih Process Dulu --</option>').prop('disabled', true);
        $('#btnSubmitCloneForm').prop('disabled', true);
        $('#modalCloneForm').modal('show');
    });

    $('#cloneDeviceSelect').on('change', function() {
        let dev = $(this).val();
        let procNameSel = $('#cloneProcessNameSelect');
        let docTypeSel  = $('#cloneProcessSelect');
        cloneProcessRows = [];
        $('#btnSubmitCloneForm').prop('disabled', true);

        docTypeSel.html('<option value="">-- Pilih Process Dulu --</option>').prop('disabled', true);

        if (!dev) {
            procNameSel.html('<option value="">-- Pilih Device Dulu --</option>').prop('disabled', true);
            return;
        }
        procNameSel.html('<option value="">Loading daftar proses...</option>').prop('disabled', true);

        $.getJSON("<?= base_url('process/list') ?>", { device: dev }, function(rows) {
            cloneProcessRows = rows || [];
            let names = [];
            cloneProcessRows.forEach(function(r) {
                let base = splitName(r.process_name || r.name).base;
                if (base && names.indexOf(base) === -1) names.push(base);
            });
            names.sort();

            if (names.length === 0) {
                procNameSel.html('<option value="">-- Data Kosong --</option>').prop('disabled', true);
                return;
            }
            procNameSel.empty().append('<option value="">-- Pilih Process --</option>');
            names.forEach(function(n) {
                procNameSel.append($('<option>', { value: n, text: n }));
            });
            procNameSel.prop('disabled', false);
        });
    });

    $('#cloneProcessNameSelect').on('change', function() {
        let selectedBase = $(this).val();
        let docTypeSel   = $('#cloneProcessSelect');
        $('#btnSubmitCloneForm').prop('disabled', true);

        if (!selectedBase) {
            docTypeSel.html('<option value="">-- Pilih Process Dulu --</option>').prop('disabled', true);
            return;
        }

        let matchedRows = cloneProcessRows.filter(function(r) {
            return splitName(r.process_name || r.name).base === selectedBase;
        });
        matchedRows.sort(function(a, b) {
            return (TYPE_ORDER[a.type] || 99) - (TYPE_ORDER[b.type] || 99);
        });

        if (matchedRows.length === 0) {
            docTypeSel.html('<option value="">-- Tidak Ada Dokumen --</option>').prop('disabled', true);
            return;
        }

        docTypeSel.empty().append('<option value="">-- Pilih Dokumen / Doc Type --</option>');
        matchedRows.forEach(function(r) {
            let variant = splitName(r.process_name || r.name).variant;
            let label = stripRevision(r.docno) + ' (' + (TYPE_LABEL[r.type] || r.type) + ')' + (variant ? ' - ' + variant : '');
            docTypeSel.append($('<option>', {
                value: r.process_code,
                text: label
            }));
        });
        docTypeSel.prop('disabled', false);
    });

    $('#cloneProcessSelect').on('change', function() {
        $('#btnSubmitCloneForm').prop('disabled', !$(this).val());
    });

    $('#formCloneProcess').on('submit', function(e) {
        e.preventDefault();
        let srcProc = $('#cloneProcessSelect').val();
        if (!srcProc) {
            alert('Pilih proses sumber terlebih dahulu!');
            return;
        }
        if (!confirm('PERINGATAN: Seluruh parameter pada revisi aktif saat ini akan diganti dengan struktur dari proses "' + srcProc.toUpperCase() + '".\n\nLanjutkan proses Clone?')) {
            return;
        }

        let btn = $('#btnSubmitCloneForm');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyalin Struktur...');

        $.ajax({
            url: "<?= base_url('form-builder/clone-from') ?>",
            type: "POST",
            data: {
                target_revision_id: currentRevisionId,
                source_process: srcProc
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Salin Struktur Sekarang');
                if (res.status === 'success') {
                    $('#modalCloneForm').modal('hide');
                    alert(res.message);
                    loadTableData(false, currentRevisionId);
                } else {
                    alert(res.message || 'Gagal menyalin struktur proses.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-copy"></i> Salin Struktur Sekarang');
                if (xhr && xhr.status === 401) return;
                alert('Terjadi kesalahan saat menyalin struktur form.');
            }
        });
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

    // PENGATURAN CATATAN SOP & MODE LAYOUT (MATRIKS 2D / STANDARD)
    $('#btnOpenSopNote').click(function() {
        let rev = window.currentRevision || {};
        $('#sopLayoutModeSelect').val(rev.layout_mode || 'standard');
        $('#sopCatatanText').val(rev.catatan_sop || '');
        $('#modalSopNote').modal('show');
    });

    $('#formSopNote').submit(function(e) {
        e.preventDefault();
        let btn = $('#btnSaveSopNote');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: "<?= base_url('form-builder/save-sop-note') ?>",
            type: "POST",
            data: {
                revision_id: currentRevisionId,
                layout_mode: $('#sopLayoutModeSelect').val(),
                catatan_sop: $('#sopCatatanText').val()
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Catatan & Mode');
                if (res.status === 'success') {
                    if (window.currentRevision) {
                        window.currentRevision.layout_mode = $('#sopLayoutModeSelect').val();
                        window.currentRevision.catatan_sop = $('#sopCatatanText').val();
                    }
                    $('#modalSopNote').modal('hide');
                    alert(res.message);
                } else {
                    alert(res.message || 'Gagal menyimpan catatan SOP.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Catatan & Mode');
                console.error("Save SOP Error:", xhr.responseText);
                alert("Gagal menyimpan Catatan SOP! Cek tab Console.");
            }
        });
    });

    // ==========================================================
    // FITUR EXPORT / SALIN HARD CODE PHP (INPUT & HISTORY)
    // ==========================================================

    function copyTextFromTextarea(textareaId, btnEl, defaultHtml) {
        let txtEl = document.getElementById(textareaId);
        if (!txtEl) return;
        let content = txtEl.value || '';

        let showCopied = function() {
            $(btnEl).html('<i class="fas fa-check"></i> Berhasil Disalin!').addClass('btn-warning text-dark');
            setTimeout(function() {
                $(btnEl).html(defaultHtml).removeClass('btn-warning text-dark');
            }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(content).then(showCopied).catch(function() {
                txtEl.focus();
                txtEl.select();
                document.execCommand('copy');
                showCopied();
            });
        } else {
            txtEl.focus();
            txtEl.select();
            document.execCommand('copy');
            showCopied();
        }
    }

    $('#btnOpenExportHardcode').click(function() {
        if (!currentRevisionId) {
            alert("Pilih dan muat form terlebih dahulu!");
            return;
        }

        $('#lblHardcodeProc').text(($('#processSelect').val() || '-').toUpperCase());
        $('#lblInputRefPath').text('Memuat...');
        $('#lblHistoryRefPath').text('Memuat...');
        $('#txtHardcodeInput').val('// Meng-generate hard code PHP Input...');
        $('#txtHardcodeHistory').val('// Meng-generate hard code PHP History...');
        $('#modalExportHardcode').modal('show');

        $.ajax({
            url: "<?= base_url('form-builder/export-hardcode') ?>",
            type: "GET",
            data: { revision_id: currentRevisionId },
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    $('#lblHardcodeProc').text((res.process_code || '').toUpperCase() + ' - Rev ' + (res.revisi || '00'));
                    $('#lblInputRefPath').text(res.input_ref_path || '-');
                    $('#lblHistoryRefPath').text(res.history_ref_path || '-');
                    $('#txtHardcodeInput').val(res.input_code || '');
                    $('#txtHardcodeHistory').val(res.history_code || '');
                } else {
                    alert(res.message || 'Gagal meng-generate hard code.');
                }
            },
            error: function(xhr) {
                console.error("Export hardcode error:", xhr.responseText);
                alert("Gagal memuat hard code PHP! Cek tab Console.");
            }
        });
    });

    $('#btnCopyInputCode').click(function() {
        copyTextFromTextarea('txtHardcodeInput', this, '<i class="fas fa-copy"></i> Salin Kode Input');
    });

    $('#btnCopyHistoryCode').click(function() {
        copyTextFromTextarea('txtHardcodeHistory', this, '<i class="fas fa-copy"></i> Salin Kode History');
    });

    $('#btnDownloadInputCode').click(function() {
        if (!currentRevisionId) return;
        window.location.href = "<?= base_url('form-builder/export-hardcode') ?>?revision_id=" + currentRevisionId + "&download=input";
    });

    $('#btnDownloadHistoryCode').click(function() {
        if (!currentRevisionId) return;
        window.location.href = "<?= base_url('form-builder/export-hardcode') ?>?revision_id=" + currentRevisionId + "&download=history";
    });

    // ==========================================================
    // FITUR REGENERASI DARI CSV (EXPORT & UPLOAD CSV)
    // ==========================================================

    // 1. Download Template CSV atau Export Parameter Saat Ini
    $('#btnExportCsv, #btnDownloadTemplateInModal').click(function(e) {
        e.preventDefault();
        if (!currentRevisionId) {
            alert("Pilih dan muat form terlebih dahulu!");
            return;
        }
        window.location.href = "<?= base_url('form-builder/export-csv') ?>?revision_id=" + currentRevisionId;
    });

    // 2. Buka Modal Upload CSV
    $('#btnOpenImportCsv').click(function() {
        if (!currentRevisionId) {
            alert("Pilih dan muat form terlebih dahulu!");
            return;
        }
        $('#formImportCsv')[0].reset();
        $('#modalImportCsv').modal('show');
    });

    // 3. Submit File CSV untuk Regenerate Parameter
    $('#formImportCsv').submit(function(e) {
        e.preventDefault();
        let fileInput = document.getElementById('csvFileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert("Pilih file CSV terlebih dahulu!");
            return;
        }

        let mode = $('input[name="import_mode"]:checked').val() || 'replace';
        let confirmMsg = (mode === 'replace')
            ? "PERINGATAN: Mode Regenerate Total akan menghapus seluruh parameter lama pada revisi ini dan menggantikannya dengan isi file CSV.\n\nApakah Anda yakin ingin meregenerate parameter?"
            : "Parameter dari file CSV akan ditambahkan ke bagian bawah form.\n\nLanjutkan?";

        if (!confirm(confirmMsg)) {
            return;
        }

        let formData = new FormData();
        formData.append('revision_id', currentRevisionId);
        formData.append('import_mode', mode);
        formData.append('csv_file', fileInput.files[0]);

        let btn = $('#btnSubmitImportCsv');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses CSV...');

        $.ajax({
            url: "<?= base_url('form-builder/import-csv') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Mulai Regenerate Parameter');
                if (res.status === 'success') {
                    $('#modalImportCsv').modal('hide');
                    alert(res.message);
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal memproses file CSV.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Mulai Regenerate Parameter');
                console.error("Import CSV Error:", xhr.responseText);
                alert("Gagal mengupload CSV! Cek tab Console untuk detail.");
            }
        });
    });

    // ==========================================================
    // FITUR TAMBAH & HAPUS TABEL BARU DENGAN PILIHAN BENTUK
    // ==========================================================

    $('#btnAddNewTable').click(function() {
        if (!currentRevisionId) {
            alert('Pilih dan muat form terlebih dahulu!');
            return;
        }
        $('#formNewTable')[0].reset();
        $('#newTableLayoutType').val('horizontal').trigger('change');
        $('#modalNewTable').modal('show');
    });

    $('#newTableLayoutType').change(function() {
        let val = $(this).val();
        if (val === 'matrix') {
            $('#boxMatrixOptions').removeClass('d-none');
            $('#boxSimpleOptions').addClass('d-none');
        } else {
            $('#boxMatrixOptions').addClass('d-none');
            $('#boxSimpleOptions').removeClass('d-none');
        }
    });

    $('#selMatrixTemplate').change(function() {
        let val = $(this).val();
        if (val === 'front_back') {
            $('#inpCustomMatrixRows').val('Front 1, Front 2, Back 1, Back 2');
        } else if (val === 'xyzt') {
            $('#inpCustomMatrixRows').val('X, Y, Z, θ');
        } else if (val === 'samples_5') {
            $('#inpCustomMatrixRows').val('Sample 1, Sample 2, Sample 3, Sample 4, Sample 5');
        }
    });

    $('#formNewTable').submit(function(e) {
        e.preventDefault();
        let tableName = $('#newTableName').val().trim();
        if (!tableName) {
            alert('Nama tabel wajib diisi!');
            return;
        }

        let layoutType = $('#newTableLayoutType').val();
        let payload = {
            revision_id: currentRevisionId,
            table_name: tableName,
            layout_type: layoutType
        };

        if (layoutType === 'matrix') {
            payload.matrix_rows = $('#inpCustomMatrixRows').val();
            payload.first_col_name = $('#inpMatrixFirstCol').val().trim() || 'Result';
        } else {
            payload.initial_param_name = $('#inpSimpleParamName').val().trim() || 'Parameter 1';
            payload.initial_tipe_input = $('#selSimpleTipeInput').val();
        }

        let btn = $('#btnSubmitNewTable');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Membuat Tabel...');

        $.ajax({
            url: "<?= base_url('form-builder/add-table') ?>",
            type: "POST",
            data: payload,
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Buat Tabel Baru');
                if (res.status === 'success') {
                    $('#modalNewTable').modal('hide');
                    alert(res.message);
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal membuat tabel baru.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Buat Tabel Baru');
                console.error("Add table error:", xhr.responseText);
                alert('Gagal membuat tabel baru! Cek Console.');
            }
        });
    });

    $(document).on('click', '.btn-delete-table', function() {
        let tIdx = $(this).data('table-index');
        let tName = $(this).data('table-name') || ('Tabel ' + tIdx);

        if (!confirm('PERINGATAN: Apakah Anda yakin ingin menghapus seluruh ' + tName + ' beserta semua parameternya?\n\nTindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        $.ajax({
            url: "<?= base_url('form-builder/delete-table') ?>",
            type: "POST",
            data: {
                revision_id: currentRevisionId,
                table_index: tIdx
            },
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    alert(res.message);
                    loadTableData(false);
                } else {
                    alert(res.message || 'Gagal menghapus tabel.');
                }
            },
            error: function(xhr) {
                console.error("Delete table error:", xhr.responseText);
                alert('Gagal menghapus tabel! Cek Console.');
            }
        });
    });

    // ==========================================================
    // FITUR STANDAR MODEL UNIVERSAL (FOREGOING & PRODUCTION)
    // ==========================================================

    function getModelStandardItems() {
        let items = [];
        // 1. Cek parameter kolom daun header yang memiliki anak grid (misal Point 1..Point 7 di LDSM Production)
        (window.currentParamsList || []).forEach(function(p) {
            if (p.posisi_grid && p.parent_id) {
                let parentNode = (window.currentParamsList || []).find(function(par) {
                    return String(par.id) === String(p.parent_id);
                });
                if (parentNode && parentNode.nama_parameter && !items.includes(parentNode.nama_parameter)) {
                    if (p.tipe_input === 'number' || !p.tipe_input) {
                        items.push(parentNode.nama_parameter);
                    }
                }
            }
        });

        // 2. Jika tidak ada kolom bertingkat beranak grid, ambil baris posisi_grid (seperti X, Y, Z, θ di Foregoing)
        if (items.length === 0) {
            (window.currentParamsList || []).forEach(function(p) {
                if (p.posisi_grid && !items.includes(p.posisi_grid)) {
                    items.push(p.posisi_grid);
                }
            });
        }

        // 3. Fallback: ambil semua parameter input 'number'
        if (items.length === 0) {
            (window.currentParamsList || []).forEach(function(p) {
                if (p.tipe_input === 'number' && p.kolom_db && !items.includes(p.nama_parameter)) {
                    items.push(p.nama_parameter);
                }
            });
        }

        return items.length > 0 ? items : ['Point 1', 'Point 2', 'Point 3', 'Point 4', 'Point 5', 'Point 6', 'Point 7'];
    }

    function loadModelsForFormBuilder(selectedId) {
        selectedId = selectedId || null;
        let processCode = $('#processSelect').val();
        let docType     = $('#docTypeSelect').val();
        let hasModel    = (docType === 'foregoing' || docType === 'production');

        if (!hasModel || !processCode) {
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

                let actionHtml = m.id ? (
                    '<button type="button" class="btn btn-xs btn-warning py-0 px-1 me-1 btn-edit-model-item" data-id="' + m.id + '" data-name="' + m.model_name + '"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-xs btn-danger py-0 px-1 btn-del-model-item" data-id="' + m.id + '" data-name="' + m.model_name + '"><i class="fas fa-trash"></i></button>'
                ) : '<span class="badge bg-light text-secondary">Riwayat</span>';

                tbodyCrud.append(
                    '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td class="fw-bold">' + m.model_name + '</td>' +
                    '<td><span class="badge ' + (hasStd ? 'bg-success' : 'bg-secondary') + '">' + badgeTxt + '</span></td>' +
                    '<td>' + actionHtml + '</td>' +
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

        // 1. Cek baris sampel / posisi matriks (seperti X, Y, Z, θ)
        $('#visualBuilderContainer table tbody tr').each(function() {
            let posCell = $(this).find('td.bg-secondary .fs-6, th').first();
            if (posCell.length === 0) return;
            let rowName = posCell.text().trim();
            let cfg = modelObj.standards[rowName];
            if (!cfg) return;

            if (parseInt(cfg.active) === 0) {
                posCell.append('<div class="model-std-preview-badge badge bg-dark text-warning mt-1 d-block" style="font-size:10px;">' + modelObj.model_name + ': OFF</div>');
            } else {
                let minLbl = (cfg.min !== null && cfg.min !== '') ? cfg.min : '-∞';
                let maxLbl = (cfg.max !== null && cfg.max !== '') ? cfg.max : '∞';
                posCell.append('<div class="model-std-preview-badge badge bg-warning text-dark mt-1 d-block" style="font-size:10px;">' + modelObj.model_name + ': ' + minLbl + ' ~ ' + maxLbl + '</div>');
            }
        });

        // 2. Cek kolom parameter (seperti Point 1..Point 7 di Production LDSM)
        $('#visualBuilderContainer table thead th').each(function() {
            let th = $(this);
            let colTitle = th.find('.fw-semibold, .fw-bold').first().text().trim() || th.text().trim();
            if (!colTitle) return;

            let matchedKey = Object.keys(modelObj.standards).find(function(k) {
                return colTitle === k || colTitle.startsWith(k) || k.startsWith(colTitle);
            });
            if (!matchedKey) return;
            let cfg = modelObj.standards[matchedKey];
            if (!cfg) return;

            if (parseInt(cfg.active) === 0) {
                th.append('<div class="model-std-preview-badge badge bg-dark text-warning mt-1 d-block" style="font-size:10px;">' + modelObj.model_name + ': OFF</div>');
            } else {
                let minLbl = (cfg.min !== null && cfg.min !== '') ? cfg.min : '-∞';
                let maxLbl = (cfg.max !== null && cfg.max !== '') ? cfg.max : '∞';
                th.append('<div class="model-std-preview-badge badge bg-warning text-dark mt-1 d-block" style="font-size:10px;">' + minLbl + ' ~ ' + maxLbl + '</div>');
            }
        });
    }

    $('#fbModelSelect').on('change', function() {
        previewSelectedModelStandardOnTable();
    });

    function renderModelStdRows(standardsObj) {
        standardsObj = standardsObj || {};
        let rows = getModelStandardItems();
        let tbody = $('#tbodyModelStdRows');
        tbody.empty();

        rows.forEach(function(rName) {
            let cfg = standardsObj[rName] || { min: '', max: '', active: 1 };
            let isAct = (cfg.active === undefined || parseInt(cfg.active) === 1);
            let minV = (cfg.min !== null && cfg.min !== undefined) ? cfg.min : '';
            let maxV = (cfg.max !== null && cfg.max !== undefined) ? cfg.max : '';
            tbody.append(
                '<tr class="model-std-row" data-pos="' + rName + '">' +
                '<td class="fw-bold fs-6 bg-light text-start ps-3">' + rName + '</td>' +
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