<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Kurir extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'kurir';

    protected $fillable = [
        'kode', 'nama', 'telepon', 'password', 'gantipassword', 'status',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'status' => 'boolean',
        'gantipassword' => 'boolean',
    ];

    public function daftarPaket()
    {
        return $this->hasMany(Paket::class, 'id_kurir', 'id');
    }
}
