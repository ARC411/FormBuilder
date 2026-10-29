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

    public function index(): string
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
            return redirect()->to('/checkseet/login');
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
            return redirect()->to('/checkseet/login');
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

        $process   = $alldata[0]['process'];
        $rowRevisi = $alldata[0]['revisi'] ?? null;

        try {
            $fb  = new \App\Controllers\FormBuilder();
            $res = $fb->buildOperatorFormPayload($process, 'production', $alldata, $number, $rowRevisi);
            if (!empty($res['html'])) {
                return $res['html'];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Production formEdit dynamic fallback: ' . $e->getMessage());
        }

        $device = $alldata[0]['device'];
        return view('/layout/' . $device . '/input/production/' . $process, [
            'title'   => 'History | Startup Management',
            'alldata' => $alldata,
            'process' => $process,
            'number'  => $number,
        ]);
    }

    public function deleteProduction($number)
    {
        if (!$this->session->has('empid')) {
            session()->setFlashdata('message', 'Login terlebih dahulu');
            return redirect()->to('/checkseet/login');
        }

        $this->ProductionModel->deleteByNumber($number);
        return "berhasil";
    }

    public function productionByNumber($number)
    {
        $rows = $this->ProductionModel->getByNumber($number);
        if (empty($rows)) {
            throw PageNotFoundException::forPageNotFound();
        }

        try {
            $fb = new \App\Controllers\FormBuilder();
            return $fb->renderDetailTableByRows($rows[0]['process'], 'production', $rows);
        } catch (\Throwable $e) {
            $device  = $this->safeSegment($rows[0]['device']);
            $process = $this->safeSegment($rows[0]['process']);
            return view('/layout/' . $device . '/history/production/' . $process, ['alldata' => $rows]);
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
        if (($v === null || trim((string)$v) === '') && $this->request->getVar($key . '_val') !== null) {
            $rawNum  = str_replace(',', '.', trim((string)$this->request->getVar($key . '_val')));
            $rawUnit = trim((string)($this->request->getVar($key . '_unit') ?? ''));
            if ($rawNum !== '') {
                $v = $rawUnit !== '' ? ($rawNum . ' ' . $rawUnit) : $rawNum;
            }
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

        $nowDateTime = date('Y-m-d H:i:s');
        $nowTimeOnly = date('H:i:s');

        // Cek apakah field Operator Finish (empid2-txt) ada di form atau sedang menyelesaikan dari Lot Pending History
        $hasEmp2Input = ($req->getPost('empid2-txt') !== null);
        if ($hasEmp2Input) {
            $empid2Val = trim((string) $req->getVar('empid2-txt'));
            $group2Val = trim((string) $req->getVar('group2-txt'));
            $shift2Val = trim((string) $req->getVar('shift2-txt'));
            $name2Val  = trim((string) $req->getVar('name2-txt'));
        } else {
            // Jika dari halaman Input Checksheet Complete (mode 1 blok operator), operator yang login adalah Operator Finish
            $empid2Val = trim((string) ($req->getVar('empid-txt') ?: ''));
            $group2Val = trim((string) ($req->getVar('group-txt') ?: ''));
            $shift2Val = trim((string) ($req->getVar('shift-txt') ?: ''));
            $name2Val  = trim((string) ($req->getVar('name-txt') ?: ''));
        }

        $hasOperatorFinish = ($empid2Val !== '' && $empid2Val !== '-');

        // Pertahankan Operator Start asli jika sedang menyelesaikan checksheet
        $origRow1 = $existingMap[1] ?? [];
        $startEmpId = (!$hasEmp2Input && !empty($origRow1['empid']))
            ? $origRow1['empid']
            : $req->getVar('empid-txt');
        $startGroup = (!$hasEmp2Input && !empty($origRow1['group']))
            ? $origRow1['group']
            : $req->getVar('group-txt');
        $startShift = (!$hasEmp2Input && !empty($origRow1['shift']))
            ? $origRow1['shift']
            : $req->getVar('shift-txt');
        $startName  = (!$hasEmp2Input && !empty($origRow1['name']))
            ? $origRow1['name']
            : ($req->getVar('name-txt') ?: '');

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
            'name'         => $startName,
            'empid2'       => $hasOperatorFinish ? $empid2Val : null,
            'group2'       => $hasOperatorFinish ? $group2Val : null,
            'shift2'       => $hasOperatorFinish ? $shift2Val : null,
            'name2'        => $hasOperatorFinish ? $name2Val : null,
            'std_die_bond' => $req->getVar('std_die_bond') ?: null,
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
                    $data[$key] = $isOldValid ? $oldIn : $nowDateTime;
                } elseif ($n === 9) {
                    // Poin 1: Jika Operator Finish tidak ada, kosongkan waktu Out (null)
                    $data[$key] = $hasOperatorFinish ? $nowDateTime : null;
                } elseif ($n === 10) {
                    $val10 = $this->pick($key, $suffix);
                    $str10 = trim((string) $val10);
                    $isTimeStr = preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $str10);
                    if ($str10 === '07:00:00' || $str10 === '07:00' || $isTimeStr) {
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
