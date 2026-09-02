<?= view('template/header') ?>

<div class="container-fluid py-4">

    <h3 class="fw-bold mb-1">
        <i class="bi bi-people-fill text-primary"></i>
        Siswa Asuh Saya
    </h3>
    <p class="text-muted">Daftar siswa yang menjadi tanggung jawab bimbingan Anda sebagai Guru Wali.</p>

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

    <!-- Notifikasi: siswa yang perlu tindakan pembinaan -->

    <?php if (!empty($siswaPerluTindakan)): ?>
    <div class="alert alert-warning shadow-sm">

        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-bell-fill fs-4"></i>
            <strong>
                Ada <?= count($siswaPerluTindakan) ?> siswa asuh yang memerlukan tindakan pembinaan
                (tercatat <?= $ambangPelanggaran ?>x atau lebih pelanggaran yang belum ditindaklanjuti).
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
                <a href="<?= base_url('guru-wali/pembinaan/tambah/' . $s['siswa_id']) ?>" class="btn btn-sm btn-danger">
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
                    Daftar Siswa Asuh (<?= count($daftarSiswa) ?>)
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
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarSiswa as $s): ?>
                            <tr class="<?= !empty($s['perlu_tindakan']) ? 'table-warning' : '' ?>">
                                <td><?= esc($s['nis']) ?></td>
                                <td><?= esc($s['nama_siswa']) ?></td>
                                <td><?= esc($s['nama_kelas']) ?></td>
                                <td class="text-center"><?= $s['jumlah_pelanggaran'] ?? 0 ?></td>
                                <td>
                                    <?php if (!empty($s['perlu_tindakan'])): ?>
                                    <span class="badge bg-warning text-dark">Perlu Pembinaan</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">Aman</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php if (!empty($s['perlu_tindakan'])): ?>
                                    <a href="<?= base_url('guru-wali/pembinaan/tambah/' . $s['siswa_id']) ?>" class="btn btn-sm btn-danger">
                                        <i class="bi bi-clipboard-heart"></i>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($daftarSiswa)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada siswa yang dibagikan ke Anda.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-danger text-white">
                    Riwayat Pelanggaran Siswa Asuh
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Uraian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembinaan as $p): ?>
                            <tr>
                                <td><?= esc($p['tanggal']) ?></td>
                                <td><?= esc($p['nama_siswa']) ?></td>
                                <td><?= esc($p['uraian_pelanggaran']) ?></td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($pembinaan)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    Belum ada catatan pelanggaran untuk siswa asuh Anda.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-clipboard-heart"></i>
                    Riwayat Tindak Lanjut Pembinaan (Sudah Ditindaklanjuti)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Tindak Lanjut</th>
                                <th width="110">Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayatPembinaan as $r): ?>
                            <tr>
                                <td class="text-nowrap"><?= date('d-m-Y', strtotime($r['tanggal'])) ?></td>
                                <td><?= esc($r['nama_siswa']) ?></td>
                                <td><?= esc($r['nama_kelas']) ?></td>
                                <td><?= nl2br(esc($r['tindak_lanjut'])) ?></td>
                                <td class="text-center">
                                    <?php if (!empty($r['foto'])): ?>
                                    <a href="<?= base_url('assets/img/pembinaan/' . $r['foto']) ?>" target="_blank">
                                        <img src="<?= base_url('assets/img/pembinaan/' . $r['foto']) ?>"
                                             style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($riwayatPembinaan)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada tindak lanjut pembinaan yang dicatat.
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
