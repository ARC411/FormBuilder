<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StartupModel;
use App\Models\LogActivityModel;
use App\Models\WeeklyApprovalModel;
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

    public function weeklyStartup()
    {
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        // Sinkronisasi otomatis data startup minggu berjalan (misal periode 26 Oct s/d 01 Nov) agar langsung muncul real-time
        $this->syncOngoingWeeklyStartup();

        $weeklyModel = new WeeklyApprovalModel();
        $role = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));

        $is_qc = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $is_prod = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        $weeklyList = $weeklyModel->getWeeklyList();

        // Ambil data tanggal checksheet aktif untuk setiap weekly_id agar modal reject dapat menampilkan info jumlah checksheet per hari
        $db = \Config\Database::connect();
        $weeklyIds = array_filter(array_column($weeklyList, 'id'));
        $datesByWeekly = [];
        $machinesByWeekly = [];

        if (!empty($weeklyIds)) {
            $activeDatesQuery = $db->table('startup')
                ->select('weekly_id, DATE(created_at) as doc_date, COUNT(*) as doc_count, SUM(CASE WHEN status = "Reject" THEN 1 ELSE 0 END) as reject_count, SUM(CASE WHEN status = "Pending" OR status = "" OR status = "-" OR status IS NULL THEN 1 ELSE 0 END) as pending_count')
                ->where('par000', '1')
                ->whereIn('weekly_id', $weeklyIds)
                ->groupBy('weekly_id, DATE(created_at)')
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
            'is_admin'    => $isAdmin,
            'is_qc'       => $is_qc,
            'is_prod'     => $is_prod
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
                      DATE(startup.created_at) as doc_date, 
                      startup.status,
                      proses.name as process_name,
                      device.name as device_name')
            ->join('proses', 'proses.process_code = startup.process', 'left')
            ->join('device', 'device.code = COALESCE(startup.device, proses.device)', 'left')
            ->where('startup.par000', '1')
            ->where('startup.weekly_id', $weeklyId)
            ->orderBy('startup.device', 'ASC')
            ->orderBy('startup.machno', 'ASC')
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

        $role = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));

        $is_qc = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $is_prod = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        // Ambil data batch lembar production yang sudah diurutkan
        $productionData = $weeklyModel->getProductionBatchList($sort);

        $data = [
            'title'           => 'Page Approval Data â€“ Production Checksheet (Per 20 Data)',
            'production_data' => $productionData,
            'sort_dir'        => $sort,
            'next_sort'       => ($sort === 'desc' ? 'asc' : 'desc'),
            'user_role'       => $this->session->get('role'),
            'is_admin'        => $isAdmin,
            'is_qc'           => $is_qc,
            'is_prod'         => $is_prod
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
        if(!$this->session->has('name')){
            return redirect()->to(base_url().'login');
        }

        $weeklyModel = new WeeklyApprovalModel();
        
        $weekly_id  = $this->request->getPost('weekly_id');
        $action     = $this->request->getPost('action'); // 'Approve' atau 'Reject'
        $departemen = strtolower(trim((string)$this->request->getPost('departemen'))); // 'qc' atau 'production'

        $role    = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));
        $canQc   = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $canProd = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        $batch = $weeklyModel->find($weekly_id);
        if (!$batch) {
            $this->session->setFlashdata('error', 'Data approval tidak ditemukan.');
        return redirect()->to(base_url().'approval/weekly?type=startup');
        }

        $isProduction = (!empty($batch['doc_type']) && $batch['doc_type'] === 'production');

        // =========================================================================
        // 1. LOGIKA APPROVAL PRODUCTION (Per 20 Data, 1 Check QC atau Prod salah satu)
        // =========================================================================
        if ($isProduction) {
            // Validasi Otoritas Akses: bisa QC atau Production (salah satu)
            if (!$canQc && !$canProd) {
                $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC atau Production.');
        return redirect()->to(base_url().'approval/weekly?type=production');
            }

            // Syarat Wajib Penuh: Harus sudah mencapai tepat 20 data per lembar
            $db = \Config\Database::connect();
            $totalCount = $db->table('production')
                ->where('weekly_id', $weekly_id)
                ->where('par001', 1)
                ->countAllResults();

            if ($action === 'Approve' && $totalCount < 20) {
                $this->session->setFlashdata('error', "Persetujuan ditolak: Lembar checksheet ini belum penuh (baru {$totalCount}/20 data). Tunggu sampai genap 20 data (1 lembar penuh) sebelum approval.");
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
            $this->session->setFlashdata('message', "Lembar Production #{$weekly_id}{$pageInfo} berhasil di-{$action} oleh {$approverName} ({$approverRole}).");

        return redirect()->to(base_url().'approval/weekly?type=production');
        }

        // =========================================================================
        // 2. LOGIKA APPROVAL STARTUP MINGGUAN (Existing - 100% Tetap Dipertahankan)
        // =========================================================================
        // Validasi Otoritas Akses Departemen
        if ($departemen === 'qc' && !$canQc) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC.');
        return redirect()->to(base_url().'approval/weekly?type=startup');
        }
        if ($departemen === 'production' && !$canProd) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan Production.');
        return redirect()->to(base_url().'approval/weekly?type=startup');
        }

        // Syarat Wajib Lengkap Startup: Daily approval harus sudah selesai
        if ($action === 'Approve') {
            $db = \Config\Database::connect();
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
                $this->session->setFlashdata('error', "Persetujuan ditolak: Masih terdapat {$pendingDaily} checksheet startup harian pada minggu ini yang berstatus Pending atau Reject. Selesaikan perbaikan dan daily approval terlebih dahulu.");
        return redirect()->to(base_url().'approval/weekly?type=startup');
            }
        }

        $approverName = $this->session->get('name') ?? 'Approver';
        $now = date('Y-m-d H:i:s');

        $updateData = [];
        if ($departemen === 'qc') {
            $updateData['status_qc']   = $action;
            $updateData['approver_qc'] = $approverName;
            $updateData['date_qc']     = $now;
        } else if ($departemen === 'production') {
            $updateData['status_production']   = $action;
            $updateData['approver_production'] = $approverName;
            $updateData['date_production']     = $now;
        }

        if (!empty($updateData)) {
            $curWeekly = $weeklyModel->find($weekly_id);
            $qcApproved = ($departemen === 'qc' && $action === 'Approve') || (!empty($curWeekly['status_qc']) && $curWeekly['status_qc'] === 'Approve');
            $prodApproved = ($departemen === 'production' && $action === 'Approve') || (!empty($curWeekly['status_production']) && $curWeekly['status_production'] === 'Approve');

            if ($action === 'Approve') {
                if ($qcApproved && $prodApproved) {
                    $updateData['status']        = 'Approved';
                    $updateData['rejected_days'] = null;
                    $updateData['reject_note']   = null;
                    $updateData['rejected_by']   = null;
                    $updateData['rejected_at']   = null;
                } else {
                    $updateData['status'] = 'Pending';
                }
            }

            $weeklyModel->update($weekly_id, $updateData);
            $this->session->setFlashdata('message', "Data mingguan #{$weekly_id} berhasil di-{$action} oleh {$approverName} (" . strtoupper($departemen) . ").");
        }

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
            $startOfWeek = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $endOfWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
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

        $role = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));
        $canQc = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $canProd = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

        if ($departemen === 'qc' && !$canQc) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan QC.');
            return redirect()->to($backUrl);
        }
        if ($departemen === 'production' && !$canProd) {
            $this->session->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki otoritas persetujuan Production.');
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

            $startOfDay = $dayClean . ' 00:00:00';
            $endOfDay   = $dayClean . ' 23:59:59';

            $syncStartup = $db->table('startup')
                ->where('created_at >=', $startOfDay)
                ->where('created_at <=', $endOfDay)
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

            $dateLabel = date('d/m', strtotime($doc['created_at']));
            $fullDateYmd = date('Y-m-d', strtotime($doc['created_at']));
            $dayTs = strtotime($doc['created_at']);
            $dayName = $dayNamesIndo[(int)date('w', $dayTs)];

            if (!in_array($dateLabel, $groupedData[$groupKey]['dates'])) {
                $groupedData[$groupKey]['dates'][] = $dateLabel;
            }

            $canonicalSt = $isRejected ? 'Reject' : ($isPending ? 'Pending' : 'Approved');

            if (!isset($groupedData[$groupKey]['dates_detail'][$fullDateYmd])) {
                $groupedData[$groupKey]['dates_detail'][$fullDateYmd] = [
                    'date'          => $fullDateYmd,
                    'date_label'    => $dayName . ', ' . date('d/m/Y', $dayTs),
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

        // Hitung total_days berdasarkan jumlah hari/tanggal unik yang terisi
        foreach ($groupedData as $k => $item) {
            $groupedData[$k]['total_days'] = count($item['dates']);
        }

        // Urutkan grup berdasarkan device, proses, nomor mesin
        uasort($groupedData, function($a, $b) {
            $c1 = strcmp((string)$a['device'], (string)$b['device']);
            if ($c1 !== 0) return $c1;
            $c2 = strcmp((string)$a['process'], (string)$b['process']);
            if ($c2 !== 0) return $c2;
            return strcmp((string)$a['machno'], (string)$b['machno']);
        });

        $role = strtolower(trim((string)$this->session->get('role')));
        $isAdmin = !empty($this->session->get('isadmin'));
        $canQc = ($role === 'qc' || $role === 'qa' || $role === 'all' || $isAdmin);
        $canProd = ($role === 'production' || in_array($role, ['fl', 'sl', 'all']) || $isAdmin);

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
            'is_admin'       => $isAdmin,
            'canQc'          => $canQc,
            'canProd'        => $canProd,
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

        $data = [
            'title'        => 'Checksheet Mingguan (7 Hari) - ' . ($processInfo['name'] ?? $actualProcess),
            'weekly_id'    => $weekly_id,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'device'       => $actualDevice,
            'device_name'  => $deviceInfo['name'] ?? $actualDevice,
            'process'      => $actualProcess,
            'process_name' => $processInfo['name'] ?? $actualProcess,
            'machno'       => $actualMachno,
            'alldata'      => $alldata,
            'machnoHidden' => $machnoHidden,
            'tableHtml'    => $tableHtml
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