<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5 class="mb-0">Realisasi Mengajar per Kelas</h5>
    </div>

    <div class="card-body">

        <p class="text-muted">
            Pilih kelas untuk melihat rekap realisasi mengajar tiap guru per minggu dalam sebulan.
        </p>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($daftarKelas as $k): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($k['nama_kelas']) ?></td>
                    <td>
                        <a href="<?= base_url('realisasi-mengajar/laporan/' . $k['id']) ?>"
                           class="btn btn-primary btn-sm">
                            Lihat Laporan
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
