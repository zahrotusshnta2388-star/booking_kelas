<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

// ========================
// Halaman Utama Publik
// ========================
Route::get('/', function () {
    return view('welcome', ['activePage' => 'home']);
})->name('home');

// ========================
// ROUTE UNTUK PUBLIK (tanpa login)
// ========================

// Jadwal Ruangan (timeline view)
Route::get('/jadwal-ruangan', [RuanganController::class, 'publik'])
    ->name('ruangan.publik');

// Form Booking untuk publik
Route::get('/booking', [BookingController::class, 'create'])
    ->name('booking.publik');

// Store Booking dari publik
Route::post('/booking', [BookingController::class, 'store'])
    ->name('booking.publik.store');

// Check availability ruangan
Route::post('/check-availability', [RuanganController::class, 'checkAvailability'])
    ->name('ruangan.checkAvailability');

// ========================
// ROUTE UNTUK AUTH (Login Teknisi)
// ========================

// Login page - hanya untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');
});

// Logout - hanya untuk auth (sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

// ========================
// ROUTE UNTUK TEKNISI (harus login & role teknisi)
// ========================
Route::middleware(['auth', 'role:teknisi'])->group(function () {

    // Dashboard Teknisi
    Route::get('/teknisi/dashboard', [DashboardController::class, 'index'])
        ->name('teknisi.dashboard');

    // Jadwal Ruangan (view khusus teknisi)
    Route::get('/teknisi/jadwal-ruangan', [RuanganController::class, 'jadwalRuangan'])
        ->name('ruangan.jadwal');

    // CRUD Ruangan untuk teknisi
    Route::prefix('teknisi/ruangan')->group(function () {
        Route::get('/', [RuanganController::class, 'index'])->name('ruangan.index');
        Route::get('/create', [RuanganController::class, 'create'])->name('ruangan.create');
        Route::post('/', [RuanganController::class, 'store'])->name('ruangan.store');
        Route::get('/{ruangan}', [RuanganController::class, 'show'])->name('ruangan.show');
        Route::get('/{ruangan}/edit', [RuanganController::class, 'edit'])->name('ruangan.edit');
        Route::put('/{ruangan}', [RuanganController::class, 'update'])->name('ruangan.update');
        Route::delete('/{ruangan}', [RuanganController::class, 'destroy'])->name('ruangan.destroy');
    });

    // CRUD Booking untuk teknisi
    Route::prefix('teknisi/booking')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('booking.index');
        Route::get('/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('booking.show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('booking.edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('booking.update');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');

        // Update status booking (approve/reject)
        Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])
            ->name('booking.status');
    });
});

// ========================
// ROUTE UNTUK USER YANG SUDAH LOGIN (role user biasa)
// ========================
Route::middleware(['auth'])->group(function () {
    // Profile user
    Route::get('/profile', function () {
        return view('profile', ['activePage' => 'profile']);
    })->name('profile');

    // History booking user
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])
        ->name('booking.my');
});

// ========================
// API Routes (untuk AJAX/JavaScript)
// ========================
Route::prefix('api')->group(function () {
    Route::get('/ruangan/{id}/availability', [RuanganController::class, 'getAvailability'])
        ->name('api.ruangan.availability');

    Route::get('/bookings/today', [BookingController::class, 'getTodayBookings'])
        ->name('api.bookings.today');

    Route::get('/stats', function () {
        return response()->json([
            'users' => \App\Models\User::count(),
            'ruangans' => \App\Models\Ruangan::count(),
            'bookings_today' => \App\Models\Booking::whereDate('tanggal', now())->count(),
            'bookings_pending' => \App\Models\Booking::where('status', 'menunggu')->count(),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    })->name('api.stats');
});

// ========================
// Fallback untuk error 404
// ========================
Route::fallback(function () {
    return redirect('/')->with('error', 'Halaman tidak ditemukan');
});
