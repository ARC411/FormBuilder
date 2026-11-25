<?php

namespace App\Libraries;

/**
 * Generator Hard Code PHP Statis (Input View & History View) dari Struktur Form Builder.
 * Murni menghasilkan string kode PHP tanpa mengubah file apa pun di dalam folder project.
 */
class HardcodeGenerator
{
    public function generateAll(array $revision, array $treeParams, string $processCode, string $processName = ''): array
    {
        $cleanCode    = strtolower(trim($processCode));
        $parts        = explode('-', $cleanCode);
        $device       = $parts[0] ?? 'device';
        $kategoriForm = $revision['kategori_form'] ?? (($parts[1] ?? 'p') === 's' ? 'startup' : (($parts[1] ?? 'p') === 'f' ? 'foregoing' : 'production'));
        $folderCat    = ($kategoriForm === 'startup_horizontal') ? 'startup' : $kategoriForm;
        $revLabel     = str_pad((string)($revision['revisi'] ?? '0'), 2, '0', STR_PAD_LEFT);
        $procTitle    = $processName !== '' ? $processName : strtoupper($cleanCode);

        return [
            'process_code'     => $cleanCode,
            'revisi'           => $revLabel,
            'kategori_form'    => $kategoriForm,
            'filename'         => $cleanCode . '.php',
            'input_ref_path'   => "app/Views/layout/{$device}/input/{$folderCat}/{$cleanCode}.php",
            'history_ref_path' => "app/Views/layout/{$device}/history/{$folderCat}/{$cleanCode}.php",
            'input_code'       => $this->generateInputCode($revision, $treeParams, $cleanCode, $procTitle, $kategoriForm, $device, $folderCat),
            'history_code'     => $this->generateHistoryCode($revision, $treeParams, $cleanCode, $procTitle, $kategoriForm, $device, $folderCat),
        ];
    }

    // =========================================================================
    // TREE HELPERS
    // =========================================================================

