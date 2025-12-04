<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Ruangan;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $ruangan = Ruangan::first();
        $today = Carbon::now()->format('Y-m-d');

        // Hapus data lama jika ada
        Booking::truncate();

        // Data dummy 1
        Booking::create([
            'ruangan_id' => $ruangan->id,
            'tanggal' => $today,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'nama_peminjam' => 'Budi Santoso',
            'keperluan' => 'Kuliah Web Programming',
            'status' => 'disetujui',
            'nim' => '20210001',
            'no_hp' => '081234567890',
            'jumlah_peserta' => 25,
            'pemesan_email' => 'budi@example.com'
        ]);

        // Data dummy 2
        Booking::create([
            'ruangan_id' => $ruangan->id,
            'tanggal' => $today,
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'nama_peminjam' => 'Siti Aisyah',
            'keperluan' => 'Praktikum Jaringan',
            'status' => 'menunggu',
            'nim' => '20210002',
            'no_hp' => '081298765432',
            'jumlah_peserta' => 20,
            'pemesan_email' => 'siti@example.com'
        ]);

        // Data dummy 3
        Booking::create([
            'ruangan_id' => $ruangan->id,
            'tanggal' => $today,
            'jam_mulai' => '15:00',
            'jam_selesai' => '17:00',
            'nama_peminjam' => 'Ahmad Fauzi',
            'keperluan' => 'Rapat Prodi TI',
            'status' => 'disetujui',
            'nim' => '20210003',
            'no_hp' => '081212345678',
            'jumlah_peserta' => 15,
            'pemesan_email' => 'ahmad@example.com'
        ]);

        $this->command->info('✅ Booking dummy berhasil dibuat!');
    }
}
