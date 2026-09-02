<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Tambah Ketua Kompetensi Keahlian
    </div>

    <div class="card-body">

        <form action="<?= base_url('ketua-kompetensi/store') ?>" method="post">

            <div class="mb-3">
                <label>Nama Kompetensi Keahlian</label>
                <input type="text"
                       name="nama_kompetensi"
                       class="form-control"
                       placeholder="Contoh: Teknik Komputer dan Jaringan"
                       required>
            </div>

            <div class="mb-3">
                <label>Nama Ketua</label>
                <input type="text" name="nama_ketua" class="form-control" required>
            </div>

            <button class="btn btn-primary">Simpan</button>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
