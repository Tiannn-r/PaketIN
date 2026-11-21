<?php

use App\Http\Controllers\PelangganController;
use App\Http\Controllers\AuthController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/login', [AuthController::class, 'formlogin']) ->name('login');
Route::post('/login', [AuthController::class, 'login']);

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
        Route::get('/dashboard', fn () => view('manager.dashboard')) ->name('manager.dashboard');
    });

    Route::middleware('role:admin,manager')->group(function(){
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    });

    Route::middleware('role:admin')->group(function (){
        Route::get('/pelanggan/tambah', [PelangganController::class, 'create'])->name('pelanggan.create');
        Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('/pelanggan{pelanggan}/ubah', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('/pelanggan{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
    });

});


