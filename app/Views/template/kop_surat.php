<?php
/**
 * Partial Kop Surat (dipakai di semua halaman cetak PDF).
 *
 * Variabel yang dipakai (semua opsional, aman kalau kosong):
 * - $sekolah             : array data dari tabel sekolah
 * - $logoBase64          : base64 logo sekolah (kanan)
 * - $logoProvinsiBase64  : base64 logo pemerintah/provinsi (kiri)
 *
 * Cara pakai di file cetak lain:
 * <?= view('template/kop_surat', ['sekolah' => $sekolah, 'logoBase64' => $logoBase64, 'logoProvinsiBase64' => $logoProvinsiBase64]) ?>
 */

$sekolah            = $sekolah ?? [];
$logoBase64         = $logoBase64 ?? '';
$logoProvinsiBase64 = $logoProvinsiBase64 ?? '';

// Kompetensi keahlian disimpan 1 baris per jurusan di database,
// ditampilkan 2 kolom: baris ganjil di kiri, baris genap di kanan.
$kompetensiList = [];
if (!empty($sekolah['kompetensi_keahlian'])) {
    $kompetensiList = array_filter(array_map('trim', explode("\n", str_replace("\r", '', $sekolah['kompetensi_keahlian']))));
}
$kompetensiKiri  = [];
$kompetensiKanan = [];
foreach (array_values($kompetensiList) as $i => $item) {
    if ($i % 2 === 0) {
        $kompetensiKiri[] = $item;
    } else {
        $kompetensiKanan[] = $item;
    }
}
?>

<style>

.kop-wrapper{
    width:100%;
    border-bottom:3px solid #000;
    padding-bottom:6px;
    margin-bottom:15px;
    font-family: DejaVu Sans, Arial, sans-serif;
}

.kop-utama{
    width:100%;
}

.kop-utama td{
    border:none;
    padding:0;
    vertical-align:middle;
}

.kop-logo{
    width:75px;
    text-align:center;
}

.kop-logo img{
    width:70px;
    height:70px;
}

.kop-tengah{
    text-align:center;
}

.kop-tengah .pemerintah{
    font-size:13px;
    font-weight:bold;
    margin:0;
    letter-spacing:0.3px;
}

.kop-tengah .dinas{
    font-size:13px;
    font-weight:bold;
    margin:0;
    letter-spacing:0.3px;
}

.kop-tengah .nama-sekolah{
    font-size:19px;
    font-weight:bold;
    margin:3px 0;
}

.kop-kompetensi-judul{
    font-size:10px;
    font-weight:bold;
    text-align:center;
    margin:2px 0;
}

.kop-kompetensi{
    width:100%;
    margin-top:2px;
}

.kop-kompetensi td{
    border:none;
    padding:0;
    font-size:9px;
    font-weight:bold;
    text-align:center;
    width:50%;
    line-height:1.5;
}

.kop-footer{
    width:100%;
    margin-top:6px;
    border-top:1px solid #000;
    padding-top:3px;
}

.kop-footer td{
    border:none;
    padding:0;
    font-size:9.5px;
}

.kop-footer .alamat{
    text-align:center;
    padding-bottom:2px;
}

.kop-footer .kiri{
    text-align:left;
    width:34%;
}

.kop-footer .tengah{
    text-align:center;
    width:32%;
}

.kop-footer .kanan{
    text-align:right;
    width:34%;
}

</style>

<div class="kop-wrapper">

<table class="kop-utama">
<tr>

<td class="kop-logo">
<?php if (!empty($logoProvinsiBase64)): ?>
<img src="<?= $logoProvinsiBase64 ?>">
<?php endif; ?>
</td>

<td class="kop-tengah">

<?php if (!empty($sekolah['nama_pemerintah'])): ?>
<p class="pemerintah"><?= esc(strtoupper($sekolah['nama_pemerintah'])) ?></p>
<?php endif; ?>

<?php if (!empty($sekolah['nama_dinas'])): ?>
<p class="dinas"><?= esc(strtoupper($sekolah['nama_dinas'])) ?></p>
<?php endif; ?>

<p class="nama-sekolah"><?= esc(strtoupper($sekolah['nama_sekolah'] ?? '')) ?></p>

<?php if (!empty($kompetensiList)): ?>
<p class="kop-kompetensi-judul">KOMPETENSI KEAHLIAN</p>

<table class="kop-kompetensi">
<tr>
<td>
<?php foreach ($kompetensiKiri as $item): ?>
<?= esc(strtoupper($item)) ?><br>
<?php endforeach; ?>
</td>
<td>
<?php foreach ($kompetensiKanan as $item): ?>
<?= esc(strtoupper($item)) ?><br>
<?php endforeach; ?>
</td>
</tr>
</table>
<?php endif; ?>

</td>

<td class="kop-logo">
<?php if (!empty($logoBase64)): ?>
<img src="<?= $logoBase64 ?>">
<?php endif; ?>
</td>

</tr>
</table>

<table class="kop-footer">

<tr>
<td colspan="3" class="alamat">
Alamat : <?= esc($sekolah['alamat'] ?? '') ?><?= !empty($sekolah['kode_pos']) ? ', ' . esc($sekolah['kode_pos']) : '' ?><?= !empty($sekolah['telepon']) ? ', Telp. ' . esc($sekolah['telepon']) : '' ?>
</td>
</tr>

<tr>
<td class="kiri">
<?php if (!empty($sekolah['nss']) || !empty($sekolah['npsn'])): ?>
NSS/NPSN : <?= esc($sekolah['nss'] ?? '') ?><?= (!empty($sekolah['nss']) && !empty($sekolah['npsn'])) ? '/' : '' ?><?= esc($sekolah['npsn'] ?? '') ?>
<?php endif; ?>
</td>
<td class="tengah">
<?php if (!empty($sekolah['website'])): ?>
Website : <?= esc($sekolah['website']) ?>
<?php endif; ?>
</td>
<td class="kanan">
<?php if (!empty($sekolah['email'])): ?>
Email : <?= esc($sekolah['email']) ?>
<?php endif; ?>
</td>
</tr>

</table>

</div>
