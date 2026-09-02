<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Edit Jurusan
    </div>

    <div class="card-body">

        <form action="<?= base_url('jurusan/update/'.$jurusan['id']) ?>" method="post">

            <div class="mb-3">
                <label>Nama Jurusan</label>
                <input type="text"
                       name="nama_jurusan"
                       class="form-control"
                       value="<?= esc($jurusan['nama_jurusan']) ?>"
                       required>
            </div>

            <button class="btn btn-success">Update</button>

            <a href="<?= base_url('jurusan') ?>" class="btn btn-secondary">Batal</a>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
