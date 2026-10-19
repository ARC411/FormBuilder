<table class="table table-striped-columns table-responsive mt-3" id="table-approval">
    <thead align="center">
        <tr>
            <th>No.</th>
            <th>Date</th>         
            <th>Device</th>
            <th>Process</th>
            <th>Status</th>
            <th>Person</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($alldata); $i++) { ?>
            <tr>
                <td><?= $i+1; ?></td>
                <td><?= $alldata[$i]['created_at']; ?></td>
                <td><?= $alldata[$i]['device_name']; ?></td>
                <td>
                    <a class="lotno-link" data-bs-toggle="modal" data-bs-target="#exampleModal" data-number="<?= $alldata[$i]['number']; ?>" data-process="<?= $alldata[$i]['process_name']?>" data-machno="<?= !empty($alldata[$i]['machno']) ? $alldata[$i]['machno'] : '-'; ?>">
                        <?= $alldata[$i]['process_name']; ?>
                    </a>
                </td>
                <td><?= $alldata[$i]['status']; ?></td>
                <?php if($alldata[$i]['status'] != "Pending") { ?>
                    <td><?= $alldata[$i][$alldata[$i]['role']]; ?></td>
                <?php } else { ?>
                    <td></td>
                <?php } ?>
                <td>
                <form action="<?= base_url('approve') ?>" method="post">
                        <?= csrf_field() ?>
                        <?php
                        $session = \Config\Services::session();
                        $userLevel = (int)$session->get('level');
                        $canApproveLevel = ($userLevel == 14 || $userLevel == 10 || ($userLevel <= 7 && $userLevel > 0));

                        $inputButton = 
                        '<input type="hidden" name="number" value="'.$alldata[$i]['number'].'">'.
                        '<input type="hidden" name="name" value="'.$session->get('name').'">'.
                        '<input type="hidden" name="level" value="'.$session->get('level').'">'.
                        '<button type="submit" class="btn btn-primary" onclick="return confirm(`Apakah kamu yakin?`);">Approve</button>';

                        // Sekali dokumen di-approve oleh siapapun (User A), maka status bukan lagi Pending
                        // dan tombol Approve langsung HILANG untuk siapapun (termasuk User B)
                        if ($canApproveLevel && $alldata[$i]['status'] === 'Pending') {
                            echo $inputButton;
                        }
                        ?>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table> 