<?php

namespace App\Http\Middleware;

use App\Models\Kurir;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPasswordKurir
{
    public function handle(Request $request, Closure $next): Response
    {
        $kurir = $request->user();

        if (!$kurir instanceof Kurir || $kurir->gantipassword) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan ganti password sementara terlebih dahulu.',
            ], 403);
        }

        return $next($request);
    }
}
