<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\RiwayatStatus;

class CekResiController extends Controller
{
    public function index()
    {
        return view('resi.index');
    }

    public function cek(Request $request)
    {
        $data = $request->validate(['no_resi' => 'required|string|max:20']);
        $noResi = strtoupper(trim($data['no_resi']));

        $paket = Paket::select('id', 'no_resi', 'telepon_penerima', 'status')
            ->where('no_resi', $noResi)
            ->first();

        if (!$paket) {
            return back()->withErrors(['no_resi' => 'Nomor resi tidak ditemukan.'])->withInput();
        }

        $telepon = preg_replace('/\D/', '', $paket->telepon_penerima);
        $teleponDisamarkan = substr($telepon, 0, 7);

        $riwayat = RiwayatStatus::where('id_paket', $paket->id)
            ->orderBy('id')
            ->get(['status_dari', 'status_ke', 'catatan', 'dibuat_pada'])
            ->map(fn ($item) => [
                'waktu' => $item->dibuat_pada,
                'status_dari' => $item->status_dari,
                'status_ke' => $item->status_ke,
                'catatan' => $item->catatan,
            ]);

        return view('resi.index', [
            'noResi' => $paket->no_resi,
            'statusPaket' => $paket->status,
            'teleponDisamarkan' => $teleponDisamarkan,
            'riwayat' => $riwayat,
        ]);
    }
}