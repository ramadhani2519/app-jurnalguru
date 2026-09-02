<?php

namespace App\Controllers;

use App\Models\JurusanModel;

/**
 * Master data Jurusan. Dipakai sebagai sumber dropdown "Jurusan yang
 * Diampu" di form Tambah/Edit User (untuk jabatan "Ketua Jurusan").
 */
class Jurusan extends BaseController
{
    protected $jurusan;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Kelola master data Jurusan khusus Admin.
        if (session()->get('role_id') != 1) {
            redirect()->to('/dashboard')
                ->with('error', 'Halaman ini khusus untuk Admin.')
                ->send();
            exit;
        }

        $this->jurusan = new JurusanModel();
    }

    public function index()
    {
        $data['jurusan'] = $this->jurusan
            ->orderBy('nama_jurusan', 'ASC')
            ->findAll();

        return view('jurusan/index', $data);
    }

    public function create()
    {
        return view('jurusan/create');
    }

    public function store()
    {
        $nama = trim((string) $this->request->getPost('nama_jurusan'));

        if ($nama === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nama jurusan wajib diisi.');
        }

        $this->jurusan->save([
            'nama_jurusan' => $nama,
        ]);

        return redirect()->to('/jurusan')
            ->with('success', 'Jurusan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['jurusan'] = $this->jurusan->find($id);

        if (!$data['jurusan']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('jurusan/edit', $data);
    }

    public function update($id)
    {
        $nama = trim((string) $this->request->getPost('nama_jurusan'));

        if ($nama === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nama jurusan wajib diisi.');
        }

        $this->jurusan->update($id, [
            'nama_jurusan' => $nama,
        ]);

        return redirect()->to('/jurusan')
            ->with('success', 'Jurusan berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->jurusan->delete($id);

        return redirect()->to('/jurusan')
            ->with('success', 'Jurusan berhasil dihapus');
    }
}
