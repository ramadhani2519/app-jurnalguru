<?php

namespace App\Controllers;

use App\Models\WaliKelasModel;
use App\Models\KelasModel;

class WaliKelas extends BaseController
{
    protected $waliKelas;
    protected $kelas;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya Admin yang boleh kelola data ini
        if (session()->get('role_id') != 1) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->waliKelas = new WaliKelasModel();
        $this->kelas     = new KelasModel();
    }

    public function index()
    {
        $data['waliKelas'] = $this->waliKelas
            ->select('wali_kelas.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = wali_kelas.kelas_id')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();

        return view('wali_kelas/index', $data);
    }

    public function create()
    {
        $data['kelas'] = $this->kelas->findAll();

        return view('wali_kelas/create', $data);
    }

    public function store()
    {
        $this->waliKelas->insert([
            'kelas_id'  => $this->request->getPost('kelas_id'),
            'nama_wali' => $this->request->getPost('nama_wali'),
        ]);

        return redirect()->to('/wali-kelas')
            ->with('success', 'Data wali kelas berhasil disimpan.');
    }

    public function edit($id)
    {
        $data['item']  = $this->waliKelas->find($id);
        $data['kelas'] = $this->kelas->findAll();

        return view('wali_kelas/edit', $data);
    }

    public function update($id)
    {
        $this->waliKelas->update($id, [
            'kelas_id'  => $this->request->getPost('kelas_id'),
            'nama_wali' => $this->request->getPost('nama_wali'),
        ]);

        return redirect()->to('/wali-kelas')
            ->with('success', 'Data wali kelas berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->waliKelas->delete($id);

        return redirect()->to('/wali-kelas')
            ->with('success', 'Data wali kelas berhasil dihapus.');
    }
}
