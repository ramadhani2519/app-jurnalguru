<?php

namespace App\Controllers;

use App\Models\SekolahModel;

class KepalaSekolah extends BaseController
{
    protected $sekolah;

    public function __construct()
    {
        $this->sekolah = new SekolahModel();
    }

    /**
     * Peta nama hari (Inggris dari PHP) -> Indonesia
     */
    private function namaHariIni()
    {
        $mapHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => "Jum'at",
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        return $mapHari[date('l')] ?? '';
    }

    /**
     * Cek apakah dua nilai jam_ke "beririsan" (overlap). Masing-masing
     * bisa berupa angka tunggal (mis. "3") atau rentang (mis. "6-9").
     */
    private function jamKeCocok($jurnalJamKe, $jamSekarang)
    {
        [$awal1, $akhir1] = $this->parseRentangJam($jurnalJamKe);
        [$awal2, $akhir2] = $this->parseRentangJam($jamSekarang);

        return $awal1 <= $akhir2 && $awal2 <= $akhir1;
    }

    /**
     * Ubah nilai jam_ke (angka tunggal atau rentang "awal-akhir")
     * jadi pasangan [awal, akhir].
     */
    private function parseRentangJam($jamKe): array
    {
        if (strpos((string) $jamKe, '-') !== false) {
            [$awal, $akhir] = explode('-', (string) $jamKe);
            return [(int) $awal, (int) $akhir];
        }

        return [(int) $jamKe, (int) $jamKe];
    }

