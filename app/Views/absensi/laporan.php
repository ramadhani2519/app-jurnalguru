<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow-sm">

    <div class="card-header bg-danger text-white">

        <h5 class="mb-0">
            <i class="bi bi-printer"></i>
            Cetak Rekap Absensi
        </h5>

    </div>

<div class="card-body">

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <i class="bi bi-x-circle"></i>
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif ?>

<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i>
    <?= session()->getFlashdata('success') ?>
</div>
<?php endif ?>

<form action="<?= base_url('absensi/cetak-rekap') ?>" method="get" target="_blank">

<div class="row g-3">

    <div class="col-md-3">
        <label class="form-label">Tanggal Awal</label>

        <input type="date"
               name="tanggal_awal"
               class="form-control"
               value="<?= date('Y-m-01') ?>"
               required>

    </div>

    <div class="col-md-3">

        <label class="form-label">Tanggal Akhir</label>

        <input type="date"
               name="tanggal_akhir"
               class="form-control"
               value="<?= date('Y-m-d') ?>"
               required>

    </div>

    <div class="col-md-3">

        <label class="form-label">Kelas</label>

        <select name="kelas_id"
                class="form-select"
                required>

            <option value="">- Pilih Kelas -</option>

            <?php foreach($kelas as $k): ?>

            <option value="<?= $k['id'] ?>">
                <?= $k['nama_kelas'] ?>
            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-3 d-grid">

        <label>&nbsp;</label>

        <button class="btn btn-primary">

            <i class="bi bi-file-pdf"></i>

            Cetak Rekap

        </button>

    </div>

</div>

</form>
<hr>
</div>
</div>

<?= view('template/footer') ?>
