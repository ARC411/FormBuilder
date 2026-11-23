<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpModel extends Model
{
    /**
     * Cek apakah driver SQLSRV siap digunakan di PHP
     */
    private function isSqlSrvReady(): bool
    {
        return function_exists('sqlsrv_configure') || extension_loaded('sqlsrv');
    }

    /**
     * Fallback pencarian data karyawan & shift/group dari database MySQL lokal
     * jika koneksi SQL Server (second) sedang offline / ekstensi sqlsrv belum terpasang.
     */
    private function getLocalEmpFallback(string $empid): ?object
    {
        $empid = trim($empid);
        if ($empid === '') {
            return null;
        }

        $db = \Config\Database::connect('default');

        // 1. Cek di tabel users lokal (jika ada)
        try {
            if ($db->tableExists('users')) {
                $fields = $db->getFieldNames('users');
                $builder = $db->table('users');
                if (in_array('empid', $fields, true)) {
                    $builder->groupStart()->where('empid', $empid)->orWhere('username', $empid)->groupEnd();
                } else {
                    $builder->where('username', $empid);
                }
                $u = $builder->get()->getRowArray();

                if ($u) {
                    $sec = strtoupper(trim((string)($u['section'] ?? $u['dept'] ?? $u['deptid'] ?? '')));
                    $dept = 'ALL';
                    if (strpos($sec, 'FRAME') !== false || strpos($sec, 'FL') !== false || strpos($sec, '017') !== false) {
                        $dept = '017,FRAME LASER 1.8';
                    } elseif (strpos($sec, 'SINGLE') !== false || strpos($sec, 'SL') !== false || strpos($sec, '044') !== false) {
                        $dept = '044,SINGLE LASER 5.6';
                    }

                    return (object) [
                        'empid'      => $u['empid'] ?? $u['username'] ?? $empid,
                        'cardid'     => $empid,
                        'name'       => $u['name'] ?? $u['fullname'] ?? $u['username'] ?? strtoupper($empid),
                        'deptid'     => $dept,
                        'positionid' => $u['position'] ?? $u['role'] ?? 'Operator',
                        'groupid'    => $u['group'] ?? $u['groupid'] ?? '',
                        'acc'        => $u['shift'] ?? '',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Lanjut ke pengecekan tabel berikutnya
        }

        // 2. Cek dari riwayat transaksi terakhir operator di tabel startup / production / foregoing
        foreach (['startup', 'production', 'foregoing'] as $tbl) {
            try {
                if ($db->tableExists($tbl)) {
                    $row = $db->table($tbl)
                        ->where('empid', $empid)
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRowArray();

                    if ($row) {
                        $dev = strtolower((string)($row['device'] ?? ''));
                        $dept = ($dev === 'fl18') ? '017,FRAME LASER 1.8' : (($dev !== '') ? '044,SINGLE LASER 5.6' : 'ALL');
                        return (object) [
                            'empid'      => $empid,
                            'cardid'     => $empid,
                            'name'       => (!empty($row['name']) && $row['name'] !== '-') ? $row['name'] : strtoupper($empid),
                            'deptid'     => $dept,
                            'positionid' => 'Operator',
                            'groupid'    => (!empty($row['group']) && $row['group'] !== '-') ? $row['group'] : '',
                            'acc'        => (!empty($row['shift']) && $row['shift'] !== '-') ? $row['shift'] : '',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // 3. Cek di tabel admin
        try {
            if ($db->tableExists('admin')) {
                $adm = $db->table('admin')->where('empid', $empid)->get()->getRowArray();
                if ($adm) {
                    return (object) [
                        'empid'      => $empid,
                        'cardid'     => $empid,
                        'name'       => $adm['name'] ?? $adm['role'] ?? strtoupper($empid),
                        'deptid'     => 'ALL',
                        'positionid' => '1,Admin',
                        'groupid'    => '',
                        'acc'        => '',
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Ambil data karyawan untuk auto-fill shift & group.
     */
    public function getEmp($empid)
    {
        if ($this->isSqlSrvReady()) {
            try {
                $db = \Config\Database::connect('second');
                $sql = "
                    SELECT TOP 1
                        c.empid,
                        c.cardid,
                        LTRIM(RTRIM(
                            ISNULL(i.firstname, '') + ' ' + ISNULL(i.midname, '') + ' ' + ISNULL(i.lastname, '')
                        )) AS name,
                        s.groupid,
                        (
                            SELECT TOP 1 RIGHT(a.acc, 1)
                            FROM tbarawdata a
                            WHERE a.empid = c.empid
                              AND a.datet >= DATEADD(day, -1, CAST(GETDATE() AS date))
                            ORDER BY a.datet DESC
                        ) AS acc
                    FROM tbtcardemp c
                    INNER JOIN tbempinfa i ON c.empid = i.empid
                    LEFT JOIN tbtschemp s ON c.empid = s.empid AND s.datetl = '1900-01-01'
                    WHERE (c.empid = ? OR c.cardid = ?)
                      AND i.empstsid <> '3,Not Active'
                      AND c.datetl = '1900-01-01'
                ";
                $res = $db->query($sql, [$empid, $empid])->getRow();
                if ($res) {
                    return $res;
                }
            } catch (\Throwable $e) {
                log_message('error', 'getEmp SQLSRV fallback: ' . $e->getMessage());
            }
        }

        $sql = "
            SELECT TOP 1
                c.empid,
                c.cardid,
                LTRIM(RTRIM(
                    ISNULL(i.firstname, '') + ' ' + ISNULL(i.midname, '') + ' ' + ISNULL(i.lastname, '')
                )) AS name,
                i.positionid,
                s.groupid,
                (
                    SELECT TOP 1 RIGHT(a.acc, 1)
                    FROM tbarawdata a
                    WHERE a.empid = c.empid
                      AND a.datet >= DATEADD(day, -1, CAST(GETDATE() AS date))
                    ORDER BY a.datet DESC
                ) AS acc
            FROM tbtcardemp c
            INNER JOIN tbempinfa i ON c.empid = i.empid
            LEFT JOIN tbtschemp s ON c.empid = s.empid AND s.datetl = '1900-01-01'
            WHERE (c.empid = ? OR c.cardid = ?)
              AND i.empstsid <> '3,Not Active'
              AND c.datetl = '1900-01-01'
        ";

        return $db->query($sql, [$empid, $empid])->getRow();
    }

    public function getDataEmp($empid)
    {
        if ($this->isSqlSrvReady()) {
            try {
                $db = \Config\Database::connect('second');
                $q = "SELECT empid, LTRIM(RTRIM(ISNULL(firstname, '') + ' ' + ISNULL(midname, '') + ' ' + ISNULL(lastname, ''))) as name, deptid, positionid, titleid, empstsid
                      FROM tbempinfa
                      WHERE empstsid <> '3,Not Active' AND empid = ?";
                $res = $db->query($q, [$empid])->getRow();
                if ($res) {
                    return $res;
                }
            } catch (\Throwable $e) {
                log_message('error', 'getDataEmp SQLSRV fallback: ' . $e->getMessage());
            }
        }

        return $this->getLocalEmpFallback((string)$empid);
    }

    public static function determineDepartmentCategory(?string $deptid): string
    {
        if (empty($deptid)) {
            return 'OTHER';
        }
        $d = strtoupper(trim($deptid));

        // QC / QA keywords
        if (
            strpos($d, 'QUALITY') !== false ||
            strpos($d, 'QC') !== false ||
            strpos($d, 'QA') !== false
        ) {
            return 'QC';
        }

        // Production keywords (FL, SL, Opto, Compound, Production, dll)
        if (
            strpos($d, 'PRODUCTION') !== false ||
            strpos($d, 'PRODUKSI') !== false ||
            strpos($d, 'FRAME LASER') !== false ||
            strpos($d, 'SINGLE LASER') !== false ||
            strpos($d, 'CHIP MAKING') !== false ||
            strpos($d, 'HOLOGRAM') !== false ||
            strpos($d, 'LED') !== false ||
            strpos($d, 'REGULATOR') !== false ||
            strpos($d, 'OPTO') !== false
        ) {
            return 'PRODUCTION';
        }

        return 'OTHER';
    }

    public function login($empid, $password)
    {
        $empid    = trim((string)$empid);
        $password = trim((string)$password);

        // 1. Validasi Super Admin Lokal
        if (strtolower($empid) === 'admin' && strtolower($password) === 'admin') {
            return [
                'empid'      => 'admin',
                'name'       => 'admin',
                'positionid' => 'admin',
                'role'       => 'all',
                'deptid'     => 'ADMIN',
                'department' => 'ADMIN',
                'level'      => 99,
                'isadmin'    => true
            ];
        }

        // 2. Cek apakah terdaftar di tabel users lokal dengan password khusus
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('users')) {
                $fields = $db->getFieldNames('users');
                $builder = $db->table('users');
                if (in_array('empid', $fields, true)) {
                    $builder->groupStart()->where('empid', $empid)->orWhere('username', $empid)->groupEnd();
                } else {
                    $builder->where('username', $empid);
                }
                $userRow = $builder->get()->getRowArray();

                if ($userRow) {
                    $dbPass = $userRow['password'] ?? '';
                    $passMatch = ($password === $empid)
                        || ($password === $dbPass)
                        || (strlen($dbPass) > 20 && password_verify($password, $dbPass));

                    if ($passMatch) {
                        $uId   = $userRow['empid'] ?? $userRow['username'] ?? $empid;
                        $uName = $userRow['name'] ?? $userRow['fullname'] ?? $userRow['username'] ?? $uId;
                        $sec   = strtoupper(trim((string)($userRow['section'] ?? $userRow['role'] ?? '')));
                        $uRole = 'all';
                        if (strpos($sec, 'FRAME') !== false || strpos($sec, 'FL') !== false || strpos($sec, '017') !== false) {
                            $uRole = 'fl';
                        } elseif (strpos($sec, 'SINGLE') !== false || strpos($sec, 'SL') !== false || strpos($sec, '044') !== false) {
                            $uRole = 'sl';
                        }

                        $isAdmin = (strtolower((string)($userRow['role'] ?? '')) === 'admin' || strtolower((string)($userRow['state'] ?? '')) === 'admin');
                        if ($isAdmin) {
                            $uRole = 'all';
                        }

                        return [
                            'empid'      => $uId,
                            'name'       => $uName,
                            'positionid' => $userRow['position'] ?? $userRow['role'] ?? 'Operator',
                            'role'       => $uRole,
                            'level'      => (int)($userRow['level'] ?? 15),
                            'isadmin'    => $isAdmin
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel users berbeda struktur
        }

        // 3. Validasi Karyawan Biasa (EmpID == Password)
        if ($password === $empid) {
            $q = $this->getDataEmp($empid);

            // Jika SQLSRV tidak aktif dan EmpID baru pertama kali login di lokal,
            // izinkan masuk sebagai Operator dengan akses semua device ('all')
            if (!$q && !$this->isSqlSrvReady()) {
                $q = (object) [
                    'empid'      => $empid,
                    'name'       => strtoupper($empid),
                    'deptid'     => 'ALL',
                    'positionid' => '15,Operator',
                ];
            }

            if (!$q) {
                return false;
            }

            $posParts = preg_split('/,/', (string)($q->positionid ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            $level    = isset($posParts[0]) && is_numeric($posParts[0]) ? (int)$posParts[0] : 15;

            $department = self::determineDepartmentCategory($q->deptid);

            $role = 'bebas';
            if (strpos(strtoupper((string)$q->deptid), 'FRAME LASER') !== false) {
                $role = 'fl';
            } elseif (strpos(strtoupper((string)$q->deptid), 'SINGLE LASER') !== false) {
                $role = 'sl';
            } elseif ($department === 'QC') {
                $role = 'qc';
            }

            if ($level > 0 && $level <= 7) {
                $role = 'all';
            }

            return [
                'empid'      => $empid,
                'name'       => $q->name,
                'positionid' => $q->positionid,
                'deptid'     => $q->deptid,
                'department' => $department,
                'role'       => $role,
                'level'      => $level,
                'isadmin'    => false
            ];
        }

        return false;
    }

    public function testing()
    {
        if (!$this->isSqlSrvReady()) {
            return [];
        }
        try {
            $db = \Config\Database::connect('second');
            $query = "SELECT empid, firstname, lastname, deptid, positionid, empstsid
                      FROM tbempinfa
                      WHERE (empstsid LIKE '%Permanent%' OR empstsid LIKE '%Contract%')";
            return $db->query($query)->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Ambil nama dan email operator secara dinamis berdasarkan empid dari database second
     */
    public function getOperatorContact($empid)
    {
        $result = [
            'empid' => $empid,
            'name'  => '',
            'email' => ''
        ];

        if (empty($empid) || !$this->isSqlSrvReady()) {
            return $result;
        }

        try {
            $db  = \Config\Database::connect('second');
            $row = $db->query("SELECT TOP 1 * FROM tbempinfa WHERE empid = ?", [$empid])->getRowArray();

            if ($row) {
                $fullName = trim(($row['firstname'] ?? '') . ' ' . ($row['midname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                $result['name'] = preg_replace('/\s+/', ' ', $fullName);

                foreach (['email', 'mail', 'email_address', 'user_email', 'work_email'] as $col) {
                    if (!empty($row[$col]) && filter_var(trim((string)$row[$col]), FILTER_VALIDATE_EMAIL)) {
                        $result['email'] = strtolower(trim((string)$row[$col]));
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal ambil kontak operator: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Mengambil seluruh data Foreman aktif dari server 130 (tbempinfa) beserta emailnya
     */
    public function getForemanEmails(): array
    {
        $db = \Config\Database::connect('second');

        // Query mengambil seluruh karyawan aktif dengan positionid Foreman (kode 14 / tulisan FOREMAN)
        $sql = "
            SELECT *
            FROM tbempinfa
            WHERE empstsid <> '3,Not Active'
              AND (
                  positionid LIKE '%14%' 
                  OR positionid LIKE '%FOREMAN%' 
                  OR positionid LIKE '%Foreman%'
              )
        ";

        return (array) $db->query($sql)->getResultArray();
    }
}