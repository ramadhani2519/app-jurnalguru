<?php

namespace App\Controllers;

use App\Models\SemesterModel;

class Semester extends BaseController
{
    protected $semester;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->semester = new SemesterModel();
    }

    public function index()
    {
        $data['semester'] = $this->semester
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('semester/index', $data);
    }

    public function create()
    {
        return view('semester/create');
    }

    public function store()
    {
        $this->semester->save([
            'semester' => $this->request->getPost('semester'),
            'aktif' => 'N'
        ]);

        return redirect()->to('/semester')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['semester'] = $this->semester->find($id);

        if (!$data['semester']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('semester/edit', $data);
    }

    public function update($id)
    {
        $this->semester->update($id, [
            'semester' => $this->request->getPost('semester')
        ]);

        return redirect()->to('/semester')
            ->with('success', 'Data berhasil diperbarui');
    }

   public function aktif($id)
{
    // Nonaktifkan semua
    $this->semester->db
        ->table('semester')
        ->update(['aktif' => 'N']);

    // Aktifkan yang dipilih
    $this->semester->update($id, [
        'aktif' => 'Y'
    ]);

    return redirect()->to('/semester')
                     ->with('success', 'Semester aktif berhasil diubah');
}

    public function delete($id)
    {
        $this->semester->delete($id);

        return redirect()->to('/semester')
            ->with('success', 'Data berhasil dihapus');
    }
}