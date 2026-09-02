<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');

$routes->post('login', 'Auth::login');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('kepala-sekolah','KepalaSekolah::dashboard');
$routes->get('kepsek/monitoring', 'KepalaSekolah::monitoring');

$routes->get('sekolah','Sekolah::index');
$routes->get('sekolah/edit','Sekolah::edit');
$routes->post('sekolah/update','Sekolah::update');


$routes->get('profile', 'Profile::index');
$routes->get('profile/edit', 'Profile::edit');
$routes->post('profile/update', 'Profile::update');



$routes->get('dashboard', 'Dashboard::index');

$routes->get('jurnal', 'Jurnal::index');
$routes->get('jurnal/tambah', 'Jurnal::create');
$routes->post('jurnal/simpan', 'Jurnal::store');

$routes->get('jurnal/edit/(:num)', 'Jurnal::edit/$1');
$routes->post('jurnal/update/(:num)', 'Jurnal::update/$1');
$routes->get('jurnal/laporan','Jurnal::laporan');
$routes->get('jurnal/cetakPdf','Jurnal::cetakPdf');
$routes->get('jurnal/export-excel', 'Jurnal::exportExcel');
$routes->get('jurnal/cetak-pembinaan-pdf', 'Jurnal::cetakPembinaanPdf');
$routes->get('jurnal/hapus/(:num)', 'Jurnal::delete/$1');


$routes->get('kelas', 'Kelas::index');
$routes->get('kelas/tambah', 'Kelas::create');
$routes->post('kelas/simpan', 'Kelas::store');
$routes->get('kelas/edit/(:num)', 'Kelas::edit/$1');
$routes->post('kelas/update/(:num)', 'Kelas::update/$1');
$routes->get('kelas/hapus/(:num)', 'Kelas::delete/$1');

$routes->get('jurusan', 'Jurusan::index');
$routes->get('jurusan/tambah', 'Jurusan::create');
$routes->post('jurusan/simpan', 'Jurusan::store');
$routes->get('jurusan/edit/(:num)', 'Jurusan::edit/$1');
$routes->post('jurusan/update/(:num)', 'Jurusan::update/$1');
$routes->get('jurusan/hapus/(:num)', 'Jurusan::delete/$1');

$routes->get('tahun', 'TahunPelajaran::index');
$routes->get('tahun/tambah', 'TahunPelajaran::create');
$routes->post('tahun/simpan', 'TahunPelajaran::store');
$routes->get('tahun/edit/(:num)', 'TahunPelajaran::edit/$1');
$routes->post('tahun/update/(:num)', 'TahunPelajaran::update/$1');
$routes->get('tahun/aktif/(:num)', 'TahunPelajaran::aktif/$1');
$routes->get('tahun/hapus/(:num)', 'TahunPelajaran::delete/$1');

$routes->get('semester', 'Semester::index');
$routes->get('semester/tambah', 'Semester::create');
$routes->post('semester/simpan', 'Semester::store');
$routes->get('semester/edit/(:num)', 'Semester::edit/$1');
$routes->post('semester/update/(:num)', 'Semester::update/$1');
$routes->get('semester/aktif/(:num)', 'Semester::aktif/$1');
$routes->get('semester/hapus/(:num)', 'Semester::delete/$1');

$routes->get('mapel', 'Mapel::index');
$routes->get('mapel/tambah', 'Mapel::create');
$routes->post('mapel/simpan', 'Mapel::store');
$routes->get('mapel/edit/(:num)', 'Mapel::edit/$1');
$routes->post('mapel/update/(:num)', 'Mapel::update/$1');
$routes->get('mapel/aktif/(:num)', 'Mapel::aktif/$1');
$routes->get('mapel/hapus/(:num)', 'Mapel::delete/$1');


$routes->get('user', 'User::index');
$routes->get('user/tambah', 'User::create');
$routes->post('user/simpan', 'User::store');
$routes->get('user/edit/(:num)', 'User::edit/$1');
$routes->post('user/update/(:num)', 'User::update/$1');
$routes->get('user/hapus/(:num)', 'User::delete/$1');

$routes->get('absensi','Absensi::index');
$routes->post('absensi/simpan','Absensi::simpan');
$routes->post('absensi/simpanMassal', 'Absensi::simpanMassal');

