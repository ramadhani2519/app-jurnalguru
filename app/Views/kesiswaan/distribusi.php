<?= view('template/header') ?>

<div class="container-fluid py-4">

    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-diagram-3 text-primary"></i>
                Distribusi Siswa ke Guru Wali
            </h3>
            <p class="text-muted mb-0">
                Bagikan siswa (lintas kelas) ke guru yang menjabat sebagai Guru Wali untuk keperluan bimbingan/pembinaan.
            </p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (empty($guruWali)): ?>
        <div class="alert alert-warning">
            Belum ada guru dengan jabatan <strong>"Guru Wali"</strong>.
            Tambahkan jabatan ini untuk guru terkait di menu
            <a href="<?= base_url('user') ?>">Data Pengguna</a> terlebih dahulu.
        </div>
    <?php endif; ?>

    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">
            Filter
        </div>

        <div class="card-body">
            <form method="get" action="<?= base_url('kesiswaan/distribusi') ?>" class="row g-2 mb-3">

                <div class="col-md-3">
                    <label>Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= ($kelas_id == $k['id']) ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelas']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Guru Wali</label>
                    <select name="guru_id" class="form-select">
                        <option value="">Semua Guru Wali</option>
                        <?php foreach ($guruWali as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($guru_id == $g['id']) ? 'selected' : '' ?>>
                                <?= esc($g['nama']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="" <?= ($status == '') ? 'selected' : '' ?>>Semua Status</option>
                        <option value="belum" <?= ($status == 'belum') ? 'selected' : '' ?>>Belum Dibagi Saja</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Terapkan
                    </button>
                </div>

            </form>

            <form method="post" action="<?= base_url('kesiswaan/distribusi/simpan') ?>" id="formBagikan">

                <div class="row g-2 mb-3 align-items-end">
                    <div class="col-md-4">
                        <label>Bagikan siswa terpilih ke Guru Wali</label>
                        <select name="guru_id" class="form-select" required <?= empty($guruWali) ? 'disabled' : '' ?>>
                            <option value="">Pilih Guru Wali</option>
                            <?php foreach ($guruWali as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success" <?= empty($guruWali) ? 'disabled' : '' ?>
                                onclick="return confirm('Bagikan siswa terpilih ke guru wali ini?');">
                            <i class="bi bi-check2-circle"></i> Bagikan Siswa Terpilih
                        </button>
                    </div>
                </div>

                <table class="table table-bordered table-hover" id="tabelSiswa">
                    <thead class="table-light">
                        <tr>
                            <th style="width:30px;">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Guru Wali Saat Ini</th>
                            <th style="width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftarSiswa as $s): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="siswa_id[]" value="<?= $s['siswa_id'] ?>" class="chkSiswa">
                            </td>
                            <td><?= esc($s['nis']) ?></td>
                            <td><?= esc($s['nama_siswa']) ?></td>
                            <td><?= esc($s['nama_kelas']) ?></td>
                            <td>
                                <?php if (!empty($s['guru_id'])): ?>
                                    <span class="badge bg-success"><?= esc($s['nama_guru_wali']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Dibagi</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($s['distribusi_id'])): ?>
                                    <a href="<?= base_url('kesiswaan/distribusi/hapus/' . $s['distribusi_id']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Batalkan pembagian siswa ini?');">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach ?>

                        <?php if (empty($daftarSiswa)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada data siswa.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </form>

        </div>
    </div>

</div>

<script>
document.getElementById('checkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkSiswa').forEach(cb => cb.checked = this.checked);
});
</script>

<?= view('template/footer') ?>
