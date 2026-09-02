<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5>Data Semester</h5>
    </div>

    <div class="card-body">

        <a href="<?= base_url('semester/tambah') ?>"
           class="btn btn-primary mb-3">

            Tambah Semester

        </a>

       <table id="tableData" class="table table-hover align-middle">

    <thead>
        <tr>
            <th>No</th>
            <th>Semester</th>
            <th>Status</th>
            <th width="220">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php $no = 1; ?>
    <?php foreach($semester as $s): ?>

        <tr>

            <td><?= $no++ ?></td>

            <td><?= $s['semester'] ?></td>

            <td>

                <?php if($s['aktif'] == 'Y'): ?>

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

                <?php if($s['aktif'] != 'Y'): ?>

                    <a href="<?= base_url('semester/aktif/'.$s['id']) ?>"
                       class="btn btn-success btn-sm"
                       onclick="return confirm('Aktifkan semester ini?')">

                        <i class="bi bi-toggle-on"></i>
                        Aktifkan

                    </a>

                <?php endif; ?>

                <a href="<?= base_url('semester/edit/'.$s['id']) ?>"
                   class="btn btn-warning btn-sm">

                    <i class="bi bi-pencil-square"></i>
                    Edit

                </a>

                <a href="<?= base_url('semester/hapus/'.$s['id']) ?>"
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