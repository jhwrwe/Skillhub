<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}', [KelasController::class, 'showDetail'])->name('kelas.detail');
    Route::get('/my-kelas', [KelasController::class, 'myKelas'])->name('kelas.my');
    Route::post('/kelas/{kelas}/join', [KelasController::class, 'join'])->name('kelas.join');
    Route::post('/kelas/{kelas}/leave', [KelasController::class, 'leave'])->name('kelas.leave');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    Route::get('/kelas', [KelasController::class, 'adminIndex'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kelas}/show', [KelasController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/{kelas}/daftarkan-peserta', [KelasController::class, 'daftarkanPeserta'])->name('kelas.daftarkan-peserta');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');
    Route::delete('/kelas/{kelas}/peserta/{pengguna}', [KelasController::class, 'removePeserta'])->name('kelas.remove-peserta');

    Route::get('/peserta', [PenggunaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/create', [PenggunaController::class, 'create'])->name('peserta.create');
    Route::post('/peserta', [PenggunaController::class, 'store'])->name('peserta.store');
    Route::get('/peserta/{pengguna}', [PenggunaController::class, 'show'])->name('peserta.show');
    Route::get('/peserta/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('peserta.edit');
    Route::put('/peserta/{pengguna}', [PenggunaController::class, 'update'])->name('peserta.update');
    Route::delete('/peserta/{pengguna}', [PenggunaController::class, 'destroy'])->name('peserta.destroy');

    Route::post('/peserta/{pengguna}/daftar-kelas', [PenggunaController::class, 'daftarKelas'])->name('peserta.daftar-kelas');
    Route::delete('/peserta/{pengguna}/batal-kelas/{kelas}', [PenggunaController::class, 'batalKelas'])->name('peserta.batal-kelas');
});
