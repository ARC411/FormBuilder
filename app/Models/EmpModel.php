<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpModel extends Model
{
    /**
     * Ambil data karyawan untuk auto-fill shift & group.
     * - groupid : dari jadwal (tbtschemp), LEFT JOIN agar baris tidak hilang
     * - acc     : dari tap terakhir (hari ini atau kemarin, untuk shift malam)
     */
    public function getEmp($empid)
    {
        try {
            $db = \Config\Database::connect('second');

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

            $res = $db->query($sql, [$empid, $empid])->getRow();
            if ($res) {
                return $res;
            }
            return null;
        } catch (\Throwable $e) {
            log_message('error', 'getEmp error on second: ' . $e->getMessage());
        }

        // Fallback untuk autofill di lokal jika SQL Server .130 sedang offline
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('tbempinfa')) {
                $row = $db->table('tbempinfa')
                    ->select("empid, empid as cardid, TRIM(CONCAT_WS(' ', COALESCE(firstname, ''), COALESCE(midname, ''), COALESCE(lastname, ''))) as name, positionid, 'A' as groupid, '1' as acc")
                    ->where('empstsid <>', '3,Not Active')
                    ->where('empid', $empid)
                    ->get()
                    ->getRow();
                if ($row) {
                    return $row;
                }
            }
        } catch (\Throwable $e) {}

        return null;
    }

    public function getDataEmp($empid)
    {
        $empid = trim((string)$empid);
        if ($empid === '') {
            return null;
        }

        try {
            $db = \Config\Database::connect('second');

            $q = "SELECT empid, LTRIM(RTRIM(ISNULL(firstname, '') + ' ' + ISNULL(midname, '') + ' ' + ISNULL(lastname, ''))) as name, deptid, positionid, titleid, empstsid
                  FROM tbempinfa
                  WHERE empstsid <> '3,Not Active' AND empid = ?";

            $res = $db->query($q, [$empid])->getRow();
            if ($res) {
                return $res;
            }
            // Jika koneksi SQL Server berhasil tetapi data memang tidak ada, jangan fallback
            return null;
        } catch (\Throwable $e) {
            log_message('error', 'getDataEmp error on second: ' . $e->getMessage());
        }

        // Fallback untuk testing di lokal jika SQL Server .130 sedang offline
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('tbempinfa')) {
                return $db->table('tbempinfa')
                    ->select("empid, TRIM(CONCAT_WS(' ', COALESCE(firstname, ''), COALESCE(midname, ''), COALESCE(lastname, ''))) as name, deptid, positionid, titleid, empstsid")
                    ->where('empstsid <>', '3,Not Active')
                    ->where('empid', $empid)
                    ->get()
                    ->getRow();
            }
        } catch (\Throwable $e) {}

        return null;
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
        $session = \Config\Services::session();
        $session->start();

        $empid    = trim((string)$empid);
        $password = trim((string)$password);

        if ($empid === '' || $password === '') {
            return false;
        }

        // 1. Validasi Super Admin Lokal
        if ($empid == $password && $password == 'admin') {
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

        // 2. Validasi Karyawan Biasa
        if ($password == $empid) {
            $q = $this->getDataEmp($empid);

            if (!$q) {
                return false;
            }

            $level_string = preg_split('/,/', (string)($q->positionid ?? ''), -1, PREG_SPLIT_NO_EMPTY)[0] ?? '15';
            $level = (int) $level_string;

            $department = self::determineDepartmentCategory($q->deptid ?? '');

            $role = 'bebas';
            if (strpos(strtoupper((string)($q->deptid ?? '')), 'FRAME LASER') !== false) {
                $role = 'fl';
            } elseif (strpos(strtoupper((string)($q->deptid ?? '')), 'SINGLE LASER') !== false) {
                $role = 'sl';
            } elseif ($department === 'QC') {
                $role = 'qc';
            }

            // Level harus di antara 1 sampai 7 (bukan 0)
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
        try {
            $db = \Config\Database::connect('second');

            // Menggunakan LIKE untuk mencocokkan teks Permanent dan Contract
            $query = "SELECT empid, firstname, lastname, deptid, positionid, empstsid
                      FROM tbempinfa
                      WHERE (empstsid LIKE '%Permanent%' OR empstsid LIKE '%Contract%')";

            $res = $db->query($query)->getResultArray();
            if (!empty($res)) {
                return $res;
            }
        } catch (\Throwable $e) {
            log_message('error', 'testing error on second: ' . $e->getMessage());
        }

        // Fallback untuk cek_role() di lokal jika SQL Server .130 sedang offline
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('tbempinfa')) {
                return $db->table('tbempinfa')
                    ->select('empid, firstname, lastname, deptid, positionid, empstsid')
                    ->where('empstsid <>', '3,Not Active')
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {}

        return [];
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

        if (empty($empid)) {
            return $result;
        }

        try {
            $db = \Config\Database::connect('second');
            $row = $db->query("SELECT TOP 1 * FROM tbempinfa WHERE empid = ?", [$empid])->getRowArray();

            if ($row) {
                $fullName = trim(($row['firstname'] ?? '') . ' ' . ($row['midname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                $result['name'] = preg_replace('/\s+/', ' ', $fullName);

                // Cari kolom email yang ada di tabel tbempinfa
                foreach (['email', 'mail', 'email_address', 'user_email', 'work_email'] as $col) {
                    if (!empty($row[$col]) && filter_var(trim((string)$row[$col]), FILTER_VALIDATE_EMAIL)) {
                        $result['email'] = strtolower(trim((string)$row[$col]));
                        break;
                    }
                }
                return $result;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal ambil kontak operator dari second: ' . $e->getMessage());
        }

        // Fallback lokal jika offline
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('tbempinfa')) {
                $row = $db->table('tbempinfa')->where('empid', $empid)->get()->getRowArray();
                if ($row) {
                    $fullName = trim(($row['firstname'] ?? '') . ' ' . ($row['midname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                    $result['name'] = preg_replace('/\s+/', ' ', $fullName);
                    if (!empty($row['email'])) {
                        $result['email'] = strtolower(trim((string)$row['email']));
                    }
                    return $result;
                }
            }
        } catch (\Throwable $e) {}

        return $result;
    }

    /**
     * Mengambil seluruh data Foreman aktif dari server 130 (tbempinfa) beserta emailnya
     */
    public function getForemanEmails(): array
    {
        try {
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

            $res = (array) $db->query($sql)->getResultArray();
            if (!empty($res)) {
                return $res;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal ambil email foreman dari second: ' . $e->getMessage());
        }

        // Fallback lokal jika offline
        try {
            $db = \Config\Database::connect('default');
            if ($db->tableExists('tbempinfa')) {
                return (array) $db->table('tbempinfa')
                    ->where('empstsid <>', '3,Not Active')
                    ->groupStart()
                        ->like('positionid', '14')
                        ->orLike('positionid', 'FOREMAN')
                        ->orLike('positionid', 'Foreman')
                    ->groupEnd()
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {}

        return [];
    }
}
