<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ========================
    // METHOD UNTUK PUBLIK
    // ========================

    /**
     * Show form untuk booking dari publik
     */
    public function create()
    {
        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        return view('publik.booking', [
            'ruangans' => $ruangans,
            'activePage' => 'booking'
        ]);
    }

    /**
     * Store booking dari publik
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:15',
            'keperluan' => 'required|string|max:500',
            'jumlah_peserta' => 'nullable|integer|min:1',
        ]);

        // Cek ketersediaan ruangan
        $isAvailable = $this->checkRoomAvailability(
            $validated['ruangan_id'],
            $validated['tanggal'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if (!$isAvailable) {
            return back()->withErrors([
                'jam_mulai' => 'Ruangan sudah dipesan pada waktu tersebut.'
            ])->withInput();
        }

        // Buat booking dengan status default 'menunggu'
        $booking = Booking::create([
            'ruangan_id' => $validated['ruangan_id'],
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'nama_peminjam' => $validated['nama_peminjam'],
            'nim' => $validated['nim'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'keperluan' => $validated['keperluan'],
            'jumlah_peserta' => $validated['jumlah_peserta'] ?? 1,
            'status' => 'menunggu',
            'pemesan_email' => Auth::check() ? Auth::user()->email : null,
            'user_id' => Auth::id() // Tambahkan user_id
        ]);

        return redirect('/jadwal-ruangan')
            ->with('success', 'Booking berhasil diajukan! Menunggu konfirmasi teknisi.');
    }

    /**
     * Show form jadwal publik
     */
    public function jadwalPublik()
    {
        return view('publik.jadwal', ['activePage' => 'jadwal']);
    }

    // ========================
    // METHOD UNTUK SEMUA USER (DETAIL, EDIT, HAPUS)
    // ========================

    /**
     * Show detail booking (JSON untuk modal)
     */
    public function show($id)
    {
        $booking = Booking::with('ruangan', 'user')->findOrFail($id);

        // Authorization: cek apakah user berhak melihat
        if (!Auth::check() || (Auth::id() != $booking->user_id && Auth::user()->role !== 'teknisi')) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($booking);
    }

    /**
     * Show form untuk edit booking
     */
    public function edit($id)
    {
        $booking = Booking::with('ruangan')->findOrFail($id);

        // Authorization: cek apakah user berhak edit
        if (Auth::id() != $booking->user_id && Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized action.');
        }

        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // Tentukan view berdasarkan role user
        if (Auth::user()->role === 'teknisi') {
            // PERBAIKAN DI SINI: 'teknisi.bookings.edit' bukan 'teknisi.booking.edit'
            return view('teknisi.bookings.edit', compact('booking', 'ruangans'));
        } else {
            return view('publik.booking-edit', compact('booking', 'ruangans'));
        }
    }

    /**
     * Update booking
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Authorization: cek apakah user berhak update
        if (Auth::id() != $booking->user_id && Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized action.');
        }

        // Validasi untuk user biasa (tanpa field status)
        $validationRules = [
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'no_hp' => 'required|string|max:15',
            'keperluan' => 'required|string|max:500',
            'jumlah_peserta' => 'required|integer|min:1',
        ];

        // Tambah validasi email hanya jika ada fieldnya
        if ($request->has('pemesan_email')) {
            $validationRules['pemesan_email'] = 'required|email';
        }

        // Tambah validasi catatan hanya jika ada fieldnya
        if ($request->has('catatan')) {
            $validationRules['catatan'] = 'nullable|string';
        }

        $validated = $request->validate($validationRules);

        // Cek ketersediaan ruangan (kecuali jika tidak berubah)
        if (
            $booking->ruangan_id != $validated['ruangan_id'] ||
            $booking->tanggal != $validated['tanggal'] ||
            $booking->jam_mulai != $validated['jam_mulai'] ||
            $booking->jam_selesai != $validated['jam_selesai']
        ) {

            $isAvailable = $this->checkRoomAvailability(
                $validated['ruangan_id'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $booking->id
            );

            if (!$isAvailable) {
                return back()->withErrors([
                    'jam_mulai' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ])->withInput();
            }
        }

        // Update hanya field yang ada di validated data
        $booking->update($validated);

        // Redirect berdasarkan role user
        if (Auth::user()->role === 'teknisi') {
            return redirect()->route('teknisi.bookings.index')
                ->with('success', 'Booking berhasil diperbarui!');
        } else {
            return redirect()->route('booking.my')
                ->with('success', 'Booking berhasil diperbarui!');
        }
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Authorization: cek apakah user berhak hapus
        if (Auth::id() != $booking->user_id && Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized action.');
        }

        $booking->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dihapus!'
            ]);
        }

        return redirect()->route('booking.my')
            ->with('success', 'Booking berhasil dihapus!');
    }

    /**
     * Approve booking (khusus teknisi)
     */
    public function approve($id)
    {
        // Hanya teknisi yang bisa approve
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized action.');
        }

        $booking = Booking::findOrFail($id);

        // Cek ketersediaan sebelum approve
        $isAvailable = $this->checkRoomAvailability(
            $booking->ruangan_id,
            $booking->tanggal,
            $booking->jam_mulai,
            $booking->jam_selesai,
            $booking->id
        );

        if (!$isAvailable) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ], 422);
            }
            return back()->withErrors(['message' => 'Ruangan sudah dipesan pada waktu tersebut.']);
        }

        $booking->update(['status' => 'disetujui']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil disetujui!'
            ]);
        }

        return redirect()->back()->with('success', 'Booking berhasil disetujui!');
    }

    // ========================
    // METHOD UNTUK TEKNISI (CRUD)
    // ========================

    /**
     * Display a listing of the resource (untuk teknisi).
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $selectedDate = $request->query('date') ?? now()->format('Y-m-d');

        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $bookings = Booking::with('ruangan')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(20);

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

        // PERBAIKAN DI SINI: 'teknisi.bookings.index' bukan 'teknisi.booking.index'
        return view('publik.ruangan', [
            'ruangans' => $ruangans,
            'bookings' => $bookings,
            'jamSlots' => $jamSlots,
            'bookingMap' => $bookingMap,
            'selectedDate' => $selectedDate,
            'occupiedSlots' => $occupiedSlots,
            'activePage' => 'booking',
        ]);
    }

    /**
     * Show the form for creating a new resource (untuk teknisi).
     */
    public function createTeknisi()
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // PERBAIKAN DI SINI: 'teknisi.bookings.create' bukan 'teknisi.booking.create'
        return view('teknisi.bookings.create', [
            'ruangans' => $ruangans,
            'activePage' => 'booking'
        ]);
    }

    /**
     * Store a newly created resource (untuk teknisi).
     */
    public function storeTeknisi(Request $request)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:15',
            'keperluan' => 'required|string|max:500',
            'status' => 'required|in:menunggu,disetujui,ditolak',
            'jumlah_peserta' => 'nullable|integer|min:1',
        ]);

        // Cek ketersediaan (kecuali untuk booking yang ditolak)
        if ($validated['status'] !== 'ditolak') {
            $isAvailable = $this->checkRoomAvailability(
                $validated['ruangan_id'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai']
            );

            if (!$isAvailable) {
                return back()->withErrors([
                    'jam_mulai' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ])->withInput();
            }
        }

        Booking::create(array_merge($validated, [
            'pemesan_email' => Auth::user()->email,
            'user_id' => Auth::id()
        ]));

        return redirect()->route('ruangan.publik')
            ->with('success', 'Booking berhasil ditambahkan!');
    }

    // ========================
// METHOD UNTUK BULK BOOKING (TEKNISI)
// ========================

    /**
     * Show form untuk booking massal
     */
    public function createBulk()
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        return view('teknisi.bookings.create-bulk', [
            'ruangans' => $ruangans,
            'activePage' => 'booking'
        ]);
    }

    /**
     * Store booking massal
     */
    public function storeBulk(Request $request)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'hari' => 'required|array',
            'hari.*' => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'no_hp' => 'required|string|max:15',
            'keperluan' => 'required|string|max:500',
            'jumlah_peserta' => 'nullable|integer|min:1',
        ]);

        // Generate tanggal berdasarkan hari yang dipilih
        $startDate = Carbon::parse($validated['tanggal_mulai']);
        $endDate = Carbon::parse($validated['tanggal_selesai']);
        $hariMapping = [
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6,
            'minggu' => 0,
        ];

        $createdCount = 0;
        $errors = [];

        // Loop melalui setiap tanggal
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek; // 0 (Minggu) sampai 6 (Sabtu)

            // Cek apakah hari ini dipilih
            $isSelectedDay = false;
            foreach ($validated['hari'] as $hari) {
                if ($dayOfWeek == $hariMapping[$hari]) {
                    $isSelectedDay = true;
                    break;
                }
            }

            if ($isSelectedDay) {
                // Cek ketersediaan ruangan
                $isAvailable = $this->checkRoomAvailability(
                    $validated['ruangan_id'],
                    $date->format('Y-m-d'),
                    $validated['jam_mulai'],
                    $validated['jam_selesai']
                );

                if ($isAvailable) {
                    // Buat booking
                    Booking::create([
                        'ruangan_id' => $validated['ruangan_id'],
                        'tanggal' => $date->format('Y-m-d'),
                        'jam_mulai' => $validated['jam_mulai'],
                        'jam_selesai' => $validated['jam_selesai'],
                        'nama_peminjam' => $validated['nama_peminjam'],
                        'nim' => $validated['nim'] ?? null,
                        'no_hp' => $validated['no_hp'] ?? null,
                        'keperluan' => $validated['keperluan'],
                        'jumlah_peserta' => $validated['jumlah_peserta'] ?? 1,
                        'status' => 'disetujui',
                        'pemesan_email' => Auth::user()->email,
                        'user_id' => Auth::id()
                    ]);
                    $createdCount++;
                } else {
                    $errors[] = "Ruangan tidak tersedia pada {$date->translatedFormat('l, d F Y')}";
                }
            }
        }

        if ($createdCount > 0) {
            $message = "Berhasil membuat {$createdCount} booking!";
            if (!empty($errors)) {
                $message .= " " . implode(', ', $errors);
            }
            return redirect()->route('teknisi.bookings.index')
                ->with('success', $message);
        } else {
            return back()->withErrors(['message' => 'Tidak ada booking yang berhasil dibuat. ' . implode(', ', $errors)])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource (teknisi).
     */
    public function editTeknisi($id)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::findOrFail($id);
        $ruangans = Ruangan::where('status', 'tersedia')
            ->orderBy('lantai', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        // PERBAIKAN DI SINI: 'teknisi.bookings.edit' bukan 'teknisi.booking.edit'
        return view('teknisi.bookings.edit', [
            'booking' => $booking,
            'ruangans' => $ruangans,
            'activePage' => 'booking'
        ]);
    }

    /**
     * Update the specified resource in storage (teknisi).
     */
    public function updateTeknisi(Request $request, $id)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'nim' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:15',
            'keperluan' => 'required|string|max:500',
            'status' => 'required|in:menunggu,disetujui,ditolak',
            'jumlah_peserta' => 'nullable|integer|min:1',
        ]);

        // Cek ketersediaan (kecuali untuk booking yang ditolak)
        if ($validated['status'] !== 'ditolak') {
            $isAvailable = $this->checkRoomAvailability(
                $validated['ruangan_id'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $booking->id // Exclude current booking
            );

            if (!$isAvailable) {
                return back()->withErrors([
                    'jam_mulai' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ])->withInput();
            }
        }

        $booking->update($validated);

        return redirect()->route('teknisi.bookings.index')
            ->with('success', 'Booking berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage (teknisi).
     */
    public function destroyTeknisi($id)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::findOrFail($id);
        $booking->delete();

        return redirect()->route('teknisi.bookings.index')
            ->with('success', 'Booking berhasil dihapus!');
    }

    /**
     * Update status booking (approve/reject) - teknisi.
     */
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak'
        ]);

        $booking = Booking::findOrFail($id);

        // Jika status berubah menjadi disetujui, cek ketersediaan
        if ($request->status === 'disetujui') {
            $isAvailable = $this->checkRoomAvailability(
                $booking->ruangan_id,
                $booking->tanggal,
                $booking->jam_mulai,
                $booking->jam_selesai,
                $booking->id
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ], 422);
            }
        }

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diubah!',
            'status' => $request->status
        ]);
    }
    
    // ========================
    // QUICK ACTIONS DARI TABEL
    // ========================

    /**
     * Quick Edit - untuk edit langsung dari tabel
     */
    public function quickEdit($id)
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $booking = Booking::with('ruangan')->findOrFail($id);
        $ruangans = Ruangan::where('status', 'tersedia')->get();

        return response()->json([
            'success' => true,
            'booking' => $booking,
            'ruangans' => $ruangans,
            'formatted_tanggal' => Carbon::parse($booking->tanggal)->format('Y-m-d')
        ]);
    }

    /**
     * Quick Update - update langsung dari tabel
     */
    public function quickUpdate(Request $request, $id)
    {
        if (Auth::user()->role !== 'teknisi') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $booking = Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'nama_peminjam' => 'required|string|max:255',
            'keperluan' => 'required|string|max:500',
            'status' => 'required|in:menunggu,disetujui,ditolak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Cek ketersediaan (kecuali untuk booking yang ditolak)
        if ($validated['status'] !== 'ditolak') {
            $isAvailable = $this->checkRoomAvailability(
                $validated['ruangan_id'],
                $validated['tanggal'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $booking->id
            );

            if (!$isAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan sudah dipesan pada waktu tersebut.'
                ], 422);
            }
        }

        $booking->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil diperbarui!',
            'booking' => $booking->load('ruangan')
        ]);
    }

    /**
     * Quick Delete - hapus langsung dari tabel
     */
    public function quickDelete($id)
    {
        if (Auth::user()->role !== 'teknisi') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $booking = Booking::findOrFail($id);
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dihapus!'
        ]);
    }

    /**
     * Quick View - lihat detail booking
     */
    public function quickView($id)
    {
        $booking = Booking::with('ruangan')->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => $booking,
            'formatted_tanggal' => Carbon::parse($booking->tanggal)
                ->translatedFormat('l, d F Y')
        ]);
    }

    /**
     * Get bookings for today (API)
     */
    public function getTodayBookings(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));

        $bookings = Booking::with('ruangan')
            ->where('tanggal', $date)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'date' => $date
        ]);
    }

    /**
     * Show JSON for API (untuk modal di view publik)
     */
    public function showJson($id)
    {
        $booking = Booking::with('ruangan')->findOrFail($id);
        return response()->json($booking);
    }
    
    // ========================
    // METHOD TAMBAHAN
    // ========================

    /**
     * Show booking history for logged in user
     */
    public function myBookings()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $bookings = Booking::with('ruangan')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(15);

        return view('publik.my-bookings', [
            'bookings' => $bookings,
            'activePage' => 'my-bookings'
        ]);
    }

    /**
     * Check room availability
     */
    private function checkRoomAvailability($ruanganId, $tanggal, $jamMulai, $jamSelesai, $excludeBookingId = null)
    {
        $query = Booking::where('ruangan_id', $ruanganId)
            ->where('tanggal', $tanggal)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($q2) use ($jamMulai, $jamSelesai) {
                        $q2->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    /**
     * Create dummy bookings for testing
     */
    public function createDummyBookings()
    {
        if (Auth::user()->role !== 'teknisi') {
            abort(403, 'Unauthorized');
        }

        $ruangans = Ruangan::all();
        $today = now()->format('Y-m-d');

        $dummyData = [
            [
                'nama_peminjam' => 'Budi Santoso',
                'nim' => '20210001',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'keperluan' => 'Kuliah Web Programming',
                'status' => 'disetujui',
                'ruangan_id' => $ruangans->where('nama', 'LIKE', '%Workshop RSI%')->first()->id ?? 1
            ],
            [
                'nama_peminjam' => 'Siti Aisyah',
                'nim' => '20210002',
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'keperluan' => 'Praktikum Jaringan Komputer',
                'status' => 'menunggu',
                'ruangan_id' => $ruangans->where('nama', 'LIKE', '%Lab RSI%')->first()->id ?? 2
            ],
            [
                'nama_peminjam' => 'Ahmad Fauzi',
                'nim' => '20210003',
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'keperluan' => 'Rapat Prodi TI',
                'status' => 'disetujui',
                'ruangan_id' => $ruangans->where('nama', 'LIKE', '%3.1%')->first()->id ?? 10
            ]
        ];

        foreach ($dummyData as $data) {
            Booking::create(array_merge($data, [
                'tanggal' => $today,
                'pemesan_email' => 'dummy@example.com',
                'no_hp' => '081234567890',
                'jumlah_peserta' => 25,
                'user_id' => Auth::id()
            ]));
        }

        return redirect()->route('ruangan.publik')
            ->with('success', 'Dummy bookings berhasil dibuat!');
    }
}
