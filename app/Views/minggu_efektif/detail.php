<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Minggu Efektif — <?= esc($kelas['nama_kelas']) ?></h5>
            <span class="text-muted small">
                <?= esc($tahunAktif['nama_tahun'] ?? '-') ?> &middot; Semester <?= esc($semesterAktif['nama_semester'] ?? '-') ?>
            </span>
        </div>
        <a href="<?= base_url('minggu-efektif') ?>" class="btn btn-outline-secondary btn-sm">
            &larr; Kembali
        </a>
    </div>

    <div class="card-body">

        <a href="<?= base_url('minggu-efektif/create/' . $kelas['id']) ?>"
           class="btn btn-primary mb-3">
            + Tambah Bulan
        </a>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Jumlah Minggu</th>
                    <th>Minggu Tidak Efektif</th>
                    <th>Minggu Efektif</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($items as $i): ?>

                <?php $efektif = max(0, (int) $i['jumlah_minggu'] - (int) $i['minggu_tidak_efektif']); ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($i['bulan']) ?></td>
                    <td><?= $i['jumlah_minggu'] ?></td>
                    <td><?= $i['minggu_tidak_efektif'] ?></td>
                    <td><strong><?= $efektif ?></strong></td>
                    <td><?= esc($i['keterangan']) ?></td>
                    <td>
                        <a href="<?= base_url('minggu-efektif/edit/' . $i['id']) ?>"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>
                        <a href="<?= base_url('minggu-efektif/delete/' . $i['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Hapus data bulan <?= esc($i['bulan']) ?>?')">
                            Hapus
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Belum ada data. Klik "Tambah Bulan" untuk mulai mengisi.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

            <?php if (!empty($items)): ?>
            <tfoot>
                <tr class="table-light">
                    <th colspan="2">Total</th>
                    <th><?= $totalMinggu ?></th>
                    <th><?= $totalTidakEfektif ?></th>
                    <th><?= $totalEfektif ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
            <?php endif; ?>

        </table>

    </div>

</div>
</div>
<?= view('template/footer') ?>
