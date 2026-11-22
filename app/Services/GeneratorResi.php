<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class GeneratorResi{

    public function buatNomorBaru(): string{
        $sekarang = now();
        $tanggalHariIni = $sekarang->toDateString();

        DB::table('resi')->insertOrIgnore([
            'tanggal' => $tanggalHariIni,
            'terakhir' => 0,
        ]);

        $nomorTerakhir = DB::table('resi')
        ->where('tanggal', $tanggalHariIni)
        ->lockForUpdate()
        ->value('terakhir');

        $nomorBaru = $nomorTerakhir + 1;

        DB::table('resi')
        ->where('tanggal', $tanggalHariIni)
        ->update(['terakhir' => $nomorBaru]);

        return 'PKE-' . $sekarang->format('ymd') . '-' . str_pad($nomorBaru, 4, '0', STR_PAD_LEFT);
    }

}