<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5 class="mb-0">Tambah Bulan — <?= esc($kelas['nama_kelas']) ?></h5>
    </div>

    <div class="card-body">

        <form action="<?= base_url('minggu-efektif/store') ?>" method="post">

            <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select" required>
                    <option value="">-- Pilih Bulan --</option>
                    <?php foreach ([
                        'Juli','Agustus','September','Oktober','November','Desember',
                        'Januari','Februari','Maret','April','Mei','Juni'
                    ] as $bulan): ?>
                        <option value="<?= $bulan ?>"><?= $bulan ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Minggu</label>
                    <input type="number" name="jumlah_minggu" class="form-control" min="0" value="4" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Minggu Tidak Efektif</label>
                    <input type="number" name="minggu_tidak_efektif" class="form-control" min="0" value="0" required>
                    <small class="text-muted">Contoh: libur semester, PTS, PAS, ujian, dll.</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Keterangan (opsional)</label>
                <input type="text" name="keterangan" class="form-control"
                       placeholder="Contoh: Libur Idul Fitri, Penilaian Tengah Semester">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('minggu-efektif/detail/' . $kelas['id']) ?>" class="btn btn-secondary">Batal</a>

        </form>

    </div>

</div>
</div>
<?= view('template/footer') ?>
