<?php

namespace App\Controllers;

use App\Models\PelanggaranModel;
use App\Models\PembinaanSiswaModel;
use App\Models\SiswaModel;

/**
 * Halaman Ketua Jurusan untuk:
 * 1. Memantau SEMUA siswa di jurusannya yang punya catatan pelanggaran
 *    (bukan cuma yang sudah dieskalasi ke tingkat Ketua Jurusan).
 * 2. Melihat sejauh mana tindak lanjutnya (Guru Wali / Wali Kelas /
 *    Ketua Jurusan, mana yang sudah dan mana yang masih ditunggu).
 * 3. Mendapat notifikasi & mencatat pembinaan untuk siswa yang sudah
 *    sampai tingkat eskalasi ke-3 (sudah dibina Guru Wali + Wali Kelas,
 *    tapi melanggar lagi).
 *
 * Jurusan yang dipantau diambil dari user_jabatan.jurusan (diisi admin
 * lewat menu Data Pengguna, saat memberi jabatan "Ketua Jurusan").
 * Siswa dicocokkan lewat kelas.jurusan (diisi admin lewat menu Kelas).
 */
class KetuaJurusanBinaan extends BaseController
{
    protected $pelanggaran;
    protected $pembinaanSiswa;
    protected $siswa;

    private const AMBANG_PELANGGARAN = 2;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        if (!$this->hasJabatan('Ketua Jurusan')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->pelanggaran    = new PelanggaranModel();
        $this->pembinaanSiswa = new PembinaanSiswaModel();
        $this->siswa          = new SiswaModel();
    }

    /**
     * Ambil nama jurusan yang diampu user yang sedang login (dari
     * jabatan "Ketua Jurusan" di user_jabatan).
     */
    private function jurusanSaya(int $userId): ?string
    {
        $db = \Config\Database::connect();

        $row = $db->table('user_jabatan')
            ->select('user_jabatan.jurusan')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->where('user_jabatan.user_id', $userId)
            ->where('jabatan.nama_jabatan', 'Ketua Jurusan')
            ->where('user_jabatan.jurusan IS NOT NULL')
            ->get()
            ->getRowArray();

        return isset($row['jurusan']) ? trim($row['jurusan']) : null;
    }

    /**
     * Terjemahkan jumlah pembinaan yang sudah dicatat + sisa pelanggaran
     * yang belum ditindaklanjuti menjadi status yang mudah dibaca.
     */
    private function tentukanStatus(int $jumlahPelanggaran, int $jumlahPembinaan): array
    {
        if ($jumlahPelanggaran === 0) {
            return ['label' => 'Tidak ada pelanggaran', 'warna' => 'success', 'perlu_tindakan_ketua' => false];
        }

        $belumDitindaklanjuti = $jumlahPelanggaran - ($jumlahPembinaan * self::AMBANG_PELANGGARAN);

        if ($jumlahPembinaan === 0) {
            if ($belumDitindaklanjuti >= self::AMBANG_PELANGGARAN) {
                return ['label' => 'Menunggu tindakan Guru Wali', 'warna' => 'warning', 'perlu_tindakan_ketua' => false];
            }
            return ['label' => 'Terpantau', 'warna' => 'secondary', 'perlu_tindakan_ketua' => false];
        }

        if ($jumlahPembinaan === 1) {
            if ($belumDitindaklanjuti >= self::AMBANG_PELANGGARAN) {
                return ['label' => 'Menunggu tindakan Wali Kelas', 'warna' => 'warning', 'perlu_tindakan_ketua' => false];
            }
            return ['label' => 'Sudah dibina Guru Wali', 'warna' => 'info', 'perlu_tindakan_ketua' => false];
        }

        // jumlahPembinaan >= 2 -> sudah sampai/lewat tingkat Wali Kelas
        if ($belumDitindaklanjuti >= self::AMBANG_PELANGGARAN) {
            return ['label' => 'Menunggu tindakan Ketua Jurusan', 'warna' => 'danger', 'perlu_tindakan_ketua' => true];
        }

        return [
            'label' => $jumlahPembinaan === 2 ? 'Sudah dibina s.d. Wali Kelas' : 'Sudah dibina s.d. Ketua Jurusan',
            'warna' => 'info',
            'perlu_tindakan_ketua' => false,
        ];
    }

