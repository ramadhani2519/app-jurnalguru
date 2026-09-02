<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel master Jenis Pelanggaran, dikelola oleh Wakasek Kesiswaan
 * (dan Admin) lewat menu Kesiswaan -> Jenis Pelanggaran. Dipakai
 * sebagai pilihan dropdown "Uraian" di form Input/Edit Pelanggaran
 * Siswa, supaya seragam (tidak ketik bebas tiap kali) -- kecuali
 * pilih "Lainnya" yang tetap bisa diisi bebas.
 */
class CreateJenisPelanggaranTable extends Migration
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
            'nama_pelanggaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->forge->createTable('jenis_pelanggaran');

        // Isi contoh awal supaya dropdown tidak kosong melompong.
        $db = \Config\Database::connect();

        $contoh = [
            'Terlambat masuk sekolah',
            'Tidak memakai atribut lengkap',
            'Tidak mengerjakan tugas',
            'Membolos / tidak masuk tanpa keterangan',
            'Merokok di lingkungan sekolah',
            'Berkelahi dengan teman',
        ];

        foreach ($contoh as $nama) {
            $db->table('jenis_pelanggaran')->insert([
                'nama_pelanggaran' => $nama,
                'created_at'       => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('jenis_pelanggaran');
    }
}
