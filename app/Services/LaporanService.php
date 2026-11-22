<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\Kurir;

class LaporanService
{
    public function buatLaporan(string $tanggalMulai, string $tanggalSelesai): array
    {
        $jumlahStatus = Paket::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $daftarStatus = ['Diterima', 'Transit', 'Dikirim', 'Terkirim', 'Gagal Kirim', 'Retur'];
        $laporanPengiriman = collect($daftarStatus)->map(fn ($status) => ['status' => $status, 'jumlah' => (int) $jumlahStatus->get($status, 0)]);

        $pendapatanPerZona = Paket::whereBetween('tanggal_masuk', [$tanggalMulai, $tanggalSelesai])
            ->select('zona')
            ->selectRaw('COUNT(*) as jumlah_paket, SUM(COALESCE(ongkir, 0) + COALESCE(biaya_cod, 0)) as total_pendapatan')
            ->groupBy('zona')->orderBy('zona')->get();

        $rekapCodPerKurir = Kurir::query()
            ->leftJoin('paket', function ($join) {
                $join->on('paket.id_kurir', '=', 'kurir.id')->where('paket.status', 'Terkirim')->where('paket.status_pembayaran', 1);
            })
            ->leftJoin('setoran', 'setoran.id_paket', '=', 'paket.id')
            ->select('kurir.id', 'kurir.kode', 'kurir.nama')
            ->selectRaw('COALESCE(SUM(CASE WHEN paket.id IS NOT NULL AND setoran.id IS NULL THEN paket.nilai_cod ELSE 0 END), 0) as cod_belum_disetor')
            ->selectRaw('COALESCE(SUM(CASE WHEN setoran.id IS NOT NULL THEN paket.nilai_cod ELSE 0 END), 0) as cod_sudah_disetor')
            ->groupBy('kurir.id', 'kurir.kode', 'kurir.nama')
            ->orderBy('kurir.kode')->get();

        return compact('laporanPengiriman', 'pendapatanPerZona', 'rekapCodPerKurir');
    }
}