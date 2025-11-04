<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DirektoratController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PosisiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/direktorat/index', [DirektoratController::class, 'index'])->name('direktorat.index');
    Route::get('/divisi/index', [DivisiController::class, 'index'])->name('divisi.index');
    Route::get('/unit/index', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/jabatan/index', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/posisi/index', [PosisiController::class, 'index'])->name('posisi.index');
});

require __DIR__.'/auth.php';
