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

</style>
<div class="container py-4">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">
🕌 Absensi Sholat
</h5>

<?php if(session()->get('role_id') != 4): ?>
<a href="<?= base_url('absensi-sholat/rekap-bulanan') ?>" class="btn btn-light btn-sm">
    <i class="bi bi-file-earmark-excel"></i>
    Rekap Bulanan (Excel)
</a>
<?php endif; ?>

</div>

</div>

<div class="card-body">

<form method="get">

<div class="row mb-3">

<div class="col-md-3">
       <label>Tanggal</label>
    <input type="date"
       id="tanggal"
       name="tanggal"
       value="<?= $tanggal ?>"
       class="form-control"
       onchange="this.form.submit()">
</div>

    <div class="col-md-3">
        <label>Kelas</label>

        <?php if(session()->get('role_id') == 4): ?>

            <?php
                $kelasAktif = '';
                foreach($kelas as $k){
                    if($k['id'] == $kelas_id){
                        $kelasAktif = $k['nama_kelas'];
                    }
                }
            ?>

            <input type="text"
                   class="form-control"
                   value="<?= esc($kelasAktif) ?>"
                   disabled>

            <input type="hidden"
                   id="kelas"
                   name="kelas_id"
                   value="<?= $kelas_id ?>">

        <?php else: ?>

        <select id="kelas"
                name="kelas_id"
                class="form-select"
                onchange="this.form.submit()">

            <option value="">Pilih Kelas</option>

            <?php foreach($kelas as $k): ?>

            <option value="<?= $k['id'] ?>"
                <?= request()->getGet('kelas_id')==$k['id']?'selected':'' ?>>

                <?= $k['nama_kelas'] ?>

            </option>

            <?php endforeach ?>

        </select>

        <?php endif; ?>

    </div>

    <div class="col-md-3">

        <label>Waktu Sholat</label>

        <select id="jenis_sholat"
                name="jenis_sholat"
                class="form-select"
                onchange="this.form.submit()">

            <option value="">- Pilih -</option>

            <option value="dhuha" <?= $jenis_sholat=='dhuha'?'selected':'' ?>>Dhuha</option>
            <option value="zuhur" <?= $jenis_sholat=='zuhur'?'selected':'' ?>>Zuhur</option>
            <option value="ashar" <?= $jenis_sholat=='ashar'?'selected':'' ?>>Ashar</option>

        </select>

    </div>

</div>

<input type="hidden"
       id="tahun"
       value="<?= $tahunAktif['id'] ?>">

<input type="hidden"
       id="semester"
       value="<?= $semesterAktif['id'] ?>">

</form>

<div class="alert alert-success d-flex justify-content-between align-items-center">
<div>
<b>Tanggal :</b>
<?= date('d-m-Y',strtotime($tanggal)) ?>
&nbsp;&nbsp;
<b>Waktu :</b>
<?= $jenis_sholat ? ucfirst($jenis_sholat) : '-' ?>
</div>
<div>
<span id="jumlahAbsen">
<?= count($absensi) ?>
</span>
/
<?= count($siswa) ?> &nbsp;Siswa

</div>
</div>

<hr>
<div class="mb-3">
<button class="btn btn-success btnMassal" data-status="S">✓ Sholat Semua</button>
<button class="btn btn-danger btnMassal" data-status="T">Tidak Sholat Semua</button>
<button class="btn btn-secondary btnMassalHaid" data-status="H">Haid Semua (Perempuan)</button>
</div>

<table class="table table-bordered align-middle">
<thead>
<tr>
<th width="60">No</th>
<th>Nama Siswa</th>
<th class="text-center">Sholat</th>
<th class="text-center">Tidak Sholat</th>
<th class="text-center">Haid</th>
<th class="text-center">Status</th>
</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($siswa as $s): ?>

<?php $perempuan = (strtoupper($s['jk'] ?? '') == 'P'); ?>

<tr>

<td><?= $no++ ?></td>

<td>
<?= $s['nama_siswa'] ?>
<?php if($perempuan): ?>
<span class="badge bg-light text-secondary border">P</span>
<?php endif; ?>
</td>

