<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        Tambah Pengguna
    </div>

    <div class="card-body">

        <form action="<?= base_url('user/simpan') ?>"
              method="post">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Nama</label>
                    <input type="text"
                           name="nama"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>NIP</label>
                    <input type="text"
                           name="nip"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label>No HP</label>
                    <input type="text"
                           name="no_hp"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">

                    <label>Role</label>

                    <select name="role_id"
                            id="role_id"
                            class="form-select">

                        <option value="1">Administrator</option>
                        <option value="2">Guru</option>
                        <option value="3">Kepala Sekolah</option>
                        <option value="4">Petugas Absen (Siswa)</option>
                        <option value="5">Petugas Absen Sholat (Siswa)</option>

                    </select>

                </div>

                <!-- Khusus role Petugas Absen (4) / Petugas Absen Sholat (5) -->
                <div class="col-md-6 mb-3" id="wrapKelasPetugas" style="display:none;">

                    <label>Kelas (khusus Petugas Absen)</label>

                    <select name="kelas_id"
                            id="kelas_id"
                            class="form-select">

                        <option value="">Pilih Kelas</option>

                        <?php foreach($kelas as $k): ?>

                        <option value="<?= $k['id'] ?>">
                            <?= esc($k['nama_kelas']) ?>
                        </option>

                        <?php endforeach ?>

                    </select>

                </div>

                <!-- Khusus role Guru (2): jabatan tambahan, bisa lebih dari satu -->
                <div class="col-md-12 mb-3" id="wrapJabatan" style="display:none;">

                    <label class="d-block mb-2">
                        Jabatan Tambahan
                        <small class="text-muted">(centang kalau ada, boleh lebih dari satu)</small>
                    </label>

                    <div class="d-flex flex-wrap gap-3">

                        <?php foreach($jabatan as $j): ?>

                        <div class="form-check">
                            <input class="form-check-input jabatan-checkbox"
                                   type="checkbox"
                                   name="jabatan_id[]"
                                   value="<?= $j['id'] ?>"
                                   id="jabatan_<?= $j['id'] ?>"
                                   data-nama="<?= esc(strtolower($j['nama_jabatan'])) ?>">

                            <label class="form-check-label" for="jabatan_<?= $j['id'] ?>">
                                <?= esc($j['nama_jabatan']) ?>
                            </label>
                        </div>

                        <?php endforeach; ?>

                    </div>

                    <!-- Muncul kalau jabatan "Wali Kelas" dicentang -->
                    <div class="mt-3" id="wrapKelasWali" style="display:none;">

                        <label>Wali Kelas untuk Kelas</label>

                        <select name="kelas_wali_id" class="form-select">

                            <option value="">Pilih Kelas</option>

                            <?php foreach($kelas as $k): ?>

                            <option value="<?= $k['id'] ?>">
                                <?= esc($k['nama_kelas']) ?>
                            </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Muncul kalau jabatan "Ketua Jurusan" dicentang -->
                    <div class="mt-3" id="wrapJurusanKetua" style="display:none;">

                        <label>Jurusan yang Diampu</label>

                        <select name="jurusan_ketua" class="form-select">

                            <option value="">Pilih Jurusan</option>

                            <?php foreach($jurusanList as $j): ?>

                            <option value="<?= esc($j['nama_jurusan']) ?>">
                                <?= esc($j['nama_jurusan']) ?>
                            </option>

                            <?php endforeach; ?>

                        </select>

                        <small class="text-muted">
                            Belum ada jurusan yang cocok? Tambahkan dulu lewat menu
                            <b>Master Data &rarr; Jurusan</b>.
                        </small>

                    </div>

                </div>

            </div>

            <button class="btn btn-primary">
                Simpan
            </button>

        </form>

    </div>

</div>
</div>

<script>
function toggleField(){

    var role = document.getElementById('role_id').value;

    document.getElementById('wrapKelasPetugas').style.display =
        (role == '4' || role == '5') ? 'block' : 'none';

    document.getElementById('wrapJabatan').style.display =
        (role == '2') ? 'block' : 'none';

    toggleKelasWali();
}

function toggleKelasWali(){

    var checkboxes = document.querySelectorAll('.jabatan-checkbox');
    var adaWaliKelas = false;
    var adaKetuaJurusan = false;

    checkboxes.forEach(function(cb){
        if(cb.checked && cb.getAttribute('data-nama') === 'wali kelas'){
            adaWaliKelas = true;
        }
        if(cb.checked && cb.getAttribute('data-nama') === 'ketua jurusan'){
            adaKetuaJurusan = true;
        }
    });

    document.getElementById('wrapKelasWali').style.display = adaWaliKelas ? 'block' : 'none';
    document.getElementById('wrapJurusanKetua').style.display = adaKetuaJurusan ? 'block' : 'none';
}

document.getElementById('role_id').addEventListener('change', toggleField);

document.querySelectorAll('.jabatan-checkbox').forEach(function(cb){
    cb.addEventListener('change', toggleKelasWali);
});

window.addEventListener('DOMContentLoaded', toggleField);
</script>

<?= view('template/footer') ?>
