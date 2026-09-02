<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5>Data Kelas</h5>
    </div>

    <div class="card-body">

        <a href="<?= base_url('kelas/tambah') ?>"
           class="btn btn-primary mb-3">

            Tambah Kelas

        </a>

        <table id="tableData"
               class="table table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Jurusan</th>
                    <th>Guru Mengajar</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; ?>
            <?php foreach($kelas as $k): ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td><?= $k['nama_kelas'] ?></td>

                    <td><?= esc($k['jurusan'] ?? '-') ?: '-' ?></td>

                    <td>

                        <?php if (!empty($k['daftar_guru'])): ?>

                            <?php foreach ($k['daftar_guru'] as $namaGuru): ?>
                                <span class="badge bg-light text-dark border me-1 mb-1">
                                    <?= esc($namaGuru) ?>
                                </span>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <span class="text-muted small">Belum ada jadwal</span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <a href="<?= base_url('kelas/edit/'.$k['id']) ?>"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <a href="<?= base_url('kelas/hapus/'.$k['id']) ?>"
                           class="btn btn-danger btn-sm">

                            Hapus

                        </a>

                    </td>

                </tr>

            <?php endforeach ?>

            </tbody>

        </table>

    </div>

</div>
</div>
<?= view('template/footer') ?>