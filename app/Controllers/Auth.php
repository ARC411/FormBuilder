<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmpModel;
use App\Models\AdminModel;

class Auth extends BaseController
{
    protected $session;
    protected $EmpModel;
    protected $AdminModel;
    
    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->EmpModel = new EmpModel();
        $this->AdminModel = new AdminModel();
    }

    /**
     * Menampilkan Halaman Login
     */
    public function index()
    {
        // Jika session sudah ada (sudah login), langsung arahkan ke home
        if ($this->session->has('empid')) {
            return redirect()->to(base_url('home'));
        }
        
        // Siapkan data yang dibutuhkan oleh template view
        $data = [
            'title' => 'Login - CMS Foxconn'
        ];
        
        // Menampilkan view login lokal beserta data title
        return view('login', $data);
    }
    
    /**
     * Memproses Data Form Login dari view (POST)
     */
    /**
     * Memproses Data Form Login dari view (POST)
     */
    public function login()
    {
        // 1. Log bahwa fungsi login mulai dipanggil
        log_message('info', '--- PROSES LOGIN DIMULAI ---');

        if ($this->session->has('empid')) {
            log_message('info', 'User sudah memiliki session, dialihkan ke Home.');
            return redirect()->to(base_url('home'));
        }
        
        $empid    = $this->request->getVar('empid');
        $password = $this->request->getVar('password');
        
        // 2. Log ID siapa yang mencoba login (Password TIDAK BOLEH di-log demi keamanan)
        log_message('info', 'Mencoba login dengan Emp ID: ' . $empid);
        
        if (!$empid || !$password) {
            log_message('error', 'Login Ditolak: Emp ID atau Password kosong.');
            return redirect()->to(base_url('login'))
                ->with('message', 'Employee ID and Password are required');
        }
        
        // 3. Log pengecekan ke database
        log_message('info', 'Mencocokkan ke database melalui EmpModel...');
        $result = $this->EmpModel->login($empid, $password);
        
        if ($result) {
            log_message('info', 'Pencocokan BERHASIL. Data ditemukan di EmpModel.');
            
            $isAdmin = $this->AdminModel->getById($empid);
            if ($isAdmin) {
                $result['isadmin'] = true;
                $result['role']    = 'all';
                log_message('info', 'Status User: ADMIN.');
            } else {
                log_message('info', 'Status User: BUKAN ADMIN.');
            }
            
            $this->session->set($result);
            
            // 4. Log sukses
            log_message('info', 'Session berhasil dibuat. --- LOGIN SUKSES ---');
            
            // INI ADALAH BAGIAN YANG MENGARAHKAN KE PAGE SELANJUTNYA SETELAH LOGIN
            return redirect()->to(base_url('home'));
        }
        
        // 5. Log gagal karena data tidak cocok
        log_message('error', 'Pencocokan GAGAL. ID atau password salah di database.');
        return redirect()->to(base_url('login'))
            ->with('message', 'Invalid credentials, please check your Employee ID and Password.');
    }
    
    /**
     * Proses Logout
     */
    public function logout()
    {
        // Hancurkan session lokal
        $this->session->destroy();
        
        // Arahkan kembali ke halaman login lokal (tidak lagi dilempar ke SSO UMS)
        return redirect()->to(base_url('login'));
    }

    public function cek_role()
    {
        echo "<style>body { font-family: monospace; padding: 20px; line-height: 1.5; }</style>";
        echo "<h2>=== DIAGNOSTIK KONEKSI DATABASE SECOND (.130) ===</h2>";

        // 1. Cek Ekstensi Driver PHP
        $hasSrv = function_exists('sqlsrv_connect') || extension_loaded('sqlsrv');
        $hasPdo = extension_loaded('pdo_sqlsrv');
        echo "<b>1. Driver PHP SQLSRV:</b> " . ($hasSrv ? "<span style='color:green; font-weight:bold;'>AKTIF (sqlsrv)</span>" : "<span style='color:red; font-weight:bold;'>TIDAK AKTIF (ekstensi php_sqlsrv belum terpasang/aktif di php.ini server ini)</span>");
        echo " | PDO: " . ($hasPdo ? "<span style='color:green; font-weight:bold;'>AKTIF (pdo_sqlsrv)</span>" : "<span style='color:orange;'>TIDAK AKTIF</span>") . "<br><br>";

        // 2. Cek Konfigurasi Database 'second'
        $config = config('Database');
        $secConf = $config->second ?? [];
        echo "<b>2. Konfigurasi Database 'second':</b><br>";
        echo "   - Hostname : " . ($secConf['hostname'] ?? '-') . "<br>";
        echo "   - Port     : " . ($secConf['port'] ?? '-') . "<br>";
        echo "   - Database : " . ($secConf['database'] ?? '-') . "<br>";
        echo "   - Username : " . ($secConf['username'] ?? '-') . "<br>";
        echo "   - DBDriver : " . ($secConf['DBDriver'] ?? '-') . "<br><br>";

        // 3. Tes Eksekusi Query ke Database 'second'
        echo "<b>3. Mencoba Query ke Database Second (192.168.132.130):</b><br>";
        try {
            $db2 = \Config\Database::connect('second');
            $rows = $db2->query("SELECT TOP 10 empid, firstname, lastname, deptid, positionid, empstsid FROM tbempinfa")->getResultArray();

            echo "<span style='color:green; font-weight:bold;'>&#10004; KONEKSI BERHASIL! Berhasil menarik " . count($rows) . " data karyawan dari tbempinfa:</span><br>";
            echo "<pre>";
            print_r($rows);
            echo "</pre>";
        } catch (\Throwable $e) {
            echo "<span style='color:red; font-weight:bold;'>&#10008; GAGAL TERHUBUNG KE DATABASE SECOND!</span><br>";
            echo "<div style='background:#ffebee; border:1px solid #f44336; padding:12px; margin-top:10px; color:#c62828;'>";
            echo "<b>Detail Error:</b> " . htmlspecialchars($e->getMessage()) . "<br><br>";
            echo "<b>File:</b> " . htmlspecialchars($e->getFile()) . " (Baris " . $e->getLine() . ")<br>";
            echo "</div>";
        }

        die();
    }
}