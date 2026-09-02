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
min-width:130px;
}

.btnMassal{
margin-right:5px;
margin-bottom:5px;
}

.jamSejakWrap{
display:none;
margin-top:4px;
}

</style>
<div class="container py-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<h5 class="mb-0">
📋 Absensi Siswa
</h5>

<?php if(session()->get('role_id') != 4): ?>
<a href="<?= base_url('absensi/rekap-bulanan') ?>" class="btn btn-light btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" style="vertical-align: -2px; margin-right: 2px;">
        <rect x="1" y="1" width="14" height="14" rx="2" fill="#1D6F42"/>
        <rect x="3" y="3" width="10" height="10" fill="#fff"/>
        <line x1="3" y1="6.3" x2="13" y2="6.3" stroke="#1D6F42" stroke-width="0.8"/>
        <line x1="3" y1="9.6" x2="13" y2="9.6" stroke="#1D6F42" stroke-width="0.8"/>
        <line x1="6.3" y1="3" x2="6.3" y2="13" stroke="#1D6F42" stroke-width="0.8"/>
        <line x1="9.6" y1="3" x2="9.6" y2="13" stroke="#1D6F42" stroke-width="0.8"/>
    </svg>
    Rekap Bulanan (Excel)
</a>
<?php endif; ?>

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

<?php if(!empty($bukanWaliKelasManapun)): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    Anda belum tercatat sebagai Wali Kelas kelas manapun, jadi belum bisa mengisi Absensi Siswa.
    Hubungi Admin kalau ini seharusnya tidak terjadi.
</div>
<?php endif ?>

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

    <div class="col-md-4">
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

    <?php if(session()->get('role_id') != 4): ?>

  <div class="col-md-2">
    <label>Cetak&nbsp;</label>
<a href="<?= base_url('absensi/cetak?tanggal='.$tanggal.'&kelas_id='.$kelas_id) ?>"
class="btn btn-danger"
target="_blank">

<i class="bi bi-printer"></i>
Cetak Absen

</a>
</div>

<div class="col-md-2">
    <label>Rekap</label>
<a href="<?= base_url('absensi/laporan-absen') ?>"
class="btn btn-primary">

<i class="bi bi-printer"></i>
Cetak Rekap

</a>
</div>

<?php endif; ?>

</div>

<input type="hidden"
       id="tahun"
       value="<?= $tahunAktif['id'] ?>">

<input type="hidden"
       id="semester"
       value="<?= $semesterAktif['id'] ?>">

</form>

<div class="alert alert-primary d-flex justify-content-between align-items-center">
<div>
<b>Tanggal :</b>
<?= date('d-m-Y',strtotime($tanggal)) ?>
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
<button class="btn btn-success btnMassal" data-status="H">✓ Hadir Semua</button>
<button class="btn btn-warning btnMassal" data-status="S">Sakit Semua</button>
<button class="btn btn-info btnMassal" data-status="I">Izin Semua</button>
</div>

<table class="table table-bordered align-middle">
<thead>
<tr>
<th width="60">No</th>
<th>Nama Siswa</th>

<th class="text-center">Hadir</th>
<th class="text-center">Sakit</th>
<th class="text-center">Izin</th>
<th class="text-center">Pulang Cepat</th>
<th class="text-center">Bolos</th>
<th class="text-center">Status</th>

</tr>

</thead>

<tbody>

<?php $no=1; ?>

<?php foreach($siswa as $s): ?>

<?php
    $statusSaatIni = $absensi[$s['id']]['status'] ?? '';
    $jamSejakSaatIni = $absensi[$s['id']]['jam_sejak'] ?? '';
?>

<tr>

<td><?= $no++ ?></td>

<td>
<?= $s['nama_siswa'] ?>
</td>

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
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen butuhJamSejak" data-siswa="<?= $s['id'] ?>" value="P"
       <?= $statusSaatIni == 'P' ? 'checked' : '' ?>>

<div class="jamSejakWrap" id="jamSejakWrap_<?= $s['id'] ?>" style="<?= $statusSaatIni=='P' ? 'display:block;' : '' ?>">
    <select class="form-select form-select-sm mt-1 jamSejakSelect" id="jamSejak_<?= $s['id'] ?>" data-siswa="<?= $s['id'] ?>">
        <option value="">Sejak Jam</option>
        <?php for($i=1;$i<=8;$i++): ?>
        <option value="<?= $i ?>" <?= $jamSejakSaatIni==$i ? 'selected' : '' ?>>Jam <?= $i ?></option>
        <?php endfor; ?>
    </select>
