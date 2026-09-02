
<?= view('template/header'); ?>

<div class="container py-4">

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-warning">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('jurnal/update/'.$jurnal['id']) ?>" method="post">
        <?= csrf_field() ?>

        <input type="hidden"
        name="tahun_pelajaran_id"
        value="<?= $tahunAktif['id'] ?? '' ?>">

        <input type="hidden"
        name="semester_id"
        value="<?= $semesterAktif['id'] ?? '' ?>">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square"></i>
                    Edit Jurnal Mengajar
                </h5>
            </div>

            <div class="card-body">

                <div class="row mb-4">

                    <div class="col-md-6">
                        <div class="alert alert-primary mb-0">
                            <strong>Tahun Pelajaran :</strong>
                            <?= $tahunAktif['tahun'] ?? '-' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="alert alert-success mb-0">
                            <strong>Semester :</strong>
                            <?= $semesterAktif['semester'] ?? '-' ?>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Kelas
                        </label>

                        <select name="kelas_id"
                        class="form-select"
                        required>

                        <option value="">Pilih Kelas</option>

                        <?php foreach($kelas as $k): ?>

                            <option value="<?= $k['id'] ?>"
                                <?= ($jurnal['kelas_id'] == $k['id']) ? 'selected' : '' ?>>
                                <?= esc($k['nama_kelas']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">
                        Mata Pelajaran
                    </label>

                    <select name="mapel_id"
                    class="form-select"
                    required>

                    <option value="">Pilih Mata Pelajaran</option>

                    <?php foreach($mapel as $m): ?>

                        <option value="<?= $m['id'] ?>"
                            <?= ($jurnal['mapel_id'] == $m['id']) ? 'selected' : '' ?>>
                            <?= esc($m['nama_mapel']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Tanggal
                </label>

                <input type="date"
                name="tanggal"
                class="form-control"
                value="<?= $jurnal['tanggal'] ?>"
                required>

            </div>

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Jam Ke
                </label>

                <input type="text"
                name="jam_ke"
                class="form-control"
                value="<?= $jurnal['jam_ke'] ?>"
                required>

            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Materi Pembelajaran
            </label>

            <textarea
            name="materi"
            rows="4"
            class="form-control"
            required><?= esc($jurnal['materi']) ?></textarea>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Refleksi Pembelajaran
            </label>

            <textarea
            name="keterangan"
            rows="3"
            class="form-control"
            placeholder="Refleksi atas kegiatan pembelajaran hari ini (kendala, capaian, tindak lanjut, dsb.)"><?= esc($jurnal['keterangan']) ?></textarea>

        </div>

        <div class="d-flex justify-content-between">

            <a href="<?= base_url('jurnal') ?>"
             class="btn btn-secondary">
             <i class="bi bi-arrow-left"></i>
             Kembali
         </a>

         <button type="submit"
         class="btn btn-warning">

         <i class="bi bi-save"></i>
         Update Jurnal

     </button>

 </div>

</div>

</div>




</form>

</div>
<?= view('template/footer'); ?>