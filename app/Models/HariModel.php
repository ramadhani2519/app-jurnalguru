<?php

namespace App\Models;

use CodeIgniter\Model;

class HariModel extends Model
{
    protected $table='hari';
    protected $primaryKey='id';
    protected $returnType='array';

    protected $allowedFields=[

        'nama_hari',
        'urutan'

    ];

    protected $useTimestamps=false;
}