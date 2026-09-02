<?= view('template/header') ?>


<div class="container py-4">

<div class="card shadow">

<div class="card-header">
Tambah Jam Pelajaran
</div>

<div class="card-body">

<form action="<?= base_url('jam/save') ?>" method="post">

<?= csrf_field() ?>

<div class="row">

<div class="col-md-4 mb-3">

<label>Kode Jam</label>

<input type="text" name="kode_jam" class="form-control" required>

</div>

<div class="col-md-2 mb-3">

<label>Jam Ke</label>

<input type="number" name="jam_ke" class="form-control" required>

</div>

<div class="col-md-3 mb-3">

<label>Jam Mulai</label>

<input type="time" name="jam_mulai" class="form-control" required>

</div>

<div class="col-md-3 mb-3">

<label>Jam Selesai</label>

<input type="time" name="jam_selesai" class="form-control" required>

</div>

<div class="col-md-3">

<div class="form-check">

<input class="form-check-input" type="checkbox" name="istirahat" value="1">

<label class="form-check-label">

Istirahat

</label>

</div>

</div>

<div class="col-md-3">

<div class="form-check">

<input class="form-check-input" type="checkbox" name="aktif_jumat" value="1">

<label class="form-check-label">

Aktif Hari Jumat

</label>

</div>

</div>

</div>

<button class="btn btn-success">
Simpan
</button>

<a href="<?= base_url('jam') ?>" class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</div>

</div>

<?= view('template/footer') ?>