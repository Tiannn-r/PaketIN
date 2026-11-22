@extends('layout.app')
@section('judul', 'Pelanggan')
@section('isi')
    <h1>Data Pelanggan</h1>

    @if (request()->user()->role === 'admin')
        <p><a href="{{ route('pelanggan.create') }}">Tambah pelanggan</a></p>
    @endif

    <table border="1" cellpadding="6">
        <tr>
            <th>Nama</th><th>Telepon</th><th>Alamat</th><th>Kota</th>
            @if (request()->user()->role === 'admin') <th>Aksi</th> @endif
        </tr>
        @foreach ($daftar as $p)
            <tr>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->telepon }}</td>
                <td>{{ $p->alamat }}</td>
                <td>{{ $p->kota }}</td>
                @if (request()->user()->role === 'admin')
                    <td><a href="{{ route('pelanggan.index', $p) }}">Ubah</a></td>
                @endif
            </tr>
        @endforeach
    </table>
@endsection