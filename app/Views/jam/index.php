<?= view('template/header') ?>


<div class="container py-4">

<div class="card shadow">

<div class="card-header d-flex justify-content-between align-items-center">

<h4>Data Jam Pelajaran</h4>

<a href="<?= base_url('jam/create') ?>" class="btn btn-primary">
<i class="bi bi-plus-circle"></i> Tambah
</a>

</div>

<div class="card-body">

<?php if(session()->getFlashdata('success')) : ?>

<div class="alert alert-success">
<?= session()->getFlashdata('success') ?>
</div>

<?php endif; ?>

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>No</th>
<th>Kode</th>
<th>Jam Ke</th>
<th>Mulai</th>
<th>Selesai</th>
<th>Istirahat</th>
<th>Aktif Jumat</th>
<th width="170">Aksi</th>

</tr>

</thead>

<tbody>

<?php $no=1; foreach($jam as $j): ?>

<tr>

<td><?= $no++ ?></td>

<td><?= $j['kode_jam'] ?></td>

<td><?= $j['jam_ke'] ?></td>

<td><?= $j['jam_mulai'] ?></td>

<td><?= $j['jam_selesai'] ?></td>

<td>

<?= $j['istirahat'] ? '<span class="badge bg-warning">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?>

</td>

<td>

<?= $j['aktif_jumat'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak</span>' ?>

</td>

<td>

<a href="<?= base_url('jam/edit/'.$j['id']) ?>" class="btn btn-warning btn-sm">
Edit
</a>

<form action="<?= base_url('jam/delete/'.$j['id']) ?>" method="post" style="display:inline">

<?= csrf_field() ?>

<button onclick="return confirm('Hapus data?')" class="btn btn-danger btn-sm">

Hapus

</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

<?= view('template/footer') ?>