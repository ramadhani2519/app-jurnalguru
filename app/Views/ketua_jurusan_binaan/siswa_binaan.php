<?= view('template/header') ?>

<div class="container-fluid py-4">

    <h3 class="fw-bold mb-1">
        <i class="bi bi-mortarboard-fill text-primary"></i>
        Monitoring Pembinaan Siswa <?= !empty($jurusan) ? '(' . esc($jurusan) . ')' : '(Ketua Jurusan)' ?>
    </h3>
    <p class="text-muted">
        Semua siswa di jurusan Anda yang punya catatan pelanggaran, lengkap dengan
        sejauh mana tindak lanjutnya (Guru Wali, Wali Kelas, Ketua Jurusan).
    </p>

    <?php if (!empty($jurusanBelumDiset)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Jurusan yang Anda ampu belum diatur oleh admin. Silakan hubungi admin untuk
        mengisi "Jurusan yang Diampu" di menu Data Pengguna, pada jabatan Ketua Jurusan Anda.
    </div>
    <?php else: ?>

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

    <!-- Notifikasi: siswa eskalasi dari Wali Kelas yang perlu tindakan -->

    <?php if (!empty($siswaPerluTindakan)): ?>
    <div class="alert alert-warning shadow-sm">

        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-bell-fill fs-4"></i>
            <strong>
                Ada <?= count($siswaPerluTindakan) ?> siswa yang memerlukan tindakan pembinaan Ketua Jurusan
                (sudah dibina Guru Wali & Wali Kelas, lalu melanggar lagi <?= $ambangPelanggaran ?>x atau lebih).
            </strong>
        </div>

        <ul class="list-group list-group-flush">
            <?php foreach ($siswaPerluTindakan as $s): ?>
            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong><?= esc($s['nama_siswa']) ?></strong>
                    <span class="text-muted">(<?= esc($s['nis']) ?> — <?= esc($s['nama_kelas']) ?>)</span>
                    <span class="badge bg-danger ms-2"><?= $s['jumlah_pelanggaran'] ?> pelanggaran</span>
                </div>
                <a href="<?= base_url('ketua-jurusan-binaan/pembinaan/tambah/' . $s['id']) ?>" class="btn btn-sm btn-danger">
                    <i class="bi bi-clipboard-heart"></i> Catat Pembinaan
                </a>
            </li>
            <?php endforeach ?>
        </ul>

    </div>
    <?php endif ?>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white">
                    Monitoring Siswa Bermasalah (<?= count($daftarSiswa) ?>)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th class="text-center">Pelanggaran</th>
                                <th class="text-center">Guru Wali</th>
                                <th class="text-center">Wali Kelas</th>
                                <th class="text-center">Ketua Jurusan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarSiswa as $s): ?>
                            <tr>
                                <td><?= esc($s['nis']) ?></td>
                                <td><?= esc($s['nama_siswa']) ?></td>
                                <td><?= esc($s['nama_kelas']) ?></td>
                                <td class="text-center"><?= $s['jumlah_pelanggaran'] ?></td>
                                <td class="text-center">
                                    <?= $s['sudah_guru_wali'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td class="text-center">
                                    <?= $s['sudah_wali_kelas'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td class="text-center">
                                    <?= $s['sudah_ketua_jurusan'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($s['status_warna']) ?>"><?= esc($s['status_label']) ?></span>
                                    <?php if ($s['perlu_tindakan']): ?>
                                    <a href="<?= base_url('ketua-jurusan-binaan/pembinaan/tambah/' . $s['id']) ?>" class="btn btn-sm btn-danger ms-1">
                                        Catat
                                    </a>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($daftarSiswa)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    Belum ada siswa bermasalah di jurusan ini.
                                </td>
                            </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-danger text-white">
                    Riwayat Pembinaan oleh Ketua Jurusan
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

    <?php endif ?>

</div>

<?= view('template/footer') ?>
