@extends('layout.app')
@section('judul', 'Input Paket')
@section('isi')
    <h1>Input Paket</h1>

    <form method="POST" action="{{ route('paket.store') }}">
        @csrf

        <div><label>Pengirim</label><br>
            <select name="id_pelanggan">
                <option value=""> pilih pelanggan </option>
                @foreach ($daftarPelanggan as $pelanggan)
                    <option value="{{ $pelanggan->id }}"@selected(old('id_pelanggan') === (string) $pelanggan->id)>
                        {{ $pelanggan->nama }} ({{ $pelanggan->telepon }})
                    </option>
                @endforeach
            </select></div>

        <div><label>Nama penerima</label><br>
            <input type="text" name="nama_penerima" value="{{ old('nama_penerima') }}"></div>
        <div><label>Telepon penerima</label><br>
            <input type="text" name="telepon_penerima" value="{{ old('telepon_penerima') }}"></div>
        <div><label>Alamat penerima</label><br>
            <input type="text" name="alamat_penerima" value="{{ old('alamat_penerima') }}"></div>
        <div><label>Kota tujuan</label><br>
            <input type="text" name="kota_tujuan" value="{{ old('kota_tujuan') }}"></div>
        <div><label>Berat (kg)</label><br>
            <input type="number" name="berat" step="0.01" min="0.01" value="{{ old('berat') }}"></div>

        <div><label>Layanan</label><br>
            <select name="layanan">
                <option value="Reguler" @selected(old('layanan') === 'Reguler')>Reguler</option>
                <option value="Express" @selected(old('layanan') === 'Express')>Express</option>
            </select></div>

        <div>
            <label><input type="checkbox" name="status_pembayaran" value="1" id="cekCod" @checked(old('status_pembayaran'))> Paket COD</label>
        </div>
        <div id="kolomNilaiCod">
            <label>Nilai COD (Rp)</label><br>
            <input type="number" name="nilai_cod" min="1" value="{{ old('nilai_cod') }}">
        </div>

        @if (session('perlu_zona_manual') || old('zona_manual'))
            <div style="border:1px solid orange; padding:8px; margin-top:8px">
                <label>Zona manual (layanan Pusat Wilayah tidak menjawab)</label><br>
                <select name="zona_manual">
                    <option value=""> Pilih zona </option>
                    <option value="1" @selected(old('zona_manual') == 1)>Zona 1 - Bandung Raya</option>
                    <option value="2" @selected(old('zona_manual') == 2)>Zona 2 - Jawa Barat lainnya</option>
                    <option value="3" @selected(old('zona_manual') == 3)>Zona 3 - Luar Jawa Barat</option>
                </select>
            </div>
        @endif

        <p><button type="submit">Simpan</button></p>
    </form>

    <script>
        const cekCod = document.getElementById('cekCod');
        const kolomNilaiCod = document.getElementById('kolomNilaiCod');
        function aturKolomCod() {
            kolomNilaiCod.style.display = cekCod.checked ? 'block' : 'none';
        }
        cekCod.addEventListener('change', aturKolomCod);
        aturKolomCod();
    </script>
@endsection