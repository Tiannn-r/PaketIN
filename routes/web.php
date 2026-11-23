<?php

use App\Http\Controllers\PelangganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KurirController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\CekResiController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\LaporanController;
use App\Models\Kurir;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/login', [AuthController::class, 'formlogin']) ->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/cek-resi', [CekResiController::class, 'index'])->name('resi.index');
Route::post('/cek-resi', [CekResiController::class, 'cek'])->name('resi.cek');

Route::middleware('auth')->group(function (){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ganti-password', [AuthController::class, 'formgantipassword'])->name('password.ganti');
    Route::post('/ganti-password', [AuthController::class, 'gantipassword']);

    Route::get('/', function () {
        return request()->user()->role === 'manager'
            ? redirect()->route('manager.dashboard')
            : redirect()->route('admin.dashboard');
    })->name('dashboard');
    
    Route::middleware('role:admin')->prefix('admin')->group(function(){
        Route::get('/dashboard', fn () => view('admin.dashboard')) ->name('admin.dashboard');
    });
    
    Route::middleware('role:manager')->prefix('manager')->group(function(){
    Route::get('/dashboard', fn () => view('manager.dashboard'))->name('manager.dashboard');
    Route::get('/tarif', [TarifController::class, 'index'])->name('manager.tarif.index');
    Route::put('/tarif/{tarif}', [TarifController::class, 'update'])->name('manager.tarif.update');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('manager.laporan.index');
    });

    Route::middleware('role:admin,manager')->group(function(){
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('/kurir', [KurirController::class, 'index'])->name('kurir.index');
    });

    Route::middleware('role:admin')->group(function (){

        //tarif (baca-saja untuk admin)
        Route::get('/tarif', [TarifController::class, 'lihat'])->name('tarif.lihat');

        //pelanggan
        Route::get('/pelanggan/tambah', [PelangganController::class, 'create'])->name('pelanggan.create');
        Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('/pelanggan/{pelanggan}/ubah', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');

        //kurir
        Route::get('/kurir/tambah', [KurirController::class, 'create'])->name('kurir.create');
        Route::post('/kurir', [KurirController::class, 'store'])->name('kurir.store');
        Route::get('/kurir/{kurir}/ubah', [KurirController::class, 'edit'])->name('kurir.edit');
        Route::put('/kurir/{kurir}', [KurirController::class, 'update'])->name('kurir.update');
        Route::post('/kurir/{kurir}/nonaktifkan', [KurirController::class, 'nonaktifkan'])->name('kurir.nonaktifkan');
        Route::post('/kurir/{kurir}/aktifkan', [KurirController::class, 'aktifkan'])->name('kurir.aktifkan');
        Route::post('/kurir/{kurir}/reset-password', [KurirController::class, 'resetPassword'])->name('kurir.reset');

        Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
        Route::get('/paket/tambah', [PaketController::class, 'create'])->name('paket.create');
        Route::post('/paket', [PaketController::class, 'store'])->name('paket.store');
        Route::get('/paket/{paket}', [PaketController::class, 'show'])->name('paket.show');
        
        Route::post('/paket/{paket}/transit', [PaketController::class, 'transit'])->name('paket.transit');
        Route::post('/paket/{paket}/tugaskan', [PaketController::class, 'tugaskan'])->name('paket.tugaskan');
        Route::post('/paket/{paket}/retur', [PaketController::class, 'retur'])->name('paket.retur');

        Route::post('/paket/{paket}/setoran', [SetoranController::class, 'store'])->name('paket.setoran');
    });



});


