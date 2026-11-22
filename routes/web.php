<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Route;

// Halaman Utama Customer
Route::get('/', [HomepageController::class, 'index'])->name('homepage');

// Rute Autentikasi untuk Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Autentikasi Pengguna Masuk (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
