<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Absensi</title>

<style>

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:12px;
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
    width:80px;
}

.judul{
    text-align:center;
}

.judul h2{
    margin:0;
    font-size:22px;
}

.judul h3{
    margin:3px 0;
    font-size:18px;
}

.judul p{
    margin:2px;
    font-size:12px;
}

hr{
    border:1px solid #000;
    margin-top:5px;
}

.info{
    margin-top:15px;
    margin-bottom:15px;
}

.info td{
    padding:3px;
    border:none;
}

.tabel{
    margin-top:10px;
}

.tabel th{

    background:#eeeeee;
    border:1px solid #000;
    padding:8px;
    text-align:center;

}

.tabel td{

    border:1px solid #000;
    padding:7px;

}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.ttd{

    margin-top:50px;

}

.ttd td{

    border:none;
    text-align:center;
    padding-top:30px;

}

</style>

</head>

<body>



<?= view('template/kop_surat', [
    'sekolah'            => $sekolah,
    'logoBase64'         => $logoBase64,
    'logoProvinsiBase64' => $logoProvinsiBase64,
]) ?>

<h3 style="text-align:center;margin-top:15px;">

LAPORAN ABSENSI SISWA

</h3>

<table class="info">

<tr>

<td width="180">Tanggal</td>

<td width="10">:</td>

<td><?= date('d-m-Y',strtotime($tanggal)) ?></td>

</tr>

<tr>

<td>Kelas</td>

<td>:</td>

<td><?= $kelas ?></td>

</tr>

<tr>

<td>Tahun Pelajaran</td>

<td>:</td>

<td><?= $tahunAktif['tahun'] ?? '' ?></td>

</tr>

<tr>

<td>Semester</td>

<td>:</td>

<td><?= $semesterAktif['semester'] ?? '' ?></td>

</tr>

</table>

<?php

$hadir=0;
$sakit=0;
$izin=0;
$pulangCepat=0;
$bolos=0;

foreach($absensi as $a){

    switch($a['status']){
        case 'H': $hadir++; break;
        case 'S': $sakit++; break;
        case 'I': $izin++; break;
        case 'P': $pulangCepat++; break;
        case 'B': $bolos++; break;
    }

}

?>

<table class="tabel">

<thead>

<tr>

<th width="40">No</th>

<th width="100">NIS</th>

<th>Nama Siswa</th>

<th width="110">Status</th>

<th width="70">Sejak Jam</th>

</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($absensi as $row): ?>

<tr>

<td class="text-center"><?= $no++ ?></td>

<td><?= $row['nis'] ?></td>

<td><?= $row['nama_siswa'] ?></td>

<td class="text-center">

<?= $labelStatus[$row['status']] ?? '-' ?>

</td>

<td class="text-center">

<?= !empty($row['jam_sejak']) ? 'Jam '.$row['jam_sejak'] : '-' ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

<tfoot>

<tr>

<td colspan="5">

<strong>

Rekap :

Hadir : <?= $hadir ?>

&nbsp;&nbsp;&nbsp;

Sakit : <?= $sakit ?>

&nbsp;&nbsp;&nbsp;

Izin Keluarga : <?= $izin ?>

&nbsp;&nbsp;&nbsp;

Pulang Cepat : <?= $pulangCepat ?>

&nbsp;&nbsp;&nbsp;

Bolos : <?= $bolos ?>

</strong>

</td>

</tr>

</tfoot>

</table>

<table class="ttd">

<tr>

<td width="60%"></td>

<td>

<?= $sekolah['kabupaten'] ?? '' ?>,
<?= date('d-m-Y') ?>

<br><br>

Wali Kelas / Petugas Absen

<br><br><br><br><br>

<b>

........................................

</b>

</td>

</tr>

</table>


</body>
</html>
