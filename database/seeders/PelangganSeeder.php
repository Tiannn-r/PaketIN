<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Services\CsvimportService;
use App\Support\Telepon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/pelanggan.csv');
        $barisCsv = app(CsvimportService::class)->bacaCsv($path, ['nama', 'telepon', 'alamat', 'kota']);
        $dataSiap = [];
        $teleponTerbaca = [];

        foreach ($barisCsv as $index => $baris) {
            $nomorBaris = $index + 2;
            $nama = trim($baris['nama']);
            $telepon = Telepon::rapikan($baris['telepon']);

            if ($nama === '' || $telepon === '') {
                throw new RuntimeException("Nama dan telepon wajib diisi pada baris {$nomorBaris} pelanggan.csv.");
            }

            if (!Telepon::valid($telepon)) {
                throw new RuntimeException("Nomor telepon tidak valid pada baris {$nomorBaris} pelanggan.csv: {$baris['telepon']}");
            }

            if (isset($teleponTerbaca[$telepon])) {
                throw new RuntimeException("Nomor telepon duplikat setelah dirapikan pada baris {$nomorBaris}: {$telepon}");
            }

            $teleponTerbaca[$telepon] = true;
            $dataSiap[] = [
                'nama' => mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8'),
                'telepon' => $telepon,
                'alamat' => $baris['alamat'] !== '' ? $baris['alamat'] : null,
                'kota' => $baris['kota'] !== '' ? $baris['kota'] : null,
            ];
        }

        DB::transaction(function () use ($dataSiap) {
            foreach ($dataSiap as $data) {
                Pelanggan::updateOrCreate(
                    ['telepon' => $data['telepon']],
                    ['nama' => $data['nama'], 'alamat' => $data['alamat'], 'kota' => $data['kota']]
                );
            }
        });

        $this->command?->info(count($dataSiap) . ' data pelanggan berhasil diproses.');
    }
}