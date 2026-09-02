<?= view('template/header') ?>

<div class="container-fluid py-4">

    <h3 class="fw-bold mb-1">
        <i class="bi bi-shield-check text-primary"></i>
        Status Pembinaan Siswa
    </h3>
    <p class="text-muted">
        Semua siswa (semua jurusan) yang punya catatan pelanggaran, lengkap dengan
        sejauh mana tindak lanjutnya (Guru Wali, Wali Kelas, Ketua Jurusan).
    </p>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white">
                    Monitoring Siswa Bermasalah (<?= count($daftarSiswa) ?>)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="tabelStatusSiswa">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th class="text-center">Pelanggaran</th>
                                <th class="text-center">Guru Wali</th>
                                <th class="text-center">Wali Kelas</th>
                                <th class="text-center">Ketua Jurusan</th>
                                <th>Status</th>
                                <th width="90">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarSiswa as $s): ?>
                            <tr>
                                <td><?= esc($s['nis'] ?? '-') ?></td>
                                <td><?= esc($s['nama_siswa']) ?></td>
                                <td><?= esc($s['nama_kelas'] ?? '-') ?></td>
                                <td><?= esc($s['jurusan'] ?? '-') ?></td>
                                <td class="text-center"><?= $s['jumlah_pelanggaran'] ?></td>
                                <td class="text-center">
                                    <?= $s['sudah_guru_wali'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td class="text-center">
                                    <?= $s['sudah_wali_kelas'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td class="text-center">
                                    <?= $s['sudah_ketua_jurusan'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash-circle text-muted"></i>' ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= esc($s['status_warna']) ?>"><?= esc($s['status_label']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('kesiswaan/pembinaan/detail/'.$s['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($daftarSiswa)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Belum ada siswa dengan catatan pelanggaran.
                                </td>
                            </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-danger text-white">
                    Riwayat Pembinaan Terbaru (Semua Jurusan)
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Tingkat</th>
                                <th>Tindak Lanjut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayatPembinaan as $r): ?>
                            <tr>
                                <td class="text-nowrap"><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                                <td><?= esc($r['nama_siswa'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $r['tingkat_warna'] ?>"><?= $r['tingkat_label'] ?></span>
                                    <div class="text-muted small"><?= esc($r['nama_penindak']) ?></div>
                                </td>
                                <td><?= esc($r['tindak_lanjut']) ?></td>
                            </tr>
                            <?php endforeach ?>

                            <?php if (empty($riwayatPembinaan)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada catatan pembinaan.
                                </td>
                            </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
$(function () {
    $('#tabelStatusSiswa').DataTable();
});
</script>

<?= view('template/footer') ?>
