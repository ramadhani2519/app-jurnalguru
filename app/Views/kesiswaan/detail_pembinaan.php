<?= view('template/header') ?>

<div class="container py-4">

    <a href="<?= base_url('kesiswaan/status-siswa') ?>" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start flex-wrap">

                <div>
                    <h4 class="fw-bold mb-1"><?= esc($siswa['nama_siswa']) ?></h4>
                    <p class="text-muted mb-0">
                        <?= esc($siswa['nama_kelas'] ?? '-') ?>
                        &middot; Jurusan <?= esc($siswa['jurusan'] ?? '-') ?>
                    </p>
                </div>

                <div class="text-end">
                    <span class="badge bg-<?= $statusWarna ?> fs-6">
                        <?= esc($statusLabel) ?>
                    </span>
                    <div class="text-muted small mt-1">
                        Total pelanggaran tercatat: <b><?= $jumlahPelanggaran ?></b>
                    </div>
                    <?php if (!empty($direset)): ?>
                    <div class="text-success small mt-1">
                        <i class="bi bi-check-circle"></i>
                        Sudah dianggap lunas (30 hari tanpa pelanggaran baru) — jenjang pembinaan dimulai dari awal lagi bila melanggar lagi.
                    </div>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history"></i>
                Riwayat Tindak Lanjut Pembinaan
            </h5>
        </div>

        <div class="card-body">

            <?php if (empty($riwayat)): ?>

                <p class="text-muted mb-0">
                    Siswa ini belum pernah mendapat tindak lanjut pembinaan.
                </p>

            <?php else: ?>

                <div class="timeline">

                    <?php foreach ($riwayat as $i => $r): ?>

                    <?php
                        switch ($r['tingkat']) {
                            case 'wali_kelas':
                                $tingkatLabel = 'Wali Kelas';
                                $tingkatWarna = 'warning';
                                $namaPenindak = $r['nama_wali_kelas'] ?? '-';
                                break;
                            case 'ketua_jurusan':
                                $tingkatLabel = 'Ketua Jurusan';
                                $tingkatWarna = 'danger';
                                $namaPenindak = $r['nama_ketua_jurusan'] ?? '-';
                                break;
                            default:
                                $tingkatLabel = 'Guru Wali';
                                $tingkatWarna = 'info';
                                $namaPenindak = $r['nama_guru_wali'] ?? '-';
                        }
                    ?>

                    <div class="d-flex mb-4">

                        <div class="me-3 text-center" style="width:40px;">
                            <span class="badge bg-<?= $tingkatWarna ?> rounded-circle p-2">
                                <?= $i + 1 ?>
                            </span>
                        </div>

                        <div class="flex-grow-1 border-bottom pb-3">

                            <div class="d-flex justify-content-between flex-wrap">
                                <div>
                                    <span class="badge bg-<?= $tingkatWarna ?>"><?= $tingkatLabel ?></span>
                                    <b class="ms-1"><?= esc($namaPenindak) ?></b>
                                </div>
                                <small class="text-muted">
                                    <?= date('d F Y', strtotime($r['tanggal'])) ?>
                                </small>
                            </div>

                            <p class="mb-2 mt-2"><?= nl2br(esc($r['tindak_lanjut'])) ?></p>

                            <?php if (!empty($r['foto'])): ?>
                            <a href="<?= base_url('assets/img/pembinaan/'.$r['foto']) ?>" target="_blank">
                                <img src="<?= base_url('assets/img/pembinaan/'.$r['foto']) ?>"
                                     style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                            </a>
                            <?php endif; ?>

                        </div>

                    </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<?= view('template/footer') ?>
