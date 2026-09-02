<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\JabatanModel;
use App\Models\UserJabatanModel;

class User extends BaseController
{
    protected $user;
    protected $kelas;
    protected $jabatan;
    protected $userJabatan;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->user        = new UserModel();
        $this->kelas        = new KelasModel();
        $this->jabatan      = new JabatanModel();
        $this->userJabatan = new UserJabatanModel();
    }

    public function index()
    {
        $daftarUser = $this->user
            ->orderBy('nama','ASC')
            ->findAll();

        // Ambil jabatan tiap user sekaligus, supaya bisa ditampilkan sebagai badge
        foreach ($daftarUser as &$u) {

            $jabatanUser = $this->userJabatan
                ->select('user_jabatan.*, jabatan.nama_jabatan, kelas.nama_kelas')
                ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
                ->join('kelas', 'kelas.id = user_jabatan.kelas_id', 'left')
                ->where('user_id', $u['id'])
                ->findAll();

            $u['jabatan_list'] = $jabatanUser;
        }
        unset($u);

        $data['user'] = $daftarUser;

        return view('user/index',$data);
    }

    public function create()
    {
        $data['kelas']   = $this->kelas->findAll();
        $data['jabatan'] = $this->jabatan->findAll();
        $data['jurusanList'] = $this->daftarJurusan();

        return view('user/create',$data);
    }

    public function store()
    {
        $userId = $this->user->insert([
            'role_id'  => $this->request->getPost('role_id'),
            'nama'     => $this->request->getPost('nama'),
            'nip'      => $this->request->getPost('nip'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'email'    => $this->request->getPost('email'),
            'no_hp'    => $this->request->getPost('no_hp'),
            // kelas_id di sini khusus untuk role Petugas Absen (Siswa) / Petugas Absen Sholat
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
        ]);

        $this->simpanJabatan($userId);

        return redirect()->to('/user')
            ->with('success','Data berhasil disimpan');
    }

    public function edit($id)
    {
        $data['user']   = $this->user->find($id);
        $data['kelas']   = $this->kelas->findAll();
        $data['jabatan'] = $this->jabatan->findAll();
        $data['jurusanList'] = $this->daftarJurusan();

        // Jabatan yang sudah dimiliki user ini (untuk pre-check checkbox)
        $data['userJabatan'] = $this->userJabatan
            ->where('user_id', $id)
            ->findAll();

        return view('user/edit',$data);
    }

    public function update($id)
    {
        $data = [
            'role_id'  => $this->request->getPost('role_id'),
            'nama'     => $this->request->getPost('nama'),
            'nip'      => $this->request->getPost('nip'),
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'no_hp'    => $this->request->getPost('no_hp'),
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
        ];

        if($this->request->getPost('password') != '')
        {
            $data['password'] = password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            );
        }

        $this->user->update($id,$data);

        // Hapus jabatan lama, lalu simpan ulang sesuai centangan terbaru
        $this->userJabatan->where('user_id', $id)->delete();
        $this->simpanJabatan($id);

        return redirect()->to('/user')
            ->with('success','Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->userJabatan->where('user_id', $id)->delete();
        $this->user->delete($id);

        return redirect()->to('/user')
            ->with('success','Data berhasil dihapus');
    }

    /**
     * Simpan checkbox jabatan (bisa lebih dari satu) untuk seorang user.
     * Kalau salah satu jabatannya "Wali Kelas", ikut simpan kelas_id
     * yang dipilih khusus untuk jabatan itu (beda dengan kelas_id di
     * tabel users yang dipakai Petugas Absen).
     */
    private function simpanJabatan($userId)
    {
        $jabatanDipilih = $this->request->getPost('jabatan_id') ?? [];
        $kelasWali      = $this->request->getPost('kelas_wali_id');
        $jurusanKetua   = trim((string) $this->request->getPost('jurusan_ketua'));

        foreach ($jabatanDipilih as $jabatanId) {

            $jabatanInfo = $this->jabatan->find($jabatanId);

            $kelasId = null;
            $jurusan = null;

            if ($jabatanInfo && strtolower($jabatanInfo['nama_jabatan']) == 'wali kelas') {
                $kelasId = $kelasWali ?: null;
            }

            if ($jabatanInfo && strtolower($jabatanInfo['nama_jabatan']) == 'ketua jurusan') {
                $jurusan = $jurusanKetua ?: null;
            }

            $this->userJabatan->insert([
                'user_id'    => $userId,
                'jabatan_id' => $jabatanId,
                'kelas_id'   => $kelasId,
                'jurusan'    => $jurusan,
            ]);
        }
    }

    /**
     * Daftar jurusan dari tabel master `jurusan`, dipakai untuk dropdown
     * "Ketua Jurusan untuk Jurusan" di form tambah/edit user.
     */
    private function daftarJurusan(): array
    {
        return (new \App\Models\JurusanModel())
            ->orderBy('nama_jurusan', 'ASC')
            ->findAll();
    }
}
