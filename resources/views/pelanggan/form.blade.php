@extends('layout.app')
@section('judul', 'Form Pelanggan')
@section('isi')
    <h1>{{ $pelanggan->exists ? 'Ubah' : 'Tambah' }} Pelanggan</h1>

    <form method="POST" action="{{ $pelanggan->exists ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}">
        @csrf
        @if ($pelanggan->exists) @method('PUT') @endif

        <div><label>Nama</label><br>
            <input type="text" name="nama" value="{{ old('nama', $pelanggan->nama) }}"></div>
        <div><label>Telepon</label><br>
            <input type="text" name="telepon" value="{{ old('telepon', $pelanggan->telepon) }}"></div>
        <div><label>Alamat</label><br>
            <input type="text" name="alamat" value="{{ old('alamat', $pelanggan->alamat) }}"></div>
        <div><label>Kota</label><br>
            <input type="text" name="kota" value="{{ old('kota', $pelanggan->kota) }}"></div>

        <button type="submit">Simpan</button>
    </form>
    <p><a href="{{ route('pelanggan.index') }}">Kembali</a></p>
@endsection