<?= view('template/header') ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="card shadow-lg border-0 mb-4 overflow-hidden">
        <div class="card-body text-white"
            style="background:linear-gradient(135deg,#0d6efd,#4f46e5);">

            <div class="row align-items-center">

                <div class="col-md-2 text-center">

                    <?php if (!empty($sekolah['logo'])) : ?>

                        <img src="<?= base_url('assets/img/'.$sekolah['logo']) ?>"
                            class="img-fluid rounded-circle bg-white p-2 shadow"
                            style="width:110px;height:110px;object-fit:contain;">

                    <?php else : ?>

                        <img src="<?= base_url('assets/img/logo-default.png') ?>"
                            class="img-fluid rounded-circle bg-white p-2 shadow"
                            style="width:110px;height:110px;object-fit:contain;">

                    <?php endif; ?>

                </div>

                <div class="col-md-10">

                    <h2 class="fw-bold mb-1">
                        <?= $sekolah['nama_sekolah']; ?>
                    </h2>

                    <h5 class="mb-3">
                        Dashboard Kepala Sekolah
                    </h5>

                    <p class="mb-0 fs-5">
                        Selamat datang,
                        <strong><?= session()->get('nama'); ?></strong>
                    </p>

                </div>

            </div>

        </div>
    </div>


    <!-- Statistik -->

    <div class="row g-4">

        <div class="col-lg-3">

            <div class="card border-0 shadow h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Total Guru
                            </small>

                            <h2 id="totalGuru" class="fw-bold">
                                <?= $totalGuru ?>
                            </h2>

                        </div>

                        <div class="text-primary">

                            <i class="bi bi-people-fill"
                               style="font-size:45px;"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card border-0 shadow h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Sudah Mengajar
                            </small>

                            <h2 id="guruMasuk" class="text-success fw-bold">
                                <?= $guruMasuk ?>
                            </h2>

                        </div>

                        <div class="text-success">

                            <i class="bi bi-check-circle-fill"
                               style="font-size:45px;"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card border-0 shadow h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Belum Mengajar
                            </small>

                            <h2 id="belumMengajar" class="text-danger fw-bold">
                                <?= $totalGuru-$guruMasuk ?>
                            </h2>

                        </div>

                        <div class="text-danger">

                            <i class="bi bi-exclamation-circle-fill"
                               style="font-size:45px;"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3">

            <div class="card border-0 shadow h-100">

                <div class="card-body">

                    <?php
                    $persen = $totalGuru > 0 ? round(($guruMasuk/$totalGuru)*100) : 0;
                    ?>

                    <small class="text-muted">
                        Progress Hari Ini
                    </small>

                    <h2 id="persen" class="fw-bold">
                        <?= $persen ?>%
                    </h2>

                    <div class="progress mt-3" style="height:10px">

                        <div class="progress-bar bg-success"
                             style="width:<?= $persen ?>%">
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Monitoring Guru -->

    <div class="card shadow border-0 mt-4">

        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                <i class="bi bi-display"></i>
                Monitoring Guru
            </h5>

            <span class="badge bg-success">
                <i class="bi bi-broadcast"></i>
                Live
            </span>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Guru</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Ruangan</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody id="monitoringBody">

<?php $no = 1; ?>
<?php foreach($guru as $g): ?>

<tr>

    <td><?= $no++ ?></td>

    <td><b><?= esc($g['nama']) ?></b></td>

    <td><?= esc($g['sedang_kelas'] ?? '-') ?></td>

    <td><?= esc($g['sedang_mapel'] ?? '-') ?></td>

    <td><?= esc($g['sedang_ruangan'] ?? '-') ?></td>

    <td>
        <?php if($g['sedang_jam_ke']): ?>
            Jam <?= esc($g['sedang_jam_ke']) ?>
            <br>
            <small class="text-muted"><?= esc($g['sedang_waktu']) ?></small>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td>
        <?php if($g['status_sesi_ini'] == 'sudah'): ?>
            <span class="badge bg-success">
                <i class="bi bi-star-fill"></i>
                Sudah Mengajar
            </span>
        <?php elseif($g['status_sesi_ini'] == 'belum'): ?>
            <span class="badge bg-danger">Belum Mengajar</span>
        <?php else: ?>
            <span class="badge bg-secondary">-</span>
        <?php endif; ?>
    </td>

    <td><?= esc($g['keterangan']) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Snapshot Semua Ruangan -->

    <div class="card shadow border-0 mt-4">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-camera-video"></i>
                Snapshot Semua Ruangan
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-3" id="snapshotBody">

<?php $adaYangMengajar = false; ?>

