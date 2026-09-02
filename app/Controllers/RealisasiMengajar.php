<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\JurnalModel;
use App\Models\KelasModel;

class RealisasiMengajar extends BaseController
{
    protected $jadwal;
    protected $jurnal;
    protected $kelas;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya Admin atau Guru yang menjabat Wakasek Kurikulum
        if (session()->get('role_id') != 1 && !$this->hasJabatan('Wakasek Kurikulum')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->jadwal = new JadwalModel();
        $this->jurnal = new JurnalModel();
        $this->kelas  = new KelasModel();
    }

    /**
     * Daftar kelas, untuk memilih kelas mana yang mau dilihat
     * laporan realisasi mengajarnya.
     */
    public function index()
    {
        $data = [
            'daftarKelas' => $this->kelas->orderBy('tingkat', 'ASC')->orderBy('nama_kelas', 'ASC')->findAll(),
        ];

        return view('realisasi_mengajar/index', $data);
    }

    /**
     * Laporan realisasi mengajar 1 kelas untuk 1 bulan,
     * dipecah per minggu (Minggu I - V), format tiap sel:
     * "jumlah pertemuan terisi jurnal (Masuk) / jumlah pertemuan terjadwal".
     */
    public function laporan($kelas_id)
    {
        $kelas = $this->kelas->find($kelas_id);

        if (!$kelas) {
            return redirect()->to('/realisasi-mengajar')->with('error', 'Kelas tidak ditemukan.');
        }

        // Bulan yang dipilih, format Y-m. Default: bulan berjalan.
        $bulanInput = $this->request->getGet('bulan') ?: date('Y-m');
        [$tahun, $bulan] = array_map('intval', explode('-', $bulanInput));

        $jumlahHariBulan = (int) date('t', strtotime($bulanInput . '-01'));

        // Blok minggu ala kalender KBM: I=1-7, II=8-14, III=15-21, IV=22-28, V=29-akhir bulan
        $blokMinggu = [
            'I'   => [1, 7],
            'II'  => [8, 14],
            'III' => [15, 21],
            'IV'  => [22, 28],
            'V'   => [29, $jumlahHariBulan],
        ];

        // 1) Ambil semua (mapel, guru) yang terjadwal untuk kelas ini,
        //    beserta hari apa saja mapel itu diajarkan (bisa lebih dari 1 hari/minggu).
        $jadwalRows = $this->jadwal
            ->select('jadwal.mapel_id, mata_pelajaran.nama_mapel, jadwal.guru_id, users.nama as nama_guru, hari.urutan as urutan_hari')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('users', 'users.id = jadwal.guru_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->where('jadwal.kelas_id', $kelas_id)
            ->where('jadwal.tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('jadwal.semester_id', $this->semesterAktif['id'] ?? null)
            ->findAll();

        // Kelompokkan: key = "mapel_id-guru_id"
        $mapelGuru = [];
        foreach ($jadwalRows as $row) {
            $key = $row['mapel_id'] . '-' . $row['guru_id'];

            if (!isset($mapelGuru[$key])) {
                $mapelGuru[$key] = [
                    'mapel_id'   => $row['mapel_id'],
                    'nama_mapel' => $row['nama_mapel'],
                    'guru_id'    => $row['guru_id'],
                    'nama_guru'  => $row['nama_guru'],
                    'hari'       => [],
                ];
            }

            $mapelGuru[$key]['hari'][(int) $row['urutan_hari']] = true;
        }

        // 2) Ambil semua jurnal (status Masuk) kelas ini selama bulan yang dipilih,
        //    untuk dicocokkan sebagai "realisasi".
        $awalBulan  = sprintf('%04d-%02d-01', $tahun, $bulan);
        $akhirBulan = sprintf('%04d-%02d-%02d', $tahun, $bulan, $jumlahHariBulan);

        $jurnalRows = $this->jurnal
            ->select('user_id, mapel_id, tanggal')
            ->where('kelas_id', $kelas_id)
            ->where('status', 'Masuk')
            ->where('tanggal >=', $awalBulan)
            ->where('tanggal <=', $akhirBulan)
            ->findAll();

        // Lookup cepat: "mapelId-guruId-Y-m-d" => true
        $jurnalLookup = [];
        foreach ($jurnalRows as $j) {
            $jurnalLookup[$j['mapel_id'] . '-' . $j['user_id'] . '-' . $j['tanggal']] = true;
        }

        // 3) Hitung jadwal vs realisasi per blok minggu, untuk tiap mapel+guru
        $laporan = [];

        foreach ($mapelGuru as $item) {

            $baris = [
                'nama_mapel' => $item['nama_mapel'],
                'nama_guru'  => $item['nama_guru'],
                'minggu'     => [],
                'totalRealisasi' => 0,
                'totalJadwal'    => 0,
            ];

            foreach ($blokMinggu as $labelMinggu => [$awal, $akhir]) {

                $jadwalCount    = 0;
                $realisasiCount = 0;

                if ($awal <= $jumlahHariBulan) {

                    $akhirEfektif = min($akhir, $jumlahHariBulan);

                    for ($d = $awal; $d <= $akhirEfektif; $d++) {

                        $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                        $urutanHari = (int) date('N', strtotime($tanggal)); // 1=Senin ... 7=Minggu

                        if (isset($item['hari'][$urutanHari])) {

                            $jadwalCount++;

                            if (isset($jurnalLookup[$item['mapel_id'] . '-' . $item['guru_id'] . '-' . $tanggal])) {
                                $realisasiCount++;
                            }
                        }
                    }
                }

                $baris['minggu'][$labelMinggu] = [
                    'realisasi' => $realisasiCount,
                    'jadwal'    => $jadwalCount,
                ];

                $baris['totalRealisasi'] += $realisasiCount;
                $baris['totalJadwal']    += $jadwalCount;
            }

            $selisih = $baris['totalJadwal'] - $baris['totalRealisasi'];

            $baris['keterangan'] = $selisih > 0
                ? $selisih . ' pertemuan belum diisi jurnal'
                : '';

            $laporan[] = $baris;
        }

        // Urutkan berdasarkan nama mapel
        usort($laporan, fn($a, $b) => strcmp($a['nama_mapel'], $b['nama_mapel']));

        $data = [
            'kelas'       => $kelas,
            'bulanInput'  => $bulanInput,
            'namaBulan'   => strftime_id($tahun, $bulan),
            'blokMinggu'  => array_keys($blokMinggu),
            'laporan'     => $laporan,
        ];

        return view('realisasi_mengajar/laporan', $data);
    }
}

/**
 * Helper lokal: nama bulan dalam Bahasa Indonesia (menggantikan
 * strftime yang sudah deprecated di PHP 8).
 */
if (!function_exists('strftime_id')) {
    function strftime_id($tahun, $bulan)
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($namaBulan[$bulan] ?? '-') . ' ' . $tahun;
    }
}
