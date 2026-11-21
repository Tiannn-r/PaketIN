@extends('layout.app')
@section('judul', 'Kurir')
@section('isi')
    <h1>Data Kurir</h1>

    @if (session('akun_baru'))
        <div style="border:1px solid orange; padding:10px; margin-bottom:10px">
            <p>Akun kurir siap dipakai. Catat sekarang, password ini tidak ditampilkan lagi.</p>
            <p>Kode kurir: <strong>{{ session('akun_baru')['kode'] }}</strong></p>
            <p>Password sementara: <strong>{{ session('akun_baru')['password'] }}</strong></p>
        </div>
    @endif

    @if (request()->user()->role === 'admin')
        <p><a href="{{ route('kurir.create') }}">Tambah kurir</a></p>
    @endif

    <table border="1" cellpadding="6">
        <tr>
            <th>Kode</th><th>Nama</th><th>Telepon</th><th>Status</th><th>Paket Dikirim</th>
            @if (request()->user()->role === 'admin') <th>Aksi</th> @endif
        </tr>
        @foreach ($daftarKurir as $kurir)
            <tr>
                <td>{{ $kurir->kode }}</td>
                <td>{{ $kurir->nama }}</td>
                <td>{{ $kurir->telepon }}</td>
                <td>{{ $kurir->status ? 'Aktif' : 'Nonaktif' }}</td>
                <td>{{ $kurir->jumlah_paket_dikirim }}</td>
                @if (request()->user()->role === 'admin')
                    <td>
                        <a href="{{ route('kurir.edit', $kurir) }}">Ubah</a>

                        @if ($kurir->status)
                            <form method="POST" action="{{ route('kurir.nonaktifkan', $kurir) }}" style="display:inline">
                                @csrf
                                <button type="submit">Nonaktifkan</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('kurir.aktifkan', $kurir) }}" style="display:inline">
                                @csrf
                                <button type="submit">Aktifkan</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('kurir.reset', $kurir) }}" style="display:inline"
                              onsubmit="return confirm('Reset password kurir ini?')">
                            @csrf
                            <button type="submit">Reset password</button>
                        </form>
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
@endsection