    /**
     * Halaman utama: monitoring semua siswa di jurusan yang diampu.
     */
    public function index()
    {
        $ketuaId = session()->get('id');
        $jurusan = $this->jurusanSaya((int) $ketuaId);

        if (!$jurusan) {
            $data = [
                'jurusanBelumDiset' => true,
                'daftarSiswa'       => [],
                'siswaPerluTindakan'=> [],
                'riwayatPembinaan'  => [],
                'ambangPelanggaran' => self::AMBANG_PELANGGARAN,
            ];

            return view('ketua_jurusan_binaan/siswa_binaan', $data);
        }

        // Hanya siswa dengan minimal 1 pelanggaran yang ditampilkan,
        // supaya tabelnya fokus ke siswa yang benar-benar bermasalah.
        $daftarSiswa = $this->siswa
            ->select('siswa.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('UPPER(TRIM(kelas.jurusan)) =', strtoupper($jurusan))
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll();

        $statusEfektif = [];

        if (!empty($daftarSiswa)) {
            $siswaIds = array_column($daftarSiswa, 'id');

            $statusEfektif = $this->statusPembinaanEfektif($siswaIds);
        }

        $siswaPerluTindakan = [];
        $daftarSiswaBermasalah = [];

        foreach ($daftarSiswa as $s) {
            $sid = $s['id'];

            $jumlahPelanggaran = $statusEfektif[$sid]['jumlah_pelanggaran'] ?? 0;
            $jumlahPembinaan   = $statusEfektif[$sid]['jumlah_pembinaan'] ?? 0;

            // Lewati siswa yang belum pernah melanggar sama sekali,
            // supaya monitoring fokus ke yang bermasalah saja.
            if ($jumlahPelanggaran === 0) {
                continue;
            }

            $status = $this->tentukanStatus($jumlahPelanggaran, $jumlahPembinaan);

            $s['jumlah_pelanggaran']   = $jumlahPelanggaran;
            $s['jumlah_pembinaan']     = $jumlahPembinaan;
            $s['sudah_guru_wali']      = $jumlahPembinaan >= 1;
            $s['sudah_wali_kelas']     = $jumlahPembinaan >= 2;
            $s['sudah_ketua_jurusan']  = $jumlahPembinaan >= 3;
            $s['status_label']        = $status['label'];
            $s['status_warna']        = $status['warna'];
            $s['perlu_tindakan']      = $status['perlu_tindakan_ketua'];

            $daftarSiswaBermasalah[] = $s;

            if ($s['perlu_tindakan']) {
                $siswaPerluTindakan[] = $s;
            }
        }

        $riwayatPembinaan = $this->pembinaanSiswa->riwayatPerKetuaJurusan((int) $ketuaId);

        $data = [
            'jurusanBelumDiset'  => false,
            'jurusan'            => $jurusan,
            'daftarSiswa'        => $daftarSiswaBermasalah,
            'siswaPerluTindakan' => $siswaPerluTindakan,
            'riwayatPembinaan'   => $riwayatPembinaan,
            'ambangPelanggaran'  => self::AMBANG_PELANGGARAN,
        ];

        return view('ketua_jurusan_binaan/siswa_binaan', $data);
    }

    /**
     * Pastikan siswa yang diakses berada di jurusan yang diampu user
     * yang sedang login.
     */
    private function pastikanSiswaJurusanSaya(int $siswaId, string $jurusan): ?array
    {
        $siswa = $this->siswa
            ->select('siswa.*, kelas.nama_kelas, kelas.jurusan')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->find($siswaId);

        if (!$siswa || strtoupper(trim($siswa['jurusan'] ?? '')) !== strtoupper($jurusan)) {
            return null;
        }

        return $siswa;
    }

    /**
     * Form untuk mencatat tindak lanjut pembinaan (tindak lanjut + foto
     * bukti) untuk 1 siswa yang sudah dieskalasi ke Ketua Jurusan.
     */
    public function formPembinaan($siswaId)
    {
        $ketuaId = session()->get('id');
        $jurusan = $this->jurusanSaya((int) $ketuaId);

        if (!$jurusan) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Jurusan Anda belum diatur oleh admin.');
        }

        $siswa = $this->pastikanSiswaJurusanSaya((int) $siswaId, $jurusan);

        if (!$siswa) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa di jurusan Anda.');
        }

