<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\TahunPelajaranModel;
use App\Models\SemesterModel;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    protected $tahunAktif;
    protected $semesterAktif;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $tpModel = new TahunPelajaranModel();
        $semesterModel = new SemesterModel();

        $this->tahunAktif = $tpModel
            ->where('aktif', 'Y')
            ->first();

        $this->semesterAktif = $semesterModel
            ->where('aktif', 'Y')
            ->first();

        $renderer = service('renderer');

        $renderer->setVar('tahunAktif', $this->tahunAktif);
        $renderer->setVar('semesterAktif', $this->semesterAktif);
    }

    /**
     * Ubah file logo (di public/assets/img) jadi base64,
     * supaya bisa ditampilkan langsung di PDF (Dompdf) tanpa
     * bergantung pada akses file:// atau URL publik.
     * Dipakai untuk logo sekolah maupun logo pemerintah/provinsi
     * di semua kop surat cetak.
     */
    protected function logoToBase64(?string $namaFile): string
    {
        if (empty($namaFile)) {
            return '';
        }

        $path = FCPATH . 'assets/img/' . $namaFile;

        if (!is_file($path)) {
            return '';
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Cek apakah user yang sedang login memegang jabatan tertentu
     * (misal 'Wali Kelas', 'Ketua Jurusan', 'Wakasek Kurikulum').
     * Data jabatan disimpan di session saat login (lihat Auth::login()),
     * jadi tidak perlu query DB ulang setiap request.
     */
    protected function hasJabatan(string $namaJabatan): bool
    {
        $daftarJabatan = session()->get('jabatan_list') ?? [];

        foreach ($daftarJabatan as $j) {
            if (strtolower($j) === strtolower($namaJabatan)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Jumlah pelanggaran & pembinaan EFEKTIF per siswa, dipakai oleh
     * seluruh sistem jenjang pembinaan (Guru Wali -> Wali Kelas ->
     * Ketua Jurusan) untuk menentukan tahap penanganan.
     *
     * Aturan reset 1 bulan: kalau siswa sudah tidak melanggar lagi
     * selama 30 hari sejak pelanggaran TERAKHIRnya, seluruh riwayat
     * pelanggaran & pembinaannya dianggap lunas -> dihitung 0 lagi
     * (siklus jenjang dimulai dari awal). Data pelanggaran & pembinaan
     * yang lama TETAP TERSIMPAN di database, cuma tidak diikutkan lagi
     * dalam perhitungan jenjang & notifikasi.
     *
     * @param int[] $siswaIds
     * @return array<int, array{jumlah_pelanggaran:int, jumlah_pembinaan:int, direset:bool, tanggal_pelanggaran_terakhir:?string}>
     */
    protected function statusPembinaanEfektif(array $siswaIds): array
    {
        $hasil = [];

        if (empty($siswaIds)) {
            return $hasil;
        }

        $db = \Config\Database::connect();

        $rowsPelanggaran = $db->table('pelanggaran_siswa')
            ->select('siswa_id, COUNT(*) as jumlah, MAX(tanggal) as tanggal_terakhir')
            ->whereIn('siswa_id', $siswaIds)
            ->groupBy('siswa_id')
            ->get()
            ->getResultArray();

        $rowsPembinaan = $db->table('pembinaan_siswa')
            ->select('siswa_id, COUNT(*) as jumlah')
            ->whereIn('siswa_id', $siswaIds)
            ->groupBy('siswa_id')
            ->get()
            ->getResultArray();

        $petaPembinaan = [];
        foreach ($rowsPembinaan as $r) {
            $petaPembinaan[$r['siswa_id']] = (int) $r['jumlah'];
        }

        $batasHari = 30;

        foreach ($rowsPelanggaran as $r) {
            $sid             = $r['siswa_id'];
            $tanggalTerakhir = $r['tanggal_terakhir'];

            $sudahLunas = $tanggalTerakhir
                && (strtotime('today') - strtotime(date('Y-m-d', strtotime($tanggalTerakhir)))) >= ($batasHari * 86400);

            $hasil[$sid] = [
                'jumlah_pelanggaran'           => $sudahLunas ? 0 : (int) $r['jumlah'],
                'jumlah_pembinaan'             => $sudahLunas ? 0 : ($petaPembinaan[$sid] ?? 0),
                'direset'                      => $sudahLunas,
                'tanggal_pelanggaran_terakhir' => $tanggalTerakhir,
            ];
        }

        return $hasil;
    }
}