<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Edit Kelas
    </div>

    <div class="card-body">

        <form action="<?= base_url('kelas/update/'.$kelas['id']) ?>"
              method="post">

            <div class="mb-3">

                <label>Nama Kelas</label>

                <input type="text"
                       name="nama_kelas"
                       class="form-control"
                       value="<?= $kelas['nama_kelas'] ?>"
                       required>

            </div>

            <div class="mb-3">

                <label>Jurusan</label>

                <select name="jurusan" class="form-select">

                    <option value="">Pilih Jurusan</option>

                    <?php foreach($jurusanList as $j): ?>

                    <option value="<?= esc($j['nama_jurusan']) ?>"
                        <?= ($kelas['jurusan'] ?? '') == $j['nama_jurusan'] ? 'selected' : '' ?>>
                        <?= esc($j['nama_jurusan']) ?>
                    </option>

                    <?php endforeach; ?>

                </select>

                <small class="text-muted">
                    Dipakai supaya Ketua Jurusan hanya melihat siswa di jurusannya sendiri.
                    Belum ada pilihannya? Tambahkan dulu lewat menu <b>Master Data &rarr; Jurusan</b>.
                </small>

            </div>

            <button class="btn btn-success">

                Update

            </button>

        </form>

    </div>

</div>
</div>
<?= view('template/footer') ?>