        $statusEfektifSiswa = $this->statusPembinaanEfektif([(int) $siswaId])[(int) $siswaId]
            ?? ['jumlah_pelanggaran' => 0, 'jumlah_pembinaan' => 0];

        $jumlahPelanggaran = $statusEfektifSiswa['jumlah_pelanggaran'];
        $jumlahPembinaan   = $statusEfektifSiswa['jumlah_pembinaan'];

        if ($jumlahPembinaan < 2) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Siswa ini belum sampai pada tahap eskalasi ke Ketua Jurusan (Wali Kelas belum menindak).');
        }

        $data = [
            'siswa'             => $siswa,
            'jumlahPelanggaran' => $jumlahPelanggaran,
        ];

        return view('ketua_jurusan_binaan/form_pembinaan', $data);
    }

    /**
     * Simpan catatan tindak lanjut pembinaan oleh Ketua Jurusan.
     */
    public function simpanPembinaan()
    {
        $ketuaId = session()->get('id');
        $siswaId = (int) $this->request->getPost('siswa_id');
        $jurusan = $this->jurusanSaya((int) $ketuaId);

        if (!$jurusan) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Jurusan Anda belum diatur oleh admin.');
        }

        $siswa = $this->pastikanSiswaJurusanSaya($siswaId, $jurusan);

        if (!$siswa) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa di jurusan Anda.');
        }

        $jumlahPembinaan = $this->statusPembinaanEfektif([$siswaId])[$siswaId]['jumlah_pembinaan'] ?? 0;

        if ($jumlahPembinaan < 2) {
            return redirect()->to('/ketua-jurusan-binaan/siswa')
                ->with('error', 'Siswa ini belum sampai pada tahap eskalasi ke Ketua Jurusan (Wali Kelas belum menindak).');
        }

        $tindakLanjut = trim((string) $this->request->getPost('tindak_lanjut'));
        $tanggal      = $this->request->getPost('tanggal') ?: date('Y-m-d');

        if ($tindakLanjut === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Uraian tindak lanjut pembinaan wajib diisi.');
        }

        $file = $this->request->getFile('foto');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->withInput()
                ->with('error', 'Foto bukti pembinaan wajib dilampirkan.');
        }

        $fotoNama = null;

        if (!$file->hasMoved()) {
            $folderFoto = FCPATH . 'assets/img/pembinaan';

            if (!is_dir($folderFoto)) {
                mkdir($folderFoto, 0777, true);
            }

            $fotoNama = $file->getRandomName();
            $file->move($folderFoto, $fotoNama);
        }

        $this->pembinaanSiswa->save([
            'siswa_id'         => $siswaId,
            'ketua_jurusan_id' => $ketuaId,
            'tingkat'          => 'ketua_jurusan',
            'tanggal'          => $tanggal,
            'tindak_lanjut'    => $tindakLanjut,
            'foto'             => $fotoNama,
        ]);

        return redirect()->to('/ketua-jurusan-binaan/siswa')
            ->with('success', 'Tindak lanjut pembinaan berhasil dicatat.');
    }
}
