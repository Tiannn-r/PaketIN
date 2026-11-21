@extends('layout.app')
@section('judul', 'Form Kurir')
@section('isi')
    <h1>{{ $kurir->exists ? 'Ubah' : 'Tambah' }} Kurir</h1>

    <form method="POST" action="{{ $kurir->exists ? route('kurir.update', $kurir) : route('kurir.store') }}">
        @csrf
        @if ($kurir->exists) @method('PUT') @endif

        @if ($kurir->exists)
            <p>Kode kurir: <strong>{{ $kurir->kode }}</strong></p>
        @endif

        <div><label>Nama</label><br>
            <input type="text" name="nama" value="{{ old('nama', $kurir->nama) }}"></div>
        <div><label>Telepon</label><br>
            <input type="text" name="telepon" value="{{ old('telepon', $kurir->telepon) }}"></div>

        <button type="submit">Simpan</button>
    </form>

    @unless ($kurir->exists)
        <p>Kode kurir dan password sementara dibuat otomatis setelah disimpan.</p>
    @endunless

    <p><a href="{{ route('kurir.index') }}">Kembali</a></p>
@endsection