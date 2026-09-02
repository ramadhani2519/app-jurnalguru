<?= view('template/header') ?>

<div class="container py-4">


<div class="card shadow border-0">


<div class="card-header bg-primary text-white">
    <h5 class="mb-0">
📋 Input Catatan Pelanggaran Siswa
</h5>

</div>

<div class="card-body">
<form action="<?= base_url('pelanggaran/save') ?>" method="post">

Tanggal
<input type="date" name="tanggal" class="form-control">

Kelas

<select name="kelas_id" class="form-select">

<?php foreach($kelas as $k): ?>

<option value="<?= $k['id'] ?>">
<?= $k['nama_kelas'] ?>
</option>

<?php endforeach ?>

</select>

Siswa

<select name="siswa_id" class="form-select">

<?php foreach($siswa as $s): ?>

<option value="<?= $s['id'] ?>">
<?= $s['nama_siswa'] ?>
</option>

<?php endforeach ?>

</select>

Uraian

<select name="uraian_pelanggaran" id="uraianPelanggaran" class="form-select">

<option value="">-- Pilih Jenis Pelanggaran --</option>

<?php foreach($jenisPelanggaran as $j): ?>

<option value="<?= esc($j['nama_pelanggaran']) ?>">
<?= esc($j['nama_pelanggaran']) ?>
</option>

<?php endforeach ?>

<option value="__lainnya__">Lainnya (ketik sendiri)</option>

</select>

<div class="mt-2" id="wrapUraianLainnya" style="display:none;">
    <textarea name="uraian_lainnya" class="form-control" placeholder="Ketik uraian pelanggaran..."></textarea>
</div>

<script>
document.getElementById('uraianPelanggaran').addEventListener('change', function(){
    document.getElementById('wrapUraianLainnya').style.display =
        (this.value === '__lainnya__') ? 'block' : 'none';
});
</script>

<br>

<button class="btn btn-success">

Simpan

</button>

</form>

</div>

</div>
</div>

<?= view('template/footer') ?>