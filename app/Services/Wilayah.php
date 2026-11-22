<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Wilayah{

    public function cariZona(string $kota): ?int{

        $alamatLayanan = config('service.wilayah.url');
        $kunciAPI = config('services.wilayah.key');

        if (!$alamatLayanan || !$kunciAPI){
            return null;
        }

        try {
            $jawaban = Http::withHeaders(['X-API-Key' => $kunciAPI])
            ->timeout(4)
            ->get($alamatLayanan, ['kota' => $kota]);

            if(!$jawaban->successful()){
                return null;
            }

            $zona = (int) $jawaban->json('zona');

            return in_array($zona, [1, 2, 3], true) ? $zona : null;
        } catch(\Throwable $error){
            return null;
        }

    }
}