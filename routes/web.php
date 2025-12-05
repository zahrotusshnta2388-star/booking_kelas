<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome', ['activePage' => 'home']);
})->name('home');
Route::get('/jadwal-ruangan', [RuanganController::class, 'publik'])
    ->name('ruangan.publik');
Route::post('/check-availability', [RuanganController::class, 'checkAvailability'])
    ->name('ruangan.checkAvailability');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::get('/teknisi/dashboard', [DashboardController::class, 'index'])
    ->name('teknisi.dashboard');

Route::get('teknisi/bookings/{booking}/show', [BookingController::class, 'show'])->name('teknisi.bookings.edit');

Route::middleware(['auth', 'role:teknisi'])->group(function () {
    Route::prefix('teknisi/bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('teknisi.bookings.index');
        Route::get('/create', [BookingController::class, 'createTeknisi'])->name('teknisi.bookings.create');
        Route::post('/store', [BookingController::class, 'storeTeknisi'])->name('teknisi.bookings.store');
        Route::get('/{booking}/edit', [BookingController::class, 'editTeknisi'])->name('teknisi.bookings.edit');

        Route::put('/{booking}', [BookingController::class, 'updateTeknisi'])->name('teknisi.bookings.update');
        Route::delete('/{booking}/delete', [BookingController::class, 'destroy'])->name('teknisi.bookings.destroy');
        // BULK BOOKING - TAMBAHKAN DI SINI
        Route::get('/create-bulk', [BookingController::class, 'createBulk'])
            ->name('teknisi.bookings.create-bulk');
        Route::post('/store-bulk', [BookingController::class, 'storeBulk'])
            ->name('teknisi.bookings.store-bulk');

        Route::get('/create-via-excel', [BookingController::class, 'createViaExcel'])
            ->name('teknisi.bookings.create-via-excel');
    });
});



Route::fallback(function () {
    return redirect('/')->with('error', 'Halaman tidak ditemukan');
});
