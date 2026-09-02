<?= view('template/header') ?>

<style>
.tabel-realisasi th, .tabel-realisasi td{
    text-align:center;
    vertical-align:middle;
}
.tabel-realisasi td.nama-mapel, .tabel-realisasi td.nama-guru{
    text-align:left;
}
.sel-lengkap{
    color:#198754;
    font-weight:600;
}
.sel-kurang{
    color:#dc3545;
    font-weight:600;
}
.sel-kosong{
    color:#adb5bd;
}
</style>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0">Realisasi Mengajar — <?= esc($kelas['nama_kelas']) ?></h5>
            <span class="text-muted small"><?= esc($namaBulan) ?></span>
        </div>

        <div class="d-flex align-items-center gap-2">

            <form method="get" class="d-flex gap-2">
                <input type="month" name="bulan" class="form-control form-control-sm"
                       value="<?= esc($bulanInput) ?>"
                       onchange="this.form.submit()">
            </form>

            <a href="<?= base_url('realisasi-mengajar') ?>" class="btn btn-outline-secondary btn-sm">
                &larr; Kembali
            </a>

        </div>

    </div>

    <div class="card-body">

        <p class="text-muted small mb-3">
            Format tiap sel: <strong>jumlah pertemuan terisi jurnal (Masuk) / jumlah pertemuan terjadwal</strong> pada minggu itu.
            Kalau ada selisih (terjadwal tapi tidak diisi jurnal), berarti guru tidak mengajar pada pertemuan itu.
        </p>

        <div class="table-responsive">

        <table class="table table-bordered tabel-realisasi">

            <thead class="table-light">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Mata Pelajaran</th>
                    <th rowspan="2">Nama Pengajar</th>
                    <th colspan="<?= count($blokMinggu) ?>">Realisasi Mengajar Minggu Ke-</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <?php foreach ($blokMinggu as $label): ?>
                        <th><?= $label ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($laporan as $baris): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td class="nama-mapel"><?= esc($baris['nama_mapel']) ?></td>
                    <td class="nama-guru"><?= esc($baris['nama_guru']) ?></td>

                    <?php foreach ($blokMinggu as $label): ?>
                        <?php
                            $m = $baris['minggu'][$label];
                        ?>
                        <td>
                            <?php if ($m['jadwal'] == 0): ?>
                                <span class="sel-kosong">-</span>
                            <?php elseif ($m['realisasi'] == $m['jadwal']): ?>
                                <span class="sel-lengkap"><?= $m['realisasi'] ?>/<?= $m['jadwal'] ?></span>
                            <?php else: ?>
                                <span class="sel-kurang"><?= $m['realisasi'] ?>/<?= $m['jadwal'] ?></span>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>

                    <td><strong><?= $baris['totalRealisasi'] ?>/<?= $baris['totalJadwal'] ?></strong></td>
                    <td class="text-start"><?= esc($baris['keterangan']) ?></td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="<?= 5 + count($blokMinggu) ?>" class="text-center text-muted">
                        Belum ada jadwal pelajaran untuk kelas ini pada tahun pelajaran / semester aktif.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>

        </div>

        <div class="small text-muted mt-2">
            <span class="sel-lengkap">Hijau</span> = semua pertemuan terjadwal sudah diisi jurnal &middot;
            <span class="sel-kurang">Merah</span> = ada pertemuan yang belum diisi (tidak mengajar) &middot;
            <span class="sel-kosong">-</span> = tidak ada jadwal minggu itu
        </div>

    </div>

</div>
</div>
<?= view('template/footer') ?>
