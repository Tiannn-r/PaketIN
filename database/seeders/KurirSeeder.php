<?php

namespace Database\Seeders;

use App\Models\Kurir;
use App\Services\CsvimportService;
use App\Support\Telepon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class KurirSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/kurir.csv');
        $barisCsv = app(CsvimportService::class)->bacaCsv($path, ['kode', 'nama', 'telepon']);
        $passwordSementara = (string) config('services.kurir.password_sementara', 'Kurir12345');

        if (strlen($passwordSementara) < 8) {
            throw new RuntimeException('KURIR_PASSWORD_SEMENTARA minimal 8 karakter.');
        }

        $dataSiap = [];
        $kodeTerbaca = [];

        foreach ($barisCsv as $index => $baris) {
            $nomorBaris = $index + 2;
            $kode = trim($baris['kode']);
            $nama = trim($baris['nama']);
            $telepon = Telepon::rapikan($baris['telepon']);

            if ($kode === '' || $nama === '' || $telepon === '') {
                throw new RuntimeException("Kode, nama, dan telepon wajib diisi pada baris {$nomorBaris} kurir.csv.");
            }

            if (isset($kodeTerbaca[$kode])) {
                throw new RuntimeException("Kode kurir duplikat pada baris {$nomorBaris}: {$kode}");
            }

            if (!Telepon::valid($telepon)) {
                throw new RuntimeException("Nomor telepon tidak valid pada baris {$nomorBaris} kurir.csv: {$baris['telepon']}");
            }

            $kodeTerbaca[$kode] = true;
            $dataSiap[] = [
                'kode' => $kode,
                'nama' => mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8'),
                'telepon' => $telepon,
            ];
        }

        DB::transaction(function () use ($dataSiap, $passwordSementara) {
            foreach ($dataSiap as $data) {
                $kurir = Kurir::firstOrNew(['kode' => $data['kode']]);

                if (!$kurir->exists || $kurir->gantipassword) {
                    $kurir->password = Hash::make($passwordSementara);
                    $kurir->gantipassword = true;

                    if (!$kurir->exists) {
                        $kurir->status = true;
                    }
                }

                $kurir->nama = $data['nama'];
                $kurir->telepon = $data['telepon'];
                $kurir->save();
            }
        });

        $this->command?->info(count($dataSiap) . ' data kurir berhasil diproses.');
    }
}