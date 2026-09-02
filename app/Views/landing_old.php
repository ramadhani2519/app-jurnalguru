<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sistem Jurnal Mengajar Guru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet">

<style>

:root{
    --primary:#0d6efd;
    --success:#20c997;
    --dark:#0f172a;
}

body{
    font-family:'Segoe UI',sans-serif;
    background:#f8fafc;
}

.navbar{
    background:rgba(13,110,253,.95)!important;
    backdrop-filter:blur(10px);
}

.hero{
    min-height:90vh;
    display:flex;
    align-items:center;
    background:
    linear-gradient(
        rgba(13,110,253,.85),
        rgba(32,201,151,.85)
    ),
    url('https://images.unsplash.com/photo-1509062522246-3755977927d7');

    background-size:cover;
    background-position:center;
    color:#fff;
}

.hero-title{
    font-size:2rem;
    font-weight:700;
}

.hero-subtitle{
    font-size:1.2rem;
    line-height:1.8;
}

.glass-card{
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.2);
    backdrop-filter:blur(15px);
    border-radius:20px;
}

.stat-card{
    border:none;
    border-radius:20px;
    transition:.3s;
}

.stat-card:hover{
    transform:translateY(-6px);
}

.feature-card{
    border:none;
    border-radius:20px;
    transition:.3s;
}

.feature-card:hover{
    transform:translateY(-8px);
}

.icon-box{
    width:70px;
    height:70px;
    background:#eef4ff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    margin:auto;
}

.section-title{
    font-weight:700;
    margin-bottom:40px;
}

.footer{
    background:#0f172a;
    color:white;
}

.mobile-menu{
    display:none;
}

.floating-login{
    display:none;
}

/* ================= MOBILE ================= */

