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

        // 2. Ekstrak elemen tabel & terapkan aturan sanitasi DOM
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

        // 4. Siapkan HTML Kop Surat yang ringkas & proporsional
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

            // Cari batas baris akhir yang sah (baris 'Note')
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
        $finalHtml = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">'
            . $customCSS
            . '</head><body>'
            . $pagesHtml
            . '</body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($finalHtml, 'UTF-8');
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
     * CSS terkalibrasi untuk A4 Landscape (841.89pt x 595.28pt)
     * Font & padding Production/Foregoing diperbesar agar memenuhi tinggi kertas secara proporsional.
     */
    private function buildStylesheet(string $typeProcess): string
    {
        if ($typeProcess === 'startup') {
            $hdrFontSize  = '6.2pt';
            $dataFontSize = '6.0pt';
            $cellPadding  = '1.1pt 1.8pt';
            $nowrapRule   = '';
        } elseif ($typeProcess === 'foregoing') {
            $hdrFontSize  = '6.5pt';
            $dataFontSize = '6.8pt';
            $cellPadding  = '2.2pt 1pt';
            $nowrapRule   = 'white-space: nowrap;';
        } else {
            // Production
            $hdrFontSize  = '6.8pt';
            $dataFontSize = '7.6pt';
            $cellPadding  = '2.5pt 1.2pt';
            $nowrapRule   = 'white-space: nowrap;';
        }

        return '
        <style>
            @page {
                size: A4 landscape;
                margin: 10pt 12pt 10pt 12pt;
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
            .sym {
                font-family: "DejaVu Sans", sans-serif;
            }
            .page-chunk {
                width: 100%;
                page-break-inside: avoid;
            }
            .page-break {
                page-break-after: always;
            }
            /* Kop Surat Ringkas & Proporsional (Kiri 23%, Tengah 60%, Kanan 17%) */
            table.kop-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                margin-bottom: 3pt;
                border: 0.8pt solid #000;
            }
            table.kop-table td {
                border: 0.6pt solid #000;
                padding: 1pt 3pt;
                vertical-align: middle;
                font-size: 6.5pt;
                line-height: 1.1;
            }
            table.kop-table td.kop-left {
                font-weight: bold;
                font-size: 6.5pt;
                text-align: left;
                vertical-align: middle;
            }
            table.kop-table td.kop-machine {
                font-weight: bold;
                font-size: 6.5pt;
                text-align: left;
                vertical-align: middle;
                white-space: nowrap;
            }
            table.kop-table td.kop-center {
                text-align: center;
                vertical-align: middle;
                padding: 1pt 4pt;
            }
            .kop-title {
                font-size: 10pt;
                font-weight: bold;
                text-decoration: underline;
                line-height: 1.15;
            }
            .kop-subtitle {
                font-size: 8.5pt;
                font-weight: bold;
                line-height: 1.1;
            }
            table.kop-table td.kop-info-lbl {
                font-weight: bold;
                font-size: 6pt;
                text-align: left;
                white-space: nowrap;
                padding: 0.8pt 2pt;
            }
            table.kop-table td.kop-info-val {
                font-weight: bold;
                font-size: 6pt;
                text-align: left;
                white-space: nowrap;
                padding: 0.8pt 2pt;
            }
            table.kop-table td.kop-ttd-hdr {
                font-weight: bold;
                font-size: 6pt;
                text-align: center;
                background-color: #f2f2f2;
                padding: 0.8pt 2pt;
            }
            table.kop-table td.kop-ttd-box {
                font-weight: bold;
                font-size: 5.5pt;
                text-align: center;
                vertical-align: middle;
                line-height: 1.05;
                padding: 1pt 2pt;
            }
            /* Tabel Checksheet Utama */
            table.table-cs {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                border: 0.8pt solid #000;
            }
            table.table-cs th,
            table.table-cs td {
                border: 0.6pt solid #000;
                text-align: center;
                vertical-align: middle;
                word-wrap: break-word;
                overflow: hidden;
            }
            table.table-cs th {
                background-color: #f2f2f2;
                font-weight: bold;
                font-size: ' . $hdrFontSize . ';
                padding: 1.2pt 1pt;
                line-height: 1.08;
            }
            table.table-cs td {
                font-size: ' . $dataFontSize . ';
                padding: ' . $cellPadding . ';
                line-height: 1.1;
                ' . $nowrapRule . '
            }
            table.table-cs td.startup-id-cell {
                font-size: 6.0pt;
                padding: 1pt 2pt;
                white-space: normal;
            }
            tr {
                page-break-inside: avoid;
            }
        </style>';
    }

    /**
     * Format teks sel ke HTML dengan dukungan penuh simbol Unicode (θ, ✓, ✔, ✖) melalui font DejaVu Sans
     */
    private function formatPdfCellHtml(string $rawText): string
    {
        $escaped = htmlspecialchars($rawText, ENT_QUOTES, 'UTF-8');
        $escaped = str_replace(
            ['θ', '&theta;', '✓', '✔', '✖'],
            [
                '<span class="sym">&#952;</span>',
                '<span class="sym">&#952;</span>',
                '<span class="sym">&#10003;</span>',
                '<span class="sym">&#10004;</span>',
                '<span class="sym">&#10006;</span>',
            ],
            $escaped
        );
        return nl2br($escaped);
    }

    /**
     * Ekstrak elemen tabel dari HTML view dengan menjaga encoding UTF-8
     */
    private function extractTableElement(
        string $html,
        string $id,
        string $typeProcess,
        string $namaApprover,
        array $alldata,
        string $process = ''
    ): ?array {
        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $utf8Html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html;
        $doc->loadHTML($utf8Html);
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

                $rawText = html_entity_decode(strip_tags($cell->textContent ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
     * Render tabel Startup per chunk (Kolom Identitas + 14 Kolom Data)
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

        $colPcts = [];
        $colPcts[1] = 2.5;
        if ($identityColCount === 4) {
            $colPcts[2] = 13.5;
            $colPcts[3] = 8.0;
            $colPcts[4] = 18.0;
        } elseif ($identityColCount === 3) {
            $colPcts[2] = 21.5;
            $colPcts[3] = 18.0;
        } else {
            $remId = 39.5 / max(1, $identityColCount - 1);
            for ($c = 2; $c <= $identityColCount; $c++) {
                $colPcts[$c] = $remId;
            }
        }
        $dataColPct = round(58.0 / max(1, $perPageCols), 3);
        for ($d = 1; $d <= $perPageCols; $d++) {
            $colPcts[$identityColCount + $d] = $dataColPct;
        }

        $sumSpanPct = function (int $startIdx, int $span) use ($colPcts): float {
            $sum = 0.0;
            for ($i = $startIdx; $i < $startIdx + $span; $i++) {
                $sum += ($colPcts[$i] ?? 4.14);
            }
            return round($sum, 2);
        };

        $html = '<table class="table-cs"><thead>';

        for ($r = 1; $r <= $validEndRow; $r++) {
            if ($r === $headerRowCount + 1) {
                $html .= '</thead><tbody>';
            }
            $html .= '<tr>';

            // 1. Render Kolom Identitas (1 .. $identityColCount)
            for ($c = 1; $c <= $identityColCount; $c++) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell   = $grid['origins'][$r][$c];
                    $tag    = !empty($cell['isHeader']) ? 'th' : 'td';
                    $cls    = ($tag === 'td') ? ' class="startup-id-cell"' : '';
                    $cs     = min((int) ($cell['colspan'] ?? 1), $identityColCount - $c + 1);
                    $rs     = min((int) ($cell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $csAttr = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $wPct   = $sumSpanPct($c, $cs);
                    $valHtml = $this->formatPdfCellHtml((string) ($cell['value'] ?? ''));
                    $html   .= "<{$tag}{$cls}{$rsAttr}{$csAttr} style=\"width: {$wPct}%;\">{$valHtml}</{$tag}>";
                }
            }

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
                $gridCol   = $startCol + $dIdx - 1;
                $targetCol = $identityColCount + $dIdx;

                if ($dIdx === 1 && $massiveHeaderCell !== null) {
                    $rs      = min((int) ($massiveHeaderCell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $rsAttr  = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $csAttr  = ($perPageCols > 1) ? ' colspan="' . $perPageCols . '"' : '';
                    $wPct    = $sumSpanPct($targetCol, $perPageCols);
                    $valHtml = $this->formatPdfCellHtml((string) ($massiveHeaderCell['value'] ?? ''));
                    $html   .= "<th{$rsAttr}{$csAttr} style=\"width: {$wPct}%;\">{$valHtml}</th>";
                    $dIdx   += $perPageCols;
                    continue;
                }

                if (isset($grid['origins'][$r][$gridCol])) {
                    $cell    = $grid['origins'][$r][$gridCol];
                    $tag     = !empty($cell['isHeader']) ? 'th' : 'td';
                    $cs      = min((int) ($cell['colspan'] ?? 1), $perPageCols - $dIdx + 1);
                    $rs      = min((int) ($cell['rowspan'] ?? 1), $validEndRow - $r + 1);
                    $csAttr  = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr  = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $wPct    = $sumSpanPct($targetCol, $cs);
                    $rawVal  = (string) ($cell['value'] ?? '');
                    $valHtml = ($rawVal !== '') ? $this->formatPdfCellHtml($rawVal) : '-';
                    $html   .= "<{$tag}{$rsAttr}{$csAttr} style=\"width: {$wPct}%;\">{$valHtml}</{$tag}>";
                    $dIdx   += $cs;
                } else {
                    if (empty($grid['occupied'][$r][$gridCol])) {
                        $wPct  = $sumSpanPct($targetCol, 1);
                        $html .= "<td style=\"width: {$wPct}%;\">-</td>";
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
     * Render tabel Production / Foregoing per 20 baris data + Dummy Rows
     * Tinggi baris data dinaikkan (21.5pt s/d 22.2pt) agar tabel memanjang penuh ke bawah kertas (~95% tinggi A4).
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
                $w = 6.6;
                if ($col === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $w = 3.5;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $w = 9.0;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $w = 14.5;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $w = 13.2;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $w = 8.0;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $w = 10.0;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $w = 9.8;
                } elseif (strpos($headerLabel, 'pos') !== false) {
                    $w = 4.2;
                } elseif (strpos($headerLabel, 'judge') !== false || strpos($headerLabel, 'ok/ng') !== false) {
                    $w = 4.6;
                }
            } else {
                // Production: proporsi optimal agar seluruh sel data 1 baris
                if ($col === 1 && ($headerLabel === 'no' || $headerLabel === 'no.')) {
                    $w = 4.5;
                } elseif (strpos($headerLabel, 'operator') !== false) {
                    $w = 9.5;
                } elseif (strpos($headerLabel, 'shift') !== false || strpos($headerLabel, 'group') !== false) {
                    $w = 12.0;
                } elseif (strpos($headerLabel, 'mch') !== false || strpos($headerLabel, 'mach') !== false) {
                    $w = 10.0;
                } elseif (strpos($headerLabel, 'model') !== false) {
                    $w = 16.5;
                } elseif (strpos($headerLabel, 'lot') !== false) {
                    $w = 15.0;
                } elseif (strpos($headerLabel, 'date') !== false) {
                    $w = 11.5;
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
                    $w = 6.0;
                } else {
                    $w = 5.2;
                }
            }
            $colWeights[$col] = $w;
        }

        $totalWeight = max(1.0, array_sum($colWeights));
        $colPcts     = [];
        foreach ($colWeights as $cIdx => $w) {
            $colPcts[$cIdx] = ($w / $totalWeight) * 100.0;
        }

        $sumSpanPct = function (int $startIdx, int $span) use ($colPcts): float {
            $sum = 0.0;
            for ($i = $startIdx; $i < $startIdx + $span; $i++) {
                $sum += ($colPcts[$i] ?? 2.5);
            }
            return round($sum, 2);
        };

        // Tinggi baris optimal agar 20 baris data mengisi penuh halaman A4 Landscape (~95% tinggi kertas)
        $hdrRowHeightPt  = ($typeProcess === 'foregoing') ? 16.5 : 16.0;
        $dataRowHeightPt = ($typeProcess === 'foregoing') ? 22.2 : 21.5;

        $html = '<table class="table-cs"><thead>';

        // 1. Render Baris Header (1 .. $headerRowCount)
        for ($r = 1; $r <= $headerRowCount; $r++) {
            $html .= '<tr style="height: ' . $hdrRowHeightPt . 'pt;">';
            for ($c = 1; $c <= $totalCols; $c++) {
                if (isset($grid['origins'][$r][$c])) {
                    $cell    = $grid['origins'][$r][$c];
                    $cs      = min((int) ($cell['colspan'] ?? 1), $totalCols - $c + 1);
                    $rs      = min((int) ($cell['rowspan'] ?? 1), $headerRowCount - $r + 1);
                    $csAttr  = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                    $rsAttr  = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                    $wPct    = $sumSpanPct($c, $cs);
                    $valHtml = $this->formatPdfCellHtml((string) ($cell['value'] ?? ''));
                    $html   .= "<th{$rsAttr}{$csAttr} style=\"width: {$wPct}%;\">{$valHtml}</th>";
                } elseif (empty($grid['occupied'][$r][$c])) {
                    $wPct  = $sumSpanPct($c, 1);
                    $html .= "<th style=\"width: {$wPct}%;\"></th>";
                }
            }
            $html .= '</tr>';
        }

        $html .= '</thead><tbody>';

        // 2. Render Baris Data (Maksimal 20 baris per halaman)
        $actualRowsRendered = 0;
        if ($startDataRow <= $endDataRow && $startDataRow <= $grid['totalRows']) {
            for ($r = $startDataRow; $r <= $endDataRow; $r++) {
                $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                for ($c = 1; $c <= $totalCols; $c++) {
                    if (isset($grid['origins'][$r][$c])) {
                        $cell    = $grid['origins'][$r][$c];
                        $cs      = min((int) ($cell['colspan'] ?? 1), $totalCols - $c + 1);
                        $rs      = min((int) ($cell['rowspan'] ?? 1), $endDataRow - $r + 1);
                        $csAttr  = ($cs > 1) ? ' colspan="' . $cs . '"' : '';
                        $rsAttr  = ($rs > 1) ? ' rowspan="' . $rs . '"' : '';
                        $wPct    = $sumSpanPct($c, $cs);
                        $rawVal  = (string) ($cell['value'] ?? '');
                        $valHtml = ($rawVal !== '') ? $this->formatPdfCellHtml($rawVal) : '-';
                        $html   .= "<td{$rsAttr}{$csAttr} style=\"width: {$wPct}%;\">{$valHtml}</td>";
                    } elseif (empty($grid['occupied'][$r][$c])) {
                        $wPct  = $sumSpanPct($c, 1);
                        $html .= "<td style=\"width: {$wPct}%;\">-</td>";
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
                $posCycle = ['X', 'Y', 'Z', 'θ'];
                $d        = 0;
                while ($d < $dummyNeeded) {
                    $blockSize = min(4, $dummyNeeded - $d);
                    for ($s = 0; $s < $blockSize; $s++) {
                        $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                        if ($s === 0) {
                            $rsAttr = ($blockSize > 1) ? ' rowspan="' . $blockSize . '"' : '';
                            for ($c = 1; $c <= min(7, $totalCols); $c++) {
                                $wPct  = $sumSpanPct($c, 1);
                                $html .= "<td{$rsAttr} style=\"width: {$wPct}%;\">-</td>";
                            }
                        }
                        if ($totalCols >= 8) {
                            $wPct   = $sumSpanPct(8, 1);
                            $posLbl = $this->formatPdfCellHtml($posCycle[$s] ?? '-');
                            $html  .= "<td style=\"width: {$wPct}%;\">{$posLbl}</td>";
                        }
                        for ($c = 9; $c <= $totalCols; $c++) {
                            $wPct  = $sumSpanPct($c, 1);
                            $html .= "<td style=\"width: {$wPct}%;\">-</td>";
                        }
                        $html .= '</tr>';
                    }
                    $d += $blockSize;
                }
            } else {
                for ($d = 1; $d <= $dummyNeeded; $d++) {
                    $html .= '<tr style="height: ' . $dataRowHeightPt . 'pt;">';
                    for ($c = 1; $c <= $totalCols; $c++) {
                        $wPct  = $sumSpanPct($c, 1);
                        $html .= "<td style=\"width: {$wPct}%;\">-</td>";
                    }
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Bangun Kop Surat 5 Baris yang Ringkas & Proporsional
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
                <tr style="height: 7.5pt;">
                    <td rowspan="4" class="kop-left" style="width: 23%;">' . $ptBlockHtml . '</td>
                    <td rowspan="4" class="kop-center" style="width: 60%;">' . $titleHtml . '</td>
                    <td class="kop-info-lbl" style="width: 6.5%;">Doc No.</td>
                    <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars($docNoClean, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 7.5pt;">
                    <td class="kop-info-lbl" style="width: 6.5%;">Revisi</td>
                    <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars((string) $revisiClean, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 7.5pt;">
                    <td class="kop-info-lbl" style="width: 6.5%;">Berlaku</td>
                    <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars($tglBerlaku, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr style="height: 7.5pt;">
                    <td class="kop-ttd-hdr" style="width: 6.5%;">QC</td>
                    <td class="kop-ttd-hdr" style="width: 10.5%;">Production</td>
                </tr>
                <tr style="height: 17pt;">
                    <td class="kop-machine" style="width: 23%;">' . $machHtml . '</td>
                    <td style="width: 60%;"></td>
                    <td class="kop-ttd-box" style="width: 6.5%;">' . $qcBox . '</td>
                    <td class="kop-ttd-box" style="width: 10.5%;">' . $prodBox . '</td>
                </tr>
            </table>';
        }

        $ttdHtml = '';
        if (trim($namaApprover) !== '' && trim($namaApprover) !== '-') {
            $ttdHtml = 'Approved by:<br>' . nl2br(htmlspecialchars(trim($namaApprover), ENT_QUOTES, 'UTF-8'));
        }

        return '
        <table class="kop-table">
            <tr style="height: 7.5pt;">
                <td rowspan="4" class="kop-left" style="width: 23%;">' . $ptBlockHtml . '</td>
                <td rowspan="4" class="kop-center" style="width: 60%;">' . $titleHtml . '</td>
                <td class="kop-info-lbl" style="width: 6.5%;">No. Dok / No.</td>
                <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars($docNoClean, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 7.5pt;">
                <td class="kop-info-lbl" style="width: 6.5%;">Revisi</td>
                <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars((string) $revisiClean, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 7.5pt;">
                <td class="kop-info-lbl" style="width: 6.5%;">Berlaku</td>
                <td class="kop-info-val" style="width: 10.5%;">: ' . htmlspecialchars($tglBerlaku, ENT_QUOTES, 'UTF-8') . '</td>
            </tr>
            <tr style="height: 7.5pt;">
                <td colspan="2" class="kop-ttd-hdr" style="width: 17%;">Checked</td>
            </tr>
            <tr style="height: 17pt;">
                <td class="kop-machine" style="width: 23%;">' . $machHtml . '</td>
                <td style="width: 60%;"></td>
                <td colspan="2" class="kop-ttd-box" style="width: 17%;">' . $ttdHtml . '</td>
            </tr>
        </table>';
    }
}