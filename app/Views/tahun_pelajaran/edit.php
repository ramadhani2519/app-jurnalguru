<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header bg-warning">
        <h5 class="mb-0">
            Edit Tahun Pelajaran
        </h5>
    </div>

    <div class="card-body">

        <form action="<?= base_url('tahun/update/'.$tahun['id']) ?>"
              method="post">

            <?= csrf_field() ?>

            <div class="mb-3">

                <label class="form-label">
                    Tahun Pelajaran
                </label>

                <input type="text"
                       name="tahun"
                       class="form-control"
                       value="<?= $tahun['tahun'] ?>"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-success">

                <i class="bi bi-check-circle"></i>
                Update

            </button>

            <a href="<?= base_url('tahun') ?>"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>
</div>
<?= view('template/footer') ?>