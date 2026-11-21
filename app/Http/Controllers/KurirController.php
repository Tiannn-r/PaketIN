<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kurir;
use App\Rules\TeleponValid;
use App\Support\Telepon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KurirController extends Controller
{
    public function index(){
        $daftarKurir = Kurir::withCount([
            'daftarPaket as jumlah_paket_dikirim' => fn ($query) => $query->where('status', 'Dikirim'),
        ])->orderBy('kode')->get();

        return view('kurir.index', compact('daftarKurir'));
    }

    public function create(){
        return view('kurir.form', ['kurir' => new Kurir()]);
    }

    public function store(Request $request){
        $dataKurir = $this->periksaInput($request);

        $passwordSementara = Str::random(10);

        $kurirBaru = kurir::create([
            'kode' => $this->buatKodeKurirBerikutnya(),
            'nama' => $dataKurir['nama'],
            'telepon' => $dataKurir['telepon'],
            'password' => Hash::make($passwordSementara),
            'gantipassword' => true,
            'status' => true,
        ]);

        return redirect()-> route('kurir.index')->with('akun_baru', [
            'kode' => $kurirBaru->kode,
            'password' => $passwordSementara,
        ]);
    }

    public function edit(Kurir $kurir){
        return view('kurir.form', compact('kurir'));
    }

    public function update(Request $request, Kurir $kurir){
        $dataKurir = $this->periksaInput($request);

        $kurir->update($dataKurir);

        return redirect()-> route('kurir.index')->with('sukses', 'Data kurir berhasil di perbarui');
    }

    public function nonaktifkan(Kurir $kurir){
        $jumlahPaketDikirim = $kurir->daftarPaket()->where('status', 'Dikirim')->count();

        if($jumlahPaketDikirim > 0){
            return back()->withErrors([
                'kurir' => "Kurir {$kurir->kode} masih membawa {$jumlahPaketDikirim} paket berstatus Dikirim, jadi belum bisa untuk di nonaktifkan",
            ]);
        }

        $kurir->update(['status' => false ]);

        return back()->with('sukses', "Kurir {$kurir->kode} di nonaktifkan");
    }

    public function aktifkan(Kurir $kurir){
        $kurir->update(['status' => true]);

        return back()->with('sukses', "Kurir {$kurir->kode} diaktifkan kembali");
    }

    public function resetPassword(Kurir $kurir){
        $passwordSementara = Str::random(10);

        $kurir->update([
            'password' => Hash::make($passwordSementara),
            'gantipassword' => true,
        ]);

        return redirect()->route('kurir.index')->with('akun_baru', [
            'kode' => $kurir->kode,
            'password' => $passwordSementara,
        ]);
    }

    private function periksaInput(Request $request): array{
        $dataKurir = $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => ['required', new  TeleponValid],
        ]);

        $dataKurir['nama'] = mb_convert_case(trim($dataKurir['nama']), MB_CASE_TITLE);
        $dataKurir['telepon'] = Telepon::rapikan($dataKurir['telepon']);

        return $dataKurir;
    }

    private function buatKodeKurirBerikutnya(): string{
        $nomorTerbesar = Kurir::pluck('kode')
        ->map(fn ($kode) => (int) substr($kode, 4))
        ->max() ?? 0;

        return sprintf('KUR-%02d', $nomorTerbesar + 1);
    }
}
