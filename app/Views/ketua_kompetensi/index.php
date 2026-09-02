<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow border-0">

    <div class="card-header bg-primary text-white">

        <div class="d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-person-badge"></i>
                Ketua Kompetensi Keahlian
            </h4>

            <a href="<?= base_url('ketua-kompetensi/create') ?>" class="btn btn-light">
                <i class="bi bi-plus-circle"></i>
                Tambah
            </a>

        </div>

    </div>

    <div class="card-body">

        <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif ?>

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-primary">
                <tr>
                    <th width="50">No</th>
                    <th>Kompetensi Keahlian</th>
                    <th>Nama Ketua</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; ?>
            <?php foreach($ketuaKompetensi as $k): ?>

            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($k['nama_kompetensi']) ?></td>
                <td><?= esc($k['nama_ketua']) ?></td>
                <td>
                    <a href="<?= base_url('ketua-kompetensi/edit/'.$k['id']) ?>"
                       class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="<?= base_url('ketua-kompetensi/delete/'.$k['id']) ?>"
                       onclick="return confirm('Hapus data ini?')"
                       class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>

            <?php endforeach ?>

            </tbody>

        </table>

    </div>

</div>

</div>

<?= view('template/footer') ?>
