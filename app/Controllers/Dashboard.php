<?php

namespace App\Controllers;

use App\Models\SekolahModel;
use App\Models\WaliAsuhModel;

class Dashboard extends BaseController
{
    protected $sekolah;

    public function __construct()
    {
        $this->sekolah = new SekolahModel();
    }

    /**
     * Ambil daftar guru yang menjabat sebagai "Guru Wali"
     * (sama seperti Kesiswaan::daftarGuruWali()).
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
     * Untuk 1 bulan (Y-m), hitung ada berapa kali tiap hari-dalam-minggu
     * (1=Senin ... 7=Minggu) muncul. Dipakai untuk memproyeksikan jumlah
     * pertemuan yang "seharusnya" terjadi bulan itu berdasarkan jadwal
     * mingguan (logika sama dengan RealisasiMengajar::laporan()).
     */
    private function jumlahHariPerWeekday(string $bulanInput): array
    {
        [$tahun, $bulan] = array_map('intval', explode('-', $bulanInput));
        $jumlahHariBulan = (int) date('t', strtotime($bulanInput . '-01'));

        $hitung = array_fill(1, 7, 0);

        for ($d = 1; $d <= $jumlahHariBulan; $d++) {
            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
            $urutan  = (int) date('N', strtotime($tanggal));
            $hitung[$urutan]++;
        }

        return $hitung;
    }

    /**
     * Ringkasan Kurikulum: total guru mengajar, mapel, kelas, jadwal,
     * serta realisasi mengajar (jadwal vs jurnal terisi) bulan berjalan
     * dan tren 6 bulan terakhir. Dipakai Admin & Wakasek Kurikulum.
     */
    private function ringkasanKurikulum(): array
    {
        $db = \Config\Database::connect();

        $tahunId    = $this->tahunAktif['id'] ?? null;
        $semesterId = $this->semesterAktif['id'] ?? null;

        $mapelModel = new \App\Models\MapelModel();
        $kelasModel = new \App\Models\KelasModel();

        $totalGuruMengajar = 0;
        $totalJadwal       = 0;
        $jadwalPerHari      = array_fill(1, 7, 0); // urutan_hari => jumlah slot jadwal/minggu
        $jadwalPerGuru      = []; // guru_id => ['nama' => .., 'per_hari' => [urutan => jumlah]]

        if ($tahunId && $semesterId) {
            $totalGuruMengajar = (int) ($db->table('jadwal')
                ->select('COUNT(DISTINCT guru_id) as total', false)
                ->where('tahun_pelajaran_id', $tahunId)
                ->where('semester_id', $semesterId)
                ->get()->getRow()->total ?? 0);

            $totalJadwal = $db->table('jadwal')
                ->where('tahun_pelajaran_id', $tahunId)
                ->where('semester_id', $semesterId)
                ->countAllResults();

            $jadwalRows = $db->table('jadwal')
                ->select('jadwal.guru_id, users.nama as nama_guru, hari.urutan as urutan_hari')
                ->join('hari', 'hari.id = jadwal.hari_id')
                ->join('users', 'users.id = jadwal.guru_id')
                ->where('jadwal.tahun_pelajaran_id', $tahunId)
                ->where('jadwal.semester_id', $semesterId)
                ->get()->getResultArray();

            foreach ($jadwalRows as $row) {
                $urutan = (int) $row['urutan_hari'];
                $jadwalPerHari[$urutan]++;

                $guruId = $row['guru_id'];
                if (!isset($jadwalPerGuru[$guruId])) {
                    $jadwalPerGuru[$guruId] = [
                        'nama'     => $row['nama_guru'],
                        'per_hari' => array_fill(1, 7, 0),
                    ];
                }
                $jadwalPerGuru[$guruId]['per_hari'][$urutan]++;
            }
        }

        // Realisasi (jadwal vs jurnal terisi) bulan berjalan
        $bulanIni = date('Y-m');
        [$expectedIni, $realisasiIni] = $this->hitungRealisasiBulan(
            $bulanIni,
            $jadwalPerHari,
            $tahunId,
            $semesterId
        );

        $persentaseIni = $expectedIni > 0 ? round($realisasiIni / $expectedIni * 100, 1) : 0.0;

        // Tren 6 bulan terakhir (pakai distribusi jadwal mingguan yang sama
        // sebagai proyeksi -- cukup akurat selama jadwal tidak berubah drastis)
        $trenRealisasi = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanLabel = date('Y-m', strtotime("-$i months"));
            [$expected, $realisasi] = $this->hitungRealisasiBulan($bulanLabel, $jadwalPerHari, $tahunId, $semesterId);

            $trenRealisasi[] = [
                'bulan'      => $bulanLabel,
                'jadwal'     => $expected,
                'realisasi'  => $realisasi,
                'persentase' => $expected > 0 ? round($realisasi / $expected * 100, 1) : 0.0,
            ];
        }

        // Rekap realisasi per guru bulan berjalan
        $rekapGuru = [];

