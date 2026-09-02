<div class="modal fade"
id="modalImport">

<div class="modal-dialog">

<div class="modal-content">

<form action="<?= base_url('siswa/import') ?>"
method="post"
enctype="multipart/form-data">

<div class="modal-header">

<h5>Import Excel</h5>

</div>

<div class="modal-body">

<input type="file"
name="file_excel"
class="form-control"
accept=".xlsx,.xls"
required>

<br>

<small>

Format:

NIS | Nama_Siswa | JK | Tmp_lahir | Tgl_lahir | Alamat | Kelas_ID

</small>

</div>

<div class="modal-footer">

<button class="btn btn-success">

Import

</button>

</div>

</form>

</div>

</div>

</div>