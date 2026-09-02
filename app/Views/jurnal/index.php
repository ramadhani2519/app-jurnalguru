<?= view('template/header'); ?>

<div class="container py-4">

<div class="row mb-4">

    <div class="col-md-8">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-journal-bookmark-fill text-primary"></i>
            Jurnal Mengajar
        </h3>

        <p class="text-muted mb-0">
            Data jurnal kegiatan mengajar guru
        </p>
    </div>

    <div class="col-md-4 text-md-end mt-3 mt-md-0">

        <a href="<?= base_url('jurnal/export-excel?' . http_build_query([
            'guru_id'  => $guru_id,
            'mapel_id' => $mapel_id,
            'kelas_id' => $kelas_id,
        ])) ?>"
           class="btn btn-success" target="_blank">

            <i class="bi bi-file-earmark-excel"></i>
            Unduh Excel

        </a>

        <a href="<?= base_url('jurnal/tambah') ?>"
           class="btn btn-primary">

            <i class="bi bi-plus-circle"></i>
            Tambah Jurnal

        </a>

    </div>

</div>

<!-- Statistik -->

<div class="row mb-4">

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <small class="text-muted">
                    Total Jurnal
                </small>

                <h3 class="mb-0">
                    <?= count($jurnal) ?>
                </h3>

            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <small class="text-muted">
                    Tahun Pelajaran
                </small>

                <h5 class="mb-0">
                    <?= $tahunAktif['tahun'] ?? '-' ?>
                </h5>

            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <small class="text-muted">
                    Semester
                </small>

                <h5 class="mb-0">
                    <?= $semesterAktif['semester'] ?? '-' ?>
                </h5>

            </div>
        </div>
    </div>

</div>

<!-- Filter (khusus Admin / Kepala Sekolah / Wakasek Kurikulum) -->

<?php if (!empty($bolehLihatSemua)): ?>

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form method="get" action="<?= base_url('jurnal') ?>" class="row g-3">

            <div class="col-md-4">
                <label class="form-label fw-bold">Guru</label>
                <select name="guru_id" class="form-select">
                    <option value="">-- Semua Guru --</option>
                    <?php foreach ($guruList as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= (string) $guru_id === (string) $g['id'] ? 'selected' : '' ?>>
                        <?= esc($g['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Mata Pelajaran</label>
                <select name="mapel_id" class="form-select">
                    <option value="">-- Semua Mapel --</option>
                    <?php foreach ($mapelList as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= (string) $mapel_id === (string) $m['id'] ? 'selected' : '' ?>>
                        <?= esc($m['nama_mapel']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($kelasList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= (string) $kelas_id === (string) $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nama_kelas']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>

        </form>

    </div>

</div>

<?php endif; ?>

<!-- Tabel -->

<div class="card border-0 shadow-sm">

    <div class="card-header bg-default">

        <h5 class="mb-0">
            <i class="bi bi-table"></i>
            Data Jurnal
        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="tableJurnal" class="table table-hover align-middle">

                <thead class="table-light">

                <tr>
                    <th width="60">No</th>
                    <th>Tanggal</th>
                    <?php if (!empty($bolehLihatSemua)): ?>
                    <th>Guru</th>
                    <?php endif; ?>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Jam</th>
                    <th>Waktu</th>
                    <th>Materi</th>
                    <th width="70">Foto</th>
                    <th width="170">Aksi</th>
                </tr>

                </thead>

                <tbody>



                <?php $no = 1; ?>
                <?php foreach($jurnal as $j): ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>
                        <?= date('d-m-Y', strtotime($j['tanggal'])) ?>
                    </td>

                    <?php if (!empty($bolehLihatSemua)): ?>
                    <td>
                        <?= esc($j['nama_guru']) ?>
                    </td>
                    <?php endif; ?>

                    <td>
                        <span class="badge bg-primary">
                            <?= esc($j['nama_kelas']) ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-success">
                            <?= esc($j['nama_mapel']) ?>
                        </span>
                    </td>

                    <td class="text-center">
                        <?= esc($j['jam_ke']) ?>
                    </td>

                    <td>
                        <?php if(!empty($j['jam_mulai']) && !empty($j['jam_akhir'])): ?>
                            <?= substr($j['jam_mulai'],0,5) ?> - <?= substr($j['jam_akhir'],0,5) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td>
                        <?= esc($j['materi']) ?>
                    </td>

                    <td class="text-center">
                        <?php if(!empty($j['foto'])): ?>
                            <a href="<?= base_url('assets/img/jurnal/'.$j['foto']) ?>" target="_blank">
                                <img src="<?= base_url('assets/img/jurnal/'.$j['foto']) ?>"
                                     style="width:45px;height:45px;object-fit:cover;border-radius:6px;">
                            </a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <td>

                        <?php if ($j['user_id'] == session()->get('id')): ?>

                        <a href="<?= base_url('jurnal/edit/'.$j['id']) ?>"
                           class="btn btn-warning btn-sm">

                            <i class="bi bi-pencil-square"></i>

                        </a>

                        <a href="<?= base_url('jurnal/hapus/'.$j['id']) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus data ini?')">

                            <i class="bi bi-trash"></i>

                        </a>

                        <?php else: ?>

                        <span class="text-muted">-</span>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


</div>
<?= view('template/footer'); ?>
<script>
$(document).ready(function() {

    $('#tableJurnal').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "→",
                previous: "←"
            }
        }
    });

});
</script>
