<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Minggu Efektif Pembelajaran</h5>
        <span class="text-muted small">
            <?= esc($tahunAktif['nama_tahun'] ?? '-') ?> &middot; Semester <?= esc($semesterAktif['nama_semester'] ?? '-') ?>
        </span>
    </div>

    <div class="card-body">

        <p class="text-muted">
            Pilih kelas untuk mengisi atau melihat jumlah minggu efektif per bulan.
        </p>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Jumlah Bulan Terisi</th>
                    <th>Total Minggu Efektif</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($daftarKelas as $k): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($k['nama_kelas']) ?></td>
                    <td><?= $k['jumlah_bulan'] ?> bulan</td>
                    <td>
                        <span class="badge bg-primary"><?= $k['total_efektif'] ?> minggu</span>
                    </td>
                    <td>
                        <a href="<?= base_url('minggu-efektif/detail/' . $k['id']) ?>"
                           class="btn btn-primary btn-sm">
                            Kelola
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
</div>
<?= view('template/footer') ?>
