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

        <!-- PROFILE -->
        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-body text-center">

                    <?php if(!empty($user['foto'])): ?>
                        <img src="<?= base_url('uploads/profile/'.$user['foto']) ?>"
                             class="rounded-circle shadow"
                             width="150"
                             height="150"
                             style="object-fit:cover;">
                    <?php else: ?>
                        <img src="<?= base_url('assets/img/default.png') ?>"
                             class="rounded-circle shadow"
                             width="150"
                             height="150"
                             style="object-fit:cover;">
                    <?php endif; ?>

                    <h4 class="mt-3 mb-0">
                        <?= $user['nama'] ?>
                    </h4>

                    <p class="text-muted">
                        <?= session()->get('role') ?? 'User'; ?>
                    </p>

                    <hr>


                    <a href="<?= base_url('profile/edit') ?>" class="btn btn-primary w-100">
                        <i class="fa fa-user-edit"></i>
                        Edit Profile
                    </a>

                </div>

            </div>

        </div>

        <!-- DETAIL -->
        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        Informasi Profil
                    </h5>
                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>
                            <th width="180">Nama Lengkap</th>
                            <td><?= $user['nama'] ?></td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><?= $user['email'] ?></td>
                        </tr>

                        <tr>
                            <th>Username</th>
                            <td><?= $user['username'] ?></td>
                        </tr>

                        <tr>
                            <th>NIP</th>
                            <td><?= $user['nip'] ?></td>
                        </tr>

                        <tr>
                            <th>No. HP</th>
                            <td><?= $user['no_hp'] ?></td>
                        </tr>

                        <tr>
                            <th>Bergabung</th>
                            <td><?= date('d F Y', strtotime($user['created_at'])) ?></td>
                        </tr>

                    </table>

                </div>

            </div>

        

        </div>

    </div>

</div>

<?= view('template/footer') ?>