<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:12px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid #000;
    padding:6px;
}

.header td{
    border:none;
}

.judul{
    text-align:center;
}

.judul h2{
    margin:0;
}

.judul h3{
    margin:3px;
}

hr{
    border:1px solid #000;
}

.identitas td{
    border:none;
    padding:4px;
}

.ttd{
    margin-top:60px;
    width:100%;
}

.ttd td{
    border:none;
    text-align:center;
}

</style>

</head>

<body>

<?= view('template/kop_surat', [
    'sekolah'            => $sekolah,
    'logoBase64'         => $logoBase64,
    'logoProvinsiBase64' => $logoProvinsiBase64,
]) ?>

<h3 style="text-align:center;margin-top:10px;">KARTU PELANGGARAN SISWA</h3>

<br>

<table class="identitas">

<tr>
<td width="150">Tanggal</td>
<td>: <?= date('d-m-Y',strtotime($pelanggaran['tanggal'])) ?></td>
</tr>

<tr>
<td>NIS</td>
<td>: <?= $pelanggaran['nis'] ?></td>
</tr>

<tr>
<td>Nama Siswa</td>
<td>: <?= $pelanggaran['nama_siswa'] ?></td>
</tr>

<tr>
<td>Kelas</td>
<td>: <?= $pelanggaran['nama_kelas'] ?></td>
</tr>

</table>

<br>

<table>

<tr style="background:#eaeaea;">

<th width="40">No</th>

<th>Uraian Pelanggaran</th>

<th width="120">Keterangan</th>

</tr>

<tr>

<td align="center">1</td>

<td><?= $pelanggaran['uraian_pelanggaran'] ?></td>

<td><?= $pelanggaran['keterangan'] ?></td>

</tr>

</table>

<br><br>

<table class="ttd">

<tr>

<td width="50%">

Mengetahui,<br>

Wali Kelas

<br><br><br><br>

(................................)

</td>

<td>

Guru BK

<br><br><br><br>

(................................)

</td>

</tr>

</table>

</body>

</html>