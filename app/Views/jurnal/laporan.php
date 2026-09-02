<?= view('template/header') ?>

<div class="container">

<div class="card shadow">

<div class="card-header">
Cetak Laporan Jurnal
</div>

<div class="card-body">

<form action="<?= base_url('jurnal/cetakPdf') ?>" target="_blank">

<div class="row">
<div class="col-md-3">
    <label>Nama Guru</label>

    <?php if ($role_id == 2): ?>

        <select class="form-select" disabled>
            <?php foreach ($user as $u): ?>
                <option selected><?= esc($u['nama']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">

    <?php else: ?>

        <select
            id="guru"
            name="guru_id"
            class="form-select">

            <option value="">Semua Guru</option>

            <?php foreach ($user as $u): ?>
                <option value="<?= $u['id'] ?>"
                    <?= ($guru_id == $u['id']) ? 'selected' : '' ?>>
                    <?= esc($u['nama']) ?>
                </option>
            <?php endforeach; ?>

        </select>

    <?php endif; ?>

</div>

<div class="col-md-2">

<label>Tanggal Awal</label>

<input
type="date"
name="tgl1"
class="form-control">

</div>

<div class="col-md-2">

<label>Tanggal Akhir</label>

<input
type="date"
name="tgl2"
class="form-control">

</div>
<div class="col-md-2">
    <label>Jam ke</label>
<select name="jam_ke" class="form-select">
    <option value="">Semua Jam</option>
    <?php foreach ($jam as $j) : ?>
        <option value="<?= $j['jam_ke']; ?>"
            <?= ($jam == $j['jam_ke']) ? 'selected' : ''; ?>>
            Jam Ke <?= $j['jam_ke']; ?>
        </option>
    <?php endforeach; ?>
</select>
</div>
<div class="col-md-2">

<label>&nbsp;</label>

<button class="btn btn-danger form-control">

<i class="fa fa-file-pdf"></i>

Cetak PDF

</button>

</div>

</div>

</form>

</div>

</div>

<div class="card shadow mt-4">

<div class="card-header">
Cetak Laporan Pembinaan Siswa
</div>

<div class="card-body">

<form action="<?= base_url('jurnal/cetak-pembinaan-pdf') ?>" target="_blank">

<div class="row">

<div class="col-md-3">

<label>Guru yang Membina</label>

<select name="guru_id" class="form-select">
    <option value="">Semua Guru</option>
    <?php foreach ($guruPembinaList as $g): ?>
    <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?></option>
    <?php endforeach; ?>
</select>

</div>

<div class="col-md-3">

<label>Siswa yang Dibina</label>

<select name="siswa_id" class="form-select">
    <option value="">Semua Siswa</option>
    <?php foreach ($siswaList as $s): ?>
    <option value="<?= $s['id'] ?>"><?= esc($s['nama_siswa']) ?></option>
    <?php endforeach; ?>
</select>

</div>

<div class="col-md-2">

<label>Tanggal Awal</label>

<input type="date" name="tgl1" class="form-control">

</div>

<div class="col-md-2">

<label>Tanggal Akhir</label>

<input type="date" name="tgl2" class="form-control">

</div>

<div class="col-md-2">

<label>&nbsp;</label>

<button class="btn btn-danger form-control">
<i class="fa fa-file-pdf"></i>
Cetak PDF
</button>

</div>

</div>

</form>

</div>

</div>

</div>

<?= view('template/footer') ?>