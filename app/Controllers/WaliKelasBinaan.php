<?php

namespace App\Controllers;

use App\Models\PelanggaranModel;
use App\Models\PembinaanSiswaModel;
use App\Models\SiswaModel;

/**
 * Ronde ke-2 eskalasi pembinaan siswa: kalau seorang siswa sudah pernah
 * dibina oleh Guru Wali (lihat GuruWali::simpanPembinaan) tapi kemudian
 * melanggar lagi sebanyak ambang batas, penanganan selanjutnya menjadi
 * tanggung jawab Wali Kelas dari kelas siswa tersebut (bukan Guru Wali
 * lagi), lengkap dengan notifikasi di halaman ini.
 *
 * "Wali Kelas" di sini mengacu ke jabatan (tabel jabatan + user_jabatan
 * dengan kelas_id), BUKAN tabel wali_kelas lama yang cuma nama bebas
 * untuk kop surat/PDF.
 */
class WaliKelasBinaan extends BaseController
{
    protected $pelanggaran;
    protected $pembinaanSiswa;
    protected $siswa;

    // Sama dengan ambang batas di GuruWali: berapa kali pelanggaran baru
    // setelah dibina, sampai siswa dianggap perlu dibina lagi.
    private const AMBANG_PELANGGARAN = 2;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        if (!$this->hasJabatan('Wali Kelas')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->pelanggaran    = new PelanggaranModel();
        $this->pembinaanSiswa = new PembinaanSiswaModel();
        $this->siswa          = new SiswaModel();
    }

    /**
     * Ambil daftar kelas_id di mana user yang sedang login tercatat
     * sebagai Wali Kelas (dari data user_jabatan), sama seperti pola
     * yang dipakai di Absensi::kelasWaliDariGuru().
     */
    private function kelasWaliDariGuru(int $userId): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('user_jabatan')
            ->select('user_jabatan.kelas_id')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->where('user_jabatan.user_id', $userId)
            ->where('jabatan.nama_jabatan', 'Wali Kelas')
            ->get()
            ->getResultArray();

