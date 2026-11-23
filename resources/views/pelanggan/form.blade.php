
@extends('layout.app')

@section('judul', $pelanggan->exists ? 'Ubah Pelanggan' : 'Tambah Pelanggan')

@section('isi')
    <div class="form-modal-backdrop">
        <section class="form-modal-card">
            <div class="form-modal-header">
                <div>
                    <h1>{{ $pelanggan->exists ? 'Ubah Pelanggan' : 'Tambah Pelanggan' }}</h1>
                    <p>Informasi pelanggan</p>
                </div>
            </div>

            <form method="POST" action="{{ $pelanggan->exists ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}">
                @csrf
                @if ($pelanggan->exists)
                    @method('PUT')
                @endif

                <div class="form-modal-grid">
                    <div class="form-modal-field">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $pelanggan->nama) }}" placeholder="Nama pelanggan">
                        @error('nama')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-modal-field">
                        <label for="telepon">Telepon</label>
                        <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $pelanggan->telepon) }}" placeholder="Nomor telepon">
                        @error('telepon')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-modal-field">
                        <label for="alamat">Alamat</label>
                        <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $pelanggan->alamat) }}" placeholder="Alamat pelanggan">
                        @error('alamat')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-modal-field">
                        <label for="kota">Kota</label>
                        <input type="text" id="kota" name="kota" value="{{ old('kota', $pelanggan->kota) }}" placeholder="Kota">
                        @error('kota')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-modal-actions">
                    <a href="{{ route('pelanggan.index') }}" class="form-modal-cancel">Batal</a>
                    <button type="submit" class="form-modal-submit">Simpan</button>
                </div>
            </form>
        </section>
    </div>
@endsection
