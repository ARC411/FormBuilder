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

        $doc = new \DOMDocument(); 
        libxml_use_internal_errors(true); 
        $doc->loadHTML('<?xml encoding="UTF-8">' . $htmlString); 
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($doc); 
        
        // Coba cari table dengan id table1 dulu
        $table = $xpath->query("//table[@id='table1']")->item(0);
        
        // Jika tidak ada, ambil saja table HTML pertama (indeks 0) yang ditemukan di view
        if ($table === null) {
            $table = $xpath->query("//table")->item(0);
        }
        
        if ($table === null) {
            die("Error: Tidak ada elemen <table> sama sekali yang ditemukan di view untuk dijadikan PDF!");
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
                if ($td instanceof \DOMElement && in_array(strtolower($td->tagName), ['td','th'])) {
                    $colspan = $td->getAttribute('colspan');
                    $cols += $colspan ? (int)$colspan : 1;
                }
            }
            if ($cols > $maxExistingCols) {
                $maxExistingCols = $cols;
            }
        }
        $identityColCount = max(1, $maxExistingCols - $dataColsCount);

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
            $colChunks = [[$identityColCount + 1, $grid['totalCols']]];
        }

        $kopSuratHtml = $this->buildKopSuratHtml(
            $namaProduk ?? '', 
            $judulProses ?? '', 
            $noDok ?? '', 
            $machNo ?? '', 
            $tglBerlaku ?? '', 
            $namaApprover ?? '', 
            $revisi ?? '', 
            $typeProcess ?? ''
        );

        $pagesHtml = '';
        $chunkCount = 0;
        
        foreach ($colChunks as $cRange) {
            $pageBreakStyle = ($chunkCount > 0) ? ' style="page-break-before: always;"' : '';
            $tableHtml = $this->build2DChunkTableHtml($grid, $identityColCount, $cRange, $headerRowCount, $typeProcess ?? '');
            $pagesHtml .= "<div{$pageBreakStyle}>\n{$tableHtml}\n</div>";
            $chunkCount++;
        }

        $isStartup = (isset($typeProcess) && $typeProcess === 'startup');
        $cssScale = $isStartup ? '
            @page { size: A4 landscape; margin-top: 130px; margin-bottom: 10px; margin-left: 15px; margin-right: 15px; }
            body { font-family: Arial, Helvetica, sans-serif; font-size: 6.5px; }
            header { position: fixed; top: -115px; left: 0px; right: 0px; height: 105px; }
            table.table-cs td, table.table-cs th { border: 1px solid black !important; padding: 1px !important; text-align: center; vertical-align: middle; word-wrap: break-word; overflow: hidden;}
        ' : '
            @page { size: A4 landscape; margin-top: 140px; margin-bottom: 30px; margin-left: 30px; margin-right: 30px; }
            body { font-family: Arial, Helvetica, sans-serif; font-size: 7.5px; }
            header { position: fixed; top: -125px; left: 0px; right: 0px; height: 115px; }
            table.table-cs td, table.table-cs th { border: 1px solid black !important; padding: 4px !important; text-align: center; vertical-align: middle; word-wrap: break-word; overflow: hidden;}
        ';

        $customCSS = '
        <style>' . $cssScale . '
            table.table-cs { width: 100%; border-collapse: collapse; border: 1px solid black; table-layout: fixed; }
            th { background-color: #f8f9fa; font-weight: bold; }
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
            if (stripos($tr->textContent, 'Note') !== false) {
                $noteTr = $tr; break;
            }
        }

        if ($noteTr) {
            $getVal = function($item, $keys) {
                if (is_array($item)) {
                    foreach($keys as $k) {
                        if (isset($item[$k]) && $item[$k] !== '') return $item[$k];
                    }
                } elseif (is_object($item)) {
                    foreach($keys as $k) {
                        if (isset($item->$k) && $item->$k !== '') return $item->$k;
                    }
                }
                return '-';
            };

            $dataColsCount = count($alldata);
            $totalPages = max(1, ceil($dataColsCount / 15));
            $targetCols = $totalPages * 15;

            $opTr = $doc->createElement('tr');
            $tdOpLbl = $doc->createElement('td', 'Operator');
            if ($identityColCount > 1) {
                $tdOpLbl->setAttribute('colspan', (string)$identityColCount);
            }
            $opTr->appendChild($tdOpLbl);

            for ($i = 0; $i < $targetCols; $i++) {
                if (isset($alldata[$i])) {
                    $opName = $getVal($alldata[$i], ['operator', 'op_start', 'nama_operator', 'pic', 'created_by']);
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
                    $status = strtolower(trim($getVal($alldata[$i], ['status_approval', 'status', 'is_approved', 'approval'])));
                    $isApp = in_array($status, ['approved', 'ok', 'yes', 'v', '1', 'true', 'done']);
                    $ceklis = $isApp ? 'V' : '-';
                    $tdAppData = $doc->createElement('td', $ceklis);
                } else {
                    $tdAppData = $doc->createElement('td', '-');
                }
                $appTr->appendChild($tdAppData);
            }
            $noteTr->parentNode->insertBefore($appTr, $noteTr);
        }
    }

    private function buildGrid(\DOMElement $table): array {
        $occupied = []; $origins = []; $currentRow = 1; $maxCol = 0;
        foreach ($table->getElementsByTagName('tr') as $tr) {
            $col = 1;
            $inThead = strtolower($tr->parentNode->tagName) === 'thead';
            foreach ($tr->childNodes as $cell) {
                if (!($cell instanceof \DOMElement)) continue;
                $tag = strtolower($cell->tagName);
                if ($tag !== 'td' && $tag !== 'th') continue;
                while (!empty($occupied[$currentRow][$col])) $col++;
                $colspan = max(1, (int) ($cell->getAttribute('colspan') ?: 1));
                $rowspan = max(1, (int) ($cell->getAttribute('rowspan') ?: 1));
                
                $isHeader = ($tag === 'th' || $inThead);
                
                $textContent = trim(preg_replace('/\s+/', ' ', $cell->textContent));
                
                $origins[$currentRow][$col] = ['value' => $textContent, 'isHeader' => $isHeader, 'colspan' => $colspan, 'rowspan' => $rowspan];
                for ($r = $currentRow; $r < $currentRow + $rowspan; $r++) {
                    for ($c = $col; $c < $col + $colspan; $c++) { $occupied[$r][$c] = true; }
                }
                $maxCol = max($maxCol, $col + $colspan - 1); $col += $colspan;
            }
            $currentRow++;
        }
        return ['origins' => $origins, 'occupied' => $occupied, 'totalRows' => $currentRow - 1, 'totalCols' => $maxCol];
    }

    private function countHeaderRows(array $grid): int {
        $r = 1;
        while ($r <= $grid['totalRows']) {
            $rowOrigins = $grid['origins'][$r] ?? [];
            if (count($rowOrigins) === 1 && reset($rowOrigins)['colspan'] === $grid['totalCols']) { $r++; continue; }
            break;
        }
        $firstCell = $grid['origins'][$r][1] ?? null;
        if ($firstCell) return ($r - 1) + $firstCell['rowspan'];
        return 1;
    }

    private function build2DChunkTableHtml(array $grid, int $identityColCount, array $colRange, int $headerRowCount, string $typeProcess): string {
        [$startCol, $endCol] = $colRange;
        
        $validCols = [];
        for ($c = 1; $c <= $identityColCount; $c++) $validCols[$c] = true;
        for ($c = $startCol; $c <= $endCol; $c++) $validCols[$c] = true;
        
        $colgroup = '<colgroup>';
        for ($c = 1; $c <= $identityColCount; $c++) {
            if ($typeProcess === 'startup') {
                if ($c === 1) $w = '4%'; elseif ($c === 2) $w = '16%'; elseif ($c === 3) $w = '8%'; else $w = '8%'; 
            } else {
                if ($c === 1) $w = '4%'; else $w = '12%';
            }
            $colgroup .= '<col style="width: ' . $w . ';">';
        }
        for ($c = $startCol; $c <= $endCol; $c++) {
            // FIX: Operator ternary disempurnakan dengan dua hasil (true : false)
            $w = ($typeProcess === 'startup') ? '4%' : '12%'; 
            $colgroup .= '<col style="width: ' . $w . ';">';
        }
        $colgroup .= '</colgroup>';

        $html = '<table class="table-cs">' . $colgroup . '<thead>';

        for ($r = 1; $r <= $grid['totalRows']; $r++) {
            if ($r == $headerRowCount + 1) $html .= '</thead><tbody>';
            $html .= '<tr>'; 
            
            foreach ($validCols as $c => $dummy) {
                if (isset($grid['origins'][$r][$c])) {
                    $origin = $grid['origins'][$r][$c];
                    $actualColspan = 0;
                    $maxC = min($c + $origin['colspan'] - 1, $grid['totalCols']);
                    for ($i = $c; $i <= $maxC; $i++) { if (isset($validCols[$i])) $actualColspan++; }
                    
                    if ($actualColspan > 0) {
                        $tag = $origin['isHeader'] ? 'th' : 'td';
                        if(strtolower(trim($origin['value'])) == "operator" || strpos(strtolower(trim($origin['value'])), "approved by") !== false) {
                            $colspanAttr = ' colspan="' . $identityColCount . '"';
                        } else {
                            $colspanAttr = $actualColspan > 1 ? ' colspan="' . $actualColspan . '"' : '';
                        }
                        $rowspanAttr = $origin['rowspan'] > 1 ? ' rowspan="' . $origin['rowspan'] . '"' : '';
                        $value = htmlspecialchars($origin['value'], ENT_QUOTES, 'UTF-8');
                        $html .= "<{$tag}{$rowspanAttr}{$colspanAttr}>{$value}</{$tag}>";
                    }
                } else if (empty($grid['occupied'][$r][$c])) {
                    $isHeader = false;
                    if (isset($grid['origins'][$r])) {
                        foreach ($grid['origins'][$r] as $org) { if ($org['isHeader']) { $isHeader = true; break; } }
                    }
                    if ($isHeader) {
                        $html .= '<th></th>';
                    } else {
                        $html .= '<td>-</td>'; 
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    private function buildKopSuratHtml($namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess): string {
        $kotakTtdHtml = "";
        
        if ($typeProcess === "startup") {
            $kotakTtdHtml = '
                <tr><td style="width: 50%; text-align: center; padding: 2px;">QC</td><td style="text-align: center; padding: 2px;">Production</td></tr>
                <tr><td style="height: 25px;"></td><td style="height: 25px;"></td></tr>';
        } else {
            $ttd = $namaApprover !== "" ? '<span style="font-size: 8px;">Approved by:<br><b>' . htmlspecialchars($namaApprover) . '</b></span>' : '';
            $kotakTtdHtml = '
                <tr><td colspan="2" style="text-align: center; vertical-align: middle; padding: 2px;">Checked</td></tr>
                <tr><td colspan="2" style="text-align: center; height: 25px; vertical-align: bottom; padding: 2px;">' . $ttd . '</td></tr>';
        }

        return '
        <table style="width: 100%; border: none; font-size: 9px; table-layout: fixed; margin-bottom: 5px;">
            <tr>
                <td style="width: 35%; vertical-align: top; border: none; padding: 0;">
                    PT. FOXCONN TECHNOLOGIES INDONESIA<br>Production Engineering Department<br>Process Engineering Section<br>
                    <b>' . htmlspecialchars($namaProduk) . '</b>
                </td>
                <td style="width: 40%; text-align: center; vertical-align: top; border: none; padding: 0;">
                    <b style="font-size: 12px; text-decoration: underline;">' . htmlspecialchars($judulProses) . '</b><br>
                    <span style="font-size: 10px;">(' . htmlspecialchars($namaProduk) . ')</span>
                </td>
                <td style="width: 25%; text-align: right; vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 8px;" border="1">
                        <tr><td style="width: 40%; text-align: left; padding: 2px;">No. Dok / No.</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($noDok) . '</td></tr>
                        <tr><td style="text-align: left; padding: 2px;">Berlaku</td><td style="text-align: left; padding: 2px;">: ' . htmlspecialchars($tglBerlaku) . '</td></tr>
                        ' . $kotakTtdHtml . '
                    </table>
                </td>
            </tr>
        </table>
        <div style="font-size: 9px; text-align: left;">
            MACHINE No &nbsp;&nbsp;&nbsp;: ' . htmlspecialchars($machNo) . '
        </div>';
    }
}
