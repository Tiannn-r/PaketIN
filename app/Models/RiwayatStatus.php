<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';
    protected $fillable = ['id_paket', 'status_dari', 'status_ke', 'tipe_pelaku', 'id_pelaku', 'catatan'];
    public $timestamps = false;
}
