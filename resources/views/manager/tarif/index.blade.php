<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Tarif - PaketIN</title>
</head>
<body>
    <h1>Pengaturan Tarif</h1>
    <p>Kelola tarif pengiriman berdasarkan zona tujuan.</p>
    <a href="{{ route('manager.dashboard') }}">Kembali ke Dashboard</a>

    @if (session('sukses'))
        <p style="color:green">{{ session('sukses') }}</p>
    @endif

    @if ($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Zona</th>
                <th>Nama Zona</th>
                <th>Tarif per Kg</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daftarTarif as $tarif)
                <tr>
                    <td>{{ $tarif->zona }}</td>
                    <td>{{ $tarif->nama_zona }}</td>
                    <td>Rp{{ number_format($tarif->tarif_per_kg, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('manager.tarif.update', $tarif->zona) }}">
                            @csrf
                            @method('PUT')
                            <input type="number" name="tarif_per_kg" value="{{ $tarif->tarif_per_kg }}" min="1" step="1" required>
                            <button type="submit">Simpan Tarif</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>