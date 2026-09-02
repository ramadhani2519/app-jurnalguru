<?php

namespace App\Controllers;

use App\Models\WaliAsuhModel;
use App\Models\PelanggaranModel;
use App\Models\PembinaanSiswaModel;

class GuruWali extends BaseController
{
    protected $waliAsuh;
    protected $pelanggaran;
    protected $pembinaanSiswa;

    // Ambang batas: berapa kali pelanggaran (yang belum ditindaklanjuti)
    // sampai siswa dianggap perlu tindakan pembinaan.
    private const AMBANG_PELANGGARAN = 2;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya guru yang menjabat sebagai "Guru Wali"
        if (!$this->hasJabatan('Guru Wali')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->waliAsuh       = new WaliAsuhModel();
        $this->pelanggaran    = new PelanggaranModel();
        $this->pembinaanSiswa = new PembinaanSiswaModel();
    }

    /**
     * Pastikan siswa yang diakses benar-benar siswa asuh guru wali
     * yang sedang login. Dipakai di formPembinaan() & simpanPembinaan()
     * supaya guru wali tidak bisa mencatat pembinaan untuk siswa orang lain.
     */
    private function pastikanSiswaAsuh(int $siswaId, int $guruId): ?array
    {
        $tahunId = $this->tahunAktif['id'] ?? null;

        if (!$tahunId) {
            return null;
        }

        $daftar = $this->waliAsuh->getDistribusi($tahunId, null, $guruId);

        foreach ($daftar as $d) {
            if ((int) $d['siswa_id'] === $siswaId) {
                return $d;
            }
        }

        return null;
    }

    /**
     * Daftar siswa asuh milik guru wali yang sedang login, lengkap
     * dengan rekap pelanggaran, status "perlu tindakan pembinaan"
     * (notifikasi), dan riwayat tindak lanjut yang sudah dicatat.
     */
    public function siswaSaya()
    {
        $tahunId = $this->tahunAktif['id'] ?? null;
        $guruId  = session()->get('id');

        $daftarSiswa = $tahunId ? $this->waliAsuh->getDistribusi($tahunId, null, $guruId) : [];
        $daftarSiswa = array_values(array_filter($daftarSiswa, fn($d) => !empty($d['guru_id'])));

        $db = \Config\Database::connect();

        $pelanggaran = [];
        $statusEfektif = [];

        if (!empty($daftarSiswa)) {
            $siswaIds = array_column($daftarSiswa, 'siswa_id');

            $pelanggaran = $db->table('pelanggaran_siswa')
                ->select('pelanggaran_siswa.*, siswa.nama_siswa')
                ->join('siswa', 'siswa.id = pelanggaran_siswa.siswa_id')
                ->whereIn('pelanggaran_siswa.siswa_id', $siswaIds)
                ->orderBy('tanggal', 'DESC')
                ->get()
                ->getResultArray();

            $statusEfektif = $this->statusPembinaanEfektif($siswaIds);
        }

        // Tandai siswa mana yang perlu tindakan pembinaan ronde pertama:
        // jumlah pelanggaran yang belum "ditutup" oleh catatan pembinaan
        // (tiap 1 catatan pembinaan dianggap menuntaskan sejumlah
        // AMBANG_PELANGGARAN pelanggaran) sudah mencapai ambang batas,
        // DAN siswa itu belum pernah dibina sama sekali. Begitu siswa
        // pernah dibina 1x oleh Guru Wali tapi melanggar lagi sebanyak
        // ambang batas, penanganan berikutnya dieskalasi ke Wali Kelas
        // (lihat WaliKelasBinaan), jadi tidak lagi muncul di sini.
        $siswaPerluTindakan = [];
        $siswaDieskalasi    = [];

        foreach ($daftarSiswa as &$s) {
            $sid = $s['siswa_id'];

            $jumlahPelanggaran = $statusEfektif[$sid]['jumlah_pelanggaran'] ?? 0;
            $jumlahPembinaan   = $statusEfektif[$sid]['jumlah_pembinaan'] ?? 0;

            $belumDitindaklanjuti = $jumlahPelanggaran - ($jumlahPembinaan * self::AMBANG_PELANGGARAN);

            $s['jumlah_pelanggaran'] = $jumlahPelanggaran;
            $s['jumlah_pembinaan']   = $jumlahPembinaan;
            $s['direset']            = $statusEfektif[$sid]['direset'] ?? false;
            $s['perlu_tindakan']     = $jumlahPembinaan === 0 && $belumDitindaklanjuti >= self::AMBANG_PELANGGARAN;

            if ($s['perlu_tindakan']) {
                $siswaPerluTindakan[] = $s;
            } elseif ($jumlahPembinaan >= 1 && $belumDitindaklanjuti >= self::AMBANG_PELANGGARAN) {
                // Sudah pernah dibina tapi melanggar lagi sebanyak ambang
                // batas -> sekarang giliran Wali Kelas yang menangani.
                $siswaDieskalasi[] = $s;
            }
        }
        unset($s);

        $riwayatPembinaan = $this->pembinaanSiswa->riwayatPerGuruWali((int) $guruId);

        $data = [
            'daftarSiswa'        => $daftarSiswa,
            'pembinaan'          => $pelanggaran, // riwayat pelanggaran mentah (dipakai tabel lama)
            'siswaPerluTindakan' => $siswaPerluTindakan,
            'siswaDieskalasi'    => $siswaDieskalasi,
            'riwayatPembinaan'   => $riwayatPembinaan,
            'ambangPelanggaran'  => self::AMBANG_PELANGGARAN,
        ];

        return view('guru_wali/siswa_saya', $data);
    }

