@extends('layout.app')

@section('judul', 'Input Paket')

@section('isi')
<section class="customer-page">
    <div class="customer-heading">
        <div>
            <h1>Input Paket</h1>
            <p>Masukkan informasi pengiriman baru.</p>
        </div>
        <a href="{{ route('paket.index') }}" class="report-back-button">Daftar Paket</a>
    </div>

    @if ($errors->any())
        <div class="package-form-error">{{ $errors->first() }}</div>
    @endif

    <section class="form-modal-card package-create-card">
        <form method="POST" action="{{ route('paket.store') }}">
            @csrf

            <div class="form-modal-grid">
                <div class="form-modal-field package-form-wide">
                    <label for="idPelanggan">Pengirim</label>
                    <select name="id_pelanggan" id="idPelanggan">
                        <option value="">Pilih pelanggan</option>
                        @foreach ($daftarPelanggan as $pelanggan)
                            <option value="{{ $pelanggan->id }}" @selected(old('id_pelanggan') === (string) $pelanggan->id)>
                                {{ $pelanggan->nama }} ({{ $pelanggan->telepon }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_pelanggan')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field">
                    <label for="namaPenerima">Nama Penerima</label>
                    <input type="text" id="namaPenerima" name="nama_penerima" value="{{ old('nama_penerima') }}" placeholder="Nama penerima">
                    @error('nama_penerima')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field">
                    <label for="teleponPenerima">Telepon Penerima</label>
                    <input type="text" id="teleponPenerima" name="telepon_penerima" value="{{ old('telepon_penerima') }}" placeholder="Nomor telepon">
                    @error('telepon_penerima')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field package-form-wide">
                    <label for="alamatPenerima">Alamat Penerima</label>
                    <input type="text" id="alamatPenerima" name="alamat_penerima" value="{{ old('alamat_penerima') }}" placeholder="Alamat lengkap penerima">
                    @error('alamat_penerima')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field">
                    <label for="kotaTujuan">Kota Tujuan</label>
                    <input type="text" id="kotaTujuan" name="kota_tujuan" value="{{ old('kota_tujuan') }}" placeholder="Kota tujuan">
                    @error('kota_tujuan')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field">
                    <label for="beratPaket">Berat (kg)</label>
                    <input type="number" id="beratPaket" name="berat" step="0.01" min="0.01" value="{{ old('berat') }}" placeholder="0.00">
                    @error('berat')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-field package-form-wide">
                    <label for="layananPaket">Layanan</label>
                    <select name="layanan" id="layananPaket">
                        <option value="Reguler" @selected(old('layanan') === 'Reguler')>Reguler</option>
                        <option value="Express" @selected(old('layanan') === 'Express')>Express</option>
                    </select>
                    @error('layanan')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="package-cod-toggle">
                <label>
                    <input type="checkbox" name="status_pembayaran" value="1" id="cekCod" @checked(old('status_pembayaran'))>
                    Paket COD
                </label>
            </div>

            <div class="form-modal-field package-cod-field" id="kolomNilaiCod" @if (!old('status_pembayaran')) hidden @endif>
                <label for="nilaiCod">Nilai COD (Rp)</label>
                <input type="number" id="nilaiCod" name="nilai_cod" min="1" value="{{ old('nilai_cod') }}" placeholder="Masukkan nilai COD">
                @error('nilai_cod')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            @if (session('perlu_zona_manual') || old('zona_manual'))
                <div class="package-zone-manual">
                    <strong>Zona Manual</strong>
                    <p>Layanan Pusat Wilayah tidak menjawab. Silakan pilih zona tujuan.</p>
                    <div class="form-modal-field">
                        <label for="zonaManual">Zona Tujuan</label>
                        <select name="zona_manual" id="zonaManual">
                            <option value="">Pilih zona</option>
                            <option value="1" @selected(old('zona_manual') == 1)>Zona 1 - Bandung Raya</option>
                            <option value="2" @selected(old('zona_manual') == 2)>Zona 2 - Jawa Barat lainnya</option>
                            <option value="3" @selected(old('zona_manual') == 3)>Zona 3 - Luar Jawa Barat</option>
                        </select>
                        @error('zona_manual')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="form-modal-actions">
                <a href="{{ route('paket.index') }}" class="form-modal-cancel">Batal</a>
                <button type="submit" class="form-modal-submit">Simpan</button>
            </div>
        </form>
    </section>
</section>
@endsection
