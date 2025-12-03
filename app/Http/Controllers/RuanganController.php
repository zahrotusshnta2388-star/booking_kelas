<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Booking;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    // ========== METHOD UNTUK PUBLIK ==========
    public function publik()
    {
        // Dapatkan semua ruangan yang tersedia, urutkan lantai dan nama
        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // Jika belum ada data ruangan, jalankan seeder atau buat dummy
        if ($ruangans->count() == 0) {
            \Artisan::call('db:seed', ['--class' => 'RuanganSeeder']);
            $ruangans = Ruangan::where('status', 'tersedia')
                ->orderBy('lantai', 'asc')
                ->orderBy('nama', 'asc')
                ->get();
        }

        // Dapatkan booking untuk hari ini
        $today = now()->format('Y-m-d');
        $bookings = Booking::with('ruangan')
            ->where('tanggal', $today)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Buat array jam dari 07:00 sampai 17:00 (1 jam interval)
        $jamSlots = [];
        for ($hour = 7; $hour <= 17; $hour++) {
            $jamSlots[] = sprintf('%02d:00', $hour);
        }

        // Mapping booking ke ruangan dan jam
        $bookingMap = [];
        foreach ($bookings as $booking) {
            $ruanganId = $booking->ruangan_id;
            $jamMulai = substr($booking->jam_mulai, 0, 5); // Format HH:MM
            $jamSelesai = substr($booking->jam_selesai, 0, 5);

            // Tentukan slot jam yang dipakai
            $startHour = (int)substr($jamMulai, 0, 2);
            $endHour = (int)substr($jamSelesai, 0, 2);

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                $slot = sprintf('%02d:00', $hour);
                if (!isset($bookingMap[$ruanganId])) {
                    $bookingMap[$ruanganId] = [];
                }
                $bookingMap[$ruanganId][$slot] = [
                    'booking' => $booking,
                    'span' => $endHour - $startHour, // Berapa jam durasinya
                    'startSlot' => $startHour
                ];
            }
        }

        return view('publik.ruangan', compact('ruangans', 'bookings', 'jamSlots', 'bookingMap'));
    }


    // ========== METHOD UNTUK TEKNISI (CRUD) ==========

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangans = Ruangan::all();
        return view('teknisi.ruangan.index', compact('ruangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangans = Ruangan::where('status', 'tersedia')->get();
        return view('teknisi.ruangan.create', compact('ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:ruangans',
            'gedung' => 'required|string|max:10',
            'lantai' => 'required|integer',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ]);

        $validated['fasilitas'] = json_encode($request->fasilitas ?? []);

        Ruangan::create($validated);

        return redirect()->route('ruangan.index')
            ->with('success', 'Ruangan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruangan $ruangan)
    {
        return view('teknisi.ruangan.show', compact('ruangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruangan $ruangan)
    {
        return view('teknisi.ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:ruangans,kode,' . $ruangan->id,
            'gedung' => 'required|string|max:10',
            'lantai' => 'required|integer',
            'kapasitas' => 'required|integer',
            'fasilitas' => 'nullable|array',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,tidak_tersedia',
        ]);

        $validated['fasilitas'] = json_encode($request->fasilitas ?? []);

        $ruangan->update($validated);

        return redirect()->route('ruangan.index')
            ->with('success', 'Ruangan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->route('ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus!');
    }

    // ========== METHOD TAMBAHAN ==========

    /**
     * Check availability of a room
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $booking = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->where('status', 'disetujui')
            ->exists();

        return response()->json([
            'available' => !$booking,
            'message' => $booking ? 'Ruangan sudah dipesan' : 'Ruangan tersedia'
        ]);
    }


    public function jadwalRuangan()
    {
        // Ambil semua ruangan
        $ruangans = Ruangan::orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // Ambil data booking untuk hari ini dan besok
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');

        $bookings = Booking::with('ruangan')
            ->whereIn('tanggal', [$today, $tomorrow])
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('publik.jadwal-ruangan', compact('ruangans', 'bookings'));
    }
}
