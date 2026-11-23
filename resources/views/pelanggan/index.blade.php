
@extends('layout.app')

@section('judul', 'Data Pelanggan')

@section('isi')
    <section class="customer-page">
        <div class="customer-heading">
            <div>
                <h1>Data Pelanggan</h1>
                <p>Kelola informasi pelanggan PaketIN.</p>
            </div>

            @if (request()->user()->role === 'admin')
                <button type="button" class="customer-add" data-open-customer-modal>
                    Tambah Pelanggan
                </button>
            @endif
        </div>

        <div data-pagination data-page-size="10">
            <div class="customer-table-wrap">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            @if (request()->user()->role === 'admin')
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody data-page-items>
                        @forelse ($daftar as $p)
                            <tr data-page-item>
                                <td class="customer-name">{{ $p->nama }}</td>
                                <td>{{ $p->telepon }}</td>
                                <td>{{ $p->alamat }}</td>
                                <td>{{ $p->kota }}</td>
                                @if (request()->user()->role === 'admin')
                                    <td>
                                        <button
                                            type="button"
                                            class="customer-edit-button"
                                            data-open-customer-modal
                                            data-customer-id="{{ $p->getKey() }}"
                                            data-update-url="{{ route('pelanggan.update', $p) }}"
                                            data-name="{{ $p->nama }}"
                                            data-phone="{{ $p->telepon }}"
                                            data-address="{{ $p->alamat }}"
                                            data-city="{{ $p->kota }}"
                                        >Ubah</button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ request()->user()->role === 'admin' ? 5 : 4 }}" class="customer-empty">
                                    Belum ada data pelanggan.
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
            id="customerModal"
            data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}"
            aria-hidden="true"
            hidden
        >
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="customerModalTitle">
                <div class="form-modal-header">
                    <div>
                        <h2 id="customerModalTitle">Tambah Pelanggan</h2>
                        <p id="customerModalDescription">Informasi pelanggan</p>
                    </div>
                    <button type="button" class="form-modal-close" data-close-customer-modal aria-label="Tutup">×</button>
                </div>

                <form
                    id="customerForm"
                    method="POST"
                    action="{{ route('pelanggan.store') }}"
                    data-store-url="{{ route('pelanggan.store') }}"
                >
                    @csrf
                    <input type="hidden" name="_method" id="customerMethod" value="PUT" disabled>
                    <input type="hidden" name="_customer_id" id="customerId" value="{{ old('_customer_id') }}">

                    <div class="form-modal-grid">
                        <div class="form-modal-field">
                            <label for="customerName">Nama</label>
                            <input type="text" id="customerName" name="nama" value="{{ old('nama') }}" placeholder="Nama pelanggan">
                            @error('nama')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-modal-field">
                            <label for="customerPhone">Telepon</label>
                            <input type="text" id="customerPhone" name="telepon" value="{{ old('telepon') }}" placeholder="Nomor telepon">
                            @error('telepon')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-modal-field">
                            <label for="customerAddress">Alamat</label>
                            <input type="text" id="customerAddress" name="alamat" value="{{ old('alamat') }}" placeholder="Alamat pelanggan">
                            @error('alamat')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-modal-field">
                            <label for="customerCity">Kota</label>
                            <input type="text" id="customerCity" name="kota" value="{{ old('kota') }}" placeholder="Kota">
                            @error('kota')
                                <small class="field-error">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-customer-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Simpan</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
@endsection