// Rekap Absensi
$routes->get('absensi/laporan-absen','Absensi::laporan');
$routes->get('absensi/cetak','Absensi::cetak');
$routes->get('absensi/cetak-rekap', 'Absensi::cetakRekap');
$routes->get('absensi/rekap-bulanan', 'Absensi::rekapBulanan');
$routes->get('absensi/export-rekap-bulanan', 'Absensi::exportRekapBulanan');

$routes->get('siswa','Siswa::index');
$routes->get('siswa/create','Siswa::create');
$routes->post('siswa/store','Siswa::store');
$routes->get('siswa/edit/(:num)', 'Siswa::edit/$1');

$routes->post('siswa/update/(:num)', 'Siswa::update/$1');

$routes->post('siswa/import','Siswa::import');
$routes->post('siswa/delete/(:num)', 'Siswa::delete/$1');

$routes->get('jam', 'Jam::index');
$routes->get('jam/create', 'Jam::create');
$routes->post('jam/save', 'Jam::save');
$routes->get('jam/edit/(:num)', 'Jam::edit/$1');
$routes->post('jam/update/(:num)', 'Jam::update/$1');
$routes->post('jam/delete/(:num)', 'Jam::delete/$1');

$routes->get('jadwal','Jadwal::index');
$routes->get('jadwal/create','Jadwal::create');
$routes->post('jadwal/store','Jadwal::store');

$routes->get('jadwal/edit/(:num)', 'Jadwal::edit/$1');
$routes->post('jadwal/update/(:num)', 'Jadwal::update/$1');
$routes->get('jadwal/delete/(:num)', 'Jadwal::delete/$1');

$routes->get('wali-kelas', 'WaliKelas::index');
$routes->get('wali-kelas/create', 'WaliKelas::create');
$routes->post('wali-kelas/store', 'WaliKelas::store');
$routes->get('wali-kelas/edit/(:num)', 'WaliKelas::edit/$1');
$routes->post('wali-kelas/update/(:num)', 'WaliKelas::update/$1');
$routes->get('wali-kelas/delete/(:num)', 'WaliKelas::delete/$1');

// Wakasek Kurikulum
$routes->get('guru-mengajar', 'GuruMengajar::index');

$routes->get('minggu-efektif', 'MingguEfektif::index');
$routes->get('minggu-efektif/detail/(:num)', 'MingguEfektif::detail/$1');
$routes->get('minggu-efektif/create/(:num)', 'MingguEfektif::create/$1');
$routes->post('minggu-efektif/store', 'MingguEfektif::store');
$routes->get('minggu-efektif/edit/(:num)', 'MingguEfektif::edit/$1');
$routes->post('minggu-efektif/update/(:num)', 'MingguEfektif::update/$1');
$routes->get('minggu-efektif/delete/(:num)', 'MingguEfektif::delete/$1');

$routes->get('realisasi-mengajar', 'RealisasiMengajar::index');
$routes->get('realisasi-mengajar/laporan/(:num)', 'RealisasiMengajar::laporan/$1');

$routes->get('ketua-kompetensi', 'KetuaKompetensi::index');
$routes->get('ketua-kompetensi/create', 'KetuaKompetensi::create');
$routes->post('ketua-kompetensi/store', 'KetuaKompetensi::store');
$routes->get('ketua-kompetensi/edit/(:num)', 'KetuaKompetensi::edit/$1');
$routes->post('ketua-kompetensi/update/(:num)', 'KetuaKompetensi::update/$1');
$routes->get('ketua-kompetensi/delete/(:num)', 'KetuaKompetensi::delete/$1');

// Wakasek Kesiswaan: distribusi siswa ke Guru Wali & rekap hasil pembinaan
$routes->get('kesiswaan', 'Kesiswaan::index');
$routes->get('kesiswaan/distribusi', 'Kesiswaan::distribusi');
$routes->post('kesiswaan/distribusi/simpan', 'Kesiswaan::simpanDistribusi');
$routes->get('kesiswaan/distribusi/hapus/(:num)', 'Kesiswaan::hapusDistribusi/$1');
$routes->get('kesiswaan/rekap', 'Kesiswaan::rekap');
$routes->get('kesiswaan/rekap/cetak', 'Kesiswaan::cetakRekap');

