<?= view('template/header') ?>

<style type="text/css">
    .card{
    border-radius:18px;
}

.card-header{
    border-radius:18px 18px 0 0 !important;
    font-weight:600;
}

.form-control{
    border-radius:10px;
    min-height:45px;
}

.btn{
    border-radius:10px;
}

.rounded-circle{
    border:5px solid #f8f9fa;
}
</style>

<?php
$roles = [
    1 => ['Administrator', 'bg-danger'],
    2 => ['Guru', 'bg-primary'],
    3 => ['Kepala Sekolah', 'bg-success'],
];

$role = $roles[session()->get('role_id')] ?? ['User', 'bg-secondary'];
?>
<div class="container py-4">

    <div class="row">

        <!-- FOTO PROFILE -->
        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-body text-center">

                    <?php if (!empty($user['foto'])) : ?>
                        <img src="<?= base_url('uploads/profile/' . $user['foto']) ?>"
                            class="rounded-circle shadow mb-3"
                            width="160"
                            height="160"
                            style="object-fit:cover;">
                    <?php else : ?>
                        <img src="<?= base_url('assets/img/default.png') ?>"
                            class="rounded-circle shadow mb-3"
                            width="160"
                            height="160"
                            style="object-fit:cover;">
                    <?php endif; ?>

                    <h4><?= $user['nama'] ?></h4>

                    <p class="text-muted mb-3">
                        <?= session()->get('role') ?? 'User'; ?>
                    </p>

                    <div class="alert alert-light border">
                        <small>
                            Pilih foto baru jika ingin mengganti foto profil.
                        </small>
                    </div>

                </div>

            </div>

        </div>

        <!-- FORM -->
        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        Edit Profil
                    </h5>
                </div>

                <form action="<?= base_url('profile/update') ?>"
                    method="post"
                    enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Nama Lengkap</label>
                                <input
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    value="<?= old('nama', $user['nama']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>NIP</label>
                                <input
                                    type="text"
                                    name="nip"
                                    class="form-control"
                                    value="<?= old('nip', $user['nip']) ?>">
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    value="<?= old('username', $user['username']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= old('email', $user['email']) ?>">
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>No HP</label>
                                <input
                                    type="text"
                                    name="no_hp"
                                    class="form-control"
                                    value="<?= old('no_hp', $user['no_hp']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Foto Profil</label>
                                <input
                                    type="file"
                                    name="foto"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png">
                            </div>

                        </div>

                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between">

                        <a href="<?= base_url('profile') ?>"
                            class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i>
                            Kembali
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="fa fa-save"></i>
                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<?= view('template/footer') ?>