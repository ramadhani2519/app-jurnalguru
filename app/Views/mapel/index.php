<?= view('template/header') ?>

<div class="container py-4">
<div class="card shadow-sm">

    <div class="card-header">
        <h5>Data Mata Pelajaran</h5>
    </div>

    <div class="card-body">

        <a href="<?= base_url('mapel/tambah') ?>"
           class="btn btn-primary mb-3">

            Tambah Mata Pelajaran

        </a>

       <table id="tableData" class="table table-hover align-middle">

    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Mata Pelajaran</th>
            <th width="220">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php $no = 1; ?>
    <?php foreach($mapel as $m): ?>

        <tr>

            <td><?= $no++ ?></td>
            <td><?= $m['kode_mapel'] ?></td>
            <td><?= $m['nama_mapel'] ?></td>

            <td>

                <a href="<?= base_url('mapel/edit/'.$m['id']) ?>"
                   class="btn btn-warning btn-sm">

                    <i class="bi bi-pencil-square"></i>
                    Edit

                </a>

                <a href="<?= base_url('mapel/hapus/'.$m['id']) ?>"
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