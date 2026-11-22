<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\RiwayatStatus;
use App\Models\Tarif;
use Illuminate\Support\Facades\DB;

class PaketService{

    public function __construct(
        private TarifKalkulator $kalkulatorTarif, 
        private GeneratorResi $pembuatResi,){
        }

    public function buatPaket(array $dataPaket, int $zona, bool $zonaDipilihManual, int $admin_id): Paket{
       
        return DB::transaction(function () use ($dataPaket, $zona, $zonaDipilihManual, $admin_id){
            $tarifZona = Tarif::where('zona', $zona)->firstOrFail();

            $beratAsli = (float) $dataPaket['berat'];
            $beratBulat = $this->kalkulatorTarif->bulatkanBerat($beratAsli);
            $ongkir = $this->kalkulatorTarif->hitungOngkir(
                $tarifZona->tarif_per_kg,
                $beratBulat,
                $dataPaket['layanan']
            );

            $adalahCOD = $dataPaket['status_pembayaran'];
            $nilaiCOD = $adalahCOD ? (int) $dataPaket['nilai_cod'] : null;
            $biayaCOD = $adalahCOD ? $this->kalkulatorTarif->hitungbiayaCOD($nilaiCOD): 0;

            $paketBaru = Paket::create([
                'no_resi' => $this->pembuatResi->buatNomorBaru(),
                'id_pelanggan' => $dataPaket['id_pelanggan'],
                'dibuat_oleh' => $admin_id,
                'nama_penerima' => $dataPaket['nama_penerima'],
                'telepon_penerima' => $dataPaket['telepon_penerima'],
                'alamat_penerima' => $dataPaket['alamat_penerima'],
                'kota_tujuan' => $dataPaket['kota_tujuan'],
                'zona' => $zona,
                'status_zona' => $zonaDipilihManual,
                'berat_asli' => $beratAsli,
                'berat_bulat' => $beratBulat,
                'layanan' => $dataPaket['layanan'],
                'tarif_per_kg' => $tarifZona->tarif_per_kg,
                'ongkir' => $ongkir,
                'status_pembayaran' => $adalahCOD,
                'nilai_cod' => $nilaiCOD,
                'biaya_cod' => $biayaCOD,
                'status' => 'Diterima',
                'jumlah_gagal' => 0,
                'tanggal_masuk' => now()->toDateString(),
            ]);

            RiwayatStatus::create([
                'id_paket' => $paketBaru->id,
                'status_dari' => null,
                'status_ke' => 'Diterima',
                'tipe_pelaku' => 'Admin',
                'id_pelaku' => $admin_id,
                'catatan' => null,
            ]);

            return $paketBaru;

        });
    }
}