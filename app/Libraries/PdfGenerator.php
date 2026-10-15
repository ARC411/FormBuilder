<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    public function generate(string $htmlString, array $context)
    {
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

        // 1. Hapus baris judul utama di dalam <thead> pertama (karena sudah ada di Kop Surat)
        $theads = $table->getElementsByTagName('thead');
        if ($theads->length > 0) {
            $firstThead = $theads->item(0);
            $firstTr    = $firstThead->getElementsByTagName('tr')->item(0);
            if ($firstTr) {
                $firstCell = $firstTr->getElementsByTagName('th')->item(0);
                if ($firstCell && (int)$firstCell->getAttribute('colspan') > 2) {
                    $firstTr->parentNode->removeChild($firstTr);
                }
            }
        }

        // 2. Hitung jumlah kolom maksimum & kolom identitas
        $maxExistingCols = 0;
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
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

        $dataColsCount = count($allD);
        if ($isStartup) {
            $identityColCount = max(1, $maxExistingCols - max(1, $dataColsCount));
            $this->injectApproverStartup($doc, $table, $identityColCount, $allD, $PER_PAGE);
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
            $namaApp,
            $revisi ?? '00',
            $typeProcess ?? ''
        );

        // 5. Render Halaman per Chunk
        $pagesHtml = '';
        $validEndRow = $grid['totalRows'];

        if ($isStartup) {
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
            $dataRowStart = $headerRowCount + 1;
            $dataRowCount = max(0, $grid['totalRows'] - $headerRowCount);
            $totalPages   = max(1, (int)ceil(max(1, $dataRowCount) / $PER_PAGE));

            for ($p = 0; $p < $totalPages; $p++) {
                $rStart = $dataRowStart + ($p * $PER_PAGE);
                $rEnd   = $rStart + $PER_PAGE - 1;
                $pageBreakStyle = ($p > 0) ? ' style="page-break-before: always;"' : '';
                $tableHtml = $this->buildHorizontalChunkTableHtml($grid, $headerRowCount, $rStart, $rEnd);
                $pagesHtml .= "<div{$pageBreakStyle}>\n{$tableHtml}\n</div>";
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

        $procName = isset($process) ? strtoupper($process) : 'REPORT';
        $fileName = 'Report_' . $procName . '_' . date('Ymd_Hi') . '.pdf';
        $dompdf->stream($fileName, ["Attachment" => true]);
        exit();
    }

    // Gabungkan sel Standard yang kembar berurutan dalam 1 item nomor yang sama agar tidak berulang-ulang
    private function mergeDuplicateStandardCells(array &$grid, int $identityColCount): void
    {
        if ($identityColCount < 3) return;
        $stdCol = $identityColCount;

        for ($r = 1; $r <= $grid['totalRows']; $r++) {
            // Cek jika baris ini memulai nomor item baru di kolom 1 dengan rowspan > 1
            if (isset($grid['origins'][$r][1]) && ($grid['origins'][$r][1]['rowspan'] ?? 1) > 1) {
                $groupSpan = $grid['origins'][$r][1]['rowspan'];
                $groupEnd  = min($grid['totalRows'], $r + $groupSpan - 1);

                $anchorRow = null;
                $anchorVal = null;

                for ($subR = $r; $subR <= $groupEnd; $subR++) {
                    if (!isset($grid['origins'][$subR][$stdCol])) continue;
                    $cell = $grid['origins'][$subR][$stdCol];
                    if (($cell['rowspan'] ?? 1) !== 1 || ($cell['colspan'] ?? 1) !== 1) {
                        $anchorRow = null;
                        continue;
                    }

                    $val = trim($cell['value']);
                    if ($anchorRow !== null && $val !== '' && $val === $anchorVal && strlen($val) > 12) {
                        $grid['origins'][$anchorRow][$stdCol]['rowspan']++;
                        unset($grid['origins'][$subR][$stdCol]);
                    } else {
                        $anchorRow = $subR;
                        $anchorVal = $val;
                    }
                }
            }
        }
    }

    private function injectApproverStartup(\DOMDocument $doc, \DOMElement $table, int $identityCols, array $alldata, int $PER_PAGE): void
    {
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
        foreach ($trs as $tr) {
            $firstCell = $tr->getElementsByTagName('td')->item(0) ?? $tr->getElementsByTagName('th')->item(0);
            if ($firstCell) {
                $txt = strtolower(trim($firstCell->textContent));
                if (
                    strpos($txt, 'status approval') !== false ||
                    strpos($txt, 'operator') !== false ||
                    strpos($txt, 'approved by') !== false ||
                    $txt === 'time'
                ) {
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
            $tdOpLbl->setAttribute('colspan', (string)$identityCols);
            $opTr->appendChild($tdOpLbl);

            for ($i = 0; $i < $targetCols; $i++) {
                if (isset($alldata[$i])) {
                    $opName   = $getVal($alldata[$i], ['operator', 'op_start', 'nama_operator', 'pic', 'created_by', 'name', 'nama']);
                    $tdOpData = $doc->createElement('td', htmlspecialchars((string)$opName));
                } else {
                    $tdOpData = $doc->createElement('td', '-');
                }
                $opTr->appendChild($tdOpData);
            }
            $noteTr->parentNode->insertBefore($opTr, $noteTr);

            $appTr    = $doc->createElement('tr');
            $tdAppLbl = $doc->createElement('td', 'Checked by');
            $tdAppLbl->setAttribute('colspan', (string)$identityCols);
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
                $appTr->appendChild($tdAppData);
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

    private function buildStartupChunkTableHtml(array $grid, int $identityColCount, array $colRange, int $headerRowCount, int $validEndRow, int $PER_PAGE): string
    {
        [$startCol, $endCol] = $colRange;

        // Proporsi lebar: 42% untuk Kolom Identitas (No + Check Item + Standard), 58% untuk 14 Kolom Data
        $colgroup = '<colgroup>';
        $leftTotal = 42;
        for ($c = 1; $c <= $identityColCount; $c++) {
            if ($c === 1) {
                $w = '3%';
            } elseif ($c === $identityColCount) {
                $w = '17%'; // Lebar khusus kolom Standard agar kalimat tidak turun banyak baris
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

    // Render Halaman Tabel Production & Foregoing (Proporsi Kolom Identitas Rapi + Anti-Gencet untuk Kolom Banyak)
    private function buildHorizontalChunkTableHtml(array $grid, int $identityColCount, array $colRange, int $headerRowCount, int $rStart, int $rEnd): string
    {
        [$startCol, $endCol] = $colRange;
        $dataColCount = max(1, $endCol - $startCol + 1);

        $validCols = [];
        for ($c = 1; $c <= $identityColCount; $c++) $validCols[$c] = true;
        for ($c = $startCol; $c <= $endCol; $c++) $validCols[$c] = true;

        // Proporsi lebar: Kolom identitas kiri mendapat lebar yang pas sesuai isinya (No=2.5%, Date=5.5%, Mch/Model/Lot=4.5%, Operator=7%)
        $colgroup = '<colgroup>';
        $usedWidth = 0.0;
        for ($c = 1; $c <= $identityColCount; $c++) {
            if ($c === 1) {
                $w = 2.5; // No.
            } elseif ($c === 2 || $c === 7) {
                $w = 5.5; // Date / Operator
            } else {
                $w = 4.2; // Mch. No, Model, Lot No, Shift/Group, Sample/Label
            }
            $usedWidth += $w;
            $colgroup .= '<col style="width: ' . $w . '%;">';
        }

        $remainingWidth = max(35.0, 100.0 - $usedWidth);
        $dataColWidth   = round($remainingWidth / $dataColCount, 2);
        for ($c = $startCol; $c <= $endCol; $c++) {
            $colgroup .= '<col style="width: ' . $dataColWidth . '%;">';
        }
        $colgroup .= '</colgroup>';

        $html = '<table class="table-cs">' . $colgroup . '<thead>';

        // 1. Render Header Tabel (Hanya untuk kolom yang masuk di halaman ini)
        for ($r = 1; $r <= $headerRowCount; $r++) {
            $html .= '<tr>';
            foreach ($validCols as $c => $dummy) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell = $grid['origins'][$r][$c];
                    $actualColspan = 0;
                    $maxC = min($c + $cell['colspan'] - 1, $grid['totalCols']);
                    for ($i = $c; $i <= $maxC; $i++) {
                        if (isset($validCols[$i])) $actualColspan++;
                    }
                    if ($actualColspan > 0) {
                        $rowspan = min($cell['rowspan'], $headerRowCount - $r + 1);
                        $csAttr  = $actualColspan > 1 ? ' colspan="' . $actualColspan . '"' : '';
                        $rsAttr  = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';
                        $html   .= "<th{$rsAttr}{$csAttr}>" . htmlspecialchars($cell['value'], ENT_QUOTES, 'UTF-8') . "</th>";
                    }
                } elseif (!empty($grid['occupied'][$r][$c])) {
                    // Cek apakah sel ini merupakan bagian dari header yang titik awalnya (origin) berada di halaman sebelah kiri
                    // Jika iya, render ulang judul induknya di awal chunk ini agar header atas tidak bolong!
                    if ($c === $startCol) {
                        for ($lookLeft = $c - 1; $lookLeft > $identityColCount; $lookLeft--) {
                            if (isset($grid['origins'][$r][$lookLeft])) {
                                $origCell = $grid['origins'][$r][$lookLeft];
                                $origEnd  = $lookLeft + $origCell['colspan'] - 1;
                                if ($origEnd >= $c) {
                                    $remSpan = min($origEnd, $endCol) - $c + 1;
                                    $rowspan = min($origCell['rowspan'], $headerRowCount - $r + 1);
                                    $csAttr  = $remSpan > 1 ? ' colspan="' . $remSpan . '"' : '';
                                    $rsAttr  = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';
                                    $html   .= "<th{$rsAttr}{$csAttr}>" . htmlspecialchars($origCell['value'], ENT_QUOTES, 'UTF-8') . "</th>";
                                }
                                break;
                            }
                        }
                    }
                } else {
                    $html .= '<th></th>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</thead><tbody>';

        // 2. Render Baris Data
        for ($r = $rStart; $r <= $rEnd; $r++) {
            $html .= '<tr style="height: 16px;">';
            if ($r <= $grid['totalRows']) {
                foreach ($validCols as $c => $dummy) {
                    if (isset($grid['origins'][$r][$c])) {
                        $cell = $grid['origins'][$r][$c];
                        $actualColspan = 0;
                        $maxC = min($c + $cell['colspan'] - 1, $grid['totalCols']);
                        for ($i = $c; $i <= $maxC; $i++) {
                            if (isset($validCols[$i])) $actualColspan++;
                        }
                        if ($actualColspan > 0) {
                            $rowspan = min($cell['rowspan'], min($rEnd, $grid['totalRows']) - $r + 1);
                            $tag     = $cell['isHeader'] ? 'th' : 'td';
                            $csAttr  = $actualColspan > 1 ? ' colspan="' . $actualColspan . '"' : '';
                            $rsAttr  = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';
                            $html   .= "<{$tag}{$rsAttr}{$csAttr}>" . htmlspecialchars($cell['value'], ENT_QUOTES, 'UTF-8') . "</{$tag}>";
                        }
                    } elseif (empty($grid['occupied'][$r][$c])) {
                        $html .= '<td>-</td>';
                    }
                }
            } else {
                foreach ($validCols as $c => $dummy) {
                    $html .= '<td>-</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    private function buildKopSuratHtml($namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess): string
    {
        $revisiFormatted = ($revisi !== '' && $revisi !== null) ? str_pad((string)$revisi, 2, '0', STR_PAD_LEFT) : '00';
        $kotakTtdHtml    = '';

        if (strtolower((string)$typeProcess) === 'startup') {
            $kotakTtdHtml = '
                <tr><td style="width: 50%; text-align: center; padding: 1px; font-weight: bold;">QC</td><td style="width: 50%; text-align: center; padding: 1px; font-weight: bold;">Production</td></tr>
                <tr><td style="height: 16px;"></td><td style="height: 16px;"></td></tr>';
        } else {
            $ttd = ($namaApprover !== '' && $namaApprover !== '-')
                ? '<span style="font-size: 7px;">Approved by:<br><b>' . nl2br(htmlspecialchars($namaApprover)) . '</b></span>'
                : '';
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
                <td rowspan="2" style="width: 25%; vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 7.5px;" border="1">
                        <tr><td style="width: 38%; text-align: left; padding: 1px 2px;">No. Dok / No.</td><td style="text-align: left; padding: 1px 2px;">: ' . htmlspecialchars((string)$noDok) . '</td></tr>
                        <tr><td style="text-align: left; padding: 1px 2px;">Revisi</td><td style="text-align: left; padding: 1px 2px;">: ' . htmlspecialchars($revisiFormatted) . '</td></tr>
                        <tr><td style="text-align: left; padding: 1px 2px;">Berlaku</td><td style="text-align: left; padding: 1px 2px;">: ' . htmlspecialchars((string)$tglBerlaku) . '</td></tr>
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