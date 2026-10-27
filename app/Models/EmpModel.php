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

        return $db->query($sql, [$empid, $empid])->getRow();
    }

    public function getDataEmp($empid)
    {
        $db = \Config\Database::connect('second');

        $q = "SELECT empid, LTRIM(RTRIM(ISNULL(firstname, '') + ' ' + ISNULL(midname, '') + ' ' + ISNULL(lastname, ''))) as name, deptid, positionid, titleid, empstsid
              FROM tbempinfa
              WHERE empstsid <> '3,Not Active' AND empid = ?";

        return $db->query($q, [$empid])->getRow();
    }

    public function login($empid, $password)
    {
        $session = \Config\Services::session();
        $session->start();

        // 1. Validasi Super Admin Lokal
        if ($empid == $password && $password == 'admin') {
            return [
                'empid'      => 'admin',
                'name'       => 'admin',
                'positionid' => 'admin',
                'role'       => 'all',
                'isadmin'    => true
            ];
        }

        // 2. Validasi Karyawan Biasa
        if ($password == $empid) {
            $q = $this->getDataEmp($empid);

            if (!$q) {
                return false;
            }

            $level_string = preg_split('/,/', $q->positionid, -1, PREG_SPLIT_NO_EMPTY)[0];
            $level = (int) $level_string;

            $role = 'bebas';
            if ($q->deptid == '017,FRAME LASER 1.8') {
                $role = 'fl';
            } elseif ($q->deptid == '044,SINGLE LASER 5.6') {
                $role = 'sl';
            }

            // Level harus di antara 1 sampai 7 (bukan 0)
            if ($level > 0 && $level <= 7) {
                $role = 'all';
            }

            return [
                'empid'      => $empid,
                'name'       => $q->name,
                'positionid' => $q->positionid,
                'role'       => $role,
                'level'      => $level,
                'isadmin'    => false
            ];
        }

        return false;
    }

    public function testing()
    {
        $db = \Config\Database::connect('second');

        // Menggunakan LIKE untuk mencocokkan teks Permanent dan Contract
        $query = "SELECT empid, firstname, lastname, deptid, positionid, empstsid
                  FROM tbempinfa
                  WHERE (empstsid LIKE '%Permanent%' OR empstsid LIKE '%Contract%')";

        return $db->query($query)->getResultArray();
    }

    /**
     * Ambil seluruh kolom data karyawan aktif yang memiliki jabatan Foreman
     * agar semua kemungkinan nama kolom email di tbempinfa dapat terbaca.
     */
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
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal ambil kontak operator: ' . $e->getMessage());
        }

        return $result;
    }
}