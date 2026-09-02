<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login - Sistem Jurnal Mengajar</title>

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

.login-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

.login-card{
    width:100%;
    max-width:430px;
    border:none;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.login-header{
    background:white;
    text-align:center;
    padding:30px;
}

.logo{
    width:90px;
    height:90px;
    border-radius:50%;
    background:#eef4ff;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:45px;
}

.form-control{
    height:50px;
    border-radius:12px;
}

.btn-login{
    height:50px;
    border-radius:12px;
    font-weight:600;
}

.password-group{
    position:relative;
}

.password-toggle{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#666;
}

.card-footer-custom{
    background:#f8f9fa;
    padding:20px;
}

@media(max-width:768px){

    .login-card{
        max-width:100%;
    }

}

</style>

</head>
<body>

<div class="login-wrapper">

<div class="card login-card">

<div class="login-header">

<div class="logo">
🏫
</div>

<h3 class="mt-2 mb-1">
Jurnal Mengajar Guru
</h3>

<p class="text-muted mb-0">
Silakan login untuk melanjutkan
</p>

</div>

<div class="card-body p-4">

<?php if(session()->getFlashdata('error')): ?>

<div class="alert alert-danger">

<?= session()->getFlashdata('error') ?>

</div>

<?php endif; ?>

<form action="<?= base_url('login') ?>" method="post">

<?= csrf_field() ?>

<div class="mb-3">

<label class="form-label">
Username
</label>

<div class="input-group">

<span class="input-group-text">
<i class="bi bi-person"></i>
</span>

<input type="text"
       name="username"
       class="form-control"
       placeholder="Masukkan username"
       required>

</div>

</div>

<div class="mb-3">

<label class="form-label">
Password
</label>

<div class="password-group">

<input type="password"
       id="password"
       name="password"
       class="form-control"
       placeholder="Masukkan password"
       required>

<i class="bi bi-eye-slash password-toggle"
   id="togglePassword"></i>

</div>

</div>

<button type="submit"
        class="btn btn-primary btn-login w-100">

<i class="bi bi-box-arrow-in-right"></i>
 Login

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

<a href="<?= base_url('register') ?>"
   class="btn btn-success">

<i class="bi bi-person-plus"></i>
 Daftar Akun

</a>
<a href="<?= base_url('bantuan') ?>"
class="btn btn-outline-info">
    <i class="bi bi-question-circle"></i>
    Bantuan
</a>
</div>

</div>

</div>

</div>

<script>

const togglePassword =
document.querySelector("#togglePassword");

const password =
document.querySelector("#password");

togglePassword.addEventListener("click", function () {

    const type =
    password.getAttribute("type") === "password"
    ? "text"
    : "password";

    password.setAttribute("type", type);

    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");

});

</script>

</body>
</html>