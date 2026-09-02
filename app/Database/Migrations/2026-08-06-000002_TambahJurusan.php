<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambahkan konsep "jurusan" supaya Ketua Jurusan bisa dibatasi hanya
 * memantau siswa di jurusannya sendiri:
 * - kelas.jurusan       : nama jurusan kelas itu (mis. "TKJ", "TJKT").
 * - user_jabatan.jurusan: jurusan yang diampu, diisi kalau jabatan user
 *                         itu adalah "Ketua Jurusan" (mirip kelas_id
 *                         yang diisi untuk jabatan "Wali Kelas").
 */
class TambahJurusan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kelas', [
            'jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nama_kelas',
            ],
        ]);

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
        $this->forge->dropColumn('kelas', ['jurusan']);
        $this->forge->dropColumn('user_jabatan', ['jurusan']);
    }
}
