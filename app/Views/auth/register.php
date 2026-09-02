<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Registrasi - Sistem Jurnal Mengajar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet">

<style>

body{
    min-height:100vh;
    background:linear-gradient(
        135deg,
        #0d6efd,
        #20c997
    );
}

.register-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.register-card{
    width:100%;
    max-width:500px;
    border:none;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.card-header-custom{
    text-align:center;
    background:white;
    padding:30px;
}

.logo{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#eef4ff;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:auto;
    font-size:45px;
}

.form-control{
    height:50px;
    border-radius:12px;
}

.btn-register{
    height:50px;
    border-radius:12px;
    font-weight:600;
}

.password-wrapper{
    position:relative;
}

.password-toggle{
    position:absolute;
    top:50%;
    right:15px;
    transform:translateY(-50%);
    cursor:pointer;
    color:#666;
}

.card-footer-custom{
    background:#f8f9fa;
    padding:20px;
}

@media(max-width:768px){

    .register-card{
        max-width:100%;
    }

}

</style>

</head>

<body>

<div class="register-wrapper">

<div class="card register-card">

<div class="card-header-custom">

<div class="logo">
👨‍🏫
</div>

<h3 class="mt-3 mb-1">
Registrasi Guru
</h3>

<p class="text-muted mb-0">
Buat akun untuk menggunakan sistem jurnal mengajar
</p>

</div>

<div class="card-body p-4">

<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">
    <?= session()->getFlashdata('error') ?>
</div>

<?php endif; ?>

<form action="<?= base_url('register') ?>" method="post">

<?= csrf_field() ?>

<div class="mb-3">

<label class="form-label">
Nama Lengkap
</label>

<div class="input-group">

<span class="input-group-text">
<i class="bi bi-person-badge"></i>
</span>

<input type="text"
       name="nama"
       class="form-control"
       placeholder="Masukkan nama lengkap"
       required>

</div>

</div>

<div class="mb-3">

<label class="form-label">
Username
</label>

<div class="input-group">

<span class="input-group-text">
<i class="bi bi-at"></i>
</span>

<input type="text"
       name="username"
       class="form-control"
       placeholder="Masukkan username"
       required>

</div>

</div>

<div class="mb-3">
    <label class="form-label">NIP</label>
    <div class="input-group">

<span class="input-group-text">
<i class="bi bi-postcard"></i>
</span>
    <input type="text"
           name="nip"
           class="form-control"
           placeholder="Nomor Induk Pegawai">
</div>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <div class="input-group">

<span class="input-group-text">
<i class="bi bi-envelope-paper"></i>
</span>

    <input type="email"
           name="email"
           class="form-control"
           placeholder="guru@sekolah.sch.id">
</div>
</div>

<div class="mb-3">
    <label class="form-label">No HP</label>
    <div class="input-group">

<span class="input-group-text">
<i class="bi bi-phone"></i>
</span>
    <input type="text"
           name="no_hp"
           class="form-control"
           placeholder="08xxxxxxxxxx">
</div>
</div>

<div class="mb-3">

<label class="form-label">
Password
</label>

<div class="password-wrapper">

    <input type="password"
           id="password"
           name="password"
           class="form-control"
           placeholder="Masukkan password">

    <i class="bi bi-eye-slash-fill password-toggle"
       id="togglePassword"></i>

</div>
</div>

<button type="submit"
        class="btn btn-success btn-register w-100">

<i class="bi bi-person-plus"></i>
 Daftar Sekarang

</button>

</form>

</div>

<div class="card-footer-custom">

<div class="d-grid gap-2">

<a href="<?= base_url('/') ?>"
   class="btn btn-outline-secondary">

<i class="bi bi-house"></i>
 Home

</a>

<a href="<?= base_url('login') ?>"
   class="btn btn-primary">

<i class="bi bi-box-arrow-in-right"></i>
 Sudah Punya Akun? Login

</a>

</div>

</div>

</div>

</div>

<script>

const togglePassword =
document.querySelector('#togglePassword');

const password =
document.querySelector('#password');

togglePassword.addEventListener('click', function(){

    const type =
        password.getAttribute('type') === 'password'
        ? 'text'
        : 'password';

    password.setAttribute('type', type);

    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');

});

</script>

</body>
</html>