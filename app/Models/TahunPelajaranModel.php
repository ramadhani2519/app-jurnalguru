<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunPelajaranModel extends Model
{
    protected $table            = 'tahun_pelajaran';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'tahun',
        'aktif'
    ];

    protected bool $allowEmptyInserts = false;

    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil tahun pelajaran aktif
     */
    public function getAktif()
    {
        return $this->where('aktif', 'Y')->first();
    }

    /**
     * Aktifkan satu tahun pelajaran
     */
    public function setAktif($id)
    {
        $this->set(['aktif' => 'N'])->update();

        return $this->update($id, [
            'aktif' => 'Y'
        ]);
    }
}