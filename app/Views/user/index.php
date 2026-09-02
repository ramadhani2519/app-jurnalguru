<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between">

        <h5 class="mb-0">
            Data Pengguna
        </h5>

        <a href="<?= base_url('user/tambah') ?>"
           class="btn btn-primary">

            Tambah Pengguna

        </a>

    </div>

    <div class="card-body">

        <table id="tableData"
               class="table table-striped table-hover align-middle">

            <thead>

            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Username</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Role</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>

            </thead>

            <tbody>

            <?php $no=1; ?>
            <?php foreach($user as $u): ?>

            <?php
                switch($u['role_id']){
                    case 1:
                        $roleWarna = 'dark';
                        $roleText  = 'Administrator';
                        break;
                    case 2:
                        $roleWarna = 'primary';
                        $roleText  = 'Guru';
                        break;
                    case 3:
                        $roleWarna = 'success';
                        $roleText  = 'Kepala Sekolah';
                        break;
                    case 4:
                        $roleWarna = 'warning';
                        $roleText  = 'Petugas Absen';
                        break;
                    case 5:
                        $roleWarna = 'info';
                        $roleText  = 'Petugas Absen Sholat';
                        break;
                    default:
                        $roleWarna = 'secondary';
                        $roleText  = '-';
                }
            ?>

            <tr>

                <td><?= $no++ ?></td>

                <td><?= esc($u['nama']) ?></td>

                <td><?= esc($u['nip']) ?></td>

                <td><?= esc($u['username']) ?></td>

                <td><?= esc($u['email']) ?></td>

                <td><?= esc($u['no_hp']) ?></td>

                <td>
                    <span class="badge bg-<?= $roleWarna ?>">
                        <?= $roleText ?>
                    </span>
                </td>

                <td>
                    <?php if(!empty($u['jabatan_list'])): ?>

                        <?php foreach($u['jabatan_list'] as $j): ?>

                            <span class="badge bg-light text-dark border mb-1">
                                <?= esc($j['nama_jabatan']) ?>
                                <?php if(!empty($j['nama_kelas'])): ?>
                                    (<?= esc($j['nama_kelas']) ?>)
                                <?php endif; ?>
                                <?php if(!empty($j['jurusan'])): ?>
                                    (<?= esc($j['jurusan']) ?>)
                                <?php endif; ?>
                            </span>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <span class="text-muted">-</span>

                    <?php endif; ?>
                </td>

                <td>

                    <a href="<?= base_url('user/edit/'.$u['id']) ?>"
                       class="btn btn-warning btn-sm">

                        Edit

                    </a>

                    <a href="<?= base_url('user/hapus/'.$u['id']) ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Hapus data?')">

                        Hapus

                    </a>

                </td>

            </tr>

            <?php endforeach ?>

            </tbody>

        </table>

    </div>

</div>
</div>
<?= view('template/footer') ?>
