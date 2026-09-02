<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;
use App\Models\MapelModel;
use App\Models\SekolahModel;
use App\Models\PelanggaranModel;


class Absensi extends BaseController
{
    protected $siswa;
    protected $kelas;
    protected $absensi;
    protected $mapel;
    protected $sekolah;
    protected $pelanggaran;

    // Label status yang dipakai di banyak tempat (form, cetak, rekap)
    private $labelStatus = [
        'H' => 'Hadir',
        'S' => 'Sakit',
        'I' => 'Izin Keluarga',
        'P' => 'Pulang Cepat (Sakit)',
        'B' => 'Bolos / Hilang',
    ];

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Petugas Absen Sholat (role_id 5) TIDAK boleh akses Absensi Harian
        if (session()->get('role_id') == 5) {
            redirect()->to('/absensi-sholat')->send();
            exit;
        }

        $this->siswa      = new SiswaModel();
        $this->kelas      = new KelasModel();
        $this->absensi    = new AbsensiModel();
        $this->mapel      = new MapelModel();
        $this->sekolah    = new SekolahModel();
        $this->pelanggaran = new PelanggaranModel();
    }

    /**
     * Ambil daftar kelas_id di mana user tsb tercatat sebagai
     * Wali Kelas, berdasarkan data jabatan (tabel user_jabatan +
     * jabatan), BUKAN dari tabel wali_kelas lama (yang cuma nama
     * bebas tanpa keterkaitan ke akun user).
     */
    private function kelasWaliDariGuru(int $user_id): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('user_jabatan')
            ->select('user_jabatan.kelas_id')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->where('user_jabatan.user_id', $user_id)
            ->where('jabatan.nama_jabatan', 'Wali Kelas')
            ->get()
            ->getResultArray();

        return array_values(array_filter(array_column($rows, 'kelas_id')));
    }

    /**
     * Untuk role Petugas Absen (Siswa / role_id 4),
     * kelas_id SELALU diambil dari session, tidak pernah dari input
     * user (GET/POST), supaya tidak bisa diakali untuk mengabsen
     * kelas lain.
     *
     * Untuk role Guru (role_id 2), kelas_id HANYA boleh salah satu
     * dari kelas yang dia jabat sebagai Wali Kelas (dari data
     * user_jabatan). Kalau dia bukan wali kelas mana pun, atau
     * memilih kelas yang bukan miliknya, dikunci ke null / kelas
     * walinya sendiri.
     */
    private function kelasTerkunci($kelas_id)
    {
        if (session()->get('role_id') == 4) {
            return session()->get('kelas_id');
        }

        if (session()->get('role_id') == 2) {
            $kelasWali = $this->kelasWaliDariGuru((int) session()->get('id'));

            if (empty($kelasWali)) {
                return null;
            }

            return in_array($kelas_id, $kelasWali) ? $kelas_id : $kelasWali[0];
        }

        return $kelas_id;
    }

    /**
     * Untuk role Petugas Absen (Siswa / role_id 4), tanggal SELALU
     * dikunci ke hari ini (server), tidak pernah dari input user
     * (GET/POST), supaya tidak bisa diakali untuk mengabsen tanggal
     * yang sudah lewat atau tanggal yang akan datang.
     */
    private function tanggalTerkunci($tanggal)
    {
        if (session()->get('role_id') == 4) {
            return date('Y-m-d');
        }

        return $tanggal;
    }

    /**
     * Kalau status berubah JADI Bolos (dari status lain, atau baru),
     * otomatis buat 1 catatan baru di Pembinaan Siswa.
     * Tidak akan dobel kalau sebelumnya memang sudah Bolos.
     */
    private function cekOtomatisPembinaan($statusLama, $statusBaru, $tanggal, $kelas_id, $siswa_id, $jam_sejak)
    {
        if ($statusBaru !== 'B') {
            return;
        }

        $uraian = 'Bolos / tidak berada di sekolah';

        if (!empty($jam_sejak)) {
            $uraian .= ' sejak Jam ke-' . $jam_sejak;
        }

        // Cek dulu berdasarkan siswa + tanggal (bukan berdasarkan transisi
        // status), supaya kalau status di-toggle bolak-balik / dropdown
        // "Sejak Jam" diubah-ubah, TIDAK bikin catatan dobel di hari yang
        // sama. Kalau catatan otomatis untuk hari itu sudah ada, cukup
        // update uraiannya saja (misal jam_sejak berubah).
        $existing = $this->pelanggaran
            ->where('siswa_id', $siswa_id)
            ->where('tanggal', $tanggal)
            ->where('keterangan', 'Otomatis dibuat dari Absensi Siswa (Bolos)')
            ->first();

        if ($existing) {
            $this->pelanggaran->update($existing['id'], [
                'uraian_pelanggaran' => $uraian,
            ]);
            return;
        }

        $this->pelanggaran->insert([
            'tanggal'            => $tanggal,
            'kelas_id'           => $kelas_id,
            'siswa_id'           => $siswa_id,
            'uraian_pelanggaran' => $uraian,
            'keterangan'         => 'Otomatis dibuat dari Absensi Siswa (Bolos)',
            'user_id'            => session()->get('id'),
        ]);
    }

    public function index()
    {
        $kelas_id = $this->kelasTerkunci($this->request->getGet('kelas_id'));
        $tanggal  = $this->tanggalTerkunci($this->request->getGet('tanggal') ?? date('Y-m-d'));

        // Guru (role_id 2) cuma boleh lihat kelas yang dia jabat
        // sebagai Wali Kelas (dari data user_jabatan), bukan semua kelas.
        $daftarKelas = $this->kelas->findAll();
        $bukanWaliKelasManapun = false;

        if (session()->get('role_id') == 2) {
            $kelasWali = $this->kelasWaliDariGuru((int) session()->get('id'));
            $daftarKelas = array_values(array_filter(
                $daftarKelas,
                fn($k) => in_array($k['id'], $kelasWali)
            ));
            $bukanWaliKelasManapun = empty($kelasWali);
        }

        $data = [
            'tanggal'       => $tanggal,
            'kelas'         => $daftarKelas,
            'kelas_id'      => $kelas_id,
            'tahunAktif'    => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif,
            'siswa'         => [],
            'absensi'       => [],
            'labelStatus'   => $this->labelStatus,
            'bukanWaliKelasManapun' => $bukanWaliKelasManapun,
        ];

        if ($kelas_id) {

            $data['siswa'] = $this->siswa
                ->where('kelas_id', $kelas_id)
                ->orderBy('nama_siswa', 'ASC')
                ->findAll();

            $hasil = $this->absensi
                ->where('tanggal', $tanggal)
                ->where('kelas_id', $kelas_id)
                ->findAll();

            foreach ($hasil as $h) {
                $data['absensi'][$h['siswa_id']] = [
                    'status'    => $h['status'],
                    'jam_sejak' => $h['jam_sejak'],
                ];
            }
        }

        return view('absensi/index', $data);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $kelas_id = $this->kelasTerkunci($this->request->getPost('kelas_id'));
        $siswa_id = $this->request->getPost('siswa_id');
        $tanggal  = $this->tanggalTerkunci($this->request->getPost('tanggal'));
        $status   = $this->request->getPost('status');
        $jamSejak = $this->request->getPost('jam_sejak') ?: null;

        // Cuma status Pulang Cepat / Bolos yang relevan pakai jam_sejak
        if (!in_array($status, ['P', 'B'])) {
            $jamSejak = null;
        }

        $data = [
            'tanggal'            => $tanggal,
            'tahun_pelajaran_id' => $this->request->getPost('tahun_pelajaran_id'),
            'semester_id'        => $this->request->getPost('semester_id'),
            'kelas_id'           => $kelas_id,
            'guru_id'            => session()->get('id'),
            'siswa_id'           => $siswa_id,
            'status'             => $status,
            'jam_sejak'          => $jamSejak,
        ];

        if (session()->get('role_id') == 4) {
            $cekSiswa = $this->siswa
                ->where('id', $siswa_id)
                ->where('kelas_id', $kelas_id)
                ->first();

            if (!$cekSiswa) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Siswa tidak berada di kelas Anda.'
                ]);
            }
        }

        $cek = $this->absensi
            ->where('tanggal', $tanggal)
            ->where('kelas_id', $kelas_id)
            ->where('siswa_id', $siswa_id)
            ->first();

        $statusLama = $cek['status'] ?? null;

        if ($cek) {
            $this->absensi->update($cek['id'], $data);
        } else {
            $this->absensi->insert($data);
        }

        $this->cekOtomatisPembinaan($statusLama, $status, $tanggal, $kelas_id, $siswa_id, $jamSejak);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi berhasil disimpan.'
        ]);
    }

    public function simpanMassal()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $kelas_id = $this->kelasTerkunci($this->request->getPost('kelas_id'));
        $tanggal  = $this->tanggalTerkunci($this->request->getPost('tanggal'));

        if (session()->get('role_id') == 4) {
            $idKelasValid = array_column(
                $this->siswa->where('kelas_id', $kelas_id)->findAll(),
                'id'
            );

            foreach ($this->request->getPost('absensi') as $row) {
                if (!in_array($row['siswa_id'], $idKelasValid)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Ada siswa di luar kelas Anda.'
                    ]);
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($this->request->getPost('absensi') as $row) {

            $status = $row['status'];

            $save = [
                'tanggal'            => $tanggal,
                'tahun_pelajaran_id' => $this->request->getPost('tahun_pelajaran_id'),
                'semester_id'        => $this->request->getPost('semester_id'),
                'kelas_id'           => $kelas_id,
                'guru_id'            => session()->get('id'),
                'siswa_id'           => $row['siswa_id'],
                'status'             => $status,
                'jam_sejak'          => null,
            ];

            $cek = $this->absensi
                ->where('tanggal', $tanggal)
                ->where('kelas_id', $kelas_id)
                ->where('siswa_id', $save['siswa_id'])
                ->first();

            $statusLama = $cek['status'] ?? null;

            if ($cek) {
                $this->absensi->update($cek['id'], $save);
            } else {
                $this->absensi->insert($save);
            }

            $this->cekOtomatisPembinaan($statusLama, $status, $tanggal, $kelas_id, $save['siswa_id'], null);
        }

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi massal berhasil.'
        ]);
    }

    public function laporan()
    {
        $kelas_id = $this->request->getGet('kelas_id');
        $tanggal  = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $data = [
            'tanggal'       => $tanggal,
            'kelas'         => $this->kelas->findAll(),
            'kelas_id'      => $kelas_id,
            'tahunAktif'    => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif,
        ];

        return view('absensi/laporan', $data);
    }

    public function cetak()
    {
        // Petugas Absen (Siswa) tidak boleh cetak
        if (session()->get('role_id') == 4) {
            return redirect()->to('/absensi');
        }

        $db = \Config\Database::connect();

        $tanggal  = $this->request->getGet('tanggal');
        $kelas_id = $this->request->getGet('kelas_id');

        // Guru (role_id 2) tidak boleh cetak kelas yang bukan kelas
        // wali-nya, meski coba lewat parameter URL langsung.
        if (session()->get('role_id') == 2) {
            $kelasWali = $this->kelasWaliDariGuru((int) session()->get('id'));

            if (!in_array($kelas_id, $kelasWali)) {
                return redirect()->to('/absensi')->with('error', 'Anda bukan wali kelas untuk kelas tersebut.');
            }
        }

        $absensi = $db->table('absensi')
            ->select('
                absensi.*,
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas,
                users.nama as nama_guru
            ')
            ->join('siswa', 'siswa.id=absensi.siswa_id')
            ->join('kelas', 'kelas.id=absensi.kelas_id')
            ->join('users', 'users.id=absensi.guru_id')
            ->where('absensi.tanggal', $tanggal)
            ->where('absensi.kelas_id', $kelas_id)
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($absensi)) {
            return redirect()->back()->with('error', 'Data absensi tidak ditemukan.');
        }

        $sekolah = $this->sekolah->first();

        $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,
            'absensi'     => $absensi,
            'tanggal'     => $tanggal,
            'kelas'       => $absensi[0]['nama_kelas'],
            'labelStatus' => $this->labelStatus,
            'tahunAktif'  => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif
        ];

        $html = view('absensi/cetak', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            'Absensi-' . $tanggal . '.pdf',
            ['Attachment' => false]
        );

        exit;
    }

    public function cetakRekap()
    {
        // Petugas Absen (Siswa) tidak boleh cetak
        if (session()->get('role_id') == 4) {
            return redirect()->to('/absensi');
        }

        $db = \Config\Database::connect();

        $tanggalAwal  = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $kelas_id     = $this->request->getGet('kelas_id');

        if (empty($tanggalAwal) || empty($tanggalAkhir) || empty($kelas_id)) {
            return redirect()->back()->with('error', 'Lengkapi semua filter terlebih dahulu.');
        }

        if ($tanggalAwal > $tanggalAkhir) {
            return redirect()->back()->with('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
        }

        $absensi = $db->table('absensi')
            ->select("
                siswa.nis,
                siswa.nama_siswa,
                kelas.nama_kelas,

                SUM(CASE WHEN absensi.status='H' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN absensi.status='S' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN absensi.status='I' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN absensi.status='P' THEN 1 ELSE 0 END) as pulang_cepat,
                SUM(CASE WHEN absensi.status='B' THEN 1 ELSE 0 END) as bolos,

                COUNT(absensi.id) as total
            ")
            ->join('siswa', 'siswa.id=absensi.siswa_id')
            ->join('kelas', 'kelas.id=absensi.kelas_id')
            ->where('absensi.kelas_id', $kelas_id)
            ->where('absensi.tanggal >=', $tanggalAwal)
            ->where('absensi.tanggal <=', $tanggalAkhir)
            ->groupBy('absensi.siswa_id')
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($absensi)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $sekolah = $this->sekolah->first();

        $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,
            'absensi'      => $absensi,
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir'=> $tanggalAkhir,
            'kelas'        => $absensi[0]['nama_kelas'],
            'tahunAktif'   => $this->tahunAktif,
            'semesterAktif'=> $this->semesterAktif
        ];

        $html = view('absensi/cetak_rekap', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream(
            'Rekap-Absensi-' . $tanggalAwal . '-' . $tanggalAkhir . '.pdf',
            ['Attachment' => false]
        );

        exit;
    }

    /*
    =====================================
    REKAP BULANAN PER KELAS (Excel)
    =====================================
    */

    public function rekapBulanan()
    {
        if (session()->get('role_id') == 4) {
            return redirect()->to('/absensi');
        }

        $db = \Config\Database::connect();

        $waliKelas = $db->table('user_jabatan')
            ->select('users.nama as nama_wali, kelas.nama_kelas')
            ->join('users', 'users.id = user_jabatan.user_id')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->join('kelas', 'kelas.id = user_jabatan.kelas_id', 'left')
            ->where('jabatan.nama_jabatan', 'Wali Kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $ketuaJurusan = $db->table('user_jabatan')
            ->select('users.nama as nama_ketua')
            ->join('users', 'users.id = user_jabatan.user_id')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->where('jabatan.nama_jabatan', 'Ketua Jurusan')
            ->orderBy('users.nama', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'kelas'           => $this->kelas->findAll(),
            'waliKelas'       => $waliKelas,
            'ketuaKompetensi' => $ketuaJurusan,
        ];

        return view('absensi/rekap_bulanan', $data);
    }

    public function exportRekapBulanan()
    {
        if (session()->get('role_id') == 4) {
            return redirect()->to('/absensi');
        }

        $kelas_id  = $this->request->getGet('kelas_id');
        $periode   = $this->request->getGet('periode'); // format: YYYY-MM
        $waliKelas = $this->request->getGet('wali_kelas');
        $ketuaKompetensi = $this->request->getGet('ketua_kompetensi');

        if (empty($kelas_id) || empty($periode)) {
            return redirect()->back()->with('error', 'Lengkapi kelas dan periode bulan terlebih dahulu.');
        }

        $tanggalAwal  = $periode . '-01';
        $tanggalAkhir = date('Y-m-t', strtotime($tanggalAwal));

        $kelasInfo = $this->kelas->find($kelas_id);

        if (!$kelasInfo) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        $daftarSiswa = $this->siswa
            ->where('kelas_id', $kelas_id)
            ->orderBy('nama_siswa', 'ASC')
            ->findAll();

        if (empty($daftarSiswa)) {
            return redirect()->back()->with('error', 'Belum ada data siswa di kelas ini.');
        }

        $db = \Config\Database::connect();

        // Hari aktif = jumlah tanggal unik yang ada absensi untuk kelas ini dalam periode
        $hariAktifRows = $db->table('absensi')
            ->select('tanggal')
            ->where('kelas_id', $kelas_id)
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->groupBy('tanggal')
            ->get()
            ->getResultArray();

        $hariAktif = count($hariAktifRows);

        $totalHariBulan = (int) date('t', strtotime($tanggalAwal));
        $totalMinggu = (int) ceil($totalHariBulan / 7);

        $semuaAbsensi = $db->table('absensi')
            ->select('siswa_id, tanggal, status')
            ->where('kelas_id', $kelas_id)
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->get()
            ->getResultArray();

        // rekap[siswa_id][minggu_ke][status] = set tanggal unik
        $rekap = [];

        foreach ($semuaAbsensi as $row) {

            if ($row['status'] == 'H') {
                continue; // hadir dihitung otomatis dari sisa
            }

            $tgl = (int) date('j', strtotime($row['tanggal']));
            $mingguKe = (int) ceil($tgl / 7);

            $sid = $row['siswa_id'];
            $status = $row['status'];

            if (!isset($rekap[$sid][$mingguKe][$status])) {
                $rekap[$sid][$mingguKe][$status] = [];
            }

            $rekap[$sid][$mingguKe][$status][$row['tanggal']] = true;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Bulanan');

        $warnaMinggu = ['FCE4E4', 'E2EFDA', 'DDEBF7', 'E4DFEC', 'FFF2CC'];

        $totalKolom = 2 + ($totalMinggu * 4) + 5 + 5;
        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'B. Rekap Absen Siswa');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $kolomHariAktifLabel = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom + 1);
        $sheet->setCellValue($kolomHariAktifLabel . '1', 'hari aktif');
        $kolomHariAktifNilai = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom + 2);
        $sheet->setCellValue($kolomHariAktifNilai . '1', $hariAktif);

        $baris1 = 2;
        $baris2 = 3;

        $sheet->mergeCells("A{$baris1}:A{$baris2}");
        $sheet->setCellValue("A{$baris1}", 'No');

        $sheet->mergeCells("B{$baris1}:B{$baris2}");
        $sheet->setCellValue("B{$baris1}", 'Nama Siswa');

        $kolom = 3;

        for ($m = 1; $m <= $totalMinggu; $m++) {

            $colStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
            $colEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3);

            $sheet->mergeCells("{$colStart}{$baris1}:{$colEnd}{$baris1}");
            $sheet->setCellValue("{$colStart}{$baris1}", "Minggu Ke-{$m}");

            $sheet->getStyle("{$colStart}{$baris1}:{$colEnd}{$baris2}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($warnaMinggu[($m - 1) % count($warnaMinggu)]);

            $sheet->setCellValue($colStart . $baris2, 'Sakit');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1) . $baris2, 'Izin');
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2) . $baris2, 'Plng Cepat');
            $sheet->setCellValue($colEnd . $baris2, 'Bolos');

            $kolom += 4;
        }

        $colTotalStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
        $colTotalEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 4);
        $sheet->mergeCells("{$colTotalStart}{$baris1}:{$colTotalEnd}{$baris1}");
        $sheet->setCellValue("{$colTotalStart}{$baris1}", 'Total');

        $sheet->setCellValue($colTotalStart . $baris2, 'Sakit');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1) . $baris2, 'Izin');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2) . $baris2, 'Plng Cepat');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3) . $baris2, 'Bolos');
        $sheet->setCellValue($colTotalEnd . $baris2, 'total hadir');

        $kolom += 5;

        $colPersenStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
        $colPersenEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 4);
        $sheet->mergeCells("{$colPersenStart}{$baris1}:{$colPersenEnd}{$baris1}");
        $sheet->setCellValue("{$colPersenStart}{$baris1}", 'persentasi');

        $sheet->setCellValue($colPersenStart . $baris2, 's');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1) . $baris2, 'i');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2) . $baris2, 'pc');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3) . $baris2, 'bolos');
        $sheet->setCellValue($colPersenEnd . $baris2, 'kehadiran %');

        $sheet->getStyle("A{$baris1}:{$colPersenEnd}{$baris2}")->getFont()->setBold(true);
        $sheet->getStyle("A{$baris1}:{$colPersenEnd}{$baris2}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $baris = $baris2 + 1;
        $no = 1;

        $totalSakitSemua = 0;
        $totalIzinSemua  = 0;
        $totalPcSemua    = 0;
        $totalBolosSemua = 0;
        $totalHadirSemua = 0;
        $jumlahSiswa     = count($daftarSiswa);

        foreach ($daftarSiswa as $s) {

            $sid = $s['id'];

            $sheet->setCellValue("A{$baris}", $no++);
            $sheet->setCellValue("B{$baris}", $s['nama_siswa']);

            $kolom = 3;
            $totalSakit = 0;
            $totalIzin  = 0;
            $totalPc    = 0;
            $totalBolos = 0;

            for ($m = 1; $m <= $totalMinggu; $m++) {

                $sakitMinggu = isset($rekap[$sid][$m]['S']) ? count($rekap[$sid][$m]['S']) : 0;
                $izinMinggu  = isset($rekap[$sid][$m]['I']) ? count($rekap[$sid][$m]['I']) : 0;
                $pcMinggu    = isset($rekap[$sid][$m]['P']) ? count($rekap[$sid][$m]['P']) : 0;
                $bolosMinggu = isset($rekap[$sid][$m]['B']) ? count($rekap[$sid][$m]['B']) : 0;

                $colS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
                $colI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);
                $colP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2);
                $colB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3);

                if ($sakitMinggu > 0) $sheet->setCellValue($colS . $baris, $sakitMinggu);
                if ($izinMinggu > 0)  $sheet->setCellValue($colI . $baris, $izinMinggu);
                if ($pcMinggu > 0)    $sheet->setCellValue($colP . $baris, $pcMinggu);
                if ($bolosMinggu > 0) $sheet->setCellValue($colB . $baris, $bolosMinggu);

                $totalSakit += $sakitMinggu;
                $totalIzin  += $izinMinggu;
                $totalPc    += $pcMinggu;
                $totalBolos += $bolosMinggu;

                $kolom += 4;
            }

            // Pulang Cepat masih dianggap hadir sebagian hari itu, jadi tidak
            // mengurangi total hadir. Yang mengurangi hanya Sakit/Izin/Bolos.
            $totalHadir = $hariAktif - $totalSakit - $totalIzin - $totalBolos;
            if ($totalHadir < 0) $totalHadir = 0;

            $colTS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
            $colTI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);
            $colTP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2);
            $colTB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3);
            $colTH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 4);

            $sheet->setCellValue($colTS . $baris, $totalSakit);
            $sheet->setCellValue($colTI . $baris, $totalIzin);
            $sheet->setCellValue($colTP . $baris, $totalPc);
            $sheet->setCellValue($colTB . $baris, $totalBolos);
            $sheet->setCellValue($colTH . $baris, $totalHadir);

            $kolom += 5;

            $persenS = $hariAktif > 0 ? round(($totalSakit / $hariAktif) * 100) : 0;
            $persenI = $hariAktif > 0 ? round(($totalIzin / $hariAktif) * 100) : 0;
            $persenP = $hariAktif > 0 ? round(($totalPc / $hariAktif) * 100) : 0;
            $persenB = $hariAktif > 0 ? round(($totalBolos / $hariAktif) * 100) : 0;
            $persenH = $hariAktif > 0 ? round(($totalHadir / $hariAktif) * 100) : 0;

            $colPS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
            $colPI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);
            $colPP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2);
            $colPB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 3);
            $colPH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 4);

            $sheet->setCellValue($colPS . $baris, $persenS . '%');
            $sheet->setCellValue($colPI . $baris, $persenI . '%');
            $sheet->setCellValue($colPP . $baris, $persenP . '%');
            $sheet->setCellValue($colPB . $baris, $persenB . '%');
            $sheet->setCellValue($colPH . $baris, $persenH . '%');

            $totalSakitSemua += $totalSakit;
            $totalIzinSemua  += $totalIzin;
            $totalPcSemua    += $totalPc;
            $totalBolosSemua += $totalBolos;
            $totalHadirSemua += $totalHadir;

            $baris++;
        }

        $rataSakit = $jumlahSiswa > 0 ? round($totalSakitSemua / $jumlahSiswa) : 0;
        $rataIzin  = $jumlahSiswa > 0 ? round($totalIzinSemua / $jumlahSiswa) : 0;
        $rataPc    = $jumlahSiswa > 0 ? round($totalPcSemua / $jumlahSiswa) : 0;
        $rataBolos = $jumlahSiswa > 0 ? round($totalBolosSemua / $jumlahSiswa) : 0;
        $rataHadir = $jumlahSiswa > 0 ? round($totalHadirSemua / $jumlahSiswa) : 0;

        $kolomTotalAwal = 3 + ($totalMinggu * 4);
        $colTS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTotalAwal);
        $colTI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTotalAwal + 1);
        $colTP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTotalAwal + 2);
        $colTB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTotalAwal + 3);
        $colTH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTotalAwal + 4);

        $sheet->setCellValue($colTS . $baris, $rataSakit);
        $sheet->setCellValue($colTI . $baris, $rataIzin);
        $sheet->setCellValue($colTP . $baris, $rataPc);
        $sheet->setCellValue($colTB . $baris, $rataBolos);
        $sheet->setCellValue($colTH . $baris, $rataHadir);

        $kolomPersenAwal = $kolomTotalAwal + 5;
        $colPS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomPersenAwal);
        $colPI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomPersenAwal + 1);
        $colPP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomPersenAwal + 2);
        $colPB = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomPersenAwal + 3);
        $colPH = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomPersenAwal + 4);

        $sheet->setCellValue($colPS . $baris, ($hariAktif > 0 ? round(($rataSakit / $hariAktif) * 100) : 0) . '%');
        $sheet->setCellValue($colPI . $baris, ($hariAktif > 0 ? round(($rataIzin / $hariAktif) * 100) : 0) . '%');
        $sheet->setCellValue($colPP . $baris, ($hariAktif > 0 ? round(($rataPc / $hariAktif) * 100) : 0) . '%');
        $sheet->setCellValue($colPB . $baris, ($hariAktif > 0 ? round(($rataBolos / $hariAktif) * 100) : 0) . '%');
        $sheet->setCellValue($colPH . $baris, ($hariAktif > 0 ? round(($rataHadir / $hariAktif) * 100) : 0) . '%');

        $baris += 3;

        $sheet->setCellValue("A{$baris}", 'Mengetahui');
        $tempatTgl = ($kelasInfo['nama_kelas'] ?? '') . ', ' . date('d F Y');
        $sheet->setCellValue($colTS . $baris, $tempatTgl);

        $baris++;
        $sheet->setCellValue("A{$baris}", 'Ketua Kompetensi Keahlian');
        $sheet->setCellValue($colTS . $baris, 'Wali Kelas ' . ($kelasInfo['nama_kelas'] ?? ''));

        $baris += 4;
        $sheet->setCellValue("A" . ($baris), $ketuaKompetensi ?: '.............................');
        $sheet->setCellValue($colTS . $baris, $waliKelas ?: '.............................');

        foreach (range('A', $colPH) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaFile = 'Rekap-Bulanan-' . ($kelasInfo['nama_kelas'] ?? '') . '-' . $periode . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }
}
