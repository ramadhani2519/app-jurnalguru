<?= view('template/header') ?>

<div class="container py-4">

<div class="card shadow border-0">

    <div class="card-header bg-primary text-white">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-0">
                    <i class="bi bi-calendar3"></i>
                    Jadwal Pelajaran
                </h4>

                <small>Pengaturan Jadwal Mengajar Guru</small>

            </div>

            <a href="<?= base_url('jadwal/create') ?>"
               class="btn btn-light">

                <i class="bi bi-plus-circle"></i>

                Tambah Jadwal

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

        <?php if(session()->getFlashdata('error')): ?>

        <div class="alert alert-danger">

            <i class="bi bi-x-circle"></i>

            <?= session()->getFlashdata('error') ?>

        </div>

        <?php endif ?>

        <div class="table-responsive">

        <table id="tableJadwal"
               class="table table-hover table-bordered align-middle">

            <thead class="table-primary">

            <tr>

                <th width="50">No</th>

                <th>Hari</th>

                <th>Jam</th>

                <th>Kelas</th>

                <th>Mata Pelajaran</th>

                <th>Guru</th>

                <th>Ruangan</th>

                <th width="120">Aksi</th>

            </tr>

            </thead>

            <tbody>

            <?php $no=1; ?>

            <?php foreach($jadwal as $j): ?>

            <tr>

                <td><?= $no++ ?></td>

                <td>

                    <span class="badge bg-primary">

                        <?= $j['nama_hari'] ?>

                    </span>

                </td>

                <td>

                    <b>Jam <?= $j['jam_ke'] ?></b>

                    <br>

                    <small class="text-muted">

                        <?= substr($j['jam_mulai'],0,5) ?>

                        -

                        <?= substr($j['jam_selesai'],0,5) ?>

                    </small>

                </td>

                <td>

                    <span class="badge bg-success">

                        <?= $j['nama_kelas'] ?>

                    </span>

                </td>

                <td>

                    <?= $j['nama_mapel'] ?>

                </td>

                <td>

                    <i class="bi bi-person"></i>

                    <?= $j['nama'] ?>

                </td>

                <td>

                    <?= $j['nama_ruang'] ?? '-' ?>

                </td>

                <td>

                    <a href="<?= base_url('jadwal/edit/'.$j['id']) ?>"

                       class="btn btn-warning btn-sm">

                        <i class="bi bi-pencil-square"></i>

                    </a>

                    <a href="<?= base_url('jadwal/delete/'.$j['id']) ?>"

                       onclick="return confirm('Hapus jadwal ini?')"

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

</div>

<?= view('template/footer') ?>

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>

$(function(){

    $('#tableJadwal').DataTable({

        responsive:true,

        pageLength:10,

        order:[[1,'asc']],

        language:{

            search:"Cari :",

            lengthMenu:"Tampilkan _MENU_ data",

            info:"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            zeroRecords:"Data tidak ditemukan",

            paginate:{

                first:"Awal",

                last:"Akhir",

                next:"›",

                previous:"‹"

            }

        }

    });

});

</script>