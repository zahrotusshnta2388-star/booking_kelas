<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        $ruangans = [
            // Lantai 2
            [
                'nama' => 'lt 2 Workshop RSI',
                'kode' => 'JTI-201-WRSI',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 30,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Komputer', 'Whiteboard', 'Kursi 30', 'Meja Instruktur']),
                'deskripsi' => 'Workshop untuk praktikum Rekayasa Sistem Informasi',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 Lab RSI',
                'kode' => 'JTI-202-LRSI',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 25,
                'fasilitas' => json_encode(['Proyektor', 'AC', '25 Komputer', 'Jaringan LAN', 'Printer']),
                'deskripsi' => 'Laboratorium untuk praktikum RSI',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 Lab RPL',
                'kode' => 'JTI-203-LRPL',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 25,
                'fasilitas' => json_encode(['Proyektor', 'AC', '25 Komputer', 'Software Development Tools']),
                'deskripsi' => 'Laboratorium untuk praktikum Rekayasa Perangkat Lunak',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 Workshop KSI',
                'kode' => 'JTI-204-WKSI',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 30,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Komputer', 'Whiteboard', 'Tools Jaringan']),
                'deskripsi' => 'Workshop untuk praktikum Keamanan Sistem Informasi',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 Lab KSI',
                'kode' => 'JTI-205-LKSI',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 25,
                'fasilitas' => json_encode(['Proyektor', 'AC', '25 Komputer', 'Security Tools', 'Server Rack']),
                'deskripsi' => 'Laboratorium untuk praktikum Keamanan SI',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 Workshop AJK',
                'kode' => 'JTI-206-WAJK',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 30,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Komputer', 'Whiteboard', 'Tools Jaringan']),
                'deskripsi' => 'Workshop untuk praktikum Administrasi Jaringan Komputer',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 lab AJK',
                'kode' => 'JTI-207-LAJK',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 25,
                'fasilitas' => json_encode(['Proyektor', 'AC', '25 Komputer', 'Network Equipment', 'Server']),
                'deskripsi' => 'Laboratorium untuk praktikum Administrasi Jaringan',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 workshop JHJ',
                'kode' => 'JTI-208-WJHJ',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 30,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Komputer', 'Whiteboard', 'Multimedia Equipment']),
                'deskripsi' => 'Workshop untuk praktikum Jaringan Hardware',
                'status' => 'tersedia'
            ],
            [
                'nama' => 'lt 2 lab JHJ',
                'kode' => 'JTI-209-LJHJ',
                'gedung' => 'Gedung JTI',
                'lantai' => 2,
                'kapasitas' => 25,
                'fasilitas' => json_encode(['Proyektor', 'AC', '25 Komputer', 'Hardware Tools', 'Testing Equipment']),
                'deskripsi' => 'Laboratorium untuk praktikum Jaringan Hardware',
                'status' => 'tersedia'
            ],

            // Lantai 3 - Ruangan 3.1 sampai 3.12
        ];

        // Generate ruangan lantai 3 (3.1 - 3.12)
        for ($i = 1; $i <= 12; $i++) {
            $ruangans[] = [
                'nama' => "lt 3 Ruangan 3.{$i}",
                'kode' => "JTI-3" . sprintf('%02d', $i),
                'gedung' => 'Gedung JTI',
                'lantai' => 3,
                'kapasitas' => 40,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Sound System', 'Whiteboard', 'Kursi 40', 'Meja']),
                'deskripsi' => "Ruangan kelas lantai 3 nomor {$i} untuk perkuliahan",
                'status' => 'tersedia'
            ];
        }

        // Lantai 4 - Ruangan 4.1 sampai 4.3
        for ($i = 1; $i <= 3; $i++) {
            $ruangans[] = [
                'nama' => "lt 4 Ruangan 4.{$i}",
                'kode' => "JTI-4" . sprintf('%02d', $i),
                'gedung' => 'Gedung JTI',
                'lantai' => 4,
                'kapasitas' => 35,
                'fasilitas' => json_encode(['Proyektor', 'AC', 'Sound System', 'Whiteboard', 'Kursi 35', 'Meja Seminar']),
                'deskripsi' => "Ruangan lantai 4 nomor {$i} untuk seminar dan meeting",
                'status' => 'tersedia'
            ];
        }

        // Insert ke database
        DB::table('ruangans')->insert($ruangans);

        $this->command->info('Seeder ruangan berhasil ditambahkan!');
        $this->command->info('Total ruangan: ' . count($ruangans));
    }
}
