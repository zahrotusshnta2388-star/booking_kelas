<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dashboard for teknisi
     */
    public function index()
    {
        // Hitung statistik
        $totalRuangan = Ruangan::count();
        $totalBooking = Booking::count();
        $bookingAktif = Booking::where('status', 'diterima')->count(); // Ganti nama variable
        $menungguKonfirmasi = Booking::where('status', 'menunggu')->count(); // Ganti nama variable
        
        // Ambil data terbaru
        $ruanganTerbaru = Ruangan::latest()->take(5)->get();
        $bookingTerbaru = Booking::with('ruangan')->latest()->take(10)->get();
        
        return view('teknisi.dashboard', compact(
            'totalRuangan',
            'totalBooking',
            'bookingAktif',        // Nama ini yang dipakai di view
            'menungguKonfirmasi',  // Nama ini yang dipakai di view
            'ruanganTerbaru',
            'bookingTerbaru'
        ));
    }
}