<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow-sm">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">
            <i class="bi bi-file-earmark-excel"></i>
            Rekap Bulanan per Kelas (Excel)
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
    Rekap ini menggabungkan absensi dari <b>semua mata pelajaran</b> dalam 1 bulan,
    dipecah per minggu, untuk 1 kelas.
    <br>
    Belum ada di daftar Wali Kelas / Ketua Jurusan?
    Atur lewat <a href="<?= base_url('user') ?>">Data Pengguna</a> — pilih Role "Guru", lalu centang jabatan tambahannya.
</p>

<form action="<?= base_url('absensi/export-rekap-bulanan') ?>" method="get">

<div class="row g-3">

    <div class="col-md-3">
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

    <div class="col-md-3">

        <label class="form-label">Bulan</label>

        <input type="month"
               name="periode"
               class="form-control"
               value="<?= date('Y-m') ?>"
               required>

    </div>

    <div class="col-md-3">

        <label class="form-label">Wali Kelas</label>

        <select name="wali_kelas" class="form-select">

            <option value="">- Pilih Wali Kelas -</option>

            <?php foreach($waliKelas as $w): ?>

            <option value="<?= esc($w['nama_wali']) ?>">
                <?= esc($w['nama_wali']) ?> (<?= esc($w['nama_kelas']) ?>)
            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-3">

        <label class="form-label">Ketua Kompetensi Keahlian</label>

        <select name="ketua_kompetensi" class="form-select">

            <option value="">- Pilih Ketua Kompetensi -</option>

            <?php foreach($ketuaKompetensi as $k): ?>

            <option value="<?= esc($k['nama_ketua']) ?>">
                <?= esc($k['nama_ketua']) ?>
            </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-3 d-grid">

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
