<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Edit Ketua Kompetensi Keahlian
    </div>

    <div class="card-body">

        <form action="<?= base_url('ketua-kompetensi/update/'.$item['id']) ?>" method="post">

            <div class="mb-3">
                <label>Nama Kompetensi Keahlian</label>
                <input type="text"
                       name="nama_kompetensi"
                       value="<?= esc($item['nama_kompetensi']) ?>"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Nama Ketua</label>
                <input type="text"
                       name="nama_ketua"
                       value="<?= esc($item['nama_ketua']) ?>"
                       class="form-control"
                       required>
            </div>

            <button class="btn btn-success">Update</button>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
