<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarif;

class TarifController extends Controller
{
    public function index()
    {
        $daftarTarif = Tarif::orderBy('zona')->get();
        return view('manager.tarif.index', compact('daftarTarif'));
    }

    public function update(Request $request, Tarif $tarif)
    {
        $data = $request->validate(['tarif_per_kg' => 'required|integer|min:1']);
        $tarif->update(['tarif_per_kg' => $data['tarif_per_kg'], 'diubah_oleh' => $request->user()->id]);

        return redirect()->route('manager.tarif.index')->with('sukses', "Tarif {$tarif->nama_zona} berhasil diperbarui.");
    }
}