<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'role_id',
        'nama',
        'nip',
        'username',
        'password',
        'email',
        'no_hp',
        'foto',
        'kelas_id'
    ];
}
