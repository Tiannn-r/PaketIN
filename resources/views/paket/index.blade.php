@extends('layout.app')
@section('judul', 'Daftar Paket')
@section('isi')
    <h1>Daftar Paket</h1>
    <p><a href="{{ route('paket.create') }}">Input paket</a></p>

    <table border="1" cellpadding="6">
        <tr><th>Resi</th><th>Pengirim</th><th>Penerima</th><th>Kota</th><th>Zona</th><th>Layanan</th><th>COD</th><th>Status</th></tr>
        @foreach ($daftarPaket as $paket)
            <tr>
                <td><a href="{{ route('paket.show', $paket) }}">{{ $paket->no_resi }}</a></td>
                <td>{{ $paket->pelanggan->nama }}</td>
                <td>{{ $paket->nama_penerima }}</td>
                <td>{{ $paket->kota_tujuan }}</td>
                <td>{{ $paket->zona }}@if ($paket->status_zona) (manual)@endif</td>
                <td>{{ $paket->layanan }}</td>
                <td>{{ $paket->status_pembayaran ? 'Rp' . number_format($paket->nilai_cod, 0, ',', '.') : '-' }}</td>
                <td>{{ $paket->status }}</td>
            </tr>
        @endforeach
    </table>
@endsection