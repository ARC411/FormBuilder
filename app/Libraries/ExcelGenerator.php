<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelGenerator
{
    public function generate(string $htmlString, array $context)
    {
        extract($context); 

        if (($typeProcess ?? '') === 'foregoing' && !empty($judulProses)) {
            $cleanFg = trim(preg_replace('/^(Foregoing\s+Check\s+Sheet\s+of|Foregoing\s+Check\s+Sheet|Foregoing)\s+/i', '', $judulProses));
            $cleanFg = trim(preg_replace('/\s*Foregoing\s*Check\s*Sheet.*$/i', '', $cleanFg));
            $judulProses = $cleanFg . ' Foregoing Check Sheet';
        }

        $extractResult = $this->extractTableElement(
            $htmlString,
            'table1',
            $typeProcess,
            $namaApprover,
            $alldata ?? [],
            $process ?? ''
        );
        if ($extractResult === null) {
            die("Error: table1 tidak ditemukan atau format tidak sesuai.");
        }
        
        $tableElement = $extractResult['table'];
        $identityCols = $extractResult['identityCols']; 

        $grid = $this->buildGrid($tableElement);
        $headerRowCount = $this->countHeaderRows($grid);

        $spreadsheet = new Spreadsheet();
        $defaultFontSize = ($typeProcess === 'startup') ? 12 : 14;
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize($defaultFontSize);
        $sheet = $spreadsheet->getActiveSheet();
        
        $tableStartRow = 6; 
        $PER_PAGE = ($typeProcess === 'startup') ? 14 : 20; 
        $dataColsCount = count($alldata ?? []);
        $targetDataRowCount = 0;

        $startupColWidths = $this->getStartupColWidths($identityCols, $PER_PAGE);
        
        if ($typeProcess === 'startup') {
            $totalPages = max(1, (int) ceil($dataColsCount / $PER_PAGE));
            $currentRow = 1;
            
            for ($p = 0; $p < $totalPages; $p++) {
                $this->insertKopSuratVertical(
                    $sheet,
                    $currentRow,
                    $namaProduk,
                    $judulProses,
                    $noDok,
                    $machNo,
                    $tglBerlaku,
                    $namaApprover,
                    $revisi,
                    $typeProcess,
                    $identityCols,
                    $PER_PAGE,
                    $weeklyInfo ?? null
                );
                
                $tableStartRowChunk = $currentRow + 5;
                $this->writeBlockVerticalChunk(
                    $grid,
                    $sheet,
                    $tableStartRowChunk,
                    $identityCols,
                    $p,
                    $PER_PAGE
                );
                
                $lastRowOfChunk = $tableStartRowChunk + $grid['totalRows'] - 1;
                $validEndRow = $lastRowOfChunk;
                
                for ($r = $lastRowOfChunk; $r >= $tableStartRowChunk; $r--) {
                    $cellVal = (string) $sheet->getCell('B' . $r)->getValue();
                    if (stripos(trim($cellVal), 'note') !== false) {
                        $validEndRow = $r; 
                        break;
                    }
                }
                
                $lastColIdxChunk = $identityCols + 1 + $PER_PAGE;
                $lastColLetter = Coordinate::stringFromColumnIndex($lastColIdxChunk);
                
                $tableRange = "B" . $tableStartRowChunk . ":" . $lastColLetter . $validEndRow;
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle($tableRange)->getFont()->setSize(11);
                
                if ($validEndRow < $lastRowOfChunk) {
                    foreach ($sheet->getMergeCells() as $mergeRange) {
                        $bounds = Coordinate::rangeBoundaries($mergeRange);
                        $mergeStartRow = (int) $bounds[0][1];
                        if ($mergeStartRow > $validEndRow && $mergeStartRow <= $lastRowOfChunk) {
                            $sheet->unmergeCells($mergeRange);
                        }
                    }

                    for ($r = $validEndRow + 1; $r <= $lastRowOfChunk; $r++) {
                        for ($c = 1; $c <= $lastColIdxChunk; $c++) {
                            $colLet = Coordinate::stringFromColumnIndex($c);
                            $sheet->setCellValue($colLet . $r, '');
                        }
                    }
                }

                $this->applyDynamicStartupRowHeights(
                    $sheet,
                    $grid,
                    $tableStartRowChunk,
                    $validEndRow,
                    $identityCols,
                    $p,
                    $PER_PAGE,
                    $startupColWidths
                );
                
                if ($p < $totalPages - 1) {
                    $sheet->setBreak("A" . $validEndRow, Worksheet::BREAK_ROW);
                }
                
                $currentRow = $validEndRow + 1;
            }
            
            $lastColIndex = $identityCols + 1 + $PER_PAGE;
            $lastRow = $validEndRow;
        } else {
            $totalDataCols = max(1, $grid['totalCols'] - $identityCols);
            $this->insertKopSuratHorizontal(
                $sheet,
                1,
                $totalDataCols,
                $namaProduk,
                $judulProses,
                $noDok,
                $machNo,
                $tglBerlaku,
                $namaApprover,
                $revisi,
                $typeProcess,
                $identityCols,
                $totalDataCols,
                $weeklyInfo ?? null
            );
            
            $this->writeBlockFromGrid(
                $grid,
                $sheet,
                $tableStartRow,
                $grid['totalCols'],
                $totalDataCols,
                $identityCols,
                0,
                $totalDataCols,
                1
            );
             
            $lastColIndex = $grid['totalCols'] + 1;
            $lastRow = $tableStartRow + $grid['totalRows'] - 1;

            $dataRowCount = $grid['totalRows'] - $headerRowCount;
            $totalPages = max(1, (int) ceil($dataRowCount / $PER_PAGE));
            $targetDataRowCount = $totalPages * $PER_PAGE;
            $dummyRowsNeeded = $targetDataRowCount - $dataRowCount;

            if ($dummyRowsNeeded > 0) {
                if ($typeProcess === 'foregoing') {
                    $posCycle = ['X', 'Y', 'Z', 'θ'];
                    $d = 0;
                    while ($d < $dummyRowsNeeded) {
                        $remaining = $dummyRowsNeeded - $d;
                        $blockSize = min(4, $remaining);
                        $blockStartRow = $lastRow + 1;
                        $blockEndRow   = $blockStartRow + $blockSize - 1;

                        for ($s = 0; $s < $blockSize; $s++) {
                            $rCurr = $blockStartRow + $s;
                            for ($c = 2; $c <= $lastColIndex; $c++) {
                                $coord = Coordinate::stringFromColumnIndex($c) . $rCurr;
                                if ($c === 9) {
                                    $sheet->setCellValue($coord, $posCycle[$s] ?? '-');
                                } else {
                                    $sheet->setCellValue($coord, '-');
                                }
                                $sheet->getStyle($coord)
                                    ->getAlignment()
                                    ->setVertical(Alignment::VERTICAL_CENTER)
                                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            }
                        }

                        if ($blockSize > 1) {
                            for ($c = 2; $c <= 8; $c++) {
                                $colLet = Coordinate::stringFromColumnIndex($c);
                                $sheet->mergeCells($colLet . $blockStartRow . ":" . $colLet . $blockEndRow);
                            }
                        }

                        $lastRow = $blockEndRow;
                        $d += $blockSize;
                    }
                } else {
                    for ($d = 1; $d <= $dummyRowsNeeded; $d++) {
                        $lastRow++; 
                        for ($c = 2; $c <= $lastColIndex; $c++) { 
                            $coord = Coordinate::stringFromColumnIndex($c) . $lastRow;
                            $sheet->setCellValue($coord, '-');
                            $sheet->getStyle($coord)
                                ->getAlignment()
                                ->setVertical(Alignment::VERTICAL_CENTER)
                                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }
                    }
                }
            }
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex); 
        $headerEndRow = $tableStartRow + $headerRowCount - 1;

        if ($typeProcess !== 'startup') {
            $tableRange = "B" . $tableStartRow . ":" . $lastColLetter . $lastRow;
            $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            for ($p = 1; $p < $totalPages; $p++) {
                $breakRow = $headerEndRow + ($p * $PER_PAGE);
                if ($breakRow < $lastRow) {
                    $sheet->setBreak("A" . $breakRow, Worksheet::BREAK_ROW);
                }
            }
        }

        $sheet->getSheetView()->setView(SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);
        
        if ($typeProcess !== 'startup') {
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $headerEndRow);
        }

        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        
        $printAreaRange = "B1:" . $lastColLetter . $lastRow;
        $sheet->getPageSetup()->setPrintArea($printAreaRange);
        $sheet->getPageMargins()->setTop(0.2)->setRight(0.2)->setLeft(0.2)->setBottom(0.2);
        
        $sheet->getColumnDimension('A')->setWidth(1); 

        if ($typeProcess === 'startup') {
            foreach ($startupColWidths as $colIdx => $wVal) {
                $colLet = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->getColumnDimension($colLet)->setWidth($wVal);
            }
        } elseif ($typeProcess === 'foregoing') {
            $defaultForegoingRestWidth = 6.5;
            $totalForegoingWidth = 0.0;

            for ($col = 2; $col <= $lastColIndex; $col++) {
                $gridCol = $col - 1;
                $headerLabel = '';
                for ($hr = 1; $hr <= $headerRowCount; $hr++) {
                    if (isset($grid['origins'][$hr][$gridCol])) {
                        $hCell = $grid['origins'][$hr][$gridCol];
                        if (($hCell['colspan'] ?? 1) === 1) {
                            $headerLabel .= ' ' . strtolower(trim($hCell['value'] ?? ''));
                        }
                    }
                }
                $headerLabel = trim(preg_replace('/\s+/', ' ', $headerLabel));

                $colWidth = $defaultForegoingRestWidth;
                if ($gridCol === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $colWidth = 4.5;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $colWidth = 11.20;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $colWidth = 15.70;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $colWidth = 14.0;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $colWidth = 12.0;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $colWidth = 11.70;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $colWidth = 11.10;
                } elseif (strpos($headerLabel, 'pos') !== false) {
                    $colWidth = 6.5;
                } elseif (strpos($headerLabel, 'judge') !== false || strpos($headerLabel, 'ok/ng') !== false) {
                    $colWidth = 5.8;
                }

                $colLet = Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($colLet)->setWidth($colWidth);
                $totalForegoingWidth += $colWidth;
            }

            $headerRange = "B" . $tableStartRow . ":" . $lastColLetter . $headerEndRow;
            $sheet->getStyle($headerRange)->getFont()->setSize(12.5)->setBold(true);
            $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);

            if ($lastRow > $headerEndRow) {
                $dataStartRow = $headerEndRow + 1;
                $dataRange = "B" . $dataStartRow . ":" . $lastColLetter . $lastRow;
                $sheet->getStyle($dataRange)->getFont()->setSize(17.5);
                $sheet->getStyle($dataRange)
                    ->getAlignment()
                    ->setWrapText(false)
                    ->setShrinkToFit(true);
            }

            // Kalibrasi tinggi baris Foregoing dengan tinggi Kop Surat seragam (144 pt)
            $maxPageHeightPt = $totalForegoingWidth * 5.05;
            $kopHeightPt     = (4 * 22) + 56; // 144 pt
            $headerRowPt     = 28;
            $headerTotalPt   = $headerRowCount * $headerRowPt;
            $idealFgRowPt    = max(38, min(62, (int) floor(($maxPageHeightPt - $kopHeightPt - $headerTotalPt) / $PER_PAGE)));

            for ($r = $tableStartRow; $r <= $headerEndRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($headerRowPt);
            }
            for ($r = $headerEndRow + 1; $r <= $lastRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($idealFgRowPt);
            }
        } else {
            $totalProdWidth = 0.0;

            for ($col = 2; $col <= $lastColIndex; $col++) {
                $gridCol = $col - 1;
                $headerLabel = '';
                for ($hr = 1; $hr <= $headerRowCount; $hr++) {
                    if (isset($grid['origins'][$hr][$gridCol])) {
                        $hCell = $grid['origins'][$hr][$gridCol];
                        if (($hCell['colspan'] ?? 1) === 1) {
                            $headerLabel .= ' ' . strtolower(trim($hCell['value'] ?? ''));
                        }
                    }
                }
                $headerLabel = trim(preg_replace('/\s+/', ' ', $headerLabel));

                if ($gridCol === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $colWidth = 4.5;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $colWidth = 22.15;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $colWidth = 12.55;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $colWidth = 10.10;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $colWidth = 15.5;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $colWidth = 14.10;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $colWidth = 14.55;
                } elseif (
                    strpos($headerLabel, 'in') !== false ||
                    strpos($headerLabel, 'out') !== false ||
                    strpos($headerLabel, 'ng') !== false ||
                    strpos($headerLabel, 'std') !== false ||
                    strpos($headerLabel, 'disp') !== false ||
                    strpos($headerLabel, 'vacuum') !== false ||
                    strpos($headerLabel, 'pressure') !== false ||
                    strpos($headerLabel, 'time') !== false
                ) {
                    $colWidth = 6.2;
                } else {
                    $colWidth = 5.2;
                }

                $colLet = Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($colLet)->setWidth($colWidth);
                $totalProdWidth += $colWidth;
            }

            $headerRange = "B" . $tableStartRow . ":" . $lastColLetter . $headerEndRow;
            $sheet->getStyle($headerRange)->getFont()->setSize(12.5)->setBold(true);
            $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);

            if ($lastRow > $headerEndRow) {
                $dataStartRow = $headerEndRow + 1;
                $dataRange = "B" . $dataStartRow . ":" . $lastColLetter . $lastRow;
                $sheet->getStyle($dataRange)->getFont()->setSize(18);
                $sheet->getStyle($dataRange)
                    ->getAlignment()
                    ->setWrapText(false)
                    ->setShrinkToFit(true);
            }

            // Kalibrasi tinggi baris Production dengan tinggi Kop Surat seragam (144 pt)
            $maxPageHeightPt = $totalProdWidth * 5.05;
            $kopHeightPt     = (4 * 22) + 56; // 144 pt
            $headerRowPt     = 28;
            $headerTotalPt   = $headerRowCount * $headerRowPt;
            $idealDataRowPt  = max(38, min(68, (int) floor(($maxPageHeightPt - $kopHeightPt - $headerTotalPt) / $PER_PAGE)));

            for ($r = $tableStartRow; $r <= $headerEndRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($headerRowPt);
            }
            for ($r = $headerEndRow + 1; $r <= $lastRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($idealDataRowPt);
            }
        }

        $procClean = strtoupper($process ?? 'DOC');
        $fileName = 'Report_' . $procClean . '_' . date('Ymd_Hi') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    private function getStartupColWidths(int $identityCols, int $PER_PAGE): array
    {
        $widths = [
            1 => 1.0,
            2 => 5.0,
        ];

        if ($identityCols === 4) {
            $widths[3] = 22.0;
            $widths[4] = 15.0;
            $widths[5] = 30.0;
        } elseif ($identityCols === 3) {
            $widths[3] = 28.0;
            $widths[4] = 30.0;
        } else {
            for ($c = 3; $c <= $identityCols + 1; $c++) {
                $widths[$c] = ($c === $identityCols + 1) ? 30.0 : 20.0;
            }
        }

        $lastColIndex = $identityCols + 1 + $PER_PAGE;
        for ($c = $identityCols + 2; $c <= $lastColIndex; $c++) {
            $widths[$c] = 14.5;
        }

        return $widths;
    }

    private function countWrappedLines(string $text, float $colWidth, float $charsPerUnit = 0.88): int
    {
        $text = trim($text);
        if ($text === '' || $text === '-') {
            return 1;
        }

        $effChars = max(1.0, ($colWidth - 1.2) * $charsPerUnit);
        $totalLines = 0;
        $paragraphs = preg_split('/\r\n|\r|\n/', $text);

        foreach ($paragraphs as $para) {
            $para = trim(preg_replace('/[ \t]+/', ' ', $para));
            if ($para === '') {
                $totalLines += 1;
                continue;
            }

            $words = explode(' ', $para);
            $currLen = 0;
            $pLines = 1;

            foreach ($words as $w) {
                $wLen = mb_strlen($w);
                if ($currLen === 0) {
                    $currLen = $wLen;
                    if ($currLen > $effChars) {
                        $extra = (int) ceil($currLen / $effChars) - 1;
                        $pLines += $extra;
                        $currLen = $currLen % max(1, (int) $effChars);
                    }
                } elseif (($currLen + 1 + $wLen) <= $effChars) {
                    $currLen += 1 + $wLen;
                } else {
                    $pLines++;
                    $currLen = $wLen;
                    if ($currLen > $effChars) {
                        $extra = (int) ceil($currLen / $effChars) - 1;
                        $pLines += $extra;
                        $currLen = $currLen % max(1, (int) $effChars);
                    }
                }
            }

            $totalLines += $pLines;
        }

        return max(1, $totalLines);
    }

    private function applyDynamicStartupRowHeights(
        $sheet,
        array $grid,
        int $tableStartRowChunk,
        int $validEndRow,
        int $identityCols,
        int $pageIndex,
        int $PER_PAGE,
        array $colWidths
    ): void {
        $origins = $grid['origins'] ?? [];
        $numChunkRows = $validEndRow - $tableStartRowChunk + 1;
        if ($numChunkRows <= 0) {
            return;
        }

        $dataColStartOffset = $pageIndex * $PER_PAGE;
        $lineHeightPt = 15.5;
        $paddingPt = 8.0;
        $baseMinRowHeight = 22.0;

        $minHeights = [];
        for ($r = 1; $r <= $numChunkRows; $r++) {
            $minHeights[$r] = $baseMinRowHeight;
        }

        for ($r = 1; $r <= $numChunkRows; $r++) {
            for ($c = 1; $c <= $identityCols; $c++) {
                if (!isset($origins[$r][$c])) {
                    continue;
                }
                $cell = $origins[$r][$c];
                $rs = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs = min((int) ($cell['colspan'] ?? 1), $identityCols - $c + 1);
                if ($rs === 1) {
                    $wSum = 0.0;
                    for ($cc = $c + 1; $cc <= $c + $cs; $cc++) {
                        $wSum += ($colWidths[$cc] ?? 14.5);
                    }
                    $valStr = html_entity_decode(strip_tags($cell['value'] ?? ''));
                    $lines = $this->countWrappedLines($valStr, $wSum);
                    $needed = ceil($lines * $lineHeightPt + $paddingPt);
                    if ($needed > $minHeights[$r]) {
                        $minHeights[$r] = $needed;
                    }
                }
            }

            for ($dIdx = 1; $dIdx <= $PER_PAGE; $dIdx++) {
                $gridCol = $identityCols + $dataColStartOffset + $dIdx;
                if (!isset($origins[$r][$gridCol])) {
                    continue;
                }
                $cell = $origins[$r][$gridCol];
                $rs = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs = min((int) ($cell['colspan'] ?? 1), $PER_PAGE - $dIdx + 1);
                if ($rs === 1) {
                    $targetCol = $identityCols + 1 + $dIdx;
                    $wSum = 0.0;
                    for ($cc = $targetCol; $cc < $targetCol + $cs; $cc++) {
                        $wSum += ($colWidths[$cc] ?? 14.5);
                    }
                    $valStr = html_entity_decode(strip_tags($cell['value'] ?? ''));
                    $lines = $this->countWrappedLines($valStr, $wSum);
                    $needed = ceil($lines * $lineHeightPt + $paddingPt);
                    if ($needed > $minHeights[$r]) {
                        $minHeights[$r] = $needed;
                    }
                }
            }
        }

        for ($r = 1; $r <= $numChunkRows; $r++) {
            for ($c = 1; $c <= $identityCols; $c++) {
                if (!isset($origins[$r][$c])) {
                    continue;
                }
                $cell = $origins[$r][$c];
                $rs = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs = min((int) ($cell['colspan'] ?? 1), $identityCols - $c + 1);
                if ($rs > 1) {
                    $endR = min($numChunkRows, $r + $rs - 1);
                    $actualRs = max(1, $endR - $r + 1);
                    $wSum = 0.0;
                    for ($cc = $c + 1; $cc <= $c + $cs; $cc++) {
                        $wSum += ($colWidths[$cc] ?? 14.5);
                    }
                    $valStr = html_entity_decode(strip_tags($cell['value'] ?? ''));
                    $lines = $this->countWrappedLines($valStr, $wSum);
                    $neededTotal = ceil($lines * $lineHeightPt + $paddingPt);

                    $currTotal = 0.0;
                    for ($rr = $r; $rr <= $endR; $rr++) {
                        $currTotal += $minHeights[$rr];
                    }
                    if ($neededTotal > $currTotal) {
                        $addPerRow = ceil(($neededTotal - $currTotal) / $actualRs);
                        for ($rr = $r; $rr <= $endR; $rr++) {
                            $minHeights[$rr] += $addPerRow;
                        }
                    }
                }
            }
        }

        // Anggaran tinggi tabel Startup disesuaikan dengan tinggi Kop Surat seragam (144 pt)
        $targetTableBudget = 1035.0;
        $rawSum = array_sum($minHeights);

        for ($r = 1; $r <= $numChunkRows; $r++) {
            $sheetRow = $tableStartRowChunk + $r - 1;
            if ($rawSum < $targetTableBudget) {
                $extraPerRow = ($targetTableBudget - $rawSum) / $numChunkRows;
                $finalH = round($minHeights[$r] + $extraPerRow, 1);
            } else {
                $scale = $targetTableBudget / max(1.0, $rawSum);
                $finalH = round(max(20.0, $minHeights[$r] * $scale), 1);
            }
            $sheet->getRowDimension($sheetRow)->setRowHeight($finalH);
        }
    }

    private function extractTableElement(
        string $html,
        string $id,
        string $typeProcess,
        string $namaApprover,
        array $alldata,
        string $process = ''
    ): ?array {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query("//table[@id='" . $id . "']");
        $table = $nodes->length > 0 ? $nodes->item(0) : null;
        if (!$table) {
            return null;
        }

        $theads = $table->getElementsByTagName('thead');
        if ($theads->length > 0) {
            $firstThead = $theads->item(0);
            $firstTr = $firstThead->getElementsByTagName('tr')->item(0);
            if ($firstTr) {
                $firstCell = $firstTr->getElementsByTagName('th')->item(0);
                if ($firstCell && $firstCell->hasAttribute('colspan')) {
                    $firstTr->parentNode->removeChild($firstTr);
                }
            }
        }

        if ($typeProcess === 'production') {
            $this->sanitizeProductionDom($doc, $table, $alldata, $process);
        }

        $maxExistingCols = 0;
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
        foreach ($trs as $tr) {
            $cols = 0;
            foreach ($tr->childNodes as $td) {
                if ($td instanceof \DOMElement && in_array(strtolower($td->tagName), ['td','th'])) {
                    $cols += (int) ($td->getAttribute('colspan') ?: 1);
                }
            }
            if ($cols > $maxExistingCols) {
                $maxExistingCols = $cols;
            }
        }

        if ($typeProcess === 'startup') {
            $dataColsCount = count($alldata);
            $identityCols = max(1, $maxExistingCols - $dataColsCount);
            
            foreach ($trs as $tr) {
                $firstCell = $tr->getElementsByTagName('td')->item(0) ?? $tr->getElementsByTagName('th')->item(0);
                if ($firstCell) {
                    $txt = strtolower(trim($firstCell->textContent));
                    $isMetaRow = (
                        strpos($txt, 'status approval') !== false ||
                        strpos($txt, 'operator') !== false ||
                        strpos($txt, 'approved by') !== false ||
                        strpos($txt, 'checked') !== false
                    );
                    if ($isMetaRow) {
                        $tr->parentNode->removeChild($tr);
                    }
                }
            }

            $trs = $table->getElementsByTagName('tr');
            $noteTr = null;
            foreach ($trs as $tr) {
                if (stripos($tr->textContent, 'Note') !== false) {
                    $noteTr = $tr;
                    break;
                }
            }

            if ($noteTr) {
                $getVal = function($item, $keys) {
                    if (is_array($item)) {
                        foreach ($keys as $k) {
                            if (isset($item[$k]) && $item[$k] !== '') {
                                return $item[$k];
                            }
                        }
                    } elseif (is_object($item)) {
                        foreach ($keys as $k) {
                            if (isset($item->$k) && $item->$k !== '') {
                                return $item->$k;
                            }
                        }
                    }
                    return '-';
                };

                $PER_PAGE = 14;
                $totalPages = max(1, (int) ceil($dataColsCount / $PER_PAGE));
                $targetCols = $totalPages * $PER_PAGE;

                $opTr = $doc->createElement('tr');
                $tdOpLbl = $doc->createElement('td', 'Operator');
                $tdOpLbl->setAttribute('colspan', (string) $identityCols);
                $opTr->appendChild($tdOpLbl);

                for ($i = 0; $i < $targetCols; $i++) {
                    if (isset($alldata[$i])) {
                        $opKeys = ['empid', 'operator', 'created_by', 'name'];
                        $opEmpId = $getVal($alldata[$i], $opKeys);
                        $tdOpData = $doc->createElement('td', htmlspecialchars((string) $opEmpId));
                    } else {
                        $tdOpData = $doc->createElement('td', '-');
                    }
                    $opTr->appendChild($tdOpData);
                }
                $noteTr->parentNode->insertBefore($opTr, $noteTr);

                $appTr = $doc->createElement('tr');
                $tdAppLbl = $doc->createElement('td', 'Checked by');
                $tdAppLbl->setAttribute('colspan', (string) $identityCols);
                $appTr->appendChild($tdAppLbl);

                for ($i = 0; $i < $targetCols; $i++) {
                    if (isset($alldata[$i])) {
                        $stKeys = ['status', 'status_approval', 'is_approved', 'approval'];
                        $rawStatus = (string) $getVal($alldata[$i], $stKeys);
                        $status = strtolower(trim($rawStatus));
                        $isApproved = (
                            $status !== '-' &&
                            $status !== '' &&
                            (strpos($status, 'approve') !== false || strpos($status, 'bypass') !== false || strpos($status, 'check') !== false)
                        );
                        if ($isApproved) {
                            $spv = $getVal($alldata[$i], ['supervisor']);
                            $ldr = $getVal($alldata[$i], ['leader']);
                            $frm = $getVal($alldata[$i], ['foreman']);
                            
                            if ($spv !== '-') {
                                $appText = trim($spv) . "\n(Supervisor)";
                            } elseif ($ldr !== '-') {
                                $appText = trim($ldr) . "\n(Leader)";
                            } elseif ($frm !== '-') {
                                $appText = trim($frm) . "\n(Foreman)";
                            } else {
                                $cleanStatus = preg_replace('/^(approved?|check(ed)?)\s*(by\s*)?/i', '', trim($rawStatus));
                                $appText = !empty($cleanStatus) ? trim($cleanStatus) : 'Approved';
                            }

                            $tdAppData = $doc->createElement('td');
                            $tdAppData->appendChild($doc->createTextNode($appText));
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
        } else {
            // Seragamkan Production & Foregoing: Kop Surat kiri merge 4 kolom (Kolom B s/d E)
            $identityCols = 4;
        }
        
        return ['table' => $table, 'identityCols' => $identityCols];
    }

    private function sanitizeProductionDom(\DOMDocument $doc, \DOMElement $table, array $alldata, string $process): void
    {
        $tbodies = $table->getElementsByTagName('tbody');
        if ($tbodies->length === 0) {
            return;
        }
        $tbody = $tbodies->item(0);
        $dataRows = [];
        foreach ($tbody->childNodes as $node) {
            if ($node instanceof \DOMElement && strtolower($node->tagName) === 'tr') {
                $dataRows[] = $node;
            }
        }

        foreach ($dataRows as $idx => $tr) {
            $rowData = $alldata[$idx] ?? null;
            if (!$rowData) {
                continue;
            }
            $emp2 = trim((string) ($rowData['empid2'] ?? ''));
            $name2 = trim((string) ($rowData['name2'] ?? ''));
            $noOpFinish = (($emp2 === '' || $emp2 === '-') && ($name2 === '' || $name2 === '-'));

            if ($noOpFinish) {
                foreach ($tr->childNodes as $td) {
                    if ($td instanceof \DOMElement && strtolower($td->tagName) === 'td') {
                        $cellTxt = trim($td->textContent);
                        if ($cellTxt === '07:00' || $cellTxt === '07:00:00') {
                            $td->nodeValue = '-';
                        }
                    }
                }
            }
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
                if (!($cell instanceof \DOMElement)) {
                    continue;
                }
                $tag = strtolower($cell->tagName);
                if ($tag !== 'td' && $tag !== 'th') {
                    continue;
                }
                while (!empty($occupied[$currentRow][$col])) {
                    $col++;
                }
                $colspan = max(1, (int) ($cell->getAttribute('colspan') ?: 1));
                $rowspan = max(1, (int) ($cell->getAttribute('rowspan') ?: 1));
                
                $textContent = trim($cell->textContent);
                $isHeader = ($tag === 'th') || $inThead;
                
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
        if ($firstCell) {
            return ($r - 1) + $firstCell['rowspan'];
        }
        return 1;
    }

    private function insertKopSuratHorizontal(
        $sheet,
        int $startRow,
        int $targetDataCols,
        string $namaProduk,
        string $judulProses,
        string $noDok,
        string $machNo,
        string $tglBerlaku,
        string $namaApprover,
        string $revisi,
        string $typeProcess,
        int $identityCols,
        int $PER_PAGE,
        $weeklyInfo = null
    ) {
        $r1 = $startRow;
        $r2 = $startRow + 1;
        $r3 = $startRow + 2;
        $r4 = $startRow + 3;
        $r5 = $startRow + 4;

        // Pastikan Kop Surat kiri selalu di-merge sampai Kolom E (indeks 5)
        $kopEndColIndex = max(5, $identityCols + 1);
        $lastIdentityLetter = Coordinate::stringFromColumnIndex($kopEndColIndex);
        $kopFontSize = 13.5;

        $ptText = "PT. FOXCONN TECHNOLOGIES INDONESIA\nProduction Engineering Department\nProcess Engineering Section\n" . $namaProduk;
        $sheet->setCellValue("B" . $r1, $ptText);
        $sheet->mergeCells("B" . $r1 . ":" . $lastIdentityLetter . $r4);
        $sheet->getStyle("B" . $r1)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B" . $r1)->getFont()->setBold(true)->setSize($kopFontSize);

        $sheet->setCellValue("B" . $r5, "MACHINE No : " . $machNo);
        $sheet->mergeCells("B" . $r5 . ":" . $lastIdentityLetter . $r5);
        $sheet->getStyle("B" . $r5)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B" . $r5)->getFont()->setBold(true)->setSize($kopFontSize);
        $sheet->getStyle("B" . $r1 . ":" . $lastIdentityLetter . $r5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        if ($typeProcess === 'startup') {
            $totalPages = max(1, (int) ceil($targetDataCols / $PER_PAGE));
            for ($p = 0; $p < $totalPages; $p++) {
                $chunkStartCol = ($kopEndColIndex + 1) + ($p * $PER_PAGE);
                $chunkEndCol = $chunkStartCol + $PER_PAGE - 1;
                $docStartCol = $chunkEndCol - 2;

                $this->drawCenterAndRightKopSurat(
                    $sheet, $r1, $r2, $r3, $r4,
                    $chunkStartCol, $chunkEndCol, $docStartCol,
                    $namaProduk, $judulProses, $noDok, $tglBerlaku,
                    $namaApprover, $revisi, $typeProcess, $weeklyInfo, $r5
                );
            }
        } else {
            $chunkStartCol = $kopEndColIndex + 1;
            $chunkEndCol = $identityCols + 1 + $targetDataCols;
            $docStartCol = max($chunkStartCol + 1, $chunkEndCol - 5);
            $this->drawCenterAndRightKopSurat(
                $sheet, $r1, $r2, $r3, $r4,
                $chunkStartCol, $chunkEndCol, $docStartCol,
                $namaProduk, $judulProses, $noDok, $tglBerlaku,
                $namaApprover, $revisi, $typeProcess, $weeklyInfo, $r5
            );
        }

        // Tinggi baris Kop Surat seragam (4x22pt + 56pt = 144pt)
        for ($r = $r1; $r <= $r4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }
        $sheet->getRowDimension($r5)->setRowHeight(56);
    }

    private function drawCenterAndRightKopSurat(
        $sheet, $r1, $r2, $r3, $r4,
        $chunkStartCol, $chunkEndCol, $docStartCol,
        $namaProduk, $judulProses, $noDok, $tglBerlaku,
        $namaApprover, $revisi, $typeProcess,
        $weeklyInfo = null, $r5 = null
    ) {
        $docStartLetter = Coordinate::stringFromColumnIndex($docStartCol);
        $docEndLetter   = Coordinate::stringFromColumnIndex($chunkEndCol);

        $docNoClean = $noDok;
        $revisiClean = !empty($revisi) ? $revisi : '00';
        if (preg_match('/^(.*?)-(\d+)$/', $noDok, $m)) {
            $docNoClean = $m[1];
            $revisiClean = $m[2];
        }

        $lastKopRow = ($r5 !== null) ? $r5 : ($r4 + 1);

        if ($typeProcess === 'startup') {
            $docMidLetter = Coordinate::stringFromColumnIndex($docStartCol + 1);

            $sheet->setCellValue($docStartLetter . $r1, "Doc No."); 
            $sheet->setCellValue($docMidLetter . $r1, ": " . $docNoClean);
            $sheet->mergeCells($docMidLetter . $r1 . ":" . $docEndLetter . $r1);

            $sheet->setCellValue($docStartLetter . $r2, "Revisi");       
            $sheet->setCellValue($docMidLetter . $r2, ": " . $revisiClean);
            $sheet->mergeCells($docMidLetter . $r2 . ":" . $docEndLetter . $r2);

            $sheet->setCellValue($docStartLetter . $r3, "Berlaku");      
            $sheet->setCellValue($docMidLetter . $r3, ": " . $tglBerlaku);
            $sheet->mergeCells($docMidLetter . $r3 . ":" . $docEndLetter . $r3);

            $sheet->setCellValue($docStartLetter . $r4, "QC");
            $sheet->setCellValue($docMidLetter . $r4, "Production");
            $sheet->mergeCells($docMidLetter . $r4 . ":" . $docEndLetter . $r4);
            $sheet->getStyle($docStartLetter . $r4 . ":" . $docEndLetter . $r4)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($docStartLetter . $r4 . ":" . $docEndLetter . $r4)
                ->getFont()
                ->setBold(true);

            $qcBox = "";
            if (!empty($weeklyInfo['status_qc']) && strtolower($weeklyInfo['status_qc']) === 'approve') {
                $approverQc = !empty($weeklyInfo['approver_qc']) ? $weeklyInfo['approver_qc'] : 'Approved';
                $dateQc = !empty($weeklyInfo['date_qc']) ? date('d-m-Y', strtotime($weeklyInfo['date_qc'])) : '';
                $qcBox = "Approved by:\n" . $approverQc . "\n(QC)\n" . $dateQc;
            }

            $prodBox = "";
            if (!empty($weeklyInfo['status_production']) && strtolower($weeklyInfo['status_production']) === 'approve') {
                $approverProd = !empty($weeklyInfo['approver_production']) ? $weeklyInfo['approver_production'] : 'Approved';
                $dateProd = !empty($weeklyInfo['date_production']) ? date('d-m-Y', strtotime($weeklyInfo['date_production'])) : '';
                $prodBox = "Approved by:\n" . $approverProd . "\n(Prod)\n" . $dateProd;
            }

            $sheet->setCellValue($docStartLetter . $lastKopRow, $qcBox);
            $sheet->setCellValue($docMidLetter . $lastKopRow, $prodBox);
            $sheet->mergeCells($docMidLetter . $lastKopRow . ":" . $docEndLetter . $lastKopRow);
            $sheet->getStyle($docStartLetter . $lastKopRow . ":" . $docEndLetter . $lastKopRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle($docStartLetter . $lastKopRow . ":" . $docEndLetter . $lastKopRow)
                ->getFont()
                ->setBold(true)
                ->setSize(10);

            $sheet->getStyle($docStartLetter . $r1 . ":" . $docEndLetter . $r3)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $midEndLetter = Coordinate::stringFromColumnIndex(max($chunkStartCol, $docStartCol - 1));
            $centerStartLetter = Coordinate::stringFromColumnIndex($chunkStartCol);

            $sheet->setCellValue($centerStartLetter . $r1, $judulProses . "\n(" . $namaProduk . ")");
            $sheet->mergeCells($centerStartLetter . $r1 . ":" . $midEndLetter . $r4);
            $sheet->getStyle($centerStartLetter . $r1)
                ->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            // Ukuran judul tengah Kop Surat diseragamkan menjadi 18 pt Bold Underline
            $sheet->getStyle($centerStartLetter . $r1)->getFont()->setBold(true)->setSize(18)->setUnderline(true);
            $sheet->mergeCells($centerStartLetter . $lastKopRow . ":" . $midEndLetter . $lastKopRow);

            $sheet->getStyle($centerStartLetter . $r1 . ":" . $docEndLetter . $lastKopRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            // Ukuran teks info dokumen kanan diseragamkan menjadi 12 pt Bold
            $sheet->getStyle($docStartLetter . $r1 . ":" . $docEndLetter . $r4)->getFont()->setBold(true)->setSize(12);
        } else {
            $spanCols = $chunkEndCol - $docStartCol + 1;
            $labelCols = ($spanCols >= 4) ? 2 : 1;
            $docLabelEndLetter = Coordinate::stringFromColumnIndex($docStartCol + $labelCols - 1);
            $docMidLetter      = Coordinate::stringFromColumnIndex($docStartCol + $labelCols);

            $sheet->setCellValue($docStartLetter . $r1, "No. Dok / No."); 
            if ($labelCols > 1) {
                $sheet->mergeCells($docStartLetter . $r1 . ":" . $docLabelEndLetter . $r1);
            }
            $sheet->setCellValue($docMidLetter . $r1, ": " . $docNoClean);
            $sheet->mergeCells($docMidLetter . $r1 . ":" . $docEndLetter . $r1);

            $sheet->setCellValue($docStartLetter . $r2, "Revisi");       
            if ($labelCols > 1) {
                $sheet->mergeCells($docStartLetter . $r2 . ":" . $docLabelEndLetter . $r2);
            }
            $sheet->setCellValue($docMidLetter . $r2, ": " . $revisiClean);
            $sheet->mergeCells($docMidLetter . $r2 . ":" . $docEndLetter . $r2);

            $sheet->setCellValue($docStartLetter . $r3, "Berlaku");      
            if ($labelCols > 1) {
                $sheet->mergeCells($docStartLetter . $r3 . ":" . $docLabelEndLetter . $r3);
            }
            $sheet->setCellValue($docMidLetter . $r3, ": " . $tglBerlaku);
            $sheet->mergeCells($docMidLetter . $r3 . ":" . $docEndLetter . $r3);

            $sheet->setCellValue($docStartLetter . $r4, "Checked");
            $sheet->mergeCells($docStartLetter . $r4 . ":" . $docEndLetter . $r4);
            $sheet->getStyle($docStartLetter . $r4)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($docStartLetter . $r4)
                ->getFont()
                ->setBold(true);

            $ttdText = $namaApprover !== '' ? "Approved by:\n" . $namaApprover : "";
            $sheet->setCellValue($docStartLetter . $lastKopRow, $ttdText);
            $sheet->mergeCells($docStartLetter . $lastKopRow . ":" . $docEndLetter . $lastKopRow);
            $sheet->getStyle($docStartLetter . $lastKopRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle($docStartLetter . $lastKopRow)
                ->getFont()
                ->setBold(true)
                ->setSize(10);

            $sheet->getStyle($docStartLetter . $r1 . ":" . $docEndLetter . $r3)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $midEndLetter = Coordinate::stringFromColumnIndex(max($chunkStartCol, $docStartCol - 1));
            $centerStartLetter = Coordinate::stringFromColumnIndex($chunkStartCol);

            $sheet->setCellValue($centerStartLetter . $r1, $judulProses . "\n(" . $namaProduk . ")");
            $sheet->mergeCells($centerStartLetter . $r1 . ":" . $midEndLetter . $r4);
            $sheet->getStyle($centerStartLetter . $r1)
                ->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            // Ukuran judul tengah Kop Surat diseragamkan menjadi 18 pt Bold Underline
            $sheet->getStyle($centerStartLetter . $r1)->getFont()->setBold(true)->setSize(18)->setUnderline(true);
            $sheet->mergeCells($centerStartLetter . $lastKopRow . ":" . $midEndLetter . $lastKopRow);

            $sheet->getStyle($centerStartLetter . $r1 . ":" . $docEndLetter . $lastKopRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            // Ukuran teks info dokumen kanan diseragamkan menjadi 12 pt Bold
            $sheet->getStyle($docStartLetter . $r1 . ":" . $docEndLetter . $r4)->getFont()->setBold(true)->setSize(12);
        }
    }

    private function writeBlockFromGrid(
        array $grid,
        $sheet,
        int $startRow,
        int $maxGridCols,
        int $targetDataCols,
        int $identityCols,
        int $dataColsCount,
        int $PER_PAGE,
        int $totalPages
    ) {
        $occupied = $grid['occupied'] ?? [];
        $origins = $grid['origins'] ?? [];
        $maxRow = $grid['totalRows'];
        
        $targetGridCols = $identityCols + $targetDataCols;

        for ($r = 1; $r <= $maxRow; $r++) {
            $targetRow = $startRow + $r - 1;

            if (isset($origins[$r])) {
                foreach ($origins[$r] as $c => $cell) {
                    if ($c > $maxGridCols) {
                        continue;
                    }

                    $targetCol = $c + 1; 
                    $cellLetter = Coordinate::stringFromColumnIndex($targetCol);
                    $coord = $cellLetter . $targetRow;
                    
                    $cleanVal = html_entity_decode(strip_tags($cell['value'] ?? ''));
                    $sheet->setCellValue($coord, $cleanVal);
                    
                    $colspan = $cell['colspan'] ?? 1; 
                    $rowspan = $cell['rowspan'] ?? 1;

                    $isHeaderCell = !empty($cell['isHeader']);

                    if ($colspan > 1 && $dataColsCount > 0 && ($c + $colspan - 1) === ($identityCols + $dataColsCount)) {
                        for ($p = 0; $p < $totalPages; $p++) {
                            $chunkStartCol = ($identityCols + 2) + ($p * $PER_PAGE);
                            $chunkEndCol = $chunkStartCol + $PER_PAGE - 1; 
                            
                            $chunkStartLetter = Coordinate::stringFromColumnIndex($chunkStartCol);
                            $chunkEndLetter = Coordinate::stringFromColumnIndex($chunkEndCol);
                            $chunkCoord = $chunkStartLetter . $targetRow;
                            
                            $sheet->setCellValue($chunkCoord, $cleanVal);
                            $sheet->mergeCells($chunkCoord . ":" . $chunkEndLetter . ($targetRow + $rowspan - 1));
                            
                            $style = $sheet->getStyle($chunkCoord);
                            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                            if ($isHeaderCell) {
                                $style->getFont()->setBold(true);
                                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                            }
                            
                            for ($rr = $r; $rr < $r + $rowspan; $rr++) {
                                for ($cc = ($identityCols + 1) + ($p * $PER_PAGE); $cc < ($identityCols + 1) + (($p + 1) * $PER_PAGE); $cc++) {
                                    $occupied[$rr][$cc] = true;
                                }
                            }
                        }
                        continue; 
                    }

                    $actualColspan = min($colspan, $targetGridCols - $c + 1);

                    if ($actualColspan > 1 || $rowspan > 1) {
                        $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $actualColspan - 1);
                        $endRow = $targetRow + $rowspan - 1;
                        $sheet->mergeCells($coord . ":" . $endColLetter . $endRow);
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setWrapText(true);
                    
                    if ($isHeaderCell) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                }
            }

            for ($c = 1; $c <= $targetGridCols; $c++) {
                if (empty($occupied[$r][$c])) {
                    $targetCol = $c + 1; 
                    $cellLetter = Coordinate::stringFromColumnIndex($targetCol);
                    $coord = $cellLetter . $targetRow;
                    
                    $isHeaderRow = false;
                    if (isset($origins[$r])) {
                        foreach ($origins[$r] as $origin) {
                            if ($origin['isHeader']) {
                                $isHeaderRow = true;
                                break;
                            }
                        }
                    }

                    if ($isHeaderRow) {
                        $sheet->getStyle($coord)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                        $sheet->setCellValue($coord, ''); 
                    } else {
                        $sheet->setCellValue($coord, '-');
                    }
                    $sheet->getStyle($coord)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }
        }
    }

    private function insertKopSuratVertical(
        $sheet, $startRow, $namaProduk, $judulProses,
        $noDok, $machNo, $tglBerlaku, $namaApprover,
        $revisi, $typeProcess, $identityCols, $PER_PAGE,
        $weeklyInfo = null
    ) {
        $r1 = $startRow; 
        $r2 = $startRow + 1; 
        $r3 = $startRow + 2; 
        $r4 = $startRow + 3;
        $r5 = $startRow + 4;

        // Pastikan Kop Surat kiri selalu di-merge sampai Kolom E (indeks 5) dan memakai ukuran font 13.5 pt yang sama
        $kopEndColIndex = max(5, $identityCols + 1);
        $lastIdentityLetter = Coordinate::stringFromColumnIndex($kopEndColIndex);
        $kopFontSize = 13.5;

        $ptText = "PT. FOXCONN TECHNOLOGIES INDONESIA\nProduction Engineering Department\nProcess Engineering Section\n" . $namaProduk;
        $sheet->setCellValue("B" . $r1, $ptText);
        $sheet->mergeCells("B" . $r1 . ":" . $lastIdentityLetter . $r4);
        $sheet->getStyle("B" . $r1)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B" . $r1)->getFont()->setBold(true)->setSize($kopFontSize);
        
        $sheet->setCellValue("B" . $r5, "MACHINE No : " . $machNo);
        $sheet->mergeCells("B" . $r5 . ":" . $lastIdentityLetter . $r5);
        $sheet->getStyle("B" . $r5)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("B" . $r5)->getFont()->setBold(true)->setSize($kopFontSize);
        $sheet->getStyle("B" . $r1 . ":" . $lastIdentityLetter . $r5)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $chunkStartCol = $kopEndColIndex + 1;
        $chunkEndCol = ($identityCols + 1) + $PER_PAGE; 
        $docStartCol = $chunkEndCol - 2; 

        $this->drawCenterAndRightKopSurat(
            $sheet, $r1, $r2, $r3, $r4,
            $chunkStartCol, $chunkEndCol, $docStartCol,
            $namaProduk, $judulProses, $noDok, $tglBerlaku,
            $namaApprover, $revisi, $typeProcess, $weeklyInfo, $r5
        );

        // Tinggi baris Kop Surat Startup diseragamkan dengan Production & Foregoing (4x22pt + 56pt = 144pt)
        for ($r = $r1; $r <= $r4; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }
        $sheet->getRowDimension($r5)->setRowHeight(56); 
    }

    private function writeBlockVerticalChunk(
        array $grid, $sheet, int $startRow,
        int $identityCols, int $pageIndex, int $PER_PAGE
    ) {
        $origins = $grid['origins'] ?? [];
        $maxRow = $grid['totalRows'];
        $dataColStartOffset = ($pageIndex * $PER_PAGE);

        for ($r = 1; $r <= $maxRow; $r++) {
            $targetRow = $startRow + $r - 1;
            
            for ($c = 1; $c <= $identityCols; $c++) {
                if (isset($origins[$r][$c])) {
                    $cell = $origins[$r][$c];
                    $targetCol = $c + 1; 
                    $coord = Coordinate::stringFromColumnIndex($targetCol) . $targetRow;
                    
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $colspan = min($cell['colspan'] ?? 1, $identityCols - $c + 1);
                    $rowspan = $cell['rowspan'] ?? 1;
                    
                    if ($colspan > 1 || $rowspan > 1) {
                        $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $colspan - 1);
                        $sheet->mergeCells($coord . ":" . $endColLetter . ($targetRow + $rowspan - 1));
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                }
            }
            
            $massiveHeaderCell = null;
            if (isset($origins[$r][$identityCols + 1])) {
                $checkCell = $origins[$r][$identityCols + 1];
                if (($checkCell['isHeader'] ?? false) && ($checkCell['colspan'] ?? 1) > 2) {
                    $massiveHeaderCell = $checkCell;
                }
            }
            
            $dataIndex = 1;
            while ($dataIndex <= $PER_PAGE) {
                $gridColIndex = $identityCols + $dataColStartOffset + $dataIndex;
                $targetCol = $identityCols + 1 + $dataIndex;
                $coord = Coordinate::stringFromColumnIndex($targetCol) . $targetRow;
                
                if ($dataIndex == 1 && $massiveHeaderCell !== null) {
                    $cell = $massiveHeaderCell;
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $PER_PAGE - 1);
                    $rowspan = $cell['rowspan'] ?? 1;
                    if ($PER_PAGE > 1 || $rowspan > 1) {
                        $sheet->mergeCells($coord . ":" . $endColLetter . ($targetRow + $rowspan - 1));
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                    
                    $dataIndex += $PER_PAGE;
                    continue;
                }
                
                if (isset($origins[$r][$gridColIndex])) {
                    $cell = $origins[$r][$gridColIndex];
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $colspan = $cell['colspan'] ?? 1;
                    $rowspan = $cell['rowspan'] ?? 1;
                    $actualColspan = min($colspan, $PER_PAGE - $dataIndex + 1);
                    
                    if ($actualColspan > 1 || $rowspan > 1) {
                        $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $actualColspan - 1);
                        $sheet->mergeCells($coord . ":" . $endColLetter . ($targetRow + $rowspan - 1));
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                    
                    $dataIndex += $actualColspan;
                } else {
                    if (empty($grid['occupied'][$r][$gridColIndex])) {
                        $sheet->setCellValue($coord, '-');
                        $sheet->getStyle($coord)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    $dataIndex++;
                }
            }
        }
    }
}