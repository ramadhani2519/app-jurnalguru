<?php
// landing.php - Modern Landing Page CodeIgniter 4
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= esc($namaSekolah ?? 'Sistem Jurnal Mengajar Guru') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
:root{--p:#2563eb;--s:#06b6d4;--d:#0f172a;}
body{font-family:Segoe UI,sans-serif;background:#f8fafc}
.navbar{background:linear-gradient(90deg,var(--p),var(--s))}
.logo{width:50px;height:50px;border-radius:50%;background:#fff;padding:4px;object-fit:cover}
.hero{min-height:92vh;background:linear-gradient(135deg,#2563eb,#06b6d4);color:#fff;display:flex;align-items:center;position:relative;overflow:hidden}
.school-silhouette{position:absolute;left:0;right:0;top:0;bottom:0;width:100%;height:100%;object-fit:cover;object-position:bottom;pointer-events:none;z-index:0}
.hero>.container{position:relative;z-index:1}
.dimensi-item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.12)}
.dimensi-item:last-child{border-bottom:none}
.dimensi-num{width:28px;height:28px;flex:0 0 28px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}
.dimensi-item i{font-size:18px;color:#fde68a}
.glass{background:rgba(255,255,255,.15);backdrop-filter:blur(12px);border-radius:24px;border:1px solid rgba(255,255,255,.2)}
.cardx{border:none;border-radius:20px;transition:.3s}.cardx:hover{transform:translateY(-6px)}
.icon{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#e0ecff;color:var(--p);font-size:28px;margin:auto}
footer{background:var(--d);color:#fff}
</style>
</head>
<body>
<nav class="navbar navbar-dark py-2 sticky-top">
<div class="container">
<a class="navbar-brand d-flex align-items-center" href="#">
<img class="logo me-3" src="<?= $logoSekolah ?>" alt="Logo">
<div><div class="fw-bold"><?= esc($namaSekolah) ?></div><small>Sistem Jurnal Mengajar Guru</small></div>
</a>
<a href="<?= base_url('login') ?>" class="btn btn-light rounded-pill px-1 d-none d-md-flex align-items-center">Login</a>
</div>
</nav>

<section class="hero">

<?php
    // Siluet gedung sekolah SMKN 3 Marabahan. Taruh foto sekolah di
    // public/assets/img/sekolah-siluet.png (atau .jpg) -- akan otomatis
    // tampil sebagai siluet di dasar hero begitu file-nya ada.
    $fotoSiluetPath = null;
    foreach (['sekolah-siluet.png', 'sekolah-siluet.jpg', 'sekolah-siluet.jpeg'] as $namaFile) {
        if (is_file(FCPATH . 'assets/img/' . $namaFile)) {
            $fotoSiluetPath = $namaFile;
            break;
        }
    }
?>
<?php if ($fotoSiluetPath): ?>
<img src="<?= base_url('assets/img/' . $fotoSiluetPath) ?>" class="school-silhouette" alt="Siluet SMKN 3 Marabahan">
<?php endif; ?>

<div class="container">
<div class="row align-items-center">
<div class="col-lg-7">
<span class="badge bg-warning text-dark mb-3">Digital School Platform</span>
<h1 class="display-4 fw-bold">Sistem Jurnal Mengajar Guru</h1>
<p class="lead">Digitalisasi jurnal mengajar, monitoring guru, absensi siswa, rekap materi dan dashboard kepala sekolah secara realtime.</p>
<a href="<?= base_url('login') ?>" class="btn btn-warning btn-lg rounded-pill me-2">Masuk</a>
<a href="#fitur" class="btn btn-outline-light btn-lg rounded-pill">Lihat Fitur</a>
</div>
<div class="col-lg-5">
<div class="glass p-4 mt-4">
<h4 class="mb-1">8 Dimensi Profil Lulusan</h4>
<small class="d-block mb-2" style="opacity:.8">Permendikdasmen No. 10 Tahun 2025</small>
<hr>
<?php
$dimensiLulusan = [
    ['bi-moon-stars-fill', 'Keimanan dan Ketakwaan terhadap Tuhan YME'],
    ['bi-flag-fill', 'Kewargaan'],
    ['bi-lightbulb-fill', 'Penalaran Kritis'],
    ['bi-palette-fill', 'Kreativitas'],
    ['bi-people-fill', 'Kolaborasi'],
    ['bi-person-check-fill', 'Kemandirian'],
    ['bi-heart-pulse-fill', 'Kesehatan'],
    ['bi-chat-dots-fill', 'Komunikasi'],
];
foreach ($dimensiLulusan as $i => $d): ?>
<div class="dimensi-item">
    <span class="dimensi-num"><?= $i + 1 ?></span>
    <i class="bi <?= $d[0] ?>"></i>
    <span><?= $d[1] ?></span>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
</div>
</section>

<section class="container py-5">
<div class="row g-4">
<?php
$stats=[
['Guru',$totalGuru??0,'people-fill','primary'],
['Kelas',$totalKelas??0,'building','success'],
['Jurnal',$totalJurnal??0,'journal-text','danger']
];
foreach($stats as $s): ?>
<div class="col-md-4"><div class="card cardx shadow-sm"><div class="card-body text-center">
<div class="icon"><i class="bi bi-<?= $s[2] ?>"></i></div>
<h2 class="mt-3"><?= $s[1] ?></h2><p><?= $s[0] ?></p>
</div></div></div>
<?php endforeach; ?>
</div>
</section>

<section id="fitur" class="py-5 bg-white">
<div class="container">
<h2 class="text-center mb-5">Fitur Unggulan</h2>
<div class="row g-4">
<?php
$f=[['📖','Jurnal Mengajar'],['📊','Monitoring'],['🖨️','Laporan PDF'],['🏫','Dashboard'],['📱','Responsive'],['☁️','Cloud'],['🔒','Keamanan'],['⚡','Realtime']];
foreach($f as $x): ?>
<div class="col-md-3 col-6"><div class="card cardx h-100 shadow-sm"><div class="card-body text-center"><div style="font-size:42px"><?= $x[0] ?></div><h6 class="mt-3"><?= $x[1] ?></h6></div></div></div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="py-5 bg-light">
<div class="container text-center">
<h2>Mengapa Memilih Sistem Ini?</h2>
<p class="lead">Membantu sekolah melakukan digitalisasi administrasi pembelajaran secara cepat, aman, dan efisien.</p>
<a href="<?= base_url('login') ?>" class="btn btn-primary btn-lg rounded-pill">Mulai Sekarang</a>
</div>
</section>

<footer class="py-2">
<div class="container text-center">
<img src="<?= $logoSekolah ?>" class="logo mb-1 mt-4">
<h5 class="mb-0"><?= esc($namaSekolah) ?></h5>
<p class="mb-0">Sistem Jurnal Mengajar Guru</p>
<p class="mb-0">© <?= date('Y') ?> All Rights Reserved</p>
</div>
</footer>
</body></html>
