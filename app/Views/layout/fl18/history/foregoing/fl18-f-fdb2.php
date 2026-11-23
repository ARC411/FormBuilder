<div class="table-responsive mt-3" id="fl18-f-fdb2">
<?= csrf_field(); ?>
<input type="hidden" id="cnt-table" value="1">
    <table class="table table-bordered table-hover" id="table1">
        <thead align="center">
            <tr>
                <th colspan="32" scope="col"><?php echo nl2br("FOREGOING DIE BONDING 2"); ?></th>
            </tr>
            <tr>
                <th rowspan="3">No.</th>
                <th rowspan="3">Mch. No.</th>
                <th rowspan="3">Model</th>
                <th rowspan="3">Lot No.</th>
                <th rowspan="3">Operator</th>
                <th rowspan="3">Shift/Group</th>
                <th rowspan="3">Date (dd/mm/yyyy)</th>
                <th rowspan="3">Measure Die Bond Pos</th>
                <th colspan="5" rowspan="2" scope="col">Foregoing Check Sample 1</th>
                <th rowspan="2" scope="col">Judge1</th>
                <th colspan="6" scope="col">Change Bonding Offset Parameter #1</th>
                <th colspan="5" rowspan="2" scope="col">Foregoing Check Sample 2</th>
                <th rowspan="2" scope="col">Judge2</th>
                <th colspan="6" scope="col">Change Bonding Offset Parameter #2</th>
            </tr>
            <tr>
                <th colspan="2" scope="col">X</th>
                <th colspan="2" scope="col">Z</th>
                <th colspan="2" scope="col">&theta;</th>
                <th colspan="2" scope="col">X</th>
                <th colspan="2" scope="col">Z</th>
                <th colspan="2" scope="col">&theta;</th>
            </tr>
            <tr>
                <th scope="col">1</th>
                <th scope="col">2</th>
                <th scope="col">3</th>
                <th scope="col">4</th>
                <th scope="col">5</th>
                <th scope="col">OK/NG</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">1</th>
                <th scope="col">2</th>
                <th scope="col">3</th>
                <th scope="col">4</th>
                <th scope="col">5</th>
                <th scope="col">OK/NG</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
                <th scope="col">From</th>
                <th scope="col">To</th>
            </tr>
        </thead>

        <tbody>
            <?php
            if (!function_exists('renderOkNg')) {
                function renderOkNg($val) {
                    $valStr = strtoupper(trim((string)$val));
                    if ($valStr === 'OK') return '<strong>&#10004;</strong>';
                    if ($valStr === 'NG') return '<strong>&#10008;</strong>';
                    return $val !== '' ? $val : '-';
                }
            }

            $groupedLots = [];
            if (!empty($alldata)) {
                foreach ($alldata as $row) {
                    $numKey = $row['number'] ?? uniqid();
                    $parIdx = (int) ($row['par000'] ?? 1);
                    $groupedLots[$numKey][$parIdx] = $row;
                }
            }

            $posLabels = [
                1 => 'X',
                2 => 'Y',
                3 => 'Z',
                4 => 'θ',
            ];

            $no = 1;
            foreach ($groupedLots as $numKey => $lotRows):
                $firstRow = $lotRows[1] ?? reset($lotRows);

                $opEmpId = trim((string) ($firstRow['empid'] ?? '-'));

                $shiftVal = trim((string) ($firstRow['shift'] ?? ''));
                $groupVal = trim((string) ($firstRow['group'] ?? ''));
                $shiftGroupStr = ($shiftVal !== '' && $shiftVal !== '-')
                    ? ($shiftVal . ' / ' . ($groupVal !== '' ? $groupVal : '-'))
                    : ($groupVal !== '' ? $groupVal : '-');

                $createdRaw = trim((string) ($firstRow['created_at'] ?? ''));
                $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
                $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';

                for ($p = 1; $p <= 4; $p++):
                    $r = $lotRows[$p] ?? [];
            ?>
            <tr>
                <?php if ($p === 1): ?>
                    <td rowspan="4"><?= $no++; ?></td>
                    <td rowspan="4"><?= (string) ($firstRow['machno'] ?? '-'); ?></td>
                    <td rowspan="4"><?= (string) ($firstRow['model'] ?? '-'); ?></td>
                    <td rowspan="4"><?= (string) ($firstRow['lotno'] ?? '-'); ?></td>
                    <td rowspan="4"><?= esc($opEmpId ?: '-'); ?></td>
                    <td rowspan="4"><?= $shiftGroupStr; ?></td>
                    <td rowspan="4"><?= $dateStr; ?></td>
                <?php endif; ?>
                <td><?= $posLabels[$p]; ?></td>
                <td><?= renderOkNg($r['par013'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par014'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par015'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par016'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par017'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par035'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par018'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par019'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par020'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par021'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par022'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par023'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par024'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par025'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par026'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par027'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par028'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par036'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par029'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par030'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par031'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par032'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par033'] ?? '-'); ?></td>
                <td><?= renderOkNg($r['par034'] ?? '-'); ?></td>
            </tr>
            <?php
                endfor;
            endforeach;
            ?>
        </tbody>
    </table>
</div>