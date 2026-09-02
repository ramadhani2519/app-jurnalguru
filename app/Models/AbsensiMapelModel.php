<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiMapelModel extends Model
{
    protected $table = 'absensi_mapel';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [

        'tanggal',

        'tahun_pelajaran_id',

        'semester_id',

        'guru_id',

        'mapel_id',

        'kelas_id',

        'jam_ke_display',

        'siswa_id',

        'status',

        'keterangan',

    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

}
