<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Perintah debug untuk mengecek kenapa "Jadwal Mengajar Hari Ini"
 * di dashboard Kepala Sekolah tampil kosong / tidak sesuai.
 *
 * Cara pakai:
 *   php spark cek:jadwal
 *
 * Bisa juga override tanggal untuk cek hari lain, misal untuk
 * simulasi hari Senin:
 *   php spark cek:jadwal 2026-08-03
 */
class CekJadwalHariIni extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'cek:jadwal';
    protected $description = 'Cek data jadwal untuk hari ini (atau tanggal tertentu), buat debug dashboard Kepala Sekolah.';
    protected $usage       = 'cek:jadwal [tanggal]';
    protected $arguments   = [
        'tanggal' => 'Tanggal yang mau dicek (format Y-m-d). Default: hari ini.',
    ];

    private $mapHari = [
        'Monday'    => 'SENIN',
        'Tuesday'   => 'SELASA',
        'Wednesday' => 'RABU',
        'Thursday'  => 'KAMIS',
        'Friday'    => "JUM'AT",
        'Saturday'  => 'SABTU',
        'Sunday'    => 'MINGGU',
    ];

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $tanggal = $params[0] ?? date('Y-m-d');
        $namaHariPhp = date('l', strtotime($tanggal));
        $namaHariIni = $this->mapHari[$namaHariPhp] ?? '(tidak dikenali)';

        CLI::write('====================================================', 'yellow');
        CLI::write(' CEK JADWAL HARI INI - Debug Dashboard Kepala Sekolah', 'yellow');
        CLI::write('====================================================', 'yellow');
        CLI::newLine();

        CLI::write('Tanggal dicek   : ' . $tanggal);
        CLI::write('Nama hari (PHP) : ' . $namaHariPhp);
        CLI::write('Dipetakan jadi  : ' . $namaHariIni);
        CLI::newLine();

        // 1. Cek tahun pelajaran & semester aktif
        $tahunAktif = $db->table('tahun_pelajaran')->where('aktif', 'Y')->get()->getRowArray();
        $semesterAktif = $db->table('semester')->where('aktif', 'Y')->get()->getRowArray();

        if (!$tahunAktif) {
            CLI::write('✗ TIDAK ADA tahun_pelajaran dengan aktif = Y !', 'red');
        } else {
            CLI::write("✓ Tahun Pelajaran aktif : #{$tahunAktif['id']} - {$tahunAktif['tahun']}", 'green');
        }

        if (!$semesterAktif) {
            CLI::write('✗ TIDAK ADA semester dengan aktif = Y !', 'red');
        } else {
            CLI::write("✓ Semester aktif        : #{$semesterAktif['id']} - {$semesterAktif['semester']}", 'green');
        }

        CLI::newLine();

        // 2. Cek baris di tabel hari yang cocok dengan nama hari ini
        $hariRow = $db->table('hari')->where('nama_hari', $namaHariIni)->get()->getRowArray();

        if (!$hariRow) {
            CLI::write("✗ TIDAK ADA baris di tabel 'hari' dengan nama_hari = '{$namaHariIni}'", 'red');
            CLI::write('  Isi tabel hari yang tersedia:');
            foreach ($db->table('hari')->get()->getResultArray() as $h) {
                CLI::write("   - id={$h['id']} nama_hari='{$h['nama_hari']}'");
            }
            CLI::newLine();
        } else {
            CLI::write("✓ Ditemukan di tabel hari : id={$hariRow['id']} nama_hari='{$hariRow['nama_hari']}'", 'green');
            CLI::newLine();
        }

        // 3. Total jadwal keseluruhan (tanpa filter apapun)
        $totalJadwal = $db->table('jadwal')->countAllResults();
        CLI::write("Total baris di tabel jadwal (semua) : {$totalJadwal}");

        // 4. Jadwal untuk hari ini SAJA (tanpa filter tahun/semester)
        if ($hariRow) {
            $jadwalHariSaja = $db->table('jadwal')
                ->where('hari_id', $hariRow['id'])
                ->countAllResults();
            CLI::write("Jadwal dengan hari_id={$hariRow['id']} (tanpa filter tahun/semester) : {$jadwalHariSaja}");
        }

        CLI::newLine();

        // 5. Jadwal hari ini + filter tahun & semester aktif (query yang dipakai dashboard)
        $builder = $db->table('jadwal')
            ->select('
                jadwal.id, jadwal.tahun_pelajaran_id, jadwal.semester_id,
                users.nama as nama_guru, mata_pelajaran.nama_mapel, kelas.nama_kelas,
                jam_pelajaran.jam_ke, hari.nama_hari
            ')
            ->join('users', 'users.id = jadwal.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id')
            ->join('hari', 'hari.id = jadwal.hari_id')
            ->join('jam_pelajaran', 'jam_pelajaran.id = jadwal.jam_id')
            ->where('hari.nama_hari', $namaHariIni);

        if ($tahunAktif) {
            $builder->where('jadwal.tahun_pelajaran_id', $tahunAktif['id']);
        }
        if ($semesterAktif) {
            $builder->where('jadwal.semester_id', $semesterAktif['id']);
        }

        $hasil = $builder->orderBy('jam_pelajaran.jam_ke', 'ASC')->get()->getResultArray();

        CLI::write('----------------------------------------------------', 'yellow');
        CLI::write(' HASIL QUERY dashboard (jadwal hari ini + tahun/semester aktif)', 'yellow');
        CLI::write('----------------------------------------------------', 'yellow');

        if (empty($hasil)) {
            CLI::write('✗ KOSONG. Tidak ada jadwal yang cocok.', 'red');
            CLI::newLine();
            CLI::write('Kemungkinan penyebab:');
            CLI::write(' 1. Belum ada baris di tabel jadwal untuk hari ini.');
            CLI::write(' 2. jadwal.tahun_pelajaran_id / semester_id di baris jadwal TIDAK SAMA');
            CLI::write('    dengan id tahun_pelajaran / semester yang aktif (lihat di atas).');
            CLI::write(' 3. hari_id di baris jadwal salah / tidak sesuai tabel hari.');
        } else {
            CLI::table(
                array_map(function ($r) {
                    return [
                        $r['id'],
                        $r['nama_guru'],
                        $r['nama_mapel'],
                        $r['nama_kelas'],
                        $r['jam_ke'],
                        $r['nama_hari'],
                        $r['tahun_pelajaran_id'],
                        $r['semester_id'],
                    ];
                }, $hasil),
                ['ID', 'Guru', 'Mapel', 'Kelas', 'Jam Ke', 'Hari', 'TP_id', 'Sem_id']
            );
            CLI::write('✓ Ditemukan ' . count($hasil) . ' jadwal.', 'green');
        }

        CLI::newLine();

        // 6. Tampilkan SEMUA baris jadwal mentah (tanpa filter) biar gampang dibandingkan manual
        CLI::write('----------------------------------------------------', 'yellow');
        CLI::write(' SEMUA BARIS JADWAL (mentah, tanpa filter apapun)', 'yellow');
        CLI::write('----------------------------------------------------', 'yellow');

        $semuaJadwal = $db->table('jadwal')
            ->select('jadwal.id, jadwal.hari_id, hari.nama_hari, jadwal.tahun_pelajaran_id, jadwal.semester_id, jadwal.guru_id')
            ->join('hari', 'hari.id = jadwal.hari_id', 'left')
            ->orderBy('jadwal.id', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($semuaJadwal)) {
            CLI::write('Tabel jadwal masih kosong sama sekali.', 'red');
        } else {
            CLI::table(
                array_map(function ($r) {
                    return [$r['id'], $r['hari_id'], $r['nama_hari'] ?? '(hari_id invalid)', $r['tahun_pelajaran_id'], $r['semester_id'], $r['guru_id']];
                }, $semuaJadwal),
                ['ID', 'hari_id', 'nama_hari', 'tahun_pelajaran_id', 'semester_id', 'guru_id']
            );
        }

        CLI::newLine();
        CLI::write('Selesai.', 'yellow');
    }
}
