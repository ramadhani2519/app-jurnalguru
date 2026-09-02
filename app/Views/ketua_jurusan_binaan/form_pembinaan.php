<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow border-0">

<div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
        <i class="bi bi-clipboard-heart"></i>
        Catat Tindak Lanjut Pembinaan (Ketua Jurusan)
    </h5>
    <a href="<?= base_url('ketua-jurusan-binaan/siswa') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card-body">

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle"></i>
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif ?>

<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
    <div>
        <strong><?= esc($siswa['nama_siswa']) ?></strong> (<?= esc($siswa['nis']) ?> — <?= esc($siswa['nama_kelas']) ?>)
        sudah tercatat <strong><?= $jumlahPelanggaran ?> kali</strong> di data pelanggaran, sudah pernah dibina
        Guru Wali dan Wali Kelas, dan sekarang memerlukan tindakan pembinaan dari Ketua Jurusan.
    </div>
</div>

<form method="post" action="<?= base_url('ketua-jurusan-binaan/pembinaan/simpan') ?>" enctype="multipart/form-data">

    <input type="hidden" name="siswa_id" value="<?= esc($siswa['id']) ?>">

    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label fw-bold">Tanggal Pembinaan</label>
            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

    </div>

    <div class="mb-3 mt-3">
        <label class="form-label fw-bold">Tindak Lanjut / Hasil Pembinaan</label>
        <textarea name="tindak_lanjut" rows="4" class="form-control" required
            placeholder="Uraikan tindakan pembinaan yang dilakukan, kesepakatan dengan siswa, rencana pemantauan, dsb."><?= old('tindak_lanjut') ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Foto Bukti Pembinaan</label>
        <input type="file" name="foto" class="form-control" accept="image/*" required>
        <small class="text-muted">Wajib diunggah sebagai bukti kegiatan pembinaan sudah dilaksanakan.</small>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="<?= base_url('ketua-jurusan-binaan/siswa') ?>" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-save"></i> Simpan Tindak Lanjut
        </button>
    </div>

</form>

</div>
</div>
</div>

<?= view('template/footer') ?>
