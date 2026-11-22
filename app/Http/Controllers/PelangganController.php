<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Support\Telepon;
use App\Rules\TeleponValid;

class PelangganController extends Controller
{
    public function index(){
        $daftar = Pelanggan::orderBy('nama')->get();
        return view ('pelanggan.index', compact('daftar'));
    }

    public function create()
    {
        return view('pelanggan.form', ['pelanggan' => new Pelanggan()]);
    }

    public function store(Request $request){
        $data = $this->validasi($request);

        if (Pelanggan::where('telepon', $data['telepon'])->exists()) {
            return back()->withErrors(['telepon' => 'Nomor telepon sudah terdaftar'])->withInput();
        }

        Pelanggan::create($data);
        return redirect()->route('pelanggan.index')->with('sukses', 'Pelanggan berhasil ditambahkan');
    }

    public function edit (Pelanggan $pelanggan){
        return view ('pelanggan.form', compact('pelanggan'));
    }

    public function update (Request $request, Pelanggan $pelanggan){
        $data = $this->validasi($request);

        $dipakai = Pelanggan::where('telepon', $data['telepon'])
        ->where('id', '!=', $pelanggan->id)
        ->exists();

        if($dipakai){
            return back()->withErrors(['telepon' => 'nomor telepon sudah dipakai pelanggan lain'])-> withInput();
        }

        $pelanggan->update($data);
        return redirect()->route('pelanggan.index')->with('sukses', 'pelanggan berhasil diperbarui');
    }

    public function validasi(Request $request): array{
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => ['required', new TeleponValid],
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:100',
        ]);

        $data['nama'] = mb_convert_case(trim($data['nama']), MB_CASE_TITLE, 'UTF-8');
        $data['telepon'] = Telepon::rapikan($data['telepon']);

        return $data;
    }
}
