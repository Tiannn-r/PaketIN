<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
   protected $table = 'paket';

    protected $fillable = [
        'no_resi', 'id_pelanggan', 'id_kurir', 'dibuat_oleh',
        'nama_penerima', 'telepon_penerima', 'alamat_penerima', 'kota_tujuan',
        'zona', 'status_zona', 'berat_asli', 'berat_bulat', 'layanan',
        'tarif_per_kg', 'ongkir', 'status_pembayaran', 'nilai_cod', 'biaya_cod',
        'status', 'jumlah_gagal', 'perlu_verifikasi', 'tanggal_masuk',
    ];

    protected $casts = [
        'status_zona' => 'boolean',
        'status_pembayaran' => 'boolean',
        'perlu_verifikasi' => 'boolean',
        'tanggal_masuk' => 'date',
    ];

    public function pelanggan(){
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }

    public function kurir(){
        return $this->belongsTo(Kurir::class, 'id_kurir', 'id');
    }

    public function daftarRiwayat(){
        return $this->hasMany(RiwayatStatus::class, 'id_paket')->orderBy('id');
    }
}
