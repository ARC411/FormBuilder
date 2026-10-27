<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    public function generate(string $htmlString, array $context)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        extract($context); 

        $isStartup = (isset($typeProcess) && strtolower($typeProcess) === 'startup');
        $PER_PAGE  = $isStartup ? 14 : 20;
        $allD      = $alldata ?? [];
        $namaApp   = $namaApprover ?? '';

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $htmlString);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $table = $xpath->query("//table[@id='table1']")->item(0);
        if ($table === null) {
            $table = $xpath->query("//table")->item(0);
        }
        if ($table === null) {
            die("Error: Tidak ada elemen <table> yang ditemukan untuk dijadikan PDF!");
        }


        $dataColsCount = count($alldata ?? []);
        $trs = iterator_to_array($table->getElementsByTagName('tr'));

        // Deteksi lebar kolom sebenarnya dari baris yang memiliki multi-sel
        $contentCols = 0;
        foreach ($trs as $tr) {
            $cells = [];
            foreach ($tr->childNodes as $child) {
                if ($child instanceof \DOMElement && in_array(strtolower($child->tagName), ['td', 'th'])) {
                    $cells[] = $child;
                }
            }
            if (count($cells) > 1) {
                $rowCols = 0;
                foreach ($cells as $cell) {
                    $cs = max(1, (int)($cell->getAttribute('colspan') ?: 1));
                    $rowCols += $cs;
                }
                if ($rowCols > $contentCols) {
                    $contentCols = $rowCols;
                }
            }
        }

        // Jika baris judul (hanya 1 sel) memiliki colspan melebihi lebar kolom konten, sesuaikan ke lebar konten
        if ($contentCols > 0) {
            foreach ($trs as $tr) {
                $cells = [];
                foreach ($tr->childNodes as $child) {
                    if ($child instanceof \DOMElement && in_array(strtolower($child->tagName), ['td', 'th'])) {
                        $cells[] = $child;
                    }
                }
                if (count($cells) === 1) {
                    $cs = (int)($cells[0]->getAttribute('colspan') ?: 1);
                    if ($cs > $contentCols) {
                        $cells[0]->setAttribute('colspan', (string)$contentCols);
                    }
                }
            }
        }

        $maxExistingCols = 0;
        foreach ($trs as $tr) {
            $cols = 0;
            foreach ($tr->childNodes as $td) {
                if ($td instanceof \DOMElement && in_array(strtolower($td->tagName), ['td', 'th'])) {
                    $cols += (int)($td->getAttribute('colspan') ?: 1);
                }
            }
            if ($cols > $maxExistingCols) {
                $maxExistingCols = $cols;
            }
        }

        if (isset($typeProcess) && $typeProcess === 'startup') {
            $namaApp = isset($namaApprover) ? $namaApprover : '';
            $allD = isset($alldata) ? $alldata : [];
            $this->injectApprover($doc, $table, $namaApp, $allD, $identityColCount);
        }

        $grid = $this->buildGrid($table);
        $headerRowCount = $this->countHeaderRows($grid);

        $colChunks = [];
        if (isset($typeProcess) && $typeProcess === 'startup') {
            $totalDataCols = $grid['totalCols'] - $identityColCount; 
            $start = $identityColCount + 1;
            $endTotal = $grid['totalCols'];
            
            while ($start <= $endTotal) {
                $end = min($endTotal, $start + 15 - 1);
                $colChunks[] = [$start, $end];
                $start = $end + 1;
            }
        } else {
            $identityColCount = 3;
        }

        // 3. Bangun Grid 2D
        $grid = $this->buildGrid($table);
        if ($isStartup) {
            // Gabungkan sel Standard yang berulang di dalam item yang sama (contoh: Item 9 Dispenser Indication)
            $this->mergeDuplicateStandardCells($grid, $identityColCount);
        }
        $headerRowCount = $this->countHeaderRows($grid);

        // 4. Bangun Kop Surat HTML
        $kopSuratHtml = $this->buildKopSuratHtml(
            $namaProduk ?? '', 
            $judulProses ?? '', 
            $noDok ?? '', 
            $machNo ?? '', 
            $tglBerlaku ?? '', 
            $namaApprover ?? '', 
            $revisi ?? '', 
            $typeProcess ?? '',
            $weeklyInfo ?? null
        );

        // 5. Render Halaman per Chunk
        $pagesHtml    = '';
        $validEndRow  = $grid['totalRows'];

        if ($isStartup) {
            // Cari baris 'Note' sebagai batas bawah halaman Startup
            for ($r = $grid['totalRows']; $r >= 1; $r--) {
                $firstCellVal = $grid['origins'][$r][1]['value'] ?? '';
                if (stripos(trim($firstCellVal), 'note') !== false) {
                    $validEndRow = $r;
                    break;
                }
            }

            $totalPages = max(1, (int)ceil(max(1, $dataColsCount) / $PER_PAGE));
            for ($p = 0; $p < $totalPages; $p++) {
                $startCol = $identityColCount + 1 + ($p * $PER_PAGE);
                $endCol   = $startCol + $PER_PAGE - 1;
                $pageBreakStyle = ($p > 0) ? ' style="page-break-before: always;"' : '';
                $tableHtml = $this->buildStartupChunkTableHtml($grid, $identityColCount, [$startCol, $endCol], $headerRowCount, $validEndRow, $PER_PAGE);
                $pagesHtml .= "<div class=\"startup-page\"{$pageBreakStyle}>\n{$tableHtml}\n</div>";
            }
        } else {
            // Production & Foregoing:
            // Hitung jumlah kolom identitas di sebelah kiri (termasuk kolom Sample / Label jika Foregoing)
            $horizIdentityCols = (isset($typeProcess) && strtolower($typeProcess) === 'foregoing') ? 8 : 9;
            if ($horizIdentityCols >= $grid['totalCols']) {
                $horizIdentityCols = min(3, max(1, $grid['totalCols'] - 1));
            }

            $totalDataCols = max(1, $grid['totalCols'] - $horizIdentityCols);
            // Maksimal 16 kolom parameter per halaman A4 Landscape agar kolom tidak pernah tergencet sempit
            $MAX_DATA_COLS_PER_PAGE = 16;
            $colChunks = [];
            if ($totalDataCols > $MAX_DATA_COLS_PER_PAGE) {
                $cStart = $horizIdentityCols + 1;
                while ($cStart <= $grid['totalCols']) {
                    $cEnd = min($grid['totalCols'], $cStart + $MAX_DATA_COLS_PER_PAGE - 1);
                    $colChunks[] = [$cStart, $cEnd];
                    $cStart = $cEnd + 1;
                }
            } else {
                $colChunks[] = [$horizIdentityCols + 1, $grid['totalCols']];
            }

            $dataRowStart  = $headerRowCount + 1;
            $dataRowCount  = max(0, $grid['totalRows'] - $headerRowCount);
            $totalRowPages = max(1, (int)ceil(max(1, $dataRowCount) / $PER_PAGE));

            $pageIdx = 0;
            for ($p = 0; $p < $totalRowPages; $p++) {
                $rStart = $dataRowStart + ($p * $PER_PAGE);
                $rEnd   = $rStart + $PER_PAGE - 1;

                foreach ($colChunks as $cRange) {
                    $pageBreakStyle = ($pageIdx > 0) ? ' style="page-break-before: always;"' : '';
                    $tableHtml = $this->buildHorizontalChunkTableHtml($grid, $horizIdentityCols, $cRange, $headerRowCount, $rStart, $rEnd);
                    $pagesHtml .= "<div{$pageBreakStyle}>\n{$tableHtml}\n</div>";
                    $pageIdx++;
                }
            }
        }

        // Skala dinamis khusus Startup agar 1 chunk (beserta Operator, Checked by, Note) SELALU muat di 1 halaman utuh
        if ($isStartup) {
            $fontSize   = ($validEndRow > 30) ? '5.4px' : (($validEndRow > 24) ? '6.0px' : '6.5px');
            $cellPad    = ($validEndRow > 30) ? '0.8px 1.5px' : '1.2px 2px';
            $lineHeight = ($validEndRow > 30) ? '1.0' : '1.08';

            $cssScale = "
                @page { size: A4 landscape; margin-top: 96px; margin-bottom: 10px; margin-left: 15px; margin-right: 15px; }
                body { font-family: Arial, Helvetica, sans-serif; font-size: {$fontSize}; line-height: {$lineHeight}; }
                header { position: fixed; top: -86px; left: 0px; right: 0px; height: 80px; }
                .startup-page { page-break-inside: avoid; }
                table.table-cs td, table.table-cs th { border: 1px solid black !important; padding: {$cellPad} !important; text-align: center; vertical-align: middle; word-wrap: break-word; overflow: hidden; }
            ";
        } else {
            $cssScale = '
                @page { size: A4 landscape; margin-top: 105px; margin-bottom: 15px; margin-left: 15px; margin-right: 15px; }
                body { font-family: Arial, Helvetica, sans-serif; font-size: 6.5px; line-height: 1.1; }
                header { position: fixed; top: -95px; left: 0px; right: 0px; height: 88px; }
                table.table-cs td, table.table-cs th { border: 1px solid black !important; padding: 2px 1px !important; text-align: center; vertical-align: middle; word-wrap: break-word; overflow: hidden; }
            ';
        }

        $customCSS = '
        <style>' . $cssScale . '
            table.table-cs { width: 100%; border-collapse: collapse; border: 1px solid black; table-layout: fixed; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr { page-break-inside: avoid; }
        </style>';

        $finalHtml = "<html><head>{$customCSS}</head><body><header>{$kopSuratHtml}</header><main>{$pagesHtml}</main></body></html>";

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($finalHtml);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $procName = isset($process) ? strtoupper($process) : 'REPORT';
        $fileName = 'Report_' . $procName . '_' . date('Ymd_Hi') . '.pdf';
        $dompdf->stream($fileName, ["Attachment" => true]);
        exit();
    }

    private function injectApprover(\DOMDocument $doc, \DOMElement $table, string $namaApprover, array $alldata, int $identityColCount = 1): void {
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
        foreach ($trs as $tr) {
            $firstCell = $tr->getElementsByTagName('td')->item(0) ?? $tr->getElementsByTagName('th')->item(0);
            if ($firstCell) {
                $txt = strtolower(trim($firstCell->textContent));
                if (strpos($txt, 'status approval') !== false || strpos($txt, 'operator') !== false || strpos($txt, 'checked') !== false) {
                    $tr->parentNode->removeChild($tr);
                }
            }
        }

        $trs = $table->getElementsByTagName('tr');
        $noteTr = null;
        foreach ($trs as $tr) {
            $firstCell = $tr->getElementsByTagName('td')->item(0) ?? $tr->getElementsByTagName('th')->item(0);
            if ($firstCell && stripos(trim($firstCell->textContent), 'note') !== false) {
                $noteTr = $tr;
                break;
            }
        }

        if ($noteTr) {
            $getVal = function ($item, $keys) {
                if (is_array($item)) {
                    foreach ($keys as $k) {
                        if (isset($item[$k]) && $item[$k] !== '' && $item[$k] !== '-') return $item[$k];
                    }
                } elseif (is_object($item)) {
                    foreach ($keys as $k) {
                        if (isset($item->$k) && $item->$k !== '' && $item->$k !== '-') return $item->$k;
                    }
                }
                return '-';
            };

            $dataColsCount = count($alldata);
            $totalPages    = max(1, (int)ceil(max(1, $dataColsCount) / $PER_PAGE));
            $targetCols    = $totalPages * $PER_PAGE;

            $opTr    = $doc->createElement('tr');
            $tdOpLbl = $doc->createElement('td', 'Operator');
            if ($identityColCount > 1) {
                $tdOpLbl->setAttribute('colspan', (string)$identityColCount);
            }
            $opTr->appendChild($tdOpLbl);

            for ($i = 0; $i < $targetCols; $i++) {
                if (isset($alldata[$i])) {
                    $opName = function_exists('getOperatorName') ? getOperatorName($alldata[$i]) : $getVal($alldata[$i], ['name', 'operator', 'op_start', 'nama_operator', 'pic', 'created_by', 'empid']);
                    $tdOpData = $doc->createElement('td', htmlspecialchars($opName));
                } else {
                    $tdOpData = $doc->createElement('td', '-');
                }
                $opTr->appendChild($tdOpData);
            }
            $noteTr->parentNode->insertBefore($opTr, $noteTr);

            $appTr = $doc->createElement('tr');
            $approverText = ($namaApprover !== '' && $namaApprover !== '-') ? $namaApprover : '-';
            $tdAppLbl = $doc->createElement('td', htmlspecialchars("Check by: " . $approverText));
            if ($identityColCount > 1) {
                $tdAppLbl->setAttribute('colspan', (string)$identityColCount);
            }
            $appTr->appendChild($tdAppLbl);

            for ($i = 0; $i < $targetCols; $i++) {
                if (isset($alldata[$i])) {
                    $status = strtolower(trim((string)$getVal($alldata[$i], ['status_approval', 'status', 'is_approved', 'approval'])));
                    if (strpos($status, 'approve') !== false || strpos($status, 'bypass') !== false || in_array($status, ['ok', 'yes', 'v', '1', 'true', 'done'])) {
                        $spv = $getVal($alldata[$i], ['supervisor']);
                        $ldr = $getVal($alldata[$i], ['leader']);
                        $frm = $getVal($alldata[$i], ['foreman']);
                        $appText = 'Approved';
                        if ($spv !== '-') $appText = trim($spv) . " (Supervisor)";
                        elseif ($ldr !== '-') $appText = trim($ldr) . " (Leader)";
                        elseif ($frm !== '-') $appText = trim($frm) . " (Foreman)";

                        $tdAppData = $doc->createElement('td', htmlspecialchars($appText));
                    } else {
                        $tdAppData = $doc->createElement('td', '-');
                    }
                } else {
                    $tdAppData = $doc->createElement('td', '-');
                }
                $appTr->appendChild($appTr->ownerDocument->importNode($tdAppData, true));
            }
            $noteTr->parentNode->insertBefore($appTr, $noteTr);
        }
    }

    private function buildGrid(\DOMElement $table): array
    {
        $occupied = [];
        $origins = [];
        $currentRow = 1;
        $maxCol = 0;
        foreach ($table->getElementsByTagName('tr') as $tr) {
            $col = 1;
            $inThead = strtolower($tr->parentNode->tagName) === 'thead';
            foreach ($tr->childNodes as $cell) {
                if (!($cell instanceof \DOMElement)) continue;
                $tag = strtolower($cell->tagName);
                if ($tag !== 'td' && $tag !== 'th') continue;
                while (!empty($occupied[$currentRow][$col])) $col++;
                $colspan = max(1, (int)($cell->getAttribute('colspan') ?: 1));
                $rowspan = max(1, (int)($cell->getAttribute('rowspan') ?: 1));

                $isHeader = ($tag === 'th' || $inThead);
                $textContent = trim(preg_replace('/\s+/', ' ', $cell->textContent));

                $origins[$currentRow][$col] = [
                    'value'    => $textContent,
                    'isHeader' => $isHeader,
                    'colspan'  => $colspan,
                    'rowspan'  => $rowspan
                ];
                for ($r = $currentRow; $r < $currentRow + $rowspan; $r++) {
                    for ($c = $col; $c < $col + $colspan; $c++) {
                        $occupied[$r][$c] = true;
                    }
                }
                $maxCol = max($maxCol, $col + $colspan - 1);
                $col += $colspan;
            }
            $currentRow++;
        }
        return [
            'origins'   => $origins,
            'occupied'  => $occupied,
            'totalRows' => $currentRow - 1,
            'totalCols' => $maxCol
        ];
    }

    private function countHeaderRows(array $grid): int
    {
        $r = 1;
        while ($r <= $grid['totalRows']) {
            $rowOrigins = $grid['origins'][$r] ?? [];
            if (count($rowOrigins) === 1 && reset($rowOrigins)['colspan'] === $grid['totalCols']) {
                $r++;
                continue;
            }
            break;
        }
        $firstCell = $grid['origins'][$r][1] ?? null;
        if ($firstCell) return ($r - 1) + $firstCell['rowspan'];
        return 1;
    }

    // Render Halaman Tabel Startup (Identitas Kiri + 14 Kolom Data per Halaman, Berhenti di Baris Note)
    private function buildStartupChunkTableHtml(array $grid, int $identityColCount, array $colRange, int $headerRowCount, int $validEndRow, int $PER_PAGE): string
    {
        [$startCol, $endCol] = $colRange;

        $colgroup = '<colgroup>';
        $leftTotal = 42;
        for ($c = 1; $c <= $identityColCount; $c++) {
            if ($c === 1) {
                $w = '3%';
            } elseif ($c === $identityColCount) {
                $w = '17%';
            } else {
                $midCols = max(1, $identityColCount - 2);
                $w = round(($leftTotal - 20) / $midCols, 2) . '%';
            }
            $colgroup .= '<col style="width: ' . $w . ';">';
        }
        $dataColW = round((100 - $leftTotal) / $PER_PAGE, 2) . '%';
        for ($c = $startCol; $c <= $endCol; $c++) {
            $colgroup .= '<col style="width: ' . $dataColW . ';">';
        }
        $colgroup .= '</colgroup>';

        $html = '<table class="table-cs">' . $colgroup . '<thead>';

        for ($r = 1; $r <= $validEndRow; $r++) {
            if ($r === $headerRowCount + 1) $html .= '</thead><tbody>';
            $html .= '<tr>';

            // 1. Kolom Identitas Kiri
            for ($c = 1; $c <= $identityColCount; $c++) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell = $grid['origins'][$r][$c];
                    $colspan = min($cell['colspan'], $identityColCount - $c + 1);
                    $rowspan = min($cell['rowspan'], $validEndRow - $r + 1);
                    $tag = $cell['isHeader'] ? 'th' : 'td';
                    $csAttr = $colspan > 1 ? ' colspan="' . $colspan . '"' : '';
                    $rsAttr = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';
                    $html .= "<{$tag}{$rsAttr}{$csAttr}>" . htmlspecialchars($cell['value'], ENT_QUOTES, 'UTF-8') . "</{$tag}>";
                }
            }

            // Cek Header Besar "Hasil Start Up Check"
            $massiveHeaderCell = null;
            if (isset($grid['origins'][$r][$identityColCount + 1])) {
                $checkCell = $grid['origins'][$r][$identityColCount + 1];
                if (($checkCell['isHeader'] ?? false) && ($checkCell['colspan'] ?? 1) > 2) {
                    $massiveHeaderCell = $checkCell;
                }
            }

            if ($massiveHeaderCell !== null) {
                $rsAttr = $massiveHeaderCell['rowspan'] > 1 ? ' rowspan="' . $massiveHeaderCell['rowspan'] . '"' : '';
                $html .= '<th colspan="' . $PER_PAGE . '"' . $rsAttr . '>' . htmlspecialchars($massiveHeaderCell['value'], ENT_QUOTES, 'UTF-8') . '</th>';
            } else {
                // 2. Render 14 Kolom Data
                for ($c = $startCol; $c <= $endCol; $c++) {
                    if (isset($grid['origins'][$r][$c])) {
                        $cell = $grid['origins'][$r][$c];
                        $colspan = min($cell['colspan'], $endCol - $c + 1);
                        $rowspan = min($cell['rowspan'], $validEndRow - $r + 1);
                        $tag = $cell['isHeader'] ? 'th' : 'td';
                        $csAttr = $colspan > 1 ? ' colspan="' . $colspan . '"' : '';
                        $rsAttr = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';
                        $html .= "<{$tag}{$rsAttr}{$csAttr}>" . htmlspecialchars($cell['value'], ENT_QUOTES, 'UTF-8') . "</{$tag}>";
                    } elseif (empty($grid['occupied'][$r][$c])) {
                        $html .= ($r <= $headerRowCount) ? '<th>-</th>' : '<td>-</td>';
                    }
                }
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function buildKopSuratHtml($namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $weeklyInfo = null): string {
        $kotakTtdHtml = "";
        
        $docNoClean = $noDok;
        $revisiClean = !empty($revisi) ? $revisi : '00';
        if (preg_match('/^(.*?)-(\d+)$/', $noDok, $m)) {
            $docNoClean = $m[1];
            $revisiClean = $m[2];
        }

        if ($typeProcess === "startup") {
            $qcBox = "";
            if (!empty($weeklyInfo['status_qc']) && strtolower($weeklyInfo['status_qc']) === 'approve') {
                $approverQc = htmlspecialchars(!empty($weeklyInfo['approver_qc']) ? $weeklyInfo['approver_qc'] : 'Approved');
                $dateQc = !empty($weeklyInfo['date_qc']) ? date('d-m-Y', strtotime($weeklyInfo['date_qc'])) : '';
                $qcBox = '<div style="font-size: 6px; line-height: 1.1;">Approved by:<br><b style="font-size: 6.5px;">' . $approverQc . '</b><br>(QC)<br>' . $dateQc . '</div>';
            }

            $prodBox = "";
            if (!empty($weeklyInfo['status_production']) && strtolower($weeklyInfo['status_production']) === 'approve') {
                $approverProd = htmlspecialchars(!empty($weeklyInfo['approver_production']) ? $weeklyInfo['approver_production'] : 'Approved');
                $dateProd = !empty($weeklyInfo['date_production']) ? date('d-m-Y', strtotime($weeklyInfo['date_production'])) : '';
                $prodBox = '<div style="font-size: 6px; line-height: 1.1;">Approved by:<br><b style="font-size: 6.5px;">' . $approverProd . '</b><br>(Prod)<br>' . $dateProd . '</div>';
            }

            $infoRowsHtml = '
                <tr><td style="width: 40%; text-align: left; padding: 2px;">Doc No.</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($docNoClean) . '</td></tr>
                <tr><td style="text-align: left; padding: 2px;">Revisi</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($revisiClean) . '</td></tr>
                <tr><td style="text-align: left; padding: 2px;">Berlaku</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($tglBerlaku) . '</td></tr>';

            $kotakTtdHtml = '
                <tr><td style="width: 50%; text-align: center; padding: 2px; font-weight: bold;">QC</td><td style="width: 50%; text-align: center; padding: 2px; font-weight: bold;">Production</td></tr>
                <tr><td style="height: 28px; text-align: center; vertical-align: middle; padding: 1px;">' . $qcBox . '</td><td style="height: 28px; text-align: center; vertical-align: middle; padding: 1px;">' . $prodBox . '</td></tr>';
        } else {
                        $ttd = $namaApprover !== "" ? '<span style="font-size: 8px;">Approved by:<br><b>' . htmlspecialchars($namaApprover) . '</b></span>' : '';
            if (isset($typeProcess) && $typeProcess === 'production' && !empty($weeklyInfo)) {
                $statusApp = !empty($weeklyInfo['status']) ? strtolower($weeklyInfo['status']) : '';
                if ($statusApp === 'approve' || (!empty($weeklyInfo['status_qc']) && strtolower($weeklyInfo['status_qc']) === 'approve') || (!empty($weeklyInfo['status_production']) && strtolower($weeklyInfo['status_production']) === 'approve')) {
                    $appName = htmlspecialchars(!empty($weeklyInfo['approver']) ? $weeklyInfo['approver'] : (!empty($weeklyInfo['approver_qc']) ? $weeklyInfo['approver_qc'] : $weeklyInfo['approver_production']));
                    $appRole = htmlspecialchars(!empty($weeklyInfo['approver_role']) ? $weeklyInfo['approver_role'] : (!empty($weeklyInfo['approver_qc']) ? 'QC' : 'Production'));
                    $rawDate = !empty($weeklyInfo['approval_date']) ? $weeklyInfo['approval_date'] : (!empty($weeklyInfo['date_qc']) ? $weeklyInfo['date_qc'] : $weeklyInfo['date_production']);
                    $appDate = !empty($rawDate) ? date('d-m-Y', strtotime($rawDate)) : '';
                    $ttd = '<div style="font-size: 6.5px; line-height: 1.1;">Approved by:<br><b style="font-size: 7px;">' . $appName . '</b><br>(' . $appRole . ')<br>' . $appDate . '</div>';
                }
            }
            
            $infoRowsHtml = '
                <tr><td style="width: 40%; text-align: left; padding: 2px;">No. Dok / No.</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($noDok) . '</td></tr>
                <tr><td style="text-align: left; padding: 2px;">Berlaku</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($tglBerlaku) . '</td></tr>';

            $kotakTtdHtml = '
                <tr><td colspan="2" style="text-align: center; vertical-align: middle; padding: 1px; font-weight: bold;">Checked</td></tr>
                <tr><td colspan="2" style="text-align: center; height: 18px; vertical-align: middle; padding: 1px;">' . $ttd . '</td></tr>';
        }

        return '
        <table style="width: 100%; border-collapse: collapse; font-size: 8px; table-layout: fixed;">
            <tr>
                <td style="width: 28%; vertical-align: top; border: 1px solid black; padding: 3px; font-weight: bold;">
                    PT. FOXCONN TECHNOLOGIES INDONESIA<br>
                    Production Engineering Department<br>
                    Process Engineering Section<br>
                    ' . htmlspecialchars((string)$namaProduk) . '
                </td>
                <td rowspan="2" style="width: 47%; text-align: center; vertical-align: middle; border: 1px solid black; padding: 3px;">
                    <b style="font-size: 12px; text-decoration: underline;">' . htmlspecialchars((string)$judulProses) . '</b><br>
                    <b style="font-size: 10px;">(' . htmlspecialchars((string)$namaProduk) . ')</b>
                </td>
                <td style="width: 25%; text-align: right; vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 8px;" border="1">
                        ' . $infoRowsHtml . '
                        ' . $kotakTtdHtml . '
                    </table>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle; border: 1px solid black; padding: 3px; font-weight: bold;">
                    MACHINE No : ' . htmlspecialchars((string)$machNo) . '
                </td>
            </tr>
        </table>';
    }
}
