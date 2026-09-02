<?= view('template/header') ?>

<div class="container-fluid py-4">

    <div class="row mb-3">
        <div class="col-md-8">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-clipboard-data text-primary"></i>
                Rekap Hasil Pembinaan per Guru Wali
            </h3>
            <p class="text-muted mb-0">
                Tahun Pelajaran: <strong><?= esc($tahunAktif['tahun'] ?? '-') ?></strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= base_url('kesiswaan/rekap/cetak') ?>" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-bordered table-striped" id="tabelRekap">
                <thead class="table-light">
                    <tr>
                        <th>Guru Wali</th>
                        <th>Jumlah Siswa Asuh</th>
                        <th>Jumlah Kasus Pembinaan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rekap as $r): ?>
                    <tr>
                        <td><?= esc($r['nama_guru_wali']) ?></td>
                        <td class="text-center"><?= $r['jumlah_siswa_asuh'] ?></td>
                        <td class="text-center fw-bold <?= $r['jumlah_pembinaan'] > 0 ? 'text-danger' : '' ?>">
                            <?= $r['jumlah_pembinaan'] ?>
                        </td>
                    </tr>
                    <?php endforeach ?>

                    <?php if (empty($rekap)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Belum ada guru wali dengan siswa asuh.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
$(function () {
    $('#tabelRekap').DataTable();
});
</script>

<?= view('template/footer') ?>
