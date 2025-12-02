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
        $ruangans = Ruangan::where('status', 'tersedia')->get();
        return view('publik.ruangan', compact('ruangans'));
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
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
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
}