@media(max-width:768px){

.hero{
    min-height:auto;
    padding:70px 0;
    text-align:center;
}

.hero-title{
    font-size:2rem;
}

.hero-subtitle{
    font-size:15px;
}

.hero .btn{
    width:100%;
    margin-bottom:10px;
}

.glass-card{
    margin-top:25px;
    text-align:left;
}

.section-title{
    font-size:24px;
}

.mobile-menu{
    display:flex;
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:white;
    box-shadow:0 -2px 15px rgba(0,0,0,.08);
    z-index:9999;
    border-top:1px solid #eee;
}

.mobile-menu a{
    flex:1;
    text-align:center;
    text-decoration:none;
    color:#555;
    padding:10px 5px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

.mobile-menu i{
    font-size:20px;
}

.mobile-menu span{
    font-size:11px;
}

.mobile-menu a:hover{
    color:#0d6efd;
}

.floating-login{
    display:flex;
    position:fixed;
    right:20px;
    bottom:80px;
    width:55px;
    height:55px;
    border-radius:50%;
    background:#0d6efd;
    color:white;
    justify-content:center;
    align-items:center;
    text-decoration:none;
    box-shadow:0 5px 20px rgba(0,0,0,.25);
    z-index:999;
}

.floating-login i{
    font-size:22px;
}

footer{
    padding-bottom:80px !important;
}

}

@media(max-width:768px){

    .footer{
        padding-bottom:20px;
        margin-bottom:85px;
    }

}

.logo-sekolah{

width:60px;
height:60px;

border-radius:50%;

background:white;

padding:5px;

object-fit:cover;

box-shadow:0 10px 25px rgba(0,0,0,.2);

}

.modern-navbar{

background:linear-gradient(135deg,#0d6efd,#2563eb);

}
</style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow sticky-top modern-navbar">

<div class="container">

<a class="navbar-brand d-flex align-items-center" href="#">

<img src="<?= $logoSekolah ?>"
     class="logo-sekolah me-3">

<div>

<div class="fw-bold fs-5">
<?= $namaSekolah ?>
</div>

<small class="text-light opacity-75">
Sistem Jurnal Mengajar Guru
</small>

</div>

</a>

<a href="<?= base_url('login') ?>"
class="btn btn-light rounded-pill px-4">

<i class="bi bi-box-arrow-in-right"></i>

Login

</a>

</div>

</nav>

<section class="hero">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-7">

<h1 class="hero-title">
📚 Sistem Jurnal Mengajar Guru
</h1>

<p class="hero-subtitle mt-3">
Platform digital untuk mencatat kegiatan pembelajaran,
monitoring aktivitas guru, rekap materi, serta pengawasan
oleh Kepala Sekolah secara realtime.
</p>

<div class="mt-4">

<a href="<?= base_url('login') ?>"
class="btn btn-warning btn-lg px-4">

<i class="bi bi-box-arrow-in-right"></i>
Masuk Sekarang

</a>

<a href="#fitur"
class="btn btn-outline-light btn-lg px-4">

Lihat Fitur

</a>

</div>

</div>

<div class="col-lg-5">

<div class="glass-card p-4">

<h4>Fitur Utama</h4>

<hr>

<p>✅ Jurnal Mengajar Guru</p>
<p>✅ Monitoring Aktivitas Guru</p>
<p>✅ Rekap Materi Pembelajaran</p>
<p>✅ Dashboard Kepala Sekolah</p>
<p>✅ Cetak Laporan PDF</p>

</div>

</div>

</div>

</div>

</section>

<section class="container py-5">

<div class="row g-4">

<div class="col-md-4">

<div class="card stat-card shadow">

<div class="card-body text-center">

<h1 class="text-primary">
<?= $totalGuru ?>
</h1>

<p class="mb-0">Guru</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card stat-card shadow">

<div class="card-body text-center">

<h1 class="text-success">
<?= $totalKelas ?>
</h1>

<p class="mb-0">Kelas</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card stat-card shadow">

<div class="card-body text-center">

<h1 class="text-danger">
<?= $totalJurnal ?>
</h1>

<p class="mb-0">Jurnal</p>

</div>

</div>

</div>

</div>

</section>

<section id="fitur" class="bg-white py-5">

<div class="container">

<h2 class="text-center section-title">
Fitur Unggulan
</h2>

<div class="row g-4">

<div class="col-md-3">
<div class="card feature-card shadow-sm h-100">
<div class="card-body text-center">
<div class="icon-box">📖</div>
<h5 class="mt-3">Jurnal Mengajar</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card feature-card shadow-sm h-100">
<div class="card-body text-center">
<div class="icon-box">📊</div>
<h5 class="mt-3">Monitoring Guru</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card feature-card shadow-sm h-100">
<div class="card-body text-center">
<div class="icon-box">🖨</div>
<h5 class="mt-3">Laporan PDF</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card feature-card shadow-sm h-100">
<div class="card-body text-center">
<div class="icon-box">🏫</div>
<h5 class="mt-3">Dashboard Kepsek</h5>
</div>
</div>
</div>

</div>

</div>

</section>

<section id="tentang" class="bg-light py-5">

<div class="container">

<div class="row align-items-center">

<div class="col-md-6">

<h2>Tentang Sistem</h2>

<p>
Sistem Jurnal Mengajar Guru membantu sekolah
melakukan digitalisasi kegiatan pembelajaran.
Guru dapat mengisi jurnal secara online dan
Kepala Sekolah dapat memantau aktivitas
mengajar secara realtime.
</p>

</div>

<div class="col-md-6 text-center">

<i class="bi bi-building"
style="font-size:120px;color:#0d6efd;"></i>

</div>

</div>

</div>

</section>

<footer class="footer py-4">

<div class="container text-center">

<h5>📚 Sistem Jurnal Mengajar Guru</h5>

<p class="mb-0">
© <?= date('Y') ?> All Rights Reserved
</p>

</div>

</footer>

<a href="<?= base_url('login') ?>"
class="floating-login">

<i class="bi bi-person-fill"></i>

</a>

<div class="mobile-menu">

<a href="#">
<i class="bi bi-house-fill"></i>
<span>Home</span>
</a>

<a href="#fitur">
<i class="bi bi-grid-fill"></i>
<span>Fitur</span>
</a>

<a href="#tentang">
<i class="bi bi-info-circle-fill"></i>
<span>Tentang</span>
</a>

<a href="<?= base_url('login') ?>">
<i class="bi bi-box-arrow-in-right"></i>
<span>Login</span>
</a>

</div>

</body>
</html>