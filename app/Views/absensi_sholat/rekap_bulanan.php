<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow-sm">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">
            <i class="bi bi-file-earmark-excel"></i>
            Rekap Bulanan Absensi Sholat (Excel)
        </h5>

    </div>

<div class="card-body">

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <i class="bi bi-x-circle"></i>
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif ?>

<p class="text-muted">
    Rekap ini menampilkan jumlah Sholat/Tidak Sholat per siswa,
    dipecah per waktu sholat (Dhuha, Zuhur, Ashar), untuk 1 bulan.
</p>

<form action="<?= base_url('absensi-sholat/export-rekap-bulanan') ?>" method="get">

<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label">Kelas</label>

        <select name="kelas_id" class="form-select" required>

            <option value="">- Pilih Kelas -</option>

            <?php foreach($kelas as $k): ?>

            <option value="<?= $k['id'] ?>">
                <?= esc($k['nama_kelas']) ?>
            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-4">

        <label class="form-label">Bulan</label>

        <input type="month"
               name="periode"
               class="form-control"
               value="<?= date('Y-m') ?>"
               required>

    </div>

    <div class="col-md-4 d-grid">

        <label>&nbsp;</label>

        <button class="btn btn-success">

            <i class="bi bi-download"></i>

            Download Excel

        </button>

    </div>

</div>

</form>
</div>
</div>
</div>

<?= view('template/footer') ?>
