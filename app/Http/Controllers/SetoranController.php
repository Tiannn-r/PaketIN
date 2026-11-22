<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use App\Services\SetoranService;
use App\Exceptions\AturanBisnisException;

class SetoranController extends Controller
{
    public function store(Request $request, Paket $paket, SetoranService $setoranService)
    {
        try {
            $setoran = $setoranService->catat($paket, (int) $request->user()->id);
        } catch (AturanBisnisException $error) {
            return back()->withErrors(['setoran' => $error->getMessage()]);
        }

        return back()->with('sukses', "Setoran COD {$paket->no_resi} berhasil dicatat sebesar Rp" . number_format($setoran->jumlah, 0, ',', '.'));
    }
}