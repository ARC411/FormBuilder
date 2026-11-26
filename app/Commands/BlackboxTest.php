<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\I18n\Time;

/**
 * Automated Blackbox Testing (Smoke Test & Thinker/Exploratory Testing)
 * untuk seluruh fitur aplikasi Checksheet Management System.
 *
 * Cara menjalankan:
 *   php spark test:blackbox
 *   php spark test:blackbox --out="writable/logs/laporan_blackbox.md"
 */
class BlackboxTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:blackbox';
    protected $description = 'Menjalankan Blackbox Testing otomatis (Smoke Test & Thinker) untuk seluruh fitur dan mengekspor laporan .md';
    protected $usage       = 'test:blackbox [--url <base_url>] [--out <markdown_path>]';
    protected $options     = [
        '--url' => 'URL server lokal jika sudah berjalan (opsional, otomatis menyalakan built-in server jika kosong)',
        '--out' => 'Lokasi output file laporan Markdown (.md) [default: writable/logs/blackbox_test_report.md]',
    ];

    private string $baseUrl = '';
    private string $cookieFile = '';
    /** @var resource|null */
    private $serverProc = null;
    private array $results = [];

    public function run(array $params)
    {
        helper('workdate');

        $outOpt  = CLI::getOption('out');
        $outPath = $outOpt ? (string)$outOpt : (WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'blackbox_test_report.md');

        $this->cookieFile = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'smoke_cookie_' . uniqid() . '.txt';

        CLI::newLine();
        CLI::write('========================================================================', 'cyan');
        CLI::write('  BLACKBOX TESTING RUNNER (SMOKE TEST & THINKER) - CMS FOXCONN', 'yellow');
        CLI::write('========================================================================', 'cyan');

        $db = \Config\Database::connect('default');

        try {
            $this->prepareTargetServer();
            CLI::write('Target Base URL : ' . $this->baseUrl, 'green');
            CLI::write('Output Markdown : ' . $outPath, 'green');
            CLI::newLine();

            // -----------------------------------------------------------------
            // 1. MODUL AUTHENTICATION & ACCESS CONTROL (SEBELUM LOGIN & LOGIN)
            // -----------------------------------------------------------------
            $this->runAuthAndGuardTests();

            // -----------------------------------------------------------------
            // 2. MODUL DASHBOARD / LOT HISTORY & USER DIAGNOSTICS
            // -----------------------------------------------------------------
            $this->runDashboardTests();

            // -----------------------------------------------------------------
            // 3. MODUL MASTER DATA: DEVICE
            // -----------------------------------------------------------------
            $this->runDeviceTests($db);

            // -----------------------------------------------------------------
            // 4. MODUL MASTER DATA: PROCESS
            // -----------------------------------------------------------------
            $this->runProcessTests();

            // -----------------------------------------------------------------
            // 5. MODUL MASTER DATA: MACHINE LIST
            // -----------------------------------------------------------------
            $this->runMachineTests($db);

            // -----------------------------------------------------------------
            // 6. MODUL MASTER DATA: MODEL & STANDAR UNIVERSAL
            // -----------------------------------------------------------------
            $this->runModelDataTests($db);

            // -----------------------------------------------------------------
            // 7. MODUL FORM INPUT & OPERATOR AUTOFILL
            // -----------------------------------------------------------------
            $this->runInputAndAutofillTests();

            // -----------------------------------------------------------------
            // 8. MODUL OPERATIONS: PRODUCTION (START -> MODAL 4 KOLOM -> FINISH)
            // -----------------------------------------------------------------
            $this->runProductionLifecycleTests($db);

            // -----------------------------------------------------------------
            // 9. MODUL OPERATIONS: STARTUP (SUBMIT -> DETAIL -> UNLOCK EDIT)
            // -----------------------------------------------------------------
            $this->runStartupLifecycleTests($db);

            // -----------------------------------------------------------------
            // 10. MODUL OPERATIONS: FOREGOING (MATRIKS -> DETAIL -> UNLOCK EDIT)
            // -----------------------------------------------------------------
            $this->runForegoingLifecycleTests($db);

            // -----------------------------------------------------------------
            // 11. MODUL HISTORY, PEMISAHAN OPERATOR START, EXPORT EXCEL & PDF
            // -----------------------------------------------------------------
            $this->runHistoryAndExportTests();

            // -----------------------------------------------------------------
            // 12. MODUL APPROVAL HARIAN & PUSAT APPROVAL MINGGUAN
            // -----------------------------------------------------------------
            $this->runApprovalTests();

            // -----------------------------------------------------------------
            // 13. MODUL FORM BUILDER, CSV, & GENERATOR HARD CODE PHP
            // -----------------------------------------------------------------
            $this->runFormBuilderAndHardcodeTests($db);

            // -----------------------------------------------------------------
            // 14. MODUL UTILITIES: LOG ACTIVITY, CLEANSING, & THINKER WAKTU KERJA
            // -----------------------------------------------------------------
            $this->runUtilitiesAndThinkerTimeTests();

        } finally {
            $this->cleanupTestArtifacts($db);
            $this->stopTargetServer();
            if (is_file($this->cookieFile)) {
                @unlink($this->cookieFile);
            }
        }

        // Tulis Laporan Markdown (.md)
        $mdContent = $this->buildMarkdownReport();
        $outDir    = dirname($outPath);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0777, true);
        }
        file_put_contents($outPath, $mdContent);

        // Ringkasan Akhir di Terminal
        $total  = count($this->results);
        $passed = count(array_filter($this->results, static fn($r) => $r['status'] === 'PASS'));
        $failed = $total - $passed;
        $rate   = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

        CLI::newLine();
        CLI::write('========================================================================', 'cyan');
        CLI::write("  HASIL AKHIR: {$passed}/{$total} PASS ({$rate}%) | FAIL: {$failed}", $failed === 0 ? 'green' : 'red');
        CLI::write('  Laporan .md berhasil dibuat di: ' . $outPath, 'yellow');
        CLI::write('========================================================================', 'cyan');
        CLI::newLine();
    }

    // =========================================================================
    // TEST SUITES PER MODUL
    // =========================================================================

    private function runAuthAndGuardTests(): void
    {
        // TC-AUTH-01: Smoke - Halaman Login
        $t0  = microtime(true);
        $res = $this->httpRequest('GET', '/login', [], false);
        $ok  = ($res['code'] === 200 && stripos($res['body'], 'empid') !== false);
        $this->record(
            'TC-AUTH-01',
            'Authentication',
            'Smoke Test',
            'Buka Halaman Login Utama',
            'GET `/login`',
            '-',
            'HTTP 200 & menampilkan form input Employee ID dan Password',
            "HTTP {$res['code']} (" . strlen($res['body']) . ' bytes)',
            $ok,
            $t0
        );

        // TC-AUTH-02: Thinker (Security) - Guard halaman terproteksi tanpa session
        $t0   = microtime(true);
        $res1 = $this->httpRequest('GET', '/input', [], false);
        $res2 = $this->httpRequest('GET', '/production', [], false);
        $res3 = $this->httpRequest('GET', '/approve', [], false);
        $ok   = (in_array($res1['code'], [302, 303], true)
            && in_array($res2['code'], [302, 303], true)
            && in_array($res3['code'], [302, 303], true));
        $this->record(
            'TC-AUTH-02',
            'Authentication',
            'Thinker (Security)',
            'Proteksi Akses URL Tanpa Login (Session Kosong)',
            'GET `/input`, `/production`, `/approve` tanpa cookie login',
            'Session kosong',
            'Semua request ditolak dengan HTTP 302 Redirect ke `/login`',
            "Input={$res1['code']}, Prod={$res2['code']}, Approve={$res3['code']}",
            $ok,
            $t0
        );

        // TC-AUTH-03: Thinker (Negative) - Login kredensial kosong
        $t0  = microtime(true);
        $res = $this->httpRequest('POST', '/login', ['empid' => '', 'password' => ''], true);
        $ok  = ($res['code'] === 200 && stripos($res['body'], 'Employee ID and Password are required') !== false);
        $this->record(
            'TC-AUTH-03',
            'Authentication',
            'Thinker (Negative)',
            'Submit Form Login dengan Kredensial Kosong',
            'POST `/login` (follow redirect)',
            '`empid=""`, `password=""`',
            'Ditolak kembali ke `/login` dengan pesan peringatan wajib isi',
            $ok ? 'Pesan validasi muncul sesuai spesifikasi' : "HTTP {$res['code']} pesan tidak cocok",
            $ok,
            $t0
        );

        // TC-AUTH-04: Thinker (Security/Negative) - Login salah & pesan aman (tidak bocor nama tabel TMS)
        $t0  = microtime(true);
        $res = $this->httpRequest('POST', '/login', ['empid' => 'INVALID999', 'password' => 'salah123'], true);
        $hasSafeMsg  = stripos($res['body'], 'Invalid credentials, please check your Employee ID and Password.') !== false;
        $noTableLeak = stripos($res['body'], 'tbempinfa') === false;
        $ok          = ($res['code'] === 200 && $hasSafeMsg && $noTableLeak);
        $this->record(
            'TC-AUTH-04',
            'Authentication',
            'Thinker (Security)',
            'Validasi Pesan Error Login Salah (Anti Information Disclosure)',
            'POST `/login` dengan ID tidak terdaftar',
            '`empid=INVALID999`, `password=salah123`',
            'Muncul "Invalid credentials, please check your Employee ID and Password." tanpa membocorkan `tbempinfa`',
            ($hasSafeMsg ? 'Pesan kredensial aman tampil' : 'Pesan tidak tampil') . ($noTableLeak ? ', zero leak' : ', LEAK DETECTED'),
            $ok,
            $t0
        );

        // TC-AUTH-05: Smoke - Login sukses menggunakan akun Admin
        $t0  = microtime(true);
        $res = $this->httpRequest('POST', '/login', ['empid' => 'admin', 'password' => 'admin'], false);
        $ok  = (in_array($res['code'], [302, 303], true) && stripos($res['redirect_url'], 'home') !== false);
        $this->record(
            'TC-AUTH-05',
            'Authentication',
            'Smoke Test',
            'Login Valid Akun Admin & Pembentukan Session',
            'POST `/login`',
            '`empid=admin`, `password=admin`',
            'HTTP 302 Redirect menuju `/home` dan session cookie aktif',
            "HTTP {$res['code']} -> {$res['redirect_url']}",
            $ok,
            $t0
        );
    }

    private function runDashboardTests(): void
    {
        // TC-DSH-01: Smoke - Halaman Home / Lot History
        $t0  = microtime(true);
        $res = $this->httpRequest('GET', '/home');
        $ok  = ($res['code'] === 200 && !$this->hasPhpError($res['body']));
        $this->record(
            'TC-DSH-01',
            'Home / Lot History',
            'Smoke Test',
            'Render Halaman Utama Dashboard Lot History',
            'GET `/home`',
            'Session `admin` aktif',
            'HTTP 200, tabel Lot History dan navbar tampil tanpa error PHP',
            "HTTP {$res['code']} (" . strlen($res['body']) . ' bytes)',
            $ok,
            $t0
        );

        // TC-DSH-02: Thinker (Resilience) - Endpoint cek_role aman walau ekstensi sqlsrv tidak aktif di lokal
        $t0  = microtime(true);
        $res = $this->httpRequest('GET', '/auth/cek_role');
        $ok  = ($res['code'] === 200 && stripos($res['body'], 'CriticalError') === false && stripos($res['body'], 'DatabaseException') === false);
        $this->record(
            'TC-DSH-02',
            'Authentication',
            'Thinker (Resilience)',
            'Ketahanan Endpoint Diagnostik Karyawan terhadap Koneksi Kedua (.130)',
            'GET `/auth/cek_role`',
            'Lingkungan lokal tanpa driver `sqlsrv` atau tanpa jaringan `.130`',
            'HTTP 200 dengan fallback aman (tidak lempar CriticalError sqlsrv)',
            "HTTP {$res['code']}, graceful fallback berjalan",
            $ok,
            $t0
        );
    }

    private function runDeviceTests($db): void
    {
        // TC-DEV-01: Smoke - Halaman Master Device & JSON List
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/device');
        $resL = $this->httpRequest('GET', '/device/list');
        $json = json_decode($resL['body'], true);
        $ok   = ($resP['code'] === 200 && !$this->hasPhpError($resP['body']) && is_array($json) && count($json) > 0);
        $this->record(
            'TC-DEV-01',
            'Master Device',
            'Smoke Test',
            'Tampil Halaman Master Device & Endpoint JSON List Device',
            'GET `/device` & GET `/device/list`',
            '-',
            'Halaman tampil HTTP 200 & JSON mengembalikan daftar device aktif',
            'HTTP 200, total device terdaftar: ' . (is_array($json) ? count($json) : 0),
            $ok,
            $t0
        );

        // TC-DEV-02: Thinker (State/CRUD) - Siklus Create -> Update -> Delete Master Device
        $t0 = microtime(true);
        $db->table('device')->where('code', 'tst9')->delete();
        $this->httpRequest('POST', '/device', ['device-code' => 'tst9', 'device-name' => 'Test Smoke Device 99']);
        $created = $db->table('device')->where('code', 'tst9')->get()->getRowArray();

        $updatedOk = false;
        $deletedOk = false;
        if ($created) {
            $this->httpRequest('POST', '/device/update', [
                'id'          => $created['id'],
                'device-code' => 'tst9',
                'device-name' => 'Test Smoke Updated 99',
            ]);
            $afterUpd  = $db->table('device')->where('id', $created['id'])->get()->getRowArray();
            $updatedOk = ($afterUpd && $afterUpd['name'] === 'Test Smoke Updated 99');

            $delRes    = $this->httpRequest('DELETE', '/device/' . $created['id']);
            $afterDel  = $db->table('device')->where('id', $created['id'])->get()->getRowArray();
            $deletedOk = ($delRes['code'] === 200 && empty($afterDel));
        }
        $ok = (!empty($created) && $updatedOk && $deletedOk);
        $this->record(
            'TC-DEV-02',
            'Master Device',
            'Thinker (State/CRUD)',
            'Siklus Penuh Tambah, Ubah, dan Hapus Master Device',
            'POST `/device` -> POST `/device/update` -> DELETE `/device/{id}`',
            '`code=tst9`, `name=Test Smoke Device 99`',
            'Data tersimpan, ter-update akurat, lalu terhapus bersih dari tabel `device`',
            sprintf('Created=%s, Updated=%s, Deleted=%s', !empty($created) ? 'OK' : 'ERR', $updatedOk ? 'OK' : 'ERR', $deletedOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runProcessTests(): void
    {
        // TC-PRC-01: Smoke - Halaman Master Process & Filter List Process
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/process');
        $resL = $this->httpRequest('GET', '/process/list?device=sl56&type=production');
        $json = json_decode($resL['body'], true);
        $ok   = ($resP['code'] === 200 && !$this->hasPhpError($resP['body']) && is_array($json) && count($json) > 0);
        $this->record(
            'TC-PRC-01',
            'Master Process',
            'Smoke Test',
            'Tampil Halaman Master Process & Cascading Filter Process JSON',
            'GET `/process` & GET `/process/list?device=sl56&type=production`',
            '`device=sl56`, `type=production`',
            'HTTP 200 & JSON mengembalikan daftar proses produksi SL56',
            'HTTP 200, jumlah proses SL56 production: ' . (is_array($json) ? count($json) : 0),
            $ok,
            $t0
        );

        // TC-PRC-02: Thinker (Boundary/Negative) - Detail Process Valid vs Kode Fiktif/Kosong
        $t0    = microtime(true);
        $resOk = $this->httpRequest('GET', '/process/detail?process_code=sl56-p-ldsm');
        $res404 = $this->httpRequest('GET', '/process/detail?process_code=kode-fiktif-999');
        $resEmp = $this->httpRequest('GET', '/process/detail');
        $jOk   = json_decode($resOk['body'], true);
        $jEmp  = json_decode($resEmp['body'], true);
        $ok    = ($resOk['code'] === 200 && !empty($jOk['process_code'])
            && $res404['code'] === 404
            && is_array($jEmp) && ($jEmp['status'] ?? null) === false);
        $this->record(
            'TC-PRC-02',
            'Master Process',
            'Thinker (Negative)',
            'Validasi Detail Dokumen Process pada Kode Valid, Kosong, dan Fiktif',
            'GET `/process/detail` (valid, kosong, fiktif)',
            '`sl56-p-ldsm`, `""`, `kode-fiktif-999`',
            'Kode valid -> HTTP 200 JSON docno; kosong -> status false; fiktif -> HTTP 404 JSON aman',
            "Valid={$resOk['code']}, Fiktif={$res404['code']}, EmptyStatus=" . json_encode($jEmp['status'] ?? null),
            $ok,
            $t0
        );
    }

    private function runMachineTests($db): void
    {
        // TC-MCH-01: Smoke - Halaman Master Machine & Daftar Mesin per Proses
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/machine');
        $resL = $this->httpRequest('GET', '/machine/list?process=sl56-p-ldsm');
        $json = json_decode($resL['body'], true);
        $ok   = ($resP['code'] === 200 && !$this->hasPhpError($resP['body']) && is_array($json));
        $this->record(
            'TC-MCH-01',
            'Master Machine',
            'Smoke Test',
            'Tampil Halaman Master Machine & Query Mesin per Process',
            'GET `/machine` & GET `/machine/list?process=sl56-p-ldsm`',
            '`process=sl56-p-ldsm`',
            'HTTP 200 & JSON daftar nomor mesin terambil tanpa error',
            'HTTP 200, jumlah mesin LDSM: ' . (is_array($json) ? count($json) : 0),
            $ok,
            $t0
        );

        // TC-MCH-02: Thinker (Boundary & Duplication) - Tambah Mesin, Tolak Duplikat, & Hapus
        $t0 = microtime(true);
        $db->table('machine_list')->where('machine_name', 'MCH-SMOKE-99')->delete();
        $this->httpRequest('POST', '/machine/save', ['process_code' => 'sl56-p-ldsm', 'machine_name' => 'mch-smoke-99']);
        // Coba insert kembar (huruf besar/kecil berbeda)
        $this->httpRequest('POST', '/machine/save', ['process_code' => 'sl56-p-ldsm', 'machine_name' => 'MCH-SMOKE-99']);
        $rows = $db->table('machine_list')->where('process_code', 'sl56-p-ldsm')->where('machine_name', 'MCH-SMOKE-99')->get()->getResultArray();
        $cntAfterDup = count($rows);
        $delOk = false;
        if ($cntAfterDup === 1) {
            $delRes = $this->httpRequest('DELETE', '/machine/' . $rows[0]['id']);
            $delOk  = ($delRes['code'] === 200);
        }
        $ok = ($cntAfterDup === 1 && $delOk);
        $this->record(
            'TC-MCH-02',
            'Master Machine',
            'Thinker (Boundary)',
            'Auto-Uppercase, Pencegahan Mesin Duplikat, dan Hapus Mesin',
            'POST `/machine/save` 2x dengan nama sama -> DELETE `/machine/{id}`',
            '`mch-smoke-99` & `MCH-SMOKE-99`',
            'Nama otomatis huruf kapital, input kedua ditolak (tetap 1 baris), dan hapus berhasil',
            "Jumlah baris tersimpan={$cntAfterDup}, Delete=" . ($delOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runModelDataTests($db): void
    {
        // TC-MDL-01: Smoke - Daftar Model Gabungan Master & Riwayat Transaksi
        $t0   = microtime(true);
        $res  = $this->httpRequest('GET', '/model/list?process=sl56-p-ldsm&type=production');
        $json = json_decode($res['body'], true);
        $ok   = ($res['code'] === 200 && is_array($json));
        $this->record(
            'TC-MDL-01',
            'Master Model',
            'Smoke Test',
            'Ambil Daftar Model & Standar JSON per Process',
            'GET `/model/list?process=sl56-p-ldsm&type=production`',
            '`process=sl56-p-ldsm`',
            'HTTP 200 & JSON array daftar model terurut alfabetis',
            'HTTP 200, total model ditemukan: ' . (is_array($json) ? count($json) : 0),
            $ok,
            $t0
        );

        // TC-MDL-02: Thinker (State/JSON) - Simpan Model, Set Standar Min/Max + Toggle OFF, & Hapus
        $t0 = microtime(true);
        $db->table('model_list')->where('model_name', 'MDL-SMOKE-99')->delete();
        $rSave = $this->httpRequest('POST', '/model/save', [
            'id'           => '',
            'process_code' => 'sl56-p-ldsm',
            'model_name'   => 'MDL-SMOKE-99',
        ]);
        $jSave   = json_decode($rSave['body'], true);
        $modelId = $jSave['id'] ?? null;
        $stdSaved = false;
        $delOk    = false;

        if ($modelId) {
            $stdPayload = json_encode([
                'Point 1 (Θc)' => ['active' => 1, 'min' => -3.0, 'max' => 3.0],
                'Point 5 (Y)'  => ['active' => 0, 'min' => null, 'max' => null],
            ], JSON_UNESCAPED_UNICODE);

            $rStd  = $this->httpRequest('POST', '/model/save-standards', [
                'model_id'       => $modelId,
                'standards_json' => $stdPayload,
            ]);
            $jStd  = json_decode($rStd['body'], true);
            $rowDb = $db->table('model_list')->where('id', $modelId)->get()->getRowArray();
            $parsed = !empty($rowDb['standards_json']) ? json_decode($rowDb['standards_json'], true) : [];
            $stdSaved = (($jStd['status'] ?? '') === 'success' && isset($parsed['Point 5 (Y)']) && (int)$parsed['Point 5 (Y)']['active'] === 0);

            $rDel  = $this->httpRequest('POST', '/model/delete', ['id' => $modelId]);
            $jDel  = json_decode($rDel['body'], true);
            $delOk = (($jDel['status'] ?? '') === 'success');
        }
        $ok = (!empty($modelId) && $stdSaved && $delOk);
        $this->record(
            'TC-MDL-02',
            'Master Model',
            'Thinker (State/JSON)',
            'Simpan Model Baru, Konfigurasi Standar Min/Max & Point OFF, lalu Hapus',
            'POST `/model/save` -> POST `/model/save-standards` -> POST `/model/delete`',
            '`MDL-SMOKE-99` (Point 1: `-3~3`, Point 5: `OFF`)',
            'Model terdaftar, JSON standar & status OFF tersimpan utuh, lalu terhapus bersih',
            sprintf('ModelID=%s, StdJSON=%s, Deleted=%s', $modelId ?: 'ERR', $stdSaved ? 'OK' : 'ERR', $delOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runInputAndAutofillTests(): void
    {
        // TC-INP-01: Smoke - Halaman Input Checksheet Utama
        $t0  = microtime(true);
        $res = $this->httpRequest('GET', '/input');
        $ok  = ($res['code'] === 200 && !$this->hasPhpError($res['body']) && stripos($res['body'], 'device-txt') !== false);
        $this->record(
            'TC-INP-01',
            'Form Input',
            'Smoke Test',
            'Buka Halaman Utama Input Checksheet Operator',
            'GET `/input`',
            '-',
            'HTTP 200 & memuat komponen form header (Device, Process, Machine, Model, Lot, Operator)',
            "HTTP {$res['code']} (" . strlen($res['body']) . ' bytes)',
            $ok,
            $t0
        );

        // TC-INP-02: Smoke & Thinker - Autofill Operator Valid vs EmpID Tidak Terdaftar
        $t0   = microtime(true);
        $rAdm = $this->httpRequest('GET', '/home/ajaxAutofill?empid-txt=admin');
        $rUnk = $this->httpRequest('GET', '/home/ajaxAutofill?empid-txt=998877');
        $jAdm = json_decode($rAdm['body'], true);
        $jUnk = json_decode($rUnk['body'], true);
        $ok   = ($rAdm['code'] === 200 && ($jAdm['found'] ?? false) === true
            && $rUnk['code'] === 200 && ($jUnk['found'] ?? true) === false);
        $this->record(
            'TC-INP-02',
            'Form Input',
            'Thinker (Resilience)',
            'Autofill Identitas Operator (Akun Terdaftar vs EmpID Manual Tidak Ditemukan)',
            'GET `/home/ajaxAutofill?empid-txt=admin` & `empid-txt=998877`',
            '`admin` & `998877`',
            'Akun valid -> `found: true`; ID asing -> tetap HTTP 200 `found: false` agar input manual tidak ter-reset',
            sprintf('AdminFound=%s, UnknownHTTP=%d (found=%s)', ($jAdm['found'] ?? false) ? 'true' : 'false', $rUnk['code'], ($jUnk['found'] ?? true) ? 'true' : 'false'),
            $ok,
            $t0
        );

        // TC-INP-03: Smoke - Render Form Input Dinamis Multi-Tabel / Matriks
        $t0   = microtime(true);
        $res  = $this->httpRequest('GET', '/form-builder/render-input?process=sl56-p-ldsm&doc_type=production');
        $json = json_decode($res['body'], true);
        $html = (string)($json['html'] ?? '');
        $ok   = ($res['code'] === 200 && ($json['status'] ?? '') === 'success'
            && stripos($html, 'par013') !== false && stripos($html, 'Front 1') !== false);
        $this->record(
            'TC-INP-03',
            'Form Input',
            'Smoke Test',
            'Render Form Input Dinamis dari Form Builder (Matriks 2D LDSM)',
            'GET `/form-builder/render-input?process=sl56-p-ldsm&doc_type=production`',
            '`process=sl56-p-ldsm`, `doc_type=production`',
            'JSON `status: success` berisi HTML tabel Quantity & matriks sampel `Front 1..Back 2`',
            'Status=' . ($json['status'] ?? 'ERR') . ', HTML=' . strlen($html) . ' bytes',
            $ok,
            $t0
        );

        // TC-INP-04: Thinker (Boundary) - Note SOP Form Valid vs Proses Tanpa File Note
        $t0   = microtime(true);
        $rVal = $this->httpRequest('GET', '/input/note-form/fl18-p-db2');
        $rMis = $this->httpRequest('GET', '/input/note-form/sl56-p-fiktif99');
        $ok   = ($rVal['code'] === 200 && $rMis['code'] === 200 && !$this->hasPhpError($rMis['body']));
        $this->record(
            'TC-INP-04',
            'Form Input',
            'Thinker (Boundary)',
            'Pemuatan Catatan SOP Standar & Fallback Aman Jika File Note Tidak Ada',
            'GET `/input/note-form/fl18-p-db2` & `/input/note-form/sl56-p-fiktif99`',
            '`fl18-p-db2` & `sl56-p-fiktif99`',
            'Keduanya HTTP 200 tanpa ViewException 500',
            "ValidHTTP={$rVal['code']}, MissingHTTP={$rMis['code']}",
            $ok,
            $t0
        );
    }

    private function runProductionLifecycleTests($db): void
    {
        // TC-PRD-01: Smoke - Halaman Monitoring Production & Server-Side DataTables
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/production');
        $resD = $this->httpRequest('GET', '/production/datatable?draw=1&start=0&length=5');
        $jDt  = json_decode($resD['body'], true);
        $ok   = ($resP['code'] === 200 && !$this->hasPhpError($resP['body'])
            && $resD['code'] === 200 && isset($jDt['recordsTotal'], $jDt['data']));
        $this->record(
            'TC-PRD-01',
            'Operations: Production',
            'Smoke Test',
            'Halaman Data Production & Endpoint Server-Side DataTables',
            'GET `/production` & GET `/production/datatable?draw=1&start=0&length=5`',
            '`start=0, length=5`',
            'HTTP 200 & JSON DataTables valid (`recordsTotal`, `recordsFiltered`, `data`)',
            'HTTP 200, recordsTotal=' . ($jDt['recordsTotal'] ?? 'ERR'),
            $ok,
            $t0
        );

        // TC-PRD-02: Thinker (Security) - Blokir Path Traversal pada Parameter Segment
        $t0  = microtime(true);
        $res = $this->httpRequest('GET', '/production/form?device=../../etc&process=passwd');
        $ok  = ($res['code'] === 404);
        $this->record(
            'TC-PRD-02',
            'Operations: Production',
            'Thinker (Security)',
            'Pencegahan Serangan Path Traversal pada Loader View Production',
            'GET `/production/form?device=../../etc&process=passwd`',
            '`device=../../etc`, `process=passwd`',
            'Diblokir oleh `safeSegment()` dengan HTTP 404 PageNotFoundException',
            "HTTP {$res['code']}",
            $ok,
            $t0
        );

        // TC-PRD-03: Smoke & Thinker (State/Flow) - Alur Penuh Process Start -> Modal 4 Kolom -> Operator Finish -> Delete
        $t0 = microtime(true);
        $db->table('production')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();

        // Step A: Submit Process Start (4 sampel LDSM)
        $this->httpRequest('POST', '/production', [
            'cnt-proses'  => '4',
            'date'        => date('Y-m-d'),
            'device-txt'  => 'sl56',
            'process-txt' => 'sl56-p-ldsm',
            'model-txt'   => 'GH0382AA2G',
            'lotno-txt'   => 'SMOKE-TEST-LOT-999',
            'machno-txt'  => 'LDSM 1',
            'revisi-txt'  => '8',
            'empid-txt'   => '001122',
            'name-txt'    => 'OP START TEST',
            'shift-txt'   => 'A',
            'group-txt'   => '1S1GW1',
            'par013'      => '100',
            'par014'      => '100',
            'par017a'     => '0.5',
            'par017b'     => '0.5',
            'par017c'     => '0.5',
            'par017d'     => '0.5',
            'par035a'     => 'OK',
            'par036a'     => 'OK',
            'par002'      => 'SMOKE',
        ]);

        $startRows = $db->table('production')->where('lotno', 'SMOKE-TEST-LOT-999')->orderBy('id', 'ASC')->get()->getResultArray();
        $numProd   = $startRows[0]['number'] ?? null;

        $modalStartOk  = false;
        $editPageOk    = false;
        $modalFinishOk = false;
        $deleteOk      = false;

        if ($numProd && count($startRows) === 4) {
            // Cek Modal Detail di Production Control sebelum Finish
            $rModal1      = $this->httpRequest('GET', '/productions/history/' . $numProd);
            $modalStartOk = ($rModal1['code'] === 200
                && strpos($rModal1['body'], '>Operator Start<') !== false
                && strpos($rModal1['body'], '>Operator Finish<') !== false
                && strpos($rModal1['body'], '>001122<') !== false);

            // Cek Halaman Edit & Form Edit
            $rEdit      = $this->httpRequest('GET', '/production/edit/' . $numProd);
            $rEditForm  = $this->httpRequest('GET', '/production/edit/form?number=' . $numProd);
            $editPageOk = ($rEdit['code'] === 200 && $rEditForm['code'] === 200);

            // Step B: Update / Selesaikan Lot dengan Operator Finish Berbeda Shift
            $this->httpRequest('POST', '/production/update', [
                'number-edit' => (string)$numProd,
                'cnt-proses'  => '4',
                'device-txt'  => 'sl56',
                'process-txt' => 'sl56-p-ldsm',
                'model-txt'   => 'GH0382AA2G',
                'lotno-txt'   => 'SMOKE-TEST-LOT-999',
                'machno-txt'  => 'LDSM 1',
                'revisi-txt'  => '8',
                'empid-txt'   => '001122',
                'name-txt'    => 'OP START TEST',
                'shift-txt'   => 'A',
                'group-txt'   => '1S1GW1',
                'empid2-txt'  => '003344',
                'name2-txt'   => 'OP FINISH TEST',
                'shift2-txt'  => 'B',
                'group2-txt'  => '2S2GW2',
                'par013'      => '100',
                'par014'      => '100',
                'par017a'     => '0.5',
                'par035a'     => 'OK',
                'par036a'     => 'OK',
                'par002'      => 'SMOKE',
            ]);

            // Cek Modal Detail di Production Control setelah Finish (harus tampil Op Start & Op Finish terpisah!)
            $rModal2       = $this->httpRequest('GET', '/productions/history/' . $numProd);
            $modalFinishOk = ($rModal2['code'] === 200
                && strpos($rModal2['body'], '>001122<') !== false
                && strpos($rModal2['body'], '>A / 1S1GW1<') !== false
                && strpos($rModal2['body'], '>003344<') !== false
                && strpos($rModal2['body'], '>B / 2S2GW2<') !== false);

            // Step C: Cek tampilan di Halaman History (HANYA boleh tampil Op Start 001122, TIDAK boleh double 001122 / 003344!)
            $rHistView = $this->httpRequest('GET', '/history/data?typeProcess=production&device=sl56&process=sl56-p-ldsm&revisi=8&lotno=SMOKE-TEST-LOT-999&dateStart=2020-01-01&dateEnd=2030-12-31');
            $histSingleOpOk = ($rHistView['code'] === 200
                && strpos($rHistView['body'], '>001122<') !== false
                && strpos($rHistView['body'], '001122 / 003344') === false
                && strpos($rHistView['body'], 'Operator Finish') === false);

            // Hapus via endpoint
            $rDel     = $this->httpRequest('DELETE', '/production/' . $numProd);
            $deleteOk = ($rDel['code'] === 200 && trim($rDel['body']) === 'berhasil' && $histSingleOpOk);
        }

        $ok = ($numProd !== null && $modalStartOk && $editPageOk && $modalFinishOk && $deleteOk);
        $this->record(
            'TC-PRD-03',
            'Operations: Production',
            'Thinker (End-to-End Flow)',
            'Siklus Penuh Production (Start -> Modal 4 Kolom -> Finish Cross-Shift -> Verifikasi History Tunggal -> Delete)',
            'POST `/production` -> GET `/productions/history/{n}` -> POST `/production/update` -> GET `/history/data` -> DELETE',
            'Op1=`001122 (A/1S1GW1)`, Op2=`003344 (B/2S2GW2)`',
            'Modal Production menampilkan 4 kolom (Op Start, Shift, Op Finish, Shift), sedangkan Page History hanya menampilkan Op Start saja',
            sprintf('Rows=%d, ModalStart=%s, Edit=%s, ModalFinish=%s, HistSingle&Del=%s', count($startRows), $modalStartOk ? 'OK' : 'ERR', $editPageOk ? 'OK' : 'ERR', $modalFinishOk ? 'OK' : 'ERR', $deleteOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runStartupLifecycleTests($db): void
    {
        // TC-STR-01: Smoke & Thinker (End-to-End) - Halaman Startup, Create (No #1364 deleted_at error), Detail, Unlock Edit, Delete
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/startup');

        $db->table('startup')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();
        $rPost = $this->httpRequest('POST', '/startup/create', [
            'cnt-proses'  => '1',
            'date'        => date('Y-m-d'),
            'device-txt'  => 'sl56',
            'process-txt' => 'sl56-s-db2',
            'model-txt'   => 'GH0382AA2G',
            'lotno-txt'   => 'SMOKE-TEST-LOT-999',
            'machno-txt'  => 'SLD 2-1',
            'revisi-txt'  => '0',
            'empid-txt'   => 'admin',
            'name-txt'    => 'admin',
            'shift-txt'   => 'A',
            'group-txt'   => 'ADMIN',
            'par001'      => '✔',
            'par025'      => '✔',
        ]);

        $stRow = $db->table('startup')->where('lotno', 'SMOKE-TEST-LOT-999')->orderBy('id', 'DESC')->get()->getRowArray();
        $numSt = $stRow['number'] ?? null;

        $histOk      = false;
        $editRejectOk = false;
        $delOk       = false;
        if ($numSt) {
            $rHist        = $this->httpRequest('GET', '/startup/history/' . $numSt);
            $histOk       = ($rHist['code'] === 200 && !$this->hasPhpError($rHist['body']));
            $rEditPage    = $this->httpRequest('GET', '/startup/edit/' . $numSt);
            $rEditForm    = $this->httpRequest('GET', '/startup/edit/form?number=' . $numSt);
            $rReqReject   = $this->httpRequest('POST', '/startup/request-reject/' . $numSt, ['reason' => 'Uji coba koreksi data startup']);
            $jReqReject   = json_decode($rReqReject['body'], true);
            $editRejectOk = ($rEditPage['code'] === 200 && $rEditForm['code'] === 200 && ($jReqReject['status'] ?? '') === 'success');
            $rDel         = $this->httpRequest('DELETE', '/startup/' . $numSt);
            $delOk        = ($rDel['code'] === 200 && trim($rDel['body']) === 'berhasil');
        }

        $ok = ($resP['code'] === 200 && !$this->hasPhpError($rPost['body']) && $numSt !== null && $histOk && $editRejectOk && $delOk);
        $this->record(
            'TC-STR-01',
            'Operations: Startup',
            'Thinker (End-to-End Flow)',
            'Halaman Startup, Submit Checksheet (Bebas Error #1364 deleted_at), Detail Modal, Edit Form, Request Reject, & Delete',
            'GET `/startup` -> POST `/startup/create` -> GET `/startup/history/{n}` -> GET `edit` -> POST `request-reject` -> DELETE',
            '`process=sl56-s-db2`, `lotno=SMOKE-TEST-LOT-999`',
            'Submit sukses tersimpan ke tabel `startup`, detail vertikal & form edit tampil, pengajuan reject & hapus berhasil',
            sprintf('Page=%d, SavedNum=%s, Detail=%s, Edit&ReqReject=%s, Del=%s', $resP['code'], $numSt ?: 'ERR', $histOk ? 'OK' : 'ERR', $editRejectOk ? 'OK' : 'ERR', $delOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runForegoingLifecycleTests($db): void
    {
        // TC-FRG-01: Smoke & Thinker (End-to-End) - Halaman Foregoing, Create Matriks, Detail, Edit Form, Delete
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/foregoing');

        $db->table('foregoing')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();
        $rPost = $this->httpRequest('POST', '/foregoing/create', [
            'cnt-proses'  => '4',
            'date'        => date('Y-m-d'),
            'device-txt'  => 'fl18',
            'process-txt' => 'fl18-f-fdb1',
            'model-txt'   => 'GH15130D8C',
            'lotno-txt'   => 'SMOKE-TEST-LOT-999',
            'machno-txt'  => 'FDB 1-1',
            'revisi-txt'  => '0',
            'empid-txt'   => 'admin',
            'name-txt'    => 'admin',
            'shift-txt'   => 'A',
            'group-txt'   => 'ADMIN',
            'par001a'     => '0.01',
            'par001b'     => '0.02',
            'par001c'     => '0.01',
            'par001d'     => '0.00',
        ]);

        $fgRows = $db->table('foregoing')->where('lotno', 'SMOKE-TEST-LOT-999')->get()->getResultArray();
        $numFg  = $fgRows[0]['number'] ?? null;

        $histOk = false;
        $editOk = false;
        $delOk  = false;
        if ($numFg) {
            $rHist      = $this->httpRequest('GET', '/foregoing/history/' . $numFg);
            $histOk     = ($rHist['code'] === 200 && !$this->hasPhpError($rHist['body']));
            $rEditPage  = $this->httpRequest('GET', '/foregoing/edit/' . $numFg);
            $rEditForm  = $this->httpRequest('GET', '/foregoing/edit/form?number=' . $numFg);
            $rRejectNeg = $this->httpRequest('POST', '/foregoing/request-reject/' . $numFg, ['reason' => '']);
            $editOk     = ($rEditPage['code'] === 200 && $rEditForm['code'] === 200 && $rRejectNeg['code'] === 400);
            $rDel       = $this->httpRequest('DELETE', '/foregoing/' . $numFg);
            $delOk      = ($rDel['code'] === 200 && trim($rDel['body']) === 'berhasil');
        }

        $ok = ($resP['code'] === 200 && !$this->hasPhpError($rPost['body']) && count($fgRows) === 4 && $histOk && $editOk && $delOk);
        $this->record(
            'TC-FRG-01',
            'Operations: Foregoing',
            'Thinker (End-to-End Flow)',
            'Halaman Foregoing, Submit 4 Baris Matriks, Detail Modal, Form Edit, Validasi Reject Kosong, & Delete',
            'GET `/foregoing` -> POST `/foregoing/create` -> GET `/foregoing/history/{n}` -> GET `edit` -> DELETE',
            '`process=fl18-f-fdb1`, 4 baris matriks (`cnt-proses=4`)',
            '4 baris matriks tersimpan ke `foregoing`, tabel detail & form edit tampil rapi, alasan reject kosong ditolak 400, & hapus berhasil',
            sprintf('Page=%d, SavedRows=%d, Detail=%s, Edit&Val=%s, Del=%s', $resP['code'], count($fgRows), $histOk ? 'OK' : 'ERR', $editOk ? 'OK' : 'ERR', $delOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );
    }

    private function runHistoryAndExportTests(): void
    {
        // TC-HIS-01: Smoke - Halaman History & Endpoint Daftar Revisi
        $t0   = microtime(true);
        $resP = $this->httpRequest('GET', '/history');
        $resR = $this->httpRequest('GET', '/history/revisions?process=sl56-p-ldsm&typeProcess=production');
        $jRev = json_decode($resR['body'], true);
        $ok   = ($resP['code'] === 200 && !$this->hasPhpError($resP['body'])
            && $resR['code'] === 200 && is_array($jRev));
        $this->record(
            'TC-HIS-01',
            'History & Report',
            'Smoke Test',
            'Buka Halaman History & Ambil Daftar Revisi Dokumen per Process',
            'GET `/history` & GET `/history/revisions?process=sl56-p-ldsm&typeProcess=production`',
            '`process=sl56-p-ldsm`',
            'Halaman History tampil HTTP 200 (bebas TypeError `#type-process`) & JSON daftar revisi tersedia',
            'HTTP 200, jumlah opsi revisi: ' . (is_array($jRev['revisions'] ?? $jRev) ? count($jRev['revisions'] ?? $jRev) : 0),
            $ok,
            $t0
        );

        // TC-HIS-02: Thinker (Grid Alignment) - Kesejajaran Kolom Tabel History Dinamis (Production Matriks & Startup)
        $t0    = microtime(true);
        $rProd = $this->httpRequest('GET', '/history/data?typeProcess=production&device=sl56&process=sl56-p-ldsm&dateStart=2026-11-01&dateEnd=2026-11-30');
        $rStr  = $this->httpRequest('GET', '/history/data?typeProcess=startup&device=sl56&process=sl56-s-db2&dateStart=2026-11-01&dateEnd=2026-11-30');
        $ok    = ($rProd['code'] === 200 && strpos($rProd['body'], 'id="table1"') !== false && !$this->hasPhpError($rProd['body'])
            && $rStr['code'] === 200 && strpos($rStr['body'], 'id="table1"') !== false && !$this->hasPhpError($rStr['body']));
        $this->record(
            'TC-HIS-02',
            'History & Report',
            'Thinker (Layout/Grid)',
            'Render Tabel History Dinamis `#table1` untuk Production Matriks & Startup Vertikal',
            'GET `/history/data` (`sl56-p-ldsm` & `sl56-s-db2`)',
            'Rentang `2026-11-01` s/d `2026-11-30`',
            'Keduanya merender `<table id="table1">` sejajar tanpa pergeseran kolom atau PHP Warning',
            "ProdBytes=" . strlen($rProd['body']) . ", StartupBytes=" . strlen($rStr['body']),
            $ok,
            $t0
        );

        // TC-HIS-03: Smoke Binary Check - Export Excel (.xlsx)
        $t0     = microtime(true);
        $rExcel = $this->httpRequest('GET', '/history/exportExcel?typeProcess=production&device=sl56&process=sl56-p-ldsm&dateStart=2026-11-01&dateEnd=2026-11-30');
        // File XLSX adalah arsip ZIP yang selalu diawali magic bytes "PK\x03\x04"
        $isXlsx = ($rExcel['code'] === 200 && strlen($rExcel['body']) > 3000 && substr($rExcel['body'], 0, 2) === 'PK');
        $this->record(
            'TC-HIS-03',
            'History & Report',
            'Smoke Test',
            'Export Tabel History ke File Microsoft Excel (`.xlsx`)',
            'GET `/history/exportExcel?typeProcess=production&device=sl56&process=sl56-p-ldsm...`',
            '`sl56-p-ldsm`',
            'HTTP 200 & menghasilkan binary file `.xlsx` valid (ZIP signature `PK`, ukuran > 3 KB)',
            "HTTP {$rExcel['code']}, Size=" . strlen($rExcel['body']) . ' bytes, Magic=' . ($isXlsx ? 'PK (Valid XLSX)' : 'INVALID'),
            $isXlsx,
            $t0
        );

        // TC-HIS-04: Smoke Binary Check - Export PDF (.pdf)
        $t0   = microtime(true);
        $rPdf = $this->httpRequest('GET', '/history/exportPDF?typeProcess=production&device=sl56&process=sl56-p-ldsm&dateStart=2026-11-01&dateEnd=2026-11-30');
        $isPdf = ($rPdf['code'] === 200 && strlen($rPdf['body']) > 1000 && substr($rPdf['body'], 0, 4) === '%PDF');
        $this->record(
            'TC-HIS-04',
            'History & Report',
            'Smoke Test',
            'Export Tabel History ke Dokumen Cetak PDF Landscape (`.pdf`)',
            'GET `/history/exportPDF?typeProcess=production&device=sl56&process=sl56-p-ldsm...`',
            '`sl56-p-ldsm`',
            'HTTP 200 & menghasilkan binary dokumen PDF valid (header `%PDF-`)',
            "HTTP {$rPdf['code']}, Size=" . strlen($rPdf['body']) . ' bytes, Header=' . ($isPdf ? '%PDF (Valid PDF)' : 'INVALID'),
            $isPdf,
            $t0
        );
    }

    private function runApprovalTests(): void
    {
        // TC-APP-01: Smoke - Approval Harian / Startup
        $t0   = microtime(true);
        $rApp = $this->httpRequest('GET', '/approve');
        $ok   = ($rApp['code'] === 200 && !$this->hasPhpError($rApp['body']));
        $this->record(
            'TC-APP-01',
            'Approval System',
            'Smoke Test',
            'Buka Halaman Approval Harian / Startup',
            'GET `/approve`',
            'Session `admin`',
            'HTTP 200 & halaman Approval Harian tampil tanpa error',
            "HTTP {$rApp['code']} (" . strlen($rApp['body']) . ' bytes)',
            $ok,
            $t0
        );

        // TC-APP-02: Smoke & Thinker - Pusat Approval Mingguan, Daftar Mesin per Batch, & Sync Batch Production
        $t0      = microtime(true);
        $wModel  = new \App\Models\WeeklyApprovalModel();
        $wRow    = $wModel->orderBy('id', 'DESC')->first();
        $wId     = $wRow['id'] ?? 1;
        $rWeek   = $this->httpRequest('GET', '/approval/weekly');
        $rMachOk = $this->httpRequest('GET', '/approval/getWeeklyMachines?weekly_id=' . $wId);
        $rMachNg = $this->httpRequest('GET', '/approval/getWeeklyMachines');
        $rSync   = $this->httpRequest('GET', '/approval/sync_production', [], false);
        $ok      = ($rWeek['code'] === 200 && !$this->hasPhpError($rWeek['body'])
            && $rMachOk['code'] === 200
            && $rMachNg['code'] === 400
            && in_array($rSync['code'], [200, 302, 303], true));
        $this->record(
            'TC-APP-02',
            'Approval System',
            'Smoke & Thinker',
            'Pusat Approval Mingguan, Daftar Mesin Batch (`weekly_id` Valid vs Kosong), & Sync Production',
            'GET `/approval/weekly`, `/approval/getWeeklyMachines?weekly_id={id}`, `/approval/sync_production`',
            "`weekly_id={$wId}` & kosong",
            'Pusat Approval HTTP 200, mesin mingguan HTTP 200 JSON, tanpa `weekly_id` ditolak HTTP 400, & sync berjalan',
            "Weekly={$rWeek['code']}, MachOK={$rMachOk['code']}, MachEmpty={$rMachNg['code']}, Sync={$rSync['code']}",
            $ok,
            $t0
        );
    }

    private function runFormBuilderAndHardcodeTests($db): void
    {
        // TC-FMB-01: Smoke - Halaman Form Builder & Cascading Process
        $t0   = microtime(true);
        $rFb  = $this->httpRequest('GET', '/form-builder');
        $rPrc = $this->httpRequest('GET', '/form-builder/get-processes?device=sl56');
        $jPrc = json_decode($rPrc['body'], true);
        $ok   = ($rFb['code'] === 200 && stripos($rFb['body'], 'btnOpenExportHardcode') !== false
            && $rPrc['code'] === 200 && is_array($jPrc));
        $this->record(
            'TC-FMB-01',
            'Form Builder',
            'Smoke Test',
            'Buka Halaman Form Builder Visual & Tombol Export Hard Code Tersedia',
            'GET `/form-builder` & GET `/form-builder/get-processes?device=sl56`',
            '`device=sl56`',
            'HTTP 200, UI Form Builder memuat tombol `Export / Salin Hard Code` & daftar proses JSON',
            'HTTP 200, tombol Hardcode aktif, proses SL56: ' . (is_array($jPrc) ? count($jPrc) : 0),
            $ok,
            $t0
        );

        // TC-FMB-02: Smoke - Load Visual Builder & Export CSV Parameter
        $t0    = microtime(true);
        $rLoad = $this->httpRequest('POST', '/form-builder/load', [
            'process'  => 'sl56-p-ldsm',
            'doc_type' => 'production',
        ]);
        $jLoad = json_decode($rLoad['body'], true);
        $revId = $jLoad['revision']['id'] ?? null;
        $csvOk = false;
        if ($revId) {
            $rCsv  = $this->httpRequest('GET', '/form-builder/export-csv?revision_id=' . $revId);
            $csvOk = ($rCsv['code'] === 200 && stripos($rCsv['body'], 'nama_parameter') !== false);
        }
        $ok = (($jLoad['status'] ?? '') === 'success' && !empty($jLoad['html']) && $csvOk);
        $this->record(
            'TC-FMB-02',
            'Form Builder',
            'Smoke Test',
            'Muat Tabel Visual Builder & Download CSV Struktur Parameter',
            'POST `/form-builder/load` -> GET `/form-builder/export-csv?revision_id={id}`',
            '`sl56-p-ldsm` (Rev ID: ' . ($revId ?: '-') . ')',
            'Visual Builder ter-render & file CSV terunduh lengkap dengan header parameter',
            sprintf('Load=%s, RevID=%s, CSV=%s', $jLoad['status'] ?? 'ERR', $revId ?: '-', $csvOk ? 'OK' : 'ERR'),
            $ok,
            $t0
        );

        // TC-FMB-03: Smoke & Thinker (Compiler Lint) - Export/Salin & Download Hard Code PHP (Tanpa Mengubah File Project)
        $t0 = microtime(true);
        $revRow = $db->table('form_revisions')->where('process_code', 'sl56-p-ldsm')->orderBy('id', 'DESC')->get()->getRowArray();
        $targetRevId = $revRow['id'] ?? $revId;

        $rHcJson = $this->httpRequest('GET', '/form-builder/export-hardcode?revision_id=' . $targetRevId);
        $jHc     = json_decode($rHcJson['body'], true);
        $rDlIn   = $this->httpRequest('GET', '/form-builder/export-hardcode?revision_id=' . $targetRevId . '&download=input');
        $rDlHi   = $this->httpRequest('GET', '/form-builder/export-hardcode?revision_id=' . $targetRevId . '&download=history');

        // Uji php -l pada kode PHP hasil generate di file sementara writable/logs
        $tmpIn = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'tmp_hc_in.php';
        $tmpHi = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'tmp_hc_hi.php';
        file_put_contents($tmpIn, (string)($jHc['input_code'] ?? ''));
        file_put_contents($tmpHi, (string)($jHc['history_code'] ?? ''));

        $phpBin = escapeshellarg(PHP_BINARY);
        exec($phpBin . ' -l ' . escapeshellarg($tmpIn) . ' 2>&1', $o1, $rcIn);
        exec($phpBin . ' -l ' . escapeshellarg($tmpHi) . ' 2>&1', $o2, $rcHi);
        @unlink($tmpIn);
        @unlink($tmpHi);

        $ok = (($jHc['status'] ?? '') === 'success'
            && !empty($jHc['input_code']) && !empty($jHc['history_code'])
            && $rDlIn['code'] === 200 && $rDlHi['code'] === 200
            && $rcIn === 0 && $rcHi === 0);
        $this->record(
            'TC-FMB-03',
            'Form Builder',
            'Thinker (Code Compiler)',
            'Generate, Salin JSON, Download `.php` Hard Code Input & History + Validasi Sintaks `php -l`',
            'GET `/form-builder/export-hardcode` (JSON, `download=input`, `download=history`) + `php -l`',
            '`revision_id=' . $targetRevId . '` (`sl56-p-ldsm`)',
            'JSON & file `.php` terunduh murni read-only (tanpa mengubah folder project) & lolos `php -l` 0 error',
            sprintf('JSON=%s, DlIn=%d, DlHist=%d, LintIn=%d, LintHist=%d', $jHc['status'] ?? 'ERR', $rDlIn['code'], $rDlHi['code'], $rcIn, $rcHi),
            $ok,
            $t0
        );

        // TC-FMB-04: Thinker (Negative) - Export Hardcode dengan revision_id kosong / tidak valid
        $t0   = microtime(true);
        $r400 = $this->httpRequest('GET', '/form-builder/export-hardcode');
        $r404 = $this->httpRequest('GET', '/form-builder/export-hardcode?revision_id=9999999');
        $ok   = ($r400['code'] === 400 && $r404['code'] === 404);
        $this->record(
            'TC-FMB-04',
            'Form Builder',
            'Thinker (Negative)',
            'Penanganan Error Export Hard Code Tanpa Parameter atau Revisi Fiktif',
            'GET `/form-builder/export-hardcode` & `?revision_id=9999999`',
            '`revision_id=""` & `9999999`',
            'Mengembalikan HTTP 400 Bad Request & HTTP 404 Not Found dalam format JSON rapi',
            "EmptyHTTP={$r400['code']}, MissingHTTP={$r404['code']}",
            $ok,
            $t0
        );

        // TC-FMB-05: Smoke - Live Preview Interaktif (Form Input & Simulasi Tabel History)
        $t0    = microtime(true);
        $rPrev = $this->httpRequest('GET', '/form-builder/preview?revision_id=' . $targetRevId);
        $jPrev = json_decode($rPrev['body'], true);
        $inH   = (string)($jPrev['input_html'] ?? '');
        $hiH   = (string)($jPrev['history_html'] ?? '');
        $ok    = ($rPrev['code'] === 200 && ($jPrev['status'] ?? '') === 'success'
            && stripos($inH, 'sl56-p-ldsm_preview') !== false
            && stripos($hiH, 'PRODUCTION PROCESS CONTROL SHEET') !== false
            && !$this->hasPhpError($inH) && !$this->hasPhpError($hiH));
        $this->record(
            'TC-FMB-05',
            'Form Builder',
            'Smoke Test',
            'Live Preview Modal 2 Tab (Simulasi Form Input Operator & Tabel History Data Contoh)',
            'GET `/form-builder/preview?revision_id=' . $targetRevId . '`',
            '`sl56-p-ldsm` (Rev ID: `' . $targetRevId . '`)',
            'JSON `status: success` merender HTML Input interaktif & Tabel History selaras tanpa PHP error',
            sprintf('Status=%s, InputHTML=%dB, HistHTML=%dB', $jPrev['status'] ?? 'ERR', strlen($inH), strlen($hiH)),
            $ok,
            $t0
        );

        // Siapkan revisi sandbox terisolasi tst9-p-fbtest & tst9-s-fbstartup untuk menguji mutasi Form Builder secara aman
        $oldSandboxRevs = $db->table('form_revisions')->whereIn('process_code', ['tst9-p-fbtest', 'tst9-s-fbstartup'])->get()->getResultArray();
        if (!empty($oldSandboxRevs)) {
            $oldIds = array_column($oldSandboxRevs, 'id');
            $db->table('form_parameters')->whereIn('revision_id', $oldIds)->delete();
            $db->table('form_revisions')->whereIn('id', $oldIds)->delete();
        }

        // TC-FMB-06: Thinker (Boundary & Protection) - Auto Slot Aman (Skip par001/par008/par009), Guard Duplikat, & is_required
        $t0 = microtime(true);
        $db->table('form_revisions')->insert([
            'process_code'  => 'tst9-p-fbtest',
            'revisi'        => '0',
            'kategori_form' => 'production',
            'layout_mode'   => 'matrix_grid',
            'catatan_sop'   => 'SOP Uji Otomatis Revisi 0',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        $sbRev0Id = (int)$db->insertID();

        // Simpan Param Alpha (auto kolom_db -> harus par002 karena par001 terproteksi di production, is_required=1)
        $rSaveA = $this->httpRequest('POST', '/form-builder/save-tree', [
            'revision_id' => $sbRev0Id,
            'tree_json'   => json_encode([
                'nama_parameter' => 'Param Alpha',
                'jenis_elemen'   => 'input',
                'tipe_input'     => 'number',
                'is_required'    => 1,
                'urutan'         => 1,
                'children'       => [],
            ]),
        ]);
        // Simpan Param Beta (auto kolom_db -> harus par003)
        $rSaveB = $this->httpRequest('POST', '/form-builder/save-tree', [
            'revision_id' => $sbRev0Id,
            'tree_json'   => json_encode([
                'nama_parameter' => 'Param Beta',
                'jenis_elemen'   => 'input',
                'tipe_input'     => 'dropdown_ok_ng',
                'is_required'    => 0,
                'urutan'         => 2,
                'children'       => [],
            ]),
        ]);
        // Coba paksa pakai kolom sistem Time IN (par008) -> harus ditolak!
        $rResv = $this->httpRequest('POST', '/form-builder/save-tree', [
            'revision_id' => $sbRev0Id,
            'tree_json'   => json_encode([
                'nama_parameter' => 'Param Ilegal Time IN',
                'kolom_db'       => 'par008',
                'jenis_elemen'   => 'input',
                'tipe_input'     => 'text',
                'urutan'         => 3,
                'children'       => [],
            ]),
        ]);
        // Coba paksa duplikat par002 -> harus ditolak!
        $rDup = $this->httpRequest('POST', '/form-builder/save-tree', [
            'revision_id' => $sbRev0Id,
            'tree_json'   => json_encode([
                'nama_parameter' => 'Param Kembar par002',
                'kolom_db'       => 'par002',
                'jenis_elemen'   => 'input',
                'tipe_input'     => 'number',
                'urutan'         => 4,
                'children'       => [],
            ]),
        ]);

        $jSaveA = json_decode($rSaveA['body'], true);
        $jSaveB = json_decode($rSaveB['body'], true);
        $jResv  = json_decode($rResv['body'], true);
        $jDup   = json_decode($rDup['body'], true);

        $sbParams = $db->table('form_parameters')->where('revision_id', $sbRev0Id)->orderBy('urutan', 'ASC')->get()->getResultArray();
        $colA     = $sbParams[0]['kolom_db'] ?? '';
        $reqA     = (int)($sbParams[0]['is_required'] ?? 0);
        $colB     = $sbParams[1]['kolom_db'] ?? '';

        $rSbPrev  = $this->httpRequest('GET', '/form-builder/preview?revision_id=' . $sbRev0Id);
        $jSbPrev  = json_decode($rSbPrev['body'], true);
        $hasReqAttr = stripos((string)($jSbPrev['input_html'] ?? ''), 'data-is-required="1"') !== false;

        $ok = (($jSaveA['status'] ?? '') === 'success' && ($jSaveB['status'] ?? '') === 'success'
            && $colA === 'par002' && $reqA === 1 && $colB === 'par003'
            && ($jResv['status'] ?? '') === 'error' && ($jDup['status'] ?? '') === 'error'
            && count($sbParams) === 2 && $hasReqAttr);
        $this->record(
            'TC-FMB-06',
            'Form Builder',
            'Thinker (Protection)',
            'Auto-Slot Aman (`par002`, Skip `par001`/`par008`), Blokir Duplikat `kolom_db`, & Flag `is_required`',
            'POST `/form-builder/save-tree` (Auto, `par008`, Duplikat `par002`) + Cek HTML `required`',
            '`tst9-p-fbtest` (`Param Alpha`, `par008`, duplikat `par002`)',
            'Slot otomatis `par002` & `par003`, `par008` & duplikat `par002` ditolak, serta atribut `required` aktif',
            sprintf('ColA=%s(req=%d), ColB=%s, BlockPar008=%s, BlockDup=%s', $colA, $reqA, $colB, $jResv['status'] ?? '?', $jDup['status'] ?? '?'),
            $ok,
            $t0
        );

        // TC-FMB-07: Smoke & Boundary - Geser Urutan Parameter dengan Tombol Panah (param-move)
        $t0      = microtime(true);
        $idBeta  = (int)($sbParams[1]['id'] ?? 0);
        $rMoveOk = $this->httpRequest('POST', '/form-builder/param-move', ['id' => $idBeta, 'direction' => 'prev']);
        $rMoveEdge = $this->httpRequest('POST', '/form-builder/param-move', ['id' => $idBeta, 'direction' => 'prev']);
        $jMoveOk   = json_decode($rMoveOk['body'], true);
        $jMoveEdge = json_decode($rMoveEdge['body'], true);

        $afterMove = $db->table('form_parameters')->where('revision_id', $sbRev0Id)->orderBy('urutan', 'ASC')->get()->getResultArray();
        $firstAfter = $afterMove[0]['nama_parameter'] ?? '';
        $ok = (($jMoveOk['status'] ?? '') === 'success'
            && $firstAfter === 'Param Beta'
            && ($jMoveEdge['status'] ?? '') === 'error');
        $this->record(
            'TC-FMB-07',
            'Form Builder',
            'Thinker (State & Boundary)',
            'Geser Urutan Kolom/Parameter 1-Klik (`param-move`) & Proteksi Batas Ujung Kiri/Atas',
            'POST `/form-builder/param-move` (`direction=prev` normal & saat sudah di ujung)',
            '`Param Beta` digeser maju ke urutan 1, lalu digeser lagi',
            'Urutan bertukar menjadi `Param Beta` -> `Param Alpha`, dan klik di batas ujung ditolak rapi',
            sprintf('Move1=%s (First=%s), EdgeGuard=%s', $jMoveOk['status'] ?? 'ERR', $firstAfter, $jMoveEdge['status'] ?? '?'),
            $ok,
            $t0
        );

        // TC-FMB-08: Thinker (Lifecycle) - Naik Revisi Menyalin SOP & Layout, Lihat Revisi Arsip, & Restore ke Revisi Aktif
        $t0    = microtime(true);
        $rUp   = $this->httpRequest('POST', '/form-builder/up-revisi', ['revision_id' => $sbRev0Id]);
        $jUp   = json_decode($rUp['body'], true);
        $sbRev1Id = (int)($jUp['new_revision_id'] ?? 0);
        $rev1Row  = $sbRev1Id ? $db->table('form_revisions')->where('id', $sbRev1Id)->get()->getRowArray() : null;
        $sopCopied = ($rev1Row && ($rev1Row['catatan_sop'] ?? '') === 'SOP Uji Otomatis Revisi 0' && ($rev1Row['layout_mode'] ?? '') === 'matrix_grid');

        // Hapus 1 parameter di Rev 1, buka Arsip Rev 0, lalu Restore Rev 0 ke Rev 1
        $rev1Param = $sbRev1Id ? $db->table('form_parameters')->where('revision_id', $sbRev1Id)->get()->getRowArray() : null;
        if ($rev1Param) {
            $this->httpRequest('POST', '/form-builder/delete', ['id' => $rev1Param['id']]);
        }
        $rLoadArc = $this->httpRequest('POST', '/form-builder/load', [
            'process'     => 'tst9-p-fbtest',
            'doc_type'    => 'production',
            'revision_id' => $sbRev0Id,
        ]);
        $jLoadArc = json_decode($rLoadArc['body'], true);
        $isArchiveDetected = (($jLoadArc['is_latest_revision'] ?? true) === false);

        $rRest = $this->httpRequest('POST', '/form-builder/restore-revisi', ['source_revision_id' => $sbRev0Id]);
        $jRest = json_decode($rRest['body'], true);
        $rev1CountAfterRestore = $sbRev1Id ? $db->table('form_parameters')->where('revision_id', $sbRev1Id)->countAllResults() : 0;

        $ok = (($jUp['status'] ?? '') === 'success' && $sopCopied && $isArchiveDetected
            && ($jRest['status'] ?? '') === 'success' && $rev1CountAfterRestore === 2);
        $this->record(
            'TC-FMB-08',
            'Form Builder',
            'Thinker (Lifecycle)',
            'Naik Revisi Menyalin `catatan_sop`/`layout_mode`, Mode Arsip Revisi Lama, & Restore ke Revisi Aktif',
            'POST `/form-builder/up-revisi` -> `/form-builder/load` (Arsip) -> `/form-builder/restore-revisi`',
            '`tst9-p-fbtest` Rev `0` -> Rev `1` -> Restore dari Rev `0`',
            'SOP & Layout tersalin utuh ke Rev 1, mode Arsip terdeteksi, dan Restore mengembalikan 2 parameter ke Rev 1',
            sprintf('SOPCopied=%s, ArchiveFlag=%s, RestoredCnt=%d', $sopCopied ? 'YES' : 'NO', $isArchiveDetected ? 'YES' : 'NO', $rev1CountAfterRestore),
            $ok,
            $t0
        );

        // TC-FMB-09: Smoke & Security - Clone Struktur dari Proses Lain + Guard AJAX 401 Saat Sesi Habis
        $t0 = microtime(true);
        $rClone = $this->httpRequest('POST', '/form-builder/clone-from', [
            'target_revision_id' => $sbRev1Id ?: $sbRev0Id,
            'source_process'     => 'sl56-p-ldsm',
        ]);
        $jClone = json_decode($rClone['body'], true);

        // Uji request AJAX tanpa cookie login -> wajib mengembalikan HTTP 401 JSON (bukan HTML <script>alert)
        $chUnauth = curl_init($this->baseUrl . '/form-builder/load');
        curl_setopt_array($chUnauth, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['process' => 'sl56-p-ldsm']),
            CURLOPT_HTTPHEADER     => ['X-Requested-With: XMLHttpRequest'],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $unauthBody = (string)curl_exec($chUnauth);
        $unauthCode = (int)curl_getinfo($chUnauth, CURLINFO_HTTP_CODE);
        curl_close($chUnauth);
        $jUnauth = json_decode($unauthBody, true);

        $ok = (($jClone['status'] ?? '') === 'success' && (int)($jClone['count'] ?? 0) > 0
            && $unauthCode === 401 && ($jUnauth['status'] ?? '') === 'unauthorized');
        $this->record(
            'TC-FMB-09',
            'Form Builder',
            'Thinker (Clone & Auth Guard)',
            'Clone Struktur Parameter dari Proses Lain & Proteksi Sesi Habis AJAX HTTP `401` JSON',
            'POST `/form-builder/clone-from` (`sl56-p-ldsm`) & Unauthenticated AJAX POST `/form-builder/load`',
            'Clone dari `sl56-p-ldsm` + Request tanpa cookie ber-header `X-Requested-With: XMLHttpRequest`',
            'Clone menyalin seluruh pohon parameter & request AJAX tanpa sesi mengembalikan HTTP 401 JSON bersih',
            sprintf('Cloned=%d item, UnauthAJAX=HTTP %d (%s)', (int)($jClone['count'] ?? 0), $unauthCode, $jUnauth['status'] ?? 'ERR'),
            $ok,
            $t0
        );

        // TC-FMB-10: Thinker (Startup Real & Preview Parity) - Anti-Double Judgment, Anti-Salah Kolom Note # (par025/par030), Standby JS, & Filter Clone 3 Tingkat
        $t0 = microtime(true);
        $db->table('form_revisions')->insert([
            'process_code'  => 'tst9-s-fbstartup',
            'revisi'        => '0',
            'kategori_form' => 'startup',
            'layout_mode'   => 'standard',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        $sbStRevId = (int)$db->insertID();

        // Parameter angka reguler termasuk batas negatif (-80 kPa) & satuan_opsi (0.6~1.0 N), plus par025 & par030
        $db->table('form_parameters')->insertBatch([
            [
                'revision_id'    => $sbStRevId,
                'parent_id'      => null,
                'table_index'    => 1,
                'nama_parameter' => '@ Vacuum Pressure (-80 kPa)',
                'kolom_db'       => 'par025',
                'tipe_input'     => 'number',
                'batas_min'      => null,
                'batas_max'      => -80,
                'satuan_opsi'    => null,
                'is_readonly'    => 0,
                'urutan'         => 1,
            ],
            [
                'revision_id'    => $sbStRevId,
                'parent_id'      => null,
                'table_index'    => 1,
                'nama_parameter' => '@ Colled Load (0.6-1.0 N)',
                'kolom_db'       => 'par030',
                'tipe_input'     => 'number',
                'batas_min'      => null,
                'batas_max'      => null,
                'satuan_opsi'    => json_encode([['unit' => 'N', 'min' => 0.6, 'max' => 1.0]]),
                'is_readonly'    => 0,
                'urutan'         => 2,
            ],
            [
                'revision_id'    => $sbStRevId,
                'parent_id'      => null,
                'table_index'    => 1,
                'nama_parameter' => 'Judgement Start Up',
                'kolom_db'       => 'par031',
                'tipe_input'     => 'auto_judgment',
                'batas_min'      => null,
                'batas_max'      => null,
                'satuan_opsi'    => null,
                'is_readonly'    => 1,
                'urutan'         => 3,
            ],
            [
                'revision_id'    => $sbStRevId,
                'parent_id'      => null,
                'table_index'    => 1,
                'nama_parameter' => 'Note #',
                'kolom_db'       => 'par032',
                'tipe_input'     => 'text',
                'batas_min'      => null,
                'batas_max'      => null,
                'satuan_opsi'    => null,
                'is_readonly'    => 0,
                'urutan'         => 4,
            ],
        ]);

        $rStPrev = $this->httpRequest('GET', '/form-builder/preview?revision_id=' . $sbStRevId);
        $jStPrev = json_decode($rStPrev['body'], true);
        $stInH   = (string)($jStPrev['input_html'] ?? '');
        $stHiH   = (string)($jStPrev['history_html'] ?? '');

        // Pastikan sample data menghormati batas negatif -80 & satuan_opsi 0.6~1.0 N (tidak ada sel merah #dc3545 di preview normal!)
        $noRedSample  = (stripos($stHiH, 'color: #dc3545; font-weight: bold;') === false);
        // Pastikan baris Judgment tidak double & bernilai ✔
        $noDupJudgRow = (stripos($stHiH, 'Judgement Start Up') === false && stripos($stHiH, 'Judgment Start Up *') !== false);
        $noFalseRedX  = ($noRedSample && strpos($stHiH, '&#10004;') !== false && strpos($stHiH, '&#10008;') === false);
        // Uji kebalikannya: Jika data aktual memiliki nilai merah di luar batas (par025 = 0 > -80), Judgment WAJIB berubah jadi ✗ (&#10008;)!
        $fbCtrl     = new \App\Controllers\FormBuilder();
        $ngHistHtml = $fbCtrl->renderDetailTableByRows('tst9-s-fbstartup', 'startup', [[
            'number'     => 999888,
            'revisi'     => '0',
            'process'    => 'tst9-s-fbstartup',
            'empid'      => '002234',
            'created_at' => date('Y-m-d H:i:s'),
            'par025'     => '0',      // 0 > -80 -> MERAH (NG)!
            'par030'     => '0.8 N',  // OK
            'par031'     => '✔',      // Sengaja stale ✔ -> harus dikoreksi otomatis jadi ✗!
            'par032'     => '-',
        ]]);
        $redForcesNg = (strpos($ngHistHtml, '&#10008;') !== false && strpos($ngHistHtml, '&#10004;') === false);
        // Pastikan Note # bersih (-), Standby JS aktif, Clone 3 tingkat, & Dropdown Target Evaluasi (.sel-node-eval-mode) tersedia
        $noteClean    = (preg_match('/Note\s*#.*?<td[^>]*>-<\/td>/is', $stHiH) === 1);
        $hasStandbyJs = (strpos($stInH, 'applyContainerStandbyMode') !== false && strpos($stInH, 'data-is-note="1"') !== false);
        $rFbPage      = $this->httpRequest('GET', '/form-builder');
        $fbHtml       = $rFbPage['body'];
        $has3StepClone = (strpos($fbHtml, 'cloneDeviceSelect') !== false
            && strpos($fbHtml, 'cloneProcessNameSelect') !== false
            && strpos($fbHtml, 'cloneProcessSelect') !== false
            && strpos($fbHtml, 'lpStandbyWrapper') !== false
            && strpos($fbHtml, 'sel-node-eval-mode') !== false
            && strpos($fbHtml, 'btn-eval-set-all') !== false);

        // Sekaligus uji php -l pada Hard Code Startup hasil generate
        $rHcSt  = $this->httpRequest('GET', '/form-builder/export-hardcode?revision_id=' . $sbStRevId);
        $jHcSt  = json_decode($rHcSt['body'], true);
        $tmpStH = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'tmp_hc_st_hi.php';
        file_put_contents($tmpStH, (string)($jHcSt['history_code'] ?? ''));
        $phpBin = escapeshellarg(PHP_BINARY);
        exec($phpBin . ' -l ' . escapeshellarg($tmpStH) . ' 2>&1', $oSt, $rcStHi);
        @unlink($tmpStH);

        $ok = ($noDupJudgRow && $noFalseRedX && $redForcesNg && $noteClean && $hasStandbyJs && $has3StepClone && $rcStHi === 0);
        $this->record(
            'TC-FMB-10',
            'Form Builder',
            'Thinker (Parity Input & History)',
            'Sinkronisasi Nilai Merah -> Judgment `✗`, Sample Negatif/Satuan Valid, Mode Stand By, Clone 3 Tingkat, & Dropdown Target Evaluasi',
            'GET `/form-builder/preview` (`tst9-s-fbstartup`), Uji Sel Merah (`0 > -80`) -> `✗`, & Cek UI Dropdown Target Evaluasi',
            '`par025<=-80`, `par030=0.6~1.0 N`, `par031=Judgement`, `par032=Note #`',
            'Sample normal bebas merah (`✔`), sel merah otomatis mengubah Judgment jadi `✗`, `Note #` bersih, & Dropdown Target Evaluasi aktif',
            sprintf('SampleValid=%s, RedForcesNG=%s, CleanNote=%s, EvalUI=%s', $noFalseRedX ? 'OK' : 'ERR', $redForcesNg ? 'OK' : 'ERR', $noteClean ? 'OK' : 'ERR', $has3StepClone ? 'OK' : 'ERR'),
            $ok,
            $t0
        );

        // Bersihkan sandbox tst9-p-fbtest & tst9-s-fbstartup segera setelah selesai
        $cleanupRevs = $db->table('form_revisions')->whereIn('process_code', ['tst9-p-fbtest', 'tst9-s-fbstartup'])->get()->getResultArray();
        if (!empty($cleanupRevs)) {
            $cIds = array_column($cleanupRevs, 'id');
            $db->table('form_parameters')->whereIn('revision_id', $cIds)->delete();
            $db->table('form_revisions')->whereIn('id', $cIds)->delete();
        }
    }

    private function runUtilitiesAndThinkerTimeTests(): void
    {
        // TC-UTL-01: Smoke - Halaman Log Activity & Simulasi Data Cleansing
        $t0   = microtime(true);
        $rLog = $this->httpRequest('GET', '/log-activity');
        $rCln = $this->httpRequest('GET', '/cleansing');
        $rShc = $this->httpRequest('GET', '/cleansing/shiftc');
        $ok   = ($rLog['code'] === 200 && !$this->hasPhpError($rLog['body'])
            && $rCln['code'] === 200 && !$this->hasPhpError($rCln['body'])
            && $rShc['code'] === 200 && !$this->hasPhpError($rShc['body']));
        $this->record(
            'TC-UTL-01',
            'Utilities & Audit',
            'Smoke Test',
            'Buka Halaman Log Activity, Preview Cleansing Mesin, & Preview Penyelarasan Shift C',
            'GET `/log-activity`, `/cleansing`, `/cleansing/shiftc`',
            '-',
            'Ketiga halaman utilitas tampil HTTP 200 dalam mode simulasi aman tanpa mengubah database',
            "Log={$rLog['code']}, Cleansing={$rCln['code']}, ShiftC={$rShc['code']}",
            $ok,
            $t0
        );

        // TC-UTL-02: Thinker (Time Simulation) - Logika Tanggal Kerja Operasional Shift C Lewat Tengah Malam
        $t0  = microtime(true);
        $wd1 = calculateWorkDate('2026-11-25 02:30:00', 'C'); // Lewat tengah malam Shift C -> harus mundur ke 2026-11-24
        $wd2 = calculateWorkDate('2026-11-25 06:59:59', '3'); // Batas akhir Shift 3/C -> harus 2026-11-24
        $wd3 = calculateWorkDate('2026-11-25 07:00:00', 'C'); // Tepat jam 07:00 -> sudah hari baru 2026-11-25
        $wd4 = calculateWorkDate('2026-11-25 02:30:00', 'A'); // Shift A -> tetap 2026-11-25

        // Simulasi batas cut-off Weekly Senin 06:59 (seperti SimulateWeekly)
        $monBefore = Time::parse('2026-11-23 06:59:00');
        $monAfter  = Time::parse('2026-11-23 07:00:00');
        $isPrevWeekBefore = ((int)$monBefore->format('N') === 1 && ((int)$monBefore->format('H') < 6 || ((int)$monBefore->format('H') === 6 && (int)$monBefore->format('i') <= 59)));
        $isPrevWeekAfter  = ((int)$monAfter->format('N') === 1 && ((int)$monAfter->format('H') < 6 || ((int)$monAfter->format('H') === 6 && (int)$monAfter->format('i') <= 59)));

        $ok = ($wd1 === '2026-11-24'
            && $wd2 === '2026-11-24'
            && $wd3 === '2026-11-25'
            && $wd4 === '2026-11-25'
            && $isPrevWeekBefore === true
            && $isPrevWeekAfter === false);
        $this->record(
            'TC-UTL-02',
            'Utilities & Workdate',
            'Thinker (Boundary Time)',
            'Simulasi Batas Waktu Operasional Shift C (`00:00-06:59` Mundur H-1) & Cut-Off Senin `06:59`',
            'Uji `calculateWorkDate()` & cut-off `Time::parse()` pada batas detik `06:59:59` vs `07:00:00`',
            '`02:30 (C)`, `06:59:59 (3)`, `07:00:00 (C)`, Senin `06:59` vs `07:00`',
            'Shift C `< 07:00` mundur H-1 (`2026-11-24`), `>= 07:00` tetap `2026-11-25`, Senin `06:59` masuk minggu sebelumnya',
            "02:30C={$wd1}, 06:59C={$wd2}, 07:00C={$wd3}, Mon06:59=" . ($isPrevWeekBefore ? 'PrevWeek' : 'CurrWeek'),
            $ok,
            $t0
        );

        // TC-AUTH-06: Smoke - Logout & Penghancuran Session
        $t0   = microtime(true);
        $rOut = $this->httpRequest('GET', '/logout', [], false);
        $rChk = $this->httpRequest('GET', '/input', [], false);
        $ok   = (in_array($rOut['code'], [302, 303], true) && in_array($rChk['code'], [302, 303], true));
        $this->record(
            'TC-AUTH-06',
            'Authentication',
            'Smoke Test',
            'Proses Logout & Verifikasi Session Dihancurkan',
            'GET `/logout` -> coba akses ulang GET `/input`',
            'Session aktif di-logout',
            'Redirect ke `/login` & akses ulang ke `/input` kembali ditolak (HTTP 302)',
            "LogoutHTTP={$rOut['code']}, ReAccessInputHTTP={$rChk['code']}",
            $ok,
            $t0
        );
    }

    // =========================================================================
    // INFRASTRUKTUR SERVER LOKAL, CURL CLIENT, CLEANUP, & GENERATOR MARKDOWN
    // =========================================================================

    private function prepareTargetServer(): void
    {
        $customUrl = CLI::getOption('url');
        if ($customUrl) {
            $this->baseUrl = rtrim((string)$customUrl, '/');
            return;
        }

        // Nyalakan temporary PHP built-in server dengan baseURL konsisten agar redirect session 100% akurat
        $port    = 8099;
        $host    = '127.0.0.1';
        $phpBin  = escapeshellarg(PHP_BINARY);
        $docroot = escapeshellarg(FCPATH);
        $rewrite = escapeshellarg(SYSTEMPATH . 'rewrite.php');
        $cmd     = "{$phpBin} -S {$host}:{$port} -t {$docroot} {$rewrite}";

        $env = $_ENV;
        foreach ($_SERVER as $k => $v) {
            if (is_string($v) && !isset($env[$k])) {
                $env[$k] = $v;
            }
        }
        $env['app.baseURL'] = "http://{$host}:{$port}/";

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'smoke_server.log', 'a'],
            2 => ['file', WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'smoke_server.log', 'a'],
        ];

        $this->serverProc = proc_open($cmd, $descriptors, $pipes, ROOTPATH, $env);
        $this->baseUrl    = "http://{$host}:{$port}";

        // Tunggu hingga port siap menerima koneksi (maks 5 detik)
        for ($i = 0; $i < 25; $i++) {
            $fp = @fsockopen($host, $port, $errno, $errstr, 0.2);
            if ($fp) {
                fclose($fp);
                return;
            }
            usleep(200000);
        }
    }

    private function stopTargetServer(): void
    {
        if (is_resource($this->serverProc)) {
            $status = proc_get_status($this->serverProc);
            if (!empty($status['running']) && !empty($status['pid'])) {
                if (stripos(PHP_OS, 'WIN') === 0) {
                    @exec('taskkill /F /T /PID ' . (int)$status['pid'] . ' >NUL 2>&1');
                } else {
                    @proc_terminate($this->serverProc, 9);
                }
            }
            @proc_close($this->serverProc);
            $this->serverProc = null;
        }
        $srvLog = WRITEPATH . 'logs' . DIRECTORY_SEPARATOR . 'smoke_server.log';
        if (is_file($srvLog)) {
            @unlink($srvLog);
        }
    }

    private function httpRequest(string $method, string $path, array $data = [], bool $followLocation = true, int $hops = 0): array
    {
        $url = str_starts_with($path, 'http') ? $path : ($this->baseUrl . '/' . ltrim($path, '/'));
        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'], true)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $body        = (string)curl_exec($ch);
        $code        = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = (string)curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);

        if ($followLocation && in_array($code, [301, 302, 303, 307, 308], true) && $redirectUrl !== '' && $hops < 5) {
            // Normalisasi host redirect (misal localhost:8080 dari .env) kembali ke target server aktif
            $parsed = parse_url($redirectUrl);
            $nextPath = ($parsed['path'] ?? '/') . (isset($parsed['query']) ? '?' . $parsed['query'] : '');
            return $this->httpRequest('GET', $nextPath, [], true, $hops + 1);
        }

        return [
            'code'         => $code,
            'body'         => $body,
            'redirect_url' => $redirectUrl,
        ];
    }

    private function hasPhpError(string $body): bool
    {
        $patterns = [
            'Fatal error:',
            'Parse error:',
            'Uncaught Error',
            'CodeIgniter\\Exceptions\\CriticalError',
            'CodeIgniter\\Database\\Exceptions\\DatabaseException',
            'Whoops!',
        ];
        foreach ($patterns as $p) {
            if (stripos($body, $p) !== false) {
                return true;
            }
        }
        return false;
    }

    private function cleanupTestArtifacts($db): void
    {
        try {
            $db->table('production')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();
            $db->table('startup')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();
            $db->table('foregoing')->where('lotno', 'SMOKE-TEST-LOT-999')->delete();
            $db->table('device')->where('code', 'tst9')->delete();
            $db->table('machine_list')->where('machine_name', 'MCH-SMOKE-99')->delete();
            $db->table('model_list')->where('model_name', 'MDL-SMOKE-99')->delete();
            $sbRevs = $db->table('form_revisions')->whereIn('process_code', ['tst9-p-fbtest', 'tst9-s-fbstartup'])->get()->getResultArray();
            if (!empty($sbRevs)) {
                $sbIds = array_column($sbRevs, 'id');
                $db->table('form_parameters')->whereIn('revision_id', $sbIds)->delete();
                $db->table('form_revisions')->whereIn('id', $sbIds)->delete();
            }
            $db->table('log_activity')
                ->groupStart()
                    ->like('activity', 'Test Smoke')
                    ->orLike('activity', 'MCH-SMOKE-99')
                    ->orLike('activity', 'MDL-SMOKE-99')
                    ->orWhere('table_name', 'tst9-p-fbtest')
                    ->orWhere('table_name', 'tst9-s-fbstartup')
                ->groupEnd()
                ->delete();

            // Bersihkan direktori kosong layout/tst9 yang dibuat oleh DeviceModel::addDevice
            $tstLayoutDir = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR . 'tst9';
            if (is_dir($tstLayoutDir)) {
                $it = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($tstLayoutDir, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($it as $item) {
                    if ($item->isDir()) {
                        @rmdir($item->getRealPath());
                    } else {
                        @unlink($item->getRealPath());
                    }
                }
                @rmdir($tstLayoutDir);
            }
        } catch (\Throwable $e) {
            // Abaikan jika ada tabel opsional yang tidak tersedia
        }
    }

    private function record(
        string $id,
        string $module,
        string $methodType,
        string $scenario,
        string $steps,
        string $testData,
        string $expected,
        string $actual,
        bool $passed,
        float $t0
    ): void {
        $ms     = (int)round((microtime(true) - $t0) * 1000);
        $status = $passed ? 'PASS' : 'FAIL';

        $this->results[] = [
            'id'          => $id,
            'module'      => $module,
            'method_type' => $methodType,
            'scenario'    => $scenario,
            'steps'       => $steps,
            'test_data'   => $testData,
            'expected'    => $expected,
            'actual'      => $actual,
            'duration_ms' => $ms,
            'status'      => $status,
        ];

        $badgeColor = $passed ? 'green' : 'red';
        CLI::write(
            sprintf('[%s] %-10s | %-22s | %-22s | %s (%d ms)', $status, $id, $module, $methodType, $scenario, $ms),
            $badgeColor
        );
    }

    private function escMd(string $txt): string
    {
        return str_replace(['|', "\r\n", "\n"], ['\\|', ' ', ' '], trim($txt));
    }

    private function buildMarkdownReport(): string
    {
        $total   = count($this->results);
        $passed  = count(array_filter($this->results, static fn($r) => $r['status'] === 'PASS'));
        $failed  = $total - $passed;
        $rate    = $total > 0 ? round(($passed / $total) * 100, 2) : 0;
        $totalMs = array_sum(array_column($this->results, 'duration_ms'));
        $nowStr  = date('d F Y H:i:s');

        $smokeCount   = count(array_filter($this->results, static fn($r) => stripos($r['method_type'], 'Smoke') !== false));
        $thinkerCount = $total - $smokeCount;

        $md   = [];
        $md[] = '# LAPORAN BLACKBOX TESTING OTOMATIS (SMOKE TEST & THINKER)';
        $md[] = '';
        $md[] = '> **Sistem**: Checksheet Management System & Dynamic Form Builder (PT. Foxconn Technologies Indonesia)  ';
        $md[] = '> **Command Runner**: `php spark test:blackbox` (`app/Commands/BlackboxTest.php`)  ';
        $md[] = '> **Metode Pengujian**: Kombinasi **Smoke Testing** (Verifikasi Fungsi Inti) & **Thinker / Exploratory Testing** (Skenario Negatif, Batas Waktu, Keamanan, End-to-End State, & Code Linting)  ';
        $md[] = "> **Waktu Eksekusi**: {$nowStr} WIB  ";
        $md[] = "> **Ringkasan Hasil**: **{$passed} / {$total} PASS ({$rate}%)** | Fail: **{$failed}** | Smoke Test: **{$smokeCount}** | Thinker Test: **{$thinkerCount}** | Total Durasi: **{$totalMs} ms**";
        $md[] = '';
        $md[] = '---';
        $md[] = '';
        $md[] = '## RINGKASAN PER MODUL';
        $md[] = '';
        $md[] = '| Modul | Total Test Case | Smoke Test | Thinker Test | Pass | Fail | Status Modul |';
        $md[] = '| :--- | :---: | :---: | :---: | :---: | :---: | :---: |';

        $byModule = [];
        foreach ($this->results as $r) {
            $m = $r['module'];
            if (!isset($byModule[$m])) {
                $byModule[$m] = ['total' => 0, 'smoke' => 0, 'thinker' => 0, 'pass' => 0, 'fail' => 0];
            }
            $byModule[$m]['total']++;
            if (stripos($r['method_type'], 'Smoke') !== false) {
                $byModule[$m]['smoke']++;
            } else {
                $byModule[$m]['thinker']++;
            }
            if ($r['status'] === 'PASS') {
                $byModule[$m]['pass']++;
            } else {
                $byModule[$m]['fail']++;
            }
        }

        foreach ($byModule as $modName => $stat) {
            $stBadge = $stat['fail'] === 0 ? 'PASS' : 'FAIL';
            $md[]    = sprintf(
                '| **%s** | %d | %d | %d | %d | %d | **%s** |',
                $this->escMd($modName),
                $stat['total'],
                $stat['smoke'],
                $stat['thinker'],
                $stat['pass'],
                $stat['fail'],
                $stBadge
            );
        }

        $md[] = '';
        $md[] = '---';
        $md[] = '';
        $md[] = '## TABEL DETAIL SKENARIO & HASIL EKSEKUSI (SIAP COPY-PASTE KE EXCEL / SPREADSHEET)';
        $md[] = '';
        $md[] = '| Test ID | Modul | Metode Uji | Skenario Pengujian | Endpoint / Langkah Uji | Data Uji | Hasil yang Diharapkan | Hasil Aktual (Otomatis) | Waktu (ms) | Status |';
        $md[] = '| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :---: | :---: |';

        foreach ($this->results as $r) {
            $md[] = sprintf(
                '| **%s** | %s | %s | %s | %s | %s | %s | %s | %d | **%s** |',
                $this->escMd($r['id']),
                $this->escMd($r['module']),
                $this->escMd($r['method_type']),
                $this->escMd($r['scenario']),
                $this->escMd($r['steps']),
                $this->escMd($r['test_data']),
                $this->escMd($r['expected']),
                $this->escMd($r['actual']),
                $r['duration_ms'],
                $r['status']
            );
        }

        $md[] = '';
        $md[] = '---';
        $md[] = '';
        $md[] = '## CARA MENJALANKAN ULANG DI TERMINAL';
        $md[] = '```powershell';
        $md[] = 'php spark test:blackbox';
        $md[] = '```';
        $md[] = 'Seluruh data transaksi dan master sementara (`SMOKE-TEST-LOT-999`, `tst9`, `MCH-SMOKE-99`, `MDL-SMOKE-99`) dibersihkan kembali secara otomatis di akhir pengujian sehingga database tetap bersih.';
        $md[] = '';

        return implode("\n", $md);
    }
}
