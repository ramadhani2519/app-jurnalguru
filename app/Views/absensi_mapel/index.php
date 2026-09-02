<?= view('template/header') ?>

<style>

.table tbody tr:hover{
background:#f8f9fa;
transition:.3s;
}

.form-check-input{
cursor:pointer;
transform:scale(1.2);
}

.badge{
min-width:90px;
}

.btnMassal{
margin-right:5px;
margin-bottom:5px;
}

.sesiCard{
cursor:pointer;
transition:.2s;
}

.sesiCard:hover{
box-shadow:0 0 0 2px #0d6efd33;
}

.sesiCard.aktif{
border-color:#0d6efd;
background:#eef5ff;
}

</style>

<div class="container py-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
        📋 Absensi Mapel (Per Sesi Mengajar)
    </h5>
    <a href="<?= base_url('absensi-mapel/rekap') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-file-earmark-spreadsheet"></i> Rekap &amp; Unduh
    </a>
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

<form method="get" class="row mb-3 align-items-end">

    <div class="col-md-3">
        <label>Tanggal</label>
        <input type="date"
               name="tanggal"
               value="<?= esc($tanggal) ?>"
               class="form-control"
               onchange="this.form.submit()">
    </div>

</form>

<p class="text-muted">
    Sesi mengajar diambil otomatis dari Jadwal Pelajaran hari itu. Pilih salah satu sesi di bawah untuk mulai mengabsen.
</p>

<div class="row mb-4">

<?php if(empty($sesiList)): ?>

    <div class="col-12">
        <div class="alert alert-secondary mb-0">
            Tidak ada jadwal mengajar untuk tanggal ini.
        </div>
    </div>

<?php endif; ?>

<?php foreach($sesiList as $s): ?>

    <div class="col-md-4 mb-3">
        <a href="<?= base_url('absensi-mapel?tanggal=' . $tanggal . '&sesi=' . $s['sesi_key']) ?>"
           class="text-decoration-none">
            <div class="card sesiCard h-100 <?= ($sesiAktif && $sesiAktif['sesi_key'] === $s['sesi_key']) ? 'aktif' : '' ?>">
                <div class="card-body">
                    <h6 class="card-title mb-1"><?= esc($s['nama_mapel']) ?></h6>
                    <div class="text-muted small mb-2"><?= esc($s['nama_kelas']) ?></div>
                    <span class="badge bg-info text-dark">Jam <?= esc($s['jam_ke_display']) ?> (<?= $s['total_jam'] ?> JP)</span>
                    <div class="small text-muted mt-1">
                        <?= esc(substr($s['jam_mulai'],0,5)) ?> - <?= esc(substr($s['jam_selesai'],0,5)) ?>
                        <?= !empty($s['nama_ruang']) ? ' &middot; ' . esc($s['nama_ruang']) : '' ?>
                    </div>
                </div>
            </div>
        </a>
    </div>

<?php endforeach; ?>

</div>

<?php if($sesiAktif): ?>

<hr>

<div class="alert alert-primary d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <b><?= esc($sesiAktif['nama_mapel']) ?></b> &middot; <?= esc($sesiAktif['nama_kelas']) ?>
        &middot; Jam <?= esc($sesiAktif['jam_ke_display']) ?>
        &middot; <?= date('d-m-Y', strtotime($tanggal)) ?>
    </div>
    <div>
        <span id="jumlahAbsen"><?= count($absensiTersimpan) ?></span> / <?= count($siswa) ?> Siswa
        &nbsp;
        <a href="<?= base_url('absensi-mapel/cetak?tanggal=' . $tanggal . '&mapel_id=' . $sesiAktif['mapel_id'] . '&kelas_id=' . $sesiAktif['kelas_id']) ?>"
           class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-printer"></i> Cetak Absen
        </a>
    </div>
</div>

<div class="mb-3">
    <button class="btn btn-success btnMassal" data-status="H">✓ Hadir Semua</button>
    <button class="btn btn-warning btnMassal" data-status="S">Sakit Semua</button>
    <button class="btn btn-info btnMassal" data-status="I">Izin Semua</button>
    <button class="btn btn-secondary btnMassal" data-status="A">Alpa Semua</button>
</div>

<input type="hidden" id="tanggal" value="<?= esc($tanggal) ?>">
<input type="hidden" id="mapel_id" value="<?= esc($sesiAktif['mapel_id']) ?>">
<input type="hidden" id="kelas_id" value="<?= esc($sesiAktif['kelas_id']) ?>">
<input type="hidden" id="jam_ke_display" value="<?= esc($sesiAktif['jam_ke_display']) ?>">

