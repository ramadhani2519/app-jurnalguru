<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JurnalModel;

class Profile extends BaseController
{
    protected $userModel;
    protected $jurnalModel;

    public function __construct()
    {
        // Cek session login
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }
        $this->userModel   = new UserModel();
        $this->jurnalModel = new JurnalModel();
    }

    public function index()
    {
        // User login
        $userId = session()->get('id');

        // Data user
        $user = $this->userModel->find($userId);

        // Total guru (role_id = 2)
        $totalGuru = $this->userModel
            ->where('role_id', 2)
            ->countAllResults();

        // Guru yang sudah mengajar hari ini
        $guruMasuk = $this->jurnalModel
            ->select('user_id')
            ->where('tanggal', date('Y-m-d'))
            ->groupBy('user_id')
            ->countAllResults();

        $data = [
            'title'      => 'Profile',
            'user'       => $user,
            'totalGuru'  => $totalGuru,
            'guruMasuk'  => $guruMasuk
        ];

        return view('profile/index', $data);
    }

    public function edit()
    {
        $data = [
            'title' => 'Edit Profile',
            'user'  => $this->userModel->find(session()->get('id'))
        ];

        return view('profile/edit', $data);
    }

    public function update()
    {
        $id = session()->get('id');

        $user = $this->userModel->find($id);

        $data = [
            'nama'     => $this->request->getPost('nama'),
            'email'    => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'nip'      => $this->request->getPost('nip'),
            'no_hp'    => $this->request->getPost('no_hp'),
            'alamat'   => $this->request->getPost('alamat'),
        ];

        $foto = $this->request->getFile('foto');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {

            $namaFoto = $foto->getRandomName();

            $foto->move(FCPATH . 'uploads/profile', $namaFoto);

            // Hapus foto lama
            if (!empty($user['foto']) &&
                file_exists(FCPATH . 'uploads/profile/' . $user['foto'])) {

                unlink(FCPATH . 'uploads/profile/' . $user['foto']);
            }

            $data['foto'] = $namaFoto;
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}