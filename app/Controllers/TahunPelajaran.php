<?php

namespace App\Controllers;

use App\Models\TahunPelajaranModel;

class TahunPelajaran extends BaseController
{
    protected $tahunPelajaran;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->tahunPelajaran = new TahunPelajaranModel();
    }

    public function index()
    {
        $data['tahun'] = $this->tahunPelajaran
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('tahun_pelajaran/index', $data);
    }

    public function create()
    {
        return view('tahun_pelajaran/create');
    }

    public function store()
    {
        $this->tahunPelajaran->save([
            'tahun' => $this->request->getPost('tahun'),
            'aktif' => 'N'
        ]);

        return redirect()->to('/tahun')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['tahun'] = $this->tahunPelajaran->find($id);

        if (!$data['tahun']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('tahun_pelajaran/edit', $data);
    }

    public function update($id)
    {
        $this->tahunPelajaran->update($id, [
            'tahun' => $this->request->getPost('tahun')
        ]);

        return redirect()->to('/tahun')
            ->with('success', 'Data berhasil diperbarui');
    }

   public function aktif($id)
{
    // Nonaktifkan semua
    $this->tahunPelajaran->db
        ->table('tahun_pelajaran')
        ->update(['aktif' => 'N']);

    // Aktifkan yang dipilih
    $this->tahunPelajaran->update($id, [
        'aktif' => 'Y'
    ]);

    return redirect()->to('/tahun')
                     ->with('success', 'Tahun Pelajaran aktif berhasil diubah');
}

    public function delete($id)
    {
        $this->tahunPelajaran->delete($id);

        return redirect()->to('/tahun')
            ->with('success', 'Data berhasil dihapus');
    }
}