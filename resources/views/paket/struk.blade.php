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

    @if ($paket->status_pembayaran)
        <h2>Setoran COD</h2>
        @if ($paket->setoran)
            <p>Status: Sudah disetor</p>
            <p>Jumlah setoran: Rp{{ number_format($paket->setoran->jumlah, 0, ',', '.') }}</p>
            <p>Waktu setoran: {{ $paket->setoran->disetor_pada->format('d-m-Y H:i') }}</p>
        @else
            <p>Status: Belum disetor</p>
            <p>Jumlah yang harus disetor: Rp{{ number_format($paket->nilai_cod, 0, ',', '.') }}</p>

            @if ($paket->status === 'Terkirim')
                <form method="POST" action="{{ route('paket.setoran', $paket) }}">
                    @csrf
                    <button type="submit">Catat Setoran COD</button>
                </form>
            @else
                <p>Setoran hanya dapat dicatat setelah paket berstatus Terkirim.</p>
            @endif
        @endif
    @endif

    <p>Kurir: {{ $paket->kurir ? $paket->kurir->kode . ' - ' . $paket->kurir->nama : '-' }}</p>
    <p>Jumlah gagal kirim: {{ $paket->jumlah_gagal }}</p>

    <h2>Tindakan</h2>
    @if ($paket->status === 'Diterima')
        <form method="POST" action="{{ route('paket.transit', $paket) }}">
            @csrf
            <input type="text" name="catatan" placeholder="Catatan (opsional)">
            <button type="submit">Proses ke Transit</button>
        </form>

    @elseif ($paket->status === 'Transit' || ($paket->status === 'Gagal Kirim' && $paket->jumlah_gagal < 2))
        <form method="POST" action="{{ route('paket.tugaskan', $paket) }}">
            @csrf
            <select name="id_kurir">
                <option value=""> pilih kurir </option>
                @foreach ($daftarKurirAktif as $kurir)
                    <option value="{{ $kurir->id }}">
                        {{ $kurir->kode }} - {{ $kurir->nama }} ({{ $kurir->jumlah_paket_dikirim }} paket Dikirim)
                    </option>
                @endforeach
            </select>
            <input type="text" name="catatan" placeholder="Catatan (opsional)">
            <button type="submit">Tugaskan</button>
        </form>

    @elseif ($paket->status === 'Gagal Kirim')
        <p>Paket sudah gagal 2 kali, tidak bisa dikirim ulang. Hanya boleh diubah menjadi Retur.</p>
        <form method="POST" action="{{ route('paket.retur', $paket) }}">
            @csrf
            <input type="text" name="catatan" placeholder="Catatan (opsional)">
            <button type="submit">Ubah ke Retur</button>
        </form>

    @elseif ($paket->status === 'Dikirim')
        <p>Menunggu kurir melaporkan hasil pengantaran.</p>

    @else
        <p>Status {{ $paket->status }} sudah final dan tidak bisa diubah lagi.</p>
    @endif

    <h2>Riwayat status</h2>
    <table border="1" cellpadding="6">
        <tr><th>Waktu</th><th>Dari</th><th>Ke</th><th>Pelaku</th><th>Catatan</th></tr>
        @foreach ($paket->daftarRiwayat as $riwayat)
            <tr>
                <td>{{ $riwayat->dibuat_pada->format('d-m-Y H:i') ?? '-' }}</td>
                <td>{{ $riwayat->status_dari ?? '-' }}</td>
                <td>{{ $riwayat->status_ke }}</td>
                <td>{{ $riwayat->tipe_pelaku }}: {{ $riwayat->nama_pelaku }}</td>
                <td>{{ $riwayat->catatan }}</td>
            </tr>
        @endforeach
    </table>

    <p><a href="{{ route('paket.create') }}">Input paket lagi</a> | <a href="{{ route('paket.index') }}">Daftar paket</a></p>
@endsection