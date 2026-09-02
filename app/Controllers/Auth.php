<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() == 'POST')
        {
            $userModel = new UserModel();

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $userModel
            ->select('users.*, roles.nama_role')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.username', $username)
            ->first();

                if($user && password_verify($password, $user['password']))
                {
                    // Ambil semua jabatan tambahan yang dipegang user ini
                    // (Wali Kelas, Ketua Jurusan, Wakasek Kurikulum, dll),
                    // disimpan di session supaya bisa dicek lewat hasJabatan()
                    // tanpa perlu query DB berulang kali.
                    $db = \Config\Database::connect();

                    $jabatanList = $db->table('user_jabatan')
                        ->select('jabatan.nama_jabatan')
                        ->join('jabatan', 'jabatan.id = user_jabatan.jabatan_id')
                        ->where('user_jabatan.user_id', $user['id'])
                        ->get()
                        ->getResultArray();

                    session()->set([
                        'id'           => $user['id'],
                        'nama'         => $user['nama'],
                        'role_id'      => $user['role_id'],
                        'role'         => $user['nama_role'],
                        'kelas_id'     => $user['kelas_id'] ?? null,
                        'jabatan_list' => array_column($jabatanList, 'nama_jabatan'),
                        'logged_in'    => true
                    ]);

                    // Role 3 = Kepala Sekolah
                    if ($user['role_id'] == 3)
                    {
                        return redirect()->to('/kepala-sekolah');
                    }

                    // Role 4 = Petugas Absen (Siswa) -> langsung ke halaman absensi kelasnya
                    if ($user['role_id'] == 4)
                    {
                        if (empty($user['kelas_id'])) {
                            session()->destroy();
                            return redirect()->to('/login')
                                ->with('error','Akun ini belum diset kelasnya. Hubungi admin.');
                        }

                        return redirect()->to('/absensi');
                    }

                    // Role 5 = Petugas Absen Sholat (Siswa) -> langsung ke halaman absensi sholat kelasnya
                    if ($user['role_id'] == 5)
                    {
                        if (empty($user['kelas_id'])) {
                            session()->destroy();
                            return redirect()->to('/login')
                                ->with('error','Akun ini belum diset kelasnya. Hubungi admin.');
                        }

                        return redirect()->to('/absensi-sholat');
                    }

                    // Role lainnya
                    return redirect()->to('/dashboard');
                }

            return redirect()->back()
                ->with('error','Username atau Password salah');
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->getMethod() == 'POST')
        {
            $userModel = new UserModel();

            $userModel->save([
                'nama' => $this->request->getPost('nama'),
                'username' => $this->request->getPost('username'),
                'nip' => $this->request->getPost('nip'),
                'email' => $this->request->getPost('email'),
                'no_hp' => $this->request->getPost('no_hp'),
                'password' => password_hash(
                    $this->request->getPost('password'),
                    PASSWORD_DEFAULT
                ),
                'role_id' =>'2'
            ]);

            return redirect()->to('/login')
                ->with('success','Registrasi berhasil');
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
