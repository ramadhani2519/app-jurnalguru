<?php

namespace App\Models;

use CodeIgniter\Model;

class RuanganModel extends Model
{
    protected $table='ruangan';
    protected $primaryKey='id';
    protected $returnType='array';

    protected $allowedFields=[

        'kode_ruang',
        'nama_ruang',
        'keterangan'

    ];

    protected $useTimestamps=true;
}