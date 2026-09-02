<?= view('template/header') ?>

<div class="container-fluid py-4">

    <h3 class="fw-bold mb-1">
        <i class="bi bi-shield-fill-exclamation text-primary"></i>
        Siswa Perlu Pembinaan (Wali Kelas)
    </h3>
    <p class="text-muted">
        Siswa di kelas Anda yang sudah pernah dibina Guru Wali, tapi melanggar lagi
        dan sekarang menjadi tanggung jawab Wali Kelas untuk ditindaklanjuti.
    </p>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i>
        <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif ?>

    <!-- Notifikasi: siswa eskalasi dari Guru Wali yang perlu tindakan -->

    <?php if (!empty($siswaPerluTindakan)): ?>
    <div class="alert alert-warning shadow-sm">

        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-bell-fill fs-4"></i>
            <strong>
                Ada <?= count($siswaPerluTindakan) ?> siswa yang memerlukan tindakan pembinaan Wali Kelas
                (sudah dibina Guru Wali, lalu melanggar lagi <?= $ambangPelanggaran ?>x atau lebih).
            </strong>
        </div>

        <ul class="list-group list-group-flush">
            <?php foreach ($siswaPerluTindakan as $s): ?>
            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong><?= esc($s['nama_siswa']) ?></strong>
                    <span class="text-muted">(<?= esc($s['nis']) ?> — <?= esc($s['nama_kelas']) ?>)</span>
                    <span class="badge bg-danger ms-2"><?= $s['jumlah_pelanggaran'] ?> pelanggaran</span>
                    <span class="badge bg-secondary"><?= $s['jumlah_pembinaan'] ?> sudah ditindaklanjuti</span>
                </div>
                <a href="<?= base_url('wali-kelas-binaan/pembinaan/tambah/' . $s['id']) ?>" class="btn btn-sm btn-danger">
                    <i class="bi bi-clipboard-heart"></i> Catat Pembinaan
                </a>
            </li>
            <?php endforeach ?>
        </ul>

    </div>
    <?php endif ?>

    <div class="row g-4">

        <div class="col-lg-6">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white">
                    Daftar Siswa di Kelas Anda (<?= count($daftarSiswa) ?>)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Pelanggaran</th>
                                <th>Pembinaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarSiswa as $s): ?>
                            <tr>
                                <td><?= esc($s['nis']) ?></td>
                                <td><?= esc($s['nama_siswa']) ?></td>
                                <td><?= esc($s['nama_kelas']) ?></td>
                                <td class="text-center"><?= $s['jumlah_pelanggaran'] ?></td>
                                <td class="text-center"><?= $s['jumlah_pembinaan'] ?></td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($daftarSiswa)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada siswa di kelas yang Anda walikan.
                                </td>
                            </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-danger text-white">
                    Riwayat Pembinaan oleh Wali Kelas
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayatPembinaan as $r): ?>
                            <tr>
                                <td><?= esc($r['tanggal']) ?></td>
                                <td><?= esc($r['nama_siswa']) ?></td>
                                <td><?= esc($r['tindak_lanjut']) ?></td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($riwayatPembinaan)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Belum ada catatan pembinaan.
                                </td>
                            </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<?= view('template/footer') ?>
