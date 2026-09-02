<?php

namespace App\Controllers;

use App\Models\KetuaKompetensiModel;

class KetuaKompetensi extends BaseController
{
    protected $ketuaKompetensi;

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

        $this->ketuaKompetensi = new KetuaKompetensiModel();
    }

    public function index()
    {
        $data['ketuaKompetensi'] = $this->ketuaKompetensi
            ->orderBy('nama_kompetensi', 'ASC')
            ->findAll();

        return view('ketua_kompetensi/index', $data);
    }

    public function create()
    {
        return view('ketua_kompetensi/create');
    }

    public function store()
    {
        $this->ketuaKompetensi->insert([
            'nama_kompetensi' => $this->request->getPost('nama_kompetensi'),
            'nama_ketua'      => $this->request->getPost('nama_ketua'),
        ]);

        return redirect()->to('/ketua-kompetensi')
            ->with('success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $data['item'] = $this->ketuaKompetensi->find($id);

        return view('ketua_kompetensi/edit', $data);
    }

    public function update($id)
    {
        $this->ketuaKompetensi->update($id, [
            'nama_kompetensi' => $this->request->getPost('nama_kompetensi'),
            'nama_ketua'      => $this->request->getPost('nama_ketua'),
        ]);

        return redirect()->to('/ketua-kompetensi')
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->ketuaKompetensi->delete($id);

        return redirect()->to('/ketua-kompetensi')
            ->with('success', 'Data berhasil dihapus.');
    }
}
