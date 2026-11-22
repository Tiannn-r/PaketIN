<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\Setoran;
use App\Exceptions\AturanBisnisException;
use Illuminate\Support\Facades\DB;

class SetoranService
{
    public function catat(Paket $paket, int $adminId): Setoran
    {
        return DB::transaction(function () use ($paket, $adminId) {
            $paket = Paket::lockForUpdate()->findOrFail($paket->id);

            if (!$paket->status_pembayaran) throw new AturanBisnisException('Paket ini bukan paket COD.');
            if ((int) $paket->nilai_cod <= 0) throw new AturanBisnisException('Nilai COD paket tidak valid.');
            if ($paket->status !== 'Terkirim') throw new AturanBisnisException('Setoran hanya dapat dicatat setelah paket berstatus Terkirim.');
            if (Setoran::where('id_paket', $paket->id)->exists()) throw new AturanBisnisException('Setoran COD paket ini sudah dicatat.');

            return Setoran::create(['id_paket' => $paket->id, 'jumlah' => (int) $paket->nilai_cod, 'dicatat_oleh' => $adminId, 'disetor_pada' => now()]);
        });
    }
}