<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LaporanService;

class LaporanController extends Controller
{
    public function index(Request $request, LaporanService $laporanService)
    {
        $tanggalMulai = $request->query('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->query('tanggal_selesai', now()->toDateString());

        $data = validator(['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai], ['tanggal_mulai' => 'required|date_format:Y-m-d', 'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai'])->validate();

        $hasilLaporan = $laporanService->buatLaporan($data['tanggal_mulai'], $data['tanggal_selesai']);

        return view('manager.laporan.index', $hasilLaporan + ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai]);
    }
}