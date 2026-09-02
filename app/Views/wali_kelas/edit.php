<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Edit Wali Kelas
    </div>

    <div class="card-body">

        <form action="<?= base_url('wali-kelas/update/'.$item['id']) ?>" method="post">

            <div class="mb-3">
                <label>Kelas</label>
                <select name="kelas_id" class="form-select" required>
                    <option value="">- Pilih Kelas -</option>
                    <?php foreach($kelas as $k): ?>
                    <option value="<?= $k['id'] ?>"
                        <?= $item['kelas_id']==$k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Nama Wali Kelas</label>
                <input type="text"
                       name="nama_wali"
                       value="<?= esc($item['nama_wali']) ?>"
                       class="form-control"
                       required>
            </div>

            <button class="btn btn-success">Update</button>

        </form>

    </div>

</div>
</div>

<?= view('template/footer') ?>
