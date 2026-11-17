<?php

if (!function_exists('calculateWorkDate')) {
    /**
     * Menghitung Tanggal Operasional Kerja (work_date).
     * Jam kerja Shift C (Malam) adalah 23:20 s/d 07:00 WIB.
     * Jika checksheet diinput antara pukul 00:00:00 s/d 06:59:59 (lewat tengah malam),
     * maka tanggal kerja operasional dihitung mundur 1 hari (H-1) dari tanggal transaksi aktual.
     *
     * @param string|null $datetime Waktu transaksi aktual (Y-m-d H:i:s)
     * @param string|null $shift    Shift kerja ('A', 'B', 'C', atau '1', '2', '3')
     * @return string Tanggal kerja operasional (format Y-m-d)
     */
    function calculateWorkDate($datetime = null, $shift = null): string
    {
        $dtStr = !empty($datetime) ? trim((string)$datetime) : date('Y-m-d H:i:s');
        $ts = strtotime($dtStr);
        if (!$ts) {
            $ts = time();
        }

        $hour = (int)date('H', $ts);
        $shiftStr = strtoupper(trim((string)$shift));

        // Jika Shift C dan waktu input antara 00:00:00 s/d 06:59:59
        if (($shiftStr === 'C' || $shiftStr === '3') && $hour < 7) {
            return date('Y-m-d', strtotime('-1 day', $ts));
        }

        return date('Y-m-d', $ts);
    }
}

if (!function_exists('getRecordWorkDate')) {
    /**
     * Mengambil tanggal kerja operasional dari record checksheet.
     * Mengutamakan kolom 'work_date' jika ada, atau menghitung secara fallback dari 'created_at' dan 'shift'.
     *
     * @param array|object|null $record Data baris checksheet
     * @param string $format Format output tanggal (default 'Y-m-d')
     * @return string Tanggal kerja operasional
     */
    function getRecordWorkDate($record, string $format = 'Y-m-d'): string
    {
        if (is_object($record)) {
            $record = (array)$record;
        }
        if (!is_array($record)) {
            return '-';
        }

        $workDate = trim((string)($record['work_date'] ?? ''));
        if (!empty($workDate) && $workDate !== '0000-00-00' && $workDate !== '-') {
            $ts = strtotime($workDate);
            return $ts ? date($format, $ts) : $workDate;
        }

        $createdAt = trim((string)($record['created_at'] ?? ''));
        if (empty($createdAt) || $createdAt === '0000-00-00 00:00:00') {
            return '-';
        }

        $shift = $record['shift'] ?? '';
        $calcDate = calculateWorkDate($createdAt, $shift);
        $ts = strtotime($calcDate);
        return $ts ? date($format, $ts) : $calcDate;
    }
}

if (!function_exists('formatWorkDate')) {
    /**
     * Shortcut untuk format tanggal kerja d/m/Y
     */
    function formatWorkDate($record): string
    {
        return getRecordWorkDate($record, 'd/m/Y');
    }
}

