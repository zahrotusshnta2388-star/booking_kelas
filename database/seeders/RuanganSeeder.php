<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        $ruangans = [
            [
                'nama' => 'Lab Komputer 1',
                'kode' => 'LAB-KOM-01',
                'gedung' => 'A',
                'lantai' => 1,
                'kapasitas' => 30,
                'fasilitas' => json_encode(['AC', 'LCD Projector', 'Komputer', 'Whiteboard']),
                'deskripsi' => 'Laboratorium komputer dengan 30 unit PC',
                'status' => 'tersedia',
            ],
            [
                'nama' => 'Ruang Kelas 101',
                'kode' => 'RK-101',
                'gedung' => 'A',
                'lantai' => 1,
                'kapasitas' => 40,
                'fasilitas' => json_encode(['AC', 'LCD Projector', 'Whiteboard', 'Sound System']),
                'deskripsi' => 'Ruang kelas teori kapasitas 40 orang',
                'status' => 'tersedia',
            ],
            [
                'nama' => 'Auditorium',
                'kode' => 'AUD-01',
                'gedung' => 'B',
                'lantai' => 1,
                'kapasitas' => 100,
                'fasilitas' => json_encode(['AC', 'Sound System', 'Stage', 'Microphone', 'Lighting']),
                'deskripsi' => 'Ruang pertemuan besar untuk seminar dan workshop',
                'status' => 'tersedia',
            ],
            [
                'nama' => 'Ruang Rapat',
                'kode' => 'RR-201',
                'gedung' => 'B',
                'lantai' => 2,
                'kapasitas' => 20,
                'fasilitas' => json_encode(['AC', 'TV', 'Whiteboard', 'Meja Rapat']),
                'deskripsi' => 'Ruang rapat kecil untuk diskusi tim',
                'status' => 'tersedia',
            ],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::create($ruangan);
        }
    }
}