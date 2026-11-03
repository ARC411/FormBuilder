<table class="table table-striped-columns table-responsive mt-3" id="table-approval">
    <thead align="center">
        <tr>
            <th style="width: 50px;">No.</th>
            <th style="width: 140px;">Date</th>         
            <th>Device</th>
            <th>Process</th>
            <th style="width: 200px;">Status</th>
            <th style="width: 160px;">Person</th>
            <th style="width: 170px;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($alldata); $i++) { ?>
            <?php 
                $st = $alldata[$i]['status'] ?? '';
                $isReject = ($st === 'Reject');
                // UPDATE: Menambahkan 'Request Reject' ke dalam kondisi Pending
                $isRequestReject = ($st === 'Request Reject');
                $isPending = ($st === 'Pending' || $st === '' || $st === '-' || $isRequestReject);
                $isWeeklyReject = (strpos($alldata[$i]['reject_reason'] ?? '', 'Weekly Reject') !== false || strpos($alldata[$i]['reject_by'] ?? '', 'Weekly') !== false);
            ?>
            <tr class="<?= $isReject ? 'table-danger' : '' ?>">
                <td class="text-center font-weight-bold"><?= $i+1; ?></td>
                <td class="text-center"><?= !empty($alldata[$i]['created_at']) ? date('d/m/Y H:i', strtotime($alldata[$i]['created_at'])) : '-'; ?></td>
                <td><span class="badge bg-secondary"><?= esc($alldata[$i]['device_name'] ?? ''); ?></span></td>
                <td>
                    <a class="lotno-link font-weight-bold" data-bs-toggle="modal" data-bs-target="#exampleModal" data-number="<?= $alldata[$i]['number']; ?>" data-process="<?= esc($alldata[$i]['process_name'] ?? '') ?>" data-machno="<?= !empty($alldata[$i]['machno']) ? esc($alldata[$i]['machno']) : '-'; ?>">
                        <?= esc($alldata[$i]['process_name'] ?? ''); ?>
                    </a>
                    <?php if(!empty($alldata[$i]['machno']) && $alldata[$i]['machno'] !== '-'): ?>
                        <div class="small text-muted">Mesin: <strong><?= esc($alldata[$i]['machno']) ?></strong></div>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($isReject): ?>
                        <?php if($isWeeklyReject): ?>
                            <span class="badge bg-danger py-1 px-2"><i class="fas fa-undo me-1"></i>Weekly Reject</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                        <?php endif; ?>

                        <?php if(!empty($alldata[$i]['reject_reason'])): ?>
                            <div class="small text-danger text-start mt-1 p-1 bg-white border border-danger rounded shadow-sm" style="font-size:0.75rem;">
                                <strong><?= $isWeeklyReject ? '<i class="fas fa-exclamation-triangle text-danger me-1"></i>Catatan Weekly Approver:' : 'Alasan:' ?></strong> 
                                <?= esc($alldata[$i]['reject_reason']) ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif($isRequestReject): ?>
                        <span class="badge bg-success py-1 px-2"><i class="fas fa-exclamation-circle me-1"></i>Request Reject</span>
                    <?php elseif($isPending): ?>
                        <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>
                    <?php else: ?>
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i><?= esc($st); ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($isReject): ?>
                        <div class="small font-weight-bold text-danger"><?= esc($alldata[$i]['reject_by'] ?? '-'); ?></div>
                        <?php if(!empty($alldata[$i]['reject_date'])): ?>
                            <div class="text-muted" style="font-size: 0.72rem;"><?= date('d/m/Y H:i', strtotime($alldata[$i]['reject_date'])); ?></div>
                        <?php endif; ?>
                    <?php elseif(!$isPending): ?>
                        <div class="small font-weight-bold text-success"><?= esc($alldata[$i][$alldata[$i]['role']] ?? '-'); ?></div>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php
                    $session = \Config\Services::session();
                    $userLevel = (int)$session->get('level');
                    $canApproveLevel = ($userLevel == 14 || $userLevel == 10 || ($userLevel <= 7 && $userLevel > 0));

                    if ($canApproveLevel && ($isPending || $isReject)) {
                    ?>
                        <div class="d-flex justify-content-center gap-1">
                            <form action="<?= base_url('approve') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="number" value="<?= $alldata[$i]['number'] ?>">
                                <input type="hidden" name="name" value="<?= esc($session->get('name')) ?>">
                                <input type="hidden" name="level" value="<?= esc($session->get('level')) ?>">
                                <input type="hidden" name="action" value="Approve">
                                <button type="submit" class="btn btn-sm <?= $isReject ? 'btn-outline-success font-weight-bold' : 'btn-success' ?>" onclick="return confirm('Apakah Anda yakin menyetujui checksheet ini<?= $isReject ? ' setelah perbaikan' : '' ?>?');" title="<?= $isReject ? 'Setujui ulang checksheet ini setelah perbaikan' : 'Approve checksheet' ?>">
                                    <i class="fas <?= $isReject ? 'fa-check-double' : 'fa-check' ?> me-1"></i><?= $isReject ? 'Re-Approve' : 'Approve' ?>
                                </button>
                            </form>
                            <?php if(!$isReject): ?>
                            <button type="button" class="btn btn-sm btn-danger btn-open-reject" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalReject" 
                                    data-number="<?= $alldata[$i]['number'] ?>" 
                                    data-process="<?= esc($alldata[$i]['process_name'] ?? '') ?>" 
                                    data-machno="<?= esc($alldata[$i]['machno'] ?? '-') ?>">
                                <i class="fas fa-times me-1"></i><?= $isRequestReject ? 'Setujui Reject' : 'Reject' ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    <?php 
                    } elseif ($isReject) {
                        echo '<span class="badge bg-secondary small"><i class="fas fa-hourglass-half me-1"></i>Menunggu Revisi Operator</span>';
                    } else {
                        echo '<span class="text-muted small"><i class="fas fa-check-double text-success me-1"></i>Selesai</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>