<?= view('template/header') ?>

<div class="container py-4">
<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success">
    <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>
<div class="card shadow border-0">

<div class="card-header bg-primary text-white d-flex justify-content-between">

<h5 class="mb-0">
Data Siswa
</h5>

<div>

<a href="<?= base_url('siswa/create') ?>"
class="btn btn-light btn-sm">

Tambah Siswa

</a>

<button class="btn btn-success btn-sm"
data-bs-toggle="modal"
data-bs-target="#modalImport">

Import Excel

</button>

</div>

</div>

<div class="card-body">

<table id="datatable"
class="table table-bordered table-striped">

<thead>

<tr>
<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>Kelas</th>
<th>Aksi</th>
</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($siswa as $s): ?>

<tr>

<td><?= $no++ ?></td>
<td><?= $s['nis'] ?></td>
<td><?= $s['nama_siswa'] ?></td>
<td><?= $s['nama_kelas'] ?></td>

<td>

<a href="<?= base_url('siswa/edit/'.$s['id']) ?>"
class="btn btn-warning btn-sm">

Edit

</a>

<form action="<?= base_url('siswa/delete/'.$s['id']) ?>" method="post" style="display:inline">

<?= csrf_field() ?>

<button onclick="return confirm('Hapus data?')" class="btn btn-danger btn-sm">

Hapus

</button>

</form>

</td>

</tr>

<?php endforeach ?>

</tbody>

</table>

</div>

</div>

</div>

<?= view('template/footer') ?>

<?= view('siswa/modal_import') ?>