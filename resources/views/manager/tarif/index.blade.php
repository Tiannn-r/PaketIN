
@extends('layout.app')

@section('judul', 'Pengaturan Tarif')

@section('isi')
    <section class="customer-page">
        <div class="customer-heading">
            <div>
                <h1>Pengaturan Tarif</h1>
                <p>Kelola tarif pengiriman berdasarkan zona tujuan.</p>
            </div>

            <a href="{{ route('manager.dashboard') }}" class="customer-add">
                Kembali
            </a>
        </div>

        @if (session('sukses'))
            <div class="notice">{{ session('sukses') }}</div>
        @endif

        <div data-pagination data-page-size="10">
            <div class="customer-table-wrap">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>Zona</th>
                            <th>Nama Zona</th>
                            <th>Tarif per Kg</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody data-page-items>
                        @forelse ($daftarTarif as $tarif)
                            <tr data-page-item>
                                <td>{{ $tarif->zona }}</td>
                                <td class="customer-name">{{ $tarif->nama_zona }}</td>
                                <td>Rp{{ number_format($tarif->tarif_per_kg, 0, ',', '.') }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="customer-edit-button"
                                        data-open-tarif-modal
                                        data-tarif-zona="{{ $tarif->zona }}"
                                        data-nama-zona="{{ $tarif->nama_zona }}"
                                        data-tarif="{{ $tarif->tarif_per_kg }}"
                                        data-update-url="{{ route('manager.tarif.update', $tarif->zona) }}"
                                    >Ubah Tarif</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="customer-empty">
                                    Belum ada data tarif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination" data-pagination-controls></div>
        </div>
    </section>

    <div
        class="form-modal-backdrop"
        id="tarifModal"
        data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
        aria-hidden="true"
        hidden
    >
        <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="tarifModalTitle">
            <div class="form-modal-header">
                <div>
                    <h2 id="tarifModalTitle">Ubah Tarif</h2>
                    <p id="tarifModalDescription">Perbarui tarif pengiriman zona.</p>
                </div>
                <button type="button" class="form-modal-close" data-close-tarif-modal aria-label="Tutup">×</button>
            </div>

            <form id="tarifForm" method="POST" action="#">
                @csrf
                @method('PUT')
                <input type="hidden" name="_tarif_zona" id="tarifZona" value="{{ old('_tarif_zona') }}">

                <div class="form-modal-field">
                    <label for="tarifPerKg">Tarif per Kg (Rp)</label>
                    <input
                        type="number"
                        id="tarifPerKg"
                        name="tarif_per_kg"
                        value="{{ old('tarif_per_kg') }}"
                        min="1"
                        step="1"
                        required
                        placeholder="Masukkan tarif"
                    >
                    @error('tarif_per_kg')
                        <small class="field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-modal-actions">
                    <button type="button" class="form-modal-cancel" data-close-tarif-modal>Batal</button>
                    <button type="submit" class="form-modal-submit">Simpan Tarif</button>
                </div>
            </form>
        </section>
    </div>
@endsection
