<?php

namespace App\Http\Middleware;

use App\Models\Kurir;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekKurirAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $kurir = $request->user();

        if (!$kurir instanceof Kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Akses hanya diperbolehkan untuk akun kurir.',
            ], 403);
        }

        if (!$kurir->status) {
            $kurir->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Akun kurir tidak aktif. Hubungi admin.',
            ], 403);
        }

        return $next($request);
    }
}
