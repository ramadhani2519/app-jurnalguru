<?= view('template/header'); ?>

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body text-white"
            style="background:linear-gradient(135deg,#0d6efd,#4f46e5);">

            <div class="row align-items-center">

                <div class="col-md-2 text-center">

                    <?php if (!empty($sekolah['logo'])) : ?>

                        <img src="<?= base_url('assets/img/'.$sekolah['logo']) ?>"
                            class="img-fluid rounded-circle bg-white p-2 shadow"
                            style="width:110px;height:110px;object-fit:contain;">

                    <?php else : ?>

                        <img src="<?= base_url('assets/img/logo-default.png') ?>"
                            class="img-fluid rounded-circle bg-white p-2 shadow"
                            style="width:110px;height:110px;object-fit:contain;">

                    <?php endif; ?>

                </div>

                <div class="col-md-10">

                    <h2 class="fw-bold mb-1">
                        <?= $sekolah['nama_sekolah']; ?>
                    </h2>

                    <h5 class="mb-3">
                        Dashboard Guru
                    </h5>

                    <p class="mb-0 fs-5">
                        Selamat datang,
                        <strong><?= session()->get('nama'); ?></strong>
                    </p>

                </div>

            </div>

        </div>
    </div>

    <!-- Menu -->
    <?php
        $jabatanList = array_map('mb_strtolower', session()->get('jabatan_list') ?? []);
        $isWaliKelas = in_array('wali kelas', $jabatanList);
    ?>
    <div class="row">

        <div class="col-lg-3 mb-4">

            <div class="card shadow border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <i class="bi bi-journal-text text-primary"
                            style="font-size:55px;"></i>

                    </div>

                    <h4>Jurnal Mengajar</h4>

                    <p class="text-muted">
                        Kelola seluruh jurnal mengajar setiap mata pelajaran.
                    </p>

                    <a href="<?= base_url('jurnal') ?>"
                        class="btn btn-primary rounded-pill px-4">

                        <i class="bi bi-arrow-right-circle"></i>

                        Buka

                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 mb-4">

            <div class="card shadow border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <i class="bi bi-clipboard-check text-info"
                            style="font-size:55px;"></i>

                    </div>

                    <h4>Absensi Mapel</h4>

                    <p class="text-muted">
                        Catat kehadiran siswa per sesi mata pelajaran yang kamu ajar.
                    </p>

                    <a href="<?= base_url('absensi-mapel') ?>"
                        class="btn btn-info rounded-pill px-4 text-white">

                        <i class="bi bi-arrow-right-circle"></i>

                        Buka

                    </a>

                </div>

            </div>

        </div>

        <?php if ($isWaliKelas): ?>
        <div class="col-lg-3 mb-4">

            <div class="card shadow border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <i class="bi bi-clipboard-check text-danger"
                            style="font-size:55px;"></i>

                    </div>

                    <h4>Absensi Siswa</h4>

                    <p class="text-muted">
                        Lakukan absensi siswa dengan cepat dan mudah.
                    </p>

                    <a href="<?= base_url('absensi') ?>"
                        class="btn btn-danger rounded-pill px-4">

                        <i class="bi bi-arrow-right-circle"></i>

                        Buka

                    </a>

                </div>

            </div>

        </div>
        <?php endif; ?>

        <div class="col-lg-3 mb-4">

            <div class="card shadow border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <i class="bi bi-moon-stars text-success"
                            style="font-size:55px;"></i>

                    </div>

                    <h4>Absensi Sholat</h4>

                    <p class="text-muted">
                        Catat absensi sholat Dhuha, Zuhur, dan Ashar siswa.
                    </p>

                    <a href="<?= base_url('absensi-sholat') ?>"
                        class="btn btn-success rounded-pill px-4">

                        <i class="bi bi-arrow-right-circle"></i>

                        Buka

                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 mb-4">

            <div class="card shadow border-0 h-100">

                <div class="card-body text-center">

                    <div class="mb-3">

                        <i class="bi bi-bookmark-check text-warning"
                            style="font-size:55px;"></i>

                    </div>

                    <h4>Pembinaan Siswa</h4>

                    <p class="text-muted">
                        Catat dan kelola pembinaan yang dilakukan terhadap siswa.
                    </p>

                    <a href="<?= base_url('pelanggaran') ?>"
                        class="btn btn-warning rounded-pill px-4">

                        <i class="bi bi-arrow-right-circle"></i>

                        Buka

                    </a>

                </div>

            </div>

        </div>

    </div>

    <?php if (!empty($isWakasekKes)): ?>

    <hr class="my-4">

    <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body text-white" style="background:linear-gradient(135deg,#0d6efd,#4f46e5);">
            <h4 class="fw-bold mb-1">
                <i class="bi bi-people-fill"></i>
                Dashboard Kesiswaan
            </h4>
            <p class="mb-0">
                Ringkasan pembagian siswa asuh & hasil pembinaan.
            </p>
        </div>
    </div>

    <div class="row g-4 mb-2">

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Siswa</small>
                        <h2 class="fw-bold mb-0"><?= $totalSiswa ?></h2>
                    </div>
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Sudah Dibagi ke Guru Wali</small>
                        <h2 class="fw-bold mb-0 text-success"><?= $sudahDibagi ?></h2>
                    </div>
                    <i class="bi bi-check-circle-fill text-success" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Belum Dibagi</small>
                        <h2 class="fw-bold mb-0 text-danger"><?= $belumDibagi ?></h2>
                    </div>
                    <i class="bi bi-exclamation-circle-fill text-danger" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Guru Wali Aktif</small>
                        <h2 class="fw-bold mb-0"><?= $totalGuruWali ?></h2>
                    </div>
                    <i class="bi bi-person-badge-fill text-info" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

    </div>

    <?php if ($belumDibagi > 0): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <i class="bi bi-exclamation-triangle-fill"></i>
            Masih ada <strong><?= $belumDibagi ?></strong> siswa yang belum dibagi ke guru wali.
        </div>
        <a href="<?= base_url('kesiswaan/distribusi?status=belum') ?>" class="btn btn-sm btn-warning">
            Bagikan Sekarang
        </a>
    </div>
    <?php endif; ?>

    <div class="row g-4 mt-1">

        <div class="col-lg-7">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-graph-up"></i>
                    Tren Hasil Pembinaan (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="chartTren" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-bar-chart-fill"></i>
                    Jumlah Pembinaan per Guru Wali
                </div>
                <div class="card-body">
                    <canvas id="chartGuruWali" height="110"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-4 mb-2">
        <a href="<?= base_url('kesiswaan/distribusi') ?>" class="btn btn-primary">
            <i class="bi bi-diagram-3"></i> Distribusi Siswa ke Guru Wali
        </a>
        <a href="<?= base_url('kesiswaan/rekap') ?>" class="btn btn-outline-primary">
            <i class="bi bi-clipboard-data"></i> Rekap Pembinaan per Guru Wali
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    const trenLabels = <?= json_encode(array_map(fn($t) => $t['bulan'], $trenBulanan)) ?>;
    const trenKasus  = <?= json_encode(array_map(fn($t) => (int) $t['jumlah_kasus'], $trenBulanan)) ?>;

    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: trenLabels,
            datasets: [
                {
                    label: 'Jumlah Kasus',
                    data: trenKasus,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true,
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    const rekapLabels = <?= json_encode(array_map(fn($r) => $r['nama_guru_wali'], $rekap)) ?>;
    const rekapJumlah  = <?= json_encode(array_map(fn($r) => (int) $r['jumlah_pembinaan'], $rekap)) ?>;

    new Chart(document.getElementById('chartGuruWali'), {
        type: 'bar',
        data: {
            labels: rekapLabels,
            datasets: [{
                label: 'Jumlah Pembinaan',
                data: rekapJumlah,
                backgroundColor: '#4f46e5',
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });
    </script>

    <?php endif; ?>

    <?php if (!empty($isWakasekKur)): ?>

    <hr class="my-4">

    <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body text-white" style="background:linear-gradient(135deg,#0d6efd,#4f46e5);">
            <h4 class="fw-bold mb-1">
                <i class="bi bi-mortarboard"></i>
                Dashboard Kurikulum
            </h4>
            <p class="mb-0">
                Ringkasan guru mengajar, jadwal, dan realisasi mengajar bulan berjalan.
            </p>
        </div>
    </div>

    <div class="row g-4 mb-2">

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Guru Mengajar</small>
                        <h2 class="fw-bold mb-0"><?= $totalGuruMengajar ?></h2>
                    </div>
                    <i class="bi bi-person-badge-fill text-primary" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Mata Pelajaran</small>
                        <h2 class="fw-bold mb-0"><?= $totalMapel ?></h2>
                    </div>
                    <i class="bi bi-book-fill text-info" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Kelas</small>
                        <h2 class="fw-bold mb-0"><?= $totalKelas ?></h2>
                    </div>
                    <i class="bi bi-door-open-fill text-success" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Slot Jadwal / Minggu</small>
                        <h2 class="fw-bold mb-0"><?= $totalJadwal ?></h2>
                    </div>
                    <i class="bi bi-calendar-week-fill text-warning" style="font-size:40px;"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-2">

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body">
                    <small class="text-muted">Realisasi Mengajar Bulan Ini</small>
                    <h2 class="fw-bold mb-0 <?= $realisasiPersen >= 80 ? 'text-success' : ($realisasiPersen >= 50 ? 'text-warning' : 'text-danger') ?>">
                        <?= $realisasiPersen ?>%
                    </h2>
                    <small class="text-muted"><?= $realisasiTerisi ?> dari <?= $realisasiJadwal ?> pertemuan terjadwal terisi jurnal</small>
                </div>
            </div>
        </div>

    </div>

    <?php if ($jumlahGuruRendah > 0): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm">
        <div>
            <i class="bi bi-exclamation-triangle-fill"></i>
            Ada <strong><?= $jumlahGuruRendah ?></strong> guru dengan realisasi mengajar di bawah 50% bulan ini.
        </div>
        <a href="<?= base_url('realisasi-mengajar') ?>" class="btn btn-sm btn-warning">
            Lihat Detail
        </a>
    </div>
    <?php endif; ?>

    <div class="row g-4 mt-1">

        <div class="col-lg-7">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-graph-up"></i>
                    Tren Realisasi Mengajar (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="chartTrenKurikulum" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white fw-bold">
                    <i class="bi bi-bar-chart-fill"></i>
                    Realisasi Mengajar per Guru (Bulan Ini)
                </div>
                <div class="card-body">
                    <canvas id="chartGuruKurikulum" height="110"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex flex-wrap gap-2 mt-4 mb-2">
        <a href="<?= base_url('guru-mengajar') ?>" class="btn btn-primary">
            <i class="bi bi-person-badge"></i> Guru Mengajar
        </a>
        <a href="<?= base_url('jadwal') ?>" class="btn btn-outline-primary">
            <i class="bi bi-calendar"></i> Jadwal Pelajaran
        </a>
        <a href="<?= base_url('mapel') ?>" class="btn btn-outline-primary">
            <i class="bi bi-book"></i> Mata Pelajaran
        </a>
        <a href="<?= base_url('kelas') ?>" class="btn btn-outline-primary">
            <i class="bi bi-mortarboard"></i> Kelas
        </a>
        <a href="<?= base_url('realisasi-mengajar') ?>" class="btn btn-outline-primary">
            <i class="bi bi-clipboard-data"></i> Realisasi Mengajar
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
    const trenKurLabels     = <?= json_encode(array_map(fn($t) => $t['bulan'], $trenRealisasi)) ?>;
    const trenKurPersentase = <?= json_encode(array_map(fn($t) => (float) $t['persentase'], $trenRealisasi)) ?>;

    new Chart(document.getElementById('chartTrenKurikulum'), {
        type: 'line',
        data: {
            labels: trenKurLabels,
            datasets: [
                {
                    label: 'Realisasi (%)',
                    data: trenKurPersentase,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.3,
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    const guruKurLabels     = <?= json_encode(array_map(fn($r) => $r['nama_guru'], $rekapGuru)) ?>;
    const guruKurPersentase = <?= json_encode(array_map(fn($r) => (float) $r['persentase'], $rekapGuru)) ?>;

    new Chart(document.getElementById('chartGuruKurikulum'), {
        type: 'bar',
        data: {
            labels: guruKurLabels,
            datasets: [{
                label: 'Realisasi (%)',
                data: guruKurPersentase,
                backgroundColor: guruKurPersentase.map(p => p >= 80 ? '#198754' : (p >= 50 ? '#ffc107' : '#dc3545')),
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, max: 100 } }
        }
    });
    </script>

    <?php endif; ?>

</div>

<?= view('template/footer'); ?>
