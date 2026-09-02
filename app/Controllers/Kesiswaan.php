<?php

namespace App\Controllers;

use App\Models\WaliAsuhModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\PelanggaranModel;
use App\Models\SekolahModel;

class Kesiswaan extends BaseController
{
    protected $waliAsuh;
    protected $siswa;
    protected $kelas;
    protected $pelanggaran;
    protected $sekolah;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya Admin atau user dengan jabatan tambahan "Wakasek Kesiswaan"
        if (session()->get('role_id') != 1 && !$this->hasJabatan('Wakasek Kesiswaan')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->waliAsuh    = new WaliAsuhModel();
        $this->siswa       = new SiswaModel();
        $this->kelas       = new KelasModel();
        $this->pelanggaran = new PelanggaranModel();
        $this->sekolah     = new SekolahModel();
    }

    /**
     * Ambil daftar guru yang menjabat sebagai "Guru Wali"
     * (jabatan tambahan, lintas kelas, bukan Wali Kelas formal).
     */
    private function daftarGuruWali(): array
    {
        $db = \Config\Database::connect();

        return $db->table('user_jabatan')
            ->select('users.id, users.nama')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->join('users', 'users.id = user_jabatan.user_id')
            ->where('jabatan.nama_jabatan', 'Guru Wali')
            ->orderBy('users.nama', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Dashboard ringkasan: jumlah siswa yang sudah/belum dibagi,
     * jumlah guru wali aktif, serta grafik hasil pembinaan.
     */
    public function index()
    {
        $tahunId = $this->tahunAktif['id'] ?? null;

        $distribusi = $tahunId ? $this->waliAsuh->getDistribusi($tahunId) : [];
        $rekap      = $tahunId ? $this->waliAsuh->rekapPerGuruWali($tahunId) : [];

        $totalSiswa   = count($distribusi);
        $sudahDibagi  = count(array_filter($distribusi, fn($d) => !empty($d['guru_id'])));
        $belumDibagi  = $totalSiswa - $sudahDibagi;

        // Tren jumlah kasus pembinaan per bulan (6 bulan terakhir)
        $db = \Config\Database::connect();

        $trenBulanan = $db->table('pelanggaran_siswa')
            ->select("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as jumlah_kasus")
            ->where('tanggal >=', date('Y-m-d', strtotime('-5 months', strtotime(date('Y-m-01')))))
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'sekolah'      => $this->sekolah->first(),
            'totalSiswa'   => $totalSiswa,
            'sudahDibagi'  => $sudahDibagi,
            'belumDibagi'  => $belumDibagi,
            'totalGuruWali' => count($this->daftarGuruWali()),
            'rekap'        => $rekap,
            'trenBulanan'  => $trenBulanan,
        ];

        return view('kesiswaan/dashboard', $data);
    }

    /**
     * Halaman pembagian siswa ke guru wali. Wakasek Kesiswaan pilih
     * kelas (opsional), centang siswa, lalu pilih 1 guru wali untuk
     * menerima semua siswa yang dicentang.
     */
    public function distribusi()
    {
        $tahunId  = $this->tahunAktif['id'] ?? null;
        $kelasId  = $this->request->getGet('kelas_id');
        $guruId   = $this->request->getGet('guru_id');
        $status   = $this->request->getGet('status'); // 'belum' | '' (semua)

        $daftar = $tahunId ? $this->waliAsuh->getDistribusi($tahunId, $kelasId ?: null, $guruId ?: null) : [];

        if ($status === 'belum') {
            $daftar = array_values(array_filter($daftar, fn($d) => empty($d['guru_id'])));
        }

        $data = [
            'daftarSiswa' => $daftar,
            'kelas'       => $this->kelas->orderBy('nama_kelas', 'ASC')->findAll(),
            'guruWali'    => $this->daftarGuruWali(),
            'kelas_id'    => $kelasId,
            'guru_id'     => $guruId,
            'status'      => $status,
            'tahunId'     => $tahunId,
        ];

        return view('kesiswaan/distribusi', $data);
    }

    /**
     * Simpan pembagian massal: sekumpulan siswa -> 1 guru wali.
     */
    public function simpanDistribusi()
    {
        $tahunId  = $this->tahunAktif['id'] ?? null;
        $siswaIds = $this->request->getPost('siswa_id') ?? [];
        $guruId   = $this->request->getPost('guru_id');

        if (empty($tahunId)) {
            return redirect()->to('/kesiswaan/distribusi')
                ->with('error', 'Tahun pelajaran aktif belum diset. Hubungi admin.');
        }

        if (empty($siswaIds) || empty($guruId)) {
            return redirect()->to('/kesiswaan/distribusi')
                ->with('error', 'Pilih minimal 1 siswa dan 1 guru wali.');
        }

        $this->waliAsuh->bagikan($siswaIds, (int) $guruId, (int) $tahunId);

        return redirect()->to('/kesiswaan/distribusi')
            ->with('success', count($siswaIds) . ' siswa berhasil dibagikan ke guru wali.');
    }

    /**
     * Batalkan pembagian 1 siswa (jadi belum punya guru wali lagi).
     */
    public function hapusDistribusi($id)
    {
        $this->waliAsuh->delete($id);

        return redirect()->to('/kesiswaan/distribusi')
            ->with('success', 'Pembagian siswa berhasil dibatalkan.');
    }

    /**
     * Rekap hasil pembinaan per guru wali (jumlah siswa asuh,
     * jumlah kasus pembinaan).
     */
    public function rekap()
    {
        $tahunId = $this->tahunAktif['id'] ?? null;

        $data = [
            'rekap'   => $tahunId ? $this->waliAsuh->rekapPerGuruWali($tahunId) : [],
            'tahunAktif' => $this->tahunAktif,
        ];

        return view('kesiswaan/rekap', $data);
    }

    /**
     * Terjemahkan jumlah pelanggaran + jumlah pembinaan yang sudah
     * dicatat menjadi status yang mudah dibaca, sudah sampai tahap
     * mana penanganannya (Guru Wali -> Wali Kelas -> Ketua Jurusan).
     * Ambang batasnya sama dengan yang dipakai di halaman Guru Wali /
     * Wali Kelas / Ketua Jurusan (2 pelanggaran baru = perlu tindak
     * lanjut lagi).
     */
    private function tentukanStatusPembinaan(int $jumlahPelanggaran, int $jumlahPembinaan): array
    {
        $ambang = 2;

        if ($jumlahPelanggaran === 0) {
            return ['label' => 'Tidak ada pelanggaran', 'warna' => 'success'];
        }

        $belumDitindaklanjuti = $jumlahPelanggaran - ($jumlahPembinaan * $ambang);

        if ($jumlahPembinaan === 0) {
            if ($belumDitindaklanjuti >= $ambang) {
                return ['label' => 'Menunggu tindakan Guru Wali', 'warna' => 'warning'];
            }
            return ['label' => 'Terpantau', 'warna' => 'secondary'];
        }

        if ($jumlahPembinaan === 1) {
            if ($belumDitindaklanjuti >= $ambang) {
                return ['label' => 'Menunggu tindakan Wali Kelas', 'warna' => 'warning'];
            }
            return ['label' => 'Sudah dibina Guru Wali', 'warna' => 'info'];
        }

        // jumlahPembinaan >= 2 -> sudah sampai/lewat tingkat Wali Kelas
        if ($belumDitindaklanjuti >= $ambang) {
            return ['label' => 'Menunggu tindakan Ketua Jurusan', 'warna' => 'danger'];
        }

        return [
            'label' => $jumlahPembinaan === 2 ? 'Sudah dibina s.d. Wali Kelas' : 'Sudah dibina s.d. Ketua Jurusan',
            'warna' => 'info',
        ];
    }

    /**
     * Monitoring status pembinaan seluruh siswa sekolah (semua
     * jurusan), untuk Wakasek Kesiswaan. Cuma siswa yang punya
     * minimal 1 pelanggaran yang ditampilkan.
     */
    public function statusSiswa()
    {
        $daftarSiswa = $this->siswa
            ->select('siswa.*, kelas.nama_kelas, kelas.jurusan')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->findAll();

        $statusEfektif = [];

        if (!empty($daftarSiswa)) {
            $siswaIds = array_column($daftarSiswa, 'id');

            $statusEfektif = $this->statusPembinaanEfektif($siswaIds);
        }

        $daftarSiswaBermasalah = [];

        foreach ($daftarSiswa as $s) {
            $sid = $s['id'];

            $jumlahPelanggaran = $statusEfektif[$sid]['jumlah_pelanggaran'] ?? 0;
            $jumlahPembinaan   = $statusEfektif[$sid]['jumlah_pembinaan'] ?? 0;

            // Lewati siswa yang belum pernah melanggar sama sekali, atau
            // yang sudah "lunas" (30 hari tanpa pelanggaran baru).
            if ($jumlahPelanggaran === 0) {
                continue;
            }

            $status = $this->tentukanStatusPembinaan($jumlahPelanggaran, $jumlahPembinaan);

            $s['jumlah_pelanggaran']  = $jumlahPelanggaran;
            $s['jumlah_pembinaan']    = $jumlahPembinaan;
            $s['sudah_guru_wali']     = $jumlahPembinaan >= 1;
            $s['sudah_wali_kelas']    = $jumlahPembinaan >= 2;
            $s['sudah_ketua_jurusan'] = $jumlahPembinaan >= 3;
            $s['status_label']        = $status['label'];
            $s['status_warna']        = $status['warna'];

            $daftarSiswaBermasalah[] = $s;
        }

        // Riwayat pembinaan terbaru dari semua tingkat & semua jurusan,
        // untuk panel monitoring di sisi Wakasek Kesiswaan.
        $db = \Config\Database::connect();

        $riwayatPembinaan = $db->table('pembinaan_siswa')
            ->select('
                pembinaan_siswa.tanggal,
                pembinaan_siswa.tingkat,
                pembinaan_siswa.tindak_lanjut,
                siswa.nama_siswa,
                gw.nama as nama_guru_wali,
                wk.nama as nama_wali_kelas,
                kj.nama as nama_ketua_jurusan
            ')
            ->join('siswa', 'siswa.id = pembinaan_siswa.siswa_id', 'left')
            ->join('users gw', 'gw.id = pembinaan_siswa.guru_wali_id', 'left')
            ->join('users wk', 'wk.id = pembinaan_siswa.wali_kelas_id', 'left')
            ->join('users kj', 'kj.id = pembinaan_siswa.ketua_jurusan_id', 'left')
            ->orderBy('pembinaan_siswa.tanggal', 'DESC')
            ->orderBy('pembinaan_siswa.id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        foreach ($riwayatPembinaan as &$r) {
            switch ($r['tingkat']) {
                case 'wali_kelas':
                    $r['tingkat_label'] = 'Wali Kelas';
                    $r['tingkat_warna'] = 'warning';
                    $r['nama_penindak'] = $r['nama_wali_kelas'] ?? '-';
                    break;
                case 'ketua_jurusan':
                    $r['tingkat_label'] = 'Ketua Jurusan';
                    $r['tingkat_warna'] = 'danger';
                    $r['nama_penindak'] = $r['nama_ketua_jurusan'] ?? '-';
                    break;
                default:
                    $r['tingkat_label'] = 'Guru Wali';
                    $r['tingkat_warna'] = 'info';
                    $r['nama_penindak'] = $r['nama_guru_wali'] ?? '-';
            }
        }
        unset($r);

        $data = [
            'daftarSiswa'      => $daftarSiswaBermasalah,
            'riwayatPembinaan' => $riwayatPembinaan,
        ];

        return view('kesiswaan/status_siswa', $data);
    }

    /**
     * Detail riwayat penanganan (tindak lanjut) 1 siswa: daftar semua
     * catatan pembinaan dari Guru Wali, Wali Kelas, sampai Ketua
     * Jurusan (kalau sudah sampai situ), lengkap tanggal, uraian,
     * siapa yang menangani, dan foto buktinya.
     */
    public function detailPembinaan($siswaId)
    {
        $siswa = $this->siswa
            ->select('siswa.*, kelas.nama_kelas, kelas.jurusan')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->find($siswaId);

        if (!$siswa) {
            return redirect()->to('/kesiswaan/status-siswa')
                ->with('error', 'Siswa tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $riwayat = $db->table('pembinaan_siswa')
            ->select('
                pembinaan_siswa.*,
                gw.nama as nama_guru_wali,
                wk.nama as nama_wali_kelas,
                kj.nama as nama_ketua_jurusan
            ')
            ->join('users gw', 'gw.id = pembinaan_siswa.guru_wali_id', 'left')
            ->join('users wk', 'wk.id = pembinaan_siswa.wali_kelas_id', 'left')
            ->join('users kj', 'kj.id = pembinaan_siswa.ketua_jurusan_id', 'left')
            ->where('pembinaan_siswa.siswa_id', $siswaId)
            ->orderBy('pembinaan_siswa.tanggal', 'ASC')
            ->orderBy('pembinaan_siswa.id', 'ASC')
            ->get()
            ->getResultArray();

        $statusEfektifSiswa = $this->statusPembinaanEfektif([(int) $siswaId])[(int) $siswaId]
            ?? ['jumlah_pelanggaran' => 0, 'jumlah_pembinaan' => 0, 'direset' => false];

        $jumlahPelanggaranTotal = $this->pelanggaran
            ->where('siswa_id', $siswaId)
            ->countAllResults();

        $status = $this->tentukanStatusPembinaan(
            $statusEfektifSiswa['jumlah_pelanggaran'],
            $statusEfektifSiswa['jumlah_pembinaan']
        );

        $data = [
            'siswa'             => $siswa,
            'riwayat'           => $riwayat,
            'jumlahPelanggaran' => $jumlahPelanggaranTotal,
            'direset'           => $statusEfektifSiswa['direset'],
            'statusLabel'       => $status['label'],
            'statusWarna'       => $status['warna'],
        ];

        return view('kesiswaan/detail_pembinaan', $data);
    }

    /**
     * Cetak PDF rekap hasil pembinaan per guru wali.
     */
    public function cetakRekap()
    {
        $tahunId = $this->tahunAktif['id'] ?? null;

        $sekolah = $this->sekolah->first();

        $logoBase64         = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'rekap'              => $tahunId ? $this->waliAsuh->rekapPerGuruWali($tahunId) : [],
            'tahunAktif'         => $this->tahunAktif,
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,
        ];

        $html = view('kesiswaan/rekap_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');

        $canvas->page_text(500, 815, "Halaman {PAGE_NUM} / {PAGE_COUNT}", $font, 9, [0, 0, 0]);

        while (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf->stream('Rekap-Pembinaan-Per-Guru-Wali.pdf', ['Attachment' => false]);
        exit;
    }
}
