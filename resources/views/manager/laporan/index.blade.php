<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PaketIN</title>
</head>
<body>
    <h1>Laporan Pengiriman</h1>
    <a href="{{ route('manager.dashboard') }}">Kembali ke Dashboard</a>

    <h2>Filter Periode Laporan</h2>
    <form method="GET" action="{{ route('manager.laporan.index') }}">
        <label>Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" required>
        <label>Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" required>
        <button type="submit">Tampilkan Laporan</button>
    </form>

    <hr>
    <h2>1. Laporan Pengiriman</h2>
    <p>Periode: {{ $tanggalMulai }} sampai {{ $tanggalSelesai }}</p>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead><tr><th>Status</th><th>Jumlah Paket</th></tr></thead>
        <tbody>
            @foreach ($laporanPengiriman as $item)
                <tr><td>{{ $item['status'] }}</td><td>{{ $item['jumlah'] }}</td></tr>
            @endforeach
            <tr><th>Total Paket</th><th>{{ $laporanPengiriman->sum('jumlah') }}</th></tr>
        </tbody>
    </table>

    <hr>
    <h2>2. Pendapatan per Zona</h2>
    <p>Periode: {{ $tanggalMulai }} sampai {{ $tanggalSelesai }}</p>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead><tr><th>Zona</th><th>Jumlah Paket</th><th>Total Pendapatan</th></tr></thead>
        <tbody>
            @forelse ($pendapatanPerZona as $item)
                <tr><td>Zona {{ $item->zona }}</td><td>{{ $item->jumlah_paket }}</td><td>Rp{{ number_format($item->total_pendapatan, 0, ',', '.') }}</td></tr>
            @empty
                <tr><td colspan="3">Tidak ada paket pada periode ini.</td></tr>
            @endforelse
            <tr><th>Total</th><th>{{ $pendapatanPerZona->sum('jumlah_paket') }}</th><th>Rp{{ number_format($pendapatanPerZona->sum('total_pendapatan'), 0, ',', '.') }}</th></tr>
        </tbody>
    </table>

    <hr>
    <h2>3. Rekap COD per Kurir</h2>
    <p>Seluruh paket COD berstatus Terkirim sampai hari ini, tanpa batas periode.</p>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead><tr><th>Kode Kurir</th><th>Nama Kurir</th><th>COD Belum Disetor</th><th>COD Sudah Disetor</th></tr></thead>
        <tbody>
            @forelse ($rekapCodPerKurir as $item)
                <tr><td>{{ $item->kode }}</td><td>{{ $item->nama }}</td><td>Rp{{ number_format($item->cod_belum_disetor, 0, ',', '.') }}</td><td>Rp{{ number_format($item->cod_sudah_disetor, 0, ',', '.') }}</td></tr>
            @empty
                <tr><td colspan="4">Belum ada data kurir.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>