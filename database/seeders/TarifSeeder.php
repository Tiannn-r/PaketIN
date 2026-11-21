<?php

namespace Database\Seeders;

use App\Models\Tarif;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        Tarif::firstOrCreate(['zona' => 1], ['nama_zona' => 'Bandung Raya', 'tarif_per_kg' => 8000]);
        Tarif::firstOrCreate(['zona' => 2], ['nama_zona' => 'Jawa Barat Lainnya', 'tarif_per_kg' => 12000]);
        Tarif::firstOrCreate(['zona' => 3], ['nama_zona' => 'Luar Jawa Barat', 'tarif_per_kg' => 20000]);
    }
}
