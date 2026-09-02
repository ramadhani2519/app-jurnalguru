<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JamModel;

class Jam extends BaseController
{
    protected $jam;

    public function __construct()
    {
        $this->jam = new JamModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Jam Pelajaran',
            'jam'   => $this->jam->orderBy('jam_ke','ASC')->findAll()
        ];

        return view('jam/index', $data);
    }

    public function create()
    {
        return view('jam/create', [
            'title' => 'Tambah Jam Pelajaran',
            'validation' => \Config\Services::validation()
        ]);
    }

    public function save()
    {
        if (!$this->validate([
            'kode_jam' => 'required',
            'jam_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->jam->save([
            'kode_jam' => $this->request->getPost('kode_jam'),
            'jam_ke' => $this->request->getPost('jam_ke'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'istirahat' => $this->request->getPost('istirahat') ?? 0,
            'aktif_jumat' => $this->request->getPost('aktif_jumat') ?? 0,
        ]);

        return redirect()->to('/jam')->with('success','Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('jam/edit',[
            'title'=>'Edit Jam',
            'jam'=>$this->jam->find($id),
            'validation'=>\Config\Services::validation()
        ]);
    }

    public function update($id)
    {
        if (!$this->validate([
            'kode_jam' => 'required',
            'jam_ke' => 'required|integer',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ])) {
            return redirect()->back()->withInput();
        }

        $this->jam->update($id,[
            'kode_jam'=>$this->request->getPost('kode_jam'),
            'jam_ke'=>$this->request->getPost('jam_ke'),
            'jam_mulai'=>$this->request->getPost('jam_mulai'),
            'jam_selesai'=>$this->request->getPost('jam_selesai'),
            'istirahat'=>$this->request->getPost('istirahat') ?? 0,
            'aktif_jumat'=>$this->request->getPost('aktif_jumat') ?? 0,
        ]);

        return redirect()->to('/jam')->with('success','Data berhasil diubah.');
    }

    public function delete($id)
    {
        $this->jam->delete($id);

        return redirect()->to('/jam')->with('success','Data berhasil dihapus.');
    }
}