<?php
namespace App\Services;

class TarifKalkulator{

    public function bulatkanBerat(float $beratAsli): int{
        return max(1, (int) ceil($beratAsli));
    }

    public function hitungOngkir(int $tarifPerKg, int $beratBulat, string $layanan): int{
        $ongkir = $tarifPerKg * $beratBulat;

        if ($layanan === 'Express') {
            $ongkir = $ongkir * 1.5;
        }

        return (int) round($ongkir);
    }

    public function hitungbiayaCOD(int $nilaiCOD): int{
        return max(2000, (int) round($nilaiCOD * 0.02));
    }

}