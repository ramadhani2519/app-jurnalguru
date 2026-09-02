<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'siswa';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nis',
        'nama_siswa',
        'jk',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kelas_id'
    ];

    protected $useTimestamps = true;
}