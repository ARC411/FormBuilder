<?php

namespace App\Controllers;

use App\Models\ProductionModel;
use App\Models\LogActivityModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Production extends BaseController
{
    protected $session;
    private $ProductionModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->ProductionModel = new ProductionModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    private function safeSegment($value): string
    {
        $value = (string) $value;
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $value)) {
            throw PageNotFoundException::forPageNotFound();
        }
        return $value;
    }

    public function index()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        return view('production', [
            'title' => 'Data Production | Startup Management',
        ]);
    }

    public function datatable()
    {
        $r = $this->request;
        
        $orderData = $r->getGet('order');
        $order = (!empty($orderData) && is_array($orderData))
            ? $orderData[0]
            : ['column' => 1, 'dir' => 'desc'];
        
        $searchData = $r->getGet('search');
        $searchValue = (!empty($searchData) && isset($searchData['value']))
            ? trim($searchData['value'])
            : '';

        $startParam = max((int) $r->getGet('start'), 0);
        $lenParam = min(max((int) $r->getGet('length'), 1), 100);
        $colParam = (int) $order['column'];
        $dirParam = $order['dir'];

        $res = $this->ProductionModel->getDatatable(
            $startParam,
            $lenParam,
            $searchValue,
            $colParam,
            $dirParam
        );

        return $this->response->setJSON([
            'draw'            => (int) $r->getGet('draw'),
            'recordsTotal'    => $res['total'],
            'recordsFiltered' => $res['filtered'],
            'data'            => $res['rows'],
        ]);
    }

    public function formInputProduction()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        $device  = $this->request->getGet('device');
        $process = $this->request->getGet('process');

        if (empty($device)) {
            return "";
        }
        if (empty($process)) {
            return "";
        }

        $data = [
            'title'      => 'Input | Startup Management',
            'validation' => \Config\Services::validation(),
        ];

        $safeDev = $this->safeSegment($device);
        $safeProc = $this->safeSegment($process);
        $viewPath = '/layout/' . $safeDev . '/input/production/' . $safeProc;

        return view($viewPath, $data);
    }

    public function edit($number)
    {
        if (!$this->session->has('empid')) {
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title'      => 'History | Startup Management',
            'validation' => \Config\Services::validation(),
            'alldata'    => $this->ProductionModel->getByNumber($number),
        ];

        return view('edit', $data);
    }

    public function formEdit()
    {
        $number  = $this->request->getGet('number');
        $alldata = $this->ProductionModel->getByNumber($number);

        if (empty($alldata)) {
            return "<div class='alert alert-warning'>Data tidak ditemukan.</div>";
        }

        $device  = $alldata[0]['device'];
        $process = $alldata[0]['process'];

        $data = [
            'title'   => 'History | Startup Management',
            'alldata' => $alldata,
            'process' => $process,
            'number'  => $number,
        ];

        try {
            $viewPath = '/layout/' . $device . '/input/production/' . $process;
            return view($viewPath, $data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, form checklist (" . esc($process) . ") belum tersedia.</div>";
        }
    }

    public function deleteProduction($number)
    {
        if (!$this->session->has('empid')) {
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to(base_url('login'));
        }

        $this->ProductionModel->deleteByNumber($number);
        return "berhasil";
    }

    // UPDATE: Inject Data Operator Finish agar berjejer (Op Start -> Shift Start -> Op Finish -> Shift Finish)
    public function productionByNumber($number)
    {
        $rows = $this->ProductionModel->getByNumber($number);

        if (empty($rows)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $device  = $this->safeSegment($rows[0]['device']);
        $process = $this->safeSegment($rows[0]['process']);
        $viewPath = '/layout/' . $device . '/history/production/' . $process;

        $html = view($viewPath, ['alldata' => $rows]);

        try {
            $doc = new \DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);

            // Cek apakah tabel ini SUDAH memiliki kolom Operator Finish di thead
            $existingFinish = $xpath->query('//thead//th[contains(translate(text(), "FINISH", "finish"), "finish")]');
            if ($existingFinish->length > 0) {
                // Template sudah memiliki kolom Finish sendiri, kembalikan HTML tanpa inject ganda
                $finalHtml = $doc->saveHTML();
                $finalHtml = str_replace('<?xml encoding="UTF-8">', '', $finalHtml);
                return trim($finalHtml);
            }

            // Cari Header <th> Shift/Group (Start) di seluruh baris <thead>
            $thShift = null;
            $shiftIndex = -1;

            $trs = $xpath->query('//thead/tr');
            foreach ($trs as $tr) {
                $colIndex = 0;
                foreach ($tr->childNodes as $th) {
                    if ($th->nodeName === 'th' || $th->nodeName === 'td') {
                        $txt = trim(strtolower($th->textContent));
                        if ((strpos($txt, 'shift') !== false || strpos($txt, 'group') !== false) && strpos($txt, 'finish') === false) {
                            $thShift = $th;
                            $shiftIndex = $colIndex;
                            break 2;
                        }
                        $colIndex++;
                    }
                }
            }

            // Fallback: Jika tidak ada header bertuliskan shift/group, cari header bertuliskan operator
            if (!$thShift) {
                foreach ($trs as $tr) {
                    $colIndex = 0;
                    foreach ($tr->childNodes as $th) {
                        if ($th->nodeName === 'th' || $th->nodeName === 'td') {
                            $txt = trim(strtolower($th->textContent));
                            if (strpos($txt, 'operator') !== false || strpos($txt, 'op') !== false) {
                                $thShift = $th;
                                $shiftIndex = $colIndex;
                                break 2;
                            }
                            $colIndex++;
                        }
                    }
                }
            }

            // Jika ditemukan kolom anchor (Shift/Group atau Operator), sisipkan Header & Kolom Finish
            if ($thShift && $shiftIndex >= 0) {
                $rowspan = $thShift->hasAttribute('rowspan') ? $thShift->getAttribute('rowspan') : '1';

                $newThOpFin = $doc->createElement('th', "Operator\nFinish");
                $newThOpFin->setAttribute('rowspan', $rowspan);
                $newThOpFin->setAttribute('style', 'vertical-align:middle; text-align:center; white-space:pre-line;');

                $newThShFin = $doc->createElement('th', "Shift/Grp\nFinish");
                $newThShFin->setAttribute('rowspan', $rowspan);
                $newThShFin->setAttribute('style', 'vertical-align:middle; text-align:center; white-space:pre-line;');

                // Urutan Inject: Sisipkan Op Finish setelah Shift Start, lalu Shift Finish setelah Op Finish
                $thShift->parentNode->insertBefore($newThOpFin, $thShift->nextSibling);
                $newThOpFin->parentNode->insertBefore($newThShFin, $newThOpFin->nextSibling);

                // Tambah colspan pada baris judul teratas jika ada
                $titleTh = $xpath->query('//thead/tr[1]/th[@colspan]');
                if ($titleTh->length > 0) {
                    $cspan = (int) $titleTh->item(0)->getAttribute('colspan');
                    $titleTh->item(0)->setAttribute('colspan', (string)($cspan + 2));
                }

                // Tambahkan <td> Operator Finish dan Shift/Group Finish di tbody
                $tbodyRows = $xpath->query('//tbody/tr');
                foreach ($tbodyRows as $idx => $tr) {
                    $rData = isset($rows[$idx]) ? $rows[$idx] : (isset($rows[0]) ? $rows[0] : []);
                    $emp2   = (!empty($rData['empid2']) && $rData['empid2'] !== '-') ? trim((string)$rData['empid2']) : '-';
                    $name2  = (!empty($rData['name2']) && $rData['name2'] !== '-') ? trim((string)$rData['name2']) : '';
                    $shift2 = (!empty($rData['shift2']) && $rData['shift2'] !== '-') ? trim((string)$rData['shift2']) : '-';
                    $grp2   = (!empty($rData['group2']) && $rData['group2'] !== '-') ? trim((string)$rData['group2']) : '-';

                    // Standarisasi label shift angka ke huruf
                    if ($shift2 === '1') $shift2 = 'A';
                    elseif ($shift2 === '2') $shift2 = 'B';
                    elseif ($shift2 === '3') $shift2 = 'C';

                    $sgVal = '-';
                    if ($shift2 !== '-' || $grp2 !== '-') {
                        $sgVal = ($shift2 !== '-' ? $shift2 : '') . ($grp2 !== '-' ? ' / ' . $grp2 : '');
                    }

                    $tds = [];
                    foreach ($tr->childNodes as $node) {
                        if ($node->nodeName === 'td' || $node->nodeName === 'th') {
                            $tds[] = $node;
                        }
                    }

                    if (isset($tds[$shiftIndex])) {
                        $tdOp = $doc->createElement('td');
                        $tdOp->setAttribute('style', 'vertical-align:middle; text-align:center;');
                        $tdOp->appendChild($doc->createTextNode($emp2));
                        if ($name2 !== '' && $name2 !== '-') {
                            $tdOp->appendChild($doc->createElement('br'));
                            $small = $doc->createElement('small');
                            $small->appendChild($doc->createTextNode($name2));
                            $tdOp->appendChild($small);
                        }

                        $tdSg = $doc->createElement('td');
                        $tdSg->setAttribute('style', 'vertical-align:middle; text-align:center;');
                        $tdSg->appendChild($doc->createTextNode($sgVal));

                        $tr->insertBefore($tdOp, $tds[$shiftIndex]->nextSibling);
                        $tr->insertBefore($tdSg, $tdOp->nextSibling);
                    }
                }
            }

            $finalHtml = $doc->saveHTML();
            $finalHtml = str_replace('<?xml encoding="UTF-8">', '', $finalHtml);
            return trim($finalHtml);

        } catch (\Throwable $e) {
            return $html;
        }
    }

    public function dataProduction()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $g = $this->request;

        $machno = $g->getGet('machno');
        $isValidMach = ($machno !== null && $machno !== '' && $machno !== 'null');
        $machno = $isValidMach ? $machno : 'ALL';

        $device  = $this->safeSegment($g->getGet('device'));
        $process = $this->safeSegment($g->getGet('process'));

        $viewName = '/layout/' . $device . '/history/production/' . $process;
        $fullPath = APPPATH . 'Views' . $viewName . '.php';
        if (!is_file($fullPath)) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('<div class="alert alert-warning">Template tidak ditemukan untuk process ini.</div>');
        }

        try {
            $dStart = $g->getGet('dateStart') ?: '1970-1-1';
            $dEnd = $g->getGet('dateEnd') ?: '2070-1-1';
            $mParam = $g->getGet('model') ?? '';
            $lParam = $g->getGet('lotno') ?? '';

            $alldata = $this->ProductionModel->getAll(
                $dStart,
                $dEnd,
                $process,
                $mParam,
                $lParam,
                $machno
            );

            $data = [
                'title'   => 'History | Startup Management',
                'alldata' => $alldata,
            ];

            return view($viewName, $data);
        } catch (\Throwable $e) {
            log_message('error', 'dataProduction gagal: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(200)
                ->setBody('<div class="alert alert-warning"><b>' . esc($e->getMessage()) . '</b></div>');
        }
    }

    private function pick(string $key, string $suffix)
    {
        $v = $this->request->getVar($key . $suffix);
        if ($v === null) {
            $v = $this->request->getVar($key);
        }
        return $v;
    }

    public function createProduction()
    {
        if (!$this->session->has('empid')) {
            return redirect()->to(base_url('login'));
        }

        $req = $this->request;

        $cntInput = $req->getVar('cnt-proses') ? (int) $req->getVar('cnt-proses') : 1;
        $editNumber = $req->getVar('number-edit') ?: $req->getVar('number-form');
        $number = !empty($editNumber)
            ? $editNumber
            : ($this->ProductionModel->getLatestId() + 1);

        $existingRows = !empty($editNumber)
            ? $this->ProductionModel->getByNumber($number)
            : [];
        $existingMap = [];
        foreach ($existingRows as $erow) {
            $bNo = (int) ($erow['par001'] ?? 1);
            $existingMap[$bNo] = $erow;
        }

        $nowHour = (int)date('H');
        $shiftVal = strtoupper(trim((string)($req->getVar('shift-txt') ?: '')));
        if ($shiftVal === '3') $shiftVal = 'C';
        $inputDate = trim((string)$req->getVar('date'));
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $customCreatedAt = null;
        if (!empty($inputDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $inputDate)) {
            $customCreatedAt = $inputDate . ' ' . date('H:i:s');
        }

        $nowDateTime = ($customCreatedAt !== null && empty($editNumber)) ? $customCreatedAt : date('Y-m-d H:i:s');
        $nowTimeOnly = date('H:i:s');

        $hasEmp2Input = ($req->getPost('empid2-txt') !== null);
        if ($hasEmp2Input) {
            $empid2Val = trim((string) $req->getVar('empid2-txt'));
            $group2Val = trim((string) $req->getVar('group2-txt'));
            $shift2Val = trim((string) $req->getVar('shift2-txt'));
            if ($shift2Val === '3') $shift2Val = 'C';
            $name2Val  = trim((string) $req->getVar('name2-txt'));
        } else {
            $empid2Val = trim((string) ($req->getVar('empid-txt') ?: ''));
            $group2Val = trim((string) ($req->getVar('group-txt') ?: ''));
            $shift2Val = $shiftVal;
            $name2Val  = trim((string) ($req->getVar('name-txt') ?: ''));
        }

        $hasOperatorFinish = ($empid2Val !== '' && $empid2Val !== '-');

        $origRow1 = $existingMap[1] ?? [];
        $startEmpId = (!$hasEmp2Input && !empty($origRow1['empid']))
            ? $origRow1['empid']
            : $req->getVar('empid-txt');
        $startGroup = (!$hasEmp2Input && !empty($origRow1['group']))
            ? $origRow1['group']
            : $req->getVar('group-txt');
            $startShift = (!$hasEmp2Input && !empty($origRow1['shift']))
            ? $origRow1['shift']
            : $shiftVal;
        $startName  = (!$hasEmp2Input && !empty($origRow1['name']))
            ? $origRow1['name']
            : ($req->getVar('name-txt') ?: '');

            $timeInDateTime = $nowDateTime;
            $workDate = calculateWorkDate($timeInDateTime, $startShift);

        $common = [
            'number'       => $number,
            'device'       => $req->getVar('device-txt'),
            'model'        => $req->getVar('model-txt'),
            'process'      => $req->getVar('process-txt'),
            'lotno'        => $req->getVar('lotno-txt'),
            'machno'       => $req->getVar('machno-txt'),
            'empid'        => $startEmpId,
            'group'        => $startGroup,
            'shift'        => $startShift,
            'work_date'    => $workDate,
            'name'         => $startName,
            'empid2'       => $hasOperatorFinish ? $empid2Val : null,
            'group2'       => $hasOperatorFinish ? $group2Val : null,
            'shift2'       => $hasOperatorFinish ? $shift2Val : null,
            'name2'        => $hasOperatorFinish ? $name2Val : null,
            'std_die_bond' => $req->getVar('std_die_bond') ?: null,
            // Modifikasi 'created_at' agar history grid ikut sinkron dengan waktu statis jika baru dibuat
            'created_at'   => empty($editNumber) ? $timeInDateTime : date('Y-m-d H:i:s'),
        ];

        $suffixes  = ['a', 'b', 'c', 'd', 'e'];
        $lastBlock = count($suffixes);
        $result    = false;

        foreach ($suffixes as $i => $suffix) {
            $blockNo = $i + 1;

            $data           = $common;
            $data['par001'] = $blockNo;

            for ($n = 2; $n <= 43; $n++) {
                $padNum = str_pad((string) $n, 3, '0', STR_PAD_LEFT);
                $key = 'par' . $padNum;

                if ($n === 8) {
                    $oldIn = $existingMap[$blockNo]['par008'] ?? null;
                    $isOldValid = (!empty($oldIn) && strpos((string) $oldIn, '07:00') === false && $oldIn !== '-');
                    // UBAH: Gunakan $timeInDateTime khusus untuk par008
                    $data[$key] = $isOldValid ? $oldIn : $timeInDateTime; 
                } elseif ($n === 9) {
                    // TETAP: Time out (par009) biarkan menggunakan waktu aktual ($nowDateTime)
                    $data[$key] = $hasOperatorFinish ? $nowDateTime : null;
                } elseif ($n === 10) {
                    $val10 = $this->pick($key, $suffix);
                    $str10 = trim((string) $val10);
                    $isTimeStr = preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $str10);
                    if ($str10 === '07:00:00' || $str10 === '07:00' || $isTimeStr) {
                        // TETAP: Time out (par010) biarkan menggunakan waktu aktual ($nowTimeOnly)
                        $data[$key] = $hasOperatorFinish ? $nowTimeOnly : null;
                    } else {
                        $data[$key] = $val10;
                    }
                } else {
                    $data[$key] = $this->pick($key, $suffix);
                }
            }

            $data['par045'] = $hasOperatorFinish ? 'Process Complete' : 'Process Start';

            $result = $this->ProductionModel->addProses($data);

            if ($cntInput === $blockNo || $blockNo === $lastBlock) {
                break;
            }
        }

        session()->setFlashdata('message', $result ? 'Input success!' : 'Input Failed!');

        return redirect()->to(base_url('input'));
    }
}