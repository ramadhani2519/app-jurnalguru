<?php

namespace App\Models;

use CodeIgniter\Model;

class WaliAsuhModel extends Model
{
    protected $table            = 'wali_asuh_siswa';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'siswa_id',
        'guru_id',
        'tahun_pelajaran_id',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Daftar siswa lengkap dengan guru wali-nya (kalau sudah dibagi)
     * untuk tahun pelajaran tertentu. Bisa difilter per kelas dan/atau
     * per guru wali. Siswa yang belum dibagi tetap ikut tampil
     * (guru_id akan NULL) supaya Wakasek Kesiswaan bisa lihat sisa
     * yang belum dibagikan.
     */
    public function getDistribusi(int $tahunPelajaranId, ?int $kelasId = null, ?int $guruId = null): array
    {
        $builder = $this->db->table('siswa')
            ->select('
                siswa.id as siswa_id,
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas,
                wali_asuh_siswa.id as distribusi_id,
                wali_asuh_siswa.guru_id,
                users.nama as nama_guru_wali
            ')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join(
                'wali_asuh_siswa',
                "wali_asuh_siswa.siswa_id = siswa.id AND wali_asuh_siswa.tahun_pelajaran_id = {$tahunPelajaranId}",
                'left'
            )
            ->join('users', 'users.id = wali_asuh_siswa.guru_id', 'left');

        if (!empty($kelasId)) {
            $builder->where('siswa.kelas_id', $kelasId);
        }

        if (!empty($guruId)) {
            $builder->where('wali_asuh_siswa.guru_id', $guruId);
        }

        return $builder
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Simpan/ubah guru wali untuk sekumpulan siswa sekaligus
     * (dipakai untuk pembagian massal oleh Wakasek Kesiswaan).
     * Kalau siswa itu sudah punya guru wali di tahun yang sama,
     * datanya di-update, bukan dobel insert (sesuai UNIQUE KEY
     * siswa_id + tahun_pelajaran_id).
     */
    public function bagikan(array $siswaIds, int $guruId, int $tahunPelajaranId): void
    {
        foreach ($siswaIds as $siswaId) {

            $existing = $this->where('siswa_id', $siswaId)
                ->where('tahun_pelajaran_id', $tahunPelajaranId)
                ->first();

            if ($existing) {
                $this->update($existing['id'], ['guru_id' => $guruId]);
            } else {
                $this->insert([
                    'siswa_id'           => $siswaId,
                    'guru_id'            => $guruId,
                    'tahun_pelajaran_id' => $tahunPelajaranId,
                ]);
            }
        }
    }

    /**
     * Rekap jumlah siswa asuh & hasil pembinaan (pelanggaran) per guru
     * wali, untuk tahun pelajaran tertentu. Dipakai di halaman rekap
     * dan dashboard Wakasek Kesiswaan.
     */
    public function rekapPerGuruWali(int $tahunPelajaranId): array
    {
        return $this->db->table('users')
            ->select('
                users.id as guru_id,
                users.nama as nama_guru_wali,
                COUNT(DISTINCT wali_asuh_siswa.siswa_id) as jumlah_siswa_asuh,
                COUNT(pelanggaran_siswa.id) as jumlah_pembinaan
            ')
            ->join('wali_asuh_siswa', "wali_asuh_siswa.guru_id = users.id AND wali_asuh_siswa.tahun_pelajaran_id = {$tahunPelajaranId}")
            ->join('pelanggaran_siswa', 'pelanggaran_siswa.siswa_id = wali_asuh_siswa.siswa_id', 'left')
            ->groupBy('users.id')
            ->orderBy('jumlah_pembinaan', 'DESC')
            ->get()
            ->getResultArray();
    }
}
