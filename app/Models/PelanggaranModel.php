<?php

namespace App\Models;

use CodeIgniter\Model;

class PelanggaranModel extends Model
{
    protected $table = 'pelanggaran_siswa';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'tanggal',
        'kelas_id',
        'siswa_id',
        'uraian_pelanggaran',
        'keterangan',
        'user_id'
    ];

    protected $useTimestamps = true;

    public function getData()
    {
        return $this->select('
                pelanggaran_siswa.*,
                siswa.nama_siswa,
                kelas.nama_kelas
            ')
            ->join('siswa','siswa.id=pelanggaran_siswa.siswa_id')
            ->join('kelas','kelas.id=pelanggaran_siswa.kelas_id')
            ->orderBy('tanggal','DESC');
    }
}