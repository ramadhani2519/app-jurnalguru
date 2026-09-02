<?php

namespace App\Models;

use CodeIgniter\Model;

class KetuaKompetensiModel extends Model
{
    protected $table = 'ketua_kompetensi';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'nama_kompetensi',
        'nama_ketua'
    ];
}
