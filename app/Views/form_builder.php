<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
    <?= $this->include('layout/navbar'); ?>

    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <style>
        .indent-1 {
            padding-left: 30px !important;
        }

        .indent-2 {
            padding-left: 60px !important;
        }

        .indent-3 {
            padding-left: 90px !important;
        }

        .row-parent {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid mt-4">
        <div class="card shadow mb-5">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold"><i class="fas fa-hammer"></i> Form Builder & Revision Manager</h4>
            </div>

            <div class="card-body">
                <!-- 1. CASCADING DROPDOWN FILTER -->
                <div class="row mb-4 bg-light p-3 rounded">
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
                <div id="revisionHeader" class="d-none mb-3 d-flex justify-content-between align-items-center border-bottom pb-2">
                    <h5 class="text-danger font-weight-bold m-0">
                        Status Dokumen: <span id="lblRevisi" class="badge bg-danger">Revisi -</span>
                    </h5>
                    <div>
                        <button class="btn btn-success btn-sm me-2" id="btnAddUtama">
                            <i class="fas fa-plus-circle"></i> Tambah Parameter Utama
                        </button>
                        <button class="btn btn-warning btn-sm font-weight-bold" id="btnUpRevisi">
                            <i class="fas fa-level-up-alt"></i> NAIKKAN REVISI
                        </button>
                    </div>
                </div>

                <!-- 3. TABEL HIERARKI PARAMETER -->
                <div class="table-responsive d-none" id="tableContainer">
                    <table class="table table-bordered table-hover" id="tblParameter">
                        <thead class="bg-dark text-white text-center">
                            <tr>
                                <th width="40%">Nama Parameter / Elemen</th>
                                <th width="15%">Kolom DB</th>
                                <th width="15%">Tipe Input</th>
                                <th width="15%">Standar (Nilai)</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyParameter">
                            <!-- Data hasil AJAX masuk ke sini -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CRUD PARAMETER -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formParam">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTitle">Tambah Parameter</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="paramId" name="id">
                        <input type="hidden" id="revisionId" name="revision_id">
                        <input type="hidden" id="parentId" name="parent_id">

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Nama Parameter (Label)</label>
                            <input type="text" class="form-control" id="nama_parameter" name="nama_parameter" required>
                        </div>

                        <!-- Area Khusus Anak Paling Bawah -->
                        <div class="bg-light p-3 border rounded mb-3">
                            <small class="text-danger font-weight-bold d-block mb-2">* Isi bagian ini HANYA jika parameter ini butuh inputan operator (ujung tabel).*</small>
                            <div class="mb-2">
                                <label class="form-label">Tujuan Kolom DB (Misal: par001)</label>
                                <input type="text" class="form-control" id="kolom_db" name="kolom_db">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Tipe Input</label>
                                <select class="form-select" id="tipe_input" name="tipe_input">
                                    <option value="">-- Hanya Judul (Tidak ada input) --</option>
                                    <option value="text">Teks Bebas</option>
                                    <option value="number">Angka (Yield/Qty)</option>
                                    <option value="dropdown_ok_ng">Dropdown (OK/NG)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Standar / Acuan (Opsional)</label>
                                <input type="text" class="form-control" id="standar_nilai" name="standar_nilai" placeholder="Contoh: Max 5 atau Harus OK">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Urutan (Kiri ke Kanan)</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" value="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Parameter</button>
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
        let loadedProcessCode = null;

        // --- 1. LOGIKA CASCADING DROPDOWN ---
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
            let type = $(this).val();

            if (type !== '') {
                $('#processSelect').html('<option>Loading...</option>').prop('disabled', true);
                $.ajax({
                    url: "<?= base_url('form-builder/get-processes') ?>",
                    type: "GET",
                    data: {
                        device_code: device,
                        type: type
                    },
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


        // --- 2. LOAD DATA KE TABEL (VERSI REVISI ID) ---
        $('#btnLoadForm').on('click', function(e) {
            e.preventDefault();

            // 1. Ambil ID yang BENAR dari HTML Abang (processSelect)
            var processCode = $('#processSelect').val();

            // 2. Cegah eksekusi kalau dropdown belum dipilih
            if (!processCode) {
                alert("Pilih proses mesinnya dulu, Bang!");
                return;
            }

            // (Opsional) Ubah tombol jadi loading biar interaktif
            let btn = $(this);
            let originalText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            btn.prop('disabled', true);

            // 3. Jalankan AJAX
            $.ajax({
                url: '<?= base_url('form-builder/load') ?>',
                type: 'POST',
                data: {
                    process: processCode
                },
                dataType: 'json',
                success: function(response) {
                    // Kembalikan tombol seperti semula
                    btn.html(originalText);
                    btn.prop('disabled', false);

                    if (response.status === 'success') {
                        // Sembunyikan wadah tabel list lama bawaan HTML Abang
                        $('#tableContainer').addClass('d-none');
                        $('#revisionHeader').addClass('d-none');
                        
                        // Buat wadah baru untuk Visual Builder jika belum ada, lalu tempel HTML-nya
                        if ($('#visualBuilderContainer').length === 0) {
                            $('.card-body').append('<div id="visualBuilderContainer" class="mt-4 border-top pt-4"></div>');
                        }
                        $('#visualBuilderContainer').html(response.html);
                    }
                },
                error: function(xhr, status, error) {
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    alert("Gagal memuat form! Cek tab Network atau Console.");
                }
            });
        });

        // Fungsi Render Silsilah (Rekursif)
        function renderTable(params) {
            let tbody = $('#tbodyParameter');
            tbody.empty();

            if (!params || params.length === 0) {
                tbody.append('<tr><td colspan="5" class="text-center text-muted py-4">Belum ada parameter. Silakan klik Tambah Parameter Utama.</td></tr>');
                return;
            }

            // Pisahkan bapak dan anak
            let map = {};
            let roots = [];
            params.forEach(p => {
                map[p.id] = p;
                p.children = [];
            });
            params.forEach(p => {
                if (p.parent_id !== null && map[p.parent_id]) {
                    map[p.parent_id].children.push(p);
                } else {
                    roots.push(p);
                }
            });

            // Gambar ke HTML
            function drawRow(item, level) {
                let indentClass = 'indent-' + level;
                let icon = level > 0 ? '↳ ' : '';
                let rowClass = level === 0 ? 'row-parent' : '';

                let btnAction = `
                <button class="btn btn-info btn-sm text-white btn-add-child" data-id="${item.id}" title="Tambah Anak"><i class="fas fa-plus"></i></button>
                <button class="btn btn-warning btn-sm text-white btn-edit" data-id="${item.id}" title="Edit"><i class="fas fa-pen"></i></button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id}" title="Hapus"><i class="fas fa-minus"></i></button>
            `;

                let html = `
                <tr class="${rowClass}">
                    <td class="${indentClass}">${icon} ${item.nama_parameter}</td>
                    <td class="text-center">${item.kolom_db || '-'}</td>
                    <td class="text-center">${item.tipe_input || '-'}</td>
                    <td class="text-center">${item.standar_nilai || '-'}</td>
                    <td class="text-center">${btnAction}</td>
                </tr>
            `;
                tbody.append(html);

                // Simpan data mentah di baris untuk ditarik saat mau diedit
                tbody.find('tr:last').data('raw', item);

                item.children.forEach(child => drawRow(child, level + 1));
            }

            roots.forEach(root => drawRow(root, 0));
        }


        // --- 3. AKSI CRUD MODAL ---

        // Buka Modal (Tambah Bapak)
        $('#btnAddUtama').click(function() {
            $('#formParam')[0].reset();
            $('#paramId').val('');
            $('#parentId').val('');
            $('#revisionId').val(currentRevisionId);
            $('#modalTitle').text('Tambah Parameter Utama');
            $('#modalForm').modal('show');
        });

        // Buka Modal (Tambah Anak)
        $(document).on('click', '.btn-add-child', function() {
            let parentId = $(this).data('id');
            $('#formParam')[0].reset();
            $('#paramId').val('');
            $('#parentId').val(parentId);
            $('#revisionId').val(currentRevisionId);
            $('#modalTitle').text('Tambah Sub-Parameter (Anak)');
            $('#modalForm').modal('show');
        });

        // Buka Modal (Edit)
        $(document).on('click', '.btn-edit', function() {
            let rawData = $(this).closest('tr').data('raw');
            $('#formParam')[0].reset();
            $('#paramId').val(rawData.id);
            $('#parentId').val(rawData.parent_id);
            $('#revisionId').val(rawData.revision_id);

            $('#nama_parameter').val(rawData.nama_parameter);
            $('#kolom_db').val(rawData.kolom_db);
            $('#tipe_input').val(rawData.tipe_input);
            $('#standar_nilai').val(rawData.standar_nilai);
            $('#urutan').val(rawData.urutan);

            $('#modalTitle').text('Edit Parameter');
            $('#modalForm').modal('show');
        });

        // Submit Modal (Simpan/Update)
        $('#formParam').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('form-builder/save') ?>",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#modalForm').modal('hide');
                    loadTableData(); // Refresh tabel
                }
            });
        });

        // Aksi Delete
        $(document).on('click', '.btn-delete', function() {
            if (confirm('Yakin ingin menghapus parameter ini? JIKA INI BAPAK, SEMUA ANAKNYA AKAN IKUT TERHAPUS!')) {
                let id = $(this).data('id');
                $.ajax({
                    url: "<?= base_url('form-builder/delete') ?>",
                    type: "POST",
                    data: {
                        id: id
                    },
                    success: function(res) {
                        loadTableData();
                    }
                });
            }
        });

        // --- 4. AKSI NAIK REVISI ---
        $('#btnUpRevisi').click(function() {
            if (confirm('PERINGATAN ISO: Anda yakin ingin menaikkan huruf revisi? Semua data di form ini akan diduplikasi menjadi versi baru.')) {
                $.ajax({
                    url: "<?= base_url('form-builder/up-revisi') ?>",
                    type: "POST",
                    data: {
                        revision_id: currentRevisionId
                    },
                    success: function(res) {
                        alert(res.message);
                        loadTableData(); // Akan otomatis memuat revisi yang baru!
                    }
                });
            }
        });

    });
</script>
<?= $this->endSection(); ?>