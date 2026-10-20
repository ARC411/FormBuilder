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

class ExcelGenerator
{
    public function generate(string $htmlString, array $context)
    {
        extract($context); 

        $extractResult = $this->extractTableElement($htmlString, 'table1', $typeProcess, $namaApprover, $alldata ?? []);
        if ($extractResult === null) die("Error: table1 tidak ditemukan atau format tidak sesuai.");
        
        $tableElement = $extractResult['table'];
        $identityCols = $extractResult['identityCols']; 

        $grid = $this->buildGrid($tableElement);
        $headerRowCount = $this->countHeaderRows($grid);

        $spreadsheet = new Spreadsheet();
        $defaultFontSize = ($typeProcess === 'startup') ? 11 : 15;
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize($defaultFontSize);
        $sheet = $spreadsheet->getActiveSheet();
        
        $tableStartRow = 6; 
        // Startup max 14 kolom, Production max 20 baris
        $PER_PAGE = ($typeProcess === 'startup') ? 14 : 20; 
        $dataColsCount = count($alldata ?? []);
        $targetDataRowCount = 0;
        
        if ($typeProcess === 'startup') {
            $totalPages = max(1, ceil($dataColsCount / $PER_PAGE));
            $currentRow = 1;
            
            for ($p = 0; $p < $totalPages; $p++) {
                // 1. Gambar ulang Kop Surat secara vertikal untuk setiap halaman baru
                $this->insertKopSuratVertical($sheet, $currentRow, $namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $identityCols, $PER_PAGE, $weeklyInfo ?? null);
                
                // 2. Gambar Tabel (Kolom Identitas statis + 14 Kolom Data)
                $tableStartRowChunk = $currentRow + 4; // Jarak baris setelah Kop Surat
                $this->writeBlockVerticalChunk($grid, $sheet, $tableStartRowChunk, $identityCols, $p, $PER_PAGE);
                
                // 3. Styling Kotak (Border) & PENGHAPUS BARIS SAMPAH
                $lastRowOfChunk = $tableStartRowChunk + $grid['totalRows'] - 1;
                $validEndRow = $lastRowOfChunk; // Titik jangkar (anchor) baru
                
                // Cari baris 'Note' sebagai penanda batas akhir halaman yang sah
                for ($r = $lastRowOfChunk; $r >= $tableStartRowChunk; $r--) {
                    $cellVal = (string) $sheet->getCell('B' . $r)->getValue();
                    if (stripos(trim($cellVal), 'note') !== false) {
                        $validEndRow = $r; 
                        break;
                    }
                }
                
                $lastColLetter = Coordinate::stringFromColumnIndex($identityCols + 1 + $PER_PAGE);
                
                // Beri kotak Border murni HANYA sampai batas baris 'Note'
                $tableRange = "B{$tableStartRowChunk}:{$lastColLetter}{$validEndRow}";
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // BERSIHKAN baris nyasar di bawah Note
                if ($validEndRow < $lastRowOfChunk) {
                    // Lepas merge di area sampah agar tidak bertabrakan dengan Kop Surat berikutnya
                    foreach ($sheet->getMergeCells() as $mergeRange) {
                        $bounds = Coordinate::rangeBoundaries($mergeRange);
                        $mergeStartRow = (int) $bounds[0][1];
                        if ($mergeStartRow > $validEndRow && $mergeStartRow <= $lastRowOfChunk) {
                            $sheet->unmergeCells($mergeRange);
                        }
                    }

                    // Kosongkan isi sel
                    for ($r = $validEndRow + 1; $r <= $lastRowOfChunk; $r++) {
                        for ($c = 1; $c <= ($identityCols + 1 + $PER_PAGE); $c++) {
                            $sheet->setCellValue(Coordinate::stringFromColumnIndex($c) . $r, '');
                        }
                    }
                }
                
                // 4. Paksa Potong Kertas TEPAT di garis bawah Note
                if ($p < $totalPages - 1) {
                    $sheet->setBreak("A{$validEndRow}", \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                }
                
                // 5. Update baris awal untuk halaman berikutnya
                // (Kop Surat berikutnya akan naik menimpa baris kosong yang sudah dibersihkan)
                $currentRow = $validEndRow + 2;
            } // <-- PENUTUP for ($p ...) YANG SEBELUMNYA HILANG
            
            $lastColIndex = $identityCols + 1 + $PER_PAGE;
            $lastRow = $currentRow - 2;
        } else {
            $totalDataCols = max(1, $grid['totalCols'] - $identityCols);
            $this->insertKopSuratHorizontal($sheet, 2, $totalDataCols, $namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $identityCols, $totalDataCols, $weeklyInfo ?? null);
            
            $this->writeBlockFromGrid($grid, $sheet, $tableStartRow, $grid['totalCols'], $totalDataCols, $identityCols, 0, $totalDataCols, 1);
             
            $lastColIndex = $grid['totalCols'] + 1;
            $lastRow = $tableStartRow + $grid['totalRows'] - 1;

            $dataRowCount = $grid['totalRows'] - $headerRowCount;
            $totalPages = max(1, ceil($dataRowCount / $PER_PAGE));
            $targetDataRowCount = $totalPages * $PER_PAGE;
            $dummyRowsNeeded = $targetDataRowCount - $dataRowCount;

            if ($dummyRowsNeeded > 0) {
                for ($d = 1; $d <= $dummyRowsNeeded; $d++) {
                    $lastRow++; 
                    for ($c = 2; $c <= $lastColIndex; $c++) { 
                        $coord = Coordinate::stringFromColumnIndex($c) . $lastRow;
                        $sheet->setCellValue($coord, '-');
                        $sheet->getStyle($coord)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            }
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex); 

        // Border global hanya untuk production/foregoing. Startup sudah diberi border per halaman.
        if ($typeProcess !== 'startup') {
            $tableRange = "B{$tableStartRow}:{$lastColLetter}{$lastRow}";
            $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getSheetView()->setView(SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        
        // SETTING AGAR TABEL RATA TENGAH DI KERTAS
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);
        
        $headerEndRow = $tableStartRow + $headerRowCount - 1;
        
        // Ulangi Kop Surat & Header di tiap halaman baru (production)
        if ($typeProcess !== 'startup') {
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $headerEndRow);
        }

        // KUNCI SKALA HALAMAN (Startup maupun Production)
        // Lebar pas 1 kertas, tinggi halaman otomatis dipotong oleh Excel
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);  
        $sheet->getPageSetup()->setFitToHeight(0);
        
        $sheet->getPageSetup()->setPrintArea("A1:{$lastColLetter}{$lastRow}");
        // Margin top dan bottom 0.25 agar lebih mepet tepi
        $sheet->getPageMargins()->setTop(0.25)->setRight(0.3)->setLeft(0.3)->setBottom(0.25);
        
        $sheet->getColumnDimension('A')->setWidth(2); 
        $sheet->getColumnDimension('B')->setWidth(5); 

        for ($col = 3; $col <= $identityCols + 1; $col++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth(20); 
        }

        $fixedWidth = 7 + (($identityCols - 1) * 20); 
        $dataColCount = $lastColIndex - ($identityCols + 1);
        
        // LEBAR KOLOM IDEAL (14.5 UNTUK STARTUP)
        if ($typeProcess === 'startup') {
            $dataWidth = 14.5;
        } else {
            $dataWidth = 12; 
            if ($dataColCount > 0) {
                $remainingWidth = 135 - $fixedWidth; 
                $dataWidth = max(10, min(25, $remainingWidth / $dataColCount));
            }
        }

        for ($col = $identityCols + 2; $col <= $lastColIndex; $col++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth($dataWidth);
        }

        if ($typeProcess !== 'startup') {
            // LOGIKA PRODUCTION 
            $totalExcelWidth = $fixedWidth + ($dataColCount * $dataWidth);
            $totalWidthPts = $totalExcelWidth * 6;
            $scaleFactor = 796 / $totalWidthPts; 
            
            $targetPageHeight = 538 / $scaleFactor; 
            $headerHeight = 160; 
            $availableDataHeight = $targetPageHeight - $headerHeight;
            
            // Multiplier 1.25 karena Kop Surat sudah diperbesar.
            // Jika lebih dari ini, Excel akan menendang baris ke-20 ke halaman berikutnya.
            $safeDataHeight = $availableDataHeight * 1.25; 
            $idealRowHeight = $safeDataHeight / $PER_PAGE;

            // Batas ideal agar seimbang dengan font 15
            $idealRowHeight = max(45, min(150, $idealRowHeight));
            
            for ($r = $tableStartRow; $r <= $lastRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight($idealRowHeight);
            }
        } else {
            // TINGGI BARIS IDEAL STARTUP
            for ($r = 1; $r <= $lastRow; $r++) {
                $currentHeight = $sheet->getRowDimension($r)->getRowHeight();
                
                // Abaikan baris Kop Surat (yang tingginya 20 atau 38)
                if ($currentHeight != 20 && $currentHeight != 38) {
                    // Gunakan 28 (maksimal 35). Jangan 80 agar 'Note' tidak tembus ke halaman 2
                    $sheet->getRowDimension($r)->setRowHeight(28); 
                }
            }
        }

        $fileName = 'Report_' . strtoupper($process ?? 'DOC') . '_' . date('Ymd_Hi') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    private function extractTableElement(string $html, string $id, string $typeProcess, string $namaApprover, array $alldata): ?array {
        $doc = new \DOMDocument(); libxml_use_internal_errors(true); $doc->loadHTML('<?xml encoding="UTF-8">' . $html); libxml_clear_errors();
        $xpath = new \DOMXPath($doc); $nodes = $xpath->query("//table[@id='{$id}']");
        $table = $nodes->length > 0 ? $nodes->item(0) : null;
        if (!$table) return null;

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

        $maxExistingCols = 0;
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
        foreach ($trs as $tr) {
            $cols = 0;
            foreach ($tr->childNodes as $td) {
                if ($td instanceof \DOMElement && in_array(strtolower($td->tagName), ['td','th'])) {
                    $cols += (int)($td->getAttribute('colspan') ?: 1);
                }
            }
            if ($cols > $maxExistingCols) $maxExistingCols = $cols;
        }

        if ($typeProcess === 'startup') {
            $dataColsCount = count($alldata);
            $identityCols = max(1, $maxExistingCols - $dataColsCount);
            
            foreach ($trs as $tr) {
                $firstCell = $tr->getElementsByTagName('td')->item(0) ?? $tr->getElementsByTagName('th')->item(0);
                if ($firstCell) {
                    $txt = strtolower(trim($firstCell->textContent));
                    if (strpos($txt, 'status approval') !== false || strpos($txt, 'operator') !== false || strpos($txt, 'approved by') !== false || strpos($txt, 'checked') !== false) {
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
                        foreach($keys as $k) if (isset($item[$k]) && $item[$k] !== '') return $item[$k];
                    } elseif (is_object($item)) {
                        foreach($keys as $k) if (isset($item->$k) && $item->$k !== '') return $item->$k;
                    }
                    return '-';
                };

                $PER_PAGE = 14;
                $totalPages = max(1, ceil($dataColsCount / $PER_PAGE));
                $targetCols = $totalPages * $PER_PAGE;

                $opTr = $doc->createElement('tr');
                $tdOpLbl = $doc->createElement('td', 'Operator');
                $tdOpLbl->setAttribute('colspan', (string)$identityCols);
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
                // Label 'Approved by' diubah menjadi 'Checked by'
                $tdAppLbl = $doc->createElement('td', 'Checked by');
                $tdAppLbl->setAttribute('colspan', (string)$identityCols);
                $appTr->appendChild($tdAppLbl);

                for ($i = 0; $i < $targetCols; $i++) {
                    if (isset($alldata[$i])) {
                        $status = strtolower(trim($getVal($alldata[$i], ['status_approval', 'status', 'is_approved', 'approval'])));
                        if (strpos($status, 'approve') !== false || strpos($status, 'bypass') !== false) {
                            $spv = $getVal($alldata[$i], ['supervisor']);
                            $ldr = $getVal($alldata[$i], ['leader']);
                            $frm = $getVal($alldata[$i], ['foreman']);
                            $appText = 'Approved';
                            if ($spv !== '-') $appText = trim($spv) . "\n(Supervisor)";
                            elseif ($ldr !== '-') $appText = trim($ldr) . "\n(Leader)";
                            elseif ($frm !== '-') $appText = trim($frm) . "\n(Foreman)";

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
            $identityCols = 3;
        }
        
        return ['table' => $table, 'identityCols' => $identityCols];
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

    private function insertKopSuratHorizontal($sheet, int $startRow, int $targetDataCols, string $namaProduk, string $judulProses, string $noDok, string $machNo, string $tglBerlaku, string $namaApprover, string $revisi, string $typeProcess, int $identityCols, int $PER_PAGE, $weeklyInfo = null) {
        $r1 = $startRow; $r2 = $startRow + 1; $r3 = $startRow + 2; $r4 = $startRow + 3;

        $lastIdentityLetter = Coordinate::stringFromColumnIndex($identityCols + 1);

        // Ukuran font khusus Kop Surat. Untuk production dibuat lebih besar.
        $kopFontSize = ($typeProcess === 'startup') ? 10 : 15; // <--- UBAH UKURAN DI SINI

        $sheet->setCellValue("B{$r1}", "PT. FOXCONN TECHNOLOGIES INDONESIA\nProduction Engineering Department\nProcess Engineering Section\n" . $namaProduk);
        $sheet->mergeCells("B{$r1}:{$lastIdentityLetter}{$r3}");
        $sheet->getStyle("B{$r1}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle("B{$r1}")->getFont()->setBold(true)->setSize($kopFontSize);

        $sheet->setCellValue("B{$r4}", "MACHINE No : " . $machNo);
        $sheet->mergeCells("B{$r4}:{$lastIdentityLetter}{$r4}");
        $sheet->getStyle("B{$r4}")->getFont()->setBold(true)->setSize($kopFontSize);
        $sheet->getStyle("B{$r1}:{$lastIdentityLetter}{$r4}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        if ($typeProcess === 'startup') {
            $totalPages = max(1, ceil($targetDataCols / $PER_PAGE));
            for ($p = 0; $p < $totalPages; $p++) {
                $chunkStartCol = ($identityCols + 2) + ($p * $PER_PAGE);
                $chunkEndCol = $chunkStartCol + $PER_PAGE - 1;
                $docStartCol = $chunkEndCol - 2;

                $this->drawCenterAndRightKopSurat($sheet, $r1, $r2, $r3, $r4, $chunkStartCol, $chunkEndCol, $docStartCol, $namaProduk, $judulProses, $noDok, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $weeklyInfo);
            }
        } else {
            $chunkStartCol = $identityCols + 2;
            $chunkEndCol = $chunkStartCol + $targetDataCols - 1;
            $docStartCol = max($chunkStartCol + 1, $chunkEndCol - 2);
            $this->drawCenterAndRightKopSurat($sheet, $r1, $r2, $r3, $r4, $chunkStartCol, $chunkEndCol, $docStartCol, $namaProduk, $judulProses, $noDok, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $weeklyInfo);
        }

        // PERBESAR KOP SURAT: Naikkan tinggi baris agar saat Fit to Width tidak terlalu pipih
        // Baris 1-3 (Identitas perusahaan, judul, info dok)
        for ($r = $r1; $r <= $r3; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(40); // Naikkan dari 20 ke 40
        }
        // Baris 4 (Machine No & TTD)
        $sheet->getRowDimension($r4)->setRowHeight(60); // Naikkan dari 38 ke 60
    }

    private function drawCenterAndRightKopSurat($sheet, $r1, $r2, $r3, $r4, $chunkStartCol, $chunkEndCol, $docStartCol, $namaProduk, $judulProses, $noDok, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $weeklyInfo = null) {
        $docStartLetter = Coordinate::stringFromColumnIndex($docStartCol);
        $docMidLetter   = Coordinate::stringFromColumnIndex($docStartCol + 1);
        $docEndLetter   = Coordinate::stringFromColumnIndex($chunkEndCol);

        $revisiStatis = "00";
        switch (strtolower($typeProcess)) {
            case 'startup': $revisiStatis = "04"; break;
            case 'production': $revisiStatis = "06"; break;
            case 'foregoing': $revisiStatis = "02"; break;
            default: $revisiStatis = $revisi; break;
        }

        $sheet->setCellValue("{$docStartLetter}{$r1}", "No. Dok / No."); $sheet->setCellValue("{$docMidLetter}{$r1}", ": " . $noDok);
        $sheet->setCellValue("{$docStartLetter}{$r2}", "Revisi");       $sheet->setCellValue("{$docMidLetter}{$r2}", ": " . $revisiStatis);
        $sheet->setCellValue("{$docStartLetter}{$r3}", "Berlaku");      $sheet->setCellValue("{$docMidLetter}{$r3}", ": " . $tglBerlaku);

        $sheet->mergeCells("{$docMidLetter}{$r1}:{$docEndLetter}{$r1}");
        $sheet->mergeCells("{$docMidLetter}{$r2}:{$docEndLetter}{$r2}");
        $sheet->mergeCells("{$docMidLetter}{$r3}:{$docEndLetter}{$r3}");

        if ($typeProcess === 'startup') {
            $qcText = "QC";
            if (!empty($weeklyInfo['status_qc']) && strtolower($weeklyInfo['status_qc']) === 'approve') {
                $approverQc = !empty($weeklyInfo['approver_qc']) ? $weeklyInfo['approver_qc'] : 'Approved';
                $dateQc = !empty($weeklyInfo['date_qc']) ? date('d-m-Y', strtotime($weeklyInfo['date_qc'])) : '';
                $qcText = "QC\n\nApproved by:\n" . $approverQc . "\n(QC)\n" . $dateQc;
            }

            $prodText = "Production";
            if (!empty($weeklyInfo['status_production']) && strtolower($weeklyInfo['status_production']) === 'approve') {
                $approverProd = !empty($weeklyInfo['approver_production']) ? $weeklyInfo['approver_production'] : 'Approved';
                $dateProd = !empty($weeklyInfo['date_production']) ? date('d-m-Y', strtotime($weeklyInfo['date_production'])) : '';
                $prodText = "Production\n\nApproved by:\n" . $approverProd . "\n(Prod)\n" . $dateProd;
            }

            $sheet->setCellValue("{$docStartLetter}{$r4}", $qcText);
            $sheet->setCellValue("{$docMidLetter}{$r4}", $prodText);
            $sheet->mergeCells("{$docMidLetter}{$r4}:{$docEndLetter}{$r4}");
            $sheet->getStyle("{$docStartLetter}{$r4}:{$docEndLetter}{$r4}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        } else {
            $ttdText = $namaApprover !== '' ? "Checked\n\nApproved by: " . $namaApprover : "Checked";
            $sheet->setCellValue("{$docStartLetter}{$r4}", $ttdText);
            $sheet->mergeCells("{$docStartLetter}{$r4}:{$docEndLetter}{$r4}");
            $sheet->getStyle("{$docStartLetter}{$r4}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        }

        $sheet->getStyle("{$docStartLetter}{$r1}:{$docStartLetter}{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $midEndLetter = Coordinate::stringFromColumnIndex(max($chunkStartCol, $docStartCol - 1));
        $centerStartLetter = Coordinate::stringFromColumnIndex($chunkStartCol);

        $sheet->setCellValue("{$centerStartLetter}{$r1}", $judulProses . "\n(" . $namaProduk . ")");
        $sheet->mergeCells("{$centerStartLetter}{$r1}:{$midEndLetter}{$r3}");
        $sheet->getStyle("{$centerStartLetter}{$r1}")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // Ukuran font judul.
        $judulFontSize = ($typeProcess === 'startup') ? 12 : 18; // <--- UBAH UKURAN DI SINI
        $sheet->getStyle("{$centerStartLetter}{$r1}")->getFont()->setBold(true)->setSize($judulFontSize)->setUnderline(true);
        $sheet->mergeCells("{$centerStartLetter}{$r4}:{$midEndLetter}{$r4}");

        $sheet->getStyle("{$centerStartLetter}{$r1}:{$docEndLetter}{$r4}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Ukuran font area kotak identitas (No Dok, Revisi, Tanda Tangan, dll)
        $detailFontSize = ($typeProcess === 'startup') ? 10 : 14; // <--- UBAH UKURAN DI SINI
        $sheet->getStyle("{$docStartLetter}{$r1}:{$docEndLetter}{$r4}")->getFont()->setSize($detailFontSize);
    }

    private function writeBlockFromGrid(array $grid, $sheet, int $startRow, int $maxGridCols, int $targetDataCols, int $identityCols, int $dataColsCount, int $PER_PAGE, int $totalPages) {
        $occupied = $grid['occupied'] ?? [];
        $origins = $grid['origins'] ?? [];
        $maxRow = $grid['totalRows'];
        
        $targetGridCols = $identityCols + $targetDataCols;

        for ($r = 1; $r <= $maxRow; $r++) {
            $targetRow = $startRow + $r - 1;

            if (isset($origins[$r])) {
                foreach ($origins[$r] as $c => $cell) {
                    if ($c > $maxGridCols) continue; 

                    $targetCol = $c + 1; 
                    $cellLetter = Coordinate::stringFromColumnIndex($targetCol);
                    $coord = $cellLetter . $targetRow;
                    
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $colspan = $cell['colspan'] ?? 1; 
                    $rowspan = $cell['rowspan'] ?? 1;

                    if ($colspan > 1 && $dataColsCount > 0 && ($c + $colspan - 1) === ($identityCols + $dataColsCount)) {
                        $headerText = html_entity_decode(strip_tags($cell['value'] ?? ''));

                        for ($p = 0; $p < $totalPages; $p++) {
                            $chunkStartCol = ($identityCols + 2) + ($p * $PER_PAGE);
                            $chunkEndCol = $chunkStartCol + $PER_PAGE - 1; 
                            
                            $chunkStartLetter = Coordinate::stringFromColumnIndex($chunkStartCol);
                            $chunkEndLetter = Coordinate::stringFromColumnIndex($chunkEndCol);
                            $chunkCoord = $chunkStartLetter . $targetRow;
                            
                            $sheet->setCellValue($chunkCoord, $headerText);
                            $sheet->mergeCells("{$chunkCoord}:{$chunkEndLetter}" . ($targetRow + $rowspan - 1));
                            
                            $style = $sheet->getStyle($chunkCoord);
                            $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                            if (isset($cell['isHeader']) && $cell['isHeader']) {
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
                        $sheet->mergeCells("{$coord}:{$endColLetter}{$endRow}");
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
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
                            if ($origin['isHeader']) { $isHeaderRow = true; break; }
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

    private function insertKopSuratVertical($sheet, $startRow, $namaProduk, $judulProses, $noDok, $machNo, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $identityCols, $PER_PAGE, $weeklyInfo = null) {
        $r1 = $startRow; $r2 = $startRow + 1; $r3 = $startRow + 2; $r4 = $startRow + 3;
        $lastIdentityLetter = Coordinate::stringFromColumnIndex($identityCols + 1);

        $sheet->setCellValue("B{$r1}", "PT. FOXCONN TECHNOLOGIES INDONESIA\nProduction Engineering Department\nProcess Engineering Section\n" . $namaProduk);
        $sheet->mergeCells("B{$r1}:{$lastIdentityLetter}{$r3}");
        $sheet->getStyle("B{$r1}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle("B{$r1}")->getFont()->setBold(true)->setSize(10);
        
        $sheet->setCellValue("B{$r4}", "MACHINE No : " . $machNo);
        $sheet->mergeCells("B{$r4}:{$lastIdentityLetter}{$r4}");
        $sheet->getStyle("B{$r4}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("B{$r1}:{$lastIdentityLetter}{$r4}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        $chunkStartCol = $identityCols + 2;
        $chunkEndCol = $chunkStartCol + $PER_PAGE - 1; 
        $docStartCol = $chunkEndCol - 2; 

        // Pakai ulang fungsi kop surat
        $this->drawCenterAndRightKopSurat($sheet, $r1, $r2, $r3, $r4, $chunkStartCol, $chunkEndCol, $docStartCol, $namaProduk, $judulProses, $noDok, $tglBerlaku, $namaApprover, $revisi, $typeProcess, $weeklyInfo);

        for ($r = $r1; $r <= $r3; $r++) $sheet->getRowDimension($r)->setRowHeight(20);
        $sheet->getRowDimension($r4)->setRowHeight(38); 
    }

    private function writeBlockVerticalChunk(array $grid, $sheet, int $startRow, int $identityCols, int $pageIndex, int $PER_PAGE) {
        $origins = $grid['origins'] ?? [];
        $maxRow = $grid['totalRows'];
        $dataColStartOffset = ($pageIndex * $PER_PAGE);

        for ($r = 1; $r <= $maxRow; $r++) {
            $targetRow = $startRow + $r - 1;
            
            // 1. Render Ulang Kolom Identitas (Pasti tercetak di tiap halaman)
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
                        $sheet->mergeCells("{$coord}:{$endColLetter}" . ($targetRow + $rowspan - 1));
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                }
            }
            
            // Cek Header Besar (Contoh: "Hasil Start Up Check") yang membentang di atas area data
            $massiveHeaderCell = null;
            if (isset($origins[$r][$identityCols + 1])) {
                $checkCell = $origins[$r][$identityCols + 1];
                if (($checkCell['isHeader'] ?? false) && ($checkCell['colspan'] ?? 1) > 2) {
                    $massiveHeaderCell = $checkCell;
                }
            }
            
            // 2. Render 14 Kolom Data 
            $dataIndex = 1;
            while ($dataIndex <= $PER_PAGE) {
                $gridColIndex = $identityCols + $dataColStartOffset + $dataIndex;
                $targetCol = $identityCols + 1 + $dataIndex;
                $coord = Coordinate::stringFromColumnIndex($targetCol) . $targetRow;
                
                // Jika ini header besar, paksa render penuh membentang di chunk ini
                if ($dataIndex == 1 && $massiveHeaderCell !== null) {
                    $cell = $massiveHeaderCell;
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $PER_PAGE - 1);
                    $rowspan = $cell['rowspan'] ?? 1;
                    if ($PER_PAGE > 1 || $rowspan > 1) {
                        $sheet->mergeCells("{$coord}:{$endColLetter}" . ($targetRow + $rowspan - 1));
                    }
                    
                    $style = $sheet->getStyle($coord);
                    $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                    if (isset($cell['isHeader']) && $cell['isHeader']) {
                        $style->getFont()->setBold(true);
                        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
                    }
                    
                    $dataIndex += $PER_PAGE; // Lompat ke akhir chunk
                    continue;
                }
                
                // Sel Data Normal
                if (isset($origins[$r][$gridColIndex])) {
                    $cell = $origins[$r][$gridColIndex];
                    $sheet->setCellValue($coord, html_entity_decode(strip_tags($cell['value'] ?? '')));
                    
                    $colspan = $cell['colspan'] ?? 1;
                    $rowspan = $cell['rowspan'] ?? 1;
                    $actualColspan = min($colspan, $PER_PAGE - $dataIndex + 1);
                    
                    if ($actualColspan > 1 || $rowspan > 1) {
                        $endColLetter = Coordinate::stringFromColumnIndex($targetCol + $actualColspan - 1);
                        $sheet->mergeCells("{$coord}:{$endColLetter}" . ($targetRow + $rowspan - 1));
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