<?php foreach($guru as $g): ?>

    <?php if(!empty($g['sedang_kelas'])): ?>

        <?php $adaYangMengajar = true; ?>

        <div class="col-md-3 col-sm-6">

            <div class="card h-100 shadow-sm">

                <div class="card-header bg-primary text-white py-2">
                    <i class="bi bi-door-open"></i>
                    <?= esc($g['sedang_ruangan'] ?? 'Tanpa Ruangan') ?>
                </div>

                <?php if(!empty($g['foto_sesi_ini'])): ?>

                    <img src="<?= base_url('assets/img/jurnal/'.$g['foto_sesi_ini']) ?>"
                         style="width:100%;height:160px;object-fit:cover;">

                <?php else: ?>

                    <div class="d-flex align-items-center justify-content-center bg-light"
                         style="height:160px;">
                        <i class="bi bi-camera text-muted" style="font-size:40px;"></i>
                    </div>

                <?php endif; ?>

                <div class="card-body py-2">

                    <div class="fw-semibold"><?= esc($g['nama']) ?></div>

                    <small class="text-muted d-block">
                        <i class="bi bi-book"></i>
                        <?= esc($g['sedang_mapel']) ?>
                    </small>

                    <small class="text-muted d-block">
                        <i class="bi bi-mortarboard"></i>
                        <?= esc($g['sedang_kelas']) ?>
                    </small>

                    <small class="text-muted d-block mb-2">
                        <i class="bi bi-clock"></i>
                        Jam Ke : <?= esc($g['sedang_jam_ke']) ?>
                    </small>

                    <?php if($g['status_sesi_ini'] == 'sudah'): ?>
                        <span class="badge bg-success w-100">
                            <i class="bi bi-check-circle"></i>
                            Sudah Mengajar
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger w-100">
                            Belum Mengajar
                        </span>
                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endif; ?>

<?php endforeach; ?>

<?php if(!$adaYangMengajar): ?>

    <div class="col-12 text-center text-muted py-4">
        <i class="bi bi-moon-stars" style="font-size:40px;"></i>
        <p class="mt-2 mb-0">Tidak ada guru yang sedang mengajar saat ini.</p>
    </div>

<?php endif; ?>

            </div>

        </div>

    </div>

    <!-- Jadwal Mengajar Hari Ini -->

    <div class="card shadow border-0 mt-4">

        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="bi bi-calendar-check"></i>
                Jadwal Mengajar Hari Ini
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Total Jam</th>
                            <th>Jam Ke</th>
                            <th>Waktu</th>
                            <th>Ruangan</th>
                            <th>Status Jurnal</th>
                        </tr>
                    </thead>

                    <tbody id="jadwalHariIniBody">

<?php $no = 1; ?>
<?php foreach($jadwalHariIni as $j): ?>

<tr>

    <td><?= $no++ ?></td>

    <td><b><?= esc($j['nama_guru']) ?></b></td>

    <td><?= esc($j['nama_mapel']) ?></td>

    <td><?= esc($j['nama_kelas']) ?></td>

    <td><span class="badge bg-info text-dark"><?= $j['total_sesi'] ?> JP</span></td>

    <td>Jam <?= esc($j['jam_ke_display']) ?></td>

    <td><?= esc(substr($j['jam_mulai'], 0, 5)) ?> - <?= esc(substr($j['jam_selesai'], 0, 5)) ?></td>

    <td><?= esc($j['nama_ruang'] ?? '-') ?></td>

    <td>
        <?php if($j['status_jurnal'] === 'Masuk'): ?>
            <span class="badge bg-success">Sudah Mengajar (<?= $j['sudah_diisi'] ?>/<?= $j['total_sesi'] ?>)</span>
        <?php elseif($j['sudah_diisi'] > 0): ?>
            <span class="badge bg-warning text-dark">Sebagian (<?= $j['sudah_diisi'] ?>/<?= $j['total_sesi'] ?>)</span>
        <?php else: ?>
            <span class="badge bg-secondary">Belum Diisi (0/<?= $j['total_sesi'] ?>)</span>
        <?php endif; ?>
    </td>

</tr>

<?php endforeach; ?>

<?php if(empty($jadwalHariIni)): ?>

<tr>
    <td colspan="9" class="text-center text-muted">
        Tidak ada jadwal mengajar hari ini.
    </td>
</tr>

<?php endif; ?>

</tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script>

function badgeStatusJurnal(j){
    if(j.status_jurnal === 'Masuk'){
        return `<span class="badge bg-success">Sudah Mengajar (${j.sudah_diisi}/${j.total_sesi})</span>`;
    } else if(j.sudah_diisi > 0){
        return `<span class="badge bg-warning text-dark">Sebagian (${j.sudah_diisi}/${j.total_sesi})</span>`;
    }
    return `<span class="badge bg-secondary">Belum Diisi (0/${j.total_sesi})</span>`;
}

function badgeStatus(status){
    if(status === 'sudah'){
        return '<span class="badge bg-success"><i class="bi bi-star-fill"></i> Sudah Mengajar</span>';
    } else if(status === 'belum'){
        return '<span class="badge bg-danger">Belum Mengajar</span>';
    }
    return '<span class="badge bg-secondary">-</span>';
}

