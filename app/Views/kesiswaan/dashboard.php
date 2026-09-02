<?= view('template/header') ?>

<div class="container-fluid py-4">

    <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body text-white" style="background:linear-gradient(135deg,#0d6efd,#4f46e5);">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-people-fill"></i>
                Dashboard Kesiswaan
            </h2>
            <p class="mb-0 fs-5">
                Selamat datang, <strong><?= esc(session()->get('nama')) ?></strong>
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

    <div class="d-flex gap-2 mt-4">
        <a href="<?= base_url('kesiswaan/distribusi') ?>" class="btn btn-primary">
            <i class="bi bi-diagram-3"></i> Distribusi Siswa ke Guru Wali
        </a>
        <a href="<?= base_url('kesiswaan/rekap') ?>" class="btn btn-outline-primary">
            <i class="bi bi-clipboard-data"></i> Rekap Pembinaan per Guru Wali
        </a>
    </div>

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

<?= view('template/footer') ?>
