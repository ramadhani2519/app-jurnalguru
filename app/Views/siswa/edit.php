<?= view('template/header') ?>

<div class="container py-4">

    <div class="card border-0 shadow">

        <div class="card-header bg-warning">

            <h5 class="mb-0 text-dark">
                <i class="bi bi-pencil-square"></i>
                Edit Data Siswa
            </h5>

        </div>

        <div class="card-body">

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('siswa/update/'.$siswa['id']) ?>"
                  method="post">

                <?= csrf_field() ?>

                <div class="row">

                    <!-- NIS -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            NIS
                        </label>

                        <input type="text"
                               name="nis"
                               class="form-control"
                               value="<?= old('nis',$siswa['nis']) ?>"
                               required>

                    </div>

                    <!-- Nama -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Nama Siswa
                        </label>

                        <input type="text"
                               name="nama_siswa"
                               class="form-control"
                               value="<?= old('nama_siswa',$siswa['nama_siswa']) ?>"
                               required>

                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Jenis Kelamin
                        </label>

                        <select name="jk"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Jenis Kelamin
                            </option>

                            <option value="L"
                                <?= ($siswa['jk'] == 'L') ? 'selected' : '' ?>>
                                Laki-Laki
                            </option>

                            <option value="P"
                                <?= ($siswa['jk'] == 'P') ? 'selected' : '' ?>>
                                Perempuan
                            </option>

                        </select>

                    </div>

                    <!-- Kelas -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Kelas
                        </label>

                        <select name="kelas_id"
                                class="form-select"
                                required>

                            <option value="">
                                Pilih Kelas
                            </option>

                            <?php foreach($kelas as $k): ?>

                                <option value="<?= $k['id'] ?>"
                                    <?= ($k['id'] == $siswa['kelas_id']) ? 'selected' : '' ?>>

                                    <?= esc($k['nama_kelas']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Tempat Lahir -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Tempat Lahir
                        </label>

                        <input type="text"
                               name="tempat_lahir"
                               class="form-control"
                               value="<?= old('tempat_lahir',$siswa['tempat_lahir']) ?>">

                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Tanggal Lahir
                        </label>

                        <input type="date"
                               name="tanggal_lahir"
                               class="form-control"
                               value="<?= old('tanggal_lahir',$siswa['tanggal_lahir']) ?>">

                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-semibold">
                            Alamat
                        </label>

                        <textarea name="alamat"
                                  rows="4"
                                  class="form-control"><?= old('alamat',$siswa['alamat']) ?></textarea>

                    </div>

                </div>

                <div class="d-flex justify-content-between">

                    <a href="<?= base_url('siswa') ?>"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>
                        Kembali

                    </a>

                    <button type="submit"
                            class="btn btn-warning">

                        <i class="bi bi-save"></i>
                        Update Data

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?= view('template/footer') ?>
