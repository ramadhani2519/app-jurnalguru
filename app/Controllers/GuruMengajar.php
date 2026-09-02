<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\JurnalModel;
use App\Models\KelasModel;

class GuruMengajar extends BaseController
{
    protected $jadwal;
    protected $guru;
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
        $this->guru   = new GuruModel();
        $this->jurnal = new JurnalModel();
        $this->kelas  = new KelasModel();
    }

    /**
     * Daftar guru beserta jadwal mengajarnya (mapel, kelas, hari, jam),
     * diambil dari data Jadwal Pelajaran yang sudah diinput,
     * untuk tahun pelajaran & semester yang sedang aktif.
     *
     * Tanggal per baris dihitung otomatis dari hari (Senin-Sabtu) +
     * minggu yang dipilih, supaya kelihatan tanggal pastinya, bukan
     * cuma nama harinya (karena jadwal sendiri sifatnya berulang tiap
     * minggu, bukan per tanggal).
     */
    public function index()
    {
        $guruDipilih  = $this->request->getGet('guru_id');
        $kelasDipilih = $this->request->getGet('kelas_id');

        // Tanggal acuan untuk menentukan minggu yang ditampilkan.
        // Default: hari ini.
        $tanggalAcuan = $this->request->getGet('minggu') ?: date('Y-m-d');

        // Cari Senin di minggu yang sama dengan tanggal acuan.
        $seninMinggu = date('Y-m-d', strtotime('monday this week', strtotime($tanggalAcuan)));

        $builder = $this->jadwal
            ->select('
                jadwal.id,
                jadwal.kelas_id,
                jadwal.mapel_id,
                users.id as guru_id,
                users.nama as nama_guru,
                mata_pelajaran.nama_mapel,
                kelas.nama_kelas,
                hari.nama_hari,
                hari.urutan as urutan_hari,
                jam_pelajaran.jam_ke,
                jam_pelajaran.jam_mulai,
                jam_pelajaran.jam_selesai,
                ruangan.nama_ruang
            ')
            ->join('users', 'users.id = jadwal.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->join('ruangan', 'ruangan.id = jadwal.ruangan_id', 'left')
            ->where('jadwal.tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('jadwal.semester_id', $this->semesterAktif['id'] ?? null);

        if (!empty($guruDipilih)) {
            $builder->where('users.id', $guruDipilih);
        }

        if (!empty($kelasDipilih)) {
            $builder->where('jadwal.kelas_id', $kelasDipilih);
        }

        $jadwal = $builder
            ->orderBy('nama_guru', 'ASC')
            ->orderBy('urutan_hari', 'ASC')
            ->orderBy('jam_pelajaran.jam_ke', 'ASC')
            ->findAll();

        // Tambahkan tanggal konkret untuk tiap baris: Senin minggu ini
        // + (urutan hari - 1) hari. urutan_hari: 1=Senin ... 6=Sabtu.
        foreach ($jadwal as &$j) {
            $j['tanggal'] = date('Y-m-d', strtotime($seninMinggu . ' + ' . ((int) $j['urutan_hari'] - 1) . ' days'));
        }
        unset($j);

        // Ambil jurnal (kolom guru_id/kelas_id/mapel_id/tanggal/status) untuk
        // minggu yang sama, supaya bisa dicocokkan: sudah diisi jurnalnya
        // atau belum, per jadwal.
        $sabtuMinggu = date('Y-m-d', strtotime($seninMinggu . ' + 5 days'));

        $jurnalRows = $this->jurnal
            ->select('user_id, kelas_id, mapel_id, tanggal, status')
            ->where('tanggal >=', $seninMinggu)
            ->where('tanggal <=', $sabtuMinggu)
            ->findAll();

        $jurnalLookup = [];
        foreach ($jurnalRows as $jr) {
            $jurnalLookup[$jr['user_id'] . '-' . $jr['kelas_id'] . '-' . $jr['mapel_id'] . '-' . $jr['tanggal']] = $jr['status'];
        }

        foreach ($jadwal as &$j) {

            $key = $j['guru_id'] . '-' . ($j['kelas_id'] ?? '') . '-' . ($j['mapel_id'] ?? '') . '-' . $j['tanggal'];

            $j['status_jurnal'] = $jurnalLookup[$key] ?? null;
        }
        unset($j);

        // Rekap total jam mengajar per guru (dihitung dari SEMUA jadwal,
        // sebelum difilter status, supaya angka ringkasannya tetap utuh)
        $rekapJam = [];
        $rekapIsi = [];
        foreach ($jadwal as $j) {
            $rekapJam[$j['nama_guru']] = ($rekapJam[$j['nama_guru']] ?? 0) + 1;

            if (!isset($rekapIsi[$j['nama_guru']])) {
                $rekapIsi[$j['nama_guru']] = 0;
            }

            if ($j['status_jurnal'] === 'Masuk') {
                $rekapIsi[$j['nama_guru']]++;
            }
        }

        // Filter tampilan tabel: semua / sudah diisi saja / belum diisi saja
        $statusFilter = $this->request->getGet('status');

        if ($statusFilter === 'sudah') {
            $jadwal = array_values(array_filter($jadwal, fn($j) => $j['status_jurnal'] === 'Masuk'));
        } elseif ($statusFilter === 'belum') {
            $jadwal = array_values(array_filter($jadwal, fn($j) => $j['status_jurnal'] !== 'Masuk'));
        }

        $data = [
            'jadwal'        => $jadwal,
            'jadwalTabel'   => $this->kelompokkanJadwal($jadwal),
            'rekapJam'      => $rekapJam,
            'rekapIsi'      => $rekapIsi,
            'daftarGuru'    => $this->guru->getGuru()->findAll(),
            'daftarKelas'   => $this->kelas->orderBy('nama_kelas', 'ASC')->findAll(),
            'guruDipilih'   => $guruDipilih,
            'kelasDipilih'  => $kelasDipilih,
            'statusFilter'  => $statusFilter,
            'tanggalAcuan'  => $tanggalAcuan,
            'seninMinggu'   => $seninMinggu,
            'sabtuMinggu'   => $sabtuMinggu,
            'tahunAktif'    => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif,
        ];

        return view('guru_mengajar/index', $data);
    }

    /**
     * Gabungkan baris-baris jadwal yang guru + mapel + kelas + tanggalnya
     * sama (misal AHMAD RAMADHANI ngajar KEAHLIAN TJKT di XII TKJ jam 1-5
     * di tanggal yang sama) jadi SATU baris saja, dengan jam_ke ditampilkan
     * sebagai rentang (mis. "1-5"), waktu dari jam paling awal sampai
     * paling akhir, dan status jurnal berupa ringkasan "berapa sesi sudah
     * diisi". Ini murni untuk tampilan tabel; tidak mengubah data mentah
     * $jadwal yang dipakai untuk rekap jam mengajar.
     */
    private function kelompokkanJadwal(array $jadwal): array
    {
        $grouped = [];

        foreach ($jadwal as $j) {
            $key = $j['guru_id'] . '-' . ($j['mapel_id'] ?? '') . '-' . ($j['kelas_id'] ?? '') . '-' . $j['tanggal'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_guru'   => $j['nama_guru'],
                    'nama_mapel'  => $j['nama_mapel'],
                    'nama_kelas'  => $j['nama_kelas'],
                    'nama_hari'   => $j['nama_hari'],
                    'tanggal'     => $j['tanggal'],
                    'nama_ruang'  => $j['nama_ruang'],
                    'jam_list'    => [],
                    'jam_mulai'   => $j['jam_mulai'],
                    'jam_selesai' => $j['jam_selesai'],
                    'status_list' => [],
                ];
            }

            $grouped[$key]['jam_list'][]    = (int) $j['jam_ke'];
            $grouped[$key]['status_list'][] = $j['status_jurnal'];

            if ($j['jam_mulai'] < $grouped[$key]['jam_mulai']) {
                $grouped[$key]['jam_mulai'] = $j['jam_mulai'];
            }
            if ($j['jam_selesai'] > $grouped[$key]['jam_selesai']) {
                $grouped[$key]['jam_selesai'] = $j['jam_selesai'];
            }
        }

        $hasil = [];

        foreach ($grouped as $g) {
            $g['jam_ke_display'] = $this->formatRentangJam($g['jam_list']);

            $totalSesi  = count($g['status_list']);
            $sudahDiisi = count(array_filter($g['status_list'], fn($s) => $s === 'Masuk'));

            $g['total_sesi']    = $totalSesi;
            $g['sudah_diisi']   = $sudahDiisi;
            $g['status_jurnal'] = $sudahDiisi === $totalSesi ? 'Masuk' : null;

            unset($g['jam_list'], $g['status_list']);
            $hasil[] = $g;
        }

        // Urutkan per guru, lalu per tanggal, biar rapi (sama seperti urutan asal)
        usort($hasil, fn($a, $b) => [$a['nama_guru'], $a['tanggal']] <=> [$b['nama_guru'], $b['tanggal']]);

        return $hasil;
    }

    /**
     * Ubah daftar angka jam_ke (mis. [1,2,3,4,5]) jadi rentang yang enak
     * dibaca (mis. "1-5"), atau "1, 3-4" kalau ada yang tidak berurutan.
     */
    private function formatRentangJam(array $jamKe): string
    {
        sort($jamKe);
        $jamKe = array_values(array_unique($jamKe));

        $rentang = [];
        $awal = $akhir = $jamKe[0];

        for ($i = 1; $i < count($jamKe); $i++) {
            if ($jamKe[$i] == $akhir + 1) {
                $akhir = $jamKe[$i];
            } else {
                $rentang[] = $awal == $akhir ? "$awal" : "$awal-$akhir";
                $awal = $akhir = $jamKe[$i];
            }
        }
        $rentang[] = $awal == $akhir ? "$awal" : "$awal-$akhir";

        return implode(', ', $rentang);
    }
}
