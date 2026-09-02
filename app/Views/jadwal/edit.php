<?= view('template/header') ?>

<div class="container py-4">

<form action="<?= base_url('jadwal/update/'.$jadwal['id']) ?>" method="post">

<?= csrf_field() ?>

<div class="card shadow border-0">

<div class="card-header bg-warning">

<h4 class="mb-0">

<i class="bi bi-pencil-square"></i>

Edit Jadwal Pelajaran

</h4>

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

<div class="col-md-4 mb-3">

<label>Kelas</label>

<select name="kelas" class="form-select">

<?php foreach($kelas as $k): ?>

<option value="<?= $k['id'] ?>"

<?= $jadwal['kelas_id']==$k['id']?'selected':'' ?>>

<?= $k['nama_kelas'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label>Hari</label>

<select name="hari" class="form-select">

<?php foreach($hari as $h): ?>

<option value="<?= $h['id'] ?>"

<?= $jadwal['hari_id']==$h['id']?'selected':'' ?>>

<?= $h['nama_hari'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label>Jam</label>

<select name="jam" class="form-select">

<?php foreach($jam as $j): ?>

<option value="<?= $j['id'] ?>"

<?= $jadwal['jam_id']==$j['id']?'selected':'' ?>>

Jam <?= $j['jam_ke'] ?>

(<?= substr($j['jam_mulai'],0,5) ?>

-

<?= substr($j['jam_selesai'],0,5) ?>)

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Mata Pelajaran</label>

<select name="mapel" class="form-select">

<?php foreach($mapel as $m): ?>

<option value="<?= $m['id'] ?>"

<?= $jadwal['mapel_id']==$m['id']?'selected':'' ?>>

<?= $m['nama_mapel'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label>Guru</label>

<select name="guru" class="form-select">

<?php foreach($guru as $g): ?>

<option value="<?= $g['id'] ?>"

<?= $jadwal['guru_id']==$g['id']?'selected':'' ?>>

<?= $g['nama'] ?>

</option>

<?php endforeach ?>

</select>

</div>

<div class="col-md-12 mb-3">

<label>Ruangan</label>

<select name="ruangan" class="form-select">

<option value="">Tidak Menggunakan Ruangan</option>

<?php foreach($ruangan as $r): ?>

<option value="<?= $r['id'] ?>"

<?= $jadwal['ruangan_id']==$r['id']?'selected':'' ?>>

<?= $r['nama_ruang'] ?>

</option>

<?php endforeach ?>

</select>

</div>

</div>

</div>

<div class="card-footer d-flex justify-content-between">

<a href="<?= base_url('jadwal') ?>" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

<button class="btn btn-warning">

<i class="bi bi-check-circle"></i>

Update Jadwal

</button>

</div>

</div>

</form>

</div>

<?= view('template/footer') ?>