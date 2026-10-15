<?php require_once(dirname( __FILE__ ). '/'.'../../../fillHistory.php'); ?>
<div class="table-responsive mt-3" id="fl18-f-fdb2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
<?php
    // Kelompokkan data per transaksi (berdasarkan par000 == 1 atau number)
    // Aman untuk data lama (3 baris: X, Z, Theta) maupun data baru (4 baris: X, Y, Z, Theta)
    $groupedData = [];
    if (!empty($alldata)) {
        $currentGroup = [];
        $lastNum = null;

        foreach ($alldata as $row) {
            $par000 = isset($row['par000']) ? (int)$row['par000'] : null;
            $numKey = $row['number'] ?? null;

            if (!empty($currentGroup)) {
                if ($par000 === 1 || ($par000 === null && $numKey !== null && $numKey !== '-' && $numKey !== $lastNum)) {
                    $groupedData[] = $currentGroup;
                    $currentGroup = [];
                }
            }
            $currentGroup[] = $row;
            $lastNum = $numKey;
        }
        if (!empty($currentGroup)) {
            $groupedData[] = $currentGroup;
        }
    }

    // Jika belum ada data pada rentang tanggal yang dipilih, tampilkan 5 grup (20 baris)
    // agar parameter X, Y, Z, dan ϴ tetap muncul di Display History, Excel, dan PDF
    $isTemplateEmpty = empty($groupedData);
    if ($isTemplateEmpty) {
        $groupedData = array_fill(0, 5, []);
    }

    $getCell = function($rowData, $key) {
        return isset($rowData[$key]) && trim((string)$rowData[$key]) !== '' ? (string)$rowData[$key] : '-';
    };
?>
    <table class="table table-bordered table-hover" id="table1" data-cols-width="10,15,15,15,10,10,20,21,10,10,10,10,10,10,10,10,10,10,10,10">
        <thead align="center">
        <tr>
            <th colspan="32" scope="col"><?php echo nl2br("DIE BONDING 2")?></th>
        </tr>
        </thead>
        <thead align="center">
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">Mch. No.</th>         
                <th rowspan="3">Model</th>
                <th rowspan="3">Lot No.</th> 
                <th rowspan="3">Shift</th>
                <th rowspan="3">Group</th>
                <th rowspan="3">Date (dd/mm/yyyy)</th>
                <th rowspan="3">Measure Die Bond Pos</th>
                <th colspan="5" rowspan="2" scope="col">Foregoing Check Sample No (2pcs/lot)</th>
                <th rowspan="3">Judge</th>
                <th colspan="6">Change Bonding Offsett Parameter</th>
                <th colspan="5" rowspan="2" scope="col">Foregoing Check Sample No (2pcs/lot)</th>
                <th rowspan="3">Judge</th>
                <th colspan="6">Change Bonding Offsett Parameter</th>
            </tr>
        
            <tr>
                <th scope="col" colspan="2">X</th>
                <th scope="col" colspan="2">Z</th>
                <th scope="col" colspan="2">&#952;</th>
                <th scope="col" colspan="2">X</th>
                <th scope="col" colspan="2">Z</th>
                <th scope="col" colspan="2">&#952;</th>
            </tr>

            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>From</th>
                <th>To</th>
                <th>From</th>
                <th>To</th>
                <th>From</th>
                <th>To</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>From</th>
                <th>To</th>
                <th>From</th>
                <th>To</th>
                <th>From</th>
                <th>To</th>
            </tr>
        </thead>
        <tbody>
            <?php for($g = 0; $g < count($groupedData); $g++) { 
                $group = $groupedData[$g];
                // Jika data sudah menggunakan format 4 baris (X, Y, Z, Theta)
                if (count($group) >= 4) {
                    $rowX     = $group[0];
                    $rowY     = $group[1];
                    $rowZ     = $group[2];
                    $rowTheta = $group[3];
                } else {
                    // Kompatibilitas data lama (3 baris: X, Z, Theta) atau saat grup kosong
                    $rowX     = $group[0] ?? [];
                    $rowY     = [];
                    $rowZ     = $group[1] ?? [];
                    $rowTheta = $group[2] ?? [];
                }

                $paramRows = [
                    ['label' => 'X',      'data' => $rowX],
                    ['label' => 'Y',      'data' => $rowY],
                    ['label' => 'Z',      'data' => $rowZ],
                    ['label' => '&#952;', 'data' => $rowTheta],
                ];
            ?>
                <?php foreach ($paramRows as $idx => $pRow) { 
                    $d = $pRow['data'];
                ?>
                <tr>
                    <?php if ($idx === 0) { ?>
                        <td rowspan="4"><?= $isTemplateEmpty ? '-' : ($g + 1); ?></td>
                        <td rowspan="4"><?= $getCell($rowX, 'machno'); ?></td>
                        <td rowspan="4"><?= $getCell($rowX, 'model'); ?></td>
                        <td rowspan="4"><?= $getCell($rowX, 'lotno'); ?></td>
                        <td rowspan="4"><?= $getCell($rowX, 'shift'); ?></td>
                        <td rowspan="4"><?= $getCell($rowX, 'group'); ?></td>
                        <td rowspan="4"><?= (!empty($rowX['created_at']) && $rowX['created_at'] !== '-') ? date('d/m/Y', strtotime($rowX['created_at'])) : '-'; ?></td>
                    <?php } ?>
                    <td rowspan="1"><b><?= $pRow['label']; ?></b></td>
                    <td rowspan="1"><?= $getCell($d, 'par013'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par014'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par015'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par016'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par017'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par035'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par018'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par019'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par020'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par021'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par022'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par023'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par024'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par025'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par026'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par027'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par028'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par036'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par029'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par030'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par031'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par032'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par033'); ?></td>
                    <td rowspan="1"><?= $getCell($d, 'par034'); ?></td>
                </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>