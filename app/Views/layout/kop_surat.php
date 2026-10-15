<?php
// Rumus Matematika Grid Universal
// Jika tidak diisi, default 32 kolom
$total = isset($total_kolom) ? $total_kolom : 32;

$col_right = 5; // Fixed 5 kolom untuk kotak Checked/No Dok (2 kolom label + 3 kolom isi)
$col_left  = floor($total * 0.35); // 35% lebar untuk teks PT Foxconn
if ($col_left < 7) $col_left = 7; // Minimal 7 kolom agar teks tidak tergencet

$col_center = $total - $col_left - $col_right; // Sisa kolom otomatis untuk Judul di tengah

// Normalisasi variabel agar kompatibel dengan semua Controller & Export
$nama_produk_fix     = $nama_produk ?? $namaProduk ?? '-';
$judul_dokumen_fix   = $judul_dokumen ?? $judulProses ?? '-';
$no_dok_fix          = $no_dok ?? $noDok ?? '-';
$nama_mesin_fix      = $nama_mesin ?? $machNo ?? 'ALL MACHINES';
$tanggal_berlaku_fix = $tanggal_berlaku ?? $tglBerlaku ?? '-';
$revisi_fix          = isset($revisi) && $revisi !== '' ? str_pad((string)$revisi, 2, '0', STR_PAD_LEFT) : '00';
$approver_fix        = $namaApprover ?? '';
?>

<!-- BARIS 1 -->
<tr>
    <td colspan="<?= $col_left ?>" rowspan="4" style="text-align: left; vertical-align: top; border: none; font-size: 11px;">
        PT. FOXCONN TECHNOLOGIES INDONESIA<br>
        Production Engineering Department<br>
        Process Engineering Section<br>
        <b><?= strtoupper($nama_produk_fix) ?></b>
    </td>
    <td colspan="<?= $col_center ?>" rowspan="2" style="text-align: center; vertical-align: middle; border: none;">
        <b style="font-size: 16px; text-decoration: underline;"><?= $judul_dokumen_fix ?></b>
    </td>
    <td colspan="2" style="border: 1px solid black; text-align: left; padding: 2px;">No. Dok</td>
    <td colspan="3" style="border: 1px solid black; text-align: left; padding: 2px;">: <?= $no_dok_fix ?></td>
</tr>
<!-- BARIS 2 -->
<tr>
    <td colspan="2" style="border: 1px solid black; text-align: left; padding: 2px;">Revisi</td>
    <td colspan="3" style="border: 1px solid black; text-align: left; padding: 2px;">: <?= $revisi_fix ?></td>
</tr>
<!-- BARIS 3 -->
<tr>
    <td colspan="<?= $col_center ?>" rowspan="2" style="text-align: center; vertical-align: top; border: none; font-size: 12px;">
        (<?= $nama_mesin_fix ?>)
    </td>
    <td colspan="2" style="border: 1px solid black; text-align: left; padding: 2px;">Berlaku</td>
    <td colspan="3" style="border: 1px solid black; text-align: left; padding: 2px;">: <?= $tanggal_berlaku_fix ?></td>
</tr>
<!-- BARIS 4 -->
<tr>
    <td colspan="5" style="border: 1px solid black; text-align: center; vertical-align: middle; padding: 2px;">Checked</td>
</tr>
<!-- BARIS 5 -->
<tr>
    <td colspan="<?= $col_left ?>" style="text-align: left; border: none; font-size: 11px;">
        MACHINE No &nbsp;&nbsp;&nbsp;: <?= ($nama_mesin_fix !== 'ALL MACHINES') ? $nama_mesin_fix : '' ?>
    </td>
    <td colspan="<?= $col_center ?>" style="border: none;"></td>
    <td colspan="5" rowspan="2" style="border: 1px solid black; text-align: center; vertical-align: middle; font-size: 11px;">
        <?= !empty($approver_fix) ? nl2br(esc($approver_fix)) : '' ?>
    </td>
</tr>
<!-- BARIS 6 -->
<tr>
    <td colspan="<?= $col_left ?>" style="text-align: left; border: none; font-size: 11px; padding-bottom: 15px;">
        AG Paste Type &nbsp;&nbsp;: 
    </td>
    <td colspan="<?= $col_center ?>" style="border: none;"></td>
</tr>
<!-- BARIS KOSONG PEMISAH KOP SURAT DAN TABEL -->
<tr><td colspan="<?= $total ?>" style="border: none; height: 10px;"></td></tr>