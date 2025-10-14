<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;
use App\Http\Controllers\siswaController;
use App\Http\Controllers\kontenController;
use App\Http\Controllers\kbmController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\GuruMiddleware;
use App\Http\Middleware\SiswaMiddleware;
use App\Http\Middleware\AuthMiddleware;

// Public Routes
Route::get('/', [kontenController::class, 'landing'])->name('landing');
Route::get('/register', [adminController::class, 'formRegister'])->name('formRegister');
Route::post('/register', [adminController::class, 'prosesRegister'])->name('prosesRegister');
Route::get('/login', [adminController::class, 'formLogin'])->name('formLogin');
Route::post('/login', [adminController::class, 'prosesLogin'])->name('prosesLogin');
Route::get('/logout', [adminController::class, 'logout'])->name('logout');

// Protected Routes (requires authentication)
Route::middleware([AuthMiddleware::class])->group(function () {
    // Home page
    Route::get('/home', [adminController::class, 'home'])->name('home');
    
    // Admin Routes
    Route::middleware([AdminMiddleware::class])->group(function () {
        // CRUD Siswa
        Route::get('/siswa/create', [siswaController::class, 'create'])->name('siswa.create');
        Route::post('/siswa/store', [siswaController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{id}/edit', [siswaController::class, 'edit'])->name('siswa.edit');
        Route::post('/siswa/{id}/update', [siswaController::class, 'update'])->name('siswa.update');
        Route::get('/siswa/{id}/delete', [siswaController::class, 'destroy'])->name('siswa.delete');
        
        // Admin KBM routes
        Route::get('/kbm/guru/{idguru}', [kbmController::class, 'showByGuru'])->name('kbm.by-guru');
    });

    // Guru Routes
    Route::middleware([GuruMiddleware::class])->group(function () {
        // Teacher specific routes can be added here
    });

    // Siswa Routes
    Route::middleware([SiswaMiddleware::class])->group(function () {
        // Student specific routes can be added here
    });

    // KBM Routes (accessible by all authenticated users)
    Route::get('/kbm', [kbmController::class, 'index'])->name('kbm.index');
    Route::get('/kbm/kelas/{idwalas}', [kbmController::class, 'showByKelas'])->name('kbm.by-kelas');

    // Protected content routes
    Route::get('/detil/{id}', [kontenController::class, 'detil'])->name('detil');
});
