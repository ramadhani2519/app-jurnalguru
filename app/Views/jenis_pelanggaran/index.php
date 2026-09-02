<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Jenis Pelanggaran</h5>
        <a href="<?= base_url('jenis-pelanggaran/tambah') ?>" class="btn btn-primary btn-sm">
            Tambah Jenis Pelanggaran
        </a>
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

        <p class="text-muted">
            Daftar ini muncul sebagai pilihan dropdown "Uraian" di form Input/Edit Pelanggaran Siswa.
            Guru tetap bisa memilih "Lainnya" untuk mengetik uraian sendiri kalau tidak ada di daftar ini.
        </p>

        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th width="60">No</th>
                    <th>Nama Jenis Pelanggaran</th>
                    <th width="160">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($jenisPelanggaran as $j): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($j['nama_pelanggaran']) ?></td>
                    <td>

                        <a href="<?= base_url('jenis-pelanggaran/edit/'.$j['id']) ?>"
                           class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                            Edit
                        </a>

                        <a href="<?= base_url('jenis-pelanggaran/hapus/'.$j['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                            <i class="bi bi-trash"></i>
                            Hapus
                        </a>

                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($jenisPelanggaran)): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Belum ada data jenis pelanggaran.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
</div>

<?= view('template/footer') ?>
