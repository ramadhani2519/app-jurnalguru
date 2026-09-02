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

.header td{
    border:none;
}

.logo{
    width:70px;
}

.judul{
    text-align:center;
}

.judul h2{
    margin:0;
    font-size:20px;
}

.judul h3{
    margin:2px;
    font-size:16px;
}

.judul p{
    margin:0;
    font-size:11px;
}

hr{
    border:1px solid #000;
    margin:8px 0;
}

.info td{
    border:none;
    padding:3px;
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
}

.footer{
    margin-top:35px;
}

.footer td{
    border:none;
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
LAPORAN DATA PELANGGARAN SISWA
</h3>

<br>

<table class="info">

<tr>

<td width="120">Periode</td>

<td width="5">:</td>

<td>

<?= !empty($tgl1) ? date('d-m-Y',strtotime($tgl1)) : '-' ?>

s/d

<?= !empty($tgl2) ? date('d-m-Y',strtotime($tgl2)) : '-' ?>

</td>

<td width="100">Jumlah Data</td>

<td width="5">:</td>

<td><?= $jumlah ?></td>

</tr>

<tr>

<td>Kelas</td>

<td>:</td>

<td><?= $kelas ?: 'Semua Kelas' ?></td>

<td>Siswa</td>

<td>:</td>

<td><?= $siswa ?: 'Semua Siswa' ?></td>

</tr>

</table>

<br>

<table class="data">

<thead>

<tr>

<th width="40">No</th>

<th width="80">Tanggal</th>

<th width="90">NIS</th>

<th>Nama Siswa</th>

<th width="90">Kelas</th>

<th>Uraian Pelanggaran</th>

<th width="120">Keterangan</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

foreach($pelanggaran as $row):

?>

<tr>

<td align="center"><?= $no++ ?></td>

<td align="center">

<?= date('d-m-Y',strtotime($row['tanggal'])) ?>

</td>

<td align="center">

<?= $row['nis'] ?>

</td>

<td>

<?= $row['nama_siswa'] ?>

</td>

<td align="center">

<?= $row['nama_kelas'] ?>

</td>

<td>

<?= $row['uraian_pelanggaran'] ?>

</td>

<td>

<?= $row['keterangan'] ?>

</td>

</tr>

<?php endforeach ?>

<?php if(empty($pelanggaran)): ?>

<tr>

<td colspan="7" align="center">

Tidak ada data

</td>

</tr>

<?php endif; ?>

<tr>

<td colspan="6" align="right">

<b>Total Kasus</b>

</td>

<td>

<b><?= (int) $jumlah ?></b>

</td>

</tr>

</tbody>

</table>

<table class="footer">

<tr>

<td width="60%"></td>

<td align="center">

<?= $sekolah['desa'] ?>,

<?= date('d F Y') ?>

<br><br>

Kepala Sekolah

<br><br><br><br><br>

<b>

<?= $sekolah['kepala_sekolah'] ?>

</b>

<br>

NIP.

<?= $sekolah['nip_kepala'] ?>

</td>

</tr>

</table>

</body>
</html>