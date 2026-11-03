<?= $this->extend('layout/templ-home'); ?>

<?= $this->section('content'); ?>
<main>
<?= $this->include('layout/navbar'); ?>
    <link rel="stylesheet" href="assets/css/daterangepicker.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
        .lotno-link:hover {
            text-decoration : underline;
            color: #007bff;
            cursor: pointer;
        }
    </style>

    <?php
        $session = \Config\Services::session();
        $posStr = (string)$session->get('positionid');
        $isAdmin = (bool)$session->get('isadmin');
        $isOperator = ($isOperator ?? (stripos($posStr, 'operator') !== false && !$isAdmin));

        $userEmp = trim((string)$session->get('empid'));
        $userName = trim((string)$session->get('name'));

        $myRejectedForms = [];
        if (!empty($alldata)) {
            foreach ($alldata as $row) {
                if (($row['status'] ?? '') === 'Reject') {
                    $myRejectedForms[] = $row;
                }
            }
        }
    ?>

    <div class="container-fluid px-4 mt-3 mb-5">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-1"></i> <?= esc(session()->getFlashdata('message')); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($myRejectedForms)): ?>
            <div class="alert alert-danger shadow-sm border-danger d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading m-0 font-weight-bold">
                        Perhatian: Terdapat <?= count($myRejectedForms) ?> Checksheet Startup yang Ditolak (Reject)!
                    </h5>
                    <div class="small mt-1">
                        Silakan periksa catatan penolakan dari Approver pada tabel di bawah, lalu klik tombol 
                        <span class="badge bg-danger"><i class="fas fa-tools"></i> Perbaiki Form</span> untuk melakukan perbaikan data.
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-5">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-rocket me-2"></i>Data Startup
                </h3>
            </div>
            <div class="card-body">
                <!-- Filter Bar: Date Range, Device (Doc Type), Status Approval -->
                <div class="card bg-light border-0 shadow-sm mb-4">
                    <div class="card-body py-3">
                        <div class="row g-3 align-items-end">
                            <!-- 1. Filter Date Range -->
                            <div class="col-lg-4 col-md-6 col-12">
                                <label for="daterange" class="form-label font-weight-bold small text-muted mb-1">
                                    <i class="fa fa-calendar text-primary me-1"></i>Date Range
                                </label>
                                <div id="daterange" class="form-control" style="background: #fff; cursor: pointer; padding: 6px 12px; border: 1px solid #ced4da; width: 100%; border-radius: 6px;">
                                    <i class="fa fa-calendar text-muted"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down float-end mt-1 text-muted"></i>
                                </div>
                            </div>

                            <!-- 2. Filter Device / Doc Type -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <label for="filter-device" class="form-label font-weight-bold small text-muted mb-1">
                                    <i class="fa fa-microchip text-success me-1"></i>Doc Type / Device
                                </label>
                                <select class="form-select form-control" id="filter-device" style="border-radius: 6px;">
                                    <option value="">-- Semua Device --</option>
                                    <option value="fl18">Frame Laser 1.8</option>
                                    <option value="sl33">Single Laser 3.3</option>
                                    <option value="sl38">Single Laser 3.8</option>
                                    <option value="sl56">Single Laser 5.6</option>
                                    <option value="sl90">Single Laser 9.0</option>
                                </select>
                            </div>

                            <!-- 3. Filter Status Approval -->
                            <div class="col-lg-3 col-md-6 col-12">
                                <label for="filter-status" class="form-label font-weight-bold small text-muted mb-1">
                                    <i class="fa fa-check-circle text-info me-1"></i>Status Approval
                                </label>
                                <select class="form-select form-control" id="filter-status" style="border-radius: 6px;">
                                    <option value="">-- Semua Status --</option>
                                    <option value="approved">Sudah Approve (Approved)</option>
                                    <option value="pending">Belum Approve (Pending)</option>
                                    <option value="reject">Reject (Ditolak)</option>
                                </select>
                            </div>

                            <!-- 4. Tombol Reset Filter -->
                            <div class="col-lg-2 col-md-6 col-12">
                                <button type="button" class="btn btn-outline-secondary w-100" id="btn-reset-filters" title="Reset Semua Filter ke Tampilan Awal">
                                    <i class="fas fa-undo me-1"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted small">Klik Lot No untuk melihat detail form checksheet</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mt-2" id="table-approval">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th style="width: 140px;">Waktu Input</th>
                                <th>Device</th>
                                <th>Process</th>
                                <th>Lot No</th>
                                <th style="width: 110px;">Emp ID</th>
                                <th style="width: 190px;">Status</th>
                                <th style="width: 170px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i < count($alldata); $i++) { 
                                $editStatus = (int)($alldata[$i]['edit_status'] ?? 0);
                                $num = $alldata[$i]['number'];
                                $rowStatus = $alldata[$i]['status'] ?? 'Pending';
                                $isRowReject = ($rowStatus === 'Reject');
                                $lotVal = isset($alldata[$i]['lotno']) ? trim((string)$alldata[$i]['lotno']) : '';
                                $procLabel = $alldata[$i]['process_name'] ?? $alldata[$i]['name'] ?? '';
                                $machVal = !empty($alldata[$i]['machno']) ? $alldata[$i]['machno'] : '-';
                                $rawDate = $alldata[$i]['created_at'] ?? '';
                                $dateVal = !empty($rawDate) ? date('d/m/Y H:i', strtotime($rawDate)) : '-';
                                $dateYmd = !empty($rawDate) ? date('Y-m-d', strtotime($rawDate)) : '';
                                $opName = !empty($alldata[$i]['name']) ? $alldata[$i]['name'] : $userName;
                                $devName = trim((string)($alldata[$i]['device_name'] ?? ''));
                                $devCode = trim((string)($alldata[$i]['device'] ?? $alldata[$i]['device_code'] ?? ''));
                                $procRaw = trim((string)($alldata[$i]['process'] ?? ''));
                                if ($devCode === '' && !empty($procRaw)) {
                                    $procParts = explode('-', $procRaw);
                                    $devCode = strtolower($procParts[0]);
                                }

                                // Klasifikasi status untuk filter (Bypass Leader & Bypass Supervisor termasuk status approved)
                                $isApproved = (
                                    stripos($rowStatus, 'approve') !== false ||
                                    stripos($rowStatus, 'bypass') !== false ||
                                    stripos($rowStatus, 'check') !== false
                                );
                                if ($isRowReject) {
                                    $statusType = 'reject';
                                } elseif ($isApproved) {
                                    $statusType = 'approved';
                                } else {
                                    $statusType = 'pending';
                                }
                            ?>
                                <tr class="<?= $isRowReject ? 'table-danger border-start border-danger border-4' : '' ?>"
                                    data-device-code="<?= esc($devCode); ?>"
                                    data-device-name="<?= esc($devName); ?>"
                                    data-process="<?= esc($procRaw); ?>"
                                    data-status="<?= esc($statusType); ?>"
                                    data-date="<?= esc($dateYmd); ?>">
                                    <td class="text-center font-weight-bold"><?= $i+1; ?></td>
                                    <td class="text-center" data-order="<?= esc($rawDate); ?>" data-date="<?= esc($dateYmd); ?>"><?= $dateVal; ?></td>
                                    <td class="text-center" data-device-code="<?= esc($devCode); ?>" data-device-name="<?= esc($devName); ?>" data-process="<?= esc($procRaw); ?>">
                                        <span class="badge bg-secondary"><?= esc($devName ?: $devCode); ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($procLabel); ?></strong>
                                        <?php if($machVal !== '-'): ?>
                                            <div class="small text-muted">Mesin: <strong><?= esc($machVal); ?></strong></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="lotno-link font-weight-bold text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-lotno="<?= esc($lotVal); ?>" data-number="<?= $num; ?>" data-process="<?= esc($procLabel); ?>" data-machno="<?= esc($machVal); ?>">
                                            <?= $lotVal !== '' ? esc($lotVal) : 'Lihat Detail'; ?>
                                        </a>
                                    </td>
                                    <td class="text-center font-monospace small"><?= esc($alldata[$i]['empid'] ?? ''); ?></td>
                                    <td class="text-center" data-status="<?= esc($statusType); ?>">
                                        <?php if ($isRowReject): ?>
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                            <?php if (!empty($alldata[$i]['reject_reason'])): ?>
                                                <div class="small text-danger text-start mt-1 p-1 bg-white border border-danger rounded shadow-sm" style="font-size: 0.75rem;">
                                                    <strong>Alasan:</strong> <?= esc($alldata[$i]['reject_reason']); ?>
                                                    <?php if (!empty($alldata[$i]['reject_by'])): ?>
                                                        <div class="text-muted" style="font-size: 0.7rem;">Oleh: <?= esc($alldata[$i]['reject_by']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php elseif ($editStatus === 1): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-envelope-open-text me-1"></i>Menunggu Reject Foreman</span>
                                        <?php elseif ($isApproved): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i><?= esc($rowStatus ?: 'Approved'); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="fa-solid fa-lock me-1"></i><?= esc($rowStatus ?: 'Pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isRowReject): ?>
                                            <button type="button" class="btn btn-sm btn-danger edit-button font-weight-bold shadow-sm" data-number="<?= $num; ?>" title="Perbaiki Form Ini">
                                                <i class="fas fa-tools me-1"></i> Perbaiki Form
                                            </button>
                                        <?php elseif ($isOperator): ?>
                                            <button type="button" class="btn btn-sm btn-warning request-reject-btn"
                                                data-number="<?= $num; ?>"
                                                data-date="<?= esc($dateVal); ?>"
                                                data-name="<?= esc($opName); ?>"
                                                data-empid="<?= esc($alldata[$i]['empid'] ?? ''); ?>"
                                                data-machno="<?= esc($machVal); ?>"
                                                data-process="<?= esc($procLabel); ?>"
                                                data-lotno="<?= esc($lotVal ?: '-'); ?>">
                                                <i class="fas fa-envelope me-1"></i> <?= $editStatus === 1 ? 'Kirim Ulang Reject' : 'Minta Reject'; ?>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-primary edit-button" data-number="<?= $num; ?>" title="Edit Form">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </button>
                                            <?php if ($isAdmin): ?>
                                                <button type="button" class="btn btn-sm btn-danger delete-button" data-number="<?= $num; ?>" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Lotno -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Detail Checksheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <div id="loading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pengajuan Reject via Gmail ke Foreman -->
    <div class="modal fade" id="requestRejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title font-weight-bold text-dark">
                        <i class="fas fa-envelope me-2"></i>Ajukan Permintaan Reject ke Foreman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="req-number">
                    <div class="mb-3 p-2 bg-light border rounded small">
                        <div><strong>Tanggal Input:</strong> <span id="req-date"></span></div>
                        <div><strong>Nama / Emp ID:</strong> <span id="req-name"></span> (<span id="req-empid"></span>)</div>
                        <div><strong>Process:</strong> <span id="req-process"></span></div>
                        <div><strong>Machine No:</strong> <span id="req-machno"></span> | <strong>Lot No:</strong> <span id="req-lotno"></span></div>
                    </div>
                    <div class="mb-3">
                        <label for="req-reason" class="form-label font-weight-bold">Catatan / Alasan Minta Reject <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="req-reason" rows="3" placeholder="Tuliskan alasan mengapa form ini perlu di-reject (misal: salah input nilai parameter Air Pressure)..." required></textarea>
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>Email akan dikirimkan secara otomatis ke seluruh alamat email <strong>Foreman</strong>. Setelah Foreman melakukan Reject di halaman Approval, tombol <strong>Perbaiki Form</strong> akan terbuka.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning font-weight-bold" id="btn-submit-reject-req">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Email ke Foreman
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/dataTable.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/js/daterangepicker.min.js"></script>
<script src="assets/js/dataTables.bootstrap4.js"></script>
<script>
    $(document).ready(function() {
        var minDateFilter = null;
        var maxDateFilter = null;

        // Baca filter tersimpan dari sessionStorage agar setelah hapus atau reload, filter tidak hilang
        var savedDevice = sessionStorage.getItem('startup_filter_device') || '';
        var savedStatus = sessionStorage.getItem('startup_filter_status') || '';
        var savedDateStart = sessionStorage.getItem('startup_filter_date_start');
        var savedDateEnd = sessionStorage.getItem('startup_filter_date_end');
        var savedDateLabel = sessionStorage.getItem('startup_filter_date_label');

        var selectedDevice = savedDevice;
        var selectedStatus = savedStatus;

        if (savedDevice) {
            $('#filter-device').val(savedDevice);
        }
        if (savedStatus) {
            $('#filter-status').val(savedStatus);
        }

        // Custom filtering function untuk DataTable (Date Range + Device + Status)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'table-approval') {
                return true;
            }

            var rowNode = settings.aoData[dataIndex].nTr;
            var $row = $(rowNode);

            // 1. Filter Tanggal (kolom 1: Waktu Input)
            if (minDateFilter && maxDateFilter) {
                var rowDate = $row.attr('data-date') || $row.find('td:eq(1)').attr('data-date') || $row.find('td:eq(1)').data('date');
                if (!rowDate) {
                    var cellText = (data[1] || '').trim();
                    var match = cellText.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
                    if (match) {
                        rowDate = match[3] + '-' + match[2] + '-' + match[1];
                    }
                }
                if (rowDate && (rowDate < minDateFilter || rowDate > maxDateFilter)) {
                    return false;
                }
            }

            // 2. Filter Device / Doc Type (kolom 2: Device)
            if (selectedDevice) {
                var devCode = ($row.attr('data-device-code') || $row.find('td:eq(2)').attr('data-device-code') || $row.find('td:eq(2)').data('device-code') || '').toString().toLowerCase();
                var devName = ($row.attr('data-device-name') || $row.find('td:eq(2)').attr('data-device-name') || $row.find('td:eq(2)').data('device-name') || data[2] || '').toString().toLowerCase();
                var procCode = ($row.attr('data-process') || $row.find('td:eq(2)').attr('data-process') || '').toString().toLowerCase();
                var qDev = selectedDevice.toLowerCase();

                var isDeviceMatch = false;
                if (devCode === qDev || devCode.indexOf(qDev) !== -1) {
                    isDeviceMatch = true;
                } else if (procCode.indexOf(qDev) === 0 || procCode.indexOf(qDev) !== -1) {
                    isDeviceMatch = true;
                } else if (qDev === 'fl18' && (devName.indexOf('1.8') !== -1 || devName.indexOf('frame laser') !== -1 || devName.indexOf('fl') !== -1)) {
                    isDeviceMatch = true;
                } else if (qDev === 'sl33' && (devName.indexOf('3.3') !== -1 || devName.indexOf('sl') !== -1)) {
                    isDeviceMatch = true;
                } else if (qDev === 'sl38' && devName.indexOf('3.8') !== -1) {
                    isDeviceMatch = true;
                } else if (qDev === 'sl56' && devName.indexOf('5.6') !== -1) {
                    isDeviceMatch = true;
                } else if (qDev === 'sl90' && devName.indexOf('9.0') !== -1) {
                    isDeviceMatch = true;
                } else if (devName.indexOf(qDev) !== -1) {
                    isDeviceMatch = true;
                }

                if (!isDeviceMatch) {
                    return false;
                }
            }

            // 3. Filter Status Approval (kolom 6: Status)
            if (selectedStatus) {
                var rowStatus = ($row.attr('data-status') || $row.find('td:eq(6)').attr('data-status') || $row.find('td:eq(6)').data('status') || '').toString().toLowerCase();
                var qStatus = selectedStatus.toLowerCase();

                if (rowStatus) {
                    if (rowStatus !== qStatus) {
                        return false;
                    }
                } else {
                    var cellText = (data[6] || '').toLowerCase();
                    var isCellApproved = (cellText.indexOf('approve') !== -1 || cellText.indexOf('bypass') !== -1 || cellText.indexOf('check') !== -1);
                    var isCellReject = (cellText.indexOf('reject') !== -1);

                    if (qStatus === 'approved' && !isCellApproved) return false;
                    if (qStatus === 'reject' && !isCellReject) return false;
                    if (qStatus === 'pending' && (isCellApproved || isCellReject)) return false;
                }
            }

            return true;
        });

        var table = $('#table-approval').DataTable({
            order: [],
            pageLength: 35,
            lengthMenu: [[10, 20, 35], [10, 20, 35]],
            stateSave: true
        });

        // Update nomor urut otomatis saat data difilter / di-search / di-draw tanpa memanggil .nodes() agar tidak error anCells saat delete row
        table.on('draw.dt', function () {
            var info = table.page.info();
            $('#table-approval tbody tr').each(function (i) {
                if (!$(this).hasClass('dataTables_empty') && $(this).children('td').length > 1) {
                    $(this).find('td:first').text(info.start + i + 1);
                }
            });
        });

        var urlStart = '<?= !empty($dateStart) ? esc($dateStart) : '' ?>';
        var urlEnd = '<?= !empty($dateEnd) ? esc($dateEnd) : '' ?>';

        var start = moment().subtract(29, 'days');
        var end = moment();
        var isAllInitial = false;

        if (urlStart && urlEnd) {
            start = moment(urlStart);
            end = moment(urlEnd);
        } else if (savedDateLabel === 'Semua Tanggal') {
            isAllInitial = true;
        } else if (savedDateStart && savedDateEnd) {
            start = moment(savedDateStart);
            end = moment(savedDateEnd);
        }

        function cb(start, end, isAll, label) {
            if (isAll) {
                $('#daterange span').html('<i class="fas fa-calendar-alt text-muted me-1"></i> Semua Tanggal');
                minDateFilter = null;
                maxDateFilter = null;
                sessionStorage.setItem('startup_filter_date_label', 'Semua Tanggal');
                sessionStorage.removeItem('startup_filter_date_start');
                sessionStorage.removeItem('startup_filter_date_end');
            } else {
                $('#daterange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                minDateFilter = start.format('YYYY-MM-DD');
                maxDateFilter = end.format('YYYY-MM-DD');
                sessionStorage.setItem('startup_filter_date_label', label || '');
                sessionStorage.setItem('startup_filter_date_start', minDateFilter);
                sessionStorage.setItem('startup_filter_date_end', maxDateFilter);
            }
            if (table) {
                table.draw();
            }
        }

        $('#daterange').daterangepicker({
            startDate: isAllInitial ? moment().subtract(29, 'days') : start,
            endDate: isAllInitial ? moment() : end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Semua Tanggal': [moment('2020-01-01'), moment().add(1, 'year')]
            }
        }, function(start, end, label) {
            if (label === 'Semua Tanggal') {
                cb(null, null, true, label);
            } else {
                cb(start, end, false, label);
            }
        });

        // Inisialisasi filter tanggal
        cb(start, end, isAllInitial, savedDateLabel || (isAllInitial ? 'Semua Tanggal' : 'Last 30 Days'));

        // Event listener dropdown Device
        $('#filter-device').on('change', function() {
            selectedDevice = $(this).val();
            sessionStorage.setItem('startup_filter_device', selectedDevice);
            table.draw();
        });

        // Event listener dropdown Status
        $('#filter-status').on('change', function() {
            selectedStatus = $(this).val();
            sessionStorage.setItem('startup_filter_status', selectedStatus);
            table.draw();
        });

        // Tombol Reset Semua Filter
        $('#btn-reset-filters').on('click', function() {
            selectedDevice = '';
            selectedStatus = '';
            $('#filter-device').val('');
            $('#filter-status').val('');
            sessionStorage.removeItem('startup_filter_device');
            sessionStorage.removeItem('startup_filter_status');
            sessionStorage.removeItem('startup_filter_date_label');
            sessionStorage.removeItem('startup_filter_date_start');
            sessionStorage.removeItem('startup_filter_date_end');
            cb(null, null, true, 'Semua Tanggal');
        });

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var number = button.data('number');
            var lotno = button.data('lotno');
            var process = button.data('process');
            var machno = button.data('machno');
            var modal = $(this);
            var loading = $('#loading');
            
            var title = process + ' - ' + (lotno || '-');
            if (machno && machno !== '-') {
                title += ' - Machine: ' + machno;
            }
            $('#modalTitle').text(title);

            loading.show();

            $.ajax({
                url: "<?= base_url('startup/history/') ?>" + number,
                dataType: 'html',
                success: function(data) {
                    loading.hide();
                    modal.find('.modal-body').html(data);
                    
                    var fetchedMachno = modal.find('#detail-machno').val();
                    if (fetchedMachno && (!machno || machno === '-')) {
                        $('#modalTitle').text(process + ' - ' + (lotno || '-') + ' - Machine: ' + fetchedMachno);
                    }
                },
                error: function() {
                    loading.hide();
                    modal.find('.modal-body').html('<p class="text-danger">Error loading data.</p>');
                },
            });
        });

        // Klik Perbaiki Form / Edit
        $(document).on('click', '.edit-button', function() {
            var number = $(this).data('number');
            window.location.href = "<?= base_url('startup/edit/') ?>" + number;
        });

        // Buka Modal Minta Reject
        $(document).on('click', '.request-reject-btn', function() {
            var btn = $(this);
            $('#req-number').val(btn.data('number'));
            $('#req-date').text(btn.data('date'));
            $('#req-name').text(btn.data('name'));
            $('#req-empid').text(btn.data('empid'));
            $('#req-process').text(btn.data('process'));
            $('#req-machno').text(btn.data('machno'));
            $('#req-lotno').text(btn.data('lotno'));
            $('#req-reason').val('');
            $('#requestRejectModal').modal('show');
        });

        // Kirim Email Permintaan Reject ke Foreman di Latar Belakang
        $('#btn-submit-reject-req').on('click', function() {
            var number = $('#req-number').val();
            var reason = $.trim($('#req-reason').val());
            if (!reason) {
                alert('Silakan isi alasan minta reject terlebih dahulu!');
                $('#req-reason').focus();
                return;
            }

            var btn = $(this);
            var oldHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengirim Email...');

            $.ajax({
                url: "<?= base_url('startup/request-reject/') ?>" + number,
                method: 'POST',
                data: { reason: reason },
                dataType: 'JSON',
                success: function(res) {
                    btn.prop('disabled', false).html(oldHtml);
                    $('#requestRejectModal').modal('hide');
                    alert(res.message || 'Permintaan Reject berhasil dikirim ke Foreman.');
                    location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(oldHtml);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal mengirim email permintaan reject.';
                    alert(msg);
                }
            });
        });

        // Klik Delete (Khusus Admin): hapus data via AJAX dan hapus row dari DataTables tanpa reload
        $(document).on('click', '.delete-button', function() {
            var $btn = $(this);
            var $tr = $btn.closest('tr');
            var number = $btn.data('number');
            if (confirm('Apakah kamu yakin ingin menghapus checksheet #' + number + '?')) {
                $btn.prop('disabled', true);
                $.ajax({
                    url: "<?= base_url('startup/') ?>" + number,
                    type: 'DELETE',
                    success: function() {
                        // Hapus baris dari DataTables secara realtime tanpa me-reload halaman
                        table.row($tr).remove().draw(false);
                        alert('Checksheet #' + number + ' berhasil dihapus.');
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false);
                        alert('Error deleting data: ' + (xhr.statusText || 'Gagal menghapus'));
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection(); ?>