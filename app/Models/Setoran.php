<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    protected $table = 'setoran';
    public $timestamps = false;
    protected $fillable = ['id_paket', 'jumlah', 'dicatat_oleh', 'disetor_pada'];
    protected $casts = ['jumlah' => 'integer', 'disetor_pada' => 'datetime'];

    public function paket() { return $this->belongsTo(Paket::class, 'id_paket', 'id'); }
}