<td class="text-center">

<input type="radio"
       name="absen_<?= $s['id'] ?>"
       class="absen"
       data-siswa="<?= $s['id'] ?>"
       value="S"
       <?= (($absensi[$s['id']] ?? '') == 'S') ? 'checked' : '' ?>>

</td>

<td class="text-center">

<input type="radio"
       name="absen_<?= $s['id'] ?>"
       class="absen"
       data-siswa="<?= $s['id'] ?>"
       value="T"
       <?= (($absensi[$s['id']] ?? '') == 'T') ? 'checked' : '' ?>>

</td>

<td class="text-center">

<?php if($perempuan): ?>

<input type="radio"
       name="absen_<?= $s['id'] ?>"
       class="absen absenHaid"
       data-siswa="<?= $s['id'] ?>"
       value="H"
       <?= (($absensi[$s['id']] ?? '') == 'H') ? 'checked' : '' ?>>

<?php else: ?>

<span class="text-muted">-</span>

<?php endif; ?>

</td>

<td class="text-center">

<span id="status<?= $s['id'] ?>">

<?php

$status = $absensi[$s['id']] ?? '';

$warna = 'secondary';
$text = 'Belum';

switch($status){

    case 'S':
        $warna='success';
        $text='Sholat';
        break;

    case 'T':
        $warna='danger';
        $text='Tidak Sholat';
        break;

    case 'H':
        $warna='secondary';
        $text='Haid';
        break;

}

?>

<span class="badge bg-<?= $warna ?>">

<?= $text ?>

</span>

</span>

</td>

</tr>

<?php endforeach ?>

</tbody>

</table>

</div>

</div>

</div>

<?= view('template/footer') ?>

<script>

function badge(status){

    switch(status){

        case 'S':
            return { warna:'success', text:'Sholat' };

        case 'T':
            return { warna:'danger', text:'Tidak Sholat' };

        case 'H':
            return { warna:'secondary', text:'Haid' };

        default:
            return { warna:'secondary', text:'Belum' };

    }

}

$('.absen').change(function(){
    let siswa=$(this).data('siswa');
    let status=$(this).val();
    $.ajax({
        url:"<?= base_url('absensi-sholat/simpan') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            tahun_pelajaran_id:$('#tahun').val(),
            semester_id:$('#semester').val(),
            kelas_id:$('#kelas').val(),
            jenis_sholat:$('#jenis_sholat').val(),
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
                alert(res.message || 'Gagal menyimpan absensi sholat.');
            }
        },

        error:function(xhr){
            console.log(xhr.responseText);
        }

    });

});


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
        url:"<?= base_url('absensi-sholat/simpanMassal') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            tahun_pelajaran_id:$('#tahun').val(),
            semester_id:$('#semester').val(),
            kelas_id:$('#kelas').val(),
            jenis_sholat:$('#jenis_sholat').val(),
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
                alert(res.message || 'Gagal menyimpan absensi sholat massal.');
            }

        },

        error:function(xhr){
            console.log(xhr.responseText);
        }

    });

});

$('.btnMassalHaid').click(function(e){

    e.preventDefault();
    let status='H';
    let data=[];

    // Cuma menyasar siswa perempuan (radio dengan class absenHaid)
    $('.absenHaid').each(function(){
        $(this).prop('checked',true);
        data.push({
            siswa_id:$(this).data('siswa'),
            status:status
        });
    });

    if(data.length === 0){
        alert('Tidak ada siswa perempuan di kelas ini.');
        return;
    }

    $.ajax({
        url:"<?= base_url('absensi-sholat/simpanMassal') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            tahun_pelajaran_id:$('#tahun').val(),
            semester_id:$('#semester').val(),
            kelas_id:$('#kelas').val(),
            jenis_sholat:$('#jenis_sholat').val(),
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
                alert(res.message || 'Gagal menyimpan absensi sholat massal.');
            }

        },

        error:function(xhr){
            console.log(xhr.responseText);
        }

    });

});
</script>
