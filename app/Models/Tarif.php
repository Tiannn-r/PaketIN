<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $table = 'tarif';
    protected $primaryKey = 'zona';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['zona', 'nama_zona', 'tarif_per_kg', 'diubah_oleh'];
}