<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'PaketIN')</title>
</head>
<body>
    <nav>
        <a href="{{ route('dashboard') }}">Dashboard</a> |
        <a href="{{ route('pelanggan.index') }}">Pelanggan</a> |
        <a href="{{ route('kurir.index') }}">Kurir</a> |            @if (request()->user()->role === 'manager')
        <a href="{{ route('manager.tarif.index') }}">Pengaturan Tarif</a> |
        <a href="{{ route('manager.laporan.index') }}">Laporan</a> |
        @endif
        <a href="{{ route('password.ganti') }}">Ganti password</a> |
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
    <hr>
    @if (session('sukses'))
        <p style="color:green">{{ session('sukses') }}</p>
    @endif
    @if ($errors->any())
        <ul style="color:red">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    @endif
    @yield('isi')
</body>
</html>