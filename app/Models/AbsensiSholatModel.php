<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiSholatModel extends Model
{
    protected $table = 'absensi_sholat';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'tanggal',
        'tahun_pelajaran_id',
        'semester_id',
        'kelas_id',
        'jenis_sholat',
        'guru_id',
        'siswa_id',
        'status'
    ];
}
