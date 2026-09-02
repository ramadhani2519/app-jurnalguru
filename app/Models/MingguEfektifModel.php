<?php

namespace App\Models;

use CodeIgniter\Model;

class MingguEfektifModel extends Model
{
    protected $table = 'minggu_efektif';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'tahun_pelajaran_id',
        'semester_id',
        'kelas_id',
        'bulan',
        'jumlah_minggu',
        'minggu_tidak_efektif',
        'keterangan',
    ];
}