<table class="table table-bordered align-middle">
<thead>
<tr>
<th width="60">No</th>
<th>Nama Siswa</th>
<th class="text-center">Hadir</th>
<th class="text-center">Sakit</th>
<th class="text-center">Izin</th>
<th class="text-center">Alpa</th>
<th class="text-center">Status</th>
</tr>
</thead>

<tbody>

<?php $no=1; ?>
<?php foreach($siswa as $s): ?>

<?php $statusSaatIni = $absensiTersimpan[$s['id']] ?? ''; ?>

<tr>

<td><?= $no++ ?></td>
<td><?= esc($s['nama_siswa']) ?></td>

<td class="text-center">
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen" data-siswa="<?= $s['id'] ?>" value="H"
       <?= $statusSaatIni == 'H' ? 'checked' : '' ?>>
</td>

<td class="text-center">
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen" data-siswa="<?= $s['id'] ?>" value="S"
       <?= $statusSaatIni == 'S' ? 'checked' : '' ?>>
</td>

<td class="text-center">
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen" data-siswa="<?= $s['id'] ?>" value="I"
       <?= $statusSaatIni == 'I' ? 'checked' : '' ?>>
</td>

<td class="text-center">
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen" data-siswa="<?= $s['id'] ?>" value="A"
       <?= $statusSaatIni == 'A' ? 'checked' : '' ?>>
</td>

<td class="text-center">
<span id="status<?= $s['id'] ?>">
<?php
    $warna = 'secondary'; $text = 'Belum';
    switch($statusSaatIni){
        case 'H': $warna='success'; $text='Hadir'; break;
        case 'S': $warna='warning'; $text='Sakit'; break;
        case 'I': $warna='info'; $text='Izin'; break;
        case 'A': $warna='dark'; $text='Alpa'; break;
    }
?>
<span class="badge bg-<?= $warna ?>"><?= $text ?></span>
</span>
</td>

</tr>

<?php endforeach ?>

</tbody>

</table>

<?php endif; ?>

</div>

</div>

</div>

<?= view('template/footer') ?>

<script>

function badge(status){
    switch(status){
        case 'H': return { warna:'success', text:'Hadir' };
        case 'S': return { warna:'warning', text:'Sakit' };
        case 'I': return { warna:'info', text:'Izin' };
        case 'A': return { warna:'dark', text:'Alpa' };
        default: return { warna:'secondary', text:'Belum' };
    }
}

$('.absen').change(function(){
    let siswa = $(this).data('siswa');
    let status = $(this).val();
    simpanAbsen(siswa, status);
});

function simpanAbsen(siswa, status){

    $.ajax({
        url:"<?= base_url('absensi-mapel/simpan') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            mapel_id:$('#mapel_id').val(),
            kelas_id:$('#kelas_id').val(),
            jam_ke_display:$('#jam_ke_display').val(),
            siswa_id:siswa,
            status:status
        },

        success:function(res){
            if(res.status=="success"){
                let b=badge(status);
                $('#status'+siswa).html(
                    '<span class="badge bg-'+b.warna+'">'+b.text+'</span>'
                );
            } else {
                alert(res.message || 'Gagal menyimpan absensi.');
            }
        },

        error:function(xhr){
            console.log(xhr.responseText);
        }

    });

}

$('.btnMassal').click(function(e){

    e.preventDefault();
    let status=$(this).data('status');
    let data=[];

    $('.absen[value="'+status+'"]').each(function(){
        $(this).prop('checked',true);
        data.push({
            siswa_id:$(this).data('siswa'),
            status:status
        });
    });

    $.ajax({
        url:"<?= base_url('absensi-mapel/simpanMassal') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            mapel_id:$('#mapel_id').val(),
            kelas_id:$('#kelas_id').val(),
            jam_ke_display:$('#jam_ke_display').val(),
            absensi:data
        },

        success:function(res){
            if(res.status=="success"){
                data.forEach(function(item){
                    let b=badge(item.status);
                    $('#status'+item.siswa_id).html(
                        '<span class="badge bg-'+b.warna+'">'+b.text+'</span>'
                    );
                });
            } else {
                alert(res.message || 'Gagal menyimpan absensi massal.');
            }
        },

        error:function(xhr){
            console.log(xhr.responseText);
        }

    });

});
</script>
