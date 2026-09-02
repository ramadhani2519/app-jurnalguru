<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel master Jurusan, supaya admin bisa kelola daftar jurusan dari
 * satu tempat (menu Master Data -> Jurusan) dan dipakai sebagai
 * dropdown di form Tambah/Edit User (jabatan "Ketua Jurusan").
 */
class CreateJurusanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_jurusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('jurusan');

        // Isi awal dari nilai kelas.jurusan yang sudah ada, supaya
        // data lama tidak hilang begitu dropdown ini mulai dipakai.
        $db = \Config\Database::connect();

        $existing = $db->table('kelas')
            ->select('jurusan')
            ->where('jurusan IS NOT NULL')
            ->where('jurusan !=', '')
            ->groupBy('jurusan')
            ->get()
            ->getResultArray();

        foreach ($existing as $row) {
            $nama = trim($row['jurusan']);

            if ($nama === '') {
                continue;
            }

            $db->table('jurusan')->insert([
                'nama_jurusan' => $nama,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('jurusan');
    }
}
