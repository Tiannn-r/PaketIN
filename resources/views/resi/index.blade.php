<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Resi - PaketIN</title>
</head>
<body>
    <h1>PaketIN - Cek Resi</h1>
    <p>Masukkan nomor resi untuk mengetahui status pengiriman paket.</p>

    <form method="POST" action="{{ route('resi.cek') }}">
        @csrf
        <label for="no_resi">Nomor Resi</label>
        <input type="text" id="no_resi" name="no_resi" value="{{ old('no_resi') }}" placeholder="Contoh: PKE-261122-0001" required>
        <button type="submit">Cek Resi</button>
    </form>

    @error('no_resi')
        <p style="color:red">{{ $message }}</p>
    @enderror

    @isset($noResi)
        <hr>
        <h2>Hasil Pelacakan</h2>
        <p>Nomor Resi: {{ $noResi }}</p>
        <p>Status Terkini: <strong>{{ $statusPaket }}</strong></p>
        <p>Telepon Penerima: {{ $teleponDisamarkan }}</p>

        <h3>Riwayat Pengiriman</h3>

        @if ($riwayat->isEmpty())
            <p>Riwayat pengiriman belum tersedia.</p>
        @else
            <table border="1" cellpadding="6">
                <tr>
                    <th>Waktu</th>
                    <th>Dari</th>
                    <th>Ke</th>
                    <th>Catatan</th>
                </tr>
                @foreach ($riwayat as $item)
                    <tr>
                        <td>{{ $item['waktu']?->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $item['status_dari'] ?? '-' }}</td>
                        <td>{{ $item['status_ke'] }}</td>
                        <td>{{ $item['catatan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endisset
</body>
</html>