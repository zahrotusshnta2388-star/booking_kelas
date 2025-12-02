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
    return view('welcome');
});

// ========================
// ROUTE UNTUK PUBLIK (tanpa login)
// ========================
Route::get('/ruangan-publik', [RuanganController::class, 'publik'])->name('ruangan.publik');
Route::get('/jadwal-publik', [BookingController::class, 'jadwalPublik'])->name('jadwal.publik');
Route::get('/booking-publik', [BookingController::class, 'createPublik'])->name('booking.publik');
Route::post('/booking-publik', [BookingController::class, 'storePublik'])->name('booking.publik.store');

// Route untuk check availability (bisa diakses publik atau teknisi)
Route::post('/check-availability', [RuanganController::class, 'checkAvailability'])
    ->name('ruangan.checkAvailability');

// ========================
// ROUTE UNTUK AUTH (Login Teknisi)
// ========================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========================
// ROUTE UNTUK TEKNISI (harus login)
// ========================
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('teknisi.dashboard');
    
    // CRUD Ruangan
    Route::resource('ruangan', RuanganController::class);
    
    // CRUD Booking
    Route::resource('booking', BookingController::class);
    Route::post('/booking/{booking}/status', [BookingController::class, 'updateStatus'])->name('booking.status');
});

// ========================
// Fallback untuk error 404
// ========================
Route::fallback(function () {
    return redirect('/');
});