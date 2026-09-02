<?= view('template/header') ?>

<style>
.card{
    border-radius:18px;
}

.card-header{
    border-radius:18px 18px 0 0 !important;
    font-weight:600;
}

.btn{
    border-radius:10px;
}

.logo-sekolah{
    width:170px;
    height:170px;
    object-fit:contain;
    border:5px solid #f8f9fa;
    border-radius:20px;
    background:#fff;
    padding:10px;
}

.table th{
    width:220px;
    color:#555;
}

.badge-info{
    font-size:13px;
    padding:8px 14px;
}
</style>

<div class="container py-4">

    <div class="row">

        <!-- LOGO SEKOLAH -->
        <div class="col-lg-4 mb-4">

            <div class="card shadow border-0">

                <div class="card-body text-center">

                    <?php if(!empty($sekolah['logo'])): ?>

                        <img
                            src="<?= base_url('assets/img/'.$sekolah['logo']) ?>"
                            class="logo-sekolah shadow">

                    <?php else: ?>

                        <img
                            src="<?= base_url('assets/img/default-school.png') ?>"
                            class="logo-sekolah shadow">

                    <?php endif; ?>

                    <h4 class="mt-4 mb-1">

                        <?= esc($sekolah['nama_sekolah']) ?>

                    </h4>

                    <span class="badge bg-primary badge-info">

                        NPSN :
                        <?= esc($sekolah['npsn']) ?>

                    </span>

                    <hr>

                    <a href="<?= base_url('sekolah/edit') ?>"
                        class="btn btn-primary w-100">

                        <i class="fa fa-edit"></i>

                        Edit Profil Sekolah

                    </a>

                </div>

            </div>

        </div>

        <!-- DETAIL SEKOLAH -->

        <div class="col-lg-8">

            <div class="card shadow border-0">

                <div class="card-header bg-white">

                    <h5 class="mb-0">

                        Informasi Sekolah

                    </h5>

                </div>

                <div class="card-body">

                    <table class="table table-borderless">

                        <tr>
                            <th>Nama Sekolah</th>
                            <td><?= esc($sekolah['nama_sekolah']) ?></td>
                        </tr>

                        <tr>
                            <th>NPSN</th>
                            <td><?= esc($sekolah['npsn']) ?></td>
                        </tr>

                        <tr>
                            <th>Alamat</th>
                            <td><?= esc($sekolah['alamat']) ?></td>
                        </tr>

                        <tr>
                            <th>Desa / Kelurahan</th>
                            <td><?= esc($sekolah['desa']) ?></td>
                        </tr>

                        <tr>
                            <th>Kecamatan</th>
                            <td><?= esc($sekolah['kecamatan']) ?></td>
                        </tr>

                        <tr>
                            <th>Kabupaten</th>
                            <td><?= esc($sekolah['kabupaten']) ?></td>
                        </tr>

                        <tr>
                            <th>Provinsi</th>
                            <td><?= esc($sekolah['provinsi']) ?></td>
                        </tr>

                        <tr>
                            <th>Kode Pos</th>
                            <td><?= esc($sekolah['kode_pos']) ?></td>
                        </tr>

                        <tr>
                            <th>Telepon</th>
                            <td><?= esc($sekolah['telepon']) ?></td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><?= esc($sekolah['email']) ?></td>
                        </tr>

                        <tr>
                            <th>Kepala Sekolah</th>
                            <td><?= esc($sekolah['kepala_sekolah']) ?></td>
                        </tr>

                        <tr>
                            <th>NIP Kepala Sekolah</th>
                            <td><?= esc($sekolah['nip_kepala']) ?></td>
                        </tr>

                        <tr>
                            <th>Latitude</th>
                            <td><?= esc($sekolah['latitude']) ?></td>
                        </tr>

                        <tr>
                            <th>Longitude</th>
                            <td><?= esc($sekolah['longitude']) ?></td>
                        </tr>

                        <tr>
                            <th>Dibuat</th>
                            <td>

                                <?= !empty($sekolah['created_at'])
                                    ? date('d F Y H:i', strtotime($sekolah['created_at']))
                                    : '-' ?>

                            </td>
                        </tr>

                        <tr>
                            <th>Terakhir Diubah</th>
                            <td>

                                <?= !empty($sekolah['updated_at'])
                                    ? date('d F Y H:i', strtotime($sekolah['updated_at']))
                                    : '-' ?>

                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<?= view('template/footer') ?>