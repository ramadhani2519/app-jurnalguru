<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Tambah Jenis Pelanggaran
    </div>

    <div class="card-body">

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('jenis-pelanggaran/simpan') ?>" method="post">

            <div class="mb-3">
                <label>Nama Jenis Pelanggaran</label>
                <input type="text"
                       name="nama_pelanggaran"
                       class="form-control"
                       placeholder="mis. Terlambat masuk sekolah"
                       value="<?= old('nama_pelanggaran') ?>"
                       required>
            </div>

            <button class="btn btn-success">Simpan</button>

            <a href="<?= base_url('jenis-pelanggaran') ?>" class="btn btn-secondary">Batal</a>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