    /**
     * Ambil data monitoring lengkap: daftar guru, status jurnal hari ini,
     * DAN status real-time (sedang mengajar jam berapa sekarang / tidak).
     */
    private function ambilDataMonitoring()
    {
        $db = \Config\Database::connect();

        $tanggal          = date('Y-m-d');
        $namaHariIni       = $this->namaHariIni();
        $jamSekarangWaktu = date('H:i:s');

        // Daftar guru + total jurnal yang sudah diinput hari ini
        $guru = $db->table('users')
            ->select("
                users.id,
                users.nama,
                COUNT(jurnal.id) AS total_jurnal
            ")
            ->join('jurnal', "jurnal.user_id = users.id AND jurnal.tanggal = '$tanggal'", 'left')
            ->where('users.role_id', 2)
            ->groupBy('users.id')
            ->orderBy('users.nama', 'ASC')
            ->get()
            ->getResultArray();

        // Semua jadwal guru hari ini (tanpa filter jam), lalu dikelompokkan
        // per guru+kelas+mapel untuk dapat rentang waktu keseluruhan sesi
        // (jam_mulai paling awal s.d. jam_selesai paling akhir). Ini penting
        // supaya guru tetap terdeteksi "sedang mengajar" walau waktu sekarang
        // jatuh di celah istirahat DI ANTARA jam pelajaran dalam blok yang
        // sama (mis. istirahat antara jam ke-6 dan jam ke-7).
        $jadwalHariIniMentah = $db->table('jadwal')
            ->select("
                jadwal.guru_id,
                jadwal.kelas_id,
                jadwal.mapel_id,
                kelas.nama_kelas,
                mata_pelajaran.nama_mapel,
                jam_pelajaran.jam_ke,
                jam_pelajaran.jam_mulai,
                jam_pelajaran.jam_selesai,
                ruangan.nama_ruang
            ")
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->join('ruangan', 'ruangan.id = jadwal.ruangan_id', 'left')
            ->where('hari.nama_hari', $namaHariIni)
            ->orderBy('jam_pelajaran.jam_ke', 'ASC')
            ->get()
            ->getResultArray();

        // Kelompokkan per guru+kelas+mapel, ambil rentang waktu keseluruhan
        $sesiByGuru = [];
        foreach ($jadwalHariIniMentah as $j) {
            $key = $j['guru_id'] . '-' . $j['kelas_id'] . '-' . $j['mapel_id'];

            if (!isset($sesiByGuru[$key])) {
                $sesiByGuru[$key] = $j;
                $sesiByGuru[$key]['jam_ke_list'] = [(int) $j['jam_ke']];
            } else {
                $sesiByGuru[$key]['jam_ke_list'][] = (int) $j['jam_ke'];

                if ($j['jam_mulai'] < $sesiByGuru[$key]['jam_mulai']) {
                    $sesiByGuru[$key]['jam_mulai'] = $j['jam_mulai'];
                }
                if ($j['jam_selesai'] > $sesiByGuru[$key]['jam_selesai']) {
                    $sesiByGuru[$key]['jam_selesai'] = $j['jam_selesai'];
                }
            }
        }

        // Untuk setiap guru, ambil sesi yang rentang waktunya (awal s.d.
        // akhir, termasuk celah istirahat di tengah) mencakup waktu sekarang.
        $jadwalByGuru = [];
        foreach ($sesiByGuru as $s) {
            if ($jamSekarangWaktu >= $s['jam_mulai'] && $jamSekarangWaktu <= $s['jam_selesai']) {
                $s['jam_ke'] = $this->formatRentangJam($s['jam_ke_list']);
                $jadwalByGuru[$s['guru_id']] = $s;
            }
        }

        // Semua baris jurnal hari ini (detail, untuk cek kecocokan sesi saat ini + ambil foto)
        $jurnalHariIni = $db->table('jurnal')
            ->select('user_id, kelas_id, mapel_id, jam_ke, foto')
            ->where('tanggal', $tanggal)
            ->get()
            ->getResultArray();

        foreach ($guru as &$g) {

            $g['sedang_kelas']    = null;
            $g['sedang_mapel']    = null;
            $g['sedang_jam_ke']   = null;
            $g['sedang_waktu']    = null;
            $g['sedang_ruangan']  = null;
            $g['foto_sesi_ini']   = null;
            $g['status_sesi_ini'] = 'tidak_ada_jadwal';
            $g['keterangan']      = 'Tidak ada jadwal saat ini';

            if (isset($jadwalByGuru[$g['id']])) {

                $j = $jadwalByGuru[$g['id']];

                $g['sedang_kelas']   = $j['nama_kelas'];
                $g['sedang_mapel']   = $j['nama_mapel'];
                $g['sedang_jam_ke']  = $j['jam_ke'];
                $g['sedang_waktu']   = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
                $g['sedang_ruangan'] = $j['nama_ruang'] ?? 'Tanpa Ruangan';

                $sudahIsi = false;

                foreach ($jurnalHariIni as $jr) {
                    if (
                        $jr['user_id'] == $g['id'] &&
                        $jr['kelas_id'] == $j['kelas_id'] &&
                        $jr['mapel_id'] == $j['mapel_id'] &&
                        $this->jamKeCocok($jr['jam_ke'], $j['jam_ke'])
                    ) {
                        $sudahIsi = true;

                        // Kalau ada beberapa baris jurnal untuk sesi yang sama
                        // (mis. entri lama tanpa foto + entri baru dengan foto),
                        // prioritaskan yang punya foto, jangan langsung berhenti
                        // di kecocokan pertama.
                        if (!empty($jr['foto'])) {
                            $g['foto_sesi_ini'] = $jr['foto'];
                            break;
                        } elseif (empty($g['foto_sesi_ini'])) {
                            $g['foto_sesi_ini'] = $jr['foto'] ?? null;
                        }
                    }
                }

                if ($sudahIsi) {
                    $g['status_sesi_ini'] = 'sudah';
                    $g['keterangan']      = 'Guru sudah melaksanakan tugas';
                } else {
                    $g['status_sesi_ini'] = 'belum';
                    $g['keterangan']      = 'Guru belum mengisi jurnal untuk sesi ini';
                }
            }
        }
        unset($g);

        $guruMasuk = 0;
        foreach ($guru as $g) {
            if ($g['total_jurnal'] > 0) {
                $guruMasuk++;
            }
        }

        return [
            'guru'      => $guru,
            'guruMasuk' => $guruMasuk,
            'totalGuru' => count($guru),
        ];
    }

    /**
     * Daftar jadwal SELURUH guru untuk hari ini saja (bukan satu minggu
     * penuh seperti di halaman Guru Mengajar), lengkap dengan status
     * jurnalnya, untuk ditampilkan di dashboard Kepala Sekolah.
     */
    private function ambilJadwalHariIni()
    {
        $db = \Config\Database::connect();

        $tanggal    = date('Y-m-d');
        $namaHariIni = $this->namaHariIni();

        $jadwal = $db->table('jadwal')
            ->select("
                users.id as guru_id,
                users.nama as nama_guru,
                mata_pelajaran.nama_mapel,
                kelas.nama_kelas,
                jam_pelajaran.jam_ke,
                jam_pelajaran.jam_mulai,
                jam_pelajaran.jam_selesai,
                ruangan.nama_ruang,
                jadwal.kelas_id,
                jadwal.mapel_id
            ")
            ->join('users', 'users.id = jadwal.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->join('ruangan', 'ruangan.id = jadwal.ruangan_id', 'left')
            ->where('hari.nama_hari', $namaHariIni)
            ->where('jadwal.tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('jadwal.semester_id', $this->semesterAktif['id'] ?? null)
            ->orderBy('jam_pelajaran.jam_ke', 'ASC')
            ->orderBy('users.nama', 'ASC')
            ->get()
            ->getResultArray();

        // Status jurnal hari ini, dicocokkan per guru+kelas+mapel (sama
        // seperti pendekatan di GuruMengajar::index())
        $jurnalRows = $db->table('jurnal')
            ->select('user_id, kelas_id, mapel_id, status')
            ->where('tanggal', $tanggal)
            ->get()
            ->getResultArray();

        $jurnalLookup = [];
        foreach ($jurnalRows as $jr) {
            $jurnalLookup[$jr['user_id'] . '-' . $jr['kelas_id'] . '-' . $jr['mapel_id']] = $jr['status'];
        }

        foreach ($jadwal as &$j) {
            $key = $j['guru_id'] . '-' . $j['kelas_id'] . '-' . $j['mapel_id'];
            $j['status_jurnal'] = $jurnalLookup[$key] ?? null;
        }
        unset($j);

        return $this->kelompokkanJadwal($jadwal);
    }

    /**
     * Gabungkan baris-baris jadwal yang guru + mapel + kelasnya sama
     * (misal AHMAD RAMADHANI ngajar KEAHLIAN TJKT di XII TKJ jam 1-5)
     * jadi SATU baris saja, dengan jam_ke ditampilkan sebagai rentang
     * (mis. "1-5"), waktu dari jam paling awal sampai paling akhir, dan
     * status jurnal berupa ringkasan "berapa sesi sudah diisi".
     */
    private function kelompokkanJadwal(array $jadwal): array
    {
        $grouped = [];

        foreach ($jadwal as $j) {
            $key = $j['guru_id'] . '-' . $j['mapel_id'] . '-' . $j['kelas_id'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_guru'   => $j['nama_guru'],
                    'nama_mapel'  => $j['nama_mapel'],
                    'nama_kelas'  => $j['nama_kelas'],
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

            $g['total_sesi']  = $totalSesi;
            $g['sudah_diisi'] = $sudahDiisi;
            $g['status_jurnal'] = $sudahDiisi === $totalSesi ? 'Masuk' : null;

            unset($g['jam_list'], $g['status_list']);
            $hasil[] = $g;
        }

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

    public function dashboard()
    {
        $monitoring = $this->ambilDataMonitoring();

        $data = [
            'totalGuru'      => $monitoring['totalGuru'],
            'guruMasuk'      => $monitoring['guruMasuk'],
            'guru'           => $monitoring['guru'],

            'sekolah'        => $this->sekolah->first(),

            'namaGuru'       => session()->get('nama'),
            'fotoGuru'       => session()->get('foto'),

            'jumlahJurnal'   => 0,
            'jumlahAbsen'    => 0,
            'jadwalHariIni'  => $this->ambilJadwalHariIni(),
        ];

        return view('kepsek/index', $data);
    }

    public function monitoring()
    {
        $monitoring = $this->ambilDataMonitoring();

        $monitoring['jadwalHariIni'] = $this->ambilJadwalHariIni();

        return $this->response->setJSON($monitoring);
    }
}
