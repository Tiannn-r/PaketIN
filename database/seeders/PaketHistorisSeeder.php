<?php

namespace Database\Seeders;

use App\Models\Kurir;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\RiwayatStatus;
use App\Models\Tarif;
use App\Models\User;
use App\Services\CsvimportService;
use App\Services\SetoranService;
use App\Services\TarifKalkulator;
use App\Services\Wilayah;
use App\Support\Telepon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaketHistorisSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/paket_historis.csv');
        $barisCsv = app(CsvimportService::class)->bacaCsv($path, [
            'no_resi', 'tanggal_terima', 'pengirim', 'penerima', 'telepon_penerima',
            'kota_tujuan', 'berat_kg', 'layanan', 'status', 'kode_kurir', 'nilai_cod',
        ]);

        $barisCsv = $this->hapusResiDuplikat($barisCsv);
        $adminId = User::where('role', 'admin')->value('id');

        if (!$adminId) {
            throw new RuntimeException('Akun Admin belum tersedia. Jalankan StafSeeder sebelum PaketHistorisSeeder.');
        }

        $pelangganByNama = [];
        foreach (Pelanggan::all() as $pelanggan) {
            $pelangganByNama[$this->kunciNama($pelanggan->nama)][] = $pelanggan;
        }

        $kurirByKode = Kurir::all()->keyBy('kode');
        $tarifByZona = Tarif::all()->keyBy('zona');
        $kalkulator = app(TarifKalkulator::class);
        $wilayah = app(Wilayah::class);
        $zonaPerKota = [];
        $dataSiap = [];

        $daftarStatus = ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'];
        $statusDenganKurir = ['Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'];

        foreach ($barisCsv as $index => $baris) {
            try {
            $nomorBaris = $index + 2;
            $noResi = trim($baris['no_resi']);
            $tanggal = trim($baris['tanggal_terima']);
            $pengirim = trim($baris['pengirim']);
            $penerima = trim($baris['penerima']);
            $telepon = Telepon::rapikan($baris['telepon_penerima']);
            $kota = trim($baris['kota_tujuan']);
            $layanan = trim($baris['layanan']);
            $status = trim($baris['status']);
            $kodeKurir = trim($baris['kode_kurir']);

            if (!preg_match('/^PKE-\d{6}-\d{4}$/', $noResi)) {
                throw new RuntimeException("Format nomor resi tidak valid pada baris {$nomorBaris}: {$noResi}");
            }

            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $tanggalBagian)
                || !checkdate((int) $tanggalBagian[2], (int) $tanggalBagian[3], (int) $tanggalBagian[1])) {
                throw new RuntimeException("Tanggal terima tidak valid pada baris {$nomorBaris}: {$tanggal}");
            }

            if ($pengirim === '' || $penerima === '' || $kota === '') {
                throw new RuntimeException("Pengirim, penerima, dan kota tujuan wajib diisi pada baris {$nomorBaris}.");
            }

            if (!Telepon::valid($telepon)) {
                throw new RuntimeException("Nomor telepon penerima tidak valid pada baris {$nomorBaris}: {$baris['telepon_penerima']}");
            }

            if (!in_array($layanan, ['Reguler', 'Express'], true)) {
                throw new RuntimeException("Layanan tidak dikenal pada baris {$nomorBaris}: {$layanan}");
            }

            if (!in_array($status, $daftarStatus, true)) {
                throw new RuntimeException("Status paket tidak dikenal pada baris {$nomorBaris}: {$status}");
            }

            $pengirimKey = $this->kunciNama($pengirim);
            $calonPelanggan = $pelangganByNama[$pengirimKey] ?? [];

            if (count($calonPelanggan) !== 1) {
                throw new RuntimeException("Nama pengirim '{$pengirim}' pada baris {$nomorBaris} tidak ditemukan atau tidak unik di pelanggan.csv.");
            }

            $kurir = null;
            if ($kodeKurir !== '') {
                $kurir = $kurirByKode->get($kodeKurir);
                if (!$kurir) {
                    throw new RuntimeException("Kode kurir '{$kodeKurir}' pada baris {$nomorBaris} tidak ditemukan di kurir.csv.");
                }
            }

            if (in_array($status, $statusDenganKurir, true) && !$kurir) {
                throw new RuntimeException("Paket berstatus {$status} wajib memiliki kode kurir pada baris {$nomorBaris}.");
            }

            $beratAsli = null;
            if ($baris['berat_kg'] !== '') {
                if (!is_numeric($baris['berat_kg']) || (float) $baris['berat_kg'] <= 0) {
                    throw new RuntimeException("Berat harus berupa angka lebih dari 0 pada baris {$nomorBaris}.");
                }
                $beratAsli = (float) $baris['berat_kg'];
            }

            $nilaiCod = 0;
            if ($baris['nilai_cod'] !== '') {
                if (!preg_match('/^\d+$/', $baris['nilai_cod'])) {
                    throw new RuntimeException("Nilai COD harus berupa bilangan bulat pada baris {$nomorBaris}.");
                }
                $nilaiCod = (int) $baris['nilai_cod'];
            }

            $kunciKota = mb_strtolower($kota, 'UTF-8');
            if (!array_key_exists($kunciKota, $zonaPerKota)) {
                $zonaPerKota[$kunciKota] = $wilayah->cariZona($kota);
            }
            $zona = $zonaPerKota[$kunciKota];
            $tarif = $zona !== null ? $tarifByZona->get($zona) : null;

            if ($zona !== null && !$tarif) {
                throw new RuntimeException("Tarif untuk zona {$zona} belum tersedia. Periksa data pada TarifSeeder dan tabel tarif.");
            }

            $beratBulat = $beratAsli !== null ? $kalkulator->bulatkanBerat($beratAsli) : null;
            $ongkir = ($beratBulat !== null && $tarif)
                ? $kalkulator->hitungOngkir((int) $tarif->tarif_per_kg, $beratBulat, $layanan)
                : null;
            $biayaCod = $nilaiCod > 0 ? $kalkulator->hitungbiayaCOD($nilaiCod) : 0;
            $jumlahGagal = match ($status) {
                'Gagal Kirim' => 1,
                'Retur' => 2,
                default => 0,
            };

            $dataSiap[] = [
                'no_resi' => $noResi,
                'id_pelanggan' => $calonPelanggan[0]->id,
                'id_kurir' => $kurir?->id,
                'dibuat_oleh' => $adminId,
                'nama_penerima' => mb_convert_case($penerima, MB_CASE_TITLE, 'UTF-8'),
                'telepon_penerima' => $telepon,
                'alamat_penerima' => null,
                'kota_tujuan' => $kota,
                'zona' => $zona,
                'status_zona' => false,
                'berat_asli' => $beratAsli,
                'berat_bulat' => $beratBulat,
                'layanan' => $layanan,
                'tarif_per_kg' => $tarif?->tarif_per_kg,
                'ongkir' => $ongkir,
                'status_pembayaran' => $nilaiCod > 0,
                'nilai_cod' => $nilaiCod > 0 ? $nilaiCod : null,
                'biaya_cod' => $biayaCod,
                'status' => $status,
                'jumlah_gagal' => $jumlahGagal,
                'perlu_verifikasi' => $beratAsli === null || $zona === null,
                'tanggal_masuk' => $tanggal,
            ];
            } catch (RuntimeException $error) {
                $this->command?->warn('Baris dilewati: ' . $error->getMessage());
                continue;
            }
        }

        $dibuat = 0;
        $dilewati = 0;
        $diperbaruiZona = 0;
        $setoranService = app(SetoranService::class);

        DB::transaction(function () use ($dataSiap, $adminId, $setoranService, $kalkulator, &$dibuat, &$dilewati, &$diperbaruiZona) {
            foreach ($dataSiap as $data) {
                $paket = Paket::where('no_resi', $data['no_resi'])->first();

                if ($paket) {
                    $dilewati++;

                    // Isi zona yang sebelumnya gagal diperoleh dari API.
                    // Jangan menimpa zona yang telah diatur manual.
                    if ($paket->zona === null && !$paket->status_zona && $data['zona'] !== null) {
                        $tarif = Tarif::find($data['zona']);

                        if (!$tarif) {
                            throw new RuntimeException("Tarif zona {$data['zona']} tidak ditemukan.");
                        }

                        $beratBulat = $paket->berat_asli !== null
                            ? $kalkulator->bulatkanBerat((float) $paket->berat_asli)
                            : null;

                        $ongkir = $beratBulat !== null
                            ? $kalkulator->hitungOngkir((int) $tarif->tarif_per_kg, $beratBulat, $paket->layanan)
                            : null;

                        $paket->update([
                            'zona' => $tarif->zona,
                            'tarif_per_kg' => $tarif->tarif_per_kg,
                            'berat_bulat' => $beratBulat,
                            'ongkir' => $ongkir,
                            'perlu_verifikasi' => $beratBulat === null,
                        ]);

                        $diperbaruiZona++;
                    }

                    if ($paket->status === 'Terkirim' && $paket->status_pembayaran && !$paket->setoran()->exists()) {
                        $setoranService->catat($paket, (int) $adminId);
                    }

                    continue;
                }

                $paket = Paket::create($data);

                RiwayatStatus::create([
                    'id_paket' => $paket->id,
                    'status_dari' => null,
                    'status_ke' => $paket->status,
                    'tipe_pelaku' => 'Import',
                    'id_pelaku' => null,
                    'catatan' => 'Snapshot status terakhir dari paket_historis.csv. Waktu dan pelaku perubahan status sebelumnya tidak tersedia pada CSV.',
                ]);

                if ($paket->status === 'Terkirim' && $paket->status_pembayaran) {
                    $setoranService->catat($paket, (int) $adminId);
                }

                $dibuat++;
            }
        });

        $perluVerifikasi = collect($dataSiap)->where('perlu_verifikasi', true)->count();
        $this->command?->info("Paket historis unik: " . count($dataSiap) . "; dibuat: {$dibuat}; diperbarui zona: {$diperbaruiZona}; dilewati karena sudah ada: {$dilewati}; perlu verifikasi: {$perluVerifikasi}.");    }

    private function hapusResiDuplikat(array $barisCsv): array
    {
        $barisUnik = [];

        foreach ($barisCsv as $baris) {
            $noResi = trim($baris['no_resi']);
            if ($noResi === '') {
                throw new RuntimeException('Ditemukan baris paket historis tanpa nomor resi.');
            }

            if (isset($barisUnik[$noResi])) {
                // Resi ganda digabung menjadi satu: baris pertama dipakai.
                continue;
            }

            $barisUnik[$noResi] = $baris;
        }

        return array_values($barisUnik);
    }

    private function kunciNama(string $nama): string
    {
        $nama = preg_replace('/\s+/u', ' ', trim($nama));
        return mb_strtolower($nama, 'UTF-8');
    }
}