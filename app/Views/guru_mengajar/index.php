<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Guru Mengajar</h5>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('realisasi-mengajar') ?>" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clipboard-check"></i>
                Realisasi Mengajar
            </a>
            <span class="text-muted small">
                <?= esc($tahunAktif['nama_tahun'] ?? '-') ?> &middot; Semester <?= esc($semesterAktif['nama_semester'] ?? '-') ?>
            </span>
        </div>
    </div>

    <div class="card-body">

        <p class="text-muted">
            Data diambil dari Jadwal Pelajaran yang sudah diinput. Tanggal dihitung otomatis dari hari + minggu yang dipilih.
        </p>

        <form method="get" class="row g-2 mb-3 align-items-center">

            <div class="col-auto">
                <select name="guru_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Guru --</option>
                    <?php foreach ($daftarGuru as $g): ?>
                        <option value="<?= $g['id'] ?>" <?= $guruDipilih == $g['id'] ? 'selected' : '' ?>>
                            <?= esc($g['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-auto">
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($daftarKelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $kelasDipilih == $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-auto">
                <label class="col-form-label small text-muted">Minggu berisi tanggal:</label>
            </div>

            <div class="col-auto">
                <input type="date" name="minggu" class="form-control"
                       value="<?= esc($tanggalAcuan) ?>"
                       onchange="this.form.submit()">
            </div>

            <div class="col-auto text-muted small">
                (<?= date('d M Y', strtotime($seninMinggu)) ?> &ndash; <?= date('d M Y', strtotime($sabtuMinggu)) ?>)
            </div>

            <div class="col-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Status Jurnal --</option>
                    <option value="sudah" <?= $statusFilter === 'sudah' ? 'selected' : '' ?>>Sudah Diisi Saja</option>
                    <option value="belum" <?= $statusFilter === 'belum' ? 'selected' : '' ?>>Belum Diisi Saja</option>
                </select>
            </div>

        </form>

        <?php if (!empty($rekapJam)): ?>
        <div class="mb-3">
            <?php foreach ($rekapJam as $namaGuru => $jumlahJam): ?>
                <?php $terisi = $rekapIsi[$namaGuru] ?? 0; ?>
                <span class="badge <?= $terisi == $jumlahJam ? 'bg-success' : 'bg-warning text-dark' ?> me-1 mb-1">
                    <?= esc($namaGuru) ?> : <?= $terisi ?>/<?= $jumlahJam ?> jurnal terisi minggu ini
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Guru</th>
                    <th>Mata Pelajaran</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Total Jam</th>
                    <th>Jam Ke</th>
                    <th>Waktu</th>
                    <th>Ruangan</th>
                    <th>Status Jurnal</th>
                </tr>
            </thead>

            <tbody>

            <?php $no = 1; ?>
            <?php foreach ($jadwalTabel as $j): ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($j['nama_guru']) ?></td>
                    <td><?= esc($j['nama_mapel']) ?></td>
                    <td><?= esc($j['nama_kelas']) ?></td>
                    <td><?= esc($j['nama_hari']) ?></td>
                    <td><?= date('d-m-Y', strtotime($j['tanggal'])) ?></td>
                    <td><span class="badge bg-info text-dark"><?= $j['total_sesi'] ?> JP</span></td>
                    <td>Jam <?= esc($j['jam_ke_display']) ?></td>
                    <td><?= esc(substr($j['jam_mulai'], 0, 5)) ?> - <?= esc(substr($j['jam_selesai'], 0, 5)) ?></td>
                    <td><?= esc($j['nama_ruang'] ?? '-') ?></td>
                    <td>
                        <?php if ($j['status_jurnal'] === 'Masuk'): ?>
                            <span class="badge bg-success">Sudah Mengajar (<?= $j['sudah_diisi'] ?>/<?= $j['total_sesi'] ?>)</span>
                        <?php elseif ($j['sudah_diisi'] > 0): ?>
                            <span class="badge bg-warning text-dark">Sebagian (<?= $j['sudah_diisi'] ?>/<?= $j['total_sesi'] ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Belum Diisi (0/<?= $j['total_sesi'] ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($jadwalTabel)): ?>
                <tr>
                    <td colspan="11" class="text-center text-muted">
                        Belum ada data jadwal untuk tahun pelajaran / semester aktif.
                    </td>
                </tr>
            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
</div>
<?= view('template/footer') ?>
