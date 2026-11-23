<?php

use App\Http\Controllers\Api\KurirAuthController;
use App\Http\Controllers\Api\KurirPaketController;
use Illuminate\Support\Facades\Route;

Route::prefix('kurir')->group(function () {
    Route::post('/login', [KurirAuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'kurir.aktif'])->group(function () {
        Route::get('/me', [KurirAuthController::class, 'me']);
        Route::post('/logout', [KurirAuthController::class, 'logout']);
        Route::put('/password', [KurirAuthController::class, 'gantiPassword']);

        Route::middleware('kurir.password')->group(function () {
            Route::get('/paket', [KurirPaketController::class, 'index']);

            Route::get('/paket/{id}', [KurirPaketController::class, 'show'])
                ->whereNumber('id');

            Route::patch('/paket/{id}/status', [KurirPaketController::class, 'updateStatus'])
                ->whereNumber('id');

            Route::get('/cod-belum-disetor', [KurirPaketController::class, 'codBelumDisetor']);
        });
    });
});
