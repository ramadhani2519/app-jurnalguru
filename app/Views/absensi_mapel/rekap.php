<?= view('template/header') ?>

<style>

.table tbody tr:hover{
background:#f8f9fa;
transition:.3s;
}

.badge-persen{
min-width:60px;
}

</style>

<div class="container py-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
        <i class="bi bi-file-earmark-spreadsheet"></i>
        Rekap Absensi Mapel
    </h5>
    <a href="<?= base_url('absensi-mapel') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card-body">

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle"></i>
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif ?>

<form method="get" action="<?= base_url('absensi-mapel/rekap') ?>" class="row g-3 mb-4">

    <div class="col-md-3">
        <label class="form-label fw-bold">Mata Pelajaran</label>
        <select name="mapel_id" class="form-select" required>
            <option value="">-- Pilih Mapel --</option>
            <?php foreach ($mapelList as $m): ?>
            <option value="<?= $m['id'] ?>" <?= (string) $mapel_id === (string) $m['id'] ? 'selected' : '' ?>>
                <?= esc($m['nama_mapel']) ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-bold">Kelas</label>
        <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= (string) $kelas_id === (string) $k['id'] ? 'selected' : '' ?>>
                <?= esc($k['nama_kelas']) ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold">Tanggal Awal</label>
        <input type="date" name="tanggal_awal" class="form-control" value="<?= esc($tanggal_awal) ?>" required>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold">Tanggal Akhir</label>
        <input type="date" name="tanggal_akhir" class="form-control" value="<?= esc($tanggal_akhir) ?>" required>
    </div>

    <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Tampilkan
        </button>
    </div>

</form>

<?php if (!empty($rekap)): ?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <span class="badge bg-secondary">Total Pertemuan: <?= $totalPertemuan ?></span>
        <?php if (!empty($tanggalList)): ?>
        <span class="text-muted ms-2 small">
            (<?= implode(', ', array_map(fn($t) => date('d/m/Y', strtotime($t)), $tanggalList)) ?>)
        </span>
        <?php endif ?>
    </div>
    <a href="<?= base_url('absensi-mapel/export-rekap?' . http_build_query([
        'mapel_id'      => $mapel_id,
        'kelas_id'      => $kelas_id,
        'tanggal_awal'  => $tanggal_awal,
        'tanggal_akhir' => $tanggal_akhir,
    ])) ?>" class="btn btn-success">
        <i class="bi bi-download"></i> Unduh Excel
    </a>
</div>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle" style="font-size:.85rem;">
<thead class="table-light">
<tr>
    <th rowspan="2" class="align-middle">No</th>
    <th rowspan="2" class="align-middle">NIS</th>
    <th rowspan="2" class="align-middle">Nama Siswa</th>
    <?php if (!empty($tanggalList)): ?>
    <th colspan="<?= count($tanggalList) ?>" class="text-center">Tanggal Pertemuan</th>
    <?php endif ?>
    <th rowspan="2" class="align-middle text-center">Hadir</th>
    <th rowspan="2" class="align-middle text-center">Sakit</th>
    <th rowspan="2" class="align-middle text-center">Izin</th>
    <th rowspan="2" class="align-middle text-center">Alpa</th>
    <th rowspan="2" class="align-middle text-center">% Hadir</th>
</tr>
<tr>
    <?php foreach ($tanggalList as $tgl): ?>
    <th class="text-center" style="writing-mode:vertical-rl;"><?= date('d/m', strtotime($tgl)) ?></th>
    <?php endforeach ?>
</tr>
</thead>
<tbody>
<?php foreach ($rekap as $i => $r): ?>
<tr>
    <td><?= $i + 1 ?></td>
    <td><?= esc($r['nis']) ?></td>
    <td class="text-nowrap"><?= esc($r['nama_siswa']) ?></td>
    <?php foreach ($tanggalList as $tgl): ?>
    <?php $status = $r['detail'][$tgl] ?? '-'; ?>
    <td class="text-center">
        <?php if ($status !== '-'): ?>
        <span class="badge <?= $status === 'H' ? 'bg-success' : ($status === 'A' ? 'bg-danger' : 'bg-warning text-dark') ?>">
            <?= esc($status) ?>
        </span>
        <?php else: ?>
        -
        <?php endif ?>
    </td>
    <?php endforeach ?>
    <td class="text-center"><?= $r['H'] ?></td>
    <td class="text-center"><?= $r['S'] ?></td>
    <td class="text-center"><?= $r['I'] ?></td>
    <td class="text-center"><?= $r['A'] ?></td>
    <td class="text-center">
        <span class="badge badge-persen <?= $r['persentase_hadir'] >= 80 ? 'bg-success' : ($r['persentase_hadir'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') ?>">
            <?= $r['persentase_hadir'] ?>%
        </span>
    </td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>

<?php
    $totalHadirSemua = array_sum(array_column($rekap, 'H'));
    $totalSiswa       = count($rekap);
    $persentaseKelas  = ($totalPertemuan > 0 && $totalSiswa > 0)
        ? round($totalHadirSemua / ($totalPertemuan * $totalSiswa) * 100, 2)
        : 0;
?>

<div class="d-flex justify-content-end mt-2">
    <div class="alert <?= $persentaseKelas >= 80 ? 'alert-success' : ($persentaseKelas >= 50 ? 'alert-warning' : 'alert-danger') ?> mb-0 py-2 px-3 fw-bold">
        Persentase Kehadiran Kelas: <?= $persentaseKelas ?>%
    </div>
</div>

<?php elseif ($mapel_id && $kelas_id && $tanggal_awal && $tanggal_akhir): ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Belum ada siswa / data absensi untuk kombinasi filter ini.
</div>

<?php endif ?>

</div>
</div>
</div>

<?= view('template/footer') ?>
