<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Wilayah
{
    public function cariZona(string $kota): ?int
    {
        $alamatLayanan = rtrim((string) config('services.wilayah.url'), '/');
        $kunciAPI = config('services.wilayah.key');
        $kota = trim($kota);

        if ($alamatLayanan === '' || !$kunciAPI || $kota === '') {
            return null;
        }

        try {
            $jawaban = Http::acceptJson()
                ->withHeaders(['X-API-Key' => $kunciAPI])
                ->connectTimeout(3)
                ->timeout(6)
                ->get($alamatLayanan . '/wilayah', ['kota' => $kota]);

            if (!$jawaban->successful()) {
                Log::warning('API wilayah mengembalikan status gagal.', [
                    'kota' => $kota,
                    'status' => $jawaban->status(),
                ]);

                return null;
            }

            $zona = $jawaban->json('zona');

            if (!is_numeric($zona)) {
                return null;
            }

            $zona = (int) $zona;

            return in_array($zona, [1, 2, 3], true) ? $zona : null;

        } catch (\Throwable $error) {
            Log::warning('Gagal menghubungi API wilayah.', [
                'kota' => $kota,
                'pesan' => $error->getMessage(),
            ]);

            return null;
        }
    }
}
