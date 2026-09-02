<?php

namespace App\Controllers;

use App\Models\JurnalModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\UserModel;
use App\Models\SekolahModel;
use App\Models\JamModel;
use App\Models\JadwalModel;
use App\Models\SiswaModel;


use Dompdf\Dompdf;
use Dompdf\Options;

class Jurnal extends BaseController
{
    protected $jurnal;
    protected $kelas;
    protected $mapel;
    protected $user;
    protected $sekolah;
    protected $jam;
    protected $jadwal;
    protected $siswa;



    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->jurnal = new JurnalModel();
        $this->kelas = new KelasModel();
        $this->mapel = new MapelModel();
        $this->user = new UserModel();
        $this->sekolah = new SekolahModel();
        $this->jam = new JamModel();
        $this->jadwal = new JadwalModel();
        $this->siswa = new SiswaModel();



    }

    public function index()
    {
        $roleId = session()->get('role_id');

        // Role 1 = Admin, Role 3 = Kepala Sekolah, atau siapapun yang
        // menjabat Wakasek Kurikulum -> boleh lihat jurnal SEMUA guru
        // (semua jurusan).
        $bolehLihatSemuaAdmin = in_array($roleId, [1, 3]) || $this->hasJabatan('Wakasek Kurikulum');

        // Ketua Jurusan -> boleh lihat jurnal semua guru, tapi DIKUNCI
        // ke jurusannya sendiri saja (kalau jurusannya sudah diset admin).
        $jurusanKetua = null;
        $jurusanBelumDiset = false;

        if (!$bolehLihatSemuaAdmin && $this->hasJabatan('Ketua Jurusan')) {
            $jurusanKetua = $this->jurusanKetuaAktif();
            $jurusanBelumDiset = empty($jurusanKetua);
        }

        // Dipakai view untuk menampilkan kolom "Guru" + form filter
        // (berlaku untuk Admin/Kepsek/Wakasek Kurikulum MAUPUN Ketua
        // Jurusan yang jurusannya sudah diset).
        $bolehLihatSemua = $bolehLihatSemuaAdmin || !empty($jurusanKetua);

        $guru_id  = $this->request->getGet('guru_id');
        $mapel_id = $this->request->getGet('mapel_id');
        $kelas_id = $this->request->getGet('kelas_id');

        $builder = $this->jurnal
            ->select('jurnal.*, kelas.nama_kelas, kelas.jurusan, mata_pelajaran.nama_mapel, users.nama as nama_guru')
            ->join('kelas', 'kelas.id = jurnal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jurnal.mapel_id')
            ->join('users', 'users.id = jurnal.user_id');

        if ($bolehLihatSemuaAdmin) {
            if (!empty($guru_id)) {
                $builder->where('jurnal.user_id', $guru_id);
            }
        } elseif (!empty($jurusanKetua)) {
            // Ketua Jurusan: dikunci ke jurusannya, opsional filter per guru.
            $builder->where('UPPER(TRIM(kelas.jurusan)) =', strtoupper(trim($jurusanKetua)));

            if (!empty($guru_id)) {
                $builder->where('jurnal.user_id', $guru_id);
            }
        } else {
            // Guru biasa (atau Ketua Jurusan yang belum diset jurusannya
            // oleh admin): dikunci ke jurnalnya sendiri saja.
            $builder->where('jurnal.user_id', session()->get('id'));
        }

        if (!empty($mapel_id)) {
            $builder->where('jurnal.mapel_id', $mapel_id);
        }

        if (!empty($kelas_id)) {
            $builder->where('jurnal.kelas_id', $kelas_id);
        }

        // Daftar guru untuk dropdown filter: semua guru (Admin/Kepsek/
        // Wakasek Kurikulum) atau hanya guru yang pernah mengisi jurnal
        // di jurusan yang diampu (Ketua Jurusan).
        $guruList = [];

        if ($bolehLihatSemuaAdmin) {
            $guruList = $this->user->where('role_id', 2)->orderBy('nama', 'ASC')->findAll();
        } elseif (!empty($jurusanKetua)) {
            $db = \Config\Database::connect();

            $guruList = $db->table('jurnal')
                ->select('users.id, users.nama')
                ->join('users', 'users.id = jurnal.user_id')
                ->join('kelas', 'kelas.id = jurnal.kelas_id')
                ->where('UPPER(TRIM(kelas.jurusan)) =', strtoupper(trim($jurusanKetua)))
                ->groupBy('users.id, users.nama')
                ->orderBy('users.nama', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'jurnal' => $builder
                ->orderBy('jurnal.tanggal', 'DESC')
                ->findAll(),

            'tahunAktif'      => $this->tahunAktif,
            'semesterAktif'   => $this->semesterAktif,
            'bolehLihatSemua' => $bolehLihatSemua,
            'jurusanKetua'    => $jurusanKetua,
            'jurusanBelumDiset' => $jurusanBelumDiset,
            'guruList'        => $guruList,
            'mapelList'       => $this->mapel->orderBy('nama_mapel', 'ASC')->findAll(),
            'kelasList'       => $this->kelas->orderBy('nama_kelas', 'ASC')->findAll(),
            'guru_id'         => $guru_id,
            'mapel_id'        => $mapel_id,
            'kelas_id'        => $kelas_id,
        ];

        return view('jurnal/index', $data);
    }

    public function create()
    {
        // Peta nama hari (Inggris dari PHP) -> Indonesia (sesuai isi tabel "hari")
        $mapHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        $namaHariIni = $mapHari[date('l')] ?? '';

        // Ambil jadwal mengajar guru yang login, khusus untuk hari ini saja
        $jadwalMentah = $this->jadwal
            ->select('
                jadwal.id,
                jadwal.kelas_id,
                jadwal.mapel_id,
                kelas.nama_kelas,
                mata_pelajaran.nama_mapel,
                jam_pelajaran.jam_ke,
                jam_pelajaran.jam_mulai,
                jam_pelajaran.jam_selesai
            ')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->where('jadwal.guru_id', session()->get('id'))
            ->where('hari.nama_hari', $namaHariIni)
            ->orderBy('jam_pelajaran.jam_ke', 'ASC')
            ->findAll();

        /*
        =====================================
        Gabungkan jam-jam yang berurutan
        (kelas & mapel sama, jam_ke lanjut)
        jadi satu sesi mengajar.
        Contoh: Jam 1,2,3,4 -> "Jam 1-4"
        =====================================
        */

        $jadwalHariIni = [];

        foreach ($jadwalMentah as $row) {

            $terakhir = end($jadwalHariIni);

            if (
                $terakhir !== false &&
                $terakhir['kelas_id'] == $row['kelas_id'] &&
                $terakhir['mapel_id'] == $row['mapel_id'] &&
                (int) $row['jam_ke'] == (int) $terakhir['jam_akhir'] + 1
            ) {
                // Sambung ke sesi sebelumnya
                $idx = count($jadwalHariIni) - 1;
                $jadwalHariIni[$idx]['jam_akhir']   = $row['jam_ke'];
                $jadwalHariIni[$idx]['jam_selesai'] = $row['jam_selesai'];
            } else {
                // Mulai sesi baru
                $jadwalHariIni[] = [
                    'kelas_id'    => $row['kelas_id'],
                    'mapel_id'    => $row['mapel_id'],
                    'nama_kelas'  => $row['nama_kelas'],
                    'nama_mapel'  => $row['nama_mapel'],
                    'jam_awal'    => $row['jam_ke'],
                    'jam_akhir'   => $row['jam_ke'],
                    'jam_mulai'   => $row['jam_mulai'],
                    'jam_selesai' => $row['jam_selesai'],
                ];
            }
        }

        $data = [
            'jadwalHariIni' => $jadwalHariIni,
            'namaHariIni'   => $namaHariIni,
            'tahunAktif'    => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif
        ];

        return view('jurnal/create',$data);

    }

    public function store()
    {
    $userId  = session()->get('id');
    $kelasId = $this->request->getPost('kelas_id');
    $mapelId = $this->request->getPost('mapel_id');
    $tanggal = $this->request->getPost('tanggal');

    // Cek apakah sesi ini (guru + kelas + mapel + tanggal yang sama)
    // sudah pernah diisi, supaya tidak tercipta baris duplikat.
    $sudahAda = $this->jurnal
        ->where('user_id', $userId)
        ->where('kelas_id', $kelasId)
        ->where('mapel_id', $mapelId)
        ->where('tanggal', $tanggal)
        ->first();

    if ($sudahAda) {
        return redirect()->to('/jurnal/edit/' . $sudahAda['id'])
                         ->with('error', 'Jurnal untuk kelas & mapel ini hari ini sudah pernah diisi. Silakan edit jurnal yang sudah ada.');
    }

    // Proses upload foto bukti mengajar (wajib)
    $fotoNama = null;
    $file = $this->request->getFile('foto');

    if (!$file || !$file->isValid()) {
        return redirect()->back()
                         ->withInput()
                         ->with('error', 'Foto bukti mengajar wajib dilampirkan.');
    }

    if (!$file->hasMoved()) {

        $folderFoto = FCPATH . 'assets/img/jurnal';

        if (!is_dir($folderFoto)) {
            mkdir($folderFoto, 0777, true);
        }

        $fotoNama = $file->getRandomName();
        $file->move($folderFoto, $fotoNama);
    }

    $this->jurnal->save([
        'user_id'             => $userId,
        'tahun_pelajaran_id'  => $this->request->getPost('tahun_pelajaran_id'),
        'semester_id'         => $this->request->getPost('semester_id'),
        'kelas_id'            => $kelasId,
        'mapel_id'            => $mapelId,
        'tanggal'             => $tanggal,
        'jam_ke'              => $this->request->getPost('jam_ke'),
        'jam_mulai'           => $this->request->getPost('jam_mulai') ?: null,
        'jam_akhir'           => $this->request->getPost('jam_akhir') ?: null,
        'materi'              => $this->request->getPost('materi'),
        'keterangan'          => $this->request->getPost('keterangan'),
        'foto'                => $fotoNama,
    ]);

    return redirect()->to('/jurnal')
                     ->with('success', 'Jurnal berhasil disimpan');
    }

    public function edit($id)
    {
        $jurnal = $this->jurnal->find($id);

        if (!$jurnal) {
            return redirect()->to('/jurnal')->with('error', 'Jurnal tidak ditemukan.');
        }

        if ($jurnal['user_id'] != session()->get('id') && session()->get('role_id') != 1) {
            return redirect()->to('/jurnal')->with('error', 'Anda tidak berhak mengubah jurnal guru lain.');
        }

        $data = [
            'jurnal'         => $jurnal,
            'kelas'          => $this->kelas->findAll(),
            'mapel'          => $this->mapel->findAll(),
            'tahunAktif'     => $this->tahunAktif,
            'semesterAktif'  => $this->semesterAktif,
        ];

        return view('jurnal/edit', $data);
    }
    
    public function update($id)
{
    $jurnal = $this->jurnal->find($id);

    if (!$jurnal) {
        return redirect()->to('/jurnal')->with('error', 'Jurnal tidak ditemukan.');
    }

    if ($jurnal['user_id'] != session()->get('id') && session()->get('role_id') != 1) {
        return redirect()->to('/jurnal')->with('error', 'Anda tidak berhak mengubah jurnal guru lain.');
    }

    $this->jurnal->update($id, [
        'kelas_id'           => $this->request->getPost('kelas_id'),
        'mapel_id'           => $this->request->getPost('mapel_id'),
        'tahun_pelajaran_id' => $this->request->getPost('tahun_pelajaran_id'),
        'semester_id'        => $this->request->getPost('semester_id'),
        'tanggal'            => $this->request->getPost('tanggal'),
        'jam_ke'             => $this->request->getPost('jam_ke'),
        'materi'             => $this->request->getPost('materi'),
        'keterangan'         => $this->request->getPost('keterangan'),
    ]);

    return redirect()->to('/jurnal')
                     ->with('success', 'Jurnal berhasil diperbarui');
    }

    public function laporan()
    {
    $roleId = session()->get('role_id');
    $userId = session()->get('id');

    $data = [
        'kelas'         => $this->kelas->findAll(),
        'mapel'         => $this->mapel->findAll(),
        'sekolah'       => $this->sekolah->first(),
        'tahunAktif'    => $this->tahunAktif,
        'semesterAktif' => $this->semesterAktif,
        'guru_id'       => '',
        'role_id'       => $roleId,
        'jam'           => $this->jam->findAll(), // Model Jam
        'siswaList'     => $this->siswa->orderBy('nama_siswa', 'ASC')->findAll(),
        'guruPembinaList' => $this->user->where('role_id', 2)->orderBy('nama', 'ASC')->findAll(),
    ];

    if ($roleId == 2) {
        // Guru hanya melihat dirinya sendiri
        $data['user'] = [
            $this->user->find($userId)
        ];

        $data['guru_id'] = $userId;
    } else {
        // Admin / Kepala Sekolah melihat semua guru
        $data['user'] = $this->user
            ->where('role_id', 2)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    return view('jurnal/laporan', $data);
    }
    
    public function cetakPdf()
    {
        $roleId  = session()->get('role_id');
        $userId  = session()->get('id');

        $tgl1    = $this->request->getGet('tgl1');
        $tgl2    = $this->request->getGet('tgl2');
        $jam_ke    = $this->request->getGet('jam_ke');

        $guru_id = $this->request->getGet('guru_id');

        $builder = $this->jurnal
            ->select("
                jurnal.*,
                users.nama,
                users.nip,
                kelas.nama_kelas,
                mata_pelajaran.nama_mapel
            ")
            ->join('users', 'users.id = jurnal.user_id')
            ->join('kelas', 'kelas.id = jurnal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jurnal.mapel_id');

        /*
        |--------------------------------------------------------
        | Hak Akses
        |--------------------------------------------------------
        */

        if ($roleId == 2) {

            // Guru hanya dapat melihat jurnal miliknya sendiri
            $builder->where('jurnal.user_id', $userId);

            $guru = $this->user->find($userId);

        } else {

            // Admin / Kepala Sekolah

            if (!empty($guru_id)) {
                $builder->where('jurnal.user_id', $guru_id);

                $guru = $this->user
                    ->select('nama,nip')
                    ->find($guru_id);
            } else {
                $guru = null;
            }
        }

        /*
        |--------------------------------------------------------
        | Filter Tanggal
        |--------------------------------------------------------
        */

        if (!empty($tgl1)) {
            $builder->where('jurnal.tanggal >=', $tgl1);
        }

        if (!empty($tgl2)) {
            $builder->where('jurnal.tanggal <=', $tgl2);
        }

        if (!empty($jam_ke)) {
            $builder->where('jurnal.jam_ke', $jam_ke);
        }
        /*
        |--------------------------------------------------------
        | Data PDF
        |--------------------------------------------------------
        */

        $jurnal = $builder
            ->orderBy('tanggal', 'ASC')
            ->orderBy('jam_ke', 'ASC')
            ->findAll();

            $sekolah = $this->sekolah->first();

            $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
            $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'guru'               => $guru,
            'guru_id'            => $guru_id,
            'tgl1'               => $tgl1,
            'tgl2'               => $tgl2,
            'jam_ke'             => $jam_ke,
            'jurnal'             => $jurnal,
            'jumlahJurnal'       => count($jurnal),
            'tahunAktif'         => $this->tahunAktif,
            'semesterAktif'      => $this->semesterAktif,
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,

            
        ];

        $html = view('jurnal/pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');

        $canvas->page_text(
            760,
            565,
            "Halaman {PAGE_NUM} / {PAGE_COUNT}",
            $font,
            9,
            [0,0,0]
        );

        while (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf->stream(
            'Laporan-Jurnal-Mengajar.pdf',
            ['Attachment' => false]
        );
        exit;
    }

    public function delete($id)
    {
        $jurnal = $this->jurnal->find($id);

        if (!$jurnal) {
            return redirect()->to('/jurnal')->with('error', 'Jurnal tidak ditemukan.');
        }

        if ($jurnal['user_id'] != session()->get('id') && session()->get('role_id') != 1) {
            return redirect()->to('/jurnal')->with('error', 'Anda tidak berhak menghapus jurnal guru lain.');
        }

        $this->jurnal->delete($id);

        return redirect()->to('/jurnal')->with('success', 'Jurnal berhasil dihapus.');
    }

    /**
     * Jurusan yang menjadi tanggung jawab guru yang sedang login,
     * kalau dia menjabat "Ketua Jurusan". Null kalau bukan / belum
     * diset jurusannya oleh admin.
     */
    private function jurusanKetuaAktif(): ?string
    {
        $db = \Config\Database::connect();

        $row = $db->table('user_jabatan')
            ->select('user_jabatan.jurusan')
            ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
            ->where('user_jabatan.user_id', session()->get('id'))
            ->where('jabatan.nama_jabatan', 'Ketua Jurusan')
            ->where('user_jabatan.jurusan IS NOT NULL')
            ->get()
            ->getRowArray();

        return $row['jurusan'] ?? null;
    }

    /**
     * Unduh Data Jurnal (Excel), dengan cakupan data sesuai jabatan:
     * - Admin / Kepala Sekolah / Wakasek Kurikulum -> semua jurnal (semua jurusan).
     * - Ketua Jurusan -> hanya jurnal di kelas-kelas jurusannya sendiri.
     * - Guru biasa -> hanya jurnalnya sendiri.
     * Filter guru_id/mapel_id/kelas_id dari halaman index (kalau ada) tetap dihormati.
     */
    public function exportExcel()
    {
        $roleId = session()->get('role_id');

        $bolehLihatSemua = in_array($roleId, [1, 3]) || $this->hasJabatan('Wakasek Kurikulum');
        $jurusanKetua     = $bolehLihatSemua ? null : $this->jurusanKetuaAktif();

        $guru_id  = $this->request->getGet('guru_id');
        $mapel_id = $this->request->getGet('mapel_id');
        $kelas_id = $this->request->getGet('kelas_id');

        $builder = $this->jurnal
            ->select('
                jurnal.*,
                kelas.nama_kelas,
                kelas.jurusan,
                mata_pelajaran.nama_mapel,
                users.nama as nama_guru
            ')
            ->join('kelas', 'kelas.id = jurnal.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jurnal.mapel_id')
            ->join('users', 'users.id = jurnal.user_id');

        if ($bolehLihatSemua) {
            // Admin / Kepsek / Wakasek Kurikulum: boleh lihat semua guru,
            // opsional filter per guru dari halaman index.
            if (!empty($guru_id)) {
                $builder->where('jurnal.user_id', $guru_id);
            }
        } elseif ($jurusanKetua) {
            // Ketua Jurusan: dikunci ke jurusannya sendiri saja.
            $builder->where('UPPER(TRIM(kelas.jurusan)) =', strtoupper(trim($jurusanKetua)));
        } else {
            // Guru biasa (atau Ketua Jurusan yang belum diset jurusannya): jurnal sendiri saja.
            $builder->where('jurnal.user_id', session()->get('id'));
        }

        if (!empty($mapel_id)) {
            $builder->where('jurnal.mapel_id', $mapel_id);
        }

        if (!empty($kelas_id)) {
            $builder->where('jurnal.kelas_id', $kelas_id);
        }

        $jurnal = $builder->orderBy('jurnal.tanggal', 'DESC')->findAll();

        $sekolah = $this->sekolah->first();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jurnal Mengajar');

        $sheet->setCellValue('A1', 'DATA JURNAL MENGAJAR GURU');
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A2', $sekolah['nama_sekolah'] ?? '');
        $sheet->mergeCells('A2:I2');

        foreach (['A1', 'A2'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize($cell === 'A1' ? 14 : 11);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $keteranganCakupan = 'Semua Jurusan';
        if (!$bolehLihatSemua) {
            $keteranganCakupan = $jurusanKetua ? ('Jurusan ' . $jurusanKetua) : 'Jurnal Saya Sendiri';
        }

        $sheet->setCellValue('A4', 'Cakupan Data');
        $sheet->setCellValue('C4', ': ' . $keteranganCakupan);
        $sheet->setCellValue('A5', 'Tahun Pelajaran');
        $sheet->setCellValue('C5', ': ' . ($this->tahunAktif['tahun'] ?? '-'));
        $sheet->setCellValue('A6', 'Semester');
        $sheet->setCellValue('C6', ': ' . ($this->semesterAktif['semester'] ?? '-'));
        $sheet->setCellValue('A7', 'Dicetak Oleh');
        $sheet->setCellValue('C7', ': ' . session()->get('nama') . ' pada ' . date('d/m/Y H:i'));

        $barisHeader = 9;
        $headerKolom = ['No', 'Tanggal', 'Guru', 'Kelas', 'Jurusan', 'Mata Pelajaran', 'Jam Ke', 'Materi', 'Keterangan'];

        foreach ($headerKolom as $i => $judul) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $barisHeader, $judul);
        }

        $sheet->getStyle("A{$barisHeader}:I{$barisHeader}")->getFont()->setBold(true);
        $sheet->getStyle("A{$barisHeader}:I{$barisHeader}")
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        $sheet->getStyle("A{$barisHeader}:I{$barisHeader}")
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $baris = $barisHeader + 1;
        $no = 1;

        foreach ($jurnal as $row) {
            $sheet->setCellValue("A{$baris}", $no++);
            $sheet->setCellValue("B{$baris}", date('d/m/Y', strtotime($row['tanggal'])));
            $sheet->setCellValue("C{$baris}", $row['nama_guru']);
            $sheet->setCellValue("D{$baris}", $row['nama_kelas']);
            $sheet->setCellValue("E{$baris}", $row['jurusan']);
            $sheet->setCellValue("F{$baris}", $row['nama_mapel']);
            $sheet->setCellValue("G{$baris}", $row['jam_ke']);
            $sheet->setCellValue("H{$baris}", $row['materi']);
            $sheet->setCellValue("I{$baris}", $row['keterangan']);
            $baris++;
        }

        if ($baris > $barisHeader + 1) {
            $sheet->getStyle("A{$barisHeader}:I" . ($baris - 1))
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'H', 'I'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaFile = 'Jurnal-Mengajar-' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    /**
     * Cetak PDF laporan Pembinaan Siswa (dari ketiga tingkat: Guru
     * Wali, Wali Kelas, Ketua Jurusan sekaligus), dengan filter guru
     * yang membina, siswa yang dibina, dan rentang tanggal.
     */
    public function cetakPembinaanPdf()
    {
        $guruId  = $this->request->getGet('guru_id');
        $siswaId = $this->request->getGet('siswa_id');
        $tgl1    = $this->request->getGet('tgl1');
        $tgl2    = $this->request->getGet('tgl2');

        $db = \Config\Database::connect();

        $builder = $db->table('pembinaan_siswa')
            ->select('
                pembinaan_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                gw.nama as nama_guru_wali,
                wk.nama as nama_wali_kelas,
                kj.nama as nama_ketua_jurusan
            ')
            ->join('siswa', 'siswa.id = pembinaan_siswa.siswa_id', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users gw', 'gw.id = pembinaan_siswa.guru_wali_id', 'left')
            ->join('users wk', 'wk.id = pembinaan_siswa.wali_kelas_id', 'left')
            ->join('users kj', 'kj.id = pembinaan_siswa.ketua_jurusan_id', 'left');

        if (!empty($guruId)) {
            $builder->groupStart()
                ->where('pembinaan_siswa.guru_wali_id', $guruId)
                ->orWhere('pembinaan_siswa.wali_kelas_id', $guruId)
                ->orWhere('pembinaan_siswa.ketua_jurusan_id', $guruId)
                ->groupEnd();
        }

        if (!empty($siswaId)) {
            $builder->where('pembinaan_siswa.siswa_id', $siswaId);
        }

        if (!empty($tgl1)) {
            $builder->where('pembinaan_siswa.tanggal >=', $tgl1);
        }

        if (!empty($tgl2)) {
            $builder->where('pembinaan_siswa.tanggal <=', $tgl2);
        }

        $riwayat = $builder
            ->orderBy('pembinaan_siswa.tanggal', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($riwayat as &$r) {
            switch ($r['tingkat']) {
                case 'wali_kelas':
                    $r['tingkat_label'] = 'Wali Kelas';
                    $r['nama_penindak'] = $r['nama_wali_kelas'] ?? '-';
                    break;
                case 'ketua_jurusan':
                    $r['tingkat_label'] = 'Ketua Jurusan';
                    $r['nama_penindak'] = $r['nama_ketua_jurusan'] ?? '-';
                    break;
                default:
                    $r['tingkat_label'] = 'Guru Wali';
                    $r['nama_penindak'] = $r['nama_guru_wali'] ?? '-';
            }
        }
        unset($r);

        $namaGuruFilter  = !empty($guruId) ? ($this->user->find($guruId)['nama'] ?? '-') : '';
        $namaSiswaFilter = !empty($siswaId) ? ($this->siswa->find($siswaId)['nama_siswa'] ?? '-') : '';

        $sekolah = $this->sekolah->first();

        $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data = [
            'riwayat'            => $riwayat,
            'jumlah'             => count($riwayat),
            'namaGuruFilter'     => $namaGuruFilter,
            'namaSiswaFilter'    => $namaSiswaFilter,
            'tgl1'               => $tgl1,
            'tgl2'               => $tgl2,
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64,
        ];

        $html = view('jurnal/pembinaan_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font   = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');

        $canvas->page_text(760, 565, "Halaman {PAGE_NUM} / {PAGE_COUNT}", $font, 9, [0, 0, 0]);

        while (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf->stream('Laporan-Pembinaan-Siswa.pdf', ['Attachment' => false]);

        exit;
    }
}
