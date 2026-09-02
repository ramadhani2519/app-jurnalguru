<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalModel extends Model
{
    protected $table = 'jurnal';

   protected $allowedFields = [
    'user_id',
    'tahun_pelajaran_id',
    'semester_id',
    'kelas_id',
    'mapel_id',
    'tanggal',
    'jam_ke',
    'jam_masuk',
    'jam_selesai',
    'materi',
    'keterangan',
    'status',
    'foto'
];

    protected $useTimestamps = true;
}