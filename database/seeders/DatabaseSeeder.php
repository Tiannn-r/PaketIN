<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StafSeeder::class,
            TarifSeeder::class,
            KurirSeeder::class,
            PelangganSeeder::class,
            PaketHistorisSeeder::class,
        ]);
    }
}