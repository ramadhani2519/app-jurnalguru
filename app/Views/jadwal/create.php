<?= view('template/header') ?>

<div class="container py-4">

<form action="<?= base_url('jadwal/store') ?>" method="post">

<?= csrf_field() ?>

<div class="card shadow border-0">

<div class="card-header bg-primary text-white d-flex justify-content-between">

<h4 class="mb-0">
<i class="bi bi-calendar-plus"></i>
Tambah Jadwal Pelajaran
</h4>

<a href="<?= base_url('jadwal') ?>" class="btn btn-light">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

<div class="card-body">

<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">

<?= session()->getFlashdata('error') ?>

</div>

<?php endif ?>

<input type="hidden" name="tahun" value="<?= $tahun['id'] ?>">
<input type="hidden" name="semester" value="<?= $semester['id'] ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Tahun Pelajaran</label>

<input class="form-control"
value="<?= $tahun['tahun'] ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Semester</label>

<input class="form-control"
value="<?= $semester['semester'] ?>"
readonly>

</div>

<div class="col-md-4 mb-3">

<label>Kelas</label>

<select name="kelas"
class="form-select"
required>

<option value="">Pilih Kelas</option>

<?php foreach($kelas as $k): ?>

<option value="<?= $k['id'] ?>">

<?= $k['nama_kelas'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label>Hari</label>

<select name="hari"
class="form-select"
required>

<option value="">Pilih Hari</option>

<?php foreach($hari as $h): ?>

<option value="<?= $h['id'] ?>">

<?= $h['nama_hari'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-2 mb-3">

<label>Jam Mulai</label>

<select name="jam_mulai"
class="form-select"
required>

<option value="">Pilih Jam</option>

<?php foreach($jam as $j): ?>

<option value="<?= $j['id'] ?>">

Jam <?= $j['jam_ke'] ?>

(<?= substr($j['jam_mulai'],0,5) ?>

-

<?= substr($j['jam_selesai'],0,5) ?>)

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-2 mb-3">

<label>Jam Selesai</label>

<select name="jam_selesai"
class="form-select"
required>

<option value="">Pilih Jam</option>

<?php foreach($jam as $j): ?>

<option value="<?= $j['id'] ?>">

Jam <?= $j['jam_ke'] ?>

(<?= substr($j['jam_mulai'],0,5) ?>

-

<?= substr($j['jam_selesai'],0,5) ?>)

</option>

<?php endforeach ?>

</select>

<small class="text-muted">
Kalau guru cuma ngajar 1 jam, pilih jam yang sama di Jam Mulai & Jam Selesai.
</small>

</div>

<div class="col-md-6 mb-3">

<label>Mata Pelajaran</label>

<select name="mapel"
class="form-select"
required>

<option value="">Pilih Mata Pelajaran</option>

<?php foreach($mapel as $m): ?>

<option value="<?= $m['id'] ?>">

<?= $m['nama_mapel'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Guru</label>

<select name="guru"
class="form-select"
required>

<option value="">Pilih Guru</option>

<?php foreach($guru as $g): ?>

<option value="<?= $g['id'] ?>">

<?= $g['nama'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-12 mb-3">

<label>Ruangan</label>

<select name="ruangan"
class="form-select">

<option value="">-- Tidak Menggunakan Ruangan --</option>

<?php foreach($ruangan as $r): ?>

<option value="<?= $r['id'] ?>">

<?= $r['nama_ruang'] ?>

</option>

<?php endforeach ?>

</select>

</div>

</div>

</div>

<div class="card-footer text-end">

<button class="btn btn-primary">

<i class="bi bi-save"></i>

Simpan Jadwal

</button>

</div>

</div>

</form>

</div>

<?= view('template/footer') ?>
