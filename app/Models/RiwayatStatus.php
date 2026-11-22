<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';
    protected $fillable = ['id_paket', 'status_dari', 'status_ke', 'tipe_pelaku', 'id_pelaku', 'catatan'];
    public $timestamps = false;

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    public function getNamaPelakuAttribute(): string {
        if ($this->tipe_pelaku === 'Admin'){
            return \App\Models\User::find($this->id_pelaku)?->nama ?? '-';
        }

        if ($this->tipe_pelaku === "Kurir") {
           return \App\Models\Kurir::find($this->id_pelaku)?->nama ?? '-';
        }
            return $this->tipe_pelaku;
        }

}
