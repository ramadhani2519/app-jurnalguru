<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table = 'jadwal';

    protected $primaryKey = 'id';

    protected $allowedFields = [

        'tahun_pelajaran_id',
        'semester_id',
        'kelas_id',
        'hari_id',
        'jam_id',
        'mapel_id',
        'guru_id',
        'ruangan_id'

    ];

    protected $useTimestamps = true;
}