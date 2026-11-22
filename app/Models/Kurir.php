<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurir extends Model
{
    protected $table = 'kurir';

    protected $fillable = ['kode','nama','telepon','password','gantipassword','status'];

    protected $hidden = ['password'];

    protected $casts = ['status' => 'boolean','gantipassword' => 'boolean',];

    public function daftarPaket()
    {
        return $this->hasMany(Paket::class,'id_kurir','id');
    }
}