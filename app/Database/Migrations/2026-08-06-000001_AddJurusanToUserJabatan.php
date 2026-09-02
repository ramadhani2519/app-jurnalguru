<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambahkan kolom `jurusan` pada tabel `user_jabatan`.
 *
 * Dipakai untuk menyimpan jurusan yang menjadi tanggung jawab seorang
 * guru dengan jabatan tambahan "Ketua Jurusan" (mirip pola `kelas_id`
 * yang sudah ada untuk jabatan "Wali Kelas"). Nilainya berupa teks
 * yang sama dengan `kelas.jurusan` (mis. "TKJ", "TO", "AKL").
 */
class AddJurusanToUserJabatan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user_jabatan', [
            'jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'kelas_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user_jabatan', 'jurusan');
    }
}
