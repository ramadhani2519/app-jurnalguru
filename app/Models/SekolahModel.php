<?php

namespace App\Models;

use CodeIgniter\Model;

class SekolahModel extends Model
{
    protected $table = 'sekolah';

    protected $primaryKey = 'id';

    protected $useTimestamps = true;

    protected $allowedFields = [

        'nama_sekolah',
        'nama_pemerintah',
        'nama_dinas',
        'npsn',
        'nss',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'kompetensi_keahlian',
        'kepala_sekolah',
        'nip_kepala',
        'logo',
        'logo_provinsi',
        'latitude',
        'longitude'

    ];
}