        return array_values(array_filter(array_column($rows, 'kelas_id')));
    }

    /**
     * Pastikan siswa yang diakses benar-benar berada di salah satu
     * kelas yang diwalikan oleh user yang sedang login.
     */
    private function pastikanSiswaKelasWali(int $siswaId, array $kelasIds): ?array
    {
        if (empty($kelasIds)) {
            return null;
        }

        $siswa = $this->siswa
            ->select('siswa.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->find($siswaId);

        if (!$siswa || !in_array($siswa['kelas_id'], $kelasIds)) {
            return null;
        }

        return $siswa;
    }

    /**
     * Daftar siswa di kelas yang diwalikan, lengkap dengan status
     * "perlu tindakan pembinaan" (notifikasi) untuk siswa yang sudah
     * pernah dibina Guru Wali tapi melanggar lagi.
     */
    public function index()
    {
        $guruId   = session()->get('id');
        $kelasIds = $this->kelasWaliDariGuru((int) $guruId);

        $daftarSiswa = [];

        if (!empty($kelasIds)) {
            $daftarSiswa = $this->siswa
                ->select('siswa.*, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->whereIn('siswa.kelas_id', $kelasIds)
                ->orderBy('siswa.nama_siswa', 'ASC')
                ->findAll();
        }

        $statusEfektif = [];

        if (!empty($daftarSiswa)) {
            $siswaIds = array_column($daftarSiswa, 'id');

            $statusEfektif = $this->statusPembinaanEfektif($siswaIds);
        }

        // Perlu tindakan Wali Kelas: siswa sudah pernah dibina minimal
        // 1x (oleh Guru Wali di ronde pertama) DAN pelanggaran barunya
        // sejak pembinaan terakhir sudah mencapai ambang batas lagi.
        // Jumlah yang dipakai sudah "efektif" (otomatis reset ke 0
        // kalau siswa sudah tidak melanggar lagi selama 1 bulan).
        $siswaPerluTindakan = [];

        foreach ($daftarSiswa as &$s) {
            $sid = $s['id'];

            $jumlahPelanggaran = $statusEfektif[$sid]['jumlah_pelanggaran'] ?? 0;
            $jumlahPembinaan   = $statusEfektif[$sid]['jumlah_pembinaan'] ?? 0;

            $belumDitindaklanjuti = $jumlahPelanggaran - ($jumlahPembinaan * self::AMBANG_PELANGGARAN);

            $s['jumlah_pelanggaran'] = $jumlahPelanggaran;
            $s['jumlah_pembinaan']   = $jumlahPembinaan;
            $s['perlu_tindakan']     = $jumlahPembinaan === 1 && $belumDitindaklanjuti >= self::AMBANG_PELANGGARAN;

            if ($s['perlu_tindakan']) {
                $siswaPerluTindakan[] = $s;
            }
        }
        unset($s);

        $riwayatPembinaan = $this->pembinaanSiswa->riwayatPerWaliKelas((int) $guruId);

        $data = [
            'daftarSiswa'        => $daftarSiswa,
            'siswaPerluTindakan' => $siswaPerluTindakan,
            'riwayatPembinaan'   => $riwayatPembinaan,
            'ambangPelanggaran'  => self::AMBANG_PELANGGARAN,
        ];

        return view('wali_kelas_binaan/siswa_binaan', $data);
    }

    /**
     * Form untuk mencatat tindak lanjut pembinaan (tindak lanjut + foto
     * bukti) untuk 1 siswa di kelas yang diwalikan.
     */
    public function formPembinaan($siswaId)
    {
        $guruId   = session()->get('id');
        $kelasIds = $this->kelasWaliDariGuru((int) $guruId);
        $siswa    = $this->pastikanSiswaKelasWali((int) $siswaId, $kelasIds);

        if (!$siswa) {
            return redirect()->to('/wali-kelas-binaan/siswa')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa di kelas yang Anda walikan.');
        }

        $statusEfektifSiswa = $this->statusPembinaanEfektif([$siswaId])[$siswaId] ?? ['jumlah_pelanggaran' => 0, 'jumlah_pembinaan' => 0];
        $jumlahPelanggaran  = $statusEfektifSiswa['jumlah_pelanggaran'];
        $jumlahPembinaan    = $statusEfektifSiswa['jumlah_pembinaan'];

        if ($jumlahPembinaan !== 1) {
            return redirect()->to('/wali-kelas-binaan/siswa')
                ->with('error', 'Siswa ini bukan (lagi) tanggung jawab Wali Kelas untuk ronde pembinaan saat ini.');
        }

        $data = [
            'siswa'             => $siswa,
            'jumlahPelanggaran' => $jumlahPelanggaran,
        ];

        return view('wali_kelas_binaan/form_pembinaan', $data);
    }

    /**
     * Simpan catatan tindak lanjut pembinaan oleh Wali Kelas. Foto
     * bukti wajib dilampirkan, sama seperti pembinaan oleh Guru Wali.
     */
    public function simpanPembinaan()
    {
        $guruId   = session()->get('id');
        $siswaId  = (int) $this->request->getPost('siswa_id');
        $kelasIds = $this->kelasWaliDariGuru((int) $guruId);

        $siswa = $this->pastikanSiswaKelasWali($siswaId, $kelasIds);

        if (!$siswa) {
            return redirect()->to('/wali-kelas-binaan/siswa')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa di kelas yang Anda walikan.');
        }

        $tindakLanjut = trim((string) $this->request->getPost('tindak_lanjut'));
        $tanggal      = $this->request->getPost('tanggal') ?: date('Y-m-d');

        $jumlahPembinaan = $this->statusPembinaanEfektif([$siswaId])[$siswaId]['jumlah_pembinaan'] ?? 0;

        if ($jumlahPembinaan !== 1) {
            return redirect()->to('/wali-kelas-binaan/siswa')
                ->with('error', 'Siswa ini bukan (lagi) tanggung jawab Wali Kelas untuk ronde pembinaan saat ini.');
        }

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
            'siswa_id'      => $siswaId,
            'wali_kelas_id' => $guruId,
            'tingkat'       => 'wali_kelas',
            'tanggal'       => $tanggal,
            'tindak_lanjut' => $tindakLanjut,
            'foto'          => $fotoNama,
        ]);

        return redirect()->to('/wali-kelas-binaan/siswa')
            ->with('success', 'Tindak lanjut pembinaan berhasil dicatat.');
    }
}
