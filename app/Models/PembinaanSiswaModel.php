<?php

namespace App\Models;

use CodeIgniter\Model;

class PembinaanSiswaModel extends Model
{
    protected $table      = 'pembinaan_siswa';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'siswa_id',
        'guru_wali_id',
        'wali_kelas_id',
        'ketua_jurusan_id',
        'tingkat',
        'tanggal',
        'tindak_lanjut',
        'foto',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Riwayat tindak lanjut pembinaan untuk 1 guru wali (semua siswa
     * asuhnya), lengkap dengan nama & kelas siswa. Dipakai di halaman
     * "Siswa Asuh Saya".
     */
    public function riwayatPerGuruWali(int $guruWaliId): array
    {
        return $this->select('
                pembinaan_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = pembinaan_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('pembinaan_siswa.guru_wali_id', $guruWaliId)
            ->orderBy('pembinaan_siswa.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Riwayat tindak lanjut pembinaan untuk 1 Wali Kelas (semua siswa
     * di kelas yang dia jabat), lengkap dengan nama & kelas siswa.
     * Dipakai di halaman "Siswa Perlu Pembinaan" milik Wali Kelas.
     */
    public function riwayatPerWaliKelas(int $waliKelasId): array
    {
        return $this->select('
                pembinaan_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = pembinaan_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('pembinaan_siswa.wali_kelas_id', $waliKelasId)
            ->orderBy('pembinaan_siswa.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Riwayat tindak lanjut pembinaan yang dicatat oleh Ketua Jurusan
     * (tingkat eskalasi ke-3). Dipakai di halaman "Siswa Perlu
     * Pembinaan" milik Ketua Jurusan.
     */
    public function riwayatPerKetuaJurusan(int $ketuaJurusanId): array
    {
        return $this->select('
                pembinaan_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = pembinaan_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('pembinaan_siswa.ketua_jurusan_id', $ketuaJurusanId)
            ->orderBy('pembinaan_siswa.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Jumlah tindak lanjut pembinaan yang sudah dicatat, per siswa
     * (dari sekumpulan siswa_id). Dipakai untuk menghitung berapa
     * kasus yang sudah ditindaklanjuti vs jumlah pelanggaran.
     */
    public function jumlahPerSiswa(array $siswaIds): array
    {
        if (empty($siswaIds)) {
            return [];
        }

        $rows = $this->select('siswa_id, COUNT(*) as jumlah')
            ->whereIn('siswa_id', $siswaIds)
            ->groupBy('siswa_id')
            ->findAll();

        $hasil = [];
        foreach ($rows as $r) {
            $hasil[$r['siswa_id']] = (int) $r['jumlah'];
        }

        return $hasil;
    }
}
