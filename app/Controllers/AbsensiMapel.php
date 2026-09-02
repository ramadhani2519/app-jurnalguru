<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\AbsensiMapelModel;
use App\Models\SekolahModel;

/**
 * Absensi untuk Guru Mata Pelajaran (BUKAN Wali Kelas).
 *
 * Beda dengan Absensi (wali kelas) yang cuma sekali sehari per kelas,
 * di sini SATU guru bisa punya BEBERAPA sesi mengajar dalam sehari
 * (kelas berbeda dan/atau mapel berbeda), jadi absensinya juga per
 * sesi mengajar (guru + mapel + kelas + tanggal), bukan per hari.
 *
 * "Sesi mengajar" diambil otomatis dari Jadwal Pelajaran yang sudah
 * dibuat Wakasek Kurikulum -- guru tidak perlu input jadwal manual,
 * cukup pilih sesi mana yang mau diabsen.
 */
class AbsensiMapel extends BaseController
{
    protected $siswa;
    protected $absensiMapel;
    protected $sekolah;

    private $labelStatus = [
        'H' => 'Hadir',
        'S' => 'Sakit',
        'I' => 'Izin',
        'A' => 'Alpa',
    ];

    private $mapHari = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => "Jum'at",
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        $this->siswa        = new SiswaModel();
        $this->absensiMapel = new AbsensiMapelModel();
        $this->sekolah      = new SekolahModel();
    }

    private function namaHariDariTanggal(string $tanggal): string
    {
        return $this->mapHari[date('l', strtotime($tanggal))] ?? '';
    }

    /**
     * Ubah daftar angka jam_ke jadi rentang enak dibaca, mis. [1,2,3] -> "1-3"
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

    /**
     * Ambil sesi-sesi mengajar guru tsb di tanggal tertentu, dari tabel
     * jadwal, dikelompokkan per mapel+kelas (sesi berurutan jam 1-5
     * jadi SATU sesi, bukan 5 sesi terpisah).
     */
    private function ambilSesiMengajar(int $guru_id, string $tanggal): array
    {
        $db = \Config\Database::connect();
        $namaHari = $this->namaHariDariTanggal($tanggal);

        $jadwal = $db->table('jadwal')
            ->select('
                jadwal.mapel_id, jadwal.kelas_id,
                mata_pelajaran.nama_mapel, kelas.nama_kelas,
                ruangan.nama_ruang,
                jam_pelajaran.jam_ke, jam_pelajaran.jam_mulai, jam_pelajaran.jam_selesai
            ')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->join('ruangan', 'ruangan.id = jadwal.ruangan_id', 'left')
            ->where('jadwal.guru_id', $guru_id)
            ->where('hari.nama_hari', $namaHari)
            ->where('jadwal.tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('jadwal.semester_id', $this->semesterAktif['id'] ?? null)
            ->orderBy('jam_pelajaran.jam_ke', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($jadwal as $j) {
            $key = $j['mapel_id'] . '-' . $j['kelas_id'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'sesi_key'    => $key,
                    'mapel_id'    => $j['mapel_id'],
                    'kelas_id'    => $j['kelas_id'],
                    'nama_mapel'  => $j['nama_mapel'],
                    'nama_kelas'  => $j['nama_kelas'],
                    'nama_ruang'  => $j['nama_ruang'],
                    'jam_list'    => [],
                    'jam_mulai'   => $j['jam_mulai'],
                    'jam_selesai' => $j['jam_selesai'],
                ];
            }

            $grouped[$key]['jam_list'][] = (int) $j['jam_ke'];

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
            $g['total_jam']      = count($g['jam_list']);
            unset($g['jam_list']);
            $hasil[] = $g;
        }

        return $hasil;
    }

    public function index()
    {
        $tanggal  = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $guru_id  = session()->get('id');
        $sesiKey  = $this->request->getGet('sesi');

        $sesiList = $this->ambilSesiMengajar((int) $guru_id, $tanggal);

        $sesiAktif        = null;
        $siswa            = [];
        $absensiTersimpan = [];

        foreach ($sesiList as $s) {
            if ($s['sesi_key'] === $sesiKey) {
                $sesiAktif = $s;
                break;
            }
        }

        if ($sesiAktif) {

            $siswa = $this->siswa
                ->where('kelas_id', $sesiAktif['kelas_id'])
                ->orderBy('nama_siswa', 'ASC')
                ->findAll();

            $rows = $this->absensiMapel
                ->where('tanggal', $tanggal)
                ->where('guru_id', $guru_id)
                ->where('mapel_id', $sesiAktif['mapel_id'])
                ->where('kelas_id', $sesiAktif['kelas_id'])
                ->findAll();

            foreach ($rows as $r) {
                $absensiTersimpan[$r['siswa_id']] = $r['status'];
            }
        }

        $data = [
            'tanggal'          => $tanggal,
            'sesiList'         => $sesiList,
            'sesiAktif'        => $sesiAktif,
            'siswa'            => $siswa,
            'absensiTersimpan' => $absensiTersimpan,
            'labelStatus'      => $this->labelStatus,
            'tahunAktif'       => $this->tahunAktif,
            'semesterAktif'    => $this->semesterAktif,
        ];

        return view('absensi_mapel/index', $data);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $guru_id  = session()->get('id');
        $tanggal  = $this->request->getPost('tanggal');
        $mapel_id = $this->request->getPost('mapel_id');
        $kelas_id = $this->request->getPost('kelas_id');
        $siswa_id = $this->request->getPost('siswa_id');
        $status   = $this->request->getPost('status');
        $jamKeDisplay = $this->request->getPost('jam_ke_display');

        $data = [
            'tanggal'            => $tanggal,
            'tahun_pelajaran_id' => $this->tahunAktif['id'] ?? null,
            'semester_id'        => $this->semesterAktif['id'] ?? null,
            'guru_id'            => $guru_id,
            'mapel_id'           => $mapel_id,
            'kelas_id'           => $kelas_id,
            'jam_ke_display'     => $jamKeDisplay,
            'siswa_id'           => $siswa_id,
            'status'             => $status,
        ];

        $cek = $this->absensiMapel
            ->where('tanggal', $tanggal)
            ->where('guru_id', $guru_id)
            ->where('mapel_id', $mapel_id)
            ->where('kelas_id', $kelas_id)
            ->where('siswa_id', $siswa_id)
            ->first();

        if ($cek) {
            $this->absensiMapel->update($cek['id'], $data);
        } else {
            $this->absensiMapel->insert($data);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi tersimpan.',
        ]);
    }

    public function simpanMassal()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $guru_id      = session()->get('id');
        $tanggal      = $this->request->getPost('tanggal');
        $mapel_id     = $this->request->getPost('mapel_id');
        $kelas_id     = $this->request->getPost('kelas_id');
        $jamKeDisplay = $this->request->getPost('jam_ke_display');

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($this->request->getPost('absensi') as $row) {

            $data = [
                'tanggal'            => $tanggal,
                'tahun_pelajaran_id' => $this->tahunAktif['id'] ?? null,
                'semester_id'        => $this->semesterAktif['id'] ?? null,
                'guru_id'            => $guru_id,
                'mapel_id'           => $mapel_id,
                'kelas_id'           => $kelas_id,
                'jam_ke_display'     => $jamKeDisplay,
                'siswa_id'           => $row['siswa_id'],
                'status'             => $row['status'],
            ];

            $cek = $this->absensiMapel
                ->where('tanggal', $tanggal)
                ->where('guru_id', $guru_id)
                ->where('mapel_id', $mapel_id)
                ->where('kelas_id', $kelas_id)
                ->where('siswa_id', $row['siswa_id'])
                ->first();

            if ($cek) {
                $this->absensiMapel->update($cek['id'], $data);
            } else {
                $this->absensiMapel->insert($data);
            }
        }

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi massal berhasil.',
        ]);
    }

    /**
     * Ambil daftar mapel & kelas yang PERNAH diajar guru ini (dari histori
     * jadwal, bukan cuma tahun/semester aktif), supaya dropdown filter
     * rekap tetap bisa dipakai untuk lihat data periode-periode lama.
     */
    private function daftarMapelKelasGuru(int $guru_id): array
    {
        $db = \Config\Database::connect();

        $mapel = $db->table('jadwal')
            ->select('mata_pelajaran.id, mata_pelajaran.nama_mapel')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('jadwal.guru_id', $guru_id)
            ->groupBy('mata_pelajaran.id, mata_pelajaran.nama_mapel')
            ->orderBy('mata_pelajaran.nama_mapel', 'ASC')
            ->get()
            ->getResultArray();

        $kelas = $db->table('jadwal')
            ->select('kelas.id, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->where('jadwal.guru_id', $guru_id)
            ->groupBy('kelas.id, kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        return [$mapel, $kelas];
    }

    /**
     * Susun rekap H/S/I/A per siswa untuk guru + mapel + kelas + rentang
     * tanggal tertentu. Dipakai bareng oleh rekap() (preview di layar)
     * dan exportRekap() (unduh Excel).
     */
    private function susunRekap(int $guru_id, int $mapel_id, int $kelas_id, string $tanggalAwal, string $tanggalAkhir): array
    {
        $db = \Config\Database::connect();

        $siswaList = $this->siswa
            ->where('kelas_id', $kelas_id)
            ->orderBy('nama_siswa', 'ASC')
            ->findAll();

        $absensi = $db->table('absensi_mapel')
            ->select('siswa_id, tanggal, status')
            ->where('guru_id', $guru_id)
            ->where('mapel_id', $mapel_id)
            ->where('kelas_id', $kelas_id)
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();

        $tanggalList = array_values(array_unique(array_column($absensi, 'tanggal')));
        sort($tanggalList);

        $totalPertemuan = count($tanggalList);

        $rekapPerSiswa   = [];
        $detailPerTanggal = []; // siswa_id => [tanggal => status]

        foreach ($siswaList as $s) {
            $rekapPerSiswa[$s['id']] = [
                'nis'        => $s['nis'] ?? '',
                'nama_siswa' => $s['nama_siswa'],
                'H' => 0, 'S' => 0, 'I' => 0, 'A' => 0,
            ];
            $detailPerTanggal[$s['id']] = array_fill_keys($tanggalList, '-');
        }

        foreach ($absensi as $row) {
            if (isset($rekapPerSiswa[$row['siswa_id']][$row['status']])) {
                $rekapPerSiswa[$row['siswa_id']][$row['status']]++;
            }
            if (isset($detailPerTanggal[$row['siswa_id']])) {
                $detailPerTanggal[$row['siswa_id']][$row['tanggal']] = $row['status'];
            }
        }

        foreach ($rekapPerSiswa as $siswaId => &$r) {
            $r['persentase_hadir'] = $totalPertemuan > 0
                ? round($r['H'] / $totalPertemuan * 100, 1)
                : 0.0;
            $r['siswa_id'] = $siswaId;
            $r['detail']   = $detailPerTanggal[$siswaId];
        }
        unset($r);

        return [
            'rekap'          => array_values($rekapPerSiswa),
            'totalPertemuan' => $totalPertemuan,
            'tanggalList'    => $tanggalList,
        ];
    }

    /**
     * Halaman filter + preview rekap absensi mapel: pilih mapel, kelas,
     * dan rentang tanggal, lalu bisa diunduh dalam bentuk Excel.
     */
    public function rekap()
    {
        $guru_id = session()->get('id');

        [$mapelList, $kelasList] = $this->daftarMapelKelasGuru((int) $guru_id);

        $mapel_id     = $this->request->getGet('mapel_id');
        $kelas_id     = $this->request->getGet('kelas_id');
        $tanggalAwal  = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $rekap          = [];
        $totalPertemuan = 0;
        $tanggalList    = [];

        if ($mapel_id && $kelas_id && $tanggalAwal && $tanggalAkhir) {
            $hasil          = $this->susunRekap((int) $guru_id, (int) $mapel_id, (int) $kelas_id, $tanggalAwal, $tanggalAkhir);
            $rekap          = $hasil['rekap'];
            $totalPertemuan = $hasil['totalPertemuan'];
            $tanggalList    = $hasil['tanggalList'];
        }

        $data = [
            'mapelList'      => $mapelList,
            'kelasList'      => $kelasList,
            'mapel_id'       => $mapel_id,
            'kelas_id'       => $kelas_id,
            'tanggal_awal'   => $tanggalAwal,
            'tanggal_akhir'  => $tanggalAkhir,
            'rekap'          => $rekap,
            'totalPertemuan' => $totalPertemuan,
            'tanggalList'    => $tanggalList,
            'labelStatus'    => $this->labelStatus,
        ];

        return view('absensi_mapel/rekap', $data);
    }

    /**
     * Unduh rekap absensi mapel (guru + mapel + kelas + rentang tanggal)
     * sebagai file Excel (.xlsx).
     */
    public function exportRekap()
    {
        $guru_id      = session()->get('id');
        $mapel_id     = $this->request->getGet('mapel_id');
        $kelas_id     = $this->request->getGet('kelas_id');
        $tanggalAwal  = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        if (empty($mapel_id) || empty($kelas_id) || empty($tanggalAwal) || empty($tanggalAkhir)) {
            return redirect()->back()->with('error', 'Lengkapi mapel, kelas, dan rentang tanggal terlebih dahulu.');
        }

        $db = \Config\Database::connect();

        $mapelInfo = $db->table('mata_pelajaran')->where('id', $mapel_id)->get()->getRowArray();
        $kelasInfo = $db->table('kelas')->where('id', $kelas_id)->get()->getRowArray();
        $guruInfo  = $db->table('users')->where('id', $guru_id)->get()->getRowArray();

        if (!$mapelInfo || !$kelasInfo) {
            return redirect()->back()->with('error', 'Mapel atau kelas tidak ditemukan.');
        }

        $hasil          = $this->susunRekap((int) $guru_id, (int) $mapel_id, (int) $kelas_id, $tanggalAwal, $tanggalAkhir);
        $rekap          = $hasil['rekap'];
        $totalPertemuan = $hasil['totalPertemuan'];
        $tanggalList    = $hasil['tanggalList'];

        if (empty($rekap)) {
            return redirect()->back()->with('error', 'Belum ada data siswa di kelas ini.');
        }

        $sekolah = $this->sekolah->first();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi Mapel');

        // Kolom: No, NIS, Nama Siswa, [satu kolom per tanggal pertemuan], Hadir, Sakit, Izin, Alpa, % Hadir
        $kolomTetapAwal  = 3; // No, NIS, Nama Siswa
        $jumlahTanggal   = count($tanggalList);
        $kolomTetapAkhir = 5; // Hadir, Sakit, Izin, Alpa, % Hadir -> tapi dihitung terpisah di bawah
        $totalKolom      = $kolomTetapAwal + $jumlahTanggal + 5;

        $colTerakhir = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom);

        $sheet->setCellValue('A1', 'REKAP ABSENSI MATA PELAJARAN');
        $sheet->mergeCells("A1:{$colTerakhir}1");

        $sheet->setCellValue('A2', $sekolah['nama_sekolah'] ?? '');
        $sheet->mergeCells("A2:{$colTerakhir}2");

        $sheet->setCellValue('A4', 'Mata Pelajaran');
        $sheet->setCellValue('C4', ': ' . ($mapelInfo['nama_mapel'] ?? ''));
        $sheet->setCellValue('A5', 'Kelas');
        $sheet->setCellValue('C5', ': ' . ($kelasInfo['nama_kelas'] ?? ''));
        $sheet->setCellValue('A6', 'Guru');
        $sheet->setCellValue('C6', ': ' . ($guruInfo['nama'] ?? ''));
        $sheet->setCellValue('A7', 'Periode');
        $sheet->setCellValue('C7', ': ' . date('d/m/Y', strtotime($tanggalAwal)) . ' s/d ' . date('d/m/Y', strtotime($tanggalAkhir)));
        $sheet->setCellValue('A8', 'Total Pertemuan');
        $sheet->setCellValue('C8', ': ' . $totalPertemuan . ' kali (' . implode(', ', array_map(fn($t) => date('d/m/Y', strtotime($t)), $tanggalList)) . ')');

        foreach (['A1', 'A2'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize($cell === 'A1' ? 14 : 11);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $barisTanggal = 10; // baris header tanggal (per kolom pertemuan)
        $barisHeader  = 11; // baris header No/NIS/Nama/H/S/I/A/%

        $sheet->setCellValue("A{$barisTanggal}", 'No');
        $sheet->setCellValue("B{$barisTanggal}", 'NIS');
        $sheet->setCellValue("C{$barisTanggal}", 'Nama Siswa');
        $sheet->mergeCells("A{$barisTanggal}:A{$barisHeader}");
        $sheet->mergeCells("B{$barisTanggal}:B{$barisHeader}");
        $sheet->mergeCells("C{$barisTanggal}:C{$barisHeader}");

        $kolomIndex = $kolomTetapAwal + 1;

        foreach ($tanggalList as $tgl) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex);
            $sheet->setCellValue($col . $barisTanggal, date('d/m', strtotime($tgl)));
            $sheet->getStyle($col . $barisTanggal)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setTextRotation(90);
            $sheet->getColumnDimension($col)->setWidth(4);
            $kolomIndex++;
        }

        if ($jumlahTanggal > 0) {
            $colAwalTgl  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTetapAwal + 1);
            $colAkhirTgl = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomTetapAwal + $jumlahTanggal);
            $sheet->mergeCells("{$colAwalTgl}9:{$colAkhirTgl}9");
            $sheet->setCellValue("{$colAwalTgl}9", 'Tanggal Pertemuan');
            $sheet->getStyle("{$colAwalTgl}9")->getFont()->setBold(true);
            $sheet->getStyle("{$colAwalTgl}9")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $labelRingkasan = ['Hadir', 'Sakit', 'Izin', 'Alpa', '% Hadir'];
        foreach ($labelRingkasan as $judul) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex);
            $sheet->setCellValue($col . $barisTanggal, $judul);
            $sheet->mergeCells($col . $barisTanggal . ':' . $col . $barisHeader);
            $sheet->getStyle($col . $barisTanggal)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $kolomIndex++;
        }

        $sheet->getStyle("A{$barisTanggal}:{$colTerakhir}{$barisHeader}")
            ->getFont()->setBold(true);
        $sheet->getStyle("A{$barisTanggal}:{$colTerakhir}{$barisHeader}")
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        $sheet->getStyle("A{$barisTanggal}:{$colTerakhir}{$barisHeader}")
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $baris = $barisHeader + 1;
        $no = 1;

        foreach ($rekap as $r) {
            $sheet->setCellValue("A{$baris}", $no);
            $sheet->setCellValue("B{$baris}", $r['nis']);
            $sheet->setCellValue("C{$baris}", $r['nama_siswa']);

            $kolomIndex = $kolomTetapAwal + 1;
            foreach ($tanggalList as $tgl) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex);
                $sheet->setCellValue($col . $baris, $r['detail'][$tgl] ?? '-');
                $sheet->getStyle($col . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $kolomIndex++;
            }

            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex) . $baris, $r['H']); $kolomIndex++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex) . $baris, $r['S']); $kolomIndex++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex) . $baris, $r['I']); $kolomIndex++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex) . $baris, $r['A']); $kolomIndex++;
            $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolomIndex) . $baris, $r['persentase_hadir'] . '%'); $kolomIndex++;

            $baris++;
            $no++;
        }

        $sheet->getStyle("A{$barisHeader}:{$colTerakhir}" . ($baris - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Ringkasan persentase kehadiran keseluruhan (semua siswa) di bagian bawah.
        $totalHadirSemua = 0;
        foreach ($rekap as $r) {
            $totalHadirSemua += $r['H'];
        }
        $totalSiswa = count($rekap);
        $persentaseKeseluruhan = ($totalPertemuan > 0 && $totalSiswa > 0)
            ? round($totalHadirSemua / ($totalPertemuan * $totalSiswa) * 100, 2)
            : 0;

        $barisPersentase = $baris + 1;
        $colLabelAkhir   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalKolom - 1);

        $sheet->mergeCells("A{$barisPersentase}:{$colLabelAkhir}{$barisPersentase}");
        $sheet->setCellValue(
            "A{$barisPersentase}",
            'PERSENTASE KEHADIRAN PESERTA DIDIK PERIODE ' . date('d/m/Y', strtotime($tanggalAwal)) . ' s/d ' . date('d/m/Y', strtotime($tanggalAkhir))
        );
        $sheet->getStyle("A{$barisPersentase}")->getFont()->setBold(true);
        $sheet->getStyle("A{$barisPersentase}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue($colTerakhir . $barisPersentase, $persentaseKeseluruhan . '%');
        $sheet->getStyle($colTerakhir . $barisPersentase)->getFont()->setBold(true);
        $sheet->getStyle($colTerakhir . $barisPersentase)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $namaFile = 'Rekap-Absensi-Mapel-'
            . str_replace(' ', '-', $mapelInfo['nama_mapel'] ?? '') . '-'
            . str_replace(' ', '-', $kelasInfo['nama_kelas'] ?? '') . '-'
            . $tanggalAwal . '_' . $tanggalAkhir . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    public function cetak()
    {
        $db = \Config\Database::connect();

        $tanggal  = $this->request->getGet('tanggal');
        $guru_id  = $this->request->getGet('guru_id') ?? session()->get('id');
        $mapel_id = $this->request->getGet('mapel_id');
        $kelas_id = $this->request->getGet('kelas_id');

        $absensi = $db->table('absensi_mapel')
            ->select('
                absensi_mapel.*,
                siswa.nis, siswa.nama_siswa,
                kelas.nama_kelas,
                mata_pelajaran.nama_mapel,
                users.nama as nama_guru
            ')
            ->join('siswa', 'siswa.id = absensi_mapel.siswa_id')
            ->join('kelas', 'kelas.id = absensi_mapel.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = absensi_mapel.mapel_id')
            ->join('users', 'users.id = absensi_mapel.guru_id')
            ->where('absensi_mapel.tanggal', $tanggal)
            ->where('absensi_mapel.guru_id', $guru_id)
            ->where('absensi_mapel.mapel_id', $mapel_id)
            ->where('absensi_mapel.kelas_id', $kelas_id)
            ->orderBy('siswa.nama_siswa', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($absensi)) {
            return redirect()->back()->with('error', 'Data absensi belum diisi / tidak ditemukan.');
        }

        $sekolah = $this->sekolah->first();

        $logoBase64         = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,
            'absensi'            => $absensi,
            'tanggal'            => $tanggal,
            'kelas'              => $absensi[0]['nama_kelas'],
            'mapel'              => $absensi[0]['nama_mapel'],
            'guru'               => $absensi[0]['nama_guru'],
            'jamKeDisplay'       => $absensi[0]['jam_ke_display'],
            'labelStatus'        => $this->labelStatus,
            'tahunAktif'         => $this->tahunAktif,
            'semesterAktif'      => $this->semesterAktif,
        ];

        $html = view('absensi_mapel/cetak', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            'Absensi-Mapel-' . $tanggal . '.pdf',
            ['Attachment' => false]
        );

        exit;
    }
}
