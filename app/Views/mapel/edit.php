<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header bg-warning">
        <h5 class="mb-0">
            Edit Mata Pelajaran
        </h5>
    </div>

    <div class="card-body">

        <form action="<?= base_url('mapel/update/'.$mapel['id']) ?>"
              method="post">

            <?= csrf_field() ?>

            <div class="mb-3">

                <label class="form-label">
                    Kode Mata Pelajaran
                </label>

                <input type="text"
                       name="kode_mapel"
                       class="form-control"
                       value="<?= $mapel['kode_mapel'] ?>"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nama Mata Pelajaran
                </label>

                <input type="text"
                       name="nama_mapel"
                       class="form-control"
                       value="<?= $mapel['nama_mapel'] ?>"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-success">

                <i class="bi bi-check-circle"></i>
                Update

            </button>

            <a href="<?= base_url('mapel') ?>"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>
</div>
<?= view('template/footer') ?>