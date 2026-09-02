<?php

namespace App\Controllers;

use App\Models\PelanggaranModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\SekolahModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Pelanggaran extends BaseController
{
    protected $pelanggaran;
    protected $siswa;
    protected $kelas;
    protected $sekolah;


    public function __construct()
    {
        $this->pelanggaran = new PelanggaranModel();
        $this->siswa = new SiswaModel();
        $this->kelas = new KelasModel();
        $this->sekolah = new SekolahModel();
    }

    public function index()
    {
        $tgl1     = $this->request->getGet('tgl1');
        $tgl2     = $this->request->getGet('tgl2');
        $kelas_id = $this->request->getGet('kelas_id');
        $siswa_id = $this->request->getGet('siswa_id');

        $builder = $this->pelanggaran
            ->select("
                pelanggaran_siswa.*,
                siswa.nama_siswa,
                kelas.nama_kelas
            ")
            ->join('siswa', 'siswa.id = pelanggaran_siswa.siswa_id')
            ->join('kelas', 'kelas.id = pelanggaran_siswa.kelas_id');

        if (!empty($tgl1)) {
            $builder->where('tanggal >=', $tgl1);
        }

        if (!empty($tgl2)) {
            $builder->where('tanggal <=', $tgl2);
        }

        if (!empty($kelas_id)) {
            $builder->where('pelanggaran_siswa.kelas_id', $kelas_id);
        }

        if (!empty($siswa_id)) {
            $builder->where('pelanggaran_siswa.siswa_id', $siswa_id);
        }

        $data = [
            'pelanggaran' => $builder->orderBy('tanggal', 'DESC')->findAll(),
            'kelas'       => $this->kelas->findAll(),
            'siswa'       => $this->siswa->findAll(),

            // kirim ke view
            'tgl1'        => $tgl1,
            'tgl2'        => $tgl2,
            'kelas_id'    => $kelas_id,
            'siswa_id'    => $siswa_id,
        ];

        return view('pelanggaran/index', $data);
    }
    public function create()
    {
        $data['siswa'] = $this->siswa->findAll();
        $data['kelas'] = $this->kelas->findAll();
        $data['jenisPelanggaran'] = (new \App\Models\JenisPelanggaranModel())
            ->orderBy('nama_pelanggaran', 'ASC')
            ->findAll();

        return view('pelanggaran/create',$data);
    }

    public function save()
    {
        $uraian = $this->request->getPost('uraian_pelanggaran');

        if ($uraian === '__lainnya__') {
            $uraian = trim((string) $this->request->getPost('uraian_lainnya'));
        }

        $this->pelanggaran->save([
            'tanggal'=>$this->request->getPost('tanggal'),
            'kelas_id'=>$this->request->getPost('kelas_id'),
            'siswa_id'=>$this->request->getPost('siswa_id'),
            'uraian_pelanggaran'=>$uraian,
            'keterangan'=>$this->request->getPost('keterangan'),
            'user_id'=>session()->get('id')
        ]);

        return redirect()->to('/pelanggaran')->with('success','Data berhasil disimpan');
    }

    public function edit($id)
    {
        $data['pelanggaran'] = $this->pelanggaran->find($id);
        $data['kelas'] = $this->kelas->findAll();
        $data['siswa'] = $this->siswa->findAll();
        $data['jenisPelanggaran'] = (new \App\Models\JenisPelanggaranModel())
            ->orderBy('nama_pelanggaran', 'ASC')
            ->findAll();

        return view('pelanggaran/edit',$data);
    }

    public function update($id)
    {
        $uraian = $this->request->getPost('uraian_pelanggaran');

        if ($uraian === '__lainnya__') {
            $uraian = trim((string) $this->request->getPost('uraian_lainnya'));
        }

        $this->pelanggaran->update($id,[
            'tanggal'=>$this->request->getPost('tanggal'),
            'kelas_id'=>$this->request->getPost('kelas_id'),
            'siswa_id'=>$this->request->getPost('siswa_id'),
            'uraian_pelanggaran'=>$uraian,
            'keterangan'=>$this->request->getPost('keterangan'),
        ]);

        return redirect()->to('/pelanggaran')->with('success','Data berhasil diubah');
    }


    public function cetak($id)
    {
        $pelanggaran = $this->pelanggaran
            ->select("
                pelanggaran_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ")
            ->join('siswa', 'siswa.id = pelanggaran_siswa.siswa_id')
            ->join('kelas', 'kelas.id = pelanggaran_siswa.kelas_id')
            ->where('pelanggaran_siswa.id', $id)
            ->first();

        if (!$pelanggaran) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        /*
        |--------------------------------------------------------
        | Data Sekolah
        |--------------------------------------------------------
        */

        $sekolah = $this->sekolah->first();

        $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        /*
        |--------------------------------------------------------
        | Data PDF
        |--------------------------------------------------------
        */

        $data = [
            'pelanggaran'        => $pelanggaran,
            'sekolah'            => $sekolah,
            'logoBase64'         => $logoBase64,
            'logoProvinsiBase64' => $logoProvinsiBase64
        ];

        $html = view('pelanggaran/pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        // Nomor halaman
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');

        $canvas->page_text(
            500,
            815,
            "Halaman {PAGE_NUM} / {PAGE_COUNT}",
            $font,
            9,
            [0, 0, 0]
        );

        while (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf->stream(
            'Kartu-Pelanggaran-' . preg_replace('/[^A-Za-z0-9]/', '-', $pelanggaran['nama_siswa']) . '.pdf',
            ['Attachment' => false]
        );

        exit;
    }


    public function cetakPdf()
    {
        $tgl1     = $this->request->getGet('tgl1');
        $tgl2     = $this->request->getGet('tgl2');
        $kelas_id = $this->request->getGet('kelas_id');
        $siswa_id = $this->request->getGet('siswa_id');

        $builder = $this->pelanggaran
            ->select("
                pelanggaran_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ")
            ->join('siswa','siswa.id=pelanggaran_siswa.siswa_id')
            ->join('kelas','kelas.id=pelanggaran_siswa.kelas_id');

        if(!empty($tgl1)){
            $builder->where('tanggal >=',$tgl1);
        }

        if(!empty($tgl2)){
            $builder->where('tanggal <=',$tgl2);
        }

        if(!empty($kelas_id)){
            $builder->where('pelanggaran_siswa.kelas_id',$kelas_id);
        }

        if(!empty($siswa_id)){
            $builder->where('pelanggaran_siswa.siswa_id',$siswa_id);
        }

        $pelanggaran = $builder
            ->orderBy('tanggal','ASC')
            ->findAll();

        $kelas = '';

        if($kelas_id!=''){
            $k = $this->kelas->find($kelas_id);
            $kelas = $k['nama_kelas'];
        }

        $siswa = '';

        if($siswa_id!=''){
            $s = $this->siswa->find($siswa_id);
            $siswa = $s['nama_siswa'];
        }

        $sekolah = $this->sekolah->first();

        $logoBase64 = $this->logoToBase64($sekolah['logo'] ?? null) ?: $this->logoToBase64('logo-default.png');
        $logoProvinsiBase64 = $this->logoToBase64($sekolah['logo_provinsi'] ?? null);

        $data=[

            'pelanggaran'=>$pelanggaran,

            'jumlah'=>count($pelanggaran),

            'tgl1'=>$tgl1,

            'tgl2'=>$tgl2,

            'kelas'=>$kelas,

            'siswa'=>$siswa,

            'sekolah'=>$sekolah,

            'logoBase64'=>$logoBase64,

            'logoProvinsiBase64'=>$logoProvinsiBase64

        ];

        $html=view('pelanggaran/cetak_all',$data);

        $options=new \Dompdf\Options();
        $options->set('isRemoteEnabled',true);

        $dompdf=new \Dompdf\Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4','landscape');

        $dompdf->render();

        $canvas=$dompdf->getCanvas();

        $font=$dompdf->getFontMetrics()->getFont('Helvetica','normal');

        $canvas->page_text(
            760,
            565,
            "Halaman {PAGE_NUM} / {PAGE_COUNT}",
            $font,
            9,
            [0,0,0]
        );

        while(ob_get_level()){
            ob_end_clean();
        }

        $dompdf->stream(
            'Laporan-Pelanggaran-Siswa.pdf',
            ['Attachment'=>false]
        );

        exit;
}
    public function delete($id)
    {
        $this->pelanggaran->delete($id);

        return redirect()->to('/pelanggaran')->with('success','Data berhasil dihapus');
    }

    /**
     * Unduh Data Pelanggaran Siswa dalam format Excel (.xlsx), dengan
     * filter yang sama persis dengan cetakPdf(): rentang tanggal,
     * kelas, dan/atau siswa tertentu.
     */
    public function exportExcel()
    {
        $tgl1     = $this->request->getGet('tgl1');
        $tgl2     = $this->request->getGet('tgl2');
        $kelas_id = $this->request->getGet('kelas_id');
        $siswa_id = $this->request->getGet('siswa_id');

        $builder = $this->pelanggaran
            ->select("
                pelanggaran_siswa.*,
                siswa.nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ")
            ->join('siswa', 'siswa.id = pelanggaran_siswa.siswa_id')
            ->join('kelas', 'kelas.id = pelanggaran_siswa.kelas_id');

        if (!empty($tgl1)) {
            $builder->where('tanggal >=', $tgl1);
        }

        if (!empty($tgl2)) {
            $builder->where('tanggal <=', $tgl2);
        }

        if (!empty($kelas_id)) {
            $builder->where('pelanggaran_siswa.kelas_id', $kelas_id);
        }

        if (!empty($siswa_id)) {
            $builder->where('pelanggaran_siswa.siswa_id', $siswa_id);
        }

        $pelanggaran = $builder->orderBy('tanggal', 'ASC')->findAll();

        $sekolah = $this->sekolah->first();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pelanggaran Siswa');

        $sheet->setCellValue('A1', 'LAPORAN PELANGGARAN SISWA');
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A2', $sekolah['nama_sekolah'] ?? '');
        $sheet->mergeCells('A2:G2');

        foreach (['A1', 'A2'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize($cell === 'A1' ? 14 : 11);
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $keteranganFilter = [];

        if (!empty($tgl1) || !empty($tgl2)) {
            $keteranganFilter[] = 'Periode: ' . ($tgl1 ? date('d/m/Y', strtotime($tgl1)) : '...') . ' s/d ' . ($tgl2 ? date('d/m/Y', strtotime($tgl2)) : '...');
        }

        if (!empty($kelas_id)) {
            $k = $this->kelas->find($kelas_id);
            $keteranganFilter[] = 'Kelas: ' . ($k['nama_kelas'] ?? '-');
        }

        if (!empty($siswa_id)) {
            $s = $this->siswa->find($siswa_id);
            $keteranganFilter[] = 'Siswa: ' . ($s['nama_siswa'] ?? '-');
        }

        $sheet->setCellValue('A4', !empty($keteranganFilter) ? implode(' | ', $keteranganFilter) : 'Semua Data');
        $sheet->mergeCells('A4:G4');
        $sheet->getStyle('A4')->getFont()->setItalic(true);

        $barisHeader = 6;
        $headerKolom = ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Uraian Pelanggaran', 'Keterangan'];

        foreach ($headerKolom as $i => $judul) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $barisHeader, $judul);
        }

        $sheet->getStyle("A{$barisHeader}:G{$barisHeader}")->getFont()->setBold(true);
        $sheet->getStyle("A{$barisHeader}:G{$barisHeader}")
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DDEBF7');
        $sheet->getStyle("A{$barisHeader}:G{$barisHeader}")
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $baris = $barisHeader + 1;
        $no = 1;

        foreach ($pelanggaran as $row) {
            $sheet->setCellValue("A{$baris}", $no++);
            $sheet->setCellValue("B{$baris}", date('d/m/Y', strtotime($row['tanggal'])));
            $sheet->setCellValue("C{$baris}", $row['nis'] ?? '-');
            $sheet->setCellValue("D{$baris}", $row['nama_siswa']);
            $sheet->setCellValue("E{$baris}", $row['nama_kelas']);
            $sheet->setCellValue("F{$baris}", $row['uraian_pelanggaran']);
            $sheet->setCellValue("G{$baris}", $row['keterangan']);
            $baris++;
        }

        if ($baris > $barisHeader + 1) {
            $sheet->getStyle("A{$barisHeader}:G" . ($baris - 1))
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $namaFile = 'Laporan-Pelanggaran-Siswa-' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $namaFile . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }
}