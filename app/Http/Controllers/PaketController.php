<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Rules\TeleponValid;
use App\Services\PaketService;
use App\Services\Wilayah;
use App\Services\WilayahClient;
use App\Support\Telepon;

class PaketController extends Controller
{
    public function index(){

        $daftarPaket = Paket::with('pelanggan')->orderByDesc('id')->limit(100)->get();

        return view('paket.index', compact('daftarPaket'));
    }

    public function create(){
        $daftarPelanggan = Pelanggan::orderBy('nama')->get();

        return view('paket.form', compact('daftarPelanggan'));
    }

    public function store(Request $request, Wilayah $wilayah, PaketService $paketService){

        $dataPaket = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id_pelanggan',
            'nama_penerima' => 'required|string|max:100',
            'telepon_penerima' => ['required', new TeleponValid],
            'alamat_penerima' => 'required|string|max:255',
            'kota_tujuan' => 'required|string|max:100',
            'berat' => 'required|numeric|gt:0|max:1000',
            'layanan' => 'required|in:Reguler,Express',
            'nilai_cod' => 'required_if:status_pembayaran,1|nullable|integer|min:1',
            'zona_manual' => 'nullable|in:1,2,3',
        ]);

        $dataPaket['status_pembayaran']= $request->boolean('status_pembayaran');
        $dataPaket['nama_penerima'] = mb_convert_case(trim($dataPaket['nama_penerima']), MB_CASE_TITLE, 'UTF-8');
        $dataPaket['telepon_penerima'] = Telepon::rapikan($dataPaket['telepon_penerima']);
        $dataPaket['kota_tujuan'] = trim($dataPaket['kota_tujuan']);


        $zona = $wilayah->cariZona($dataPaket['kota_tujuan']);
        $zonaDipilihManual = false;

        if ($zona === null){
            if (empty($dataPaket['zona_manual'])) {
                return back()
                ->withInput()
                ->with('perlu_zona_manual', true)
                ->withErrors(['zona_manual' => 'Zona tidak bisa ditemukan otomatis. Pilih zona secaara manual, lalu simpan lagi']);
            }

            $zona = (int) $dataPaket['zona_manual'];
            $zonaDipilihManual = true;
        }

        $paketBaru = $paketService->buatPaket($dataPaket, $zona, $zonaDipilihManual, $request->user()-> id);

        return redirect()->route('paket.show', $paketBaru);
    }

    public function show(Paket $paket){
        $paket->load(['pelanggan', 'daftarRiwayat']);

        return view('paket.struk', compact('paket'));
    }
}
