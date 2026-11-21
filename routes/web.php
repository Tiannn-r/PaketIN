<?php

use App\Http\Controllers\AuthController;
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
});


