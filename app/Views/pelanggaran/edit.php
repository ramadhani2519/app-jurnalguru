<?= view('template/header') ?>

<div class="container py-4">

    <div class="card shadow border-0">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                📋 Edit Catatan Pelanggaran Siswa
            </h5>
        </div>

        <div class="card-body">

            <form action="<?= base_url('pelanggaran/update/' . $pelanggaran['id']) ?>" method="post">

                <?= csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="<?= $pelanggaran['tanggal']; ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kelas</label>

                    <select name="kelas_id" class="form-select" required>

                        <option value="">-- Pilih Kelas --</option>

                        <?php foreach ($kelas as $k) : ?>

                            <option value="<?= $k['id']; ?>"
                                <?= ($k['id'] == $pelanggaran['kelas_id']) ? 'selected' : ''; ?>>
                                <?= esc($k['nama_kelas']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Siswa</label>

                    <select name="siswa_id" class="form-select" required>

                        <option value="">-- Pilih Siswa --</option>

                        <?php foreach ($siswa as $s) : ?>

                            <option value="<?= $s['id']; ?>"
                                <?= ($s['id'] == $pelanggaran['siswa_id']) ? 'selected' : ''; ?>>
                                <?= esc($s['nama_siswa']); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <?php
                    $daftarNamaPelanggaran = array_column($jenisPelanggaran, 'nama_pelanggaran');
                    $isLainnya = !in_array($pelanggaran['uraian_pelanggaran'], $daftarNamaPelanggaran, true);
                ?>

                <div class="mb-3">
                    <label class="form-label">Uraian Pelanggaran</label>

                    <select name="uraian_pelanggaran" id="uraianPelanggaran" class="form-select" required>

                        <option value="">-- Pilih Jenis Pelanggaran --</option>

                        <?php foreach ($jenisPelanggaran as $j) : ?>

                            <option value="<?= esc($j['nama_pelanggaran']); ?>"
                                <?= (!$isLainnya && $j['nama_pelanggaran'] === $pelanggaran['uraian_pelanggaran']) ? 'selected' : ''; ?>>
                                <?= esc($j['nama_pelanggaran']); ?>
                            </option>

                        <?php endforeach; ?>

                        <option value="__lainnya__" <?= $isLainnya ? 'selected' : ''; ?>>
                            Lainnya (ketik sendiri)
                        </option>

                    </select>

                    <div class="mt-2" id="wrapUraianLainnya" style="<?= $isLainnya ? '' : 'display:none;' ?>">
                        <textarea name="uraian_lainnya"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Ketik uraian pelanggaran..."><?= $isLainnya ? esc($pelanggaran['uraian_pelanggaran']) : '' ?></textarea>
                    </div>

                    <script>
                    document.getElementById('uraianPelanggaran').addEventListener('change', function(){
                        document.getElementById('wrapUraianLainnya').style.display =
                            (this.value === '__lainnya__') ? 'block' : 'none';
                    });
                    </script>

                </div>

                <div class="text-end">

                    <a href="<?= base_url('pelanggaran') ?>" class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="submit" class="btn btn-success">
                        💾 Update Data
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?= view('template/footer') ?>