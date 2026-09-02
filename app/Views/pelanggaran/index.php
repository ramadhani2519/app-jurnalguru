<?= view('template/header') ?>

<div class="container py-4">

<div class="row mb-4">

    <div class="col-md-8">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-journal-bookmark-fill text-primary"></i>
            Pelanggaran Siswa
        </h3>
    </div>

    <div class="col-md-4 text-md-end mt-3 mt-md-0">

        <a href="<?= base_url('pelanggaran/create') ?>"
           class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>
            Tambah Data

        </a>

    </div>

</div>
<div class="card shadow border-0">


<div class="card-header bg-primary text-white">
    <h5 class="mb-0">
📋 Catatan Pelanggaran Siswa
</h5>

</div>

<div class="card-body">
<form method="get" action="<?= base_url('pelanggaran') ?>">
<div class="row">

    <div class="col-md-2">
        <label>Tanggal Awal</label>
        <input type="date" name="tgl1" value="<?= $tgl1 ?>" class="form-control">
    </div>

    <div class="col-md-2">
        <label>Tanggal Akhir</label>
        <input type="date" name="tgl2" value="<?= $tgl2 ?>" class="form-control">
    </div>

    <div class="col-md-2">
        <label>Kelas</label>
        <select name="kelas_id" class="form-select">
            <option value="">Semua Kelas</option>
            <?php foreach($kelas as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($kelas_id==$k['id'])?'selected':'' ?>>
                <?= $k['nama_kelas'] ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Siswa</label>
        <select name="siswa_id" class="form-select">
            <option value="">Semua Siswa</option>
            <?php foreach($siswa as $s): ?>
            <option value="<?= $s['id'] ?>" <?= ($siswa_id==$s['id'])?'selected':'' ?>>
                <?= $s['nama_siswa'] ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-md-1 d-grid">
        <label>&nbsp;</label>
        <button class="btn btn-primary">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>
    <div class="col-md-2 d-grid">
        <label>&nbsp;</label>
        <a href="<?= base_url('pelanggaran/cetakPdf?'.http_build_query([
    'tgl1'=>$tgl1,
    'tgl2'=>$tgl2,
    'kelas_id'=>$kelas_id,
    'siswa_id'=>$siswa_id
    ])) ?>"
    class="btn btn-danger"
    target="_blank">

    <i class="fas fa-file-pdf"></i>
    Cetak PDF

    </a>
    </div>
    <div class="col-md-2 d-grid">
        <label>&nbsp;</label>
        <a href="<?= base_url('pelanggaran/export-excel?'.http_build_query([
    'tgl1'=>$tgl1,
    'tgl2'=>$tgl2,
    'kelas_id'=>$kelas_id,
    'siswa_id'=>$siswa_id
    ])) ?>"
    class="btn btn-success"
    target="_blank">

    <i class="bi bi-file-earmark-excel"></i>
    Unduh Excel

    </a>
    </div>
</div>
</form>


<table class="table table-bordered table-striped mt-3">

<thead>

<tr>
<th>No</th>
<th>Tanggal</th>
<th>Kelas</th>
<th>Nama Siswa</th>
<th>Uraian</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($pelanggaran as $row): ?>

<tr>

<td><?= $no++ ?></td>

<td><?= date('d-m-Y',strtotime($row['tanggal'])) ?></td>

<td><?= $row['nama_kelas'] ?></td>

<td><?= $row['nama_siswa'] ?></td>

<td><?= $row['uraian_pelanggaran'] ?></td>

<td><?= $row['keterangan'] ?></td>

<td width="180">

<a href="<?= base_url('pelanggaran/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">

Edit

</a>

<a href="<?= base_url('pelanggaran/cetak/'.$row['id']) ?>"
        target="_blank"
        class="btn btn-info btn-sm">
        <i class="bi bi-print"></i> Cetak
    </a>

<a href="<?= base_url('pelanggaran/delete/'.$row['id']) ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">

Hapus

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>
</div>

<?= view('template/footer') ?>