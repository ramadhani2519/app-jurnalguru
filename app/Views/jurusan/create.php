<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Tambah Jurusan
    </div>

    <div class="card-body">

        <form action="<?= base_url('jurusan/simpan') ?>" method="post">

            <div class="mb-3">
                <label>Nama Jurusan</label>
                <input type="text"
                       name="nama_jurusan"
                       class="form-control"
                       placeholder="mis. TKJ, TJKT, TITL"
                       value="<?= old('nama_jurusan') ?>"
                       required>
            </div>

            <button class="btn btn-success">Simpan</button>

            <a href="<?= base_url('jurusan') ?>" class="btn btn-secondary">Batal</a>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