$routes->get('jenis-pelanggaran', 'JenisPelanggaran::index');
$routes->get('jenis-pelanggaran/tambah', 'JenisPelanggaran::create');
$routes->post('jenis-pelanggaran/simpan', 'JenisPelanggaran::store');
$routes->get('jenis-pelanggaran/edit/(:num)', 'JenisPelanggaran::edit/$1');
$routes->post('jenis-pelanggaran/update/(:num)', 'JenisPelanggaran::update/$1');
$routes->get('jenis-pelanggaran/hapus/(:num)', 'JenisPelanggaran::delete/$1');
$routes->get('kesiswaan/status-siswa', 'Kesiswaan::statusSiswa');
$routes->get('kesiswaan/pembinaan/detail/(:num)', 'Kesiswaan::detailPembinaan/$1');

// Guru Wali: lihat siswa asuh masing-masing
$routes->get('guru-wali/siswa-saya', 'GuruWali::siswaSaya');
$routes->get('guru-wali/pembinaan/tambah/(:num)', 'GuruWali::formPembinaan/$1');
$routes->post('guru-wali/pembinaan/simpan', 'GuruWali::simpanPembinaan');

// Wali Kelas: eskalasi pembinaan ronde ke-2, untuk siswa di kelas yang
// diwalikan yang sudah pernah dibina Guru Wali tapi melanggar lagi.
$routes->get('wali-kelas-binaan/siswa', 'WaliKelasBinaan::index');
$routes->get('wali-kelas-binaan/pembinaan/tambah/(:num)', 'WaliKelasBinaan::formPembinaan/$1');
$routes->post('wali-kelas-binaan/pembinaan/simpan', 'WaliKelasBinaan::simpanPembinaan');

// Ketua Jurusan: eskalasi pembinaan ronde ke-3, untuk siswa yang sudah
// dibina Guru Wali + Wali Kelas tapi melanggar lagi.
$routes->get('ketua-jurusan-binaan/siswa', 'KetuaJurusanBinaan::index');
$routes->get('ketua-jurusan-binaan/pembinaan/tambah/(:num)', 'KetuaJurusanBinaan::formPembinaan/$1');
$routes->post('ketua-jurusan-binaan/pembinaan/simpan', 'KetuaJurusanBinaan::simpanPembinaan');

// Absensi Mapel (per sesi mengajar guru mapel, beda dari Absensi wali kelas)
$routes->get('absensi-mapel', 'AbsensiMapel::index');
$routes->get('absensi-mapel/rekap', 'AbsensiMapel::rekap');
$routes->get('absensi-mapel/export-rekap', 'AbsensiMapel::exportRekap');
$routes->post('absensi-mapel/simpan', 'AbsensiMapel::simpan');
$routes->post('absensi-mapel/simpanMassal', 'AbsensiMapel::simpanMassal');
$routes->get('absensi-mapel/cetak', 'AbsensiMapel::cetak');

$routes->get('absensi-sholat','AbsensiSholat::index');
$routes->post('absensi-sholat/simpan','AbsensiSholat::simpan');
$routes->post('absensi-sholat/simpanMassal', 'AbsensiSholat::simpanMassal');

$routes->get('absensi-sholat/rekap-bulanan', 'AbsensiSholat::rekapBulanan');
$routes->get('absensi-sholat/export-rekap-bulanan', 'AbsensiSholat::exportRekapBulanan');

$routes->group('pelanggaran', function($routes){

    $routes->get('/', 'Pelanggaran::index');

    $routes->get('create', 'Pelanggaran::create');

    $routes->post('save', 'Pelanggaran::save');

    $routes->get('edit/(:num)', 'Pelanggaran::edit/$1');

    $routes->post('update/(:num)', 'Pelanggaran::update/$1');

    $routes->get('delete/(:num)', 'Pelanggaran::delete/$1');
    $routes->get('cetak/(:num)', 'Pelanggaran::cetak/$1');
    $routes->get('cetakPdf', 'Pelanggaran::cetakPdf');
    $routes->get('export-excel', 'Pelanggaran::exportExcel');

});
