<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table            = 'semester';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'semester',
        'aktif'
    ];

    protected bool $allowEmptyInserts = false;

    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semester aktif
     */
    public function getAktif()
    {
        return $this->where('aktif', 'Y')->first();
    }

    /**
     * Aktifkan semester
     */
    public function setAktif($id)
    {
        $this->set(['aktif' => 'N'])->update();

        return $this->update($id, [
            'aktif' => 'Y'
        ]);
    }
}