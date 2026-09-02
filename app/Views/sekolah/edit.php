<?= view('template/header') ?>

<style>
.card{
    border-radius:18px;
}

.card-header{
    border-radius:18px 18px 0 0 !important;
    font-weight:600;
}

.form-control{
    border-radius:10px;
    min-height:45px;
}

.form-control:focus{
    box-shadow:none;
    border-color:#0d6efd;
}

textarea.form-control{
    min-height:100px;
}

.btn{
    border-radius:10px;
}

.logo-preview{
    width:180px;
    height:180px;
    object-fit:contain;
    border:4px solid #f1f1f1;
    border-radius:15px;
    padding:10px;
    background:#fff;
}
</style>

<div class="container py-4">

<form action="<?= base_url('sekolah/update') ?>"
      method="post"
      enctype="multipart/form-data">

<input type="hidden"
       name="id"
       value="<?= $sekolah['id'] ?>">

<div class="row">

    <!-- Logo -->

    <div class="col-lg-4 mb-4">

        <div class="card shadow border-0 mb-4">

            <div class="card-header bg-white">
                Logo Pemerintah / Provinsi
            </div>

            <div class="card-body text-center">

                <?php if(!empty($sekolah['logo_provinsi'])): ?>

                    <img
                        src="<?= base_url('assets/img/'.$sekolah['logo_provinsi']) ?>"
                        class="logo-preview mb-3"
                        id="preview-provinsi">

                <?php else: ?>

                    <img
                        src="<?= base_url('assets/img/default-school.png') ?>"
                        class="logo-preview mb-3"
                        id="preview-provinsi">

                <?php endif; ?>

                <input
                    type="file"
                    class="form-control"
                    name="logo_provinsi"
                    accept="image/*"
                    onchange="previewLogo(event, 'preview-provinsi')">

                <small class="text-muted">
                    Format PNG, tampil di kop surat sebelah kiri
                </small>

            </div>

        </div>

        <div class="card shadow border-0">

            <div class="card-header bg-white">
                Logo Sekolah
            </div>

            <div class="card-body text-center">

                <?php if(!empty($sekolah['logo'])): ?>

                    <img
                        src="<?= base_url('assets/img/'.$sekolah['logo']) ?>"
                        class="logo-preview mb-3"
                        id="preview">

                <?php else: ?>

                    <img
                        src="<?= base_url('assets/img/default-school.png') ?>"
                        class="logo-preview mb-3"
                        id="preview">

                <?php endif; ?>

                <input
                    type="file"
                    class="form-control"
                    name="logo"
                    accept="image/*"
                    onchange="previewLogo(event, 'preview')">

                <small class="text-muted">
                    Format PNG, tampil di kop surat sebelah kanan
                </small>

            </div>

        </div>

    </div>

    <!-- Form -->

    <div class="col-lg-8">

        <div class="card shadow border-0">

            <div class="card-header bg-white">
                Informasi Sekolah
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8 mb-3">
                        <label>Nama Pemerintah (baris atas kop surat)</label>
                        <input
                            type="text"
                            name="nama_pemerintah"
                            class="form-control"
                            placeholder="Contoh: PEMERINTAH PROVINSI KALIMANTAN SELATAN"
                            value="<?= esc($sekolah['nama_pemerintah'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>NSS</label>
                        <input
                            type="text"
                            name="nss"
                            class="form-control"
                            value="<?= esc($sekolah['nss'] ?? '') ?>">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label>Nama Dinas</label>
                        <input
                            type="text"
                            name="nama_dinas"
                            class="form-control"
                            placeholder="Contoh: DINAS PENDIDIKAN DAN KEBUDAYAAN"
                            value="<?= esc($sekolah['nama_dinas'] ?? '') ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Website</label>
                        <input
                            type="text"
                            name="website"
                            class="form-control"
                            placeholder="www.contoh-sekolah.sch.id"
                            value="<?= esc($sekolah['website'] ?? '') ?>">
                    </div>

                    <div class="col-md-8 mb-3">
                        <label>Nama Sekolah</label>
                        <input
                            type="text"
                            name="nama_sekolah"
                            class="form-control"
                            value="<?= esc($sekolah['nama_sekolah']) ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>NPSN</label>
                        <input
                            type="text"
                            name="npsn"
                            class="form-control"
                            value="<?= esc($sekolah['npsn']) ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Kompetensi Keahlian</label>
                        <textarea
                            name="kompetensi_keahlian"
                            class="form-control"
                            placeholder="Satu jurusan per baris, contoh:&#10;TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI&#10;AKUNTANSI DAN KEUANGAN LEMBAGA&#10;TEKNIK OTOMOTIF&#10;TEKNIK PENGELASAN & FABRIKASI LOGAM"><?= esc($sekolah['kompetensi_keahlian'] ?? '') ?></textarea>
                        <small class="text-muted">
                            Satu jurusan per baris. Akan tampil 2 kolom di kop surat (baris ganjil kiri, baris genap kanan).
                        </small>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Alamat</label>
                        <textarea
                            name="alamat"
                            class="form-control"><?= esc($sekolah['alamat']) ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Desa / Kelurahan</label>
                        <input
                            type="text"
                            name="desa"
                            class="form-control"
                            value="<?= esc($sekolah['desa']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kecamatan</label>
                        <input
                            type="text"
                            name="kecamatan"
                            class="form-control"
                            value="<?= esc($sekolah['kecamatan']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kabupaten</label>
                        <input
                            type="text"
                            name="kabupaten"
                            class="form-control"
                            value="<?= esc($sekolah['kabupaten']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Provinsi</label>
                        <input
                            type="text"
                            name="provinsi"
                            class="form-control"
                            value="<?= esc($sekolah['provinsi']) ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Kode Pos</label>
                        <input
                            type="text"
                            name="kode_pos"
                            class="form-control"
                            value="<?= esc($sekolah['kode_pos']) ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Telepon</label>
                        <input
                            type="text"
                            name="telepon"
                            class="form-control"
                            value="<?= esc($sekolah['telepon']) ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= esc($sekolah['email']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kepala Sekolah</label>
                        <input
                            type="text"
                            name="kepala_sekolah"
                            class="form-control"
                            value="<?= esc($sekolah['kepala_sekolah']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>NIP Kepala Sekolah</label>
                        <input
                            type="text"
                            name="nip_kepala"
                            class="form-control"
                            value="<?= esc($sekolah['nip_kepala']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Latitude</label>
                        <input
                            type="text"
                            name="latitude"
                            class="form-control"
                            value="<?= esc($sekolah['latitude']) ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Longitude</label>
                        <input
                            type="text"
                            name="longitude"
                            class="form-control"
                            value="<?= esc($sekolah['longitude']) ?>">
                    </div>

                </div>

            </div>

            <div class="card-footer bg-white text-end">

                <a href="<?= base_url('sekolah') ?>"
                   class="btn btn-secondary">

                    <i class="fa fa-arrow-left"></i>

                    Kembali

                </a>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa fa-save"></i>

                    Simpan Perubahan

                </button>

            </div>

        </div>

    </div>

</div>

</form>

</div>

<script>

function previewLogo(event, targetId){

    const preview = document.getElementById(targetId || 'preview');

    preview.src = URL.createObjectURL(event.target.files[0]);

}

</script>

<?= view('template/footer') ?>