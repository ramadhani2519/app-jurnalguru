<?php

namespace App\Models;

use CodeIgniter\Model;

class JamModel extends Model
{
    protected $table            = 'jam_pelajaran';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode_jam',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'istirahat',
        'aktif_jumat'
    ];

    protected $useTimestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Semua Jam
    |--------------------------------------------------------------------------
    */
    public function getAll()
    {
        return $this->orderBy('jam_ke', 'ASC')->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Jam Aktif Senin-Kamis
    |--------------------------------------------------------------------------
    */
    public function getJamBiasa()
    {
        return $this->orderBy('jam_ke', 'ASC')
                    ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Jam Hari Jumat
    |--------------------------------------------------------------------------
    */
    public function getJamJumat()
    {
        return $this->where('aktif_jumat', 'Y')
                    ->orderBy('jam_ke', 'ASC')
                    ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Jam Tanpa Istirahat
    |--------------------------------------------------------------------------
    */
    public function getJamPelajaran()
    {
        return $this->where('istirahat', 'N')
                    ->orderBy('jam_ke', 'ASC')
                    ->findAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Jam
    |--------------------------------------------------------------------------
    */
    public function detail($id)
    {
        return $this->find($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Format Tampilan
    |--------------------------------------------------------------------------
    */
    public function listDropdown()
    {
        $data = [];

        foreach ($this->orderBy('jam_ke', 'ASC')->findAll() as $row) {

            $data[$row['id']] =
                'Jam ' . $row['jam_ke'] .
                ' (' .
                substr($row['jam_mulai'],0,5) .
                ' - ' .
                substr($row['jam_selesai'],0,5) .
                ')';
        }

        return $data;
    }
}