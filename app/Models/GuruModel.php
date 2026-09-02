<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields = [
        'role_id',
        'nama',
        'nip',
        'username',
        'password',
        'email',
        'no_hp'
    ];

    protected $useTimestamps = true;

    public function getGuru()
    {
        return $this->select('users.*')
                    ->where('role_id',2)
                    ->orderBy('nama','ASC');
    }
}