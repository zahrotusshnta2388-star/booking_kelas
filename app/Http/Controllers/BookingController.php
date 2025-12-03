<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // ========================
    // METHOD UNTUK PUBLIK
    // ========================
    
    /**
     * Menampilkan form booking untuk publik
     */
    public function createPublik()
    {
        $ruangans = Ruangan::where('status', 'tersedia')->get();
        return view('booking.publik', compact('ruangans'));
    }
    
    /**
     * Menyimpan booking dari publik
     */
    public function storePublik(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'keperluan' => 'required|string|max:1000',
            'jumlah_peserta' => 'nullable|integer|min:1',
            'syarat_ketentuan' => 'required|accepted',
            'konfirmasi_data' => 'required|accepted',
        ]);

        // PERBAIKAN: Handle nilai NIM yang tidak valid
    if (isset($validated['nim']) && ($validated['nim'] == '?' || empty(trim($validated['nim'])))) {
        $validated['nim'] = '-';
    }

        // Cek ketersediaan ruangan
        $isAvailable = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', 'disetujui')
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })
            ->doesntExist();

        if (!$isAvailable) {
            return back()->withErrors([
                'jam_mulai' => 'Ruangan sudah dipesan pada jam tersebut.',
            ])->withInput();
        }

        // Simpan booking ke database
        Booking::create([
            'ruangan_id' => $request->ruangan_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'nama_peminjam' => $request->nama_peminjam,
            'nim' => $request->nim,
            'keperluan' => $request->keperluan,
            'no_hp' => $request->no_hp,
            'pemesan_email' => $request->email, // Mapping dari 'email' ke 'pemesan_email'
            'status' => 'menunggu',
            'jumlah_peserta' => $request->jumlah_peserta,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('booking.publik')
            ->with('success', 'Pengajuan booking berhasil dikirim! Teknisi akan menghubungi Anda dalam waktu 1x24 jam.');
    }
    
    /**
     * Menampilkan jadwal untuk publik
     */
    public function jadwalPublik()
    {
        $bookings = Booking::with('ruangan')
            ->where('status', 'disetujui')
            ->where('tanggal', '>=', now()->format('Y-m-d'))
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();
            
        return view('booking.jadwal-publik', compact('bookings'));
    }

    // ========================
    // METHOD UNTUK TEKNISI (CRUD)
    // ========================
    
    /**
     * Menampilkan semua booking untuk teknisi
     */
    public function index()
    {
        $bookings = Booking::with('ruangan')->orderBy('created_at', 'desc')->get();
        return view('booking.index', compact('bookings'));
    }
    
    /**
     * Menampilkan form booking untuk teknisi
     */
    public function create()
    {
        $ruangans = Ruangan::all();
        return view('booking.create', compact('ruangans'));
    }
    
    /**
     * Menyimpan booking dari teknisi
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => ($request->nim == '?' || empty($request->nim)) ? '-' : $request->nim, // FIX            'no_hp' => 'required|string|max:20',
            'keperluan' => 'required|string|max:1000',
            'jumlah_peserta' => 'nullable|integer|min:1',
            'status' => 'required|in:menunggu,disetujui,ditolak',
        ]);
        
        // Cek ketersediaan jika status disetujui
        if ($request->status == 'disetujui') {
            $isAvailable = Booking::where('ruangan_id', $request->ruangan_id)
                ->where('tanggal', $request->tanggal)
                ->where('status', 'disetujui')
                ->where('id', '!=', $request->id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                          ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                          ->orWhere(function($q) use ($request) {
                              $q->where('jam_mulai', '<=', $request->jam_mulai)
                                ->where('jam_selesai', '>=', $request->jam_selesai);
                          });
                })
                ->doesntExist();
                
            if (!$isAvailable) {
                return back()->withErrors([
                    'jam_mulai' => 'Ruangan sudah dipesan pada jam tersebut.',
                ])->withInput();
            }
        }
        
        // Simpan booking
        Booking::create($validated);
        
        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil dibuat!');
    }
    
    /**
     * Menampilkan detail booking
     */
    public function show(Booking $booking)
    {
        return view('booking.show', compact('booking'));
    }
    
    /**
     * Menampilkan form edit booking
     */
    public function edit(Booking $booking)
    {
        $ruangans = Ruangan::all();
        return view('booking.edit', compact('booking', 'ruangans'));
    }
    
    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'pemesan_email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'keperluan' => 'required|string|max:1000',
            'jumlah_peserta' => 'nullable|integer|min:1',
            'status' => 'required|in:menunggu,disetujui,ditolak',
        ]);
        
        // Cek ketersediaan jika status disetujui
        if ($request->status == 'disetujui') {
            $isAvailable = Booking::where('ruangan_id', $request->ruangan_id)
                ->where('tanggal', $request->tanggal)
                ->where('status', 'disetujui')
                ->where('id', '!=', $booking->id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                          ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                          ->orWhere(function($q) use ($request) {
                              $q->where('jam_mulai', '<=', $request->jam_mulai)
                                ->where('jam_selesai', '>=', $request->jam_selesai);
                          });
                })
                ->doesntExist();
                
            if (!$isAvailable) {
                return back()->withErrors([
                    'jam_mulai' => 'Ruangan sudah dipesan pada jam tersebut.',
                ])->withInput();
            }
        }
        
        $booking->update($validated);
        
        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil diupdate!');
    }
    
    /**
     * Hapus booking
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        
        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil dihapus!');
    }
    
    /**
     * Update status booking
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,batal',
            'catatan' => 'nullable|string|max:1000',
        ]);
        
        // Jika menyetujui, cek ketersediaan
        if ($request->status == 'disetujui') {
            $isAvailable = Booking::where('ruangan_id', $booking->ruangan_id)
                ->where('tanggal', $booking->tanggal)
                ->where('status', 'disetujui')
                ->where('id', '!=', $booking->id)
                ->where(function($query) use ($booking) {
                    $query->whereBetween('jam_mulai', [$booking->jam_mulai, $booking->jam_selesai])
                          ->orWhereBetween('jam_selesai', [$booking->jam_mulai, $booking->jam_selesai])
                          ->orWhere(function($q) use ($booking) {
                              $q->where('jam_mulai', '<=', $booking->jam_mulai)
                                ->where('jam_selesai', '>=', $booking->jam_selesai);
                          });
                })
                ->doesntExist();
                
            if (!$isAvailable) {
                return back()->withErrors([
                    'status' => 'Ruangan sudah dipesan pada jam tersebut.',
                ])->withInput();
            }
        }
        
        $booking->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);
        
        return back()->with('success', 'Status booking berhasil diupdate!');
    }
}