    private function countLeaves(array $node, bool $ignoreGridChildren = false): int
    {
        if (empty($node['children'])) {
            return 1;
        }
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) {
            return 1;
        }
        $count = 0;
        foreach ($node['children'] as $child) {
            $count += $this->countLeaves($child, $ignoreGridChildren);
        }
        return $count;
    }

    private function getSubtreeDepth(array $node, bool $ignoreGridChildren = false): int
    {
        if (empty($node['children'])) {
            return 1;
        }
        if ($ignoreGridChildren && !empty($node['children'][0]['posisi_grid'])) {
            return 1;
        }
        $max = 0;
        foreach ($node['children'] as $child) {
            $d = $this->getSubtreeDepth($child, $ignoreGridChildren);
            if ($d > $max) {
                $max = $d;
            }
        }
        return $max + 1;
    }

    private function prepareHistoryTree(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            if (!empty($node['children'])) {
                $node['children'] = $this->prepareHistoryTree($node['children']);
            }
            if (!empty($node['standar_dinamis']) && !empty($node['children'])) {
                $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
                if (is_array($dyn) && !empty($dyn['options'])) {
                    $stdCol = [
                        'id'              => 'std_' . $node['id'],
                        'parent_id'       => $node['id'],
                        'nama_parameter'  => 'Std (Min)',
                        'kolom_db'        => $dyn['db_col'] ?? 'std_die_bond',
                        'tipe_input'      => 'std_display',
                        'std_suffix'      => $dyn['suffix_text'] ?? ' N',
                        'batas_min'       => null,
                        'batas_max'       => null,
                        'satuan_opsi'     => null,
                        'standar_dinamis' => null,
                        'posisi_grid'     => null,
                        'children'        => [],
                    ];
                    array_unshift($node['children'], $stdCol);
                }
            }
            $result[] = $node;
        }
        return $result;
    }

    private function collectInputMeta(array $nodes, array &$exclusiveMap, array &$dynamicStdList, array &$dynamicStdTargetMap, ?string $activeExcl = null, ?string $activeDynId = null): void
    {
        foreach ($nodes as $node) {
            $currExcl = $activeExcl;
            if (!empty($node['is_exclusive'])) {
                $currExcl = 'excl_' . $node['id'];
            }

            $currDyn = $activeDynId;
            if (!empty($node['standar_dinamis'])) {
                $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
                if (is_array($dyn) && !empty($dyn['options'])) {
                    $currDyn        = 'dynstd_' . $node['id'];
                    $dyn['dom_id']  = $currDyn;
                    $dyn['node_id'] = $node['id'];
                    $dynamicStdList[$currDyn] = $dyn;
                }
            }

            if (empty($node['children'])) {
                if ($currExcl) {
                    $exclusiveMap[$node['id']] = $currExcl;
                }
                if ($currDyn) {
                    $dynamicStdTargetMap[$node['id']] = $currDyn;
                }
            } else {
                $this->collectInputMeta($node['children'], $exclusiveMap, $dynamicStdList, $dynamicStdTargetMap, $currExcl, $currDyn);
            }
        }
    }

    private function buildStartupRows(array $nodes, int $currentCol, int $maxDepth, array &$verticalRows, int &$rowIndex, bool $isRootLevel = false, bool $skipNotes = false): void
    {
        $no = 1;
        foreach ($nodes as $node) {
            if (!isset($verticalRows[$rowIndex])) {
                $verticalRows[$rowIndex] = [];
            }

            if ($skipNotes && empty($node['children']) && (stripos($node['nama_parameter'] ?? '', 'note') !== false || stripos($node['nama_parameter'] ?? '', 'catatan') !== false)) {
                continue;
            }

            $rowspan  = $this->countLeaves($node);
            $subDepth = $this->getSubtreeDepth($node);
            $colspan  = max(1, $maxDepth - $currentCol - ($subDepth - 1));

            if ($isRootLevel) {
                $verticalRows[$rowIndex][] = [
                    'type'    => 'no',
                    'no'      => $no++,
                    'rowspan' => $rowspan,
                ];
            }

            $verticalRows[$rowIndex][] = [
                'type'      => 'param',
                'name'      => $node['nama_parameter'],
                'std'       => $node['standar_nilai'] ?? null,
                'rowspan'   => $rowspan,
                'colspan'   => $colspan,
                'node_data' => $node,
            ];

            if (empty($node['children'])) {
                $verticalRows[$rowIndex][] = [
                    'type'      => 'input',
                    'node_data' => $node,
                ];
                $rowIndex++;
            } else {
                $this->buildStartupRows($node['children'], $currentCol + $colspan, $maxDepth, $verticalRows, $rowIndex, false, $skipNotes);
            }
        }
    }

    private function buildHorizontalHeaders(array $nodes, int $level, array &$headerRows, array &$headerLeaves, int $maxDepth, bool $forHistory = false): void
    {
        if (!isset($headerRows[$level])) {
            $headerRows[$level] = [];
        }
        foreach ($nodes as $node) {
            $hasGridChildren = (!empty($node['children']) && !empty($node['children'][0]['posisi_grid']));
            $isHeaderLeaf    = (empty($node['children']) || $hasGridChildren);

            $colspan  = $this->countLeaves($node, true);
            $subDepth = $this->getSubtreeDepth($node, true);
            $rowspan  = max(1, ($maxDepth - $level) - ($subDepth - 1));

            $headerDisplay = $node['nama_parameter'];
            if ($forHistory && !empty($node['standar_dinamis'])) {
                $headerDisplay = 'OK:✓ / NG:X';
            }

            $headerRows[$level][] = [
                'name'      => $headerDisplay,
                'colspan'   => $colspan,
                'rowspan'   => $rowspan,
                'node_data' => $node,
            ];

            if ($isHeaderLeaf) {
                $headerLeaves[] = $node;
            } else {
                $this->buildHorizontalHeaders($node['children'], $level + $rowspan, $headerRows, $headerLeaves, $maxDepth, $forHistory);
            }
        }
    }

    private function h($val): string
    {
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }

    private function phpStr($val): string
    {
        return "'" . addcslashes((string)$val, "'\\") . "'";
    }

    // =========================================================================
    // 1. GENERATOR HARD CODE INPUT (.php)
    // =========================================================================

    public function generateInputCode(array $revision, array $treeParams, string $processCode, string $procTitle, string $kategoriForm, string $device, string $folderCat): string
    {
        $revNum  = (string)($revision['revisi'] ?? '0');
        $revId   = (int)($revision['id'] ?? 0);
        $genTime = date('Y-m-d H:i:s');

        $exclusiveMap        = [];
        $dynamicStdList      = [];
        $dynamicStdTargetMap = [];
        if (!empty($treeParams)) {
            $this->collectInputMeta($treeParams, $exclusiveMap, $dynamicStdList, $dynamicStdTargetMap);
        }

        $cntProses       = 1;
        $allGridRowNames = [];
        $hasSuffixCols   = false;

        if ($kategoriForm !== 'startup' && !empty($treeParams)) {
            foreach ($treeParams as $node) {
                $queue = [$node];
                while (!empty($queue)) {
                    $curr = array_shift($queue);
                    if (!empty($curr['children'])) {
                        if (!empty($curr['children'][0]['posisi_grid'])) {
                            foreach ($curr['children'] as $gc) {
                                if (!empty($gc['posisi_grid']) && !in_array($gc['posisi_grid'], $allGridRowNames)) {
                                    $allGridRowNames[] = $gc['posisi_grid'];
                                }
                                if (!empty($gc['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $gc['kolom_db'])) {
                                    $hasSuffixCols = true;
                                }
                            }
                        } else {
                            foreach ($curr['children'] as $ch) {
                                $queue[] = $ch;
                            }
                        }
                    }
                }
            }
            if (!empty($allGridRowNames) && $hasSuffixCols) {
                $cntProses = count($allGridRowNames);
            }
        }

        $lines   = [];
        $lines[] = '<?php';
        $lines[] = '/**';
        $lines[] = " * HARD CODE FORM INPUT - " . strtoupper($processCode) . " (Revisi {$revNum})";
        $lines[] = " * Kategori      : {$kategoriForm}";
        $lines[] = " * Lokasi Target : app/Views/layout/{$device}/input/{$folderCat}/{$processCode}.php";
        $lines[] = " * Digenerate    : {$genTime}";
        $lines[] = ' */';
        $lines[] = 'if (!function_exists(\'hcGetInputVal\')) {';
        $lines[] = '    function hcGetInputVal($alldata, int $rowIdx, string $colDb): string {';
        $lines[] = '        if (!is_array($alldata) || $colDb === \'\') return \'\';';
        $lines[] = '        $baseCol = preg_replace(\'/[a-z]$/i\', \'\', $colDb);';
        $lines[] = '        if (isset($alldata[$rowIdx][$colDb])) return (string)$alldata[$rowIdx][$colDb];';
        $lines[] = '        if (isset($alldata[$rowIdx][$baseCol])) return (string)$alldata[$rowIdx][$baseCol];';
        $lines[] = '        if (isset($alldata[0][$colDb])) return (string)$alldata[0][$colDb];';
        $lines[] = '        return \'\';';
        $lines[] = '    }';
        $lines[] = '}';
        $lines[] = '?>';
        $lines[] = '<div class="table-responsive" id="' . $this->h($processCode) . '">';
        $lines[] = '    <?= csrf_field(); ?>';
        $lines[] = '    <input type="hidden" id="cnt-proses" name="cnt-proses" value="' . $cntProses . '">';
        $lines[] = '    <input type="hidden" id="revision_id" name="revision_id" value="' . $revId . '">';
        $lines[] = '    <?php if (isset($alldata) && isset($number)): ?>';
        $lines[] = '        <input type="hidden" id="number-edit" name="number-edit" value="<?= esc($number) ?>">';
        $lines[] = '    <?php endif; ?>';
        $lines[] = '';

        // Dynamic standard dropdowns (jika ada)
        if (!empty($dynamicStdList)) {
            foreach ($dynamicStdList as $domId => $dyn) {
                $dbColName = $dyn['db_col'] ?? 'std_die_bond';
                $lblDrop   = $dyn['label_dropdown'] ?? 'Pilih Standar';
                $lines[]   = '    <?php $curStd_' . $this->h($domId) . ' = (isset($alldata) && isset($alldata[0][' . $this->phpStr($dbColName) . '])) ? (string)$alldata[0][' . $this->phpStr($dbColName) . '] : \'\'; ?>';
                $lines[]   = '    <div class="mb-3 row align-items-center bg-light p-2 rounded border mx-0">';
                $lines[]   = '        <label class="col-sm-2 col-form-label fw-bold">' . $this->h($lblDrop) . '</label>';
                $lines[]   = '        <div class="col-sm-4">';
                $lines[]   = '            <select class="form-select dyn-std-selector" data-dom-id="' . $this->h($domId) . '" data-db-col="' . $this->h($dbColName) . '" required>';
                $lines[]   = '                <option value="" data-min="">-- Pilih ' . $this->h($lblDrop) . ' --</option>';
                foreach ($dyn['options'] as $opt) {
                    $oVal = (string)($opt['value'] ?? '');
                    $oMin = (string)($opt['min'] ?? '');
                    $oLbl = (string)($opt['label'] ?? $oVal);
                    $lines[] = '                <option value="' . $this->h($oVal) . '" data-min="' . $this->h($oMin) . '" <?= ($curStd_' . $this->h($domId) . ' !== \'\' && ($curStd_' . $this->h($domId) . ' == ' . $this->phpStr($oVal) . ' || $curStd_' . $this->h($domId) . ' == ' . $this->phpStr($oMin) . ')) ? \'selected\' : \'\' ?>>' . $this->h($oLbl) . '</option>';
                }
                $lines[] = '            </select>';
                $lines[] = '        </div>';
                $lines[] = '        <input type="hidden" id="' . $this->h($dbColName) . '" name="' . $this->h($dbColName) . '" value="<?= esc($curStd_' . $this->h($domId) . ') ?>">';
                $lines[] = '    </div>';
            }
        }

        if (empty($treeParams)) {
            $lines[] = '    <div class="alert alert-warning text-center my-3">Belum ada parameter pada revisi ini.</div>';
        } elseif ($kategoriForm === 'startup') {
            $maxDepth = 1;
            foreach ($treeParams as $n) {
                $d = $this->getSubtreeDepth($n);
                if ($d > $maxDepth) {
                    $maxDepth = $d;
                }
            }
            $verticalRows = [];
            $rowIndex     = 0;
            $this->buildStartupRows($treeParams, 0, $maxDepth, $verticalRows, $rowIndex, true, false);

            $lines[] = '    <table class="table table-striped-columns table-bordered align-middle text-center">';
            $lines[] = '        <thead align="center">';
            $lines[] = '            <tr>';
            $lines[] = '                <th colspan="' . ($maxDepth + 2) . '" scope="col">' . $this->h(strtoupper($procTitle)) . '</th>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <th scope="col" width="5%">No</th>';
            $lines[] = '                <th colspan="' . max(1, $maxDepth - 1) . '" scope="col">Start up Check Item</th>';
            if ($maxDepth > 1) {
                $lines[] = '                <th scope="col">Standard</th>';
            }
            $lines[] = '                <th scope="col" width="24%">Data</th>';
            $lines[] = '            </tr>';
            $lines[] = '        </thead>';
            $lines[] = '        <tbody>';
            foreach ($verticalRows as $r) {
                $lines[] = '            <tr>';
                foreach ($r as $cell) {
                    if ($cell['type'] === 'no') {
                        $lines[] = '                <td rowspan="' . (int)$cell['rowspan'] . '" class="fw-bold">' . (int)$cell['no'] . '</td>';
                    } elseif ($cell['type'] === 'param') {
                        $titleHtml = $this->renderInputHeaderTitleHtml($cell['node_data']);
                        $stdHtml   = !empty($cell['std']) ? '<div class="text-muted small">' . $this->h($cell['std']) . '</div>' : '';
                        $lines[]   = '                <td rowspan="' . (int)$cell['rowspan'] . '" colspan="' . (int)$cell['colspan'] . '">' . $titleHtml . $stdHtml . '</td>';
                    } elseif ($cell['type'] === 'input') {
                        $inpCode = $this->compileInputElement($cell['node_data'], $kategoriForm, 0, $exclusiveMap, $dynamicStdTargetMap, '');
                        $lines[] = '                <td>' . $inpCode . '</td>';
                    }
                }
                $lines[] = '            </tr>';
            }
            $lines[] = '        </tbody>';
            $lines[] = '    </table>';
        } else {
            $tablesMap = [];
            foreach ($treeParams as $node) {
                $tIdx = !empty($node['table_index']) ? (int)$node['table_index'] : 1;
                $tablesMap[$tIdx][] = $node;
            }
            ksort($tablesMap);

            foreach ($tablesMap as $tIdx => $currentTree) {
                $headerMaxDepth = 1;
                foreach ($currentTree as $node) {
                    $d = $this->getSubtreeDepth($node, true);
                    if ($d > $headerMaxDepth) {
                        $headerMaxDepth = $d;
                    }
                }

                $headerRows   = [];
                $headerLeaves = [];
                $this->buildHorizontalHeaders($currentTree, 0, $headerRows, $headerLeaves, $headerMaxDepth, false);
                ksort($headerRows);

                $tableGridRowNames = [];
                $firstGridLeafIdx  = null;
                $gridHeaderLabel   = 'Sample / Label';
                foreach ($headerLeaves as $hIdx => $hLeaf) {
                    if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                        if ($firstGridLeafIdx === null) {
                            $firstGridLeafIdx = $hIdx;
                        }
                        foreach ($hLeaf['children'] as $gc) {
                            if (!empty($gc['standar_nilai'])) {
                                $gridHeaderLabel = $gc['standar_nilai'];
                            }
                            if (!empty($gc['posisi_grid']) && !in_array($gc['posisi_grid'], $tableGridRowNames)) {
                                $tableGridRowNames[] = $gc['posisi_grid'];
                            }
                        }
                    }
                }

                $totalTableCols = count($headerLeaves) + (!empty($tableGridRowNames) ? 1 : 0);

                $lines[] = '    <table class="table table-striped-columns table-bordered align-middle text-center mb-4" style="white-space: nowrap;">';
                $lines[] = '        <thead align="center">';
                if ($tIdx === 1) {
                    $lines[] = '            <tr>';
                    $lines[] = '                <th colspan="' . max(1, $totalTableCols) . '" scope="col">' . $this->h(strtoupper($procTitle)) . '</th>';
                    $lines[] = '            </tr>';
                }
                foreach ($headerRows as $level => $row) {
                    $lines[]        = '            <tr>';
                    $runningCol     = 0;
                    $labelInserted  = false;
                    foreach ($row as $col) {
                        if (!empty($tableGridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx) {
                            $labelInserted = true;
                            $lines[]       = '                <th class="input-form align-middle" rowspan="' . $headerMaxDepth . '">' . $this->h($gridHeaderLabel) . '</th>';
                        }
                        $titleHtml = $this->renderInputHeaderTitleHtml($col['node_data']);
                        $stdSub    = (!empty($col['node_data']['standar_nilai']) && ($col['node_data']['tipe_input'] ?? '') !== 'header_only')
                            ? '<br><small class="fw-normal">' . $this->h($col['node_data']['standar_nilai']) . '</small>'
                            : '';
                        $lines[]   = '                <th class="input-form align-middle" colspan="' . (int)$col['colspan'] . '" rowspan="' . (int)$col['rowspan'] . '">' . $titleHtml . $stdSub . '</th>';
                        $runningCol += (int)$col['colspan'];
                    }
                    $lines[] = '            </tr>';
                }
                $lines[] = '        </thead>';
                $lines[] = '        <tbody>';

                if (empty($tableGridRowNames)) {
                    $lines[] = '            <tr>';
                    foreach ($headerLeaves as $leaf) {
                        $inpCode = $this->compileInputElement($leaf, $kategoriForm, 0, $exclusiveMap, $dynamicStdTargetMap, $leaf['nama_parameter'] ?? '');
                        $lines[] = '                <td>' . $inpCode . '</td>';
                    }
                    $lines[] = '            </tr>';
                } else {
                    $gridRowCount = count($tableGridRowNames);
                    foreach ($tableGridRowNames as $rIdx => $rowName) {
                        $lines[] = '            <tr data-grid-row="' . $this->h($rowName) . '">';
                        foreach ($headerLeaves as $hIdx => $hLeaf) {
                            if ($hIdx === $firstGridLeafIdx) {
                                $lines[] = '                <th scope="row" class="align-middle">' . $this->h($rowName) . '</th>';
                            }
                            if (empty($hLeaf['children'])) {
                                if ($rIdx === 0) {
                                    $inpCode = $this->compileInputElement($hLeaf, $kategoriForm, 0, $exclusiveMap, $dynamicStdTargetMap, $hLeaf['nama_parameter'] ?? '');
                                    $lines[] = '                <td rowspan="' . $gridRowCount . '" class="align-middle">' . $inpCode . '</td>';
                                }
                            } else {
                                $matchedCell = null;
                                foreach ($hLeaf['children'] as $gChild) {
                                    if ($gChild['posisi_grid'] === $rowName) {
                                        $matchedCell = $gChild;
                                        break;
                                    }
                                }
                                if ($matchedCell) {
                                    $inpCode = $this->compileInputElement($matchedCell, $kategoriForm, $rIdx, $exclusiveMap, $dynamicStdTargetMap, $hLeaf['nama_parameter'] ?? '');
                                    $lines[] = '                <td>' . $inpCode . '</td>';
                                } else {
                                    $lines[] = '                <td>-</td>';
                                }
                            }
                        }
                        $lines[] = '            </tr>';
                    }
                }

                $lines[] = '        </tbody>';
                $lines[] = '    </table>';
            }
        }

        if (!empty($revision['catatan_sop'])) {
            $lines[] = '    <div class="note mt-3 p-3 bg-light border rounded">';
            $lines[] = '        <h6 class="fw-bold mb-2"><i class="fas fa-info-circle text-primary me-1"></i> Catatan SOP Pemeriksaan:</h6>';
            $lines[] = '        <div class="small text-secondary" style="white-space: pre-line; line-height: 1.6;">' . $this->h($revision['catatan_sop']) . '</div>';
            $lines[] = '    </div>';
        }

        $lines[] = '</div>';
        $lines[] = '';
        $lines[] = $this->getInputValidationScript($processCode);

        return implode("\n", $lines) . "\n";
    }

    private function renderInputHeaderTitleHtml(array $node): string
    {
        if (!empty($node['standar_dinamis'])) {
            $dyn = is_string($node['standar_dinamis']) ? json_decode($node['standar_dinamis'], true) : $node['standar_dinamis'];
            if (is_array($dyn) && !empty($dyn['options'])) {
                $prefix = $dyn['prefix_text'] ?? 'Min: ';
                $suffix = $dyn['suffix_text'] ?? ' N';
                $domId  = 'dynstd_' . $node['id'];
                return $this->h($prefix) . '<span id="lbl_' . $this->h($domId) . '" class="fw-bold text-danger">-</span>' . $this->h($suffix);
            }
        }
        return $this->h($node['nama_parameter'] ?? '');
    }

    private function compileInputElement(array $leaf, string $kategoriForm, int $rowIdx, array $exclusiveMap, array $dynamicStdTargetMap, string $colHeaderName): string
    {
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));
        if ($colDb === '' || ($leaf['tipe_input'] ?? '') === 'header_only') {
            $staticTxt = trim((string)($leaf['standar_nilai'] ?? ''));
            return $staticTxt !== ''
                ? '<div class="fw-semibold text-wrap" style="min-width: 120px;">' . nl2br($this->h($staticTxt)) . '</div>'
                : '<span class="text-muted">-</span>';
        }

        $min        = ($leaf['batas_min'] !== null && $leaf['batas_min'] !== '') ? (float)$leaf['batas_min'] : null;
        $max        = ($leaf['batas_max'] !== null && $leaf['batas_max'] !== '') ? (float)$leaf['batas_max'] : null;
        $isReadonly = !empty($leaf['is_readonly']);

        $exclGroup  = $exclusiveMap[$leaf['id']] ?? '';
        $dynStdId   = $dynamicStdTargetMap[$leaf['id']] ?? '';
        $exclAttr   = $exclGroup ? ' data-exclusive-group="' . $this->h($exclGroup) . '"' : '';
        $dynStdAttr = $dynStdId ? ' data-dynstd-target="' . $this->h($dynStdId) . '"' : '';

        $formula     = !empty($leaf['rumus_otomatis']) ? (is_string($leaf['rumus_otomatis']) ? json_decode($leaf['rumus_otomatis'], true) : $leaf['rumus_otomatis']) : null;
        $formulaAttr = '';
        if (!empty($formula['type'])) {
            $isReadonly = true;
            $inCol      = $formula['in_col'] ?? 'par013';
            $outCol     = $formula['out_col'] ?? 'par014';
            if (preg_match('/^par\d+([a-z])$/i', $colDb, $mSuf)) {
                $suf = $mSuf[1];
                if (!preg_match('/[a-z]$/i', $inCol)) {
                    $inCol .= $suf;
                }
                if (!preg_match('/[a-z]$/i', $outCol)) {
                    $outCol .= $suf;
                }
            }
            $formulaAttr = ' data-formula-type="' . $this->h($formula['type']) . '" data-in-col="' . $this->h($inCol) . '" data-out-col="' . $this->h($outCol) . '"';
        }

        $units = [];
        if (!empty($leaf['satuan_opsi'])) {
            $decodedUnits = is_string($leaf['satuan_opsi']) ? json_decode($leaf['satuan_opsi'], true) : $leaf['satuan_opsi'];
            if (is_array($decodedUnits)) {
                $units = $decodedUnits;
            }
        }

        $stepVal = (!empty($leaf['step_value']) && $leaf['step_value'] !== 'any') ? (string)$leaf['step_value'] : 'any';
        $rgJson  = !empty($leaf['rentang_ganda']) ? (is_string($leaf['rentang_ganda']) ? $leaf['rentang_ganda'] : json_encode($leaf['rentang_ganda'], JSON_UNESCAPED_UNICODE)) : '';
        $rgAttr  = $rgJson ? ' data-rentang-ganda="' . $this->h($rgJson) . '"' : '';

        $roAttr  = $isReadonly ? ' readonly' : '';
        $roStyle = $isReadonly ? 'background-color: #e9ecef;' : '';

        $valExpr = 'hcGetInputVal($alldata ?? null, ' . $rowIdx . ', ' . $this->phpStr($colDb) . ')';
        $tipe    = $leaf['tipe_input'] ?? 'text';

        if ($tipe === 'dropdown_ok_ng') {
            if ($kategoriForm === 'startup') {
                return '<?php $v = ' . $valExpr . '; ?>'
                    . '<select class="form-control form-select dyn-input-el" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '"' . $exclAttr . '>'
                    . '<option value="">-- Pilih --</option>'
                    . '<option value="✔" <?= in_array($v, [\'✔\', \'OK\', \'V\'], true) ? \'selected\' : \'\' ?>>✔ (OK)</option>'
                    . '<option value="✖" <?= in_array($v, [\'✖\', \'NG\', \'X\'], true) ? \'selected\' : \'\' ?>>✖ (NG)</option>'
                    . '</select>';
            }
            if ($kategoriForm === 'startup_horizontal') {
                return '<?php $v = ' . $valExpr . '; ?>'
                    . '<select class="form-select dyn-input-el" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" style="min-width: 85px;"' . $exclAttr . '>'
                    . '<option value="-" <?= ($v === \'-\' || $v === \'\') ? \'selected\' : \'\' ?>>-</option>'
                    . '<option value="V" <?= in_array($v, [\'V\', \'OK\', \'✔\'], true) ? \'selected\' : \'\' ?>>V</option>'
                    . '<option value="X" <?= in_array($v, [\'X\', \'NG\', \'✖\'], true) ? \'selected\' : \'\' ?>>X</option>'
                    . '</select>';
            }
            return '<?php $v = ' . $valExpr . '; ?>'
                . '<select class="form-select dyn-input-el" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" style="min-width: 85px;"' . $exclAttr . '>'
                . '<option value="-" <?= ($v === \'-\' || $v === \'\') ? \'selected\' : \'\' ?>>-</option>'
                . '<option value="OK" <?= ($v === \'OK\') ? \'selected\' : \'\' ?>>OK</option>'
                . '<option value="NG" <?= ($v === \'NG\') ? \'selected\' : \'\' ?>>NG</option>'
                . '</select>';
        }

        if ($tipe === 'dropdown_custom') {
            $customOpts = [];
            if (!empty($leaf['opsi_dropdown'])) {
                $decoded = is_string($leaf['opsi_dropdown']) ? json_decode($leaf['opsi_dropdown'], true) : $leaf['opsi_dropdown'];
                $customOpts = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', (string)$leaf['opsi_dropdown'])));
            }
            $out = '<?php $v = ' . $valExpr . '; ?>'
                . '<select class="form-select dyn-input-el" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" style="min-width: 95px;"' . $exclAttr . '>'
                . '<option value="">-- Pilih --</option>';
            foreach ($customOpts as $cOpt) {
                $optVal = is_array($cOpt) ? (string)($cOpt['value'] ?? $cOpt['label'] ?? '') : (string)$cOpt;
                $optLbl = is_array($cOpt) ? (string)($cOpt['label'] ?? $cOpt['value'] ?? '') : (string)$cOpt;
                $out .= '<option value="' . $this->h($optVal) . '" <?= ($v === ' . $this->phpStr($optVal) . ') ? \'selected\' : \'\' ?>>' . $this->h($optLbl) . '</option>';
            }
            $out .= '</select>';
            return $out;
        }

        if ($tipe === 'auto_judgment') {
            $targetEval = !empty($leaf['target_evaluasi']) ? $leaf['target_evaluasi'] : '*';
            return '<?php $v = ' . $valExpr . '; $dv = ($v === \'✔\' || $v === \'OK\') ? \'✔\' : (($v === \'✖\' || $v === \'NG\') ? \'✖\' : $v); ?>'
                . '<input type="text" class="form-control text-center fw-bold dyn-input-el dyn-auto-judgment" '
                . 'id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc($dv) ?>" '
                . 'data-target-eval="' . $this->h($targetEval) . '" readonly '
                . 'style="min-width: 80px; font-size: 1.15rem; background-color: #f8f9fa; cursor: not-allowed;"' . $exclAttr . '>';
        }

        if ($tipe === 'time') {
            return '<input type="time" class="form-control text-center dyn-input-el" style="min-width: 105px; ' . $roStyle . '" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc(' . $valExpr . ') ?>"' . $roAttr . $exclAttr . '>';
        }

        if ($tipe === 'date') {
            return '<input type="date" class="form-control text-center dyn-input-el" style="min-width: 130px; ' . $roStyle . '" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc(' . $valExpr . ') ?>"' . $roAttr . $exclAttr . '>';
        }

        if ($tipe === 'textarea') {
            return '<textarea class="form-control dyn-input-el" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" rows="4" style="min-width: 160px; ' . $roStyle . '"' . $roAttr . $exclAttr . '><?= esc(' . $valExpr . ') ?></textarea>';
        }

        if ($tipe === 'number') {
            if (!empty($units)) {
                $defUnit = (string)($units[0]['unit'] ?? '');
                $out     = '<?php $rawU = trim(' . $valExpr . '); $uParts = $rawU !== \'\' ? explode(\' \', $rawU, 2) : [\'\', \'\']; $uNum = $uParts[0] ?? \'\'; $uCur = ($uParts[1] ?? \'\') ?: ' . $this->phpStr($defUnit) . '; ?>';
                $out    .= '<div class="input-group" style="min-width: 165px;">';
                $out    .= '<input type="hidden" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc($rawU) ?>">';
                $out    .= '<input type="number" step="' . $this->h($stepVal) . '" class="form-control text-center dyn-input-el dyn-unit-val" id="' . $this->h($colDb) . '_val" data-target-col="' . $this->h($colDb) . '" value="<?= esc($uNum) ?>" style="' . $roStyle . '"' . $roAttr . $exclAttr . $dynStdAttr . $rgAttr . '>';
                $out    .= '<select class="form-select dyn-unit-sel" id="' . $this->h($colDb) . '_unit" data-target-col="' . $this->h($colDb) . '" style="max-width: 88px; padding-left: 6px; padding-right: 20px;">';
                foreach ($units as $u) {
                    $uName = (string)($u['unit'] ?? '');
                    $uMin  = ($u['min'] !== null && $u['min'] !== '') ? (string)$u['min'] : '';
                    $uMax  = ($u['max'] !== null && $u['max'] !== '') ? (string)$u['max'] : '';
                    $out  .= '<option value="' . $this->h($uName) . '" data-min="' . $this->h($uMin) . '" data-max="' . $this->h($uMax) . '" <?= ($uCur === ' . $this->phpStr($uName) . ') ? \'selected\' : \'\' ?>>' . $this->h($uName) . '</option>';
                }
                $out .= '</select></div>';
                return $out;
            }

            $attrs = '';
            if ($min !== null) {
                $attrs .= ' data-min="' . $min . '" data-orig-min="' . $min . '"';
            }
            if ($max !== null) {
                $attrs .= ' data-max="' . $max . '" data-orig-max="' . $max . '"';
            }
            if ($colHeaderName !== '') {
                $attrs .= ' data-col-name="' . $this->h($colHeaderName) . '"';
            }
            if (!empty($leaf['posisi_grid'])) {
                $attrs .= ' data-grid-pos="' . $this->h($leaf['posisi_grid']) . '"';
            }
            if (!empty($leaf['nama_parameter'])) {
                $attrs .= ' data-param-name="' . $this->h($leaf['nama_parameter']) . '"';
            }

            $legacyEvents = '';
            if ($kategoriForm === 'production' && ($colDb === 'par013' || $colDb === 'par014')) {
                $legacyEvents = ' oninput="if(typeof quantityChange1===\'function\') quantityChange1();" onchange="if(typeof quantityChange1===\'function\') quantityChange1();"';
            }

            $stdLabel = '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;"></small>';
            if ($rgJson !== '') {
                $decodedRg = json_decode($rgJson, true);
                if (is_array($decodedRg)) {
                    $rgParts = [];
                    foreach ($decodedRg as $dr) {
                        $rgParts[] = ($dr['min'] ?? '') . ' ~ ' . ($dr['max'] ?? '');
                    }
                    $stdLabel = '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;">Std: ' . $this->h(implode(' / ', $rgParts)) . '</small>';
                }
            } elseif ($min !== null || $max !== null) {
                $minLbl   = ($min !== null) ? (string)$min : '-∞';
                $maxLbl   = ($max !== null) ? (string)$max : '∞';
                $stdLabel = '<small class="dyn-std-label text-muted d-block mt-1" style="font-size:10px;">Std: ' . $this->h($minLbl . ' ~ ' . $maxLbl) . '</small>';
            }

            return '<input type="number" step="' . $this->h($stepVal) . '" class="form-control text-center dyn-input-el dyn-number-input" '
                . 'style="min-width: 95px; ' . $roStyle . '" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc(' . $valExpr . ') ?>"'
                . $attrs . $exclAttr . $dynStdAttr . $formulaAttr . $rgAttr . $roAttr . $legacyEvents . '>' . $stdLabel;
        }

        return '<input type="text" class="form-control dyn-input-el" style="min-width: 110px; ' . $roStyle . '" id="' . $this->h($colDb) . '" name="' . $this->h($colDb) . '" value="<?= esc(' . $valExpr . ') ?>"' . $roAttr . $exclAttr . '>';
    }

    private function getInputValidationScript(string $processCode): string
    {
        $idJs = addcslashes($processCode, "'\\");
        return <<<JS
<script>
(function() {
    let container = document.getElementById('{$idJs}');
    if (!container) return;

    function checkInputMinMax(el, triggerAlert = false) {
        if (!el || el.value === '' || el.value === '-' || el.disabled) {
            el.dataset.hasError = "0";
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
            if (typeof syncErrorCount === 'function') syncErrorCount();
            return;
        }
        let val = parseFloat(el.value);
        let rentangGandaAttr = el.getAttribute('data-rentang-ganda');

        if (rentangGandaAttr && rentangGandaAttr.trim() !== '') {
            let parts = [];
            try {
                if (rentangGandaAttr.startsWith('[') || rentangGandaAttr.startsWith('{')) {
                    let parsed = JSON.parse(rentangGandaAttr);
                    if (Array.isArray(parsed) && parsed.length >= 2) {
                        parts = [parseFloat(parsed[0].min), parseFloat(parsed[0].max), parseFloat(parsed[1].min), parseFloat(parsed[1].max)];
                    }
                } else {
                    parts = rentangGandaAttr.split(',').map(s => parseFloat(s.trim()));
                }
            } catch (e) {}

            if (parts.length === 4 && !parts.some(isNaN)) {
                let [min1, max1, min2, max2] = parts;
                let isValid = (!isNaN(val) && ((val >= min1 && val <= max1) || (val >= min2 && val <= max2)));
                el.dataset.hasError = isValid ? "0" : "1";
                el.style.backgroundColor = isValid ? (el.hasAttribute('readonly') ? '#e9ecef' : '') : '#ffcccc';
                if (!isValid && triggerAlert && typeof changeRange2 === 'function' && el.id) {
                    changeRange2(el.id, min1, max1, min2, max2);
                }
                if (typeof syncErrorCount === 'function') syncErrorCount();
                return;
            }
        }

        let minAttr = el.getAttribute('data-min');
        let maxAttr = el.getAttribute('data-max');
        let min = (minAttr !== null && minAttr !== '') ? parseFloat(minAttr) : -999999999;
        let max = (maxAttr !== null && maxAttr !== '') ? parseFloat(maxAttr) : 999999999;

        if (isNaN(val) || val < min || val > max) {
            el.dataset.hasError = "1";
            el.style.backgroundColor = '#ffcccc';
        } else {
            el.dataset.hasError = "0";
            el.style.backgroundColor = el.hasAttribute('readonly') ? '#e9ecef' : '';
        }

        if (typeof syncErrorCount === 'function') syncErrorCount();
        if (triggerAlert && typeof changeRange === 'function' && el.id && (min > -999999999 || max < 999999999)) {
            changeRange(el.id, min, max);
        }
    }

    function syncUnitInputGroup(colId, isFinishedTyping = false) {
        let valEl = document.getElementById(colId + '_val');
        let unitEl = document.getElementById(colId + '_unit');
        let hiddenEl = document.getElementById(colId);
        if (!valEl || !unitEl || !hiddenEl) return;

        let selectedOpt = unitEl.options[unitEl.selectedIndex];
        if (selectedOpt) {
            valEl.setAttribute('data-min', selectedOpt.getAttribute('data-min') || '');
            valEl.setAttribute('data-max', selectedOpt.getAttribute('data-max') || '');
        }
        hiddenEl.value = (valEl.value !== '') ? (valEl.value + ' ' + unitEl.value) : '';
        if (isFinishedTyping) checkInputMinMax(valEl, true);
    }

    function enforceExclusiveGroups() {
        let groups = {};
        container.querySelectorAll('[data-exclusive-group]').forEach(function(el) {
            let grp = el.getAttribute('data-exclusive-group');
            if (!groups[grp]) groups[grp] = [];
            groups[grp].push(el);
        });
        Object.keys(groups).forEach(function(grp) {
            let items = groups[grp];
            let filledItem = items.find(el => el.value !== '' && el.value !== '-');
            items.forEach(function(el) {
                let unitSibling = el.id.endsWith('_val') ? document.getElementById(el.id.replace('_val', '_unit')) : null;
                if (filledItem && el !== filledItem) {
                    el.disabled = true;
                    el.style.backgroundColor = '#e2e8f0';
                    if (unitSibling) unitSibling.disabled = true;
                } else {
                    el.disabled = false;
                    if (el.value === '' && !el.hasAttribute('readonly')) el.style.backgroundColor = '';
                    if (unitSibling) unitSibling.disabled = false;
                }
            });
        });
    }

    function recalculateFormulas() {
        container.querySelectorAll('[data-formula-type]').forEach(function(targetEl) {
            let fType = targetEl.getAttribute('data-formula-type');
            let inEl = document.getElementById(targetEl.getAttribute('data-in-col'));
            let outEl = document.getElementById(targetEl.getAttribute('data-out-col'));
            if (!inEl || !outEl) return;
            let inVal = parseFloat(inEl.value);
            let outVal = parseFloat(outEl.value);
            if (isNaN(inVal) || isNaN(outVal)) {
                targetEl.value = '';
                targetEl.style.backgroundColor = '#e9ecef';
                return;
            }
            if (fType === 'ng') {
                let ng = inVal - outVal;
                targetEl.value = Number.isInteger(ng) ? ng : parseFloat(ng.toFixed(4));
                targetEl.style.backgroundColor = (ng < 0) ? '#ffcccc' : '#e9ecef';
            } else if (fType === 'yield') {
                targetEl.value = (inVal <= 0) ? 0 : parseFloat(((outVal / inVal) * 100).toFixed(2));
                targetEl.style.backgroundColor = (outVal > inVal || outVal < 0) ? '#ffcccc' : '#e9ecef';
            }
        });
    }

    function evaluateAutoJudgments() {
        let autoJudgInputs = container.querySelectorAll('.dyn-auto-judgment');
        if (autoJudgInputs.length === 0) return;
        let isStandby = (document.getElementById('is-standby') && document.getElementById('is-standby').checked);

        autoJudgInputs.forEach(function(judgInput) {
            if (isStandby) {
                judgInput.value = '-';
                judgInput.style.backgroundColor = '#e9ecef';
                return;
            }
            let targetFilter = (judgInput.getAttribute('data-target-eval') || '*').trim();
            let targetCols = (targetFilter === '*' || targetFilter === '') ? null : targetFilter.split(',').map(s => s.trim().toLowerCase());
            let hasNg = false, allFilled = true, inputsToEvaluate = [];

            container.querySelectorAll('.dyn-input-el').forEach(function(inp) {
                if (inp.classList.contains('dyn-auto-judgment')) return;
                let colName = (inp.getAttribute('name') || inp.id || '').toLowerCase();
                if (colName.endsWith('_unit')) return;
                if (targetCols && !targetCols.includes(colName)) return;
                inputsToEvaluate.push(inp);
            });

            inputsToEvaluate.forEach(function(inp) {
                if (inp.disabled) return;
                let val = (inp.value || '').trim();
                if (inp.dataset.hasError === '1' || inp.style.backgroundColor === '#ffcccc' || inp.style.backgroundColor === 'rgb(255, 204, 204)') hasNg = true;
                if (val === '✖' || val === 'X' || val.toUpperCase().indexOf('NG') !== -1) hasNg = true;
                if (!inp.getAttribute('data-exclusive-group') && val === '' && !inp.hasAttribute('readonly')) allFilled = false;
            });

            if (hasNg) {
                judgInput.value = '✖';
                judgInput.style.backgroundColor = '#f8d7da';
                judgInput.style.color = '#721c24';
            } else if (allFilled && inputsToEvaluate.length > 0) {
                judgInput.value = '✔';
                judgInput.style.backgroundColor = '#d1e7dd';
                judgInput.style.color = '#0f5132';
            } else {
                judgInput.value = '';
                judgInput.style.backgroundColor = '#f8f9fa';
            }
        });
    }

    container.querySelectorAll('.dyn-std-selector').forEach(function(sel) {
        function updateStdGroup() {
            let domId = sel.getAttribute('data-dom-id');
            let dbCol = sel.getAttribute('data-db-col');
            let opt = sel.options[sel.selectedIndex];
            let minVal = opt ? opt.getAttribute('data-min') : '';
            let lblSpan = document.getElementById('lbl_' + domId);
            let hiddenStd = document.getElementById(dbCol);
            if (lblSpan) lblSpan.textContent = minVal || '-';
            if (hiddenStd) hiddenStd.value = minVal || '';
            container.querySelectorAll('[data-dynstd-target="' + domId + '"]').forEach(function(inp) {
                inp.setAttribute('data-min', minVal || '');
                if (inp.value !== '') checkInputMinMax(inp, false);
            });
            evaluateAutoJudgments();
        }
        sel.addEventListener('change', updateStdGroup);
        if (sel.value !== '') updateStdGroup();
    });

    container.querySelectorAll('.dyn-unit-val, .dyn-unit-sel').forEach(function(el) {
        let colId = el.getAttribute('data-target-col');
        el.addEventListener('input', function() { syncUnitInputGroup(colId, false); enforceExclusiveGroups(); evaluateAutoJudgments(); });
        el.addEventListener('change', function() { syncUnitInputGroup(colId, true); enforceExclusiveGroups(); evaluateAutoJudgments(); });
        syncUnitInputGroup(colId, false);
    });

    container.querySelectorAll('.dyn-number-input').forEach(function(el) {
        el.addEventListener('input', function() {
            if (el.value === '' && !el.hasAttribute('readonly')) { el.style.backgroundColor = ''; el.dataset.hasError = '0'; }
            enforceExclusiveGroups(); recalculateFormulas(); evaluateAutoJudgments();
        });
        el.addEventListener('change', function() {
            checkInputMinMax(el, true); enforceExclusiveGroups(); recalculateFormulas(); evaluateAutoJudgments();
        });
    });

    container.querySelectorAll('.dyn-input-el:not(.dyn-number-input):not(.dyn-unit-val)').forEach(function(el) {
        el.addEventListener('change', function() { enforceExclusiveGroups(); evaluateAutoJudgments(); });
        el.addEventListener('input', function() { enforceExclusiveGroups(); evaluateAutoJudgments(); });
    });

    enforceExclusiveGroups();
    recalculateFormulas();
    evaluateAutoJudgments();
})();
</script>
JS;
    }

    // =========================================================================
    // 2. GENERATOR HARD CODE HISTORY (.php)
    // =========================================================================

    public function generateHistoryCode(array $revision, array $treeParams, string $processCode, string $procTitle, string $kategoriForm, string $device, string $folderCat): string
    {
        $revNum       = str_pad((string)($revision['revisi'] ?? '0'), 2, '0', STR_PAD_LEFT);
        $genTime      = date('Y-m-d H:i:s');
        $preparedTree = $this->prepareHistoryTree($treeParams);

        $lines   = [];
        $lines[] = '<?php';
        $lines[] = '/**';
        $lines[] = " * HARD CODE TABEL HISTORY - " . strtoupper($processCode) . " (Revisi {$revNum})";
        $lines[] = " * Kategori      : {$kategoriForm}";
        $lines[] = " * Lokasi Target : app/Views/layout/{$device}/history/{$folderCat}/{$processCode}.php";
        $lines[] = " * Digenerate    : {$genTime}";
        $lines[] = ' */';
        $lines[] = $this->getHistoryHelperPhpBlock();
        $lines[] = '$isExportMode   = !empty($is_export);';
        $lines[] = '$showFinishCols = !empty($show_finish_cols);';
        $lines[] = '$lenData        = count($alldata ?? []);';
        $lines[] = '?>';
        $lines[] = '<div class="table-responsive mt-3" id="' . $this->h($processCode) . '">';
        $lines[] = '    <?= csrf_field(); ?>';
        $lines[] = '    <input type="hidden" id="cnt-table" value="1">';

        if ($kategoriForm === 'startup') {
            $maxDepth = 2;
            foreach ($preparedTree as $n) {
                $d = $this->getSubtreeDepth($n);
                if ($d > $maxDepth) {
                    $maxDepth = $d;
                }
            }
            $identityCols = $maxDepth + 1;

            $verticalRows = [];
            $rowIndex     = 0;
            $this->buildStartupRows($preparedTree, 0, $maxDepth, $verticalRows, $rowIndex, true, true);

            $noteDbCol = '';
            foreach ($preparedTree as $n) {
                if ((stripos($n['nama_parameter'] ?? '', 'note') !== false || stripos($n['nama_parameter'] ?? '', 'catatan') !== false) && !empty($n['kolom_db'])) {
                    $noteDbCol = $n['kolom_db'];
                    break;
                }
            }

            $lines[] = '    <?php $dataColsSpan = max(1, $lenData); ?>';
            $lines[] = '    <table class="table table-bordered table-hover" id="table1">';
            $lines[] = '        <thead align="center">';
            $lines[] = '            <tr>';
            $lines[] = '                <th colspan="<?= ' . $identityCols . ' + $dataColsSpan ?>" scope="col">' . $this->h(strtoupper($procTitle)) . ' (REVISI ' . $this->h($revNum) . ')</th>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <th scope="col" rowspan="2">No</th>';
            $lines[] = '                <th scope="col" colspan="' . max(1, $maxDepth - 1) . '" rowspan="2">Start Up Check Item</th>';
            $lines[] = '                <th scope="col" rowspan="2">Standard</th>';
            $lines[] = '                <th scope="col" colspan="<?= $dataColsSpan ?>">Hasil Start Up Check</th>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <?php if ($lenData > 0): ?>';
            $lines[] = '                    <?php for ($i = 0; $i < $lenData; $i++): ?>';
            $lines[] = '                        <td><?= (!empty($alldata[$i][\'created_at\']) && $alldata[$i][\'created_at\'] !== \'-\') ? date(\'d/m/Y\', strtotime((string)$alldata[$i][\'created_at\'])) : \'-\' ?></td>';
            $lines[] = '                    <?php endfor; ?>';
            $lines[] = '                <?php else: ?>';
            $lines[] = '                    <td>-</td>';
            $lines[] = '                <?php endif; ?>';
            $lines[] = '            </tr>';
            $lines[] = '        </thead>';
            $lines[] = '        <tbody>';

            foreach ($verticalRows as $r) {
                $lines[] = '            <tr>';
                foreach ($r as $cell) {
                    if ($cell['type'] === 'no') {
                        $lines[] = '                <td rowspan="' . (int)$cell['rowspan'] . '" align="center">' . (int)$cell['no'] . '</td>';
                    } elseif ($cell['type'] === 'param') {
                        $lines[] = '                <td rowspan="' . (int)$cell['rowspan'] . '" colspan="' . (int)$cell['colspan'] . '">' . $this->h($cell['name']) . '</td>';
                    } elseif ($cell['type'] === 'input') {
                        $cellExpr = $this->compileHistoryCellExpr('$alldata[$i]', $cell['node_data']);
                        $lines[]  = '                <?php if ($lenData > 0): ?>';
                        $lines[]  = '                    <?php for ($i = 0; $i < $lenData; $i++): ?>';
                        $lines[]  = '                        <td align="center"><?= ' . $cellExpr . ' ?></td>';
                        $lines[]  = '                    <?php endfor; ?>';
                        $lines[]  = '                <?php else: ?>';
                        $lines[]  = '                    <td align="center">-</td>';
                        $lines[]  = '                <?php endif; ?>';
                    }
                }
                $lines[] = '            </tr>';
            }

            // Footer rows Startup: Judgment, Operator, Checked, Note #
            $lines[] = '            <tr>';
            $lines[] = '                <td colspan="' . $identityCols . '"><strong>Judgment Start Up *</strong></td>';
            $lines[] = '                <?php if ($lenData > 0): for ($i = 0; $i < $lenData; $i++): ?>';
            $lines[] = '                    <td align="center"><?= hcFormatStartupJudgment($alldata[$i], $isExportMode) ?></td>';
            $lines[] = '                <?php endfor; else: ?><td align="center">-</td><?php endif; ?>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <td colspan="' . $identityCols . '"><strong>Operator</strong></td>';
            $lines[] = '                <?php if ($lenData > 0): for ($i = 0; $i < $lenData; $i++): ?>';
            $lines[] = '                    <td align="center"><?= esc(hcResolveEmpId($alldata[$i][\'empid\'] ?? $alldata[$i][\'name\'] ?? $alldata[$i][\'operator\'] ?? \'-\')) ?></td>';
            $lines[] = '                <?php endfor; else: ?><td align="center">-</td><?php endif; ?>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <td colspan="' . $identityCols . '"><strong>Checked</strong></td>';
            $lines[] = '                <?php if ($lenData > 0): for ($i = 0; $i < $lenData; $i++): ?>';
            $lines[] = '                    <td align="center"><?= esc(hcFormatCheckedEmpId($alldata[$i] ?? null)) ?></td>';
            $lines[] = '                <?php endfor; else: ?><td align="center">-</td><?php endif; ?>';
            $lines[] = '            </tr>';
            $lines[] = '            <tr>';
            $lines[] = '                <td colspan="' . $identityCols . '">Note #</td>';
            $lines[] = '                <?php if ($lenData > 0): for ($i = 0; $i < $lenData; $i++): $nv = $alldata[$i][\'note\'] ?? ' . ($noteDbCol !== '' ? '$alldata[$i][' . $this->phpStr($noteDbCol) . '] ?? ' : '') . '$alldata[$i][\'par030\'] ?? $alldata[$i][\'par023\'] ?? \'-\'; ?>';
            $lines[] = '                    <td align="center"><?= esc($nv !== \'\' ? $nv : \'-\') ?></td>';
            $lines[] = '                <?php endfor; else: ?><td align="center">-</td><?php endif; ?>';
            $lines[] = '            </tr>';
            $lines[] = '        </tbody>';
            $lines[] = '    </table>';
        } else {
            $headerMaxDepth = 1;
            foreach ($preparedTree as $node) {
                $d = $this->getSubtreeDepth($node, true);
                if ($d > $headerMaxDepth) {
                    $headerMaxDepth = $d;
                }
            }

            $headerRows   = [];
            $headerLeaves = [];
            $this->buildHorizontalHeaders($preparedTree, 0, $headerRows, $headerLeaves, $headerMaxDepth, true);
            ksort($headerRows);

            $gridRowNames     = [];
            $firstGridLeafIdx = null;
            $hasSuffixCols    = false;
            $gridHeaderLabel  = ($kategoriForm === 'foregoing' || str_contains($processCode, '-f-')) ? 'Measure Die Bond Pos' : 'Sample / Label';

            foreach ($headerLeaves as $hIdx => $hLeaf) {
                if (!empty($hLeaf['children']) && !empty($hLeaf['children'][0]['posisi_grid'])) {
                    if ($firstGridLeafIdx === null) {
                        $firstGridLeafIdx = $hIdx;
                    }
                    foreach ($hLeaf['children'] as $gc) {
                        if (!empty($gc['standar_nilai'])) {
                            $gridHeaderLabel = $gc['standar_nilai'];
                        }
                        if (!empty($gc['posisi_grid']) && !in_array($gc['posisi_grid'], $gridRowNames)) {
                            $gridRowNames[] = $gc['posisi_grid'];
                        }
                        if (!empty($gc['kolom_db']) && preg_match('/^par\d+[a-z]$/i', $gc['kolom_db'])) {
                            $hasSuffixCols = true;
                        }
                    }
                }
            }

            $hasTimeInTree = false;
            foreach ($headerLeaves as $hl) {
                if (in_array($hl['kolom_db'] ?? '', ['par008', 'par009'], true)) {
                    $hasTimeInTree = true;
                    break;
                }
            }
            $showAutoTimeCols = ($kategoriForm === 'production' && !$hasTimeInTree);
            $baseMetaCols     = ($kategoriForm === 'startup_horizontal') ? 6 : 7;
            $extraColsStatic  = ($showAutoTimeCols ? 2 : 0) + (!empty($gridRowNames) ? 1 : 0) + count($headerLeaves);

            if ($kategoriForm === 'production') {
                $lines[] = '    <?php $totalTableCols = ($showFinishCols ? 9 : 7) + ' . $extraColsStatic . '; ?>';
            } else {
                $lines[] = '    <?php $totalTableCols = ' . ($baseMetaCols + $extraColsStatic) . '; ?>';
            }

            $lines[] = '    <table class="table table-bordered table-hover" id="table1">';
            $lines[] = '        <thead align="center">';
            $lines[] = '            <tr>';
            $lines[] = '                <th colspan="<?= $totalTableCols ?>" scope="col">' . $this->h(strtoupper($procTitle)) . ' (REVISI ' . $this->h($revNum) . ')</th>';
            $lines[] = '            </tr>';

            foreach ($headerRows as $level => $row) {
                $lines[] = '            <tr>';
                if ($level === 0) {
                    if ($kategoriForm === 'production') {
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">No.</th>';
                        $lines[] = '                <?php if ($showFinishCols): ?>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Operator Start</th>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Shift/Group</th>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Operator Finish</th>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Shift/Group</th>';
                        $lines[] = '                <?php else: ?>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Operator</th>';
                        $lines[] = '                    <th rowspan="' . $headerMaxDepth . '">Shift/Group</th>';
                        $lines[] = '                <?php endif; ?>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Mch. No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Model</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Lot No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Date (dd/mm/yyyy)</th>';
                        if ($showAutoTimeCols) {
                            $lines[] = '                <th colspan="2" rowspan="' . max(1, $headerMaxDepth - 1) . '">Time</th>';
                        }
                    } elseif ($kategoriForm === 'startup_horizontal') {
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Date</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Mch. No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Shift/Group</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Operator</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Status Approval</th>';
                    } else {
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Mch. No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Model</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Lot No.</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Operator</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Shift/Group</th>';
                        $lines[] = '                <th rowspan="' . $headerMaxDepth . '">Date (dd/mm/yyyy)</th>';
                    }
                } elseif ($showAutoTimeCols && $level === $headerMaxDepth - 1) {
                    $lines[] = '                <th>IN</th>';
                    $lines[] = '                <th>OUT</th>';
                }

                $runningCol    = 0;
                $labelInserted = false;
                foreach ($row as $col) {
                    if (!empty($gridRowNames) && $level === 0 && !$labelInserted && $runningCol === $firstGridLeafIdx) {
                        $labelInserted = true;
                        $lines[]       = '                <th rowspan="' . $headerMaxDepth . '">' . $this->h($gridHeaderLabel) . '</th>';
                    }
                    $lines[] = '                <th colspan="' . (int)$col['colspan'] . '" rowspan="' . (int)$col['rowspan'] . '">' . $this->h($col['name']) . '</th>';
                    $runningCol += (int)$col['colspan'];
                }
                $lines[] = '            </tr>';
            }

            $lines[] = '        </thead>';
            $lines[] = '        <tbody>';
            $lines[] = '            <?php if ($lenData === 0): ?>';
            $lines[] = '                <tr><?php for ($c = 0; $c < $totalTableCols; $c++): ?><td align="center">-</td><?php endfor; ?></tr>';
            $lines[] = '            <?php else: ?>';

            if (empty($gridRowNames)) {
                $lines[] = '                <?php for ($i = 0; $i < $lenData; $i++): $row = $alldata[$i]; ?>';
                $lines[] = '                    <tr>';
                $lines[] = '                        <td align="center"><?= $i + 1 ?></td>';
                $this->appendHistoryMetaCells($lines, '$row', $kategoriForm, $showAutoTimeCols, 1, '                        ');
                foreach ($headerLeaves as $leaf) {
                    $cellExpr = $this->compileHistoryCellExpr('$row', $leaf);
                    $lines[]  = '                        <td align="center"><?= ' . $cellExpr . ' ?></td>';
                }
                $lines[] = '                    </tr>';
                $lines[] = '                <?php endfor; ?>';
            } else {
                $gridCount = count($gridRowNames);
                $lines[]   = '                <?php';
                $lines[]   = '                $isSplitRows = (' . ($hasSuffixCols ? 'true' : 'false') . ' && !isset($alldata[0][\'par001a\']) && !isset($alldata[0][\'par013a\']) && !isset($alldata[0][\'par021a\']));';
                $lines[]   = '                $step   = $isSplitRows ? ' . max(1, $gridCount) . ' : 1;';
                $lines[]   = '                $noUrut = 1;';
                $lines[]   = '                ?>';
                $lines[]   = '                <?php for ($i = 0; $i < $lenData; $i += $step): $baseRow = $alldata[$i]; ?>';

                foreach ($gridRowNames as $rIdx => $rowName) {
                    $lines[] = '                    <?php $row_' . $rIdx . ' = ($isSplitRows && isset($alldata[$i + ' . $rIdx . '])) ? $alldata[$i + ' . $rIdx . '] : $baseRow; ?>';
                    $lines[] = '                    <tr>';
                    if ($rIdx === 0) {
                        $lines[] = '                        <td rowspan="' . $gridCount . '" align="center"><?= $noUrut++ ?></td>';
                        $this->appendHistoryMetaCells($lines, '$baseRow', $kategoriForm, $showAutoTimeCols, $gridCount, '                        ');
                    }

                    foreach ($headerLeaves as $hIdx => $hLeaf) {
                        if ($hIdx === $firstGridLeafIdx) {
                            $lines[] = '                        <th scope="row" align="center">' . $this->h($rowName) . '</th>';
                        }

                        if (empty($hLeaf['children'])) {
                            if ($rIdx === 0) {
                                $cellExpr = $this->compileHistoryCellExpr('$baseRow', $hLeaf);
                                $lines[]  = '                        <td rowspan="' . $gridCount . '" align="center"><?= ' . $cellExpr . ' ?></td>';
                            }
                        } else {
                            $matchedCell = null;
                            foreach ($hLeaf['children'] as $gChild) {
                                if ($gChild['posisi_grid'] === $rowName) {
                                    $matchedCell = $gChild;
                                    break;
                                }
                            }
                            if ($matchedCell) {
                                $cellExpr = $this->compileHistoryCellExpr('$row_' . $rIdx, $matchedCell, true);
                                $lines[]  = '                        <td align="center"><?= ' . $cellExpr . ' ?></td>';
                            } else {
                                $lines[] = '                        <td align="center">-</td>';
                            }
                        }
                    }

                    $lines[] = '                    </tr>';
                }

                $lines[] = '                <?php endfor; ?>';
            }

            $lines[] = '            <?php endif; ?>';
            $lines[] = '        </tbody>';
            $lines[] = '    </table>';
        }

        $lines[] = '</div>';

        return implode("\n", $lines) . "\n";
    }

    private function appendHistoryMetaCells(array &$lines, string $rowVar, string $kategoriForm, bool $showAutoTimeCols, int $rowspan, string $indent): void
    {
        $rsAttr = $rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '';

        if ($kategoriForm === 'production') {
            $lines[] = $indent . '<?php $meta = hcExtractProdMeta(' . $rowVar . '); ?>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc($meta[\'op_start\']) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc($meta[\'sg_start\']) ?></td>';
            $lines[] = $indent . '<?php if ($showFinishCols): ?>';
            $lines[] = $indent . '    <td' . $rsAttr . ' align="center"><?= esc($meta[\'op_finish\']) ?></td>';
            $lines[] = $indent . '    <td' . $rsAttr . ' align="center"><?= esc($meta[\'sg_finish\']) ?></td>';
            $lines[] = $indent . '<?php endif; ?>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'machno\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'model\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'lotno\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= $meta[\'date_str\'] ?></td>';
            if ($showAutoTimeCols) {
                $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= $meta[\'time_in\'] ?></td>';
                $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= $meta[\'time_out\'] ?></td>';
            }
        } elseif ($kategoriForm === 'startup_horizontal') {
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= (!empty(' . $rowVar . '[\'created_at\']) && ' . $rowVar . '[\'created_at\'] !== \'-\') ? date(\'d/m/Y\', strtotime((string)' . $rowVar . '[\'created_at\'])) : \'-\' ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'machno\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((' . $rowVar . '[\'shift\'] ?? \'-\') . \' / \' . (' . $rowVar . '[\'group\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . '><?= esc(hcResolveEmpId(' . $rowVar . '[\'empid\'] ?? ' . $rowVar . '[\'name\'] ?? ' . $rowVar . '[\'operator\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'status\'] ?? \'-\')) ?></td>';
        } else {
            $lines[] = $indent . '<?php $metaFg = hcExtractProdMeta(' . $rowVar . '); ?>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'machno\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'model\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc((string)(' . $rowVar . '[\'lotno\'] ?? \'-\')) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc($metaFg[\'op_start\']) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= esc($metaFg[\'sg_start\']) ?></td>';
            $lines[] = $indent . '<td' . $rsAttr . ' align="center"><?= $metaFg[\'date_str\'] ?></td>';
        }
    }

    private function compileHistoryCellExpr(string $rowVar, array $leaf, bool $fallbackStripSuffix = false): string
    {
        $tipe  = $leaf['tipe_input'] ?? '';
        $colDb = trim((string)($leaf['kolom_db'] ?? ''));

        if ($tipe === 'header_only' || $colDb === '') {
            $stdVal = trim((string)($leaf['standar_nilai'] ?? ''));
            return $stdVal !== '' ? 'nl2br(esc(' . $this->phpStr($stdVal) . '))' : '\'-\'';
        }

        $valGetter = $rowVar . '[' . $this->phpStr($colDb) . '] ?? \'\'';
        if ($fallbackStripSuffix && preg_match('/^(par\d+)[a-z]$/i', $colDb, $mBase)) {
            $baseKey   = strtolower($mBase[1]);
            $valGetter = $rowVar . '[' . $this->phpStr($colDb) . '] ?? ' . $rowVar . '[' . $this->phpStr($baseKey) . '] ?? \'\'';
        }

        if ($tipe === 'std_display') {
            $suffix = $leaf['std_suffix'] ?? ' N';
            return 'hcFormatStdDisplay(' . $valGetter . ', ' . $this->phpStr($suffix) . ')';
        }

        if ($tipe === 'auto_judgment') {
            return 'hcFormatAutoJudgment(' . $valGetter . ', $isExportMode)';
        }

        if ($tipe === 'number') {
            $min   = ($leaf['batas_min'] !== null && $leaf['batas_min'] !== '') ? (float)$leaf['batas_min'] : 'null';
            $max   = ($leaf['batas_max'] !== null && $leaf['batas_max'] !== '') ? (float)$leaf['batas_max'] : 'null';
            $rgRaw = !empty($leaf['rentang_ganda']) ? (is_string($leaf['rentang_ganda']) ? $leaf['rentang_ganda'] : json_encode($leaf['rentang_ganda'], JSON_UNESCAPED_UNICODE)) : '';
            $uRaw  = !empty($leaf['satuan_opsi']) ? (is_string($leaf['satuan_opsi']) ? $leaf['satuan_opsi'] : json_encode($leaf['satuan_opsi'], JSON_UNESCAPED_UNICODE)) : '';
            if ($rgRaw !== '' || $uRaw !== '') {
                return 'hcFormatNumberCell(' . $valGetter . ', ' . $min . ', ' . $max . ', $isExportMode, ' . $this->phpStr($rgRaw) . ', ' . $this->phpStr($uRaw) . ')';
            }
            return 'hcFormatNumberCell(' . $valGetter . ', ' . $min . ', ' . $max . ', $isExportMode)';
        }

        return '(($v = trim((string)(' . $valGetter . '))) !== \'\' ? esc($v) : \'-\')';
    }

    private function getHistoryHelperPhpBlock(): string
    {
        return <<<'PHP'
if (!function_exists('hcResolveEmpId')) {
    function hcResolveEmpId($val): string {
        static $empMap = null;
        $clean = trim((string)$val);
        if ($clean === '' || $clean === '-') return '-';
        if (function_exists('histResolveEmpId')) return histResolveEmpId($clean);

        $roleSuffix = '';
        if (preg_match('/^(.*?)\s*(\(.*?\))$/', $clean, $m)) {
            $clean = trim($m[1]);
            $roleSuffix = ' ' . trim($m[2]);
        }
        if (preg_match('/^[0-9A-Za-z_-]{3,12}$/', $clean) && preg_match('/\d/', $clean)) {
            return $clean . $roleSuffix;
        }
        if ($empMap === null) {
            $empMap = [];
            try {
                if (extension_loaded('sqlsrv') && function_exists('sqlsrv_connect')) {
                    $db2 = \Config\Database::connect('second');
                    foreach ($db2->query("SELECT empid, firstname, midname, lastname FROM tbempinfa")->getResultArray() as $r) {
                        $eid = trim((string)($r['empid'] ?? ''));
                        if ($eid === '') continue;
                        $fn = strtoupper(trim((string)($r['firstname'] ?? '')));
                        $mn = strtoupper(trim((string)($r['midname'] ?? '')));
                        $ln = strtoupper(trim((string)($r['lastname'] ?? '')));
                        $full = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $mn $ln")));
                        if ($full !== '') $empMap[$full] = $eid;
                        $fl = strtoupper(trim(preg_replace('/\s+/', ' ', "$fn $ln")));
                        if ($fl !== '' && !isset($empMap[$fl])) $empMap[$fl] = $eid;
                        if ($fn !== '' && !isset($empMap[$fn])) $empMap[$fn] = $eid;
                    }
                }
            } catch (\Throwable $e) {}
            try {
                $db = \Config\Database::connect('default');
                if ($db->tableExists('users')) {
                    foreach ($db->table('users')->select('username, name')->get()->getResultArray() as $ur) {
                        $uname = trim((string)($ur['username'] ?? ''));
                        $fn = strtoupper(trim((string)($ur['name'] ?? '')));
                        if ($uname !== '' && $fn !== '' && !isset($empMap[$fn])) $empMap[$fn] = $uname;
                    }
                }
            } catch (\Throwable $e) {}
        }
        $key = strtoupper((string)preg_replace('/\s+/', ' ', $clean));
        return ($empMap[$key] ?? $clean) . $roleSuffix;
    }
}

if (!function_exists('hcFormatCheckedEmpId')) {
    function hcFormatCheckedEmpId($row): string {
        if (!is_array($row)) return '-';
        $st = strtolower(trim((string)($row['status'] ?? '')));
        if ($st === '' || $st === '-' || (strpos($st, 'approve') === false && strpos($st, 'bypass') === false && strpos($st, 'check') === false)) {
            return '-';
        }
        if (!empty($row['supervisor']) && $row['supervisor'] !== '-') return hcResolveEmpId($row['supervisor']) . ' (Supervisor)';
        if (!empty($row['leader']) && $row['leader'] !== '-') return hcResolveEmpId($row['leader']) . ' (Leader)';
        if (!empty($row['foreman']) && $row['foreman'] !== '-') return hcResolveEmpId($row['foreman']) . ' (Foreman)';
        return '-';
    }
}

if (!function_exists('hcExtractProdMeta')) {
    function hcExtractProdMeta(array $r): array {
        $op1Raw = !empty($r['empid']) ? $r['empid'] : (!empty($r['name']) ? $r['name'] : ($r['operator'] ?? '-'));
        $op1Emp = hcResolveEmpId($op1Raw);

        // Pertahankan logic Operator Finish & Shift/Group Finish
        $op2Raw = !empty($r['empid2']) ? $r['empid2'] : ($r['name2'] ?? '');
        $op2Emp = ($op2Raw !== '' && $op2Raw !== '-') ? hcResolveEmpId($op2Raw) : '';
        $hasOpFinish = ($op2Emp !== '' && $op2Emp !== '-');

        $shift1 = trim((string)($r['shift'] ?? ''));
        $group1 = trim((string)($r['group'] ?? ''));
        $sgStart = ($shift1 !== '' && $shift1 !== '-') ? ($shift1 . ' / ' . ($group1 !== '' ? $group1 : '-')) : ($group1 !== '' ? $group1 : '-');

        $shift2 = trim((string)($r['shift2'] ?? ''));
        $group2 = trim((string)($r['group2'] ?? ''));
        if ($shift2 === '1') $shift2 = 'A';
        elseif ($shift2 === '2') $shift2 = 'B';
        elseif ($shift2 === '3') $shift2 = 'C';

        $sgFinish = '-';
        if ($hasOpFinish && (($shift2 !== '' && $shift2 !== '-') || ($group2 !== '' && $group2 !== '-'))) {
            $sgFinish = ($shift2 !== '' && $shift2 !== '-') ? ($shift2 . ($group2 !== '' && $group2 !== '-' ? ' / ' . $group2 : '')) : $group2;
        }

        $createdRaw = trim((string)($r['created_at'] ?? ''));
        $tsCreated  = ($createdRaw !== '' && $createdRaw !== '-') ? strtotime($createdRaw) : false;
        $dateStr    = $tsCreated ? date('d/m/Y', $tsCreated) : '-';

        $timeIn  = (!empty($r['par008']) && $r['par008'] !== '-') ? date('H:i', strtotime((string)$r['par008'])) : '-';
        $timeOut = ($hasOpFinish && !empty($r['par009']) && $r['par009'] !== '-') ? date('H:i', strtotime((string)$r['par009'])) : '-';

        return [
            'op_start'  => $op1Emp,
            'sg_start'  => $sgStart,
            'op_finish' => $hasOpFinish ? $op2Emp : '-',
            'sg_finish' => $sgFinish,
            'date_str'  => $dateStr,
            'time_in'   => $timeIn,
            'time_out'  => $timeOut,
        ];
    }
}

if (!function_exists('hcFormatStdDisplay')) {
    function hcFormatStdDisplay($raw, string $suffix = ' N'): string {
        $s = trim((string)$raw);
        if ($s === '' || $s === '-') return '-';
        return stripos($s, trim($suffix)) === false ? esc($s . $suffix) : esc($s);
    }
}

if (!function_exists('hcFormatAutoJudgment')) {
    function hcFormatAutoJudgment($raw, bool $isExport = false): string {
        $s = trim((string)$raw);
        if ($s === '') return '-';
        if ($s === '✔' || $s === 'OK') return $isExport ? 'OK (✔)' : '<span class="badge bg-success text-white" style="font-size: 0.95rem;">✔</span>';
        if ($s === '✖' || $s === 'NG' || $s === 'X') return $isExport ? 'NG (✖)' : '<span class="badge bg-danger text-white" style="font-size: 0.95rem;">✖</span>';
        return esc($s);
    }
}

if (!function_exists('hcFormatNumberCell')) {
    function hcFormatNumberCell($raw, $min, $max, bool $isExport = false, string $rgJson = '', string $unitsJson = ''): string {
        $s = trim((string)$raw);
        if ($s === '' || $s === '-') return '-';
        if (!$isExport) {
            $parts = explode(' ', $s, 2);
            $unitPart = isset($parts[1]) ? trim($parts[1]) : '';
            if ($unitPart !== '' && $unitsJson !== '') {
                $units = json_decode($unitsJson, true);
                if (is_array($units)) {
                    foreach ($units as $u) {
                        if (isset($u['unit']) && strtolower(trim((string)$u['unit'])) === strtolower($unitPart)) {
                            $min = ($u['min'] !== null && $u['min'] !== '') ? (float)$u['min'] : $min;
                            $max = ($u['max'] !== null && $u['max'] !== '') ? (float)$u['max'] : $max;
                            break;
                        }
                    }
                }
            }
            if (is_numeric($parts[0])) {
                $f = (float)$parts[0];
                $out = false;
                if ($rgJson !== '') {
                    $rg = (str_starts_with($rgJson, '[') || str_starts_with($rgJson, '{')) ? json_decode($rgJson, true) : null;
                    $rgParts = (is_array($rg) && count($rg) >= 2)
                        ? [(float)$rg[0]['min'], (float)$rg[0]['max'], (float)$rg[1]['min'], (float)$rg[1]['max']]
                        : array_map('floatval', explode(',', $rgJson));
                    if (count($rgParts) === 4) {
                        $out = !(($f >= $rgParts[0] && $f <= $rgParts[1]) || ($f >= $rgParts[2] && $f <= $rgParts[3]));
                    }
                } elseif (($min !== null && $f < $min) || ($max !== null && $f > $max)) {
                    $out = true;
                }
                if ($out) {
                    return '<span style="color: #dc3545; font-weight: bold;">' . esc($s) . '</span>';
                }
            }
        }
        return esc($s);
    }
}

if (!function_exists('hcFormatStartupJudgment')) {
    function hcFormatStartupJudgment(array $row, bool $isExport = false): string {
        $judg = trim((string)($row['par025'] ?? $row['judgment'] ?? ''));
        if ($judg === '' || $judg === '-') {
            $hasNg = false;
            foreach ($row as $k => $v) {
                if (str_starts_with((string)$k, 'par')) {
                    $vu = strtoupper(trim((string)$v));
                    if ($vu === 'NG' || $vu === '✖' || $vu === 'X') { $hasNg = true; break; }
                }
            }
            $judg = $hasNg ? '✖' : '✔';
        }
        $isOk = ($judg === '✔' || strtoupper($judg) === 'OK');
        return $isOk
            ? ($isExport ? 'OK (✔)' : '<strong style="color: #198754; font-size: 1.1rem;">&#10004;</strong>')
            : ($isExport ? 'NG (✖)' : '<strong style="color: #dc3545; font-size: 1.1rem;">&#10008;</strong>');
    }
}
PHP;
    }
}
