<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header bg-warning">
        <h5 class="mb-0">
            Edit Semester
        </h5>
    </div>

    <div class="card-body">

        <form action="<?= base_url('semester/update/'.$semester['id']) ?>"
              method="post">

            <?= csrf_field() ?>

            <div class="mb-3">

                <label class="form-label">
                    Semester
                </label>

                <input type="text"
                       name="semester"
                       class="form-control"
                       value="<?= $semester['semester'] ?>"
                       required>

            </div>

            <button type="submit"
                    class="btn btn-success">

                <i class="bi bi-check-circle"></i>
                Update

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