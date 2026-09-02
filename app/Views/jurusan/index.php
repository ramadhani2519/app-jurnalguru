<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5>Data Jurusan</h5>
    </div>

    <div class="card-body">

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <a href="<?= base_url('jurusan/tambah') ?>"
           class="btn btn-primary mb-3">

            Tambah Jurusan

        </a>

        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th width="60">No</th>
                    <th>Nama Jurusan</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($jurusan as $j): ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td><?= esc($j['nama_jurusan']) ?></td>

                    <td>

                        <a href="<?= base_url('jurusan/edit/'.$j['id']) ?>"
                           class="btn btn-warning btn-sm">

                            <i class="bi bi-pencil-square"></i>
                            Edit

                        </a>

                        <a href="<?= base_url('jurusan/hapus/'.$j['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus jurusan ini?')">

                            <i class="bi bi-trash"></i>
                            Hapus

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

            <?php if (empty($jurusan)): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Belum ada data jurusan.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
</div>

<?= view('template/footer') ?>
