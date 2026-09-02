<?= view('template/header'); ?>

<div class="container py-4">

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('jurnal/simpan') ?>" method="post" id="formJurnal" enctype="multipart/form-data">

    <?= csrf_field() ?>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-journal-text"></i>
                Input Jurnal Mengajar Tanggal : <?= date('d-m-Y') ?>
            </h5>
        </div>

        <div class="card-body">

            <input type="hidden"
                   name="tahun_pelajaran_id"
                   value="<?= $tahunAktif['id'] ?? '' ?>">

            <input type="hidden"
                   name="semester_id"
                   value="<?= $semesterAktif['id'] ?? '' ?>">

            <input type="hidden"
                   name="tanggal"
                   value="<?= date('Y-m-d') ?>">

            <!-- Field yang benar-benar dikirim ke server, diisi otomatis via JS -->
            <input type="hidden" name="kelas_id" id="kelas_id" required>
            <input type="hidden" name="mapel_id" id="mapel_id" required>
            <input type="hidden" name="jam_ke" id="jam_ke" required>
            <input type="hidden" name="jam_mulai" id="jam_mulai_hidden">
            <input type="hidden" name="jam_akhir" id="jam_selesai_hidden">

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Jadwal Mengajar Hari Ini
                </label>

                <?php if(empty($jadwalHariIni)): ?>

                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    Tidak ada jadwal mengajar untuk Anda hari
                    <b><?= $namaHariIni ?></b>.
                    Silakan hubungi Admin kalau ini seharusnya ada.
                </div>

                <?php else: ?>

                <select id="pilihJadwal" class="form-select" required>

                    <option value="">Pilih Jadwal</option>

                    <?php foreach($jadwalHariIni as $index => $j): ?>

                        <?php
                            $labelJam = ($j['jam_awal'] == $j['jam_akhir'])
                                ? 'Jam '.$j['jam_awal']
                                : 'Jam '.$j['jam_awal'].'-'.$j['jam_akhir'];

                            $jamKeKirim = ($j['jam_awal'] == $j['jam_akhir'])
                                ? $j['jam_awal']
                                : $j['jam_awal'].'-'.$j['jam_akhir'];
                        ?>

                        <option value="<?= $index ?>"
                            data-kelas="<?= $j['kelas_id'] ?>"
                            data-mapel="<?= $j['mapel_id'] ?>"
                            data-jam="<?= $jamKeKirim ?>"
                            data-mulai="<?= substr($j['jam_mulai'],0,5) ?>"
                            data-selesai="<?= substr($j['jam_selesai'],0,5) ?>"
                            data-nama-kelas="<?= esc($j['nama_kelas']) ?>"
                            data-nama-mapel="<?= esc($j['nama_mapel']) ?>">

                            <?= $labelJam ?> (<?= substr($j['jam_mulai'],0,5) ?>-<?= substr($j['jam_selesai'],0,5) ?>) | <?= esc($j['nama_kelas']) ?> | <?= esc($j['nama_mapel']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

                <div class="row mt-3" id="ringkasanJadwal" style="display:none;">

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Kelas</label>
                        <input type="text" id="tampilKelas" class="form-control" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Mata Pelajaran</label>
                        <input type="text" id="tampilMapel" class="form-control" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Jam Ke</label>
                        <input type="text" id="tampilJam" class="form-control" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted small">Waktu</label>
                        <input type="text" id="tampilWaktu" class="form-control" disabled>
                    </div>

                </div>

                <?php endif; ?>

            </div>

<div class="mb-3">

    <label class="form-label">
        Materi Pembelajaran
    </label>

    <textarea
        name="materi"
        rows="4"
        class="form-control"
        required></textarea>

</div>

<div class="mb-3">

    <label class="form-label">
        Refleksi Pembelajaran
    </label>

    <textarea
        name="keterangan"
        rows="3"
        class="form-control"
        placeholder="Refleksi atas kegiatan pembelajaran hari ini (kendala, capaian, tindak lanjut, dsb.)"></textarea>

</div>

<div class="mb-3">

    <label class="form-label">
        Foto Bukti Mengajar <span class="text-danger">*</span>
    </label>

    <input type="file"
           name="foto"
           id="inputFoto"
           class="form-control"
           accept="image/*"
           capture="environment"
           required>

    <small class="text-muted">
        Di HP, ini akan otomatis membuka kamera. Foto wajib dilampirkan sebagai bukti mengajar.
    </small>

    <div class="mt-2" id="previewWrap" style="display:none;">
        <img id="previewFoto" src="" style="max-width:200px;max-height:200px;border-radius:8px;border:1px solid #ddd;">
    </div>

</div>

<div class="d-flex justify-content-between">

            <a href="<?= base_url('jurnal') ?>"
             class="btn btn-secondary">
             <i class="bi bi-arrow-left"></i>
             Kembali
         </a>

         <button type="submit"
         class="btn btn-primary">

         <i class="bi bi-save"></i>
         Simpan Jurnal

     </button>

 </div>

</div>
</div>

</form>

</div>

<script>
document.getElementById('pilihJadwal')?.addEventListener('change', function () {

    var opt = this.options[this.selectedIndex];

    var kelas     = opt.getAttribute('data-kelas') || '';
    var mapel     = opt.getAttribute('data-mapel') || '';
    var jam       = opt.getAttribute('data-jam') || '';
    var mulai     = opt.getAttribute('data-mulai') || '';
    var selesai   = opt.getAttribute('data-selesai') || '';
    var namaKelas = opt.getAttribute('data-nama-kelas') || '';
    var namaMapel = opt.getAttribute('data-nama-mapel') || '';

    document.getElementById('kelas_id').value = kelas;
    document.getElementById('mapel_id').value = mapel;
    document.getElementById('jam_ke').value = jam;
    document.getElementById('jam_mulai_hidden').value = mulai;
    document.getElementById('jam_selesai_hidden').value = selesai;

    if (kelas && mapel) {
        document.getElementById('tampilKelas').value = namaKelas;
        document.getElementById('tampilMapel').value = namaMapel;
        document.getElementById('tampilJam').value = jam;
        document.getElementById('tampilWaktu').value = mulai && selesai ? (mulai + ' - ' + selesai) : '-';
        document.getElementById('ringkasanJadwal').style.display = 'flex';
    } else {
        document.getElementById('ringkasanJadwal').style.display = 'none';
    }
});

document.getElementById('formJurnal')?.addEventListener('submit', function (e) {

    if (!document.getElementById('kelas_id').value) {
        e.preventDefault();
        alert('Silakan pilih jadwal mengajar terlebih dahulu.');
    }
});

document.getElementById('inputFoto')?.addEventListener('change', function () {

    var preview = document.getElementById('previewFoto');
    var wrap = document.getElementById('previewWrap');

    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            wrap.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
    } else {
        wrap.style.display = 'none';
    }
});
</script>

<?= view('template/footer'); ?>
