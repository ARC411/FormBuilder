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
                $result['isadmin'] = $isAdmin;
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
        log_message('error', 'Pencocokan GAGAL. ID atau password salah di database lokal.');
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
        $data_karyawan = $this->EmpModel->testing();
        
        echo "<pre>";
        print_r($data_karyawan);
        echo "</pre>";
        die();
    }
}