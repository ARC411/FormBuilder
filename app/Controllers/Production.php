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
        $order = (!empty($orderData) && is_array($orderData)) ? $orderData[0] : ['column' => 1, 'dir' => 'desc'];
        
        $searchData = $r->getGet('search');
        $searchValue = (!empty($searchData) && isset($searchData['value'])) ? trim($searchData['value']) : '';

        $res = $this->ProductionModel->getDatatable(
            max((int) $r->getGet('start'), 0),
            min(max((int) $r->getGet('length'), 1), 100),
            $searchValue,
            (int) $order['column'],
            $order['dir']
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

        if (empty($device) || empty($process)) {
            return "";
        }

        $data = [
            'title'      => 'Input | Startup Management',
            'validation' => \Config\Services::validation(),
        ];

        return view('/layout/' . $this->safeSegment($device) . '/input/production/' . $this->safeSegment($process), $data);
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

        $device  = $alldata[0]['device'];
        $process = $alldata[0]['process'];

        $data = [
            'title'   => 'History | Startup Management',
            'alldata' => $alldata,
            'process' => $process,
            'number'  => $number,
        ];

        try {
            return view('/layout/' . $device . '/input/production/' . $process, $data);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            return "<div class='alert alert-danger'>Maaf, form checklist (".$process.") belum tersedia atau file view tidak ditemukan.</div>";
        }
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

        $device  = $this->safeSegment($rows[0]['device']);
        $process = $this->safeSegment($rows[0]['process']);

        return view('/layout/' . $device . '/history/production/' . $process, ['alldata' => $rows]);
    }

    public function dataProduction()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $g = $this->request;

        $machno = $g->getGet('machno');
        $machno = ($machno !== null && $machno !== '' && $machno !== 'null') ? $machno : 'ALL';

        $device  = $this->safeSegment($g->getGet('device'));
        $process = $this->safeSegment($g->getGet('process'));

        $viewName = '/layout/' . $device . '/history/production/' . $process;
        if (!is_file(APPPATH . 'Views' . $viewName . '.php')) {
            return $this->response
                ->setStatusCode(404)
                ->setBody('<div class="alert alert-warning">Template tidak ditemukan untuk process ini.</div>');
        }

        try {
            $data = [
                'title'   => 'History | Startup Management',
                'alldata' => $this->ProductionModel->getAll(
                    $g->getGet('dateStart') ?: '1970-1-1',
                    $g->getGet('dateEnd') ?: '2070-1-1',
                    $process,
                    $g->getGet('model') ?? '',
                    $g->getGet('lotno') ?? '',
                    $machno
                ),
            ];

            return view($viewName, $data);
        } catch (\Throwable $e) {
            log_message('error', 'dataProduction gagal: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return $this->response
                ->setStatusCode(200)
                ->setBody(
                    '<div class="alert alert-warning"><b>' . esc($e->getMessage()) . '</b><br>'
                    . esc($e->getFile()) . ' : ' . $e->getLine() . '</div>'
                );
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
        $number   = !empty($editNumber) ? $editNumber : ($this->ProductionModel->getLatestId() + 1);

        // Cek apakah data sudah pernah ada sebelumnya (agar waktu IN tidak tertimpa saat penyelesaian/edit)
        $existingRows = !empty($editNumber) ? $this->ProductionModel->getByNumber($number) : [];
        $existingMap  = [];
        foreach ($existingRows as $erow) {
            $bNo = (int) ($erow['par001'] ?? 1);
            $existingMap[$bNo] = $erow;
        }

        $nowDateTime = date('Y-m-d H:i:s');
        $nowTimeOnly = date('H:i:s');

        $common = [
            'number'  => $number,
            'device'  => $req->getVar('device-txt'),
            'model'   => $req->getVar('model-txt'),
            'process' => $req->getVar('process-txt'),
            'lotno'   => $req->getVar('lotno-txt'),
            'machno'  => $req->getVar('machno-txt'),
            'empid'   => $req->getVar('empid-txt'),
            'group'   => $req->getVar('group-txt'),
            'shift'   => $req->getVar('shift-txt'),
            'name'    => $req->getVar('name-txt') ?: "",
            'empid2'  => $req->getVar('empid2-txt') ?: ($req->getVar('empid-txt') ?: ""),
            'group2'  => $req->getVar('group2-txt') ?: ($req->getVar('group-txt') ?: ""),
            'shift2'  => $req->getVar('shift2-txt') ?: ($req->getVar('shift-txt') ?: ""),
            'name2'   => $req->getVar('name2-txt') ?: ($req->getVar('name-txt') ?: ""),
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
                $key = 'par' . str_pad((string) $n, 3, '0', STR_PAD_LEFT);

                if ($n === 8) {
                    // Waktu IN: pertahankan waktu IN lama jika sedang menyelesaikan/edit, atau rekam waktu server saat ini jika baru
                    $oldIn = $existingMap[$blockNo]['par008'] ?? null;
                    if (!empty($oldIn) && strpos((string)$oldIn, '07:00') === false) {
                        $data[$key] = $oldIn;
                    } else {
                        $data[$key] = $nowDateTime;
                    }
                } elseif ($n === 9) {
                    // Waktu OUT: selalu rekam waktu server secara otomatis tepat saat tombol Submit ditekan
                    $data[$key] = $nowDateTime;
                } elseif ($n === 10) {
                    // Jika form memakai par010 untuk waktu selesai (misal dari dropdown hh_2/mm_2 default 07:00), ganti dengan waktu server
                    $val10 = $this->pick($key, $suffix);
                    if ($val10 !== null && (trim((string)$val10) === '07:00:00' || trim((string)$val10) === '07:00' || preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', trim((string)$val10)))) {
                        $data[$key] = $nowTimeOnly;
                    } else {
                        $data[$key] = $val10;
                    }
                } else {
                    $data[$key] = $this->pick($key, $suffix);
                }
            }

            $data['par045'] = 'Process Complete';

            $result = $this->ProductionModel->addProses($data);

            if ($cntInput === $blockNo || $blockNo === $lastBlock) {
                break;
            }
        }

        session()->setFlashdata('message', $result ? 'Input success!' : 'Input Failed!');

        return redirect()->to(base_url('input'));
    }
}