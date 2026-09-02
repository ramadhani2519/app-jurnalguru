<?php

namespace App\Controllers;

use App\Models\JadwalModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\HariModel;
use App\Models\JamModel;
use App\Models\RuanganModel;

class Jadwal extends BaseController
{
    protected $jadwal;
    protected $guru;
    protected $kelas;
    protected $mapel;
    protected $hari;
    protected $jam;
    protected $ruangan;

    public function __construct()
    {
        $this->jadwal   = new JadwalModel();
        $this->guru     = new GuruModel();
        $this->kelas    = new KelasModel();
        $this->mapel    = new MapelModel();
        $this->hari     = new HariModel();
        $this->jam      = new JamModel();
        $this->ruangan  = new RuanganModel();
    }

    /*
    =====================================
    INDEX
    =====================================
    */

    public function index()
    {
        $data = [

            'jadwal' => $this->jadwal
                ->select('jadwal.*,
                        kelas.nama_kelas,
                        mata_pelajaran.nama_mapel,
                        users.nama,
                        hari.nama_hari,
                        jam_pelajaran.jam_ke,
                        jam_pelajaran.jam_mulai,
                        jam_pelajaran.jam_selesai,
                        ruangan.nama_ruang')
                ->join('kelas','kelas.id=jadwal.kelas_id')
                ->join('mata_pelajaran','mata_pelajaran.id=jadwal.mapel_id')
                ->join('users','users.id=jadwal.guru_id')
                ->join('hari','hari.id=jadwal.hari_id')
                ->join('jam_pelajaran','jam_pelajaran.id=jadwal.jam_id')
                ->join('ruangan','ruangan.id=jadwal.ruangan_id','left')
                ->orderBy('hari.id')
                ->orderBy('jam_pelajaran.id')
                ->findAll()

        ];

        return view('jadwal/index',$data);
    }

    /*
    =====================================
    CREATE
    =====================================
    */

    public function create()
    {

        $data=[

            'tahun'=>$this->tahunAktif,

            'semester'=>$this->semesterAktif,

            'kelas'=>$this->kelas->findAll(),

            'guru'=>$this->guru->getGuru()->findAll(),

            'mapel'=>$this->mapel->findAll(),

            'hari'=>$this->hari->findAll(),

            'jam'=>$this->jam->orderBy('jam_ke','ASC')->findAll(),

            'ruangan'=>$this->ruangan->findAll()

        ];

        return view('jadwal/create',$data);

    }

    /*
    =====================================
    STORE
    Mendukung guru mengajar beberapa jam
    sekaligus dalam 1 sesi (mis. Jam 1 s/d
    Jam 4), dengan cara membuat beberapa
    baris jadwal otomatis, 1 baris per jam.
    =====================================
    */

    public function store()
    {

        $tahun_pelajaran_id = $this->request->getPost('tahun');
        $semester_id        = $this->request->getPost('semester');
        $kelas_id           = $this->request->getPost('kelas');
        $hari_id            = $this->request->getPost('hari');
        $mapel_id           = $this->request->getPost('mapel');
        $guru_id            = $this->request->getPost('guru');
        $ruangan_id         = $this->request->getPost('ruangan');

        $jamMulaiId   = $this->request->getPost('jam_mulai');
        $jamSelesaiId = $this->request->getPost('jam_selesai');

        $jamMulaiInfo   = $this->jam->find($jamMulaiId);
        $jamSelesaiInfo = $this->jam->find($jamSelesaiId);

        if (!$jamMulaiInfo || !$jamSelesaiInfo) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jam mulai / jam selesai tidak valid.');
        }

        if ((int) $jamSelesaiInfo['jam_ke'] < (int) $jamMulaiInfo['jam_ke']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jam Selesai tidak boleh lebih awal dari Jam Mulai.');
        }

        // Ambil semua jam pelajaran di antara Jam Mulai s/d Jam Selesai (inklusif)
        $daftarJam = $this->jam
            ->where('jam_ke >=', $jamMulaiInfo['jam_ke'])
            ->where('jam_ke <=', $jamSelesaiInfo['jam_ke'])
            ->orderBy('jam_ke', 'ASC')
            ->findAll();

        if (empty($daftarJam)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Rentang jam tidak ditemukan.');
        }

        /*
        =====================================
        CEK BENTROK UNTUK SETIAP JAM DALAM
        RENTANG, SEBELUM MENYIMPAN APAPUN
        =====================================
        */

        foreach ($daftarJam as $j) {

            $jam_id = $j['id'];

            // Guru bentrok
            $cekGuru = $this->jadwal
                ->where('hari_id', $hari_id)
                ->where('jam_id', $jam_id)
                ->where('guru_id', $guru_id)
                ->first();

            if ($cekGuru) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Guru sudah mengajar pada Jam {$j['jam_ke']}.");
            }

            // Kelas bentrok
            $cekKelas = $this->jadwal
                ->where('hari_id', $hari_id)
                ->where('jam_id', $jam_id)
                ->where('kelas_id', $kelas_id)
                ->first();

            if ($cekKelas) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Kelas sudah memiliki jadwal pada Jam {$j['jam_ke']}.");
            }