</div>

</td>

<td class="text-center">
<input type="radio" name="absen_<?= $s['id'] ?>" class="absen butuhJamSejak" data-siswa="<?= $s['id'] ?>" value="B"
       <?= $statusSaatIni == 'B' ? 'checked' : '' ?>>

<div class="jamSejakWrap" id="jamSejakWrapB_<?= $s['id'] ?>" style="<?= $statusSaatIni=='B' ? 'display:block;' : '' ?>">
    <select class="form-select form-select-sm mt-1 jamSejakSelectB" id="jamSejakB_<?= $s['id'] ?>" data-siswa="<?= $s['id'] ?>">
        <option value="">Sejak Jam</option>
        <?php for($i=1;$i<=8;$i++): ?>
        <option value="<?= $i ?>" <?= $jamSejakSaatIni==$i ? 'selected' : '' ?>>Jam <?= $i ?></option>
        <?php endfor; ?>
    </select>
</div>

</td>

<td class="text-center">

<span id="status<?= $s['id'] ?>">

<?php

$warna = 'secondary';
$text = 'Belum';

switch($statusSaatIni){
    case 'H': $warna='success'; $text='Hadir'; break;
    case 'S': $warna='warning'; $text='Sakit'; break;
    case 'I': $warna='info'; $text='Izin Keluarga'; break;
    case 'P': $warna='primary'; $text='Pulang Cepat'; break;
    case 'B': $warna='danger'; $text='Bolos / Hilang'; break;
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
        case 'H': return { warna:'success', text:'Hadir' };
        case 'S': return { warna:'warning', text:'Sakit' };
        case 'I': return { warna:'info', text:'Izin Keluarga' };
        case 'P': return { warna:'primary', text:'Pulang Cepat' };
        case 'B': return { warna:'danger', text:'Bolos / Hilang' };
        default: return { warna:'secondary', text:'Belum' };
    }
}

// Tampilkan/sembunyikan dropdown "Sejak Jam" sesuai radio yang dipilih
$('.absen').change(function(){

    let siswa = $(this).data('siswa');
    let status = $(this).val();

    $('#jamSejakWrap_'+siswa).hide();
    $('#jamSejakWrapB_'+siswa).hide();

    let jamSejak = null;

    if(status === 'P'){
        $('#jamSejakWrap_'+siswa).show();
        jamSejak = $('#jamSejak_'+siswa).val() || null;
    } else if(status === 'B'){
        $('#jamSejakWrapB_'+siswa).show();
        jamSejak = $('#jamSejakB_'+siswa).val() || null;
    }

    simpanAbsen(siswa, status, jamSejak);

});

$('.jamSejakSelect, .jamSejakSelectB').change(function(){
    let siswa = $(this).data('siswa');
    let status = $('input[name="absen_'+siswa+'"]:checked').val();
    simpanAbsen(siswa, status, $(this).val());
});

function simpanAbsen(siswa, status, jamSejak){

    $.ajax({
        url:"<?= base_url('absensi/simpan') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            tahun_pelajaran_id:$('#tahun').val(),
            semester_id:$('#semester').val(),
            kelas_id:$('#kelas').val(),
            siswa_id:siswa,
            status:status,
            jam_sejak:jamSejak
        },

        success:function(res){
            if(res.status=="success"){
                let b=badge(status);
                $('#status'+siswa).html(
                    '<span class="badge bg-'+b.warna+'">'+b.text+'</span>'
                );
                if(status === 'B'){
                    // beri tahu kalau otomatis masuk ke Pembinaan Siswa
                    $('#status'+siswa).append(' <i class="bi bi-flag-fill text-danger" title="Otomatis tercatat di Pembinaan Siswa"></i>');
                }
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
        url:"<?= base_url('absensi/simpanMassal') ?>",
        type:"POST",
        dataType:"json",
        data:{
            tanggal:$('#tanggal').val(),
            tahun_pelajaran_id:$('#tahun').val(),
            semester_id:$('#semester').val(),
            kelas_id:$('#kelas').val(),
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
