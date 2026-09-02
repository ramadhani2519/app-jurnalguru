<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">

Tambah Siswa

</div>

<div class="card-body">

<form action="<?= base_url('siswa/store') ?>"
method="post">

<div class="row">

<div class="col-md-6 mb-3">

<label>NIS</label>

<input type="text"
name="nis"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Nama Siswa</label>

<input type="text"
name="nama_siswa"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Kelas</label>

<select name="kelas_id"
class="form-select">

<?php foreach($kelas as $k): ?>

<option value="<?= $k['id'] ?>">
<?= $k['nama_kelas'] ?>
</option>

<?php endforeach ?>

</select>

</div>

</div>

<button class="btn btn-primary">

Simpan

</button>

</form>

</div>

</div>

</div>
<?= view('template/footer') ?>