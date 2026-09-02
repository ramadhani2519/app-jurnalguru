<!doctype html>
<html lang="id">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Jurnal Mengajar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
body{
    background:#f8fafc;
}

.navbar-brand{
    font-weight:700;
}

.user-info{
    display:flex;
    align-items:center;
    gap:10px;
}

.user-avatar{
    width:38px;
    height:38px;
    border-radius:50%;
    background:rgba(255,255,255,.2);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}

.role-badge{
    font-size:.75rem;
}
</style>


<style>

footer{
    margin-top:auto;
}

.card{
    border-radius:15px;
}

.btn{
    border-radius:10px;
}

.form-control,
.form-select{
    border-radius:10px;
}

.table{
    vertical-align:middle;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current{
    background:#0d6efd !important;
    color:#fff !important;
    border:none !important;
    border-radius:8px;
}
</style>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">

<div class="container">

    <a class="navbar-brand" href="#">
        <i class="bi bi-journal-bookmark-fill"></i>
        Jurnal Mengajar
    </a>

    <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainMenu"
            aria-controls="mainMenu"
            aria-expanded="false"
            aria-label="Toggle navigation">

        <span class="navbar-toggler-icon"></span>

    </button>

    <div class="collapse navbar-collapse"
         id="mainMenu">

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php if(in_array(session()->get('role_id'),['1','2'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
        <?php endif; ?>

        <?php if(in_array(session()->get('role_id'),['3'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('kepala-sekolah') ?>">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
        <?php endif; ?>
            <?php if(session()->get('role_id') == 1): ?>

                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">

                        <i class="bi bi-gear"></i>
                        Master Data

                    </a>

                    <ul class="dropdown-menu">
                         <li>
                            <a class="dropdown-item"
                               href="<?= base_url('sekolah') ?>">
                                <i class="bi bi-building"></i>
                                Data Sekolah
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('tahun') ?>">
                                <i class="bi bi-calendar-range"></i>
                                Tahun Pelajaran
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('semester') ?>">
                                <i class="bi bi-bookmark-check"></i>
                                Semester
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('kelas') ?>">
                                <i class="bi bi-mortarboard"></i>
                                Kelas
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('jurusan') ?>">
                                <i class="bi bi-diagram-3"></i>
                                Jurusan
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('jenis-pelanggaran') ?>">
                                <i class="bi bi-list-check"></i>
                                Jenis Pelanggaran
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('jam') ?>">
                                <i class="bi bi-clock"></i>
                                Jam Pelajaran
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('mapel') ?>">
                                <i class="bi bi-book"></i>
                                Mata Pelajaran
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('user') ?>">
                                <i class="bi bi-people"></i>
                                Data Pengguna
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('siswa') ?>">
                                <i class="bi bi-people"></i>
                                Data Siswa
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="<?= base_url('jadwal') ?>">
                                <i class="bi bi-calendar"></i>
                                Jadwal Pelajaran
                            </a>
                        </li>
                    </ul>

                </li>

            <?php endif; ?>

        <?php
            $jabatanList  = array_map('mb_strtolower', session()->get('jabatan_list') ?? []);
            $isWakasekKur = in_array('wakasek kurikulum', $jabatanList);
            $isWaliKelas  = in_array('wali kelas', $jabatanList);
            $isWakasekKes = in_array('wakasek kesiswaan', $jabatanList);
            $isGuruWali   = in_array('guru wali', $jabatanList);
            $isKetuaJurusan = in_array('ketua jurusan', $jabatanList);
        ?>
        <?php if(session()->get('role_id') == 2 && $isWakasekKes): ?>

            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown">

                    <i class="bi bi-people-fill"></i>
                    Kesiswaan

                </a>

                <ul class="dropdown-menu">

                    <li>
                        <a class="dropdown-item" href="<?= base_url('kesiswaan/distribusi') ?>">
                            <i class="bi bi-diagram-3"></i>
                            Distribusi Siswa ke Guru Wali
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('kesiswaan/rekap') ?>">
                            <i class="bi bi-clipboard-data"></i>
                            Rekap Hasil Pembinaan
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('kesiswaan/status-siswa') ?>">
                            <i class="bi bi-shield-check"></i>
                            Status Pembinaan Siswa
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('jenis-pelanggaran') ?>">
                            <i class="bi bi-list-check"></i>
                            Jenis Pelanggaran
                        </a>
                    </li>

                </ul>

            </li>

        <?php endif; ?>

        <?php if(session()->get('role_id') == 2 && $isGuruWali): ?>

            <?php
                $jumlahPerluPembinaan = 0;

                if (!empty($tahunAktif['id'])) {
                    $db = \Config\Database::connect();

                    $siswaAsuhIds = $db->table('wali_asuh_siswa')
                        ->select('siswa_id')
                        ->where('guru_id', session()->get('id'))
                        ->where('tahun_pelajaran_id', $tahunAktif['id'])
                        ->get()
                        ->getResultArray();

                    $siswaAsuhIds = array_column($siswaAsuhIds, 'siswa_id');

                    if (!empty($siswaAsuhIds)) {
                        $jumlahPelanggaranNav = $db->table('pelanggaran_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah, MAX(tanggal) as tanggal_terakhir')
                            ->whereIn('siswa_id', $siswaAsuhIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $jumlahPembinaanNav = $db->table('pembinaan_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah')
                            ->whereIn('siswa_id', $siswaAsuhIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $petaPembinaanNav = [];
                        foreach ($jumlahPembinaanNav as $row) {
                            $petaPembinaanNav[$row['siswa_id']] = (int) $row['jumlah'];
                        }

                        foreach ($jumlahPelanggaranNav as $row) {
                            // Reset 1 bulan: kalau 30 hari tanpa pelanggaran baru, dianggap lunas.
                            $sudahLunasNav = $row['tanggal_terakhir']
                                && (strtotime('today') - strtotime(date('Y-m-d', strtotime($row['tanggal_terakhir'])))) >= (30 * 86400);

                            if ($sudahLunasNav) {
                                continue;
                            }

                            $sisa = (int) $row['jumlah'] - (($petaPembinaanNav[$row['siswa_id']] ?? 0) * 2);
                            if ($sisa >= 2) {
                                $jumlahPerluPembinaan++;
                            }
                        }
                    }
                }
            ?>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('guru-wali/siswa-saya') ?>">
                    <i class="bi bi-person-hearts"></i>
                    Siswa Asuh Saya
                    <?php if ($jumlahPerluPembinaan > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $jumlahPerluPembinaan ?></span>
                    <?php endif; ?>
                </a>
            </li>

        <?php endif; ?>

        <?php if(session()->get('role_id') == 2 && $isWaliKelas): ?>

            <?php
                $jumlahPerluTindakWali = 0;

                $db = \Config\Database::connect();

                $kelasWaliIds = $db->table('user_jabatan')
                    ->select('user_jabatan.kelas_id')
                    ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
                    ->where('user_jabatan.user_id', session()->get('id'))
                    ->where('jabatan.nama_jabatan', 'Wali Kelas')
                    ->get()
                    ->getResultArray();

                $kelasWaliIds = array_values(array_filter(array_column($kelasWaliIds, 'kelas_id')));

                if (!empty($kelasWaliIds)) {
                    $siswaWaliIds = $db->table('siswa')
                        ->select('id')
                        ->whereIn('kelas_id', $kelasWaliIds)
                        ->get()
                        ->getResultArray();

                    $siswaWaliIds = array_column($siswaWaliIds, 'id');

                    if (!empty($siswaWaliIds)) {
                        $jumlahPelanggaranWaliNav = $db->table('pelanggaran_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah, MAX(tanggal) as tanggal_terakhir')
                            ->whereIn('siswa_id', $siswaWaliIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $jumlahPembinaanWaliNav = $db->table('pembinaan_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah')
                            ->whereIn('siswa_id', $siswaWaliIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $petaPembinaanWaliNav = [];
                        foreach ($jumlahPembinaanWaliNav as $row) {
                            $petaPembinaanWaliNav[$row['siswa_id']] = (int) $row['jumlah'];
                        }

                        foreach ($jumlahPelanggaranWaliNav as $row) {
                            // Reset 1 bulan: kalau 30 hari tanpa pelanggaran baru, dianggap lunas.
                            $sudahLunasWaliNav = $row['tanggal_terakhir']
                                && (strtotime('today') - strtotime(date('Y-m-d', strtotime($row['tanggal_terakhir'])))) >= (30 * 86400);

                            if ($sudahLunasWaliNav) {
                                continue;
                            }

                            $jumlahPembinaanSiswaIni = $petaPembinaanWaliNav[$row['siswa_id']] ?? 0;
                            $sisa = (int) $row['jumlah'] - ($jumlahPembinaanSiswaIni * 2);

                            if ($jumlahPembinaanSiswaIni === 1 && $sisa >= 2) {
                                $jumlahPerluTindakWali++;
                            }
                        }
                    }
                }
            ?>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('wali-kelas-binaan/siswa') ?>">
                    <i class="bi bi-shield-check"></i>
                    Pembinaan Siswa (Wali Kelas)
                    <?php if ($jumlahPerluTindakWali > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $jumlahPerluTindakWali ?></span>
                    <?php endif; ?>
                </a>
            </li>

        <?php endif; ?>

        <?php if(session()->get('role_id') == 2 && $isKetuaJurusan): ?>

            <?php
                $jumlahPerluTindakKetua = 0;

                $db = \Config\Database::connect();

                $jurusanSayaNav = $db->table('user_jabatan')
                    ->select('user_jabatan.jurusan')
                    ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
                    ->where('user_jabatan.user_id', session()->get('id'))
                    ->where('jabatan.nama_jabatan', 'Ketua Jurusan')
                    ->where('user_jabatan.jurusan IS NOT NULL')
                    ->get()
                    ->getRowArray();

                $jurusanSayaNav = isset($jurusanSayaNav['jurusan']) ? trim($jurusanSayaNav['jurusan']) : null;

                if (!empty($jurusanSayaNav)) {
                    $siswaJurusanIds = $db->table('siswa')
                        ->select('siswa.id')
                        ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                        ->where('UPPER(TRIM(kelas.jurusan)) =', strtoupper($jurusanSayaNav))
                        ->get()
                        ->getResultArray();

                    $siswaJurusanIds = array_column($siswaJurusanIds, 'id');

                    if (!empty($siswaJurusanIds)) {
                        $jumlahPelanggaranKetuaNav = $db->table('pelanggaran_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah, MAX(tanggal) as tanggal_terakhir')
                            ->whereIn('siswa_id', $siswaJurusanIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $jumlahPembinaanKetuaNav = $db->table('pembinaan_siswa')
                            ->select('siswa_id, COUNT(*) as jumlah')
                            ->whereIn('siswa_id', $siswaJurusanIds)
                            ->groupBy('siswa_id')
                            ->get()
                            ->getResultArray();

                        $petaPembinaanKetuaNav = [];
                        foreach ($jumlahPembinaanKetuaNav as $row) {
                            $petaPembinaanKetuaNav[$row['siswa_id']] = (int) $row['jumlah'];
                        }

                        foreach ($jumlahPelanggaranKetuaNav as $row) {
                            // Reset 1 bulan: kalau 30 hari tanpa pelanggaran baru, dianggap lunas.
                            $sudahLunasKetuaNav = $row['tanggal_terakhir']
                                && (strtotime('today') - strtotime(date('Y-m-d', strtotime($row['tanggal_terakhir'])))) >= (30 * 86400);

                            if ($sudahLunasKetuaNav) {
                                continue;
                            }

                            $jumlahPembinaanSiswaIni = $petaPembinaanKetuaNav[$row['siswa_id']] ?? 0;
                            $sisa = (int) $row['jumlah'] - ($jumlahPembinaanSiswaIni * 2);

                            if ($jumlahPembinaanSiswaIni >= 2 && $sisa >= 2) {
                                $jumlahPerluTindakKetua++;
                            }
                        }
                    }
                }
            ?>

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('ketua-jurusan-binaan/siswa') ?>">
                    <i class="bi bi-shield-check"></i>
                    Pembinaan Siswa (Ketua Jurusan)
                    <?php if ($jumlahPerluTindakKetua > 0): ?>
                    <span class="badge bg-danger rounded-pill"><?= $jumlahPerluTindakKetua ?></span>
                    <?php endif; ?>
                </a>
            </li>

        <?php endif; ?>
        <?php if(session()->get('role_id') == 2 && $isWakasekKur): ?>

            <li class="nav-item dropdown">

                <a class="nav-link dropdown-toggle"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown">

                    <i class="bi bi-mortarboard"></i>
                    Kurikulum

                </a>

                <ul class="dropdown-menu">

                    <li>
                        <a class="dropdown-item" href="<?= base_url('guru-mengajar') ?>">
                            <i class="bi bi-person-badge"></i>
                            Guru Mengajar
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('jadwal') ?>">
                            <i class="bi bi-calendar"></i>
                            Jadwal Pelajaran
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('mapel') ?>">
                            <i class="bi bi-book"></i>
                            Mata Pelajaran
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('kelas') ?>">
                            <i class="bi bi-mortarboard"></i>
                            Kelas
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="<?= base_url('minggu-efektif') ?>">
                            <i class="bi bi-calendar-week"></i>
                            Minggu Efektif
                        </a>
                    </li>

                </ul>

            </li>

        <?php endif; ?>
             <?php if(session()->get('role_id') == 2): ?>
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('jurnal') ?>">
                    <i class="bi bi-journal-text"></i>
                    Jurnal Mengajar
                </a>
            </li>
            <?php if($isWaliKelas): ?>
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('absensi') ?>">
                    <i class="bi bi-list"></i>
                    Absensi Siswa
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('absensi-mapel') ?>">
                    <i class="bi bi-clipboard-check"></i>
                    Absensi Mapel
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('absensi-sholat') ?>">
                    <i class="bi bi-moon-stars"></i>
                     Absensi Sholat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('pelanggaran') ?>">
                    <i class="bi bi-bookmark-check"></i>
                    Pembinaan Siswa
                </a>
            </li>
        <?php endif; ?>

        <?php if(session()->get('role_id') == 4): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('absensi') ?>">
                    <i class="bi bi-list"></i>
                    Absensi Siswa
                </a>
            </li>
        <?php endif; ?>

        <?php if(session()->get('role_id') == 5): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('absensi-sholat') ?>">
                    <i class="bi bi-moon-stars"></i>
                    Absensi Sholat
                </a>
            </li>
        <?php endif; ?>

        <?php if(in_array(session()->get('role_id'),['1','2','3'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('jurnal/laporan') ?>">
                    <i class="bi bi-file"></i>
                    Laporan
                </a>
            </li>
        <?php endif; ?>
        </ul>

        <div class="dropdown">

            <a href="#"
               class="text-white text-decoration-none dropdown-toggle user-info"
               data-bs-toggle="dropdown">

                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div>

                    <div class="fw-semibold">
                        <?= session()->get('nama') ?>
                    </div>

                    <span class="badge bg-light text-primary role-badge">
                        <?= session()->get('role') ?>
                    </span>

                </div>

            </a>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <h6 class="dropdown-header">
                        <?= session()->get('nama') ?>
                    </h6>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item"
                       href="<?= base_url('profile') ?>">
                        <i class="bi bi-person-fill"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

</div>

</nav>

<div class="container-fluid py-4">
