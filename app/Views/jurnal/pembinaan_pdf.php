<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">

<style>

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:11px;
    color:#000;
}

table{
    width:100%;
    border-collapse:collapse;
}

.info td{
    border:none;
    padding:3px;
}

.data{
    table-layout:fixed;
}

.data th{
    background:#eaeaea;
    border:1px solid #000;
    padding:6px;
    text-align:center;
}

.data td{
    border:1px solid #000;
    padding:5px;
    vertical-align:top;
    word-wrap:break-word;
    overflow-wrap:break-word;
}

</style>

</head>

<body>

<?= view('template/kop_surat', [
    'sekolah'            => $sekolah,
    'logoBase64'         => $logoBase64,
    'logoProvinsiBase64' => $logoProvinsiBase64,
]) ?>

<h3 style="text-align:center;margin:5px 0;">
LAPORAN PEMBINAAN SISWA
</h3>

<br>

<table class="info">

<tr>

<td width="120">Periode</td>
<td width="5">:</td>
<td>
<?= !empty($tgl1) ? date('d-m-Y', strtotime($tgl1)) : '-' ?>
s/d
<?= !empty($tgl2) ? date('d-m-Y', strtotime($tgl2)) : '-' ?>
</td>

<td width="100">Jumlah Data</td>
<td width="5">:</td>
<td><?= $jumlah ?></td>

</tr>

<tr>

<td>Guru Pembina</td>
<td>:</td>
<td><?= esc($namaGuruFilter) ?: 'Semua Guru' ?></td>

<td>Siswa</td>
<td>:</td>
<td><?= esc($namaSiswaFilter) ?: 'Semua Siswa' ?></td>

</tr>

</table>

<br>

<table class="data">

<thead>

<tr>
<th width="25">No</th>
<th width="55">Tanggal</th>
<th width="55">NIS</th>
<th width="110">Nama Siswa</th>
<th width="55">Kelas</th>
<th width="70">Tingkat</th>
<th width="110">Guru Pembina</th>
<th>Tindak Lanjut</th>
</tr>

</thead>

<tbody>

<?php $no = 1; ?>

<?php foreach ($riwayat as $r): ?>

<tr>

<td style="text-align:center;"><?= $no++ ?></td>
<td><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
<td><?= esc($r['nis'] ?? '-') ?></td>
<td><?= esc($r['nama_siswa'] ?? '-') ?></td>
<td><?= esc($r['nama_kelas'] ?? '-') ?></td>
<td><?= esc($r['tingkat_label']) ?></td>
<td><?= esc($r['nama_penindak']) ?></td>
<td><?= esc($r['tindak_lanjut']) ?></td>

</tr>

<?php endforeach; ?>

<?php if (empty($riwayat)): ?>
<tr>
<td colspan="8" style="text-align:center;">Tidak ada data.</td>
</tr>
<?php endif; ?>

</tbody>

</table>

</body>

</html>
