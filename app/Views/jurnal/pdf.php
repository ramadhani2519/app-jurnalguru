<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size:11px;
}

.judul{
    text-align:center;
    margin:15px 0;
}

.info{
    width:100%;
    margin-bottom:15px;
}

.info td{
    padding:3px;
    border:none;
}

table.tabel{
    width:100%;
    border-collapse:collapse;
}

table.tabel th{
    background:#0d6efd;
    color:#fff;
    border:1px solid #000;
    padding:6px;
    text-align:center;
}

table.tabel td{
    border:1px solid #000;
    padding:5px;
    vertical-align:top;
}

.rekap{
    margin-top:10px;
    margin-bottom:20px;
}

.ttd{
    width:100%;
    margin-top:40px;
}

.ttd td{
    border:none;
    text-align:center;
}

</style>

</head>

<body>

	
<!-- HEADER -->

<?= view('template/kop_surat', [
    'sekolah'            => $sekolah,
    'logoBase64'         => $logoBase64,
    'logoProvinsiBase64' => $logoProvinsiBase64,
]) ?>

<h3 style="text-align:center;margin-top:10px;">LAPORAN JURNAL MENGAJAR GURU</h3>

<!-- IDENTITAS -->

<table class="info">

<?php if($guru != null): ?>

<tr>

<td width="120"><b>Nama Guru</b></td>

<td width="10">:</td>

<td><?= $guru['nama']; ?></td>

</tr>

<tr>

<td><b>NIP</b></td>

<td>:</td>

<td><?= $guru['nip']; ?></td>

</tr>

<?php else: ?>

<tr>

<td width="120"><b>Guru</b></td>

<td width="10">:</td>

<td>Semua Guru</td>

</tr>

<?php endif; ?>

<tr>

<td><b>Tahun Pelajaran</b></td>

<td>:</td>

<td><?= $tahunAktif['tahun']; ?></td>

</tr>

<tr>

<td><b>Semester</b></td>

<td>:</td>

<td><?= $semesterAktif['semester']; ?></td>

</tr>

<tr>

<td><b>Periode</b></td>

<td>:</td>

<td>

<?php

if($tgl1 && $tgl2){

echo date('d-m-Y',strtotime($tgl1));

echo " s/d ";

echo date('d-m-Y',strtotime($tgl2));

}else{

echo "Semua Periode";

}

?>

</td>

</tr>
<tr>

<td><b>Jam ke</b></td>

<td>:</td>

<td><?= $jam_ke ?></td>

</tr>

<tr>
</table>

<div class="rekap">

<b>Jumlah Jurnal Mengajar :</b>

<?= $jumlahJurnal ?> Kali Mengajar

</div>

<table class="tabel">

<thead>

<tr>

<th width="40">No</th>

<th width="65">Tanggal</th>

<th width="90">Guru</th>

<th width="50">Kelas</th>

<th width="90">Mata Pelajaran</th>

<th width="35">Jam</th>

<th width="70">Waktu</th>

<th>Materi</th>

<th width="110">Refleksi Pembelajaran</th>

</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($jurnal as $row): ?>

<tr>

<td align="center"><?= $no++ ?></td>

<td align="center">

<?= date('d-m-Y',strtotime($row['tanggal'])) ?>

</td>

<td><?= $row['nama'] ?></td>

<td align="center"><?= $row['nama_kelas'] ?></td>

<td><?= $row['nama_mapel'] ?></td>

<td align="center"><?= $row['jam_ke'] ?></td>

<td align="center">

<?php if(!empty($row['jam_mulai']) && !empty($row['jam_akhir'])): ?>
<?= substr($row['jam_mulai'],0,5) ?>-<?= substr($row['jam_akhir'],0,5) ?>
<?php else: ?>
-
<?php endif; ?>

</td>

<td><?= $row['materi'] ?></td>

<td><?= $row['keterangan'] ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<!-- TANDA TANGAN -->

<table class="ttd">

<tr>

<td width="50%"></td>

<td>

<?= date('d F Y'); ?>

<br><br>

Guru Mata Pelajaran

<br><br><br><br><br>

<b>

<?= esc($guru['nama'] ?? session()->get('nama')); ?>

</b>

</td>

</tr>

</table>

</body>

</html>
