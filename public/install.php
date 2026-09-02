<?php

/**
 * CodeIgniter 4 Installer
 * PHP 8.2 Compatible
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOTPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

if (file_exists(ROOTPATH . '.installed')) {
?>
<!doctype html>
<html lang="id">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Aplikasi Sudah Terinstall</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Segoe UI,Arial,sans-serif;
}

.card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,.2);
}

.icon-check{
    width:110px;
    height:110px;
    background:#198754;
    color:#fff;
    font-size:60px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    margin-top:-70px;
    border:8px solid #fff;
}

.btn-custom{
    border-radius:50px;
    padding:12px 30px;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-5">

<div class="card">

<div class="card-header bg-primary text-white text-center py-4">

<h3 class="mb-0">
<i class="bi bi-shield-check"></i>
 Installer Jurnal Mengajar Guru
</h3>

</div>

<div class="card-body text-center p-5">

<div class="icon-check">
<i class="bi bi-check-lg"></i>
</div>

<h3 class="mt-4 text-success">
Instalasi Berhasil
</h3>

<p class="text-muted">

Aplikasi telah berhasil diinstal.

Untuk alasan keamanan, installer telah dikunci sehingga tidak dapat dijalankan kembali.

</p>

<hr>

<div class="d-grid gap-2">

<a href="../" class="btn btn-success btn-lg btn-custom">

<i class="bi bi-box-arrow-in-right"></i>

Masuk ke Aplikasi

</a>

<a href="javascript:history.back()"
class="btn btn-outline-secondary btn-custom">

<i class="bi bi-arrow-left"></i>

Kembali

</a>

</div>

<div class="mt-4 text-muted small">

<i class="bi bi-lock-fill text-danger"></i>

Installer Locked

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>

<?php
exit;
}

$message = '';
$success = false;

// Cek PHP
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    die("PHP minimal 8.1");
}

// Cek ekstensi
$extensions = [
    'mysqli',
    'intl',
    'mbstring',
    'json',
    'openssl',
    'curl',
    'fileinfo'
];

foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        die("Extension <b>$ext</b> belum aktif.");
    }
}

function importSQL(mysqli $mysqli, $file)
{
    if (!file_exists($file)) {
        throw new Exception("File jurnalguru.sql tidak ditemukan.");
    }

    $sql = file_get_contents($file);

    if (!$sql) {
        throw new Exception("Isi jurnalguru.sql kosong.");
    }

    if (!$mysqli->multi_query($sql)) {
        throw new Exception($mysqli->error);
    }

    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    return true;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $host     = trim($_POST['host']);
    $database = trim($_POST['database']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $baseURL  = rtrim($_POST['baseurl'], '/') . '/';

    try {

    // Koneksi ke MySQL (tanpa memilih database)
    $mysqli = new mysqli(
        $host,
        $username,
        $password
    );

    if ($mysqli->connect_errno) {
        throw new Exception($mysqli->connect_error);
    }

    // Buat database jika belum ada
   if (!$mysqli->query("
    CREATE DATABASE IF NOT EXISTS `$database`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci
")) {
    throw new Exception($mysqli->error);
}

    // Pilih database
    if (!$mysqli->select_db($database)) {
    throw new Exception("Database gagal dipilih.");
}

    // Import database.sql
    $sqlFile = ROOTPATH . 'db/jurnalguru.sql';
    $result = $mysqli->query("SHOW TABLES");

if ($result && $result->num_rows > 0) {
    throw new Exception("Database '$database' sudah berisi tabel. Silakan gunakan database kosong atau hapus tabel yang ada.");
}
   if (!importSQL($mysqli, $sqlFile)) {
    throw new Exception("Import database gagal.");
}

    // Generate .env
    $env = <<<ENV
CI_ENVIRONMENT = production

app.baseURL = '{$baseURL}'

app.forceGlobalSecureRequests = false

database.default.hostname = {$host}
database.default.database = {$database}
database.default.username = {$username}
database.default.password = '{$password}'
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci

app.sessionDriver = CodeIgniter\Session\Handlers\FileHandler
app.CSPEnabled = false
ENV;

    file_put_contents(ROOTPATH . '.env', $env);

    // Buat file lock installer
    file_put_contents(
    ROOTPATH . '.installed',
    json_encode([
        'installed_at'=>date('Y-m-d H:i:s'),
        'php'=>PHP_VERSION
    ])
);
    $mysqli->close();

    $success = true;
    $message = "Instalasi berhasil.";

} catch(Exception $e){

    if(isset($mysqli)){
        $mysqli->close();
    }

    $message=$e->getMessage();
}

}

?>


<!doctype html>
<html>
<head>

<meta charset="utf-8">

<title>Install Jurnal Mengajar Guru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>Install Aplikasi Jurnal Mengajar Guru</h4>

</div>

<div class="card-body">

<?php if($message): ?>

<div class="alert <?= $success ? 'alert-success':'alert-danger' ?>">

<?= $message ?>

</div>

<?php endif; ?>

<?php if(!$success): ?>

<form method="post">

<div class="mb-3">
<label>Host</label>
<input class="form-control" name="host" value="localhost">
</div>

<div class="mb-3">
<label>Database</label>
<input class="form-control" name="database" placeholder="nama database">
</div>

<div class="mb-3">
<label>Username</label>
<input class="form-control" name="username">
<small>Default : root</small>
</div>

<div class="mb-3">
<label>Password</label>
<input class="form-control" type="password" name="password">
<small>Kosongkan *)</small>
</div>

<div class="mb-3">
<label>Base URL</label>
<input class="form-control" name="baseurl"
value="<?= $base = (
    isset($_SERVER['HTTPS']) ? 'https' : 'http'
) . '://' .
$_SERVER['HTTP_HOST'] .
rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/'; ?>">
</div>

<button class="btn btn-primary w-100">

Install

</button>

</form>

<?php else: ?>

<a href="../" class="btn btn-success">

Masuk ke Aplikasi

</a>

<?php endif; ?>

</div>

</div>

</div>

</div>

</div>

</body>

</html>