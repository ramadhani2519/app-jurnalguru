<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\JurnalModel;
use App\Models\KelasModel;
use App\Models\SekolahModel;


class Home extends BaseController
{
    public function index()
    {
        $userModel   = new UserModel();
        $jurnalModel = new JurnalModel();
        $kelasModel  = new KelasModel();
        $sekolahModel  = new SekolahModel();

        $profil = $sekolahModel->first();

        $data = [
            'totalGuru'   => $userModel->where('role_id', 2)->countAllResults(),
            'totalKelas'  => $kelasModel->countAll(),
            'totalJurnal' => $jurnalModel->countAll(),
            'namaSekolah' => $profil['nama_sekolah'],
            'logoSekolah' => base_url('assets/img/'.$profil['logo']),
        ];


        return view('landing', $data);
    }
}