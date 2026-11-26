<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\LogActivityModel;
use App\Models\WeeklyApprovalModel;
use App\Models\WeeklyDocumentApprovalModel;
use App\Models\ProductionModel;

class Approval extends BaseController
{
    protected $session;
    protected $StartupModel;
    protected $ProductionModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->StartupModel = new StartupModel();
        $this->ProductionModel = new ProductionModel();
        $this->session = \Config\Services::session();
        $this->session->start();
    }

    // Menampilkan data 
    public function index()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        // Cek apakah user adalah operator murni (non-admin)
        $pos = (string) $this->session->get('positionid');
        $isAdmin = (bool) $this->session->get('isadmin');
        $isOperator = (!$isAdmin && stripos($pos, 'operator') !== false);

        $data = [
            'title' => 'Approval | Startup Management',
            'list_request_reject' => $this->StartupModel->getRequestRejectList(),
            'isOperator' => $isOperator
        ];

        return view("approval",$data);
    }

    public function formApproval()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }
        
        $dateStart = $this->request->getGet('dateStart') ?? date('Y-m-d', strtotime('-1 month'));
        $dateEnd = $this->request->getGet('dateEnd') ?? date('Y-m-d');

        $data = [
            'title' => 'Approval | Startup Management',
            'alldata' => $this->StartupModel->getApprovalData($dateStart, $dateEnd)
        ];

        return view("layout/approval-table",$data);
    }

    public function approvalById($id)
    {
        $data = [
            'title' => 'History | Startup Management',
            'alldata' => $this->ProductionModel->getByNumber($id)
        ];

        return view("/layout/history/".$this->ProductionModel->getByNumber($id)[0]['process'],$data);
    }
    
    public function updateApproval()
    {
        if(!$this->session->has('empid')){
            return redirect()->to(base_url()."login");
        }
        
        $number = $this->request->getVar('number');
        $name = $this->request->getVar('name') ?: $this->session->get('name');
        $level = $this->request->getVar('level') ?: $this->session->get('level');
        $action = $this->request->getVar('action') ?? 'Approve';
        $rejectReason = trim((string)$this->request->getVar('reject_reason'));

        if (empty($number)) {
            $this->session->setFlashdata('error', 'Gagal memproses approval: Nomor checksheet tidak terbaca atau kosong.');
            return redirect()->to(base_url().'approve');
        }

        if ($action === 'Reject' && empty($rejectReason)) {
            $this->session->setFlashdata('error', 'Gagal Reject: Alasan penolakan / catatan perbaikan wajib diisi!');
            return redirect()->to(base_url().'approve');
        }
        
        $update = $this->StartupModel->updateApproval($number, $name, $level, $action, $rejectReason);
        if ($update) {
            if ($action === 'Reject') {
                $this->session->setFlashdata('message', "Checksheet #{$number} berhasil di-Reject. Operator akan melihat notifikasi pada navbar Operations dan dapat memperbaiki form ini.");
            } else {
                $this->session->setFlashdata('message', "Checksheet #{$number} berhasil di-Approve.");
            }
        } else {
            $this->session->setFlashdata('error', "Gagal memproses approval untuk checksheet #{$number}.");
        }

        return redirect()->to(base_url().'approve');
    }

    public function weekly()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $type = $this->request->getGet('type');
        $tab  = $this->request->getGet('tab');

        if ($type === 'startup' || $tab === 'startup') {
            return $this->weeklyStartup();
        } elseif ($type === 'production' || $tab === 'production') {
            return $this->weeklyProduction();
        } elseif ($type === 'foregoing' || $tab === 'foregoing') {
            return $this->weeklyForegoing();
        }

        // Tampilan Hub (Pusat Approval Checksheet)
        $pos = (string) $this->session->get('positionid');
        $isAdmin = (bool) $this->session->get('isadmin');
        $isOperator = (!$isAdmin && stripos($pos, 'operator') !== false);

        $data = [
            'title'     => 'Pusat Approval Checksheet | Antigravity',
            'user_role' => $this->session->get('role'),
            'is_admin'  => $isAdmin,
            'list_request_reject' => $this->StartupModel->getRequestRejectList(),
            'isOperator' => $isOperator
        ];

        return view('approval_hub', $data);
    }

    /**
     * Dapatkan otoritas approval pengguna secara ketat:
     * - Admin (isadmin / username admin): Super Admin bypass, dapat approve & reject kedua departemen (QC & Production).
     * - QC / QA: Hanya untuk Leader/Foreman/Supervisor/Manager (Level 1-7) dari departemen QC/QA.
     * - Production: Hanya untuk Leader/Foreman/Supervisor/Manager (Level 1-7) dari departemen Production (FL, SL, Opto, Compound, dll).
     * - Departemen lain / Operator biasa (Level 0): Ditolak (tidak memiliki otoritas approve/reject mingguan).
     */
    private function getUserApprovalAuthority(): array
    {
        $isAdmin = !empty($this->session->get('isadmin')) || ($this->session->get('empid') === 'admin');
        $level = (int) ($this->session->get('level') ?? 0);
        $deptCategory = strtoupper((string)($this->session->get('department') ?? ''));
        $deptid = (string)($this->session->get('deptid') ?? '');

        // Fallback untuk sesi aktif yang belum memiliki data department di session
        if (empty($deptCategory) && !$isAdmin) {
            $empid = (string) $this->session->get('empid');
            if (!empty($empid)) {
                $empModel = new \App\Models\EmpModel();
                $empData = $empModel->getDataEmp($empid);
                if ($empData) {
                    $deptid = (string)$empData->deptid;
                    $deptCategory = \App\Models\EmpModel::determineDepartmentCategory($deptid);
                    $this->session->set('deptid', $deptid);
                    $this->session->set('department', $deptCategory);

                    if ($level === 0 && !empty($empData->positionid)) {
                        $level_string = preg_split('/,/', $empData->positionid, -1, PREG_SPLIT_NO_EMPTY)[0];
                        $level = (int) $level_string;
                        $this->session->set('level', $level);
                    }
                }
            }
        }

        // Leader ke atas (Level 1 s/d 7: Leader, Foreman, Supervisor, Manager)
        $isEligibleLevel = ($level >= 1 && $level <= 7);

        $canQc = $isAdmin || ($isEligibleLevel && $deptCategory === 'QC');
        $canProd = $isAdmin || ($isEligibleLevel && $deptCategory === 'PRODUCTION');

        return [
            'is_admin'       => $isAdmin,
            'level'          => $level,
            'deptid'         => $deptid,
            'department'     => $deptCategory,
            'can_qc'         => $canQc,
            'can_production' => $canProd,
            'is_eligible'    => ($isAdmin || ($isEligibleLevel && ($deptCategory === 'QC' || $deptCategory === 'PRODUCTION')))
        ];
    }

    public function weeklyStartup()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        // Sinkronisasi otomatis data startup minggu berjalan (misal periode 26 Oct s/d 01 Nov) agar langsung muncul real-time
        $this->syncOngoingWeeklyStartup();

        $weeklyModel = new WeeklyApprovalModel();
        $auth = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        $weeklyList = $weeklyModel->getWeeklyList();

        // Ambil data tanggal checksheet aktif untuk setiap weekly_id agar modal reject dapat menampilkan info jumlah checksheet per hari
        $db = \Config\Database::connect();
        $weeklyIds = array_filter(array_column($weeklyList, 'id'));
        $datesByWeekly = [];
        $machinesByWeekly = [];

        if (!empty($weeklyIds)) {
            $activeDatesQuery = $db->table('startup')
                ->select('weekly_id, COALESCE(work_date, DATE(created_at)) as doc_date, COUNT(*) as doc_count, SUM(CASE WHEN status = "Reject" THEN 1 ELSE 0 END) as reject_count, SUM(CASE WHEN status = "Pending" OR status = "" OR status = "-" OR status IS NULL THEN 1 ELSE 0 END) as pending_count')
                ->where('par000', '1')
                ->whereIn('weekly_id', $weeklyIds)
                ->groupBy('weekly_id, COALESCE(work_date, DATE(created_at))')
                ->get()->getResultArray();

            foreach ($activeDatesQuery as $ad) {
                $wId = $ad['weekly_id'];
                $dDate = $ad['doc_date'];
                if (!isset($datesByWeekly[$wId])) {
                    $datesByWeekly[$wId] = [];
                }
                $datesByWeekly[$wId][$dDate] = [
                    'count'        => (int)$ad['doc_count'],
                    'reject_count' => (int)$ad['reject_count']
                ];
            }

        }

        foreach ($weeklyList as &$wRow) {
            $wRow['active_dates'] = $datesByWeekly[$wRow['id']] ?? [];
        }
        unset($wRow);

        $data = [
            'title'       => 'Weekly Approval Data - Startup Checksheet',
            'weekly_data' => $weeklyList,
            'user_role'   => $this->session->get('role'),
            'user_dept'   => $auth['department'],
            'is_admin'    => $isAdmin,
            'can_qc'      => $canQc,
            'can_prod'    => $canProd,
            'is_qc'       => $canQc,
            'is_prod'     => $canProd
        ];

        return view('weekly_approval_startup', $data);
    }

    // Endpoint AJAX untuk mengambil rincian mesin & tanggal aktif pada 1 periode mingguan secara on-demand (hemat memori)
    public function getWeeklyMachines()
    {
        if(!$this->session->has('name')){
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $weeklyId = (int)$this->request->getGet('weekly_id');
        if (empty($weeklyId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID mingguan diperlukan'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $machRows = $db->table('startup')
            ->select('startup.weekly_id, 
                      startup.device, 
                      startup.process, 
                      startup.machno, 
                      startup.shift,
                      startup.work_date,
                      startup.created_at,
                      COALESCE(startup.work_date, DATE(startup.created_at)) as doc_date, 
                      startup.status,
                      proses.name as process_name,
                      device.name as device_name')
            ->join('proses', 'proses.process_code = startup.process', 'left')
            ->join('device', 'device.code = COALESCE(startup.device, proses.device)', 'left')
            ->where('startup.par000', '1')
            ->where('startup.weekly_id', $weeklyId)
            ->orderBy('startup.device', 'ASC')
            ->orderBy('startup.machno', 'ASC')
            ->orderBy('COALESCE(startup.work_date, DATE(startup.created_at))', 'ASC', false)
            ->orderBy('startup.created_at', 'ASC')
            ->get()->getResultArray();

        $dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $machines = [];

        foreach ($machRows as $mr) {
            $machKey = (!empty($mr['machno']) && $mr['machno'] !== '-') ? $mr['machno'] : 'No Machine';
            $devKey = $mr['device'] ?: '';
            $procKey = $mr['process'] ?: '';
            $uniqueKey = $devKey . '__' . $procKey . '__' . $machKey;
            $dDate = $mr['doc_date'];
            $dayTs = strtotime($dDate);
            $dayName = $dayNamesIndo[(int)date('w', $dayTs)];

            if (!isset($machines[$uniqueKey])) {
                $machines[$uniqueKey] = [
                    'machno'       => $mr['machno'] ?: '-',
                    'device'       => $mr['device'] ?: '',
                    'device_name'  => $mr['device_name'] ?: $mr['device'],
                    'process'      => $mr['process'] ?: '',
                    'process_name' => $mr['process_name'] ?: $mr['process'],
                    'dates'        => []
                ];
            }
            $rawSt = trim((string)($mr['status'] ?? ''));
            $isRej = ($rawSt === 'Reject');
            $isPend = ($rawSt === 'Pending' || $rawSt === '' || $rawSt === '-');
            $canonicalSt = $isRej ? 'Reject' : ($isPend ? 'Pending' : 'Approved');

            if (!isset($machines[$uniqueKey]['dates'][$dDate])) {
                $machines[$uniqueKey]['dates'][$dDate] = [
                    'date'       => $dDate,
                    'label'      => $dayName . ', ' . date('d/m/Y', $dayTs),
                    'status'     => $canonicalSt,
                    'raw_status' => $rawSt,
                    'count'      => 1
                ];
            } else {
                $machines[$uniqueKey]['dates'][$dDate]['count']++;
                if ($isRej) {
                    $machines[$uniqueKey]['dates'][$dDate]['status'] = 'Reject';
                } elseif ($isPend && $machines[$uniqueKey]['dates'][$dDate]['status'] !== 'Reject') {
                    $machines[$uniqueKey]['dates'][$dDate]['status'] = 'Pending';
                }
            }
        }

        // Ambil status approval per-dokumen spesifik
        $docModel = new WeeklyDocumentApprovalModel();
        $docApprovals = $docModel->getGroupedByWeeklyId($weeklyId);

        foreach ($machines as $k => $m) {
            $docKey = trim((string)$m['device']) . '___' . trim((string)$m['process']) . '___' . trim((string)$m['machno']);
            $docApp = $docApprovals[$docKey] ?? null;

            $statusQc   = $docApp['status_qc'] ?? 'Pending';
            $statusProd = $docApp['status_production'] ?? 'Pending';

            $machines[$k]['status_qc']              = $statusQc;
            $machines[$k]['approver_qc']            = $docApp['approver_qc'] ?? null;
            $machines[$k]['date_qc']                = $docApp['date_qc'] ?? null;
            $machines[$k]['status_production']      = $statusProd;
            $machines[$k]['approver_production']    = $docApp['approver_production'] ?? null;
            $machines[$k]['date_production']        = $docApp['date_production'] ?? null;
            $machines[$k]['is_approved_qc']         = ($statusQc === 'Approve');
            $machines[$k]['is_approved_production'] = ($statusProd === 'Approve');
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'machines' => array_values($machines)
        ]);
    }

    public function weeklyProduction()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();

        // Parameter sorting Rentang Tanggal (terbaru-terlama vs lama-terbaru)
        $sort = strtolower(trim((string)($this->request->getGet('sort') ?? 'desc')));
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        $auth    = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        // Ambil data batch lembar production yang sudah diurutkan
        $productionData = $weeklyModel->getProductionBatchList($sort);

        $data = [
            'title'           => 'Page Approval Data – Production Checksheet (Per 20 Data)',
            'production_data' => $productionData,
            'sort_dir'        => $sort,
            'next_sort'       => ($sort === 'desc' ? 'asc' : 'desc'),
            'user_role'       => $this->session->get('role'),
            'user_dept'       => $auth['department'],
            'is_admin'        => $isAdmin,
            'can_qc'          => $canQc,
            'can_prod'        => $canProd,
            'is_qc'           => $canQc,
            'is_prod'         => $canProd
        ];

        return view('weekly_approval_production', $data);
    }

    public function weeklyForegoing()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $data = [
            'title'     => 'Approval Data â€“ Foregoing Checksheet',
            'user_role' => $this->session->get('role'),
            'is_admin'  => !empty($this->session->get('isadmin'))
        ];

        return view('weekly_approval_foregoing', $data);
    }

    public function syncProduction()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        $count = $weeklyModel->syncProductionBatches(500);

        if ($count > 0) {
            $this->session->setFlashdata('message', "Sinkronisasi berhasil! Sebanyak {$count} checksheet production telah dikelompokkan ke dalam lembar.");
        } else {
            $this->session->setFlashdata('message', "Semua checksheet production sudah tersinkronisasi ke dalam lembar approval.");
        }

        return redirect()->to(base_url('approval/weekly?type=production'));
    }

    public function processWeekly()
    {
        $isAjax = $this->request->isAJAX() 
               || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' 
               || str_contains($this->request->getHeaderLine('Accept'), 'application/json');

        if(!$this->session->has('name')){
            if ($isAjax) {
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Sesi login telah berakhir, silakan login kembali.']);
            }
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        
        $weekly_id  = $this->request->getPost('weekly_id');
        $action     = $this->request->getPost('action'); // 'Approve' atau 'Reject'
        $departemen = strtolower(trim((string)$this->request->getPost('departemen'))); // 'qc' atau 'production'

        $auth    = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        $batch = $weeklyModel->find($weekly_id);
        if (!$batch) {
            $msg = 'Data approval tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => $msg]);
            }
            $this->session->setFlashdata('error', $msg);
            return redirect()->to(base_url().'approval/weekly?type=startup');
        }

        $isProduction = (!empty($batch['doc_type']) && $batch['doc_type'] === 'production');

        // =========================================================================
        // 1. LOGIKA APPROVAL PRODUCTION (Per 20 Data, 1 Check QC atau Prod salah satu)
        // =========================================================================
        if ($isProduction) {
            // Validasi Otoritas Akses: bisa QC atau Production (salah satu)
            if (!$canQc && !$canProd) {
                $msg = 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC atau Production.';
                if ($isAjax) {
                    return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => $msg]);
                }
                $this->session->setFlashdata('error', $msg);
                return redirect()->to(base_url().'approval/weekly?type=production');
            }

            // Syarat Wajib Penuh: Harus sudah mencapai tepat 20 data per lembar
            $db = \Config\Database::connect();
            $totalCount = $db->table('production')
                ->where('weekly_id', $weekly_id)
                ->where('par001', 1)
                ->countAllResults();

            if ($action === 'Approve' && $totalCount < 20) {
                $msg = "Persetujuan ditolak: Lembar checksheet ini belum penuh (baru {$totalCount}/20 data). Tunggu sampai genap 20 data (1 lembar penuh) sebelum approval.";
                if ($isAjax) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
                }
                $this->session->setFlashdata('error', $msg);
                return redirect()->to(base_url().'approval/weekly?type=production');
            }

            // Single Check: Dilakukan oleh QC atau Prod salah satu saja
            $approverName = $this->session->get('name') ?? 'Approver';
            $approverRole = ($departemen === 'qc' || ($canQc && !$canProd)) ? 'QC' : 'Production';
            if ($departemen === 'production' && $canProd) {
                $approverRole = 'Production';
            }
            $now = date('Y-m-d H:i:s');

            $updateData = [
                'status'            => $action,
                'status_qc'         => $action,
                'status_production' => $action,
                'approver'          => $approverName,
                'approver_role'     => $approverRole,
                'approval_date'     => $now,
                'updated_at'        => $now
            ];

            $weeklyModel->update($weekly_id, $updateData);
            $pageInfo = !empty($batch['page_no']) ? " (Lembar {$batch['page_no']})" : "";
            $msg = "Lembar Production #{$weekly_id}{$pageInfo} berhasil di-{$action} oleh {$approverName} ({$approverRole}).";

            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
            }
            $this->session->setFlashdata('message', $msg);
            return redirect()->to(base_url().'approval/weekly?type=production');
        }

        // =========================================================================
        // 2. LOGIKA APPROVAL STARTUP MINGGUAN (Validasi Peran Departemen Ketat)
        // =========================================================================
        $targetDevice  = trim((string)($this->request->getPost('device') ?? ''));
        $targetProcess = trim((string)($this->request->getPost('process') ?? ''));
        $targetMachno  = trim((string)($this->request->getPost('machno') ?? ''));
        $isSpecificDoc = (!empty($targetDevice) && !empty($targetProcess));

        // Validasi Otoritas Akses Departemen
        $backUrlFallback = $isSpecificDoc 
            ? base_url('approval/weekly_form/' . $weekly_id . '?device=' . urlencode($targetDevice) . '&process=' . urlencode($targetProcess) . '&machno=' . urlencode($targetMachno))
            : base_url('approval/weekly?type=startup');

        if ($departemen === 'qc' && !$canQc) {
            $msg = 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC/QA. Hanya Leader/Foreman/Supervisor QC/QA yang diizinkan menyetujui bagian QC.';
            if ($isAjax) {
                return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => $msg]);
            }
            $this->session->setFlashdata('error', $msg);
            return redirect()->to($backUrlFallback);
        }
        if ($departemen === 'production' && !$canProd) {
            $msg = 'Akses ditolak: Anda tidak memiliki otoritas persetujuan Production. Hanya Leader/Foreman/Supervisor Production yang diizinkan menyetujui bagian Production.';
            if ($isAjax) {
                return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => $msg]);
            }
            $this->session->setFlashdata('error', $msg);
            return redirect()->to($backUrlFallback);
        }

        $approverName = $this->session->get('name') ?? 'Approver';
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $docModel = new WeeklyDocumentApprovalModel();

        // -------------------------------------------------------------------------
        // A. APPROVAL KHUSUS SPESIFIK DOKUMEN (Device, Process, No Mesin)
        // -------------------------------------------------------------------------
        if ($isSpecificDoc) {
            // Syarat Wajib Lengkap: Daily approval untuk dokumen mesin ini harus sudah selesai
            $pendingDocQ = $db->table('startup')
                ->where('weekly_id', $weekly_id)
                ->where('device', $targetDevice)
                ->where('process', $targetProcess)
                ->where('par000', '1')
                ->groupStart()
                    ->where('status', 'Pending')
                    ->orWhere('status', 'Reject')
                    ->orWhere('status IS NULL')
                    ->orWhere('status', '')
                    ->orWhere('status', '-')
                ->groupEnd();

            if ($targetMachno !== '' && $targetMachno !== '-' && $targetMachno !== 'null') {
                $pendingDocQ->where('machno', $targetMachno);
            } else {
                $pendingDocQ->groupStart()
                    ->where('machno', '')
                    ->orWhere('machno', '-')
                    ->orWhere('machno IS NULL')
                    ->orWhere('machno', 'null')
                ->groupEnd();
            }
            $pendingDaily = $pendingDocQ->countAllResults();

            if ($pendingDaily > 0) {
                $msg = "Persetujuan ditolak: Masih terdapat {$pendingDaily} checksheet startup harian untuk dokumen mesin ini yang berstatus Pending atau Reject. Selesaikan daily approval terlebih dahulu.";
                if ($isAjax) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
                }
                $this->session->setFlashdata('error', $msg);
                return redirect()->to($backUrlFallback);
            }

            $existingDoc = $docModel->getDocApproval($weekly_id, $targetDevice, $targetProcess, $targetMachno);
            $statusField   = ($departemen === 'qc') ? 'status_qc' : 'status_production';
            $approverField = ($departemen === 'qc') ? 'approver_qc' : 'approver_production';
            $dateField     = ($departemen === 'qc') ? 'date_qc' : 'date_production';

            if ($existingDoc && $existingDoc[$statusField] === 'Approve') {
                $msg = "Dokumen mesin " . ($targetMachno ?: '-') . " sudah disetujui sebelumnya oleh " . esc($existingDoc[$approverField]) . ".";
                if ($isAjax) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
                }
                $this->session->setFlashdata('error', $msg);
                return redirect()->to($backUrlFallback);
            }

            // Simpan / Perbarui approval spesifik dokumen
            if ($existingDoc) {
                $docModel->update($existingDoc['id'], [
                    $statusField   => 'Approve',
                    $approverField => $approverName,
                    $dateField     => $now,
                    'updated_at'   => $now
                ]);
            } else {
                $docModel->insert([
                    'weekly_id'           => $weekly_id,
                    'device'              => $targetDevice,
                    'process'             => $targetProcess,
                    'machno'              => $targetMachno,
                    'status_qc'           => ($departemen === 'qc') ? 'Approve' : 'Pending',
                    'approver_qc'         => ($departemen === 'qc') ? $approverName : null,
                    'date_qc'             => ($departemen === 'qc') ? $now : null,
                    'status_production'   => ($departemen === 'production') ? 'Approve' : 'Pending',
                    'approver_production' => ($departemen === 'production') ? $approverName : null,
                    'date_production'     => ($departemen === 'production') ? $now : null,
                    'created_at'          => $now,
                    'updated_at'          => $now
                ]);
            }

            // Periksa apakah SELURUH dokumen pada weekly batch ini sudah disetujui oleh departemen ini
            $allDistinctDocs = $db->table('startup')
                ->select('device, process, COALESCE(machno, "") as machno')
                ->where('weekly_id', $weekly_id)
                ->where('par000', '1')
                ->groupBy('device, process, COALESCE(machno, "")')
                ->get()->getResultArray();
            $totalDistinct = count($allDistinctDocs);

            $approvedDocsDept = $docModel->where('weekly_id', $weekly_id)
                ->where($statusField, 'Approve')
                ->findAll();
            $approvedDeptMap = [];
            foreach ($approvedDocsDept as $ad) {
                $approvedDeptMap[trim((string)$ad['device']) . '___' . trim((string)$ad['process']) . '___' . trim((string)$ad['machno'])] = true;
            }

            $allDeptDone = true;
            foreach ($allDistinctDocs as $doc) {
                $k = trim((string)$doc['device']) . '___' . trim((string)$doc['process']) . '___' . trim((string)$doc['machno']);
                if (!isset($approvedDeptMap[$k])) {
                    $allDeptDone = false;
                    break;
                }
            }

            // Jika semua dokumen sudah approve untuk departemen ini, sinkronkan ke weekly_approvals parent
            if ($allDeptDone && $totalDistinct > 0) {
                $curWeekly = $weeklyModel->find($weekly_id);
                $otherStatusField = ($departemen === 'qc') ? 'status_production' : 'status_qc';
                $isOtherApproved  = (!empty($curWeekly[$otherStatusField]) && $curWeekly[$otherStatusField] === 'Approve');

                $weeklyUpdate = [
                    $statusField   => 'Approve',
                    $approverField => $approverName,
                    $dateField     => $now,
                    'updated_at'   => $now
                ];
                if ($isOtherApproved) {
                    $weeklyUpdate['status']        = 'Approved';
                    $weeklyUpdate['rejected_days'] = null;
                    $weeklyUpdate['reject_note']   = null;
                    $weeklyUpdate['rejected_by']   = null;
                    $weeklyUpdate['rejected_at']   = null;
                }
                $weeklyModel->update($weekly_id, $weeklyUpdate);
            }

            $machLabel = ($targetMachno !== '' && $targetMachno !== '-') ? "Mesin {$targetMachno}" : "Dokumen";
            $msg = "Dokumen {$machLabel} ({$targetDevice} - {$targetProcess}) berhasil di-approve untuk " . strtoupper($departemen) . " oleh {$approverName}.";
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
            }
            $this->session->setFlashdata('message', $msg);
            return redirect()->to($backUrlFallback);
        }

        // -------------------------------------------------------------------------
        // B. APPROVAL BATCH MINGGUAN (Dari Halaman Utama weekly?type=startup)
        // -------------------------------------------------------------------------
        // Syarat Wajib Lengkap: Seluruh daily checks pada minggu ini harus sudah selesai
        if ($action === 'Approve') {
            $pendingDaily = $db->table('startup')
                ->where('weekly_id', $weekly_id)
                ->where('par000', '1')
                ->groupStart()
                    ->where('status', 'Pending')
                    ->orWhere('status', 'Reject')
                    ->orWhere('status IS NULL')
                    ->orWhere('status', '')
                    ->orWhere('status', '-')
                ->groupEnd()
                ->countAllResults();

            if ($pendingDaily > 0) {
                $msg = "Persetujuan ditolak: Masih terdapat {$pendingDaily} checksheet startup harian pada minggu ini yang berstatus Pending atau Reject. Selesaikan perbaikan dan daily approval terlebih dahulu.";
                if ($isAjax) {
                    return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
                }
                $this->session->setFlashdata('error', $msg);
                return redirect()->to(base_url().'approval/weekly?type=startup');
            }
        }

        // Ambil data approval spesifik dokumen yang sudah ada (untuk SKIP dokumen yang sudah diapprove)
        $existingDocMap = $docModel->getGroupedByWeeklyId($weekly_id);

        $allDocs = $db->table('startup')
            ->select('device, process, COALESCE(machno, "") as machno')
            ->where('weekly_id', $weekly_id)
            ->where('par000', '1')
            ->groupBy('device, process, COALESCE(machno, "")')
            ->get()->getResultArray();

        $statusField   = ($departemen === 'qc') ? 'status_qc' : 'status_production';
        $approverField = ($departemen === 'qc') ? 'approver_qc' : 'approver_production';
        $dateField     = ($departemen === 'qc') ? 'date_qc' : 'date_production';

        $skippedCount  = 0;
        $approvedCount = 0;

        foreach ($allDocs as $doc) {
            $dDev  = trim((string)$doc['device']);
            $dProc = trim((string)$doc['process']);
            $dMach = trim((string)$doc['machno']);
            $key   = $dDev . '___' . $dProc . '___' . $dMach;

            if (isset($existingDocMap[$key])) {
                $docRec = $existingDocMap[$key];
                // JIKA SUDAH APPROVED OLEH DEPARTEMEN INI: SKIP! TIDAK DITIMPA!
                if ($docRec[$statusField] === 'Approve') {
                    $skippedCount++;
                    continue;
                }
                $docModel->update($docRec['id'], [
                    $statusField   => 'Approve',
                    $approverField => $approverName,
                    $dateField     => $now,
                    'updated_at'   => $now
                ]);
                $approvedCount++;
            } else {
                $docModel->insert([
                    'weekly_id'           => $weekly_id,
                    'device'              => $dDev,
                    'process'             => $dProc,
                    'machno'              => $dMach,
                    'status_qc'           => ($departemen === 'qc') ? 'Approve' : 'Pending',
                    'approver_qc'         => ($departemen === 'qc') ? $approverName : null,
                    'date_qc'             => ($departemen === 'qc') ? $now : null,
                    'status_production'   => ($departemen === 'production') ? 'Approve' : 'Pending',
                    'approver_production' => ($departemen === 'production') ? $approverName : null,
                    'date_production'     => ($departemen === 'production') ? $now : null,
                    'created_at'          => $now,
                    'updated_at'          => $now
                ]);
                $approvedCount++;
            }
        }

        $curWeekly = $weeklyModel->find($weekly_id);
        $otherStatusField = ($departemen === 'qc') ? 'status_production' : 'status_qc';
        $isOtherApproved  = (!empty($curWeekly[$otherStatusField]) && $curWeekly[$otherStatusField] === 'Approve');

        $updateData = [
            $statusField   => 'Approve',
            $approverField => $approverName,
            $dateField     => $now,
            'updated_at'   => $now
        ];
        if ($isOtherApproved) {
            $updateData['status']        = 'Approved';
            $updateData['rejected_days'] = null;
            $updateData['reject_note']   = null;
            $updateData['rejected_by']   = null;
            $updateData['rejected_at']   = null;
        } else {
            $updateData['status'] = 'Pending';
        }
        $weeklyModel->update($weekly_id, $updateData);

        $skipInfo = ($skippedCount > 0) ? " ({$skippedCount} dokumen yang sudah diapprove satuan di-skip)" : "";
        $msg = "Data mingguan #{$weekly_id} berhasil di-approve untuk " . strtoupper($departemen) . " oleh {$approverName}.{$skipInfo}";
        
        if ($isAjax) {
            return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
        }
        $this->session->setFlashdata('message', $msg);

        return redirect()->to(base_url().'approval/weekly?type=startup');
    }
    
    /**
     * Auto-sync data startup minggu berjalan yang belum memiliki weekly_id
     * agar langsung muncul di tabel Weekly Approval secara real-time.
     */
    private function syncOngoingWeeklyStartup(): void
    {
        try {
            date_default_timezone_set('Asia/Jakarta');
            $db = \Config\Database::connect();
            // Potong 7 jam untuk memanipulasi referensi "This Week" (Senin 07:00 - Senin 06:59)
            $nowAdjusted = time() - (7 * 3600);
            $baseMonday = date('Y-m-d', strtotime('monday this week', $nowAdjusted));
            
            $startOfWeek = $baseMonday . ' 07:00:00';
            $endOfWeek   = date('Y-m-d', strtotime($baseMonday . ' +7 days')) . ' 06:59:59';
            $periodStart = date('Y-m-d', strtotime($startOfWeek));
            $periodEnd   = date('Y-m-d', strtotime($endOfWeek));

            $unassignedCount = $db->table('startup')
                ->where('created_at >=', $startOfWeek)
                ->where('created_at <=', $endOfWeek)
                ->where('weekly_id', null)
                ->countAllResults();

            if ($unassignedCount > 0) {
                $weeklyModel = new WeeklyApprovalModel();
                $existing = $weeklyModel->where('period_start', $periodStart)
                                        ->where('period_end', $periodEnd)
                                        ->groupStart()
                                            ->where('doc_type', 'startup')
                                            ->orWhere('doc_type IS NULL')
                                            ->orWhere('doc_type', '')
                                        ->groupEnd()
                                        ->first();

                if ($existing) {
                    $weeklyId = $existing['id'];
                } else {
                    $weeklyId = $weeklyModel->insert([
                        'doc_type'          => 'startup',
                        'device'            => null,
                        'process'           => null,
                        'period_start'      => $periodStart,
                        'period_end'        => $periodEnd,
                        'status'            => 'Pending',
                        'status_qc'         => 'Pending',
                        'status_production' => 'Pending',
                    ]);
                }

                if (!empty($weeklyId)) {
                    $db->table('startup')
                        ->where('created_at >=', $startOfWeek)
                        ->where('created_at <=', $endOfWeek)
                        ->where('weekly_id', null)
                        ->update(['weekly_id' => $weeklyId]);
                }
            }

            // Normalisasi status mingguan yang sempat Rejected:
            // Jika seluruh dokumen harian yang di-reject sudah di-approve oleh Foreman (tidak ada lagi yang berstatus Reject),
            // kembalikan status mingguan menjadi 'Pending' dan bersihkan catatan reject.
            $rejectedWeeklies = $db->table('weekly_approvals')
                ->where('status', 'Rejected')
                ->get()->getResultArray();

            foreach ($rejectedWeeklies as $rw) {
                $countReject = $db->table('startup')
                    ->where('weekly_id', $rw['id'])
                    ->where('par000', '1')
                    ->where('status', 'Reject')
                    ->countAllResults();

                if ($countReject === 0) {
                    $db->table('weekly_approvals')
                        ->where('id', $rw['id'])
                        ->update([
                            'status'              => 'Pending',
                            'status_qc'           => ($rw['status_qc'] === 'Approve') ? 'Approve' : 'Pending',
                            'status_production'   => ($rw['status_production'] === 'Approve') ? 'Approve' : 'Pending',
                            'rejected_days'       => null,
                            'reject_note'         => null,
                            'rejected_by'         => null,
                            'rejected_at'         => null,
                        ]);
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika error agar tidak memblokir alur utama
        }
    }

    // Fungsi untuk memproses Granular Day-Unlock Reject khusus Weekly Startup
    public function rejectWeeklyStartup()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weekly_id     = $this->request->getPost('weekly_id');
        $departemen    = strtolower(trim((string)$this->request->getPost('departemen')));
        $rejected_days = $this->request->getPost('rejected_days');
        $reject_note   = trim((string)$this->request->getPost('reject_note'));
        $targetMachno  = trim((string)($this->request->getPost('machno') ?? $this->request->getPost('target_machno') ?? ''));
        $targetDevice  = trim((string)($this->request->getPost('device') ?? $this->request->getPost('target_device') ?? ''));
        $targetProcess = trim((string)($this->request->getPost('process') ?? $this->request->getPost('target_process') ?? ''));
        $redirectTo    = trim((string)($this->request->getPost('redirect_to') ?? 'weekly'));

        $weeklyModel = new WeeklyApprovalModel();
        $weeklyRecord = $weeklyModel->find($weekly_id);

        $backUrl = ($redirectTo === 'detail') ? base_url('approval/detail_mingguan/' . $weekly_id) : base_url('approval/weekly?type=startup');

        if (!$weeklyRecord) {
            $this->session->setFlashdata('error', 'Data approval mingguan tidak ditemukan.');
            return redirect()->to($backUrl);
        }

        $auth    = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        if ($departemen === 'qc' && !$canQc) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas penolakan (reject) QC/QA. Hanya Leader/Foreman/Supervisor QC/QA yang diizinkan melakukan reject QC.');
            return redirect()->to($backUrl);
        }
        if ($departemen === 'production' && !$canProd) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas penolakan (reject) Production. Hanya Leader/Foreman/Supervisor Production yang diizinkan melakukan reject Production.');
            return redirect()->to($backUrl);
        }

        if (empty($rejected_days) || !is_array($rejected_days)) {
            $this->session->setFlashdata('error', 'Gagal Reject: Anda wajib memilih minimal 1 hari/tanggal yang bermasalah!');
            return redirect()->to($backUrl);
        }

        if (empty($reject_note)) {
            $this->session->setFlashdata('error', 'Gagal Reject: Alasan penolakan / catatan revisi wajib diisi!');
            return redirect()->to($backUrl);
        }

        // Resolusi filter target mesin jika ALL atau strip
        if ($targetMachno === 'ALL' || $targetMachno === '-') {
            $targetMachno = '';
        }
        if ($targetDevice === 'ALL') {
            $targetDevice = '';
        }
        if ($targetProcess === 'ALL') {
            $targetProcess = '';
        }

        $db = \Config\Database::connect();
        $docModel = new WeeklyDocumentApprovalModel();
        $deptStatusField = ($departemen === 'qc') ? 'status_qc' : 'status_production';

        // 1. Validasi Proteksi: Jika menargetkan mesin tertentu yang sudah disetujui (Approved)
        if (!empty($targetMachno)) {
            $existingDoc = $docModel->getDocApproval($weekly_id, $targetDevice, $targetProcess, $targetMachno);
            if ($existingDoc && ($existingDoc[$deptStatusField] === 'Approve' || ($existingDoc['status_qc'] === 'Approve' && $existingDoc['status_production'] === 'Approve'))) {
                $this->session->setFlashdata('error', "Penolakan ditolak: Mesin {$targetMachno} sudah disetujui (Approved) secara spesifik dan tidak dapat di-reject.");
                return redirect()->to($backUrl);
            }
        }

        // 2. Validasi Proteksi: Jika menargetkan Semua Mesin (ALL)
        $approvedDocs = $docModel->where('weekly_id', $weekly_id)
            ->where($deptStatusField, 'Approve')
            ->findAll();
        $approvedMachnos = array_unique(array_filter(array_column($approvedDocs, 'machno')));

        $allBatchMachines = $db->table('startup')
            ->select('DISTINCT machno')
            ->where('weekly_id', $weekly_id)
            ->where('par000', '1')
            ->get()->getResultArray();
        $allBatchMachnos = array_unique(array_filter(array_column($allBatchMachines, 'machno')));

        if (empty($targetMachno) && !empty($allBatchMachnos) && count($approvedMachnos) >= count($allBatchMachnos)) {
            $this->session->setFlashdata('error', "Penolakan ditolak: Seluruh mesin pada minggu ini sudah berstatus Approved dan tidak dapat di-reject.");
            return redirect()->to($backUrl);
        }

        $approverName = $this->session->get('name') ?? 'Approver';
        $approverRole = ($departemen === 'qc' || ($canQc && !$canProd)) ? 'QC' : 'Production';
        if ($departemen === 'production' && $canProd) {
            $approverRole = 'Production';
        }
        $rejectedBy = "{$approverName} ({$approverRole})";
        $now = date('Y-m-d H:i:s');

        $machineLabel = !empty($targetMachno) ? "Mesin {$targetMachno}" : "Semua Mesin";
        $fullRejectReason = "[Weekly Reject oleh {$rejectedBy} - {$machineLabel}]: {$reject_note}";

        $db = \Config\Database::connect();
        $unlockedDatesFormatted = [];
        $dayNamesIndo = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        // Granular Day-Unlock: Reset Daily Approval Foreman hanya pada mesin & tanggal-tanggal yang dipilih
        foreach ($rejected_days as $day) {
            $dayClean = trim((string)$day);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayClean)) {
                continue;
            }

            $ts = strtotime($dayClean);
            $dayName = $dayNamesIndo[date('l', $ts)] ?? date('l', $ts);
            $unlockedDatesFormatted[] = $dayName . ' (' . date('d/m', $ts) . ')';

            $syncStartup = $db->table('startup')
                ->where('COALESCE(work_date, DATE(created_at)) =', $dayClean)
                ->where('par000', '1')
                ->groupStart()
                    ->where('weekly_id', $weekly_id)
                    ->orGroupStart()
                        ->where('weekly_id', null)
                        ->orWhere('weekly_id', 0)
                    ->groupEnd()
                ->groupEnd();

            if (!empty($targetDevice)) {
                $syncStartup->where('device', $targetDevice);
            } elseif (!empty($weeklyRecord['device'])) {
                $syncStartup->where('device', $weeklyRecord['device']);
            }

            if (!empty($targetProcess)) {
                $syncStartup->where('process', $targetProcess);
            } elseif (!empty($weeklyRecord['process'])) {
                $syncStartup->where('process', $weeklyRecord['process']);
            }

            if (!empty($targetMachno)) {
                $syncStartup->where('machno', $targetMachno);
            } elseif (!empty($weeklyRecord['machno'])) {
                $syncStartup->where('machno', $weeklyRecord['machno']);
            } elseif (!empty($approvedMachnos)) {
                // Jangan sentuh / unlock mesin-mesin yang sudah diapprove
                $syncStartup->whereNotIn('machno', $approvedMachnos);
            }

            $syncStartup->update([
                'status'        => 'Reject',
                'reject_reason' => $fullRejectReason,
                'reject_by'     => $rejectedBy,
                'reject_date'   => $now,
                'edit_status'   => 1, // Unlock form agar operator/foreman dapat mengedit
                'unlocked_by'   => $approverName,
                // Reset daily approval signatures agar Foreman/Leader wajib memeriksa & approve ulang
                'foreman'       => null,
                'leader'        => null,
                'supervisor'    => null,
                'role'          => null,
                'weekly_id'     => $weekly_id
            ]);
        }

        // Update record di weekly_approvals: status menjadi 'Rejected', reset sign-off QC & Prod
        $rejectedDaysStr = (!empty($targetMachno) ? "[{$targetMachno}] " : "") . implode(', ', $rejected_days);
        $fullRejectNoteWeekly = (!empty($targetMachno) ? "[Mesin {$targetMachno}]: " : "") . $reject_note;

        $weeklyModel->update($weekly_id, [
            'status'              => 'Rejected',
            'status_qc'           => 'Pending',
            'status_production'   => 'Pending',
            'approver_qc'         => null,
            'date_qc'             => null,
            'approver_production' => null,
            'date_production'     => null,
            'reject_note'         => $fullRejectNoteWeekly,
            'rejected_days'       => $rejectedDaysStr,
            'rejected_by'         => $rejectedBy,
            'rejected_at'         => $now,
            'updated_at'          => $now
        ]);

        $formattedListStr = implode(', ', $unlockedDatesFormatted);
        $successMsg = "Checksheet {$machineLabel} pada hari [{$formattedListStr}] berhasil di-Reject! Status checksheet harian telah di-unlock menjadi 'Reject' agar Foreman dan Operator dapat melakukan perbaikan.";
        $this->session->setFlashdata('message', $successMsg);
        $this->session->setFlashdata('success', $successMsg);

        return redirect()->to($backUrl);
    }
    
    private function getFolderType($processStr) 
    {
        if (empty($processStr)) return 'startup';
        $parts = explode('-', $processStr);
        if (count($parts) > 1) {
            if ($parts[1] === 'p') return 'production';
            if ($parts[1] === 'f') return 'foregoing';
        }
        return 'startup';
    }

    // Fungsi untuk menampilkan isi dokumen dari satu periode mingguan (dikelompokkan per Form Unit Mesin 7 Hari)
    public function detail_mingguan($id)
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        $dataMingguan = $weeklyModel->find($id);

        if(empty($dataMingguan)) {
            return "Data mingguan tidak ditemukan.";
        }

        $db = \Config\Database::connect();
        $deviceInfo = !empty($dataMingguan['device']) ? $db->table('device')->where('code', $dataMingguan['device'])->get()->getRowArray() : null;
        $processInfo = !empty($dataMingguan['process']) ? $db->table('proses')->where('process_code', $dataMingguan['process'])->get()->getRowArray() : null;

        // Auto-sinkronisasi: update weekly_id untuk checksheet startup dalam rentang tanggal minggu ini yang belum ter-assign
        if (!empty($dataMingguan['period_start']) && !empty($dataMingguan['period_end'])) {
            $startStr = date('Y-m-d', strtotime($dataMingguan['period_start'])) . ' 00:00:00';
            $endStr   = date('Y-m-d', strtotime($dataMingguan['period_end'])) . ' 23:59:59';

            $syncBuilder = $db->table('startup')
                ->where('created_at >=', $startStr)
                ->where('created_at <=', $endStr)
                ->groupStart()
                    ->where('weekly_id', null)
                    ->orWhere('weekly_id', 0)
                ->groupEnd();

            if (!empty($dataMingguan['device'])) {
                $syncBuilder->where('device', $dataMingguan['device']);
            }
            if (!empty($dataMingguan['process'])) {
                $syncBuilder->where('process', $dataMingguan['process']);
            }
            $syncBuilder->update(['weekly_id' => $id]);
        }

        $rawDocs = $this->StartupModel->getByWeeklyId($id, $dataMingguan['period_start'] ?? null, $dataMingguan['period_end'] ?? null);

        // Fallback jika belum ter-assign weekly_id namun ada periode tanggalnya
        if (empty($rawDocs) && !empty($dataMingguan['period_start']) && !empty($dataMingguan['period_end'])) {
            $rawDocs = $this->StartupModel->getWeeklyChecksheetData($id, '', '', '', $dataMingguan['period_start'], $dataMingguan['period_end']);
        }

        $pendingDaily = 0;
        $rejectedDaily = 0;
        $groupedData = [];

        $dayNamesIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($rawDocs as $doc) {
            $devCode   = !empty($doc['final_device']) ? $doc['final_device'] : (!empty($doc['device']) ? $doc['device'] : '');
            $procCode  = $doc['process'] ?? '';
            $machNo    = trim((string)($doc['machno'] ?? ''));

            $groupKey = $devCode . '___' . $procCode . '___' . $machNo;

            $st = trim((string)($doc['status'] ?? ''));
            $isPending = ($st === 'Pending' || $st === '' || $st === '-');
            $isRejected = ($st === 'Reject');
            if ($isPending) {
                $pendingDaily++;
            }
            if ($isRejected) {
                $rejectedDaily++;
            }

            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'device'             => $devCode,
                    'device_name'        => $doc['device_name'] ?? $devCode,
                    'process'            => $procCode,
                    'process_name'       => $doc['process_name'] ?? $procCode,
                    'machno'             => $machNo,
                    'total_days'         => 0,
                    'dates'              => [],
                    'dates_detail'       => [],
                    'lots'               => [],
                    'models'             => [],
                    'pending_count'      => 0,
                    'approved_count'     => 0,
                    'rejected_count'     => 0,
                    'first_date'         => $doc['created_at'],
                    'last_date'          => $doc['created_at'],
                    'sample_number'      => $doc['number'] ?? ''
                ];
            }

            $fullDateYmd = getRecordWorkDate($doc, 'Y-m-d');
            $workTs = strtotime($fullDateYmd);
            $dateLabel = date('d/m', $workTs);
            $dayName = $dayNamesIndo[(int)date('w', $workTs)];

            if (!in_array($dateLabel, $groupedData[$groupKey]['dates'])) {
                $groupedData[$groupKey]['dates'][] = $dateLabel;
            }

            $canonicalSt = $isRejected ? 'Reject' : ($isPending ? 'Pending' : 'Approved');

            if (!isset($groupedData[$groupKey]['dates_detail'][$fullDateYmd])) {
                $groupedData[$groupKey]['dates_detail'][$fullDateYmd] = [
                    'date'          => $fullDateYmd,
                    'date_label'    => $dayName . ', ' . date('d/m/Y', $workTs),
                    'status'        => $canonicalSt,
                    'raw_status'    => $st,
                    'reject_reason' => $doc['reject_reason'] ?? '',
                    'reject_by'     => $doc['reject_by'] ?? '',
                    'count'         => 1
                ];
            } else {
                $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['count']++;
                if ($isRejected) {
                    $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['status'] = 'Reject';
                    $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['reject_reason'] = $doc['reject_reason'] ?? '';
                    $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['reject_by'] = $doc['reject_by'] ?? '';
                } elseif ($isPending && $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['status'] !== 'Reject') {
                    $groupedData[$groupKey]['dates_detail'][$fullDateYmd]['status'] = 'Pending';
                }
            }

            if (!empty($doc['lotno']) && !in_array($doc['lotno'], $groupedData[$groupKey]['lots'])) {
                $groupedData[$groupKey]['lots'][] = $doc['lotno'];
            }
            if (!empty($doc['model']) && !in_array($doc['model'], $groupedData[$groupKey]['models'])) {
                $groupedData[$groupKey]['models'][] = $doc['model'];
            }
            if ($isRejected) {
                $groupedData[$groupKey]['rejected_count']++;
            } elseif ($isPending) {
                $groupedData[$groupKey]['pending_count']++;
            } else {
                $groupedData[$groupKey]['approved_count']++;
            }

            if (strtotime($doc['created_at']) < strtotime($groupedData[$groupKey]['first_date'])) {
                $groupedData[$groupKey]['first_date'] = $doc['created_at'];
            }
            if (strtotime($doc['created_at']) > strtotime($groupedData[$groupKey]['last_date'])) {
                $groupedData[$groupKey]['last_date'] = $doc['created_at'];
            }
        }

        // Hitung total_days dan sematkan status approval per dokumen
        $docModel = new WeeklyDocumentApprovalModel();
        $docApprovals = $docModel->getGroupedByWeeklyId($id);

        foreach ($groupedData as $k => $item) {
            $groupedData[$k]['total_days'] = count($item['dates']);
            $docKey = trim((string)$item['device']) . '___' . trim((string)$item['process']) . '___' . trim((string)$item['machno']);
            $groupedData[$k]['doc_approval'] = $docApprovals[$docKey] ?? null;
        }

        // Urutkan grup berdasarkan device, proses, nomor mesin
        uasort($groupedData, function($a, $b) {
            $c1 = strcmp((string)$a['device'], (string)$b['device']);
            if ($c1 !== 0) return $c1;
            $c2 = strcmp((string)$a['process'], (string)$b['process']);
            if ($c2 !== 0) return $c2;
            return strcmp((string)$a['machno'], (string)$b['machno']);
        });

        $auth    = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        $data = [
            'title'          => 'Detail Dokumen Mingguan | Startup Management',
            'mingguan'       => $dataMingguan,
            'device_info'    => $deviceInfo,
            'process_info'   => $processInfo,
            'pending_daily'  => $pendingDaily,
            'rejected_daily' => $rejectedDaily,
            'grouped_data'   => array_values($groupedData),
            'alldata'        => $rawDocs,
            'user_role'      => $this->session->get('role'),
            'user_dept'      => $auth['department'],
            'is_admin'       => $isAdmin,
            'canQc'          => $canQc,
            'canProd'        => $canProd,
            'can_qc'         => $canQc,
            'can_prod'       => $canProd,
            'is_qc'          => $canQc,
            'is_prod'        => $canProd
        ];

        return view('weekly_detail', $data);
    }
    
    // Fungsi untuk menampilkan Form Checksheet Mingguan Lengkap (7 Hari: Senin s/d Minggu 23:59)
    public function weekly_form($weekly_id)
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        $weeklyData = $weeklyModel->find($weekly_id);

        if(empty($weeklyData)) {
            return "Data mingguan tidak ditemukan.";
        }

        $device  = $this->request->getGet('device') ?? '';
        $process = $this->request->getGet('process') ?? '';
        $machno  = $this->request->getGet('machno') ?? '';

        $periodStart = $weeklyData['period_start'];
        $periodEnd   = $weeklyData['period_end'];

        $alldata = $this->StartupModel->getWeeklyChecksheetData($weekly_id, $device, $process, $machno, $periodStart, $periodEnd);

        // Fallback jika tidak menemukan dengan parameter spesifik machno
        if (empty($alldata)) {
            $alldata = $this->StartupModel->getWeeklyChecksheetData($weekly_id, $device, $process, '', $periodStart, $periodEnd);
        }

        if (empty($alldata)) {
            return "Tidak ada data checksheet startup untuk mesin/proses ini pada periode " . date('d/m/Y', strtotime($periodStart)) . " s/d " . date('d/m/Y', strtotime($periodEnd)) . ".";
        }

        $firstRow = $alldata[0];
        $actualDevice  = !empty($device) ? $device : ($firstRow['device'] ?? '');
        $actualProcess = !empty($process) ? $process : ($firstRow['process'] ?? '');
        $actualMachno  = ($machno !== '' && $machno !== 'null') ? $machno : ($firstRow['machno'] ?? '');

        $db = \Config\Database::connect();
        $deviceInfo  = $db->table('device')->where('code', $actualDevice)->get()->getRowArray();
        $processInfo = $db->table('proses')->where('process_code', $actualProcess)->get()->getRowArray();

        $folder = $this->getFolderType($actualProcess);

        $machnoEsc = esc($actualMachno);
        $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machnoEsc . '">';

        try {
            $viewPath = "/layout/" . $actualDevice . "/history/" . $folder . "/" . $actualProcess;
            $tableHtml = view($viewPath, ['alldata' => $alldata]);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            $tableHtml = "<div class='alert alert-danger'>Template form checksheet untuk proses " . esc($actualProcess) . " belum tersedia (File layout tidak ditemukan).</div>";
        }

        $docModel = new WeeklyDocumentApprovalModel();
        $docApproval = $docModel->getDocApproval($weekly_id, $actualDevice, $actualProcess, $actualMachno);

        $auth    = $this->getUserApprovalAuthority();
        $isAdmin = $auth['is_admin'];
        $canQc   = $auth['can_qc'];
        $canProd = $auth['can_production'];

        // Cek jika terdapat daily checksheet yang belum di-approve untuk dokumen ini
        $pendingDocQ = $db->table('startup')
            ->where('weekly_id', $weekly_id)
            ->where('device', $actualDevice)
            ->where('process', $actualProcess)
            ->where('par000', '1')
            ->groupStart()
                ->where('status', 'Pending')
                ->orWhere('status', 'Reject')
                ->orWhere('status IS NULL')
                ->orWhere('status', '')
                ->orWhere('status', '-')
            ->groupEnd();

        if ($actualMachno !== '' && $actualMachno !== '-' && $actualMachno !== 'null') {
            $pendingDocQ->where('machno', $actualMachno);
        } else {
            $pendingDocQ->groupStart()
                ->where('machno', '')
                ->orWhere('machno', '-')
                ->orWhere('machno IS NULL')
                ->orWhere('machno', 'null')
            ->groupEnd();
        }
        $pendingDailyDocCount = $pendingDocQ->countAllResults();

        $data = [
            'title'               => 'Checksheet Mingguan (7 Hari) - ' . ($processInfo['name'] ?? $actualProcess),
            'weekly_id'           => $weekly_id,
            'period_start'        => $periodStart,
            'period_end'          => $periodEnd,
            'device'              => $actualDevice,
            'device_name'         => $deviceInfo['name'] ?? $actualDevice,
            'process'             => $actualProcess,
            'process_name'        => $processInfo['name'] ?? $actualProcess,
            'machno'              => $actualMachno,
            'alldata'             => $alldata,
            'machnoHidden'        => $machnoHidden,
            'tableHtml'           => $tableHtml,
            'docApproval'         => $docApproval,
            'canQc'               => $canQc,
            'canProd'             => $canProd,
            'can_qc'              => $canQc,
            'can_prod'            => $canProd,
            'isAdmin'             => $isAdmin,
            'user_dept'           => $auth['department'],
            'user_role'           => $this->session->get('role'),
            'pending_daily_count' => $pendingDailyDocCount
        ];

        return view('weekly_form_detail', $data);
    }

    // Fungsi untuk menampilkan Form Checksheet Production Lengkap (1 Lembar / 20 Data)
    public function detail_production($id)
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        $batch = $weeklyModel->find($id);

        if(empty($batch)) {
            return "Data batch production tidak ditemukan.";
        }

        $alldata = $this->ProductionModel->getByWeeklyId($id);

        if (empty($alldata)) {
            return "Tidak ada data checksheet production untuk batch #" . esc($id);
        }

        $firstRow = $alldata[0];
        $actualDevice  = !empty($batch['device']) ? $batch['device'] : ($firstRow['device'] ?? '');
        $actualProcess = !empty($batch['process']) ? $batch['process'] : ($firstRow['process'] ?? '');
        $actualMachno  = !empty($batch['machno']) ? $batch['machno'] : ($firstRow['machno'] ?? '');

        $db = \Config\Database::connect();
        $deviceInfo  = $db->table('device')->where('code', $actualDevice)->get()->getRowArray();
        $processInfo = $db->table('proses')->where('process_code', $actualProcess)->get()->getRowArray();

        $machnoEsc = esc($actualMachno);
        $machnoHidden = '<input type="hidden" id="detail-machno" value="' . $machnoEsc . '">';

        try {
            $viewPath = "/layout/" . $actualDevice . "/history/production/" . $actualProcess;
            $tableHtml = view($viewPath, ['alldata' => $alldata]);
        } catch (\CodeIgniter\View\Exceptions\ViewException $e) {
            $tableHtml = "<div class='alert alert-danger'>Template form checksheet untuk proses " . esc($actualProcess) . " belum tersedia (File layout tidak ditemukan).</div>";
        }

        $data = [
            'title'        => 'Checksheet Production (20 Data / 1 Lembar) - ' . ($processInfo['name'] ?? $actualProcess),
            'weekly_id'    => $id,
            'batch'        => $batch,
            'period_start' => $batch['period_start'],
            'period_end'   => $batch['period_end'],
            'device'       => $actualDevice,
            'device_name'  => $deviceInfo['name'] ?? $actualDevice,
            'process'      => $actualProcess,
            'process_name' => $processInfo['name'] ?? $actualProcess,
            'machno'       => $actualMachno,
            'alldata'      => $alldata,
            'machnoHidden' => $machnoHidden,
            'tableHtml'    => $tableHtml
        ];

        return view('weekly_production_detail', $data);
    }
}