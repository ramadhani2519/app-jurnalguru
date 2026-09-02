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

hr{
    border:1px solid #000;
    margin:8px 0;
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
REKAP HASIL PEMBINAAN SISWA PER GURU WALI
</h3>
<p style="text-align:center;margin:0 0 10px 0;">
Tahun Pelajaran: <?= esc($tahunAktif['tahun'] ?? '-') ?>
</p>

<table class="data">
    <thead>
        <tr>
            <th style="width:35px;">No</th>
            <th>Guru Wali</th>
            <th style="width:110px;">Jumlah Siswa Asuh</th>
            <th style="width:110px;">Jumlah Kasus</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($rekap as $r): ?>
        <tr>
            <td style="text-align:center;"><?= $no++ ?></td>
            <td><?= esc($r['nama_guru_wali']) ?></td>
            <td style="text-align:center;"><?= $r['jumlah_siswa_asuh'] ?></td>
            <td style="text-align:center;"><?= $r['jumlah_pembinaan'] ?></td>
        </tr>
        <?php endforeach; ?>

        <?php if (empty($rekap)): ?>
        <tr>
            <td colspan="4" style="text-align:center;">Tidak ada data.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<table class="footer">
    <tr>
        <td style="width:60%;"></td>
        <td style="text-align:center;">
            <?= esc($sekolah['kabupaten'] ?? '') ?>, <?= date('d F Y') ?><br>
            Wakasek Kesiswaan
            <br><br><br><br>
            <strong><?= esc(session()->get('nama')) ?></strong>
        </td>
    </tr>
</table>

</body>
</html>
