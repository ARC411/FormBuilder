<!-- ========================================================================= -->
<!-- MODAL POPUP AJAX DI TENGAH LAYAR UNTUK VALIDASI PENGGUNA (WEEKLY APPROVAL) -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalConfirmWeeklyApprove" tabindex="-1" aria-labelledby="modalConfirmWeeklyApproveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalConfirmWeeklyApproveLabel">
                    <i class="fas fa-check-circle me-2"></i>Validasi &amp; Konfirmasi Approval
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Alert Error / Sukses AJAX -->
                <div id="approveModalAlert" class="alert alert-danger d-none py-2 px-3 small mb-3 shadow-sm border-0" style="border-left: 4px solid #dc3545 !important;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2 fs-5 text-danger" id="approveModalAlertIcon"></i>
                        <div id="approveModalAlertText"></div>
                    </div>
                </div>

                <!-- Box Validasi Identitas Pengguna / Approver -->
                <div class="card bg-light border-0 mb-3 shadow-sm" style="border-radius: 8px;">
                    <div class="card-body p-3">
                        <h6 class="text-primary font-weight-bold mb-2 pb-1 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-shield me-1"></i> Identitas Validasi Pengguna</span>
                            <span class="badge bg-success shadow-sm" id="approveModalDeptBadge">QC</span>
                        </h6>
                        <table class="table table-sm table-borderless m-0 small">
                            <tr>
                                <td class="text-muted" style="width: 125px;">Nama Approver</td>
                                <td style="width: 10px;">:</td>
                                <td class="font-weight-bold text-dark"><?= esc(session()->get('name')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Emp ID / NIK</td>
                                <td>:</td>
                                <td class="font-weight-bold text-dark"><?= esc(session()->get('empid')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan / Posisi</td>
                                <td>:</td>
                                <td class="text-dark"><?= esc(session()->get('positionid') ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Departemen</td>
                                <td>:</td>
                                <td class="text-dark"><?= esc(session()->get('department') ?? 'QC / Production') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Box Rincian Dokumen Mingguan Target -->
                <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 py-2 px-3 small mb-3 rounded shadow-sm">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-calendar-check text-success me-2 fs-5"></i>
                        <div>
                            <span class="text-muted d-block small">Periode yang Disetujui:</span>
                            <strong class="text-dark fs-6" id="approveModalPeriod">-</strong>
                        </div>
                    </div>
                    <div class="text-muted small mt-1 pt-1 border-top border-success border-opacity-25">
                        Tipe Dokumen: <span class="badge bg-secondary" id="approveModalDocType">Startup Checksheet</span>
                        <span id="approveModalSpecificDoc" class="ms-2 d-none">
                            &bull; Target: <strong class="text-dark" id="approveModalMachLabel">-</strong>
                        </span>
                    </div>
                </div>

                <div class="text-muted small">
                    <i class="fas fa-info-circle text-primary me-1"></i>
                    Dengan menekan tombol <strong>Setujui (Approve)</strong>, Anda menyatakan secara resmi bahwa seluruh data checksheet pada periode ini telah diperiksa dan disetujui sesuai wewenang Anda.
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal" id="btnCancelWeeklyApprove">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success font-weight-bold shadow-sm" id="btnSubmitWeeklyApprove">
                    <i class="fas fa-check-double me-1"></i> <span id="btnSubmitWeeklyApproveText">Ya, Setujui (Approve)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalConfirmWeeklyApprove');
    if (!modalEl) return;

    let currentApproveTarget = {
        weekly_id: null,
        departemen: null,
        doc_type: 'startup',
        device: '',
        process: '',
        machno: '',
        period: '',
        formEl: null
    };

    // Handler klik pada semua tombol Approve Weekly (Event Delegation)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-open-approve-weekly');
        if (!btn) return;
        
        e.preventDefault();
        
        currentApproveTarget.weekly_id  = btn.getAttribute('data-weekly-id');
        currentApproveTarget.departemen = (btn.getAttribute('data-departemen') || 'qc').toLowerCase();
        currentApproveTarget.doc_type   = btn.getAttribute('data-doc-type') || 'startup';
        currentApproveTarget.device     = btn.getAttribute('data-device') || '';
        currentApproveTarget.process    = btn.getAttribute('data-process') || '';
        currentApproveTarget.machno     = btn.getAttribute('data-machno') || '';
        currentApproveTarget.period     = btn.getAttribute('data-period') || '-';
        currentApproveTarget.formEl     = btn.closest('form');

        const deptUpper = currentApproveTarget.departemen.toUpperCase();
        
        // Set data ke dalam modal
        document.getElementById('approveModalPeriod').textContent = currentApproveTarget.period;
        
        const badge = document.getElementById('approveModalDeptBadge');
        badge.textContent = deptUpper;
        badge.className = 'badge shadow-sm ' + (currentApproveTarget.departemen === 'qc' ? 'bg-success' : 'bg-primary');

        const docTypeLabel = (currentApproveTarget.doc_type === 'production') ? 'Production Checksheet (20 Data)' : 'Startup Checksheet (Mingguan)';
        document.getElementById('approveModalDocType').textContent = docTypeLabel;

        const specDocWrap = document.getElementById('approveModalSpecificDoc');
        const specDocLabel = document.getElementById('approveModalMachLabel');
        if (currentApproveTarget.machno || currentApproveTarget.process) {
            let labelText = (currentApproveTarget.machno ? ('Mesin ' + currentApproveTarget.machno) : 'Semua Mesin');
            if (currentApproveTarget.process) {
                labelText += ' (' + (currentApproveTarget.device ? currentApproveTarget.device.toUpperCase() + ' - ' : '') + currentApproveTarget.process + ')';
            }
            specDocLabel.textContent = labelText;
            specDocWrap.classList.remove('d-none');
        } else {
            specDocWrap.classList.add('d-none');
        }

        const submitBtnText = document.getElementById('btnSubmitWeeklyApproveText');
        submitBtnText.textContent = 'Ya, Setujui (' + deptUpper + ')';

        const submitBtn = document.getElementById('btnSubmitWeeklyApprove');
        submitBtn.className = 'btn font-weight-bold shadow-sm ' + (currentApproveTarget.departemen === 'qc' ? 'btn-success' : 'btn-primary');
        submitBtn.disabled = false;
        document.getElementById('btnCancelWeeklyApprove').disabled = false;

        // Reset Alert
        const alertBox = document.getElementById('approveModalAlert');
        alertBox.classList.add('d-none');
        alertBox.className = 'alert alert-danger d-none py-2 px-3 small mb-3 shadow-sm border-0';

        // Buka Modal di Tengah Layar
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    });

    // Handler Submit AJAX
    const btnSubmit = document.getElementById('btnSubmitWeeklyApprove');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function () {
            if (!currentApproveTarget.weekly_id) return;

            const submitBtn = this;
            const btnText = document.getElementById('btnSubmitWeeklyApproveText');
            const alertBox = document.getElementById('approveModalAlert');
            const alertText = document.getElementById('approveModalAlertText');
            const alertIcon = document.getElementById('approveModalAlertIcon');
            const cancelBtn = document.getElementById('btnCancelWeeklyApprove');

            // Set state loading
            submitBtn.disabled = true;
            cancelBtn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memvalidasi &amp; Menyetujui...';
            alertBox.classList.add('d-none');

            // Siapkan FormData
            const formData = new FormData();
            formData.append('weekly_id', currentApproveTarget.weekly_id);
            formData.append('departemen', currentApproveTarget.departemen);
            formData.append('action', 'Approve');
            formData.append('doc_type', currentApproveTarget.doc_type);
            if (currentApproveTarget.device) formData.append('device', currentApproveTarget.device);
            if (currentApproveTarget.process) formData.append('process', currentApproveTarget.process);
            if (currentApproveTarget.machno) formData.append('machno', currentApproveTarget.machno);

            // Ambil CSRF Token jika form atau halaman memiliki CSRF
            let csrfInput = currentApproveTarget.formEl ? currentApproveTarget.formEl.querySelector('input[type="hidden"][name^="csrf_"]') : null;
            if (!csrfInput) {
                csrfInput = document.querySelector('input[type="hidden"][name^="csrf_"]');
            }
            if (csrfInput) {
                formData.append(csrfInput.name, csrfInput.value);
            }

            fetch('<?= base_url("approval/processWeekly") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return {
                        status: response.status,
                        ok: response.ok,
                        data: data
                    };
                }).catch(function () {
                    return {
                        status: response.status,
                        ok: response.ok,
                        data: {
                            status: response.ok ? 'success' : 'error',
                            message: 'Respon dari server telah diterima.'
                        }
                    };
                });
            })
            .then(function (res) {
                if (res.ok && res.data.status === 'success') {
                    // Tampilkan Sukses
                    btnText.innerHTML = '<i class="fas fa-check me-1"></i> Berhasil Disetujui!';
                    alertBox.className = 'alert alert-success py-2 px-3 small mb-3 shadow-sm border-0';
                    alertBox.style.borderLeft = '4px solid #198754 !important';
                    alertIcon.className = 'fas fa-check-circle me-2 fs-5 text-success';
                    alertText.textContent = res.data.message || 'Persetujuan mingguan berhasil divalidasi dan disimpan.';
                    alertBox.classList.remove('d-none');

                    // Auto-reload halaman agar tabel & status ter-update secara resmi
                    setTimeout(function () {
                        window.location.reload();
                    }, 800);
                } else {
                    // Tampilkan Error Validasi
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    btnText.textContent = 'Ya, Setujui (' + currentApproveTarget.departemen.toUpperCase() + ')';

                    alertBox.className = 'alert alert-danger py-2 px-3 small mb-3 shadow-sm border-0';
                    alertBox.style.borderLeft = '4px solid #dc3545 !important';
                    alertIcon.className = 'fas fa-exclamation-triangle me-2 fs-5 text-danger';
                    alertText.textContent = res.data.message || 'Gagal memproses persetujuan. Mohon periksa kembali.';
                    alertBox.classList.remove('d-none');
                }
            })
            .catch(function (err) {
                submitBtn.disabled = false;
                cancelBtn.disabled = false;
                btnText.textContent = 'Ya, Setujui (' + currentApproveTarget.departemen.toUpperCase() + ')';

                alertBox.className = 'alert alert-danger py-2 px-3 small mb-3 shadow-sm border-0';
                alertBox.style.borderLeft = '4px solid #dc3545 !important';
                alertIcon.className = 'fas fa-exclamation-triangle me-2 fs-5 text-danger';
                alertText.textContent = 'Terjadi kesalahan jaringan atau koneksi server terputus.';
                alertBox.classList.remove('d-none');
            });
        });
    }
});
</script>
