<?php

namespace App\Controllers;

use App\Models\SiswaModel;
use App\Models\KelasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Siswa extends BaseController
{
    protected $siswa;
    protected $kelas;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->siswa = new SiswaModel();
        $this->kelas = new KelasModel();
    }
public function index()
{
    $data['siswa'] = $this->siswa
        ->select('siswa.*, kelas.nama_kelas')
        ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
        ->orderBy('nama_siswa', 'ASC')
        ->findAll();

    return view('siswa/index', $data);
}

public function create()
{
    $data['kelas'] = $this->kelas
        ->orderBy('nama_kelas', 'ASC')
        ->findAll();

    return view('siswa/create', $data);
}

public function store()
{
    $nis = trim($this->request->getPost('nis'));

    $cek = $this->siswa
        ->where('nis', $nis)
        ->first();

    if ($cek) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'NIS sudah terdaftar');
    }

    $this->siswa->insert([
        'nis'           => $nis,
        'nama_siswa'    => strtoupper(trim($this->request->getPost('nama_siswa'))),
        'jk'            => $this->request->getPost('jk'),
        'tempat_lahir'  => trim($this->request->getPost('tempat_lahir')),
        'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
        'alamat'        => trim($this->request->getPost('alamat')),
        'kelas_id'      => $this->request->getPost('kelas_id'),
    ]);

    return redirect()->to('siswa')
        ->with('success', 'Data siswa berhasil ditambahkan');
}

public function edit($id)
{
    $data['siswa'] = $this->siswa->find($id);

    if (!$data['siswa']) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    $data['kelas'] = $this->kelas
        ->orderBy('nama_kelas', 'ASC')
        ->findAll();

    return view('siswa/edit', $data);
}

public function update($id)
{
    $nis = trim($this->request->getPost('nis'));

    $cek = $this->siswa
        ->where('nis', $nis)
        ->where('id !=', $id)
        ->first();

    if ($cek) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'NIS sudah digunakan siswa lain');
    }

    $this->siswa->update($id, [
        'nis'           => $nis,
        'nama_siswa'    => strtoupper(trim($this->request->getPost('nama_siswa'))),
        'jk'            => $this->request->getPost('jk'),
        'tempat_lahir'  => trim($this->request->getPost('tempat_lahir')),
        'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
        'alamat'        => trim($this->request->getPost('alamat')),
        'kelas_id'      => $this->request->getPost('kelas_id'),
    ]);

    return redirect()->to('siswa')
        ->with('success', 'Data siswa berhasil diubah');
}

public function import()
{
    $file = $this->request->getFile('file_excel');

    if (!$file->isValid()) {
        return redirect()->back()
            ->with('error', 'File tidak valid');
    }

    $spreadsheet = IOFactory::load($file->getTempName());

    $rows = $spreadsheet
        ->getActiveSheet()
        ->toArray(null, true, true, true);

    $berhasil = 0;
    $duplikat = 0;

    foreach ($rows as $key => $row) {

        // Skip header
        if ($key == 1) {
            continue;
        }

        $nis = trim((string)$row['A']);

        if ($nis == '') {
            continue;
        }

        // Cek NIS ganda
        $cek = $this->siswa
            ->where('nis', $nis)
            ->first();

        if ($cek) {
            $duplikat++;
            continue;
        }

        $this->siswa->insert([
            'nis'             => $nis,
            'nama_siswa'      => trim($row['B']),
            'jk'              => trim($row['C']),
            'tempat_lahir'    => trim($row['D']),
            'tanggal_lahir'   => trim($row['E']),
            'alamat'          => trim($row['F']),
            'kelas_id'        => trim($row['G']),
        ]);

        $berhasil++;
    }

    return redirect()->back()
        ->with(
            'success',
            "Import berhasil. {$berhasil} data ditambahkan, {$duplikat} data duplikat dilewati."
        );
}

    public function delete($id)
    {
        $this->siswa->delete($id);

        return redirect()->to('siswa');
    }
}