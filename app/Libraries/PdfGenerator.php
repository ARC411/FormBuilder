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

        $typeProcess = strtolower(trim((string) ($typeProcess ?? 'production')));

        // 1. Pastikan format judul Foregoing sesuai standar "{Nama Proses} Foregoing Check Sheet"
        if ($typeProcess === 'foregoing' && !empty($judulProses)) {
            $cleanFg = trim((string) preg_replace('/^(Foregoing\s+Check\s+Sheet\s+of|Foregoing\s+Check\s+Sheet|Foregoing)\s+/i', '', (string) $judulProses));
            $cleanFg = trim((string) preg_replace('/\s*Foregoing\s*Check\s*Sheet.*$/i', '', $cleanFg));
            $judulProses = $cleanFg . ' Foregoing Check Sheet';
        }

        // 2. Ekstrak elemen tabel & terapkan aturan sanitasi DOM (sama seperti ExcelGenerator)
        $extractResult = $this->extractTableElement(
            $htmlString,
            'table1',
            $typeProcess,
            (string) ($namaApprover ?? ''),
            $alldata ?? [],
            (string) ($process ?? '')
        );

        if ($extractResult === null) {
            die("Error: Tidak ada elemen <table> yang ditemukan di view untuk dijadikan PDF!");
        }

        $tableElement     = $extractResult['table'];
        $identityColCount = $extractResult['identityCols'];

        // 3. Bangun Grid 2D dari tabel HTML
        $grid           = $this->buildGrid($tableElement);
        $headerRowCount = $this->countHeaderRows($grid);

        // 4. Siapkan HTML Kop Surat 5 baris (seragam untuk Production, Startup, dan Foregoing)
        $kopSuratHtml = $this->buildKopSuratHtml(
            (string) ($namaProduk ?? ''),
            (string) ($judulProses ?? ''),
            (string) ($noDok ?? ''),
            (string) ($machNo ?? ''),
            (string) ($tglBerlaku ?? ''),
            (string) ($namaApprover ?? ''),
            (string) ($revisi ?? ''),
            $typeProcess,
            $weeklyInfo ?? null
        );

        // 5. Bangun halaman per bagian (Startup = 14 kolom/halaman, Production & Foregoing = 20 baris/halaman)
        $pagesHtml = '';

        if ($typeProcess === 'startup') {
            $perPageCols   = 14;
            $dataColsCount = count($alldata ?? []);
            $totalPages    = max(1, (int) ceil($dataColsCount / $perPageCols));

            // Cari batas baris akhir yang sah (baris 'Note') agar baris sampah di bawahnya tidak ikut tercetak
            $validEndRow = $grid['totalRows'];
            for ($r = $grid['totalRows']; $r >= 1; $r--) {
                $firstVal = strtolower(trim((string) ($grid['origins'][$r][1]['value'] ?? '')));
                if (strpos($firstVal, 'note') !== false) {
                    $validEndRow = $r;
                    break;
                }
            }

            for ($p = 0; $p < $totalPages; $p++) {
                $startCol  = $identityColCount + 1 + ($p * $perPageCols);
                $endCol    = $startCol + $perPageCols - 1;
                $breakCls  = ($p < $totalPages - 1) ? ' page-break' : '';
                $tableHtml = $this->buildStartupChunkTableHtml(
                    $grid,
                    $identityColCount,
                    [$startCol, $endCol],
                    $headerRowCount,
                    $validEndRow
                );
                $pagesHtml .= '<div class="page-chunk' . $breakCls . '">' . $kopSuratHtml . $tableHtml . '</div>';
            }
        } else {
            $perPageRows  = 20;
            $dataRowCount = max(0, $grid['totalRows'] - $headerRowCount);
            $totalPages   = max(1, (int) ceil($dataRowCount / $perPageRows));

            for ($p = 0; $p < $totalPages; $p++) {
                $startDataRow = $headerRowCount + 1 + ($p * $perPageRows);
                $endDataRow   = min($grid['totalRows'], $startDataRow + $perPageRows - 1);
                $breakCls     = ($p < $totalPages - 1) ? ' page-break' : '';
                $tableHtml    = $this->buildHorizontalRowChunkTableHtml(
                    $grid,
                    $headerRowCount,
                    $startDataRow,
                    $endDataRow,
                    $perPageRows,
                    $typeProcess
                );
                $pagesHtml .= '<div class="page-chunk' . $breakCls . '">' . $kopSuratHtml . $tableHtml . '</div>';
            }
        }

        $customCSS = $this->buildStylesheet($typeProcess);
        $finalHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8">'
            . $customCSS
            . '</head><body>'
            . $pagesHtml
            . '</body></html>';

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

        $procName = isset($process) ? strtoupper((string) $process) : 'REPORT';
        $fileName = 'Report_' . $procName . '_' . date('Ymd_Hi') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit();
    }

    /**
     * CSS terkalibrasi untuk A4 Landscape (841.89pt x 595.28pt) agar setiap chunk pas 1 halaman penuh
     */
    private function buildStylesheet(string $typeProcess): string
    {
        $dataFontSize = ($typeProcess === 'startup') ? '7.2pt' : (($typeProcess === 'foregoing') ? '8.5pt' : '9pt');
        $hdrFontSize  = ($typeProcess === 'startup') ? '7.2pt' : '7.2pt';

        return '
        <style>
            @page {
                size: A4 landscape;
                margin: 12pt 14pt 12pt 14pt;
            }
            * {
                box-sizing: border-box;
            }
            body {
                font-family: Arial, Helvetica, sans-serif;
                margin: 0;
                padding: 0;
                color: #000;
            }
            .page-chunk {
                width: 100%;
            }
            .page-break {
                page-break-after: always;
            }
            /* Kop Surat 5 Baris Berbingkai */
            table.kop-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                margin-bottom: 4pt;
                border: 1pt solid #000;
            }
            table.kop-table td {
                border: 0.75pt solid #000;
                padding: 1.5pt 4pt;
                vertical-align: middle;
                font-size: 8.5pt;
                line-height: 1.15;
            }
            table.kop-table td.kop-left {
                font-weight: bold;
                font-size: 8.5pt;
                text-align: left;
                vertical-align: middle;
            }
            table.kop-table td.kop-machine {
                font-weight: bold;
                font-size: 8.5pt;
                text-align: left;
                vertical-align: middle;
            }
            table.kop-table td.kop-center {
                text-align: center;
                vertical-align: middle;
            }
            .kop-title {
                font-size: 13pt;
                font-weight: bold;
                text-decoration: underline;
            }
            .kop-subtitle {
                font-size: 11pt;
                font-weight: bold;
            }
            table.kop-table td.kop-info-lbl {
                font-weight: bold;
                font-size: 8pt;
                text-align: left;
                white-space: nowrap;
            }
            table.kop-table td.kop-info-val {
                font-weight: bold;
                font-size: 8pt;
                text-align: left;
                white-space: nowrap;
            }
            table.kop-table td.kop-ttd-hdr {
                font-weight: bold;
                font-size: 8pt;
                text-align: center;
                background-color: #f2f2f2;
            }
            table.kop-table td.kop-ttd-box {
                font-weight: bold;
                font-size: 7pt;
                text-align: center;
                vertical-align: middle;
                line-height: 1.1;
            }
            /* Tabel Checksheet Utama */
            table.table-cs {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                border: 1pt solid #000;
            }
            table.table-cs th,
            table.table-cs td {
                border: 0.75pt solid #000;
                text-align: center;
                vertical-align: middle;
                word-wrap: break-word;
                overflow: hidden;
            }
            table.table-cs th {
                background-color: #f2f2f2;
                font-weight: bold;
                font-size: ' . $hdrFontSize . ';
                padding: 1pt 1.5pt;
                line-height: 1.08;
            }
            table.table-cs td {
                font-size: ' . $dataFontSize . ';
                padding: 1pt 1.5pt;
                line-height: 1.1;
            }
            table.table-cs td.startup-id-cell {
                font-size: 7.2pt;
                padding: 1pt 2.5pt;
            }
            tr {
                page-break-inside: avoid;
            }
        </style>';
    }

    /**
     * Ekstrak elemen tabel dari HTML view, hapus baris judul ganda, dan terapkan aturan Startup/Production
     */
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
        $table = $xpath->query("//table[@id='" . $id . "']")->item(0);
        if ($table === null) {
            $table = $xpath->query("//table")->item(0);
        }
        if (!($table instanceof \DOMElement)) {
            return null;
        }

        // Hapus baris judul utama di dalam <thead> pertama karena sudah ada di Kop Surat
        $theads = $table->getElementsByTagName('thead');
        if ($theads->length > 0) {
            $firstThead = $theads->item(0);
            $firstTr    = $firstThead->getElementsByTagName('tr')->item(0);
            if ($firstTr) {
                $firstCell = $firstTr->getElementsByTagName('th')->item(0);
                if ($firstCell && $firstCell->hasAttribute('colspan')) {
                    $firstTr->parentNode->removeChild($firstTr);
                }
            }
        }

        // Aturan Poin 1 Production: Jika Operator Finish kosong, ubah jam Out "07:00" menjadi "-"
        if ($typeProcess === 'production') {
            $this->sanitizeProductionDom($table, $alldata);
        }

        $maxExistingCols = 0;
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
        foreach ($trs as $tr) {
            $cols = 0;
            foreach ($tr->childNodes as $td) {
                if ($td instanceof \DOMElement && in_array(strtolower($td->tagName), ['td', 'th'])) {
                    $cols += max(1, (int) ($td->getAttribute('colspan') ?: 1));
                }
            }
            if ($cols > $maxExistingCols) {
                $maxExistingCols = $cols;
            }
        }

        if ($typeProcess === 'startup') {
            $dataColsCount = count($alldata);
            $identityCols  = max(1, $maxExistingCols - $dataColsCount);
            $this->injectStartupMetaRows($doc, $table, $alldata, $identityCols);
        } else {
            $identityCols = 4;
        }

        return [
            'table'        => $table,
            'identityCols' => $identityCols,
        ];
    }

    /**
     * Sisipkan baris Operator (by EmpID) dan Checked by (by EmpID + Role) sebelum baris Note pada Startup
     */
    private function injectStartupMetaRows(
        \DOMDocument $doc,
        \DOMElement $table,
        array $alldata,
        int $identityCols
    ): void {
        $trs = iterator_to_array($table->getElementsByTagName('tr'));
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

        $trs    = $table->getElementsByTagName('tr');
        $noteTr = null;
        foreach ($trs as $tr) {
            if (stripos($tr->textContent, 'Note') !== false) {
                $noteTr = $tr;
                break;
            }
        }

        if (!$noteTr) {
            return;
        }

        $getVal = function ($item, array $keys) {
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

        $perPage       = 14;
        $dataColsCount = count($alldata);
        $totalPages    = max(1, (int) ceil($dataColsCount / $perPage));
        $targetCols    = $totalPages * $perPage;

        // 1. Baris Operator (Menggunakan EmpID)
        $opTr    = $doc->createElement('tr');
        $tdOpLbl = $doc->createElement('td', 'Operator');
        $tdOpLbl->setAttribute('colspan', (string) $identityCols);
        $opTr->appendChild($tdOpLbl);

        for ($i = 0; $i < $targetCols; $i++) {
            if (isset($alldata[$i])) {
                $opEmpId  = $getVal($alldata[$i], ['empid', 'operator', 'created_by', 'name']);
                $tdOpData = $doc->createElement('td', htmlspecialchars((string) $opEmpId));
            } else {
                $tdOpData = $doc->createElement('td', '-');
            }
            $opTr->appendChild($tdOpData);
        }
        $noteTr->parentNode->insertBefore($opTr, $noteTr);

        // 2. Baris Checked by (Menggunakan EmpID Approver + Jabatan)
        $appTr    = $doc->createElement('tr');
        $tdAppLbl = $doc->createElement('td', 'Checked by');
        $tdAppLbl->setAttribute('colspan', (string) $identityCols);
        $appTr->appendChild($tdAppLbl);

        for ($i = 0; $i < $targetCols; $i++) {
            if (isset($alldata[$i])) {
                $rawStatus  = (string) $getVal($alldata[$i], ['status', 'status_approval', 'is_approved', 'approval']);
                $status     = strtolower(trim($rawStatus));
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
                        $appText = trim((string) $spv) . "\n(Supervisor)";
                    } elseif ($ldr !== '-') {
                        $appText = trim((string) $ldr) . "\n(Leader)";
                    } elseif ($frm !== '-') {
                        $appText = trim((string) $frm) . "\n(Foreman)";
                    } else {
                        $cleanStatus = preg_replace('/^(approved?|check(ed)?)\s*(by\s*)?/i', '', trim($rawStatus));
                        $appText     = !empty($cleanStatus) ? trim((string) $cleanStatus) : 'Approved';
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

    /**
     * Bersihkan sel "07:00" pada kolom Time Out Production jika Operator Finish kosong
     */
    private function sanitizeProductionDom(\DOMElement $table, array $alldata): void
    {
        $tbodies = $table->getElementsByTagName('tbody');
        if ($tbodies->length === 0) {
            return;
        }
        $tbody    = $tbodies->item(0);
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
            $emp2       = trim((string) ($rowData['empid2'] ?? ''));
            $name2      = trim((string) ($rowData['name2'] ?? ''));
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

    /**
     * Bangun struktur Grid 2D dengan mempertahankan baris baru (\n)
     */
    private function buildGrid(\DOMElement $table): array
    {
        $occupied   = [];
        $origins    = [];
        $currentRow = 1;
        $maxCol     = 0;

        foreach ($table->getElementsByTagName('tr') as $tr) {
            $col     = 1;
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

                $rawText = html_entity_decode(strip_tags($cell->textContent ?? ''), ENT_QUOTES, 'UTF-8');
                $lines   = preg_split('/\r\n|\r|\n/', $rawText);
                $cleanLines = [];
                foreach ($lines as $ln) {
                    $trimmed = trim((string) preg_replace('/[ \t]+/', ' ', $ln));
                    if ($trimmed !== '') {
                        $cleanLines[] = $trimmed;
                    }
                }
                $textContent = implode("\n", $cleanLines);
                $isHeader    = ($tag === 'th') || $inThead;

                $origins[$currentRow][$col] = [
                    'value'    => $textContent,
                    'isHeader' => $isHeader,
                    'colspan'  => $colspan,
                    'rowspan'  => $rowspan,
                ];

                for ($r = $currentRow; $r < $currentRow + $rowspan; $r++) {
                    for ($c = $col; $c < $col + $colspan; $c++) {
                        $occupied[$r][$c] = true;
                    }
                }
                $maxCol = max($maxCol, $col + $colspan - 1);
                $col   += $colspan;
            }
            $currentRow++;
        }

        return [
            'origins'   => $origins,
            'occupied'  => $occupied,
            'totalRows' => $currentRow - 1,
            'totalCols' => $maxCol,
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

    /**
     * Hitung estimasi jumlah baris teks setelah word-wrap (untuk tinggi baris dinamis Startup)
     */
    private function countWrappedLines(string $text, float $colWidthUnits, float $charsPerUnit = 0.88): int
    {
        $text = trim($text);
        if ($text === '' || $text === '-') {
            return 1;
        }

        $effChars   = max(1.0, ($colWidthUnits - 1.2) * $charsPerUnit);
        $totalLines = 0;
        $paragraphs = preg_split('/\r\n|\r|\n/', $text);

        foreach ($paragraphs as $para) {
            $para = trim((string) preg_replace('/[ \t]+/', ' ', $para));
            if ($para === '') {
                $totalLines += 1;
                continue;
            }

            $words   = explode(' ', $para);
            $currLen = 0;
            $pLines  = 1;

            foreach ($words as $w) {
                $wLen = mb_strlen($w);
                if ($currLen === 0) {
                    $currLen = $wLen;
                    if ($currLen > $effChars) {
                        $extra   = (int) ceil($currLen / $effChars) - 1;
                        $pLines += $extra;
                        $currLen = $currLen % max(1, (int) $effChars);
                    }
                } elseif (($currLen + 1 + $wLen) <= $effChars) {
                    $currLen += 1 + $wLen;
                } else {
                    $pLines++;
                    $currLen = $wLen;
                    if ($currLen > $effChars) {
                        $extra   = (int) ceil($currLen / $effChars) - 1;
                        $pLines += $extra;
                        $currLen = $currLen % max(1, (int) $effChars);
                    }
                }
            }

            $totalLines += $pLines;
        }

        return max(1, $totalLines);
    }

    /**
     * Render tabel Startup per chunk (Kolom Identitas + 14 Kolom Data) dengan tinggi baris dinamis
     */
    private function buildStartupChunkTableHtml(
        array $grid,
        int $identityColCount,
        array $colRange,
        int $headerRowCount,
        int $validEndRow
    ): string {
        [$startCol, $endCol] = $colRange;
        $perPageCols = $endCol - $startCol + 1;

        // Bobot lebar kolom Startup persis seperti ExcelGenerator
        $colWeights = [];
        $colWeights[1] = 5.0; // No
        if ($identityColCount === 4) {
            $colWeights[2] = 22.0; // Start Up Check Item
            $colWeights[3] = 15.0; // Sub Item
            $colWeights[4] = 30.0; // Standard
        } elseif ($identityColCount === 3) {
            $colWeights[2] = 28.0;
            $colWeights[3] = 30.0;
        } else {
            for ($c = 2; $c <= $identityColCount; $c++) {
                $colWeights[$c] = ($c === $identityColCount) ? 30.0 : 20.0;
            }
        }
        for ($d = 1; $d <= $perPageCols; $d++) {
            $colWeights[$identityColCount + $d] = 14.5;
        }

        $totalWeight = array_sum($colWeights);
        $colgroup    = '<colgroup>';
        foreach ($colWeights as $w) {
            $pct       = round(($w / $totalWeight) * 100, 3);
            $colgroup .= '<col style="width: ' . $pct . '%;">';
        }
        $colgroup .= '</colgroup>';

        // Hitung tinggi baris dinamis agar kalimat panjang tidak terpotong & muat 1 halaman A4 Landscape (anggaran 465pt)
        $linePt    = 8.2;
        $padPt     = 4.0;
        $minRowPts = [];
        for ($r = 1; $r <= $validEndRow; $r++) {
            $minRowPts[$r] = 11.5;
        }

        for ($r = 1; $r <= $validEndRow; $r++) {
            for ($c = 1; $c <= $identityColCount; $c++) {
                if (!isset($grid['origins'][$r][$c])) {
                    continue;
                }
                $cell = $grid['origins'][$r][$c];
                $rs   = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs   = min((int) ($cell['colspan'] ?? 1), $identityColCount - $c + 1);
                if ($rs === 1) {
                    $wSum = 0.0;
                    for ($cc = $c; $cc < $c + $cs; $cc++) {
                        $wSum += ($colWeights[$cc] ?? 14.5);
                    }
                    $lines  = $this->countWrappedLines((string) ($cell['value'] ?? ''), $wSum);
                    $needed = ceil($lines * $linePt + $padPt);
                    if ($needed > $minRowPts[$r]) {
                        $minRowPts[$r] = $needed;
                    }
                }
            }

            for ($d = 1; $d <= $perPageCols; $d++) {
                $gridCol = $startCol + $d - 1;
                if (!isset($grid['origins'][$r][$gridCol])) {
                    continue;
                }
                $cell = $grid['origins'][$r][$gridCol];
                $rs   = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs   = min((int) ($cell['colspan'] ?? 1), $perPageCols - $d + 1);
                if ($rs === 1) {
                    $wSum  = $cs * 14.5;
                    $lines = $this->countWrappedLines((string) ($cell['value'] ?? ''), $wSum);
                    $needed = ceil($lines * $linePt + $padPt);
                    if ($needed > $minRowPts[$r]) {
                        $minRowPts[$r] = $needed;
                    }
                }
            }
        }

        // Cek sel gabungan multi-baris (rowspan > 1)
        for ($r = 1; $r <= $validEndRow; $r++) {
            for ($c = 1; $c <= $identityColCount; $c++) {
                if (!isset($grid['origins'][$r][$c])) {
                    continue;
                }
                $cell = $grid['origins'][$r][$c];
                $rs   = max(1, (int) ($cell['rowspan'] ?? 1));
                $cs   = min((int) ($cell['colspan'] ?? 1), $identityColCount - $c + 1);
                if ($rs > 1) {
                    $endR     = min($validEndRow, $r + $rs - 1);
                    $actualRs = max(1, $endR - $r + 1);
                    $wSum     = 0.0;
                    for ($cc = $c; $cc < $c + $cs; $cc++) {
                        $wSum += ($colWeights[$cc] ?? 14.5);
                    }
                    $lines       = $this->countWrappedLines((string) ($cell['value'] ?? ''), $wSum);
                    $neededTotal = ceil($lines * $linePt + $padPt);
                    $currTotal   = 0.0;
                    for ($rr = $r; $rr <= $endR; $rr++) {
                        $currTotal += $minRowPts[$rr];
                    }
                    if ($neededTotal > $currTotal) {
                        $add = ceil(($neededTotal - $currTotal) / $actualRs);
                        for ($rr = $r; $rr <= $endR; $rr++) {
                            $minRowPts[$rr] += $add;
                        }
                    }
                }
            }
        }

        $targetBudgetPt = 465.0;
        $rawSumPt       = array_sum($minRowPts);
        $finalRowPts    = [];
        for ($r = 1; $r <= $validEndRow; $r++) {
            if ($rawSumPt < $targetBudgetPt) {
                $extra           = ($targetBudgetPt - $rawSumPt) / max(1, $validEndRow);
                $finalRowPts[$r] = round($minRowPts[$r] + $extra, 1);
            } else {
                $scale           = $targetBudgetPt / max(1.0, $rawSumPt);
                $finalRowPts[$r] = round(max(10.0, $minRowPts[$r] * $scale), 1);
            }
        }

        $html = '<table class="table-cs">' . $colgroup . '<thead>';

        for ($r = 1; $r <= $validEndRow; $r++) {
            if ($r === $headerRowCount + 1) {
                $html .= '</thead><tbody>';
            }
            $rowH  = $finalRowPts[$r] ?? 14.0;
            $html .= '<tr style="height: ' . $rowH . 'pt;">';

            // 1. Render Kolom Identitas (1 .. $identityColCount)
            for ($c = 1; $c <= $identityColCount; $c++) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell       = $grid['origins'][$r][$c];
                    $tag        = !empty($cell['isHeader']) ? 'th' : 'td';
                    $cls        = ($tag === 'td') ? ' class="startup-id-cell"' : '';
                    $cs         = min((int) ($cell['colspan'] ?? 1), $identityColCount - $c + 1);
                    $rs         = min((int) ($cell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $csAttr     = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr     = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $valEsc     = nl2br(htmlspecialchars((string) ($cell['value'] ?? ''), ENT_QUOTES, 'UTF-8'));
                    $html      .= "<{$tag}{$cls}{$rsAttr}{$csAttr}>{$valEsc}</{$tag}>";
                }
            }

            // Cek apakah baris ini memiliki Header Besar (misal "Hasil Start Up Check") di atas kolom data
            $massiveHeaderCell = null;
            if (isset($grid['origins'][$r][$identityColCount + 1])) {
                $checkCell = $grid['origins'][$r][$identityColCount + 1];
                if (!empty($checkCell['isHeader']) && ((int) ($checkCell['colspan'] ?? 1)) > 2) {
                    $massiveHeaderCell = $checkCell;
                }
            }

            // 2. Render 14 Kolom Data
            $dIdx = 1;
            while ($dIdx <= $perPageCols) {
                $gridCol = $startCol + $dIdx - 1;

                if ($dIdx === 1 && $massiveHeaderCell !== null) {
                    $rs     = min((int) ($massiveHeaderCell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $rsAttr = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $csAttr = ($perPageCols > 1) ? ' colspan="' . $perPageCols . '"' : '';
                    $valEsc = nl2br(htmlspecialchars((string) ($massiveHeaderCell['value'] ?? ''), ENT_QUOTES, 'UTF-8'));
                    $html  .= "<th{$rsAttr}{$csAttr}>{$valEsc}</th>";
                    $dIdx  += $perPageCols;
                    continue;
                }

                if (isset($grid['origins'][$r][$gridCol])) {
                    $cell      = $grid['origins'][$r][$gridCol];
                    $tag       = !empty($cell['isHeader']) ? 'th' : 'td';
                    $cs        = min((int) ($cell['colspan'] ?? 1), $perPageCols - $dIdx + 1);
                    $rs        = min((int) ($cell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $csAttr    = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr    = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $rawVal    = (string) ($cell['value'] ?? '');
                    $valEsc    = ($rawVal !== '') ? nl2br(htmlspecialchars($rawVal, ENT_QUOTES, 'UTF-8')) : '-';
                    $html     .= "<{$tag}{$rsAttr}{$csAttr}>{$valEsc}</{$tag}>";
                    $dIdx     += $cs;
                } else {
                    if (empty($grid['occupied'][$r][$gridCol])) {
                        $html .= '<td>-</td>';
                    }
                    $dIdx++;
                }
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Render tabel Production / Foregoing per 20 baris data + Dummy Rows & proporsi kolom Excel
     */
    private function buildHorizontalRowChunkTableHtml(
        array $grid,
        int $headerRowCount,
        int $startDataRow,
        int $endDataRow,
        int $perPageRows,
        string $typeProcess
    ): string {
        $totalCols = $grid['totalCols'];

        // Hitung bobot lebar kolom sesuai spesifikasi ExcelGenerator
        $colWeights = [];
        for ($col = 1; $col <= $totalCols; $col++) {
            $headerLabel = '';
            for ($hr = 1; $hr <= $headerRowCount; $hr++) {
                if (isset($grid['origins'][$hr][$col])) {
                    $hCell = $grid['origins'][$hr][$col];
                    if (((int) ($hCell['colspan'] ?? 1)) === 1) {
                        $headerLabel .= ' ' . strtolower(trim((string) ($hCell['value'] ?? '')));
                    }
                }
            }
            $headerLabel = trim((string) preg_replace('/\s+/', ' ', $headerLabel));

            if ($typeProcess === 'foregoing') {
                $w = 6.5;
                if ($col === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $w = 4.5;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $w = 11.20;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $w = 15.70;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $w = 14.0;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $w = 12.0;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $w = 11.70;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $w = 11.10;
                } elseif (strpos($headerLabel, 'pos') !== false) {
                    $w = 6.5;
                } elseif (strpos($headerLabel, 'judge') !== false || strpos($headerLabel, 'ok/ng') !== false) {
                    $w = 5.8;
                }
            } else {
                if ($col === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $w = 4.5;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $w = 14.0;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $w = 12.55;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $w = 10.10;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $w = 15.5;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $w = 14.10;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $w = 14.55;
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
                    $w = 6.2;
                } else {
                    $w = 5.2;
                }
            }
            $colWeights[$col] = $w;
        }

        $totalWeight = max(1.0, array_sum($colWeights));
        $colgroup    = '<colgroup>';
        foreach ($colWeights as $w) {
            $pct       = round(($w / $totalWeight) * 100, 3);
            $colgroup .= '<col style="width: ' . $pct . '%;">';
        }
        $colgroup .= '</colgroup>';

        $hdrRowHeightPt  = 13.5;
        $dataRowHeightPt = ($typeProcess === 'foregoing') ? 21.0 : 20.5;

        $html = '<table class="table-cs">' . $colgroup . '<thead>';

        // 1. Render Baris Header (1 .. $headerRowCount)
        for ($r = 1; $r <= $headerRowCount; $r++) {
            $html .= '<tr style="height: ' . $hdrRowHeightPt . 'pt;">';
            for ($c = 1; $c <= $totalCols; $c++) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell   = $grid['origins'][$r][$c];
                    $cs     = min((int) ($cell['colspan'] ?? 1), $totalCols - $c + 1);
                    $rs     = min((int) ($cell['rowspan'] ?? 1), $headerRowCount - $r + 1);
                    $csAttr = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $valEsc = nl2br(htmlspecialchars((string) ($cell['value'] ?? ''), ENT_QUOTES, 'UTF-8'));
                    $html  .= "<th{$rsAttr}{$csAttr}>{$valEsc}</th>";
                } elseif (empty($grid['occupied'][$r][$c])) {
                    $html .= '<th></th>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</thead><tbody>';

        // 2. Render Baris Data pada Chunk Ini
        $actualRowsRendered = 0;
        if ($startDataRow <= $endDataRow && $startDataRow <= $grid['totalRows']) {
            for ($r = $startDataRow; $r <= $endDataRow; $r++) {
                $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                for ($c = 1; $c <= $totalCols; $c++) {
                    if (isset($grid['origins'][$r][$c])) {
                        $cell   = $grid['origins'][$r][$c];
                        $cs     = min((int) ($cell['colspan'] ?? 1), $totalCols - $c + 1);
                        $rs     = min((int) ($cell['rowspan'] ?? 1), $endDataRow - $r + 1);
                        $csAttr = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                        $rsAttr = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                        $rawVal = (string) ($cell['value'] ?? '');
                        $valEsc = ($rawVal !== '') ? nl2br(htmlspecialchars($rawVal, ENT_QUOTES, 'UTF-8')) : '-';
                        $html  .= "<td{$rsAttr}{$csAttr}>{$valEsc}</td>";
                    } elseif (empty($grid['occupied'][$r][$c])) {
                        $html .= '<td>-</td>';
                    }
                }
                $html .= '</tr>';
                $actualRowsRendered++;
            }
        }

        // 3. Tambahkan Dummy Rows hingga genap 20 baris per halaman
        $dummyNeeded = max(0, $perPageRows - $actualRowsRendered);
        if ($dummyNeeded > 0) {
            if ($typeProcess === 'foregoing') {
                // Dummy rows Foregoing dibuat per blok 4 baris (X, Y, Z, θ) dengan rowspan=4 pada 7 kolom identitas lot
                $posCycle = ['X', 'Y', 'Z', 'θ'];
                $d        = 0;
                while ($d < $dummyNeeded) {
                    $blockSize = min(4, $dummyNeeded - $d);
                    for ($s = 0; $s < $blockSize; $s++) {
                        $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                        if ($s === 0) {
                            $rsAttr = ($blockSize > 1) ? ' rowspan="' . $blockSize . '"' : '';
                            for ($c = 1; $c <= min(7, $totalCols); $c++) {
                                $html .= "<td{$rsAttr}>-</td>";
                            }
                        }
                        if ($totalCols >= 8) {
                            $posLbl = htmlspecialchars($posCycle[$s] ?? '-', ENT_QUOTES, 'UTF-8');
                            $html  .= "<td>{$posLbl}</td>";
                        }
                        for ($c = 9; $c <= $totalCols; $c++) {
                            $html .= '<td>-</td>';
                        }
                        $html .= '</tr>';
                    }
                    $d += $blockSize;
                }
            } else {
                for ($d = 1; $d <= $dummyNeeded; $d++) {
                    $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                    for ($c = 1; $c <= $totalCols; $c++) {
                        $html .= '<td>-</td>';
                    }
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Bangun Kop Surat 5 Baris Berbingkai (Seragam untuk Production, Startup, dan Foregoing)
     */
    private function buildKopSuratHtml(
        string $namaProduk,
        string $judulProses,
        string $noDok,
        string $machNo,
        string $tglBerlaku,
        string $namaApprover,
        string $revisi,
        string $typeProcess,
        $weeklyInfo = null
    ): string {
        $docNoClean  = $noDok;
        $revisiClean = !empty($revisi) ? $revisi : '00';
        if (preg_match('/^(.*?)-(\d+)$/', $noDok, $m)) {
            $docNoClean  = $m[1];
            $revisiClean = $m[2];
        }

        $ptBlockHtml = 'PT. FOXCONN TECHNOLOGIES INDONESIA<br>'
            . 'Production Engineering Department<br>'
            . 'Process Engineering Section<br>'
            . htmlspecialchars($namaProduk, ENT_QUOTES, 'UTF-8');

        $titleHtml = '<div class="kop-title">' . htmlspecialchars($judulProses, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div class="kop-subtitle">(' . htmlspecialchars($namaProduk, ENT_QUOTES, 'UTF-8') . ')</div>';

        $machHtml = 'MACHINE No : ' . htmlspecialchars($machNo, ENT_QUOTES, 'UTF-8');

        if ($typeProcess === 'startup') {
            $qcBox = '';
            if (!empty($weeklyInfo['status_qc']) && strtolower((string) $weeklyInfo['status_qc']) === 'approve') {
                $approverQc = htmlspecialchars(!empty($weeklyInfo['approver_qc']) ? (string) $weeklyInfo['approver_qc'] : 'Approved', ENT_QUOTES, 'UTF-8');
                $dateQc     = !empty($weeklyInfo['date_qc']) ? date('d-m-Y', strtotime((string) $weeklyInfo['date_qc'])) : '';
                $qcBox      = 'Approved by:<br>' . $approverQc . '<br>(QC)<br>' . $dateQc;
            }

            $prodBox = '';
            if (!empty($weeklyInfo['status_production']) && strtolower((string) $weeklyInfo['status_production']) === 'approve') {
                $approverProd = htmlspecialchars(!empty($weeklyInfo['approver_production']) ? (string) $weeklyInfo['approver_production'] : 'Approved', ENT_QUOTES, 'UTF-8');
                $dateProd     = !empty($weeklyInfo['date_production']) ? date('d-m-Y', strtotime((string) $weeklyInfo['date_production'])) : '';
                $prodBox      = 'Approved by:<br>' . $approverProd . '<br>(Prod)<br>' . $dateProd;
            }

            return '
            <table class="kop-table">
                <colgroup>
                    <col style="width: 26%;">
                    <col style="width: 52%;">
                    <col style="width: 9%;">
                    <col style="width: 13%;">
                </colgroup>
                <tr style="height: 11pt;">
                    <td rowspan="4" class="kop-left">' . $ptBlockHtml . '</td>
                    <td rowspan="4" class="kop-center">' . $titleHtml . '</td>
                    <td class="kop-info-lbl">Doc No.</td>
                    <td class="kop-info-val">: ' . htmlspecialchars($docNoClean, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 11pt;">
                    <td class="kop-info-lbl">Revisi</td>
                    <td class="kop-info-val">: ' . htmlspecialchars((string) $revisiClean, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 11pt;">
                    <td class="kop-info-lbl">Berlaku</td>
                    <td class="kop-info-val">: ' . htmlspecialchars($tglBerlaku, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 11pt;">
                    <td class="kop-ttd-hdr">QC</td>
                    <td class="kop-ttd-hdr">Production</td>
                </tr>
                <tr style="height: 28pt;">
                    <td class="kop-machine">' . $machHtml . '</td>
                    <td></td>
                    <td class="kop-ttd-box">' . $qcBox . '</td>
                    <td class="kop-ttd-box">' . $prodBox . '</td>
                </tr>
            </table>';
        }

        // Production & Foregoing (Lengkap dengan baris Revisi & Kotak Checked / Approved by)
        $ttdHtml = '';
        if (trim($namaApprover) !== '' && trim($namaApprover) !== '-') {
            $ttdHtml = 'Approved by:<br>' . nl2br(htmlspecialchars(trim($namaApprover), ENT_QUOTES, 'UTF-8'));
        }

        return '
        <table class="kop-table">
            <colgroup>
                <col style="width: 24%;">
                <col style="width: 54%;">
                <col style="width: 9%;">
                <col style="width: 13%;">
            </colgroup>
            <tr style="height: 11pt;">
                <td rowspan="4" class="kop-left">' . $ptBlockHtml . '</td>
                <td rowspan="4" class="kop-center">' . $titleHtml . '</td>
                <td class="kop-info-lbl">No. Dok / No.</td>
                <td class="kop-info-val">: ' . htmlspecialchars($docNoClean, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 11pt;">
                <td class="kop-info-lbl">Revisi</td>
                <td class="kop-info-val">: ' . htmlspecialchars((string) $revisiClean, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 11pt;">
                <td class="kop-info-lbl">Berlaku</td>
                <td class="kop-info-val">: ' . htmlspecialchars($tglBerlaku, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 11pt;">
                <td colspan="2" class="kop-ttd-hdr">Checked</td>
            </tr>
            <tr style="height: 28pt;">
                <td class="kop-machine">' . $machHtml . '</td>
                <td></td>
                <td colspan="2" class="kop-ttd-box">' . $ttdHtml . '</td>
            </tr>
        </table>';
    }
}