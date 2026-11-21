<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $table = 'tarif';
    protected $fillable = ['zone', 'nama_zona', 'tarif_per_kg', 'diubah_oleh'];
}
