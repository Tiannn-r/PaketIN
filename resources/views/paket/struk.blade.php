@extends('layout.app')
@section('judul', 'Struk Paket')
@section('isi')
    <h1>Paket Tersimpan</h1>
    <h2>Nomor resi: {{ $paket->no_resi }}</h2>

    <table border="1" cellpadding="6">
        <tr><td>Pengirim</td><td>{{ $paket->pelanggan->nama }}</td></tr>
        <tr><td>Penerima</td><td>{{ $paket->nama_penerima }} ({{ $paket->telepon_penerima }})</td></tr>
        <tr><td>Alamat</td><td>{{ $paket->alamat_penerima }}</td></tr>
        <tr><td>Kota tujuan</td>
            <td>{{ $paket->kota_tujuan }}, Zona {{ $paket->zona }}@if ($paket->status_zona) (zona manual)@endif</td></tr>
        <tr><td>Berat</td><td>{{ $paket->berat_asli }} kg, dibulatkan menjadi {{ $paket->berat_bulat }} kg</td></tr>
        <tr><td>Layanan</td><td>{{ $paket->layanan }}</td></tr>
        <tr><td>Ongkir</td><td>Rp{{ number_format($paket->ongkir, 0, ',', '.') }}</td></tr>
        @if ($paket->status_pembayaran)
            <tr><td>Biaya COD</td><td>Rp{{ number_format($paket->biaya_cod, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td><strong>Dibayar pengirim di loket</strong></td>
            <td><strong>Rp{{ number_format($paket->ongkir + $paket->biaya_cod, 0, ',', '.') }}</strong></td></tr>
        @if ($paket->status_pembayaran)
            <tr><td>Nilai COD (ditagih kurir ke penerima)</td>
                <td>Rp{{ number_format($paket->nilai_cod, 0, ',', '.') }}</td></tr>
        @endif
        <tr><td>Status</td><td>{{ $paket->status }}</td></tr>
    </table>

    <p><a href="{{ route('paket.create') }}">Input paket lagi</a> | <a href="{{ route('paket.index') }}">Daftar paket</a></p>
@endsection