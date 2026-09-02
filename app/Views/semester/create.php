<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            Tambah Semester
        </h5>
    </div>

    <div class="card-body">

        <form action="<?= base_url('semester/simpan') ?>"
              method="post">

            <?= csrf_field() ?>

            <div class="mb-3">

                <label class="form-label">
                    Semester
                </label>

                <input type="text"
                       name="tahun"
                       class="form-control"
                       placeholder="Contoh: Ganjil"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-save"></i>
                Simpan

            </button>

            <a href="<?= base_url('semester') ?>"
               class="btn btn-secondary">

                Kembali

            </a>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>