    /**
     * Form untuk mencatat tindak lanjut pembinaan (tindak lanjut + foto
     * bukti) untuk 1 siswa asuh.
     */
    public function formPembinaan($siswaId)
    {
        $guruId = session()->get('id');
        $siswa  = $this->pastikanSiswaAsuh((int) $siswaId, (int) $guruId);

        if (!$siswa) {
            return redirect()->to('/guru-wali/siswa-saya')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa asuh Anda.');
        }

        $jumlahPelanggaran = $this->pelanggaran
            ->where('siswa_id', $siswaId)
            ->countAllResults();

        $jumlahPembinaan = $this->statusPembinaanEfektif([(int) $siswaId])[(int) $siswaId]['jumlah_pembinaan'] ?? 0;

        if ($jumlahPembinaan >= 1) {
            return redirect()->to('/guru-wali/siswa-saya')
                ->with('error', 'Siswa ini sudah pernah dibina sebelumnya. Penanganan selanjutnya menjadi tanggung jawab Wali Kelas.');
        }

        $data = [
            'siswa'             => $siswa,
            'jumlahPelanggaran' => $jumlahPelanggaran,
        ];

        return view('guru_wali/form_pembinaan', $data);
    }

    /**
     * Simpan catatan tindak lanjut pembinaan. Foto bukti wajib
     * dilampirkan.
     */
    public function simpanPembinaan()
    {
        $guruId  = session()->get('id');
        $siswaId = (int) $this->request->getPost('siswa_id');

        $siswa = $this->pastikanSiswaAsuh($siswaId, (int) $guruId);

        if (!$siswa) {
            return redirect()->to('/guru-wali/siswa-saya')
                ->with('error', 'Siswa tidak ditemukan atau bukan siswa asuh Anda.');
        }

        $tindakLanjut = trim((string) $this->request->getPost('tindak_lanjut'));
        $tanggal      = $this->request->getPost('tanggal') ?: date('Y-m-d');

        $jumlahPembinaan = $this->statusPembinaanEfektif([(int) $siswaId])[(int) $siswaId]['jumlah_pembinaan'] ?? 0;

        if ($jumlahPembinaan >= 1) {
            return redirect()->to('/guru-wali/siswa-saya')
                ->with('error', 'Siswa ini sudah pernah dibina sebelumnya. Penanganan selanjutnya menjadi tanggung jawab Wali Kelas.');
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
            'guru_wali_id'  => $guruId,
            'tingkat'       => 'guru_wali',
            'tanggal'       => $tanggal,
            'tindak_lanjut' => $tindakLanjut,
            'foto'          => $fotoNama,
        ]);

        return redirect()->to('/guru-wali/siswa-saya')
            ->with('success', 'Tindak lanjut pembinaan berhasil dicatat.');
    }
}
