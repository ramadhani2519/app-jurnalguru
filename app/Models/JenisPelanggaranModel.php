<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisPelanggaranModel extends Model
{
    protected $table = 'jenis_pelanggaran';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_pelanggaran',
    ];

    protected $useTimestamps = true;
}
