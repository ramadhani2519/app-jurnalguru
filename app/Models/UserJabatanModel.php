<?php

namespace App\Models;

use CodeIgniter\Model;

class UserJabatanModel extends Model
{
    protected $table = 'user_jabatan';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'jabatan_id',
        'kelas_id',
        'jurusan'
    ];
}
