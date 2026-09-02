<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AbsensiSholatModel;

class AbsensiSholat extends BaseController
{
    protected $siswa;
    protected $kelas;
    protected $absensiSholat;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Petugas Absen Harian (role_id 4) TIDAK boleh akses Absensi Sholat
        // -- ini akun terpisah, khusus absen sholat pakai role_id 5.
        if (session()->get('role_id') == 4) {
            redirect()->to('/absensi')->send();
            exit;
        }

        $this->siswa         = new SiswaModel();
        $this->kelas          = new KelasModel();
        $this->absensiSholat = new AbsensiSholatModel();
    }

    /**
     * Untuk role Petugas Absen Sholat (Siswa / role_id 5),
     * kelas_id SELALU diambil dari session, tidak pernah dari input
     * user (GET/POST), supaya tidak bisa diakali untuk mengabsen
     * kelas lain.
     */
    private function kelasTerkunci($kelas_id)
    {
        if (session()->get('role_id') == 5) {
            return session()->get('kelas_id');
        }

        return $kelas_id;
    }

    /**
     * Untuk role Petugas Absen Sholat (Siswa / role_id 5), tanggal
     * SELALU dikunci ke hari ini (server), tidak pernah dari input
     * user (GET/POST), supaya tidak bisa diakali untuk mengabsen
     * tanggal yang sudah lewat atau tanggal yang akan datang.
     */
    private function tanggalTerkunci($tanggal)
    {
        if (session()->get('role_id') == 5) {
            return date('Y-m-d');
        }

        return $tanggal;
    }

    public function index()
    {
        $kelas_id     = $this->kelasTerkunci($this->request->getGet('kelas_id'));
        $jenis_sholat = $this->request->getGet('jenis_sholat');
        $tanggal      = $this->tanggalTerkunci($this->request->getGet('tanggal') ?? date('Y-m-d'));

        $data = [
            'tanggal'      => $tanggal,
            'kelas'        => $this->kelas->findAll(),
            'kelas_id'     => $kelas_id,
            'jenis_sholat' => $jenis_sholat,
            'tahunAktif'   => $this->tahunAktif,
            'semesterAktif'=> $this->semesterAktif,
            'siswa'        => [],
            'absensi'      => []
        ];

        if ($kelas_id && $jenis_sholat) {

            $data['siswa'] = $this->siswa
                ->where('kelas_id', $kelas_id)
                ->orderBy('nama_siswa', 'ASC')
                ->findAll();

            $hasil = $this->absensiSholat
                ->where('tanggal', $tanggal)
                ->where('kelas_id', $kelas_id)
                ->where('jenis_sholat', $jenis_sholat)
                ->findAll();

            foreach ($hasil as $h) {
                $data['absensi'][$h['siswa_id']] = $h['status'];
            }
        }

        return view('absensi_sholat/index', $data);
    }

    public function simpan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $kelas_id = $this->kelasTerkunci($this->request->getPost('kelas_id'));

        $data = [
            'tanggal'            => $this->tanggalTerkunci($this->request->getPost('tanggal')),
            'tahun_pelajaran_id' => $this->request->getPost('tahun_pelajaran_id'),
            'semester_id'        => $this->request->getPost('semester_id'),
            'kelas_id'           => $kelas_id,
            'jenis_sholat'       => $this->request->getPost('jenis_sholat'),
            'guru_id'            => session()->get('id'),
            'siswa_id'           => $this->request->getPost('siswa_id'),
            'status'             => $this->request->getPost('status'),
        ];

        if (session()->get('role_id') == 5) {
            $cekSiswa = $this->siswa
                ->where('id', $data['siswa_id'])
                ->where('kelas_id', $kelas_id)
                ->first();

            if (!$cekSiswa) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Siswa tidak berada di kelas Anda.'
                ]);
            }
        }

        $cek = $this->absensiSholat
            ->where('tanggal', $data['tanggal'])
            ->where('kelas_id', $data['kelas_id'])
            ->where('jenis_sholat', $data['jenis_sholat'])
            ->where('siswa_id', $data['siswa_id'])
            ->first();

        if ($cek) {
            $this->absensiSholat->update($cek['id'], $data);
        } else {
            $this->absensiSholat->insert($data);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi sholat berhasil disimpan.'
        ]);
    }

    public function simpanMassal()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $kelas_id = $this->kelasTerkunci($this->request->getPost('kelas_id'));

        if (session()->get('role_id') == 5) {
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

        $tanggal = $this->tanggalTerkunci($this->request->getPost('tanggal'));

        foreach ($this->request->getPost('absensi') as $row) {

            $save = [
                'tanggal'            => $tanggal,
                'tahun_pelajaran_id' => $this->request->getPost('tahun_pelajaran_id'),
                'semester_id'        => $this->request->getPost('semester_id'),
                'kelas_id'           => $kelas_id,
                'jenis_sholat'       => $this->request->getPost('jenis_sholat'),
                'guru_id'            => session()->get('id'),
                'siswa_id'           => $row['siswa_id'],
                'status'             => $row['status'],
            ];

            $cek = $this->absensiSholat
                ->where('tanggal', $save['tanggal'])
                ->where('kelas_id', $save['kelas_id'])
                ->where('jenis_sholat', $save['jenis_sholat'])
                ->where('siswa_id', $save['siswa_id'])
                ->first();

            if ($cek) {
                $this->absensiSholat->update($cek['id'], $save);
            } else {
                $this->absensiSholat->insert($save);
            }
        }

        $db->transComplete();

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Absensi sholat massal berhasil.'
        ]);
    }

    /*
    =====================================
    REKAP BULANAN ABSENSI SHOLAT (Excel)
    Per kelas, per bulan, dipecah per
    waktu sholat (Dhuha/Zuhur/Ashar).
    =====================================
    */

    public function rekapBulanan()
    {
        if (session()->get('role_id') == 5) {
            return redirect()->to('/absensi-sholat');
        }

        $data = [
            'kelas' => $this->kelas->findAll(),
        ];

        return view('absensi_sholat/rekap_bulanan', $data);
    }

    public function exportRekapBulanan()
    {
        if (session()->get('role_id') == 5) {
            return redirect()->to('/absensi-sholat');
        }

        $kelas_id = $this->request->getGet('kelas_id');
        $periode  = $this->request->getGet('periode'); // format: YYYY-MM

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

        $jenisList = ['dhuha' => 'Dhuha', 'zuhur' => 'Zuhur', 'ashar' => 'Ashar'];

        // Hari aktif per jenis sholat (jumlah tanggal unik yang direkam)
        $hariAktif = [];
        foreach ($jenisList as $key => $label) {
            $rows = $db->table('absensi_sholat')
                ->select('tanggal')
                ->where('kelas_id', $kelas_id)
                ->where('jenis_sholat', $key)
                ->where('tanggal >=', $tanggalAwal)
                ->where('tanggal <=', $tanggalAkhir)
                ->groupBy('tanggal')
                ->get()
                ->getResultArray();

            $hariAktif[$key] = count($rows);
        }

        // Semua record absensi_sholat kelas ini dalam periode
        $semuaData = $db->table('absensi_sholat')
            ->select('siswa_id, jenis_sholat, status')
            ->where('kelas_id', $kelas_id)
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir)
            ->get()
            ->getResultArray();

        // rekap[siswa_id][jenis][status] = jumlah
        $rekap = [];
        foreach ($semuaData as $row) {
            $sid = $row['siswa_id'];
            $jenis = $row['jenis_sholat'];
            $status = $row['status'];

            if (!isset($rekap[$sid][$jenis][$status])) {
                $rekap[$sid][$jenis][$status] = 0;
            }
            $rekap[$sid][$jenis][$status]++;
        }

        // ==========================
        // Bangun file Excel
        // ==========================

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Sholat');

        $warnaJenis = [
            'dhuha' => 'FFF2CC',
            'zuhur' => 'DDEBF7',
            'ashar' => 'E2EFDA',
        ];

        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'Rekap Bulanan Absensi Sholat - ' . $kelasInfo['nama_kelas']);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $baris1 = 2;
        $baris2 = 3;

        $sheet->mergeCells("A{$baris1}:A{$baris2}");
        $sheet->setCellValue("A{$baris1}", 'No');

        $sheet->mergeCells("B{$baris1}:B{$baris2}");
        $sheet->setCellValue("B{$baris1}", 'Nama Siswa');

        $kolom = 3;

        foreach ($jenisList as $key => $label) {

            $colStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
            $colEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);

            $sheet->mergeCells("{$colStart}{$baris1}:{$colEnd}{$baris1}");
            $sheet->setCellValue("{$colStart}{$baris1}", $label . " (aktif: {$hariAktif[$key]})");

            $sheet->getStyle("{$colStart}{$baris1}:{$colEnd}{$baris2}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($warnaJenis[$key]);

            $sheet->setCellValue($colStart . $baris2, 'Sholat');
            $sheet->setCellValue($colEnd . $baris2, 'Tidak');

            $kolom += 2;
        }

        // Grup Total
        $colTotalStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
        $colTotalEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2);

        $sheet->mergeCells("{$colTotalStart}{$baris1}:{$colTotalEnd}{$baris1}");
        $sheet->setCellValue("{$colTotalStart}{$baris1}", 'Total');

        $sheet->setCellValue($colTotalStart . $baris2, 'Sholat');
        $sheet->setCellValue(
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1) . $baris2,
            'Tidak'
        );
        $sheet->setCellValue($colTotalEnd . $baris2, 'Persentase %');

        $sheet->getStyle("A{$baris1}:{$colTotalEnd}{$baris2}")->getFont()->setBold(true);
        $sheet->getStyle("A{$baris1}:{$colTotalEnd}{$baris2}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $totalHariAktifSemua = array_sum($hariAktif);

        $baris = $baris2 + 1;
        $no = 1;

        foreach ($daftarSiswa as $s) {

            $sid = $s['id'];

            $sheet->setCellValue("A{$baris}", $no++);
            $sheet->setCellValue("B{$baris}", $s['nama_siswa']);

            $kolom = 3;
            $totalSholat = 0;
            $totalTidak  = 0;

            foreach ($jenisList as $key => $label) {

                $sholat = $rekap[$sid][$key]['S'] ?? 0;
                $tidak  = $rekap[$sid][$key]['T'] ?? 0;

                $colS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
                $colT = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);

                if ($sholat > 0) $sheet->setCellValue($colS . $baris, $sholat);
                if ($tidak > 0)  $sheet->setCellValue($colT . $baris, $tidak);

                $totalSholat += $sholat;
                $totalTidak  += $tidak;

                $kolom += 2;
            }

            $colTS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom);
            $colTT = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 1);
            $colTP = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($kolom + 2);

            $sheet->setCellValue($colTS . $baris, $totalSholat);
            $sheet->setCellValue($colTT . $baris, $totalTidak);

            $persen = $totalHariAktifSemua > 0 ? round(($totalSholat / $totalHariAktifSemua) * 100) : 0;
            $sheet->setCellValue($colTP . $baris, $persen . '%');

            $baris++;
        }

        // Lebar kolom otomatis
        foreach (range('A', $colTP) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaFile = 'Rekap-Sholat-' . ($kelasInfo['nama_kelas'] ?? '') . '-' . $periode . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }
}
