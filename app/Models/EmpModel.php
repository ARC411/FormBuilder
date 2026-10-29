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
                $u = $db->table('users')
                    ->groupStart()
                        ->where('empid', $empid)
                        ->orWhere('username', $empid)
                    ->groupEnd()
                    ->get()
                    ->getRowArray();

                if ($u) {
                    return (object) [
                        'empid'      => $u['empid'] ?? $u['username'] ?? $empid,
                        'cardid'     => $empid,
                        'name'       => $u['name'] ?? $u['fullname'] ?? $u['username'] ?? strtoupper($empid),
                        'deptid'     => $u['dept'] ?? $u['deptid'] ?? 'ALL',
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

        return $this->getLocalEmpFallback((string)$empid);
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
                'level'      => 1,
                'isadmin'    => true
            ];
        }

        // 2. Cek apakah terdaftar di tabel users lokal dengan password khusus
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('users')) {
                $userRow = $db->table('users')
                    ->groupStart()
                        ->where('empid', $empid)
                        ->orWhere('username', $empid)
                    ->groupEnd()
                    ->get()
                    ->getRowArray();

                if ($userRow) {
                    $dbPass = $userRow['password'] ?? '';
                    $passMatch = ($password === $empid)
                        || ($password === $dbPass)
                        || (strlen($dbPass) > 20 && password_verify($password, $dbPass));

                    if ($passMatch) {
                        $uId   = $userRow['empid'] ?? $userRow['username'] ?? $empid;
                        $uName = $userRow['name'] ?? $userRow['fullname'] ?? $userRow['username'] ?? $uId;
                        $uRole = strtolower((string)($userRow['role'] ?? 'all'));
                        if (!in_array($uRole, ['sl', 'fl', 'all'])) {
                            $uRole = 'all';
                        }
                        return [
                            'empid'      => $uId,
                            'name'       => $uName,
                            'positionid' => $userRow['position'] ?? $userRow['role'] ?? 'Operator',
                            'role'       => $uRole,
                            'level'      => (int)($userRow['level'] ?? 15),
                            'isadmin'    => (strtolower((string)($userRow['role'] ?? '')) === 'admin')
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

            $role = 'all';
            $dept = strtoupper((string)($q->deptid ?? ''));
            if (strpos($dept, 'FRAME LASER') !== false || $dept === 'FL') {
                $role = 'fl';
            } elseif (strpos($dept, 'SINGLE LASER') !== false || $dept === 'SL') {
                $role = 'sl';
            }

            if ($level > 0 && $level <= 7) {
                $role = 'all';
            }

            return [
                'empid'      => $empid,
                'name'       => !empty($q->name) ? $q->name : strtoupper($empid),
                'positionid' => $q->positionid ?? 'Operator',
                ' shift'      => $q->acc ?? '',
                'groupid'    => $q->groupid ?? '',
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
}