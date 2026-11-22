<?php

namespace Database\Seeders;

use App\Models\Kurir;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\RiwayatStatus;
use App\Models\Tarif;
use App\Models\User;
use App\Services\GeneratorResi;
use App\Services\TarifKalkulator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoSeeder extends Seeder
{
    private const TARGET_PAKET_DIKIRIM = 20;
    private const KODE_KURIR_DEMO = 'KUR-01';
    private const AWAL_NAMA_DEMO = 'DEMO - Muatan KUR-01-';

    public function run(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $pelanggan = Pelanggan::orderBy('id')->firstOrFail();
        $kurir = Kurir::where('kode', self::KODE_KURIR_DEMO)->where('status', true)->firstOrFail();
        $tarif = Tarif::where('zona', 1)->firstOrFail();
        $kalkulator = app(TarifKalkulator::class);
        $generatorResi = app(GeneratorResi::class);

        DB::transaction(function () use ($admin, $pelanggan, $kurir, $tarif, $kalkulator, $generatorResi) {
            $this->hapusPaketDemoVersiLama();

            $jumlahPaketAsliDikirim = Paket::where('id_kurir', $kurir->id)
                ->where('status', 'Dikirim')
                ->where('nama_penerima', 'not like', self::AWAL_NAMA_DEMO . '%')
                ->count();

            if ($jumlahPaketAsliDikirim > self::TARGET_PAKET_DIKIRIM) {
                throw new RuntimeException(self::KODE_KURIR_DEMO . " sudah memiliki {$jumlahPaketAsliDikirim} paket Dikirim. Seeder demo tidak akan mengubah paket non-demo.");
            }

            $jumlahDemoDibutuhkan = self::TARGET_PAKET_DIKIRIM - $jumlahPaketAsliDikirim;
            $namaDemoDibutuhkan = [];

            for ($nomor = 1; $nomor <= $jumlahDemoDibutuhkan; $nomor++) {
                $namaDemoDibutuhkan[] = self::AWAL_NAMA_DEMO . sprintf('%02d', $nomor);
            }

            $paketDemoLama = Paket::where('nama_penerima', 'like', self::AWAL_NAMA_DEMO . '%')->get();
            foreach ($paketDemoLama as $paketLama) {
                if (!in_array($paketLama->nama_penerima, $namaDemoDibutuhkan, true)) {
                    $paketLama->setoran()->delete(); $paketLama->daftarRiwayat()->delete(); $paketLama->delete();
                }
            }

            $beratAsli = 1.0;
            $beratBulat = $kalkulator->bulatkanBerat($beratAsli);
            $ongkir = $kalkulator->hitungOngkir((int) $tarif->tarif_per_kg, $beratBulat, 'Reguler');

            foreach ($namaDemoDibutuhkan as $namaDemo) {
                $paket = Paket::firstOrNew(['nama_penerima' => $namaDemo]);

                if (!$paket->exists) {
                    $paket->no_resi = $generatorResi->buatNomorBaru();
                }

                $paket->fill([
                    'id_pelanggan' => $pelanggan->id,
                    'id_kurir' => $kurir->id,
                    'dibuat_oleh' => $admin->id,
                    'telepon_penerima' => '081234567890',
                    'alamat_penerima' => 'Alamat data demonstrasi',
                    'kota_tujuan' => 'Bandung',
                    'zona' => 1,
                    'status_zona' => false,
                    'berat_asli' => $beratAsli,
                    'berat_bulat' => $beratBulat,
                    'layanan' => 'Reguler',
                    'tarif_per_kg' => $tarif->tarif_per_kg,
                    'ongkir' => $ongkir,
                    'status_pembayaran' => false,
                    'nilai_cod' => null,
                    'biaya_cod' => 0,
                    'status' => 'Dikirim',
                    'jumlah_gagal' => 0,
                    'perlu_verifikasi' => false,
                    'tanggal_masuk' => now()->toDateString(),
                ]);
                $paket->save();

                $paket->setoran()->delete();
                $paket->daftarRiwayat()->delete();
                RiwayatStatus::create([
                    'id_paket' => $paket->id,
                    'status_dari' => null,
                    'status_ke' => 'Dikirim',
                    'tipe_pelaku' => 'Admin',
                    'id_pelaku' => $admin->id,
                    'catatan' => 'Data demo disiapkan langsung berstatus Dikirim sesuai ketentuan seeder.',
                ]);
            }
        });

        $this->command?->info(self::KODE_KURIR_DEMO . ' disiapkan dengan tepat 20 paket berstatus Dikirim.');
    }

    private function hapusPaketDemoVersiLama(): void
    {
        $namaDemoLama = [
            'DEMO - Diterima',
            'DEMO - Transit',
            'DEMO - Dikirim',
            'DEMO - Gagal Kirim',
            'DEMO - COD Belum Disetor',
            'DEMO - COD Sudah Disetor',
            'DEMO - Retur',
        ];

        $paketDemoLama = Paket::whereIn('nama_penerima', $namaDemoLama)->get();
        foreach ($paketDemoLama as $paket) {
            $paket->setoran()->delete();
            $paket->daftarRiwayat()->delete();
            $paket->delete();
        }
    }
}