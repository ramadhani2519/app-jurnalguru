<?php

namespace App\Controllers;

use App\Models\SekolahModel;

class Sekolah extends BaseController
{

    protected $sekolah;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        $this->sekolah = new SekolahModel();
    }

    public function index()
    {

        $data['sekolah'] = $this->sekolah->first();

        return view('sekolah/index',$data);

    }

    public function edit()
    {

        $data['sekolah'] = $this->sekolah->first();

        return view('sekolah/edit',$data);

    }

    public function update()
    {
        $id = $this->request->getPost('id');

        // Validasi
        $rules = [
            'nama_sekolah' => 'required',
            'npsn'         => 'required',
            'logo' => [
                'rules' => 'if_exist|is_image[logo]|mime_in[logo,image/png]|max_size[logo,2048]',
                'errors' => [
                    'mime_in'  => 'Logo harus berformat PNG.',
                    'is_image' => 'File yang dipilih bukan gambar.',
                    'max_size' => 'Ukuran logo maksimal 2 MB.'
                ]
            ],
            'logo_provinsi' => [
                'rules' => 'if_exist|is_image[logo_provinsi]|mime_in[logo_provinsi,image/png]|max_size[logo_provinsi,2048]',
                'errors' => [
                    'mime_in'  => 'Logo Pemerintah/Provinsi harus berformat PNG.',
                    'is_image' => 'File yang dipilih bukan gambar.',
                    'max_size' => 'Ukuran logo maksimal 2 MB.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [

            'nama_sekolah'        => $this->request->getPost('nama_sekolah'),
            'nama_pemerintah'     => $this->request->getPost('nama_pemerintah'),
            'nama_dinas'          => $this->request->getPost('nama_dinas'),
            'npsn'                => $this->request->getPost('npsn'),
            'nss'                 => $this->request->getPost('nss'),

            'alamat'              => $this->request->getPost('alamat'),
            'desa'                => $this->request->getPost('desa'),
            'kecamatan'           => $this->request->getPost('kecamatan'),
            'kabupaten'           => $this->request->getPost('kabupaten'),
            'provinsi'            => $this->request->getPost('provinsi'),
            'kode_pos'            => $this->request->getPost('kode_pos'),

            'telepon'             => $this->request->getPost('telepon'),
            'email'               => $this->request->getPost('email'),
            'website'             => $this->request->getPost('website'),
            'kompetensi_keahlian' => $this->request->getPost('kompetensi_keahlian'),

            'kepala_sekolah'      => $this->request->getPost('kepala_sekolah'),
            'nip_kepala'          => $this->request->getPost('nip_kepala'),

            'latitude'            => $this->request->getPost('latitude'),
            'longitude'           => $this->request->getPost('longitude'),
        ];

        // Data lama
        $sekolah = $this->sekolah->find($id);

        $logo = $this->request->getFile('logo');

        // Jika ada file logo sekolah yang diupload
        if ($logo && $logo->getError() != 4) {

            $namaLogo = 'logo-sekolah.png';

            // Hapus logo lama
            if (!empty($sekolah['logo'])) {

                $oldFile = FCPATH . 'assets/img/' . $sekolah['logo'];

                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Simpan ke assets/img
            $logo->move(
                FCPATH . 'assets/img',
                $namaLogo,
                true // overwrite jika sudah ada
            );

            $data['logo'] = $namaLogo;
        }

        $logoProvinsi = $this->request->getFile('logo_provinsi');

        // Jika ada file logo pemerintah/provinsi yang diupload
        if ($logoProvinsi && $logoProvinsi->getError() != 4) {

            $namaLogoProvinsi = 'logo-provinsi.png';

            // Hapus logo lama
            if (!empty($sekolah['logo_provinsi'])) {

                $oldFile = FCPATH . 'assets/img/' . $sekolah['logo_provinsi'];

                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Simpan ke assets/img
            $logoProvinsi->move(
                FCPATH . 'assets/img',
                $namaLogoProvinsi,
                true // overwrite jika sudah ada
            );

            $data['logo_provinsi'] = $namaLogoProvinsi;
        }

        $this->sekolah->update($id, $data);

        return redirect()->to('/sekolah')
            ->with('success', 'Profil sekolah berhasil diperbarui.');
    }

}