            // Ruangan bentrok
            if ($ruangan_id != "") {

                $cekRuangan = $this->jadwal
                    ->where('hari_id', $hari_id)
                    ->where('jam_id', $jam_id)
                    ->where('ruangan_id', $ruangan_id)
                    ->first();

                if ($cekRuangan) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Ruangan sedang dipakai pada Jam {$j['jam_ke']}.");
                }
            }

            // Mapel ganda
            $cekMapel = $this->jadwal
                ->where('hari_id', $hari_id)
                ->where('jam_id', $jam_id)
                ->where('kelas_id', $kelas_id)
                ->where('mapel_id', $mapel_id)
                ->first();

            if ($cekMapel) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Mapel sudah ada pada Jam {$j['jam_ke']}.");
            }
        }

        /*
        =====================================
        SEMUA AMAN, SIMPAN SEMUA JAM SEKALIGUS
        =====================================
        */

        $db = \Config\Database::connect();
        $db->transStart();

        $gagal = [];

        foreach ($daftarJam as $j) {

            $ok = $this->jadwal->insert([
                'tahun_pelajaran_id' => $tahun_pelajaran_id,
                'semester_id'        => $semester_id,
                'kelas_id'           => $kelas_id,
                'hari_id'            => $hari_id,
                'jam_id'             => $j['id'],
                'mapel_id'           => $mapel_id,
                'guru_id'            => $guru_id,
                'ruangan_id'         => $ruangan_id ?: null,
            ]);

            // insert() CI4 mengembalikan primary key baru (truthy) kalau
            // berhasil, atau false/0 kalau gagal. Kalau ada yang gagal,
            // catat pesan errornya supaya tidak silently dianggap sukses.
            if (!$ok) {

                $dbError = $db->error();

                $gagal[] = "Jam {$j['jam_ke']}: " . ($dbError['message'] ?? 'gagal disimpan (tidak diketahui sebabnya)');
            }
        }

        // Kalau ADA yang gagal, batalkan semua (rollback) dan kasih tahu
        // pesan error yang sebenarnya, bukan pesan sukses palsu.
        if (!empty($gagal)) {

            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Sebagian jadwal GAGAL disimpan: ' . implode(' | ', $gagal));
        }

        $db->transComplete();

        if ($db->transStatus() === false) {

            return redirect()->back()
                ->withInput()
                ->with('error', 'Transaksi database gagal, tidak ada jadwal yang tersimpan. Coba lagi.');
        }

        $jumlahJam = count($daftarJam);

        return redirect()->to('jadwal')
            ->with('success', "Jadwal berhasil disimpan ({$jumlahJam} jam pelajaran).");

    }

    /*
    =====================================
    EDIT
    =====================================
    */

    public function edit($id)
    {

        $data=[

            'jadwal'=>$this->jadwal->find($id),

            'tahun'=>$this->tahunAktif,

            'semester'=>$this->semesterAktif,

            'kelas'=>$this->kelas->findAll(),

            'guru'=>$this->guru->getGuru()->findAll(),

            'mapel'=>$this->mapel->findAll(),

            'hari'=>$this->hari->findAll(),

            'jam'=>$this->jam->orderBy('jam_ke','ASC')->findAll(),

            'ruangan'=>$this->ruangan->findAll()

        ];

        return view('jadwal/edit',$data);

    }

    /*
    =====================================
    UPDATE
    (tetap 1 jam per baris, karena edit
    dilakukan per baris jadwal yang sudah
    ada)
    =====================================
    */

    public function update($id)
    {

        $data=[

            'tahun_pelajaran_id'=>$this->request->getPost('tahun'),

            'semester_id'=>$this->request->getPost('semester'),

            'kelas_id'=>$this->request->getPost('kelas'),

            'hari_id'=>$this->request->getPost('hari'),

            'jam_id'=>$this->request->getPost('jam'),

            'mapel_id'=>$this->request->getPost('mapel'),

            'guru_id'=>$this->request->getPost('guru'),

            'ruangan_id'=>$this->request->getPost('ruangan')

        ];

        /*
        Hindari bentrok selain data sendiri
        */

        $cek=$this->jadwal

            ->where('hari_id',$data['hari_id'])

            ->where('jam_id',$data['jam_id'])

            ->where('guru_id',$data['guru_id'])

            ->where('id !=',$id)

            ->first();

        if($cek){

            return redirect()->back()

            ->withInput()

            ->with('error','Guru bentrok.');

        }

        $cek=$this->jadwal

            ->where('hari_id',$data['hari_id'])

            ->where('jam_id',$data['jam_id'])

            ->where('kelas_id',$data['kelas_id'])

            ->where('id !=',$id)

            ->first();

        if($cek){

            return redirect()->back()

            ->withInput()

            ->with('error','Kelas bentrok.');

        }

        if($data['ruangan_id']!=""){

            $cek=$this->jadwal

                ->where('hari_id',$data['hari_id'])

                ->where('jam_id',$data['jam_id'])

                ->where('ruangan_id',$data['ruangan_id'])

                ->where('id !=',$id)

                ->first();

            if($cek){

                return redirect()->back()

                ->withInput()

                ->with('error','Ruangan bentrok.');

            }

        }

        $this->jadwal->update($id,$data);

        return redirect()->to('jadwal')

        ->with('success','Jadwal berhasil diperbarui.');

    }

    /*
    =====================================
    DELETE
    =====================================
    */

    public function delete($id)
    {

        $this->jadwal->delete($id);

        return redirect()->to('jadwal')

        ->with('success','Jadwal berhasil dihapus.');

    }

}
