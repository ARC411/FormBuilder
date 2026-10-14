<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
    <?= $this->include('layout/navbar'); ?>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">

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
                            <?php foreach ($devices as$d): ?>
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

                <!-- 2. REVISION HEADER & ACTION (MUNCUL SAAT FORM DILOAD) -->
                <div id="revisionHeader" class="d-none mb-3 p-3 bg-white border rounded shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="m-0 font-weight-bold">
                            Status Dokumen: <span id="lblRevisi" class="badge bg-danger fs-6">Revisi -</span>
                        </h5>
                        <small class="text-muted">Gunakan tombol di kanan untuk menambah kolom utama atau menaikkan revisi dokumen.</small>
                    </div>
                    <div class="d-flex gap-2">
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

    <!-- MODAL CRUD PARAMETER (VERSI RAMAH ORANG AWAM) -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formParam">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalTitle">Tambah Parameter</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="paramId" name="id">
                        <input type="hidden" id="revisionId" name="revision_id">
                        <input type="hidden" id="kolom_db" name="kolom_db">

                        <!-- Info Induk Saat Tambah Anak -->
                        <div id="infoIndukBox" class="alert alert-info py-2 d-none">
                            Menambahkan sub-bagian di bawah: <strong id="lblNamaInduk">-</strong>
                        </div>

                        <!-- 1. Nama Parameter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kolom / Item Pengecekan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_parameter" name="nama_parameter" placeholder="Contoh: Chip-clean Air Pressure / Output / Crack" required>
                        </div>

                        <!-- 2. Pindah Induk (HANYA MUNCUL SAAT EDIT) -->
                        <div class="mb-3 d-none" id="boxPindahInduk">
                            <label class="form-label fw-bold text-success"><i class="fas fa-sitemap"></i> Posisi Induk (Bapak)</label>
                            <select class="form-select border-success" id="parent_id" name="parent_id">
                                <!-- Diisi otomatis oleh JS -->
                            </select>
                            <small class="text-muted">Ubah pilihan ini jika Anda ingin memindahkan kolom ini ke bawah kelompok lain.</small>
                        </div>

                        <!-- 3. Pilihan Fungsi Elemen (Ramah Awam) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fungsi Bagian Ini:</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="border rounded p-3 d-block cursor-pointer bg-light h-100">
                                        <input type="radio" name="jenis_elemen" id="jenis_header" value="header" class="form-check-input me-2">
                                        <strong>📁 Judul Kelompok (Induk)</strong>
                                        <small class="d-block text-muted mt-1">Hanya berupa judul di atas/kiri tabel dan masih memiliki sub-bagian lagi.</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="border rounded p-3 d-block cursor-pointer bg-light h-100">
                                        <input type="radio" name="jenis_elemen" id="jenis_input" value="input" class="form-check-input me-2" checked>
                                        <strong>✍️ Kolom Isian Operator</strong>
                                        <small class="d-block text-muted mt-1">Bagian ujung tabel yang langsung diisi oleh Operator (Angka / OK-NG / Teks).</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Pengaturan Isian Operator (Muncul Jika Pilih Kolom Isian) -->
                        <div id="boxPengaturanInput" class="p-3 border rounded bg-light mb-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Bentuk Isian Operator</label>
                                    <select class="form-select" id="tipe_input" name="tipe_input">
                                        <option value="number">🔢 Angka (Ukuran / Qty / Tekanan)</option>
                                        <option value="dropdown_ok_ng">✅ Pilihan OK / NG (Dropdown)</option>
                                        <option value="text">🔤 Teks Bebas (1 Baris)</option>
                                        <option value="textarea">📝 Catatan Panjang (Kotak Besar)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Keterangan Standar (Opsional)</label>
                                    <input type="text" class="form-control" id="standar_nilai" name="standar_nilai" placeholder="Contoh: 0.34 - 0.49 MPa">
                                </div>
                            </div>

                            <!-- Muncul Otomatis Khusus Jika Pilih ANGKA -->
                            <div id="boxBatasAngka" class="row bg-white p-3 rounded border border-warning mx-0 mt-1">
                                <div class="col-12 mb-2">
                                    <strong class="text-dark"><i class="fas fa-sliders-h text-warning"></i> Standar Batas Angka (Validasi Otomatis)</strong>
                                    <small class="d-block text-muted">Kosongkan saja jika angka bebas (tidak ada batas minimal/maksimal).</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Batas Minimum (Min)</label>
                                    <input type="number" step="any" class="form-control" id="batas_min" name="batas_min" placeholder="Bebas (Kosongkan)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Batas Maksimum (Max)</label>
                                    <input type="number" step="any" class="form-control" id="batas_max" name="batas_max" placeholder="Bebas (Kosongkan)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Urutan Tampil</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanParam">
                            <i class="fas fa-save"></i> Simpan Parameter
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

        // Hilangkan warning aria-hidden focus saat modal ditutup
        $('#modalForm').on('hide.bs.modal', function () {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        // Toggle tampilan antara "Judul Kelompok" vs "Kolom Isian Operator"
        $('input[name="jenis_elemen"]').change(function() {
            if ($('#jenis_header').is(':checked')) {
                $('#boxPengaturanInput').slideUp(150);
            } else {
                $('#boxPengaturanInput').slideDown(150);
                $('#tipe_input').trigger('change');
            }
        });

        // Toggle Batas Min/Max khusus untuk tipe input "Angka"
        $('#tipe_input').change(function() {
            if ($(this).val() === 'number') {$('#boxBatasAngka').slideDown(150);
            } else {
                $('#boxBatasAngka').slideUp(150);
                $('#batas_min').val('');
                $('#batas_max').val('');
            }
        });

        // --- 1. LOGIKA CASCADING DROPDOWN ---
        $('#deviceSelect').change(function() {
            if ($(this).val() !== '') {$('#docTypeSelect').prop('disabled', false).val('');
                $('#processSelect').empty().append('<option value="">-- Pilih Doc Type Dulu --</option>').prop('disabled', true);
                $('#btnLoadForm').prop('disabled', true);
            } else {
                $('#docTypeSelect').prop('disabled', true).val('');
                $('#processSelect').empty().append('<option value="">-- Pilih Device Dulu --</option>').prop('disabled', true);
            }
        });

        $('#docTypeSelect').change(function() {
            let device = $('#deviceSelect').val();
            let type = $(this).val();

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

        // --- 2. FUNGSI UTAMA LOAD TABEL VISUAL ---
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
                        currentRevisionId = response.revision.id;

                        // Tampilkan kembali bar Revisi & Tombol Tambah Parameter Utama!
                        $('#lblRevisi').text('Revisi ' + response.revision.revisi);
                        $('#revisionHeader').removeClass('d-none');

                        $('#visualBuilderContainer').html(response.html);
                    } else {
                        alert(response.message || "Gagal memuat form.");
                    }
                },
                error: function(xhr, status, error) {
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

        function populateParentDropdown(excludeId = null, selectedParentId = '') {
            let parentDropdown = $('#parent_id');
            parentDropdown.empty();
            parentDropdown.append('<option value="">-- Jadikan Parameter Utama (Paling Atas / Kiri) --</option>');
            if (window.currentParamsList) {
                window.currentParamsList.forEach(function(p) {
                    if (p.id != excludeId) {
                        let isSelected = (p.id == selectedParentId) ? 'selected' : '';
                        parentDropdown.append(`<option value="${p.id}" ${isSelected}>${p.nama_parameter}</option>`);
                    }
                });
            }
        }

        // --- 3. AKSI CRUD MODAL ---

        // A. Buka Modal: Tambah Parameter Utama (Induk)
        $('#btnAddUtama').click(function() {
            $('#formParam')[0].reset();
            $('#paramId').val('');
            $('#revisionId').val(currentRevisionId);
            $('#kolom_db').val('');
            
            populateParentDropdown(null, '');
            $('#boxPindahInduk').addClass('d-none');
            $('#infoIndukBox').addClass('d-none');

            // Default untuk Parameter Utama adalah Judul Kelompok
            $('#jenis_header').prop('checked', true).trigger('change');
            $('#urutan').val((window.currentParamsList ? window.currentParamsList.length : 0) + 1);

            $('#modalTitle').html('<i class="fas fa-plus-circle"></i> Tambah Parameter Utama (Induk)');
            $('#modalForm').modal('show');
        });

        // B. Buka Modal: Tambah Sub-Parameter (Anak)
        $(document).on('click', '.btn-add-child', function() {
            let parentId = $(this).data('id');
            let parentObj = (window.currentParamsList || []).find(p => p.id == parentId);

            $('#formParam')[0].reset();
            $('#paramId').val('');
            $('#revisionId').val(currentRevisionId);
            $('#kolom_db').val('');

            populateParentDropdown(null, parentId);
            $('#boxPindahInduk').addClass('d-none'); // Sembunyikan dropdown pindah induk agar tidak bingung
            
            $('#lblNamaInduk').text(parentObj ? parentObj.nama_parameter : 'Parameter Terpilih');
            $('#infoIndukBox').removeClass('d-none');

            // Default untuk Anak adalah Kolom Isian Operator
            $('#jenis_input').prop('checked', true).trigger('change');
            $('#urutan').val((window.currentParamsList ? window.currentParamsList.length : 0) + 1);

            $('#modalTitle').html('<i class="fas fa-sitemap"></i> Tambah Sub-Bagian (Anak)');
            $('#modalForm').modal('show');
        });

        // C. Buka Modal: Edit Parameter
        $(document).on('click', '.btn-edit', function() {
            let rawData = $(this).data('raw');$('#formParam')[0].reset();

            $('#paramId').val(rawData.id);
            $('#revisionId').val(rawData.revision_id);
            $('#nama_parameter').val(rawData.nama_parameter);
            $('#kolom_db').val(rawData.kolom_db || '');
            $('#standar_nilai').val(rawData.standar_nilai || '');
            $('#batas_min').val(rawData.batas_min !== null ? rawData.batas_min : '');
            $('#batas_max').val(rawData.batas_max !== null ? rawData.batas_max : '');
            $('#urutan').val(rawData.urutan || 1);

            populateParentDropdown(rawData.id, rawData.parent_id);
            $('#boxPindahInduk').removeClass('d-none'); // Tampilkan fitur pindah induk khusus saat Edit
            $('#infoIndukBox').addClass('d-none');

            if (rawData.tipe_input || rawData.kolom_db) {
                $('#jenis_input').prop('checked', true).trigger('change');
                $('#tipe_input').val(rawData.tipe_input || 'text').trigger('change');
            } else {
                $('#jenis_header').prop('checked', true).trigger('change');
            }

            $('#modalTitle').html('<i class="fas fa-edit"></i> Edit Parameter');
            $('#modalForm').modal('show');
        });

        // D. Submit Simpan (Tambah / Edit)
        $('#formParam').submit(function(e) {
            e.preventDefault();
            let btn = $('#btnSimpanParam');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            $.ajax({
                url: "<?= base_url('form-builder/save') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Parameter');
                    if (res.status === 'success') {
                        $('#modalForm').modal('hide');
                        loadTableData(false);
                    } else {
                        alert(res.message || 'Gagal menyimpan parameter.');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Parameter');
                    console.error("Save Error:", xhr.responseText);
                    alert("Gagal menyimpan! Pastikan route 'form-builder/save' sudah ada di Routes.php.");
                }
            });
        });

        // E. Aksi Hapus
        $(document).on('click', '.btn-delete', function() {
            if (confirm('Yakin ingin menghapus bagian ini? Jika ini adalah Induk, semua sub-bagian di bawahnya juga akan ikut terhapus!')) {
                let id = $(this).data('id');$.ajax({
                    url: "<?= base_url('form-builder/delete') ?>",
                    type: "POST",
                    data: { id: id },
                    dataType: "json",
                    success: function(res) {
                        loadTableData(false);
                    }
                });
            }
        });

        // --- 4. AKSI NAIK REVISI ---
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