function loadMonitoring(){

    $.get("<?= base_url('kepsek/monitoring') ?>", function(res){

        $("#totalGuru").text(res.totalGuru);
        $("#guruMasuk").text(res.guruMasuk);
        $("#belumMengajar").text(res.totalGuru-res.guruMasuk);

        let persen=0;

        if(res.totalGuru>0){
            persen=Math.round((res.guruMasuk/res.totalGuru)*100);
        }

        $("#persen").text(persen+"%");
        $(".progress-bar").css("width",persen+"%");

        // Tabel Monitoring Guru
        let html='';
        let no=1;

        res.guru.forEach(function(g){

            html+=`
            <tr>
                <td>${no++}</td>
                <td><b>${g.nama}</b></td>
                <td>${g.sedang_kelas ?? '-'}</td>
                <td>${g.sedang_mapel ?? '-'}</td>
                <td>${g.sedang_ruangan ?? '-'}</td>
                <td>${g.sedang_jam_ke ? 'Jam '+g.sedang_jam_ke+'<br><small class="text-muted">'+g.sedang_waktu+'</small>' : '-'}</td>
                <td>${badgeStatus(g.status_sesi_ini)}</td>
                <td>${g.keterangan}</td>
            </tr>
            `;

        });

        $("#monitoringBody").html(html);

        // Snapshot Semua Ruangan
        let snap='';
        let adaYangMengajar=false;

        res.guru.forEach(function(g){

            if(g.sedang_kelas){

                adaYangMengajar=true;

                let fotoHtml = '';

                if(g.foto_sesi_ini){
                    fotoHtml = `<img src="<?= base_url('assets/img/jurnal/') ?>${g.foto_sesi_ini}" style="width:100%;height:160px;object-fit:cover;">`;
                } else {
                    fotoHtml = `<div class="d-flex align-items-center justify-content-center bg-light" style="height:160px;"><i class="bi bi-camera text-muted" style="font-size:40px;"></i></div>`;
                }

                let statusHtml = g.status_sesi_ini === 'sudah'
                    ? '<span class="badge bg-success w-100"><i class="bi bi-check-circle"></i> Sudah Mengajar</span>'
                    : '<span class="badge bg-danger w-100">Belum Mengajar</span>';

                snap += `
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white py-2">
                            <i class="bi bi-door-open"></i> ${g.sedang_ruangan ?? 'Tanpa Ruangan'}
                        </div>
                        ${fotoHtml}
                        <div class="card-body py-2">
                            <div class="fw-semibold">${g.nama}</div>
                            <small class="text-muted d-block"><i class="bi bi-book"></i> ${g.sedang_mapel}</small>
                            <small class="text-muted d-block"><i class="bi bi-mortarboard"></i> ${g.sedang_kelas}</small>
                            <small class="text-muted d-block mb-2"><i class="bi bi-clock"></i> Jam Ke : ${g.sedang_jam_ke}</small>
                            ${statusHtml}
                        </div>
                    </div>
                </div>
                `;
            }

        });

        if(!adaYangMengajar){
            snap = `
            <div class="col-12 text-center text-muted py-4">
                <i class="bi bi-moon-stars" style="font-size:40px;"></i>
                <p class="mt-2 mb-0">Tidak ada guru yang sedang mengajar saat ini.</p>
            </div>
            `;
        }

        $("#snapshotBody").html(snap);

        // Jadwal Mengajar Hari Ini
        let jadwalHtml = '';
        let noJadwal = 1;

        (res.jadwalHariIni || []).forEach(function(j){

            jadwalHtml += `
            <tr>
                <td>${noJadwal++}</td>
                <td><b>${j.nama_guru}</b></td>
                <td>${j.nama_mapel}</td>
                <td>${j.nama_kelas}</td>
                <td><span class="badge bg-info text-dark">${j.total_sesi} JP</span></td>
                <td>Jam ${j.jam_ke_display}</td>
                <td>${j.jam_mulai.substring(0,5)} - ${j.jam_selesai.substring(0,5)}</td>
                <td>${j.nama_ruang ?? '-'}</td>
                <td>${badgeStatusJurnal(j)}</td>
            </tr>
            `;

        });

        if(!res.jadwalHariIni || res.jadwalHariIni.length === 0){
            jadwalHtml = `
            <tr>
                <td colspan="9" class="text-center text-muted">
                    Tidak ada jadwal mengajar hari ini.
                </td>
            </tr>
            `;
        }

        $("#jadwalHariIniBody").html(jadwalHtml);

    });

}

// Panggil sekali langsung saat halaman dibuka, supaya tabel tidak kosong 5 detik pertama
loadMonitoring();

setInterval(loadMonitoring,5000);

</script>

<?= view('template/footer') ?>
