
@extends('layout.app')

@section('judul', 'Laporan Pengiriman')

@section('isi')
    <section class="customer-page">
        <div class="customer-heading">
            <div>
                <h1>Laporan Pengiriman</h1>
                <p>Periode laporan: {{ $tanggalMulai }} sampai {{ $tanggalSelesai }}</p>
            </div>

            <div class="report-heading-actions">
                <a href="{{ route('manager.dashboard') }}" class="report-back-button">
                    Kembali
                </a>
                <button type="button" class="customer-add" data-open-report-modal>
                    Filter Periode
                </button>
            </div>
        </div>

        @if (session('sukses'))
            <div class="notice">{{ session('sukses') }}</div>
        @endif

        {{-- 1. LAPORAN PENGIRIMAN --}}
        <section class="report-section">
            <div class="report-section-heading">
                <h2>1. Laporan Pengiriman</h2>
                <p>Periode: {{ $tanggalMulai }} sampai {{ $tanggalSelesai }}</p>
            </div>

            <div data-pagination data-page-size="10">
                <div class="customer-table-wrap">
                    <table class="customer-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Jumlah Paket</th>
                            </tr>
                        </thead>

                        <tbody data-page-items>
                            @forelse ($laporanPengiriman as $item)
                                <tr data-page-item>
                                    <td>{{ $item['status'] }}</td>
                                    <td>{{ $item['jumlah'] }}</td>
                                </tr>
                            @empty
                                <tr data-page-item>
                                    <td colspan="2" class="customer-empty">
                                        Tidak ada data pengiriman pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Total Paket</th>
                                <th>{{ $laporanPengiriman->sum('jumlah') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="table-pagination" data-pagination-controls></div>
            </div>
        </section>

        {{-- 2. PENDAPATAN PER ZONA --}}
        <section class="report-section">
            <div class="report-section-heading">
                <h2>2. Pendapatan per Zona</h2>
                <p>Periode: {{ $tanggalMulai }} sampai {{ $tanggalSelesai }}</p>
            </div>

            <div data-pagination data-page-size="10">
                <div class="customer-table-wrap">
                    <table class="customer-table">
                        <thead>
                            <tr>
                                <th>Zona</th>
                                <th>Jumlah Paket</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>

                        <tbody data-page-items>
                            @forelse ($pendapatanPerZona as $item)
                                <tr data-page-item>
                                    <td>Zona {{ $item->zona }}</td>
                                    <td>{{ $item->jumlah_paket }}</td>
                                    <td>Rp{{ number_format($item->total_pendapatan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr data-page-item>
                                    <td colspan="3" class="customer-empty">
                                        Tidak ada paket pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th>{{ $pendapatanPerZona->sum('jumlah_paket') }}</th>
                                <th>Rp{{ number_format($pendapatanPerZona->sum('total_pendapatan'), 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="table-pagination" data-pagination-controls></div>
            </div>
        </section>

        {{-- 3. REKAP COD --}}
        <section class="report-section">
            <div class="report-section-heading">
                <h2>3. Rekap COD per Kurir</h2>
                <p>Seluruh paket COD berstatus Terkirim sampai hari ini, tanpa batas periode.</p>
            </div>

            <div data-pagination data-page-size="10">
                <div class="customer-table-wrap">
                    <table class="customer-table">
                        <thead>
                            <tr>
                                <th>Kode Kurir</th>
                                <th>Nama Kurir</th>
                                <th>COD Belum Disetor</th>
                                <th>COD Sudah Disetor</th>
                            </tr>
                        </thead>

                        <tbody data-page-items>
                            @forelse ($rekapCodPerKurir as $item)
                                <tr data-page-item>
                                    <td>{{ $item->kode }}</td>
                                    <td class="customer-name">{{ $item->nama }}</td>
                                    <td>Rp{{ number_format($item->cod_belum_disetor, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($item->cod_sudah_disetor, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr data-page-item>
                                    <td colspan="4" class="customer-empty">
                                        Belum ada data kurir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-pagination" data-pagination-controls></div>
            </div>
        </section>
    </section>

    {{-- MODAL FILTER PERIODE --}}
    <div
        class="form-modal-backdrop"
        id="reportFilterModal"
        data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
        aria-hidden="true"
        hidden
    >
        <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="reportModalTitle">
            <div class="form-modal-header">
                <div>
                    <h2 id="reportModalTitle">Filter Periode Laporan</h2>
                    <p>Pilih rentang tanggal laporan.</p>
                </div>

                <button type="button" class="form-modal-close" data-close-report-modal aria-label="Tutup">
                    ×
                </button>
            </div>

            <form method="GET" action="{{ route('manager.laporan.index') }}" id="reportFilterForm">
                <div class="form-modal-grid">
                    <div class="form-modal-field">
                        <label for="tanggalMulai">Tanggal Mulai</label>
                        <input
                            type="date"
                            id="tanggalMulai"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai', $tanggalMulai) }}"
                            required
                        >
                        @error('tanggal_mulai')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-modal-field">
                        <label for="tanggalSelesai">Tanggal Selesai</label>
                        <input
                            type="date"
                            id="tanggalSelesai"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai', $tanggalSelesai) }}"
                            required
                        >
                        @error('tanggal_selesai')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-modal-actions">
                    <button type="button" class="form-modal-cancel" data-close-report-modal>
                        Batal
                    </button>
                    <button type="submit" class="form-modal-submit">
                        Tampilkan Laporan
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
