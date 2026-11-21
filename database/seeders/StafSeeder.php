<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StafSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            ['nama' => 'Admin', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        User::firstOrCreate(
            ['username' => 'manager'],
            ['nama' => 'Dedi', 'password' => Hash::make('manager123'), 'role' => 'manager']
        );
    }
}
