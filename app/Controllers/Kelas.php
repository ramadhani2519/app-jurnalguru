<?php

namespace App\Controllers;

use App\Models\KelasModel;
use App\Models\JadwalModel;

class Kelas extends BaseController
{
    protected $kelas;
    protected $jadwal;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->kelas  = new KelasModel();
        $this->jadwal = new JadwalModel();
    }

    public function index()
    {
        $data['kelas'] = $this->kelas->findAll();

        // Ambil daftar guru (unik) yang mengajar tiap kelas, dari data
        // Jadwal Pelajaran, untuk tahun pelajaran & semester yang aktif.
        $jadwalRows = $this->jadwal
            ->select('jadwal.kelas_id, users.nama as nama_guru')
            ->join('users', 'users.id = jadwal.guru_id')
            ->where('jadwal.tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('jadwal.semester_id', $this->semesterAktif['id'] ?? null)
            ->findAll();

        $guruPerKelas = [];
        foreach ($jadwalRows as $row) {
            $guruPerKelas[$row['kelas_id']][$row['nama_guru']] = true;
        }

        foreach ($data['kelas'] as &$k) {
            $k['daftar_guru'] = isset($guruPerKelas[$k['id']])
                ? array_keys($guruPerKelas[$k['id']])
                : [];
        }
        unset($k);

        return view('kelas/index', $data);
    }

    public function create()
    {
        $data['jurusanList'] = (new \App\Models\JurusanModel())
            ->orderBy('nama_jurusan', 'ASC')
            ->findAll();

        return view('kelas/create', $data);
    }

    public function store()
    {
        $this->kelas->save([
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'jurusan'    => $this->request->getPost('jurusan'),
        ]);

        return redirect()->to('/kelas');
    }

    public function edit($id)
    {
        $data['kelas'] = $this->kelas->find($id);

        $data['jurusanList'] = (new \App\Models\JurusanModel())
            ->orderBy('nama_jurusan', 'ASC')
            ->findAll();

        return view('kelas/edit', $data);
    }

    public function update($id)
    {
        $this->kelas->update($id, [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'jurusan'    => $this->request->getPost('jurusan'),
        ]);

        return redirect()->to('/kelas');
    }

    public function delete($id)
    {
        $this->kelas->delete($id);

        return redirect()->to('/kelas');
    }
}