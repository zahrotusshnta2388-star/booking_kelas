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

        return redirect()->route('ruangan.publik')
            ->with('success', 'Booking berhasil diperbarui!');
    }

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
            'no_hp' => 'nullable|string|max:15',
            'keperluan' => 'nullable|string|max:500',
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
                        'no_hp' => $validated['no_hp'] ?? 0,
                        'keperluan' => $validated['keperluan'] ?? '',
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

            return redirect()->route('teknisi.dashboard')
                ->with('success', $message);
        } else {
            return back()->withErrors(['message' => 'Tidak ada booking yang berhasil dibuat. ' . implode(', ', $errors)])
                ->withInput();
        }
    }

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

    public function show($id)
    {
        $booking = Booking::with('ruangan', 'user')->findOrFail($id);


        return response()->json($booking);
    }
}
