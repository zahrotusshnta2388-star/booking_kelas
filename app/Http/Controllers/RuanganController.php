<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RuanganController extends Controller
{
    // ========== METHOD UNTUK PUBLIK ==========
    public function publik(Request $request)
    {
        // 1. Validasi dan ambil tanggal
        $selectedDate = $request->query('date') ?? now()->format('Y-m-d');

        // 2. Dapatkan semua ruangan yang tersedia, urutkan lantai dan nama
        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // 3. Dapatkan booking untuk tanggal yang dipilih
        $bookings = Booking::with('ruangan')
            ->where('tanggal', $selectedDate)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // 4. Buat array jam dari 07:00 sampai 17:00 (1 jam interval)
        $jamSlots = [];
        for ($hour = 7; $hour <= 17; $hour++) {
            $jamSlots[] = sprintf('%02d:00', $hour);
        }

        // 5. Mapping booking ke ruangan dan jam (FIXED - untuk colspan)
        $bookingMap = [];
        $occupiedSlots = []; // Untuk melacak slot yang sudah ditempati

        foreach ($bookings as $booking) {
            try {
                $ruanganId = $booking->ruangan_id;

                // Parse jam_mulai dan jam_selesai
                $jamMulai = (string) $booking->jam_mulai;
                $jamSelesai = (string) $booking->jam_selesai;

                // Bersihkan format (hapus tanggal jika ada)
                $jamMulai = preg_replace('/^\d{4}-\d{2}-\d{2}\s*/', '', $jamMulai);
                $jamSelesai = preg_replace('/^\d{4}-\d{2}-\d{2}\s*/', '', $jamSelesai);

                // Ambil jam saja (HH dari HH:MM:SS)
                $startHour = (int) substr($jamMulai, 0, 2);
                $endHour = (int) substr($jamSelesai, 0, 2);

                // Validasi jam
                if ($startHour < 7) $startHour = 7;
                if ($endHour > 17) $endHour = 17;
                if ($startHour >= $endHour) $endHour = $startHour + 1;

                // Hitung durasi (berapa jam)
                $duration = $endHour - $startHour;
                if ($duration <= 0) $duration = 1;

                // Slot key untuk jam mulai
                $startSlotKey = sprintf('%02d:00', $startHour);

                // Inisialisasi array jika belum ada
                if (!isset($bookingMap[$ruanganId])) {
                    $bookingMap[$ruanganId] = [];
                }

                if (!isset($occupiedSlots[$ruanganId])) {
                    $occupiedSlots[$ruanganId] = [];
                }

                // Simpan booking di slot mulai dengan colspan
                $bookingMap[$ruanganId][$startSlotKey] = [
                    'booking' => $booking,
                    'span' => $duration, // Jumlah cell yang akan di-span
                    'startHour' => $startHour,
                    'endHour' => $endHour,
                    'jam_mulai_display' => substr($jamMulai, 0, 5),
                    'jam_selesai_display' => substr($jamSelesai, 0, 5)
                ];

                // Tandai semua slot yang akan di-cover oleh colspan
                for ($h = $startHour; $h < $endHour; $h++) {
                    $slotKey = sprintf('%02d:00', $h);
                    $occupiedSlots[$ruanganId][$slotKey] = true;
                }
            } catch (\Exception $e) {
                \Log::error("Error processing booking: " . $e->getMessage());
                continue;
            }
        }

        // 6. Kirim data ke view
        return view('publik.ruangan', compact(
            'ruangans',
            'bookings',
            'jamSlots',
            'bookingMap',
            'selectedDate',
            'occupiedSlots' // Kirim juga occupiedSlots ke view
        ));
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

        $bookings = Booking::with(['ruangan', 'user']) // <-- TAMBAH 'user' juga di sini
            ->whereIn('tanggal', [$today, $tomorrow])
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('publik.jadwal-ruangan', compact('ruangans', 'bookings'));
    }

    /**
     * Method helper untuk dummy data ruangan
     */
    private function createDummyRuangan()
    {
        $dummyData = [
            ['nama' => 'Ruang Rapat 1', 'lantai' => 1, 'kode' => 'RR1', 'kapasitas' => 20, 'status' => 'tersedia'],
            ['nama' => 'Lab Komputer', 'lantai' => 2, 'kode' => 'LK1', 'kapasitas' => 30, 'status' => 'tersedia'],
            ['nama' => 'Ruang Kelas 301', 'lantai' => 3, 'kode' => 'RK301', 'kapasitas' => 40, 'status' => 'tersedia'],
        ];

        foreach ($dummyData as $data) {
            Ruangan::firstOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }

        return Ruangan::where('status', 'tersedia')->get();
    }
}
