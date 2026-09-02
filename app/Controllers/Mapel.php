<?php

namespace App\Controllers;

use App\Models\MapelModel;

class Mapel extends BaseController
{
    protected $mapel;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->mapel = new MapelModel();
    }

    public function index()
    {
        $data['mapel'] = $this->mapel
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('mapel/index', $data);
    }

    public function create()
    {
        return view('mapel/create');
    }

    public function store()
    {
        $this->mapel->save([
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel')
            
        ]);

        return redirect()->to('/mapel')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['mapel'] = $this->mapel->find($id);

        if (!$data['mapel']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('mapel/edit', $data);
    }

    public function update($id)
    {
        $this->mapel->update($id, [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel')

        ]);

        return redirect()->to('/mapel')
            ->with('success', 'Data berhasil diperbarui');
    }

   

    public function delete($id)
    {
        $this->mapel->delete($id);

        return redirect()->to('/mapel')
            ->with('success', 'Data berhasil dihapus');
    }
}