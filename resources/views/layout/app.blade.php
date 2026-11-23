<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'PaketIN')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @unless (request()->routeIs('admin.dashboard', 'manager.dashboard'))
        <header class="site-header">
            <div class="site-header__inner">
                <span class="brand">PaketIN</span>
                <nav class="nav" aria-label="Navigasi utama">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="nav-link {{ request()->routeIs('pelanggan.*') ? 'is-active' : '' }}" href="{{ route('pelanggan.index') }}">Pelanggan</a>
                    <a class="nav-link {{ request()->routeIs('kurir.*') ? 'is-active' : '' }}" href="{{ route('kurir.index') }}">Kurir</a>
                    @if (request()->user()->role === 'admin')
                        <a class="nav-link {{ request()->routeIs('paket.*') ? 'is-active' : '' }}" href="{{ route('paket.index') }}">Paket</a>
                        <a class="nav-link {{ request()->routeIs('tarif.*') ? 'is-active' : '' }}" href="{{ route('tarif.lihat') }}">Tarif</a>
                    @endif
                    @if (request()->user()->role === 'manager')
                        <a class="nav-link {{ request()->routeIs('manager.tarif.*') ? 'is-active' : '' }}" href="{{ route('manager.tarif.index') }}">Tarif</a>
                        <a class="nav-link {{ request()->routeIs('manager.laporan.*') ? 'is-active' : '' }}" href="{{ route('manager.laporan.index') }}">Laporan</a>
                    @endif
                    <a class="nav-link {{ request()->routeIs('password.ganti') ? 'is-active' : '' }}" href="{{ route('password.ganti') }}">Ganti Password</a>
                    <form class="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Logout</button>
                    </form>
                </nav>
            </div>
        </header>
    @endunless

    <main class="page-container">
        @if (session('sukses'))
            <div class="notice" role="status">{{ session('sukses') }}</div>
        @endif

        @if ($errors->any())
            <div class="notice" role="alert">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('isi')
    </main>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
