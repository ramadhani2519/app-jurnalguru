<?php

namespace App\Controllers;

use App\Models\JenisPelanggaranModel;

/**
 * Master data Jenis Pelanggaran. Dikelola oleh Wakasek Kesiswaan
 * (atau Admin), dipakai sebagai pilihan dropdown "Uraian" di form
 * Input/Edit Pelanggaran Siswa.
 */
class JenisPelanggaran extends BaseController
{
    protected $jenisPelanggaran;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya Admin atau user dengan jabatan tambahan "Wakasek Kesiswaan"
        if (session()->get('role_id') != 1 && !$this->hasJabatan('Wakasek Kesiswaan')) {
            redirect()->to('/dashboard')
                ->with('error', 'Halaman ini khusus untuk Admin atau Wakasek Kesiswaan.')
                ->send();
            exit;
        }

        $this->jenisPelanggaran = new JenisPelanggaranModel();
    }

    public function index()
    {
        $data['jenisPelanggaran'] = $this->jenisPelanggaran
            ->orderBy('nama_pelanggaran', 'ASC')
            ->findAll();

        return view('jenis_pelanggaran/index', $data);
    }

    public function create()
    {
        return view('jenis_pelanggaran/create');
    }

    public function store()
    {
        $nama = trim((string) $this->request->getPost('nama_pelanggaran'));

        if ($nama === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nama jenis pelanggaran wajib diisi.');
        }

        $this->jenisPelanggaran->save([
            'nama_pelanggaran' => $nama,
        ]);

        return redirect()->to('/jenis-pelanggaran')
            ->with('success', 'Jenis pelanggaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['jenisPelanggaran'] = $this->jenisPelanggaran->find($id);

        if (!$data['jenisPelanggaran']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('jenis_pelanggaran/edit', $data);
    }

    public function update($id)
    {
        $nama = trim((string) $this->request->getPost('nama_pelanggaran'));

        if ($nama === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Nama jenis pelanggaran wajib diisi.');
        }

        $this->jenisPelanggaran->update($id, [
            'nama_pelanggaran' => $nama,
        ]);

        return redirect()->to('/jenis-pelanggaran')
            ->with('success', 'Jenis pelanggaran berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->jenisPelanggaran->delete($id);

        return redirect()->to('/jenis-pelanggaran')
            ->with('success', 'Jenis pelanggaran berhasil dihapus');
    }
}
