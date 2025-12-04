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
// API ROUTES UNTUK JAVASCRIPT DI VIEW
// ========================
Route::prefix('api')->group(function () {
    // Detail booking untuk modal (publik bisa akses)
    Route::get('/bookings/{booking}', [BookingController::class, 'showJson'])
        ->name('api.bookings.show');

    // Stats
    Route::get('/stats', function () {
        return response()->json([
            'users' => \App\Models\User::count(),
            'ruangans' => \App\Models\Ruangan::count(),
            'bookings_today' => \App\Models\Booking::whereDate('tanggal', now())->count(),
            'bookings_pending' => \App\Models\Booking::where('status', 'menunggu')->count(),
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    })->name('api.stats');

    // Check ruangan availability
    Route::get('/ruangan/{id}/availability', [RuanganController::class, 'getAvailability'])
        ->name('api.ruangan.availability');
});

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
// ROUTE UNTUK SEMUA USER YANG SUDAH LOGIN
// ========================
Route::middleware(['auth'])->group(function () {
    // Profile user
    Route::get('/profile', function () {
        return view('profile', ['activePage' => 'profile']);
    })->name('profile');

    // History booking user
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])
        ->name('booking.my');

    // ========================
    // ROUTE BOOKING UNTUK SEMUA USER (edit/hapus booking sendiri)
    // ========================
    Route::prefix('bookings')->group(function () {
        // Detail booking JSON (untuk modal) - HARUS DI ATAS EDIT
        Route::get('/{booking}', [BookingController::class, 'show'])
            ->name('bookings.show');

        // Edit booking (hanya pemilik)
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])
            ->name('bookings.edit');

        // Update booking (hanya pemilik)
        Route::put('/{booking}', [BookingController::class, 'update'])
            ->name('bookings.update');

        // Hapus booking (hanya pemilik)
        Route::delete('/{booking}', [BookingController::class, 'destroy'])
            ->name('bookings.destroy');

        // Approve booking (khusus teknisi) - HAPUS INI karena sudah ada di teknisi
        // Route::post('/{booking}/approve', [BookingController::class, 'approve'])
        //     ->name('bookings.approve');
    });
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
    Route::prefix('teknisi/bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('teknisi.bookings.index');
        Route::get('/create', [BookingController::class, 'createTeknisi'])->name('teknisi.bookings.create');
        Route::post('/', [BookingController::class, 'storeTeknisi'])->name('teknisi.bookings.store');

        // PERBAIKAN: Tambah route untuk edit dan update teknisi
        Route::get('/{booking}/edit', [BookingController::class, 'editTeknisi'])->name('teknisi.bookings.edit');
        Route::put('/{booking}', [BookingController::class, 'updateTeknisi'])->name('teknisi.bookings.update');

        Route::delete('/{booking}', [BookingController::class, 'destroyTeknisi'])->name('teknisi.bookings.destroy');

        // Update status booking (approve/reject)
        Route::post('/{booking}/status', [BookingController::class, 'updateStatus'])
            ->name('teknisi.bookings.status');

        // Approve booking khusus
        Route::post('/{booking}/approve', [BookingController::class, 'approve'])
            ->name('teknisi.bookings.approve');

        // Quick actions
        Route::get('/{booking}/quick-edit', [BookingController::class, 'quickEdit'])
            ->name('teknisi.bookings.quick-edit');
        Route::put('/{booking}/quick-update', [BookingController::class, 'quickUpdate'])
            ->name('teknisi.bookings.quick-update');
        Route::delete('/{booking}/quick-delete', [BookingController::class, 'quickDelete'])
            ->name('teknisi.bookings.quick-delete');
        Route::get('/{booking}/quick-view', [BookingController::class, 'quickView'])
            ->name('teknisi.bookings.quick-view');
    });
});

// ========================
// Fallback untuk error 404
// ========================
Route::fallback(function () {
    return redirect('/')->with('error', 'Halaman tidak ditemukan');
});
