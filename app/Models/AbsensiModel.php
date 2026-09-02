<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table = 'absensi';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [

        'tanggal',

        'tahun_pelajaran_id',

        'semester_id',

        'kelas_id',

        'mapel_id',

        'guru_id',

        'jam_ke',

        'jam_sejak',

        'siswa_id',

        'status'

    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

}
