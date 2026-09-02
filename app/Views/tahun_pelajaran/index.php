<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5>Data Tahun</h5>
    </div>

    <div class="card-body">

        <a href="<?= base_url('tahun/tambah') ?>"
           class="btn btn-primary mb-3">

            Tambah Tahun

        </a>

       <table id="tableData" class="table table-hover align-middle">

    <thead>
        <tr>
            <th>No</th>
            <th>Tahun Pelajaran</th>
            <th>Status</th>
            <th width="220">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php $no = 1; ?>
    <?php foreach($tahun as $t): ?>

        <tr>

            <td><?= $no++ ?></td>

            <td><?= $t['tahun'] ?></td>

            <td>

                <?php if($t['aktif'] == 'Y'): ?>

                    <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill"></i>
                        Aktif
                    </span>

                <?php else: ?>

                    <span class="badge bg-secondary">
                        Tidak Aktif
                    </span>

                <?php endif; ?>

            </td>

            <td>

                <?php if($t['aktif'] != 'Y'): ?>

                    <a href="<?= base_url('tahun/aktif/'.$t['id']) ?>"
                       class="btn btn-success btn-sm"
                       onclick="return confirm('Aktifkan tahun pelajaran ini?')">

                        <i class="bi bi-toggle-on"></i>
                        Aktifkan

                    </a>

                <?php endif; ?>

                <a href="<?= base_url('tahun/edit/'.$t['id']) ?>"
                   class="btn btn-warning btn-sm">

                    <i class="bi bi-pencil-square"></i>
                    Edit

                </a>

                <a href="<?= base_url('tahun/hapus/'.$t['id']) ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Hapus data ini?')">

                    <i class="bi bi-trash"></i>
                    Hapus

                </a>

            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

    </div>

</div>
</div>
<?= view('template/footer') ?>