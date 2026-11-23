@extends('layout.app')

@section('judul', 'Daftar Tarif')

@section('isi')
    <section class="customer-page">
        <div class="customer-heading">
            <div>
                <h1>Daftar Tarif</h1>
                <p>Tarif per kg tiap zona (hanya Manajer yang dapat mengubah). Express +50%.</p>
            </div>
        </div>

        <div class="customer-table-wrap">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Nama Zona</th>
                        <th>Tarif per Kg</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarTarif as $tarif)
                        <tr>
                            <td>{{ $tarif->zona }}</td>
                            <td class="customer-name">{{ $tarif->nama_zona }}</td>
                            <td>Rp{{ number_format($tarif->tarif_per_kg, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="customer-empty">Belum ada data tarif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
