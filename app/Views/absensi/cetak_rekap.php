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

LAPORAN REKAPITULASI  ABSENSI SISWA

</h3>


<table class="info">

<tr>

<td width="150">Kelas</td>

<td width="10">:</td>

<td><?= $kelas ?></td>

</tr>

<tr>

<td>Periode</td>

<td>:</td>

<td>
<?= date('d-m-Y',strtotime($tanggal_awal)) ?>
s/d
<?= date('d-m-Y',strtotime($tanggal_akhir)) ?>
</td>

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

<table border="1" width="100%" cellspacing="0" cellpadding="5">

    <thead>

        <tr style="background:#eee;text-align:center;font-weight:bold;">
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Hadir</th>
            <th>Sakit</th>
            <th>Izin Keluarga</th>
            <th>Pulang Cepat</th>
            <th>Bolos</th>
            <th>Total Hari</th>
            <th>Persentase Hadir</th>
        </tr>

    </thead>

    <tbody>

    <?php
    $no=1;

    foreach($absensi as $row):

        $persen = 0;

        if($row['total']>0){

            $persen = round(($row['hadir']/$row['total'])*100,2);

        }
    ?>

        <tr>

            <td align="center"><?= $no++ ?></td>

            <td><?= $row['nis'] ?></td>

            <td><?= $row['nama_siswa'] ?></td>

            <td align="center"><?= $row['hadir'] ?></td>

            <td align="center"><?= $row['sakit'] ?></td>

            <td align="center"><?= $row['izin'] ?></td>

            <td align="center"><?= $row['pulang_cepat'] ?></td>

            <td align="center"><?= $row['bolos'] ?></td>

            <td align="center"><?= $row['total'] ?></td>

            <td align="center"><?= $persen ?>%</td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>
</body>
</html>
