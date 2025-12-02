<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ruangan; // <-- PASTIKAN INI ADA
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Method untuk halaman booking publik
    public function createPublik()
    {
        // Ambil data ruangan
        $ruangans = Ruangan::where('status', 'tersedia')->get();
        
        return view('booking.publik', compact('ruangans'));
    }
    
    // Method untuk menyimpan booking dari publik
    public function storePublik(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date|after_or_equal:tomorrow',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'email' => 'required|email',
            'no_hp' => 'required|string|max:20',
            'keperluan' => 'required|string|max:1000',
            'jumlah_peserta' => 'nullable|integer|min:1',
            'syarat_ketentuan' => 'required|accepted',
            'konfirmasi_data' => 'required|accepted',
        ]);

        // Cek ketersediaan
        $isAvailable = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', 'disetujui')
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai]);
            })
            ->doesntExist();

        if (!$isAvailable) {
            return back()->withErrors([
                'jam_mulai' => 'Ruangan sudah dipesan pada jam tersebut.',
            ])->withInput();
        }

        // Simpan booking
        Booking::create([
            'ruangan_id' => $request->ruangan_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'nama_peminjam' => $request->nama_peminjam,
            'nim' => $request->nim,
            'keperluan' => $request->keperluan,
            'no_hp' => $request->no_hp,
            'pemesan_email' => $request->email,
            'status' => 'menunggu',
            'jumlah_peserta' => $request->jumlah_peserta,
        ]);

        return redirect()->route('booking.publik')
            ->with('success', 'Pengajuan booking berhasil dikirim! Teknisi akan menghubungi Anda dalam waktu 1x24 jam.');
    }
}