<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\RiwayatStatus;
use App\Models\Tarif;
use App\Exceptions\AturanBisnisException;
use App\Models\Kurir;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class PaketService{

    public const BATAS_PAKET_KURIR = 20;

    public function __construct(
        private TarifKalkulator $kalkulatorTarif, 
        private GeneratorResi $pembuatResi,
        private PaketStateMachine $mesinStatus,
        ){
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

    public function ubahKeTransit(Paket $paket, int $admin_id, ?string $catatan = null): Paket{

        return DB::transaction(function () use ($paket, $admin_id, $catatan){
            $paket = Paket::lockForUpdate()->findOrFail($paket->id);
            $statusLama= $paket->status;

            $this->pastikanPerpindahanBoleh($statusLama, 'Transit');

            $paket->status = 'Transit';
            $paket->save();

            $this->catatRiwayat($paket, $statusLama, 'Transit', 'Admin', $admin_id, $catatan);

            return $paket;
        });
    }

     public function tugaskan(Paket $paket, Kurir $kurir, int $admin_id, ?string $catatan = null): Paket
    {
        return DB::transaction(function () use ($paket, $kurir, $admin_id, $catatan) {
            $kurir = Kurir::lockForUpdate()->findOrFail($kurir->id);

            $paket = Paket::lockForUpdate()->findOrFail($paket->id);
            $statusLama = $paket->status;

            $this->pastikanPerpindahanBoleh($statusLama, 'Dikirim');

            if ($statusLama === 'Gagal Kirim' && $paket->jumlah_gagal >= 2) {
                throw new AturanBisnisException(
                    'Paket sudah gagal 2 kali, tidak bisa dikirim ulang. Ubah statusnya menjadi Retur'
                );
            }

            if (!$kurir->status) {
                throw new AturanBisnisException("Kurir {$kurir->kode} nonaktif, tidak bisa ditugaskan paket");
            }

            $jumlahPaketDikirim = Paket::where('id_kurir', $kurir->id)
                ->where('status', 'Dikirim')
                ->count();

            if ($jumlahPaketDikirim + 1 > self::BATAS_PAKET_KURIR) {
                throw new AturanBisnisException(
                    "Kurir {$kurir->kode} sudah membawa {$jumlahPaketDikirim} paket Dikirim (batas " . self::BATAS_PAKET_KURIR . ")"
                );
            }

            $paket->id_kurir = $kurir->id;
            $paket->status = 'Dikirim';
            $paket->save();

            $catatanRiwayat = trim("Ditugaskan ke {$kurir->kode}. " . $catatan);
            $this->catatRiwayat($paket, $statusLama, 'Dikirim', 'Admin', $admin_id, $catatanRiwayat);

            return $paket;
        });
    }

    public function laporkanHasilAntar(Paket $paket, Kurir $kurir, string $statusBaru, ?string $catatan = null): Paket
    {
        if (!in_array($statusBaru, ['Terkirim', 'Gagal Kirim'], true)) {
            throw new AturanBisnisException('Kurir hanya boleh mengubah status menjadi Terkirim atau Gagal Kirim');
        }

        return DB::transaction(function () use ($paket, $kurir, $statusBaru, $catatan) {
            $paket = Paket::lockForUpdate()->findOrFail($paket->id);

            if ((int) $paket->id_kurir !== (int) $kurir->id) {
                throw new AuthorizationException('Paket ini bukan milik Anda');
            }

            $statusLama = $paket->status;
            $this->pastikanPerpindahanBoleh($statusLama, $statusBaru);

            if ($statusBaru === 'Gagal Kirim') {
                if (blank($catatan)) {
                    throw new AturanBisnisException('Catatan wajib diisi');
                }
                $paket->jumlah_gagal = $paket->jumlah_gagal + 1;
            }

            $paket->status = $statusBaru;
            $paket->save();

            $this->catatRiwayat($paket, $statusLama, $statusBaru, 'Kurir', $kurir->id, $catatan);

            return $paket;
        });
    }

    public function returkan(Paket $paket, int $admin_id, ?string $catatan = null): Paket
    {
        return DB::transaction(function () use ($paket, $admin_id, $catatan) {
            $paket = Paket::lockForUpdate()->findOrFail($paket->id);
            $statusLama = $paket->status;

            $this->pastikanPerpindahanBoleh($statusLama, 'Retur');

            if ($paket->jumlah_gagal < 2) {
                throw new AturanBisnisException(
                    "Paket baru gagal {$paket->jumlah_gagal} kali. Retur hanya boleh dilakukan setelah 2 kali gagal, silahkan lakukan proses kirim ulang terlebih dahulu"
                );
            }

            $paket->status = 'Retur';
            $paket->save();

            $this->catatRiwayat($paket, $statusLama, 'Retur', 'Admin', $admin_id, $catatan);

            return $paket;
        });
    }

     private function pastikanPerpindahanBoleh(string $statusLama, string $statusBaru): void
    {
        if ($this->mesinStatus->adalahStatusFinal($statusLama)) {
            throw new AturanBisnisException("Status {$statusLama}. Proses sudah selesai, tidak dapat mengubah status lagi");
        }

        if (!$this->mesinStatus->boleh($statusLama, $statusBaru)) {
            throw new AturanBisnisException("Status tidak boleh berpindah dari {$statusLama} ke {$statusBaru}");
        }
    }

    private function catatRiwayat(Paket $paket, ?string $statusLama, string $statusBaru, string $pelakuTipe, ?int $pelakuId, ?string $catatan): void
    {
        RiwayatStatus::create([
            'id_paket' => $paket->id,
            'status_dari' => $statusLama,
            'status_ke' => $statusBaru,
            'tipe_pelaku' => $pelakuTipe,
            'id_pelaku' => $pelakuId,
            'catatan' => $catatan,
        ]);
        
    }

}