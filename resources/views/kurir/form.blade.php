@extends('layout.app')
@section('judul', $kurir->exists ? 'Ubah Kurir' : 'Tambah Kurir')
@section('isi')
<div class="courier-form-page">
    <div class="courier-form-heading">
        <div>
            <h1>{{ $kurir->exists ? 'Ubah Kurir' : 'Tambah Kurir' }}</h1>
            <p>{{ $kurir->exists ? 'Perbarui informasi kurir.' : 'Masukkan informasi kurir baru.' }}</p>
        </div>
        <a href="{{ route('kurir.index') }}" class="courier-back-button">Kembali</a>
    </div>

    <form method="POST" action="{{ $kurir->exists ? route('kurir.update', $kurir) : route('kurir.store') }}" class="courier-form-card">
        @csrf
        @if ($kurir->exists)
            @method('PUT')
            <div class="courier-field">
                <label>Kode Kurir</label>
                <input type="text" value="{{ $kurir->kode }}" disabled>
            </div>
        @endif

        <div class="courier-field">
            <label for="courierName">Nama</label>
            <input id="courierName" type="text" name="nama" value="{{ old('nama', $kurir->nama) }}">
            @error('nama')
                <small class="courier-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="courier-field">
            <label for="courierPhone">Telepon</label>
            <input id="courierPhone" type="text" name="telepon" value="{{ old('telepon', $kurir->telepon) }}">
            @error('telepon')
                <small class="courier-error">{{ $message }}</small>
            @enderror
        </div>

        @unless ($kurir->exists)
            <p class="courier-form-note">Kode kurir dan password sementara dibuat otomatis setelah data disimpan.</p>
        @endunless

        <div class="courier-form-actions">
            <a href="{{ route('kurir.index') }}" class="courier-cancel-button">Batal</a>
            <button type="submit" class="courier-primary-button">Simpan</button>
        </div>
    </form>
</div>
@endsection