        if ($tahunId && $semesterId) {
            $awalBulan  = $bulanIni . '-01';
            $akhirBulan = date('Y-m-t', strtotime($awalBulan));

            $realisasiPerGuru = $db->table('jurnal')
                ->select('user_id, COUNT(*) as jumlah')
                ->where('status', 'Masuk')
                ->where('tahun_pelajaran_id', $tahunId)
                ->where('semester_id', $semesterId)
                ->where('tanggal >=', $awalBulan)
                ->where('tanggal <=', $akhirBulan)
                ->groupBy('user_id')
                ->get()->getResultArray();

            $realisasiLookup = [];
            foreach ($realisasiPerGuru as $r) {
                $realisasiLookup[$r['user_id']] = (int) $r['jumlah'];
            }

            $hariBulanIni = $this->jumlahHariPerWeekday($bulanIni);

            foreach ($jadwalPerGuru as $guruId => $info) {
                $expectedGuru = 0;
                foreach ($info['per_hari'] as $urutan => $jumlahSlot) {
                    $expectedGuru += $jumlahSlot * $hariBulanIni[$urutan];
                }

                $realisasiGuru = $realisasiLookup[$guruId] ?? 0;

                $rekapGuru[] = [
                    'nama_guru'  => $info['nama'],
                    'jadwal'     => $expectedGuru,
                    'realisasi'  => $realisasiGuru,
                    'persentase' => $expectedGuru > 0 ? round($realisasiGuru / $expectedGuru * 100, 1) : 0.0,
                ];
            }

            usort($rekapGuru, fn($a, $b) => $a['persentase'] <=> $b['persentase']);
        }

        $guruRendah = array_filter($rekapGuru, fn($r) => $r['persentase'] < 50);

        return [
            'totalGuruMengajar'  => $totalGuruMengajar,
            'totalMapel'         => $mapelModel->countAll(),
            'totalKelas'         => $kelasModel->countAll(),
            'totalJadwal'        => $totalJadwal,
            'realisasiPersen'    => $persentaseIni,
            'realisasiJadwal'    => $expectedIni,
            'realisasiTerisi'    => $realisasiIni,
            'trenRealisasi'      => $trenRealisasi,
            'rekapGuru'          => $rekapGuru,
            'jumlahGuruRendah'   => count($guruRendah),
        ];
    }

    /**
     * Hitung proyeksi jumlah pertemuan terjadwal ("expected") dan jumlah
     * jurnal berstatus Masuk ("realisasi") untuk 1 bulan (format Y-m).
     */
    private function hitungRealisasiBulan(string $bulanInput, array $jadwalPerHari, $tahunId, $semesterId): array
    {
        if (!$tahunId || !$semesterId) {
            return [0, 0];
        }

        $db = \Config\Database::connect();

        $hariBulan = $this->jumlahHariPerWeekday($bulanInput);

        $expected = 0;
        foreach ($jadwalPerHari as $urutan => $jumlahSlot) {
            $expected += $jumlahSlot * $hariBulan[$urutan];
        }

        $awalBulan  = $bulanInput . '-01';
        $akhirBulan = date('Y-m-t', strtotime($awalBulan));

        $realisasi = $db->table('jurnal')
            ->where('status', 'Masuk')
            ->where('tahun_pelajaran_id', $tahunId)
            ->where('semester_id', $semesterId)
            ->where('tanggal >=', $awalBulan)
            ->where('tanggal <=', $akhirBulan)
            ->countAllResults();

        return [$expected, $realisasi];
    }

    public function index()
    {
        $data = [
            'sekolah'      => $this->sekolah->first(),
            'namaGuru'     => session()->get('nama'),
            'fotoGuru'     => session()->get('foto'),
            'jumlahJurnal' => 0,
            'jumlahAbsen'  => 0,
            'jadwalHariIni'=> [],
        ];

        // Ringkasan Kesiswaan, dipindahkan ke dashboard utama untuk
        // Admin & Wakasek Kesiswaan (sebelumnya halaman terpisah /kesiswaan).
        $isWakasekKes = session()->get('role_id') == 1 || $this->hasJabatan('Wakasek Kesiswaan');

        if ($isWakasekKes) {
            $waliAsuh = new WaliAsuhModel();
            $tahunId  = $this->tahunAktif['id'] ?? null;

            $distribusi = $tahunId ? $waliAsuh->getDistribusi($tahunId) : [];
            $rekap      = $tahunId ? $waliAsuh->rekapPerGuruWali($tahunId) : [];

            $totalSiswa  = count($distribusi);
            $sudahDibagi = count(array_filter($distribusi, fn($d) => !empty($d['guru_id'])));

            $db = \Config\Database::connect();

            $trenBulanan = $db->table('pelanggaran_siswa')
                ->select("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as jumlah_kasus")
                ->where('tanggal >=', date('Y-m-d', strtotime('-5 months', strtotime(date('Y-m-01')))))
                ->groupBy('bulan')
                ->orderBy('bulan', 'ASC')
                ->get()
                ->getResultArray();

            $data['isWakasekKes']  = true;
            $data['totalSiswa']    = $totalSiswa;
            $data['sudahDibagi']   = $sudahDibagi;
            $data['belumDibagi']   = $totalSiswa - $sudahDibagi;
            $data['totalGuruWali'] = count($this->daftarGuruWali());
            $data['rekap']         = $rekap;
            $data['trenBulanan']   = $trenBulanan;
        } else {
            $data['isWakasekKes'] = false;
        }

        // Ringkasan Kurikulum, untuk Admin & Wakasek Kurikulum.
        $isWakasekKur = session()->get('role_id') == 1 || $this->hasJabatan('Wakasek Kurikulum');

        if ($isWakasekKur) {
            $data['isWakasekKur'] = true;
            $data += $this->ringkasanKurikulum();
        } else {
            $data['isWakasekKur'] = false;
        }

        return view('dashboard', $data);
    }
}