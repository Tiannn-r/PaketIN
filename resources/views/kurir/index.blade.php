
@extends('layout.app')

@section('judul', 'Data Kurir')

@section('isi')
    <section class="customer-page">
        <div class="customer-heading">
            <div>
                <h1>Data Kurir</h1>
                <p>Kelola informasi kurir PaketIN.</p>
            </div>

            @if (request()->user()->role === 'admin')
                <button type="button" class="customer-add" data-open-courier-modal>
                    Tambah Kurir
                </button>
            @endif
        </div>

        @if (session('akun_baru'))
            <div class="notice">
                <p>Akun kurir siap dipakai. Catat sekarang, password ini tidak ditampilkan lagi.</p>
                <p>Kode kurir: <strong>{{ session('akun_baru')['kode'] }}</strong></p>
                <p>Password sementara: <strong>{{ session('akun_baru')['password'] }}</strong></p>
            </div>
        @endif

        <div data-pagination data-page-size="10">
            <div class="customer-table-wrap">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Paket Dikirim</th>
                            @if (request()->user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody data-page-items>
                        @forelse ($daftarKurir as $kurir)
                            <tr data-page-item>
                                <td>{{ $kurir->kode }}</td>
                                <td class="customer-name">{{ $kurir->nama }}</td>
                                <td>{{ $kurir->telepon }}</td>
                                <td>{{ $kurir->status ? 'Aktif' : 'Nonaktif' }}</td>
                                <td>{{ $kurir->jumlah_paket_dikirim }}</td>

                                @if (request()->user()->role === 'admin')
                                    <td>
                                        <div class="courier-actions">
                                            <button type="button" class="customer-edit-button" data-open-courier-modal data-kurir-id="{{ $kurir->getKey() }}" data-update-url="{{ route('kurir.update', $kurir) }}" data-name="{{ $kurir->nama }}" data-phone="{{ $kurir->telepon }}">Ubah</button>

                                            @if ($kurir->status)
                                                <form method="POST" action="{{ route('kurir.nonaktifkan', $kurir) }}">
                                                    @csrf
                                                    <button type="submit" class="customer-edit-button">Nonaktifkan</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('kurir.aktifkan', $kurir) }}">
                                                    @csrf
                                                    <button type="submit" class="customer-edit-button">Aktifkan</button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('kurir.reset', $kurir) }}" onsubmit="return confirm('Reset password kurir ini?')">
                                                @csrf
                                                <button type="submit" class="customer-edit-button">Reset Password</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ request()->user()->role === 'admin' ? 6 : 5 }}" class="customer-empty">
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

    @if (request()->user()->role === 'admin')
        <div
            class="form-modal-backdrop"
            id="courierModal"
            data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
            aria-hidden="true"
            hidden
        >
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="courierModalTitle">
                <div class="form-modal-header">
                    <div>
                        <h2 id="courierModalTitle">Tambah Kurir</h2>
                        <p id="courierModalDescription">Informasi kurir</p>
                    </div>
                    <button type="button" class="form-modal-close" data-close-courier-modal aria-label="Tutup">×</button>
                </div>

                <form
                    id="courierForm"
                    method="POST"
                    action="{{ route('kurir.store') }}"
                    data-store-url="{{ route('kurir.store') }}"
                >
                    @csrf
                    <input type="hidden" name="_method" id="courierMethod" value="PUT" disabled>
                    <input type="hidden" name="_courier_id" id="courierId" value="{{ old('_courier_id') }}">

                    <div class="form-modal-grid">
                        <div class="form-modal-field">
                            <label for="courierName">Nama</label>
                            <input type="text" id="courierName" name="nama" value="{{ old('nama') }}" placeholder="Nama kurir">
                            @error('nama')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-modal-field">
                            <label for="courierPhone">Telepon</label>
                            <input type="text" id="courierPhone" name="telepon" value="{{ old('telepon') }}" placeholder="Nomor telepon">
                            @error('telepon')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <p id="courierModalNote" class="courier-modal-note">
                        Kode kurir dan password sementara dibuat otomatis setelah disimpan.
                    </p>

                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-courier-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Simpan</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
@endsection
