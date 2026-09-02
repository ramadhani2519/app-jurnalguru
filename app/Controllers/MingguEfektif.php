<?php

namespace App\Controllers;

use App\Models\MingguEfektifModel;
use App\Models\KelasModel;

class MingguEfektif extends BaseController
{
    protected $mingguEfektif;
    protected $kelas;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/')->send();
            exit;
        }

        // Hanya Admin atau Guru yang menjabat Wakasek Kurikulum
        if (session()->get('role_id') != 1 && !$this->hasJabatan('Wakasek Kurikulum')) {
            redirect()->to('/dashboard')->send();
            exit;
        }

        $this->mingguEfektif = new MingguEfektifModel();
        $this->kelas         = new KelasModel();
    }

    /**
     * Daftar kelas + ringkasan total minggu efektif tiap kelas
     * (untuk tahun pelajaran & semester yang sedang aktif).
     */
    public function index()
    {
        $daftarKelas = $this->kelas->orderBy('tingkat', 'ASC')->orderBy('nama_kelas', 'ASC')->findAll();

        foreach ($daftarKelas as &$k) {

            $items = $this->ambilData($k['id']);

            $k['total_efektif'] = array_sum(array_map(
                fn($i) => max(0, (int) $i['jumlah_minggu'] - (int) $i['minggu_tidak_efektif']),
                $items
            ));

            $k['jumlah_bulan'] = count($items);
        }
        unset($k);

        $data = [
            'daftarKelas'   => $daftarKelas,
            'tahunAktif'    => $this->tahunAktif,
            'semesterAktif' => $this->semesterAktif,
        ];

        return view('minggu_efektif/index', $data);
    }

    /**
     * Detail minggu efektif untuk satu kelas, per bulan.
     */
    public function detail($kelas_id)
    {
        $kelas = $this->kelas->find($kelas_id);

        if (!$kelas) {
            return redirect()->to('/minggu-efektif')->with('error', 'Kelas tidak ditemukan.');
        }

        $items = $this->ambilData($kelas_id);

        $totalMinggu       = array_sum(array_column($items, 'jumlah_minggu'));
        $totalTidakEfektif = array_sum(array_column($items, 'minggu_tidak_efektif'));
        $totalEfektif      = $totalMinggu - $totalTidakEfektif;

        $data = [
            'kelas'             => $kelas,
            'items'             => $items,
            'totalMinggu'       => $totalMinggu,
            'totalTidakEfektif' => $totalTidakEfektif,
            'totalEfektif'      => $totalEfektif,
            'tahunAktif'        => $this->tahunAktif,
            'semesterAktif'     => $this->semesterAktif,
        ];

        return view('minggu_efektif/detail', $data);
    }

    public function create($kelas_id)
    {
        $data = [
            'kelas' => $this->kelas->find($kelas_id),
        ];

        return view('minggu_efektif/create', $data);
    }

    public function store()
    {
        $this->mingguEfektif->insert([
            'tahun_pelajaran_id'   => $this->tahunAktif['id'] ?? null,
            'semester_id'          => $this->semesterAktif['id'] ?? null,
            'kelas_id'             => $this->request->getPost('kelas_id'),
            'bulan'                => $this->request->getPost('bulan'),
            'jumlah_minggu'        => (int) $this->request->getPost('jumlah_minggu'),
            'minggu_tidak_efektif' => (int) $this->request->getPost('minggu_tidak_efektif'),
            'keterangan'           => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/minggu-efektif/detail/' . $this->request->getPost('kelas_id'))
            ->with('success', 'Data minggu efektif berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['item'] = $this->mingguEfektif->find($id);

        if (!$data['item']) {
            return redirect()->to('/minggu-efektif')->with('error', 'Data tidak ditemukan.');
        }

        $data['kelas'] = $this->kelas->find($data['item']['kelas_id']);

        return view('minggu_efektif/edit', $data);
    }

    public function update($id)
    {
        $item = $this->mingguEfektif->find($id);

        if (!$item) {
            return redirect()->to('/minggu-efektif')->with('error', 'Data tidak ditemukan.');
        }

        $this->mingguEfektif->update($id, [
            'bulan'                => $this->request->getPost('bulan'),
            'jumlah_minggu'        => (int) $this->request->getPost('jumlah_minggu'),
            'minggu_tidak_efektif' => (int) $this->request->getPost('minggu_tidak_efektif'),
            'keterangan'           => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/minggu-efektif/detail/' . $item['kelas_id'])
            ->with('success', 'Data minggu efektif berhasil diperbarui.');
    }

    public function delete($id)
    {
        $item = $this->mingguEfektif->find($id);

        if (!$item) {
            return redirect()->to('/minggu-efektif')->with('error', 'Data tidak ditemukan.');
        }

        $this->mingguEfektif->delete($id);

        return redirect()->to('/minggu-efektif/detail/' . $item['kelas_id'])
            ->with('success', 'Data minggu efektif berhasil dihapus.');
    }

    /**
     * Ambil data minggu efektif 1 kelas, untuk tahun pelajaran &
     * semester yang sedang aktif, urut sesuai input (id ASC).
     */
    private function ambilData($kelas_id)
    {
        return $this->mingguEfektif
            ->where('kelas_id', $kelas_id)
            ->where('tahun_pelajaran_id', $this->tahunAktif['id'] ?? null)
            ->where('semester_id', $this->semesterAktif['id'] ?? null)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
