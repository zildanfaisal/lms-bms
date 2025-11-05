<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DirektoratController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PosisiController;
use App\Http\Controllers\KaryawanController;
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

    // Direktorat Routes
    Route::get('/direktorat/index', [DirektoratController::class, 'index'])->name('direktorat.index');
    Route::get('/direktorat/create', [DirektoratController::class, 'create'])->name('direktorat.create');
    Route::post('/direktorat', [DirektoratController::class, 'store'])->name('direktorat.store');
    Route::get('/direktorat/{direktorat}', [DirektoratController::class, 'show'])->name('direktorat.show');
    Route::get('/direktorat/{direktorat}/edit', [DirektoratController::class, 'edit'])->name('direktorat.edit');
    Route::put('/direktorat/{direktorat}', [DirektoratController::class, 'update'])->name('direktorat.update');
    Route::delete('/direktorat/{direktorat}', [DirektoratController::class, 'destroy'])->name('direktorat.destroy');

    // Divisi Routes
    Route::get('/divisi/index', [DivisiController::class, 'index'])->name('divisi.index');
    Route::get('/divisi/create', [DivisiController::class, 'create'])->name('divisi.create');
    Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');
    Route::get('/divisi/{divisi}', [DivisiController::class, 'show'])->name('divisi.show');
    Route::get('/divisi/{divisi}/edit', [DivisiController::class, 'edit'])->name('divisi.edit');
    Route::put('/divisi/{divisi}', [DivisiController::class, 'update'])->name('divisi.update');
    Route::delete('/divisi/{divisi}', [DivisiController::class, 'destroy'])->name('divisi.destroy');

    // Unit Routes
    Route::get('/unit/index', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/unit/create', [UnitController::class, 'create'])->name('unit.create');
    Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');
    Route::get('/unit/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');
    Route::put('/unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');

    // Karyawan Routes
    Route::get('/karyawan/index', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    // Ajax endpoints for dependent selects
    Route::get('/ajax/divisi-by-direktorat/{direktorat}', [DivisiController::class, 'ajaxByDirektorat'])->name('ajax.divisi.by.direktorat');
    Route::get('/ajax/unit-by-divisi/{divisi}', [UnitController::class, 'ajaxByDivisi'])->name('ajax.unit.by.divisi');

    // Jabatan Routes
    Route::get('/jabatan/index', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('jabatan.create');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/jabatan/{jabatan}/edit', [JabatanController::class, 'edit'])->name('jabatan.edit');
    Route::put('/jabatan/{jabatan}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

    // Posisi Routes
    Route::get('/posisi/index', [PosisiController::class, 'index'])->name('posisi.index');
    Route::get('/posisi/create', [PosisiController::class, 'create'])->name('posisi.create');
    Route::post('/posisi', [PosisiController::class, 'store'])->name('posisi.store');
    Route::get('/posisi/{posisi}/edit', [PosisiController::class, 'edit'])->name('posisi.edit');
    Route::put('/posisi/{posisi}', [PosisiController::class, 'update'])->name('posisi.update');
    Route::delete('/posisi/{posisi}', [PosisiController::class, 'destroy'])->name('posisi.destroy');
});

require __DIR__.'/auth.php';
