<?php

namespace App\Services;

class PaketStateMachine{
    private const PERPINDAHAN_BOLEH = [
        'Diterima' => ['Transit'],
        'Transit' => ['Dikirim'],
        'Dikirim' => ['Terkirim', 'Gagal Kirim'],
        'Gagal Kirim' => ['Dikirim', 'Retur'],
        'Terkirim' => [],
        'Retur' => [],
    ];
    
    public function boleh(string $statusLama, string $statusBaru): bool{
        $daftarStatusTujuan = self::PERPINDAHAN_BOLEH[$statusLama] ?? [];

        return in_array($statusBaru, $daftarStatusTujuan, true);
    }

    public function adalahStatusFinal(string $status):bool{
        return in_array($status, ['Terkirim', 'Retur'], true);
    }
}