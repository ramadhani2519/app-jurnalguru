<?php

namespace App\Models;

use CodeIgniter\Model;

class WaliKelasModel extends Model
{
    protected $table = 'wali_kelas';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'kelas_id',
        'nama_wali'
    ];
}
