@extends('layout.app')
@section('judul', 'Detail Paket')
@section('isi')

    <section class="customer-page package-detail-page">
        {{-- HEADER --}}
        <div class="customer-heading">
            <div>
                <h1>Detail Paket</h1>
                <p>Nomor Resi: <strong>{{ $paket->no_resi }}</strong></p>
            </div>
            @if ($paket->status === 'Diterima')
                <button type="button" class="package-action-button is-primary" data-open-package-modal="transitModal">Proses ke Transit</button>
            @elseif ($paket->status === 'Transit' || ($paket->status === 'Gagal Kirim' && $paket->jumlah_gagal < 2))
                <button type="button" class="package-action-button is-primary" data-open-package-modal="tugaskanModal">Pilih Kurir</button>
            @elseif ($paket->status === 'Gagal Kirim' && $paket->jumlah_gagal >= 2)
                <button type="button" class="package-action-button is-primary" data-open-package-modal="returModal">Ubah ke Retur</button>
            @endif
        </div>

        {{-- INFORMASI PENGIRIMAN --}}
        <section class="package-detail-card">
            <div class="package-detail-heading">
                <h2>Informasi Pengiriman</h2>
                <span class="package-status">{{ $paket->status }}</span>
                @if ($paket->perlu_verifikasi)<span class="badge-verifikasi">Perlu verifikasi (berat/zona belum lengkap)</span>@endif
            </div>
            <div class="package-detail-grid">
                <div class="package-detail-item"><span>Nomor Resi</span><strong>{{ $paket->no_resi }}</strong></div>
                <div class="package-detail-item"><span>Pengirim</span><strong>{{ $paket->pelanggan->nama }}</strong></div>
                <div class="package-detail-item"><span>Penerima</span><strong>{{ $paket->nama_penerima }}</strong></div>
                <div class="package-detail-item"><span>Telepon Penerima</span><strong>{{ $paket->telepon_penerima }}</strong></div>
                <div class="package-detail-item"><span>Alamat Penerima</span><strong>{{ $paket->alamat_penerima }}</strong></div>
                <div class="package-detail-item"><span>Kota Tujuan</span><strong>{{ $paket->kota_tujuan }}</strong></div>
                <div class="package-detail-item"><span>Zona</span><strong>{{ $paket->zona ?? '-' }} @if ($paket->status_zona)(Manual)@endif</strong></div>
                <div class="package-detail-item"><span>Layanan</span><strong>{{ $paket->layanan }}</strong></div>
                <div class="package-detail-item"><span>Berat Asli</span><strong>{{ $paket->berat_asli !== null ? $paket->berat_asli . ' kg' : '-' }}</strong></div>
                <div class="package-detail-item"><span>Berat Dibulatkan</span><strong>{{ $paket->berat_bulat !== null ? $paket->berat_bulat . ' kg' : '-' }}</strong></div>
                <div class="package-detail-item"><span>Kurir</span><strong>{{ $paket->kurir ? $paket->kurir->kode . ' - ' . $paket->kurir->nama : '-' }}</strong></div>
                <div class="package-detail-item"><span>Jumlah Gagal Kirim</span><strong>{{ $paket->jumlah_gagal }}</strong></div>
            </div>
        </section>

        {{-- RINCIAN BIAYA --}}
        <section class="package-detail-card package-cost-card">
            <div class="package-detail-heading"><h2>Rincian Biaya</h2></div>
            <div class="package-detail-grid">
                <div class="package-detail-item"><span>Ongkir</span><strong>{{ $paket->ongkir !== null ? 'Rp' . number_format($paket->ongkir, 0, ',', '.') : 'Belum dihitung' }}</strong></div>
                @if ($paket->status_pembayaran)
                    <div class="package-detail-item"><span>Biaya COD</span><strong>Rp{{ number_format($paket->biaya_cod, 0, ',', '.') }}</strong></div>
                @endif
                <div class="package-detail-item package-detail-total"><span>Dibayar Pengirim di Loket</span><strong>Rp{{ number_format(($paket->ongkir ?? 0) + $paket->biaya_cod, 0, ',', '.') }}</strong></div>
                @if ($paket->status_pembayaran)
                    <div class="package-detail-item"><span>Nilai COD dari Penerima</span><strong>Rp{{ number_format($paket->nilai_cod, 0, ',', '.') }}</strong></div>
                @endif
            </div>
        </section>

        {{-- SETORAN COD --}}
        @if ($paket->status_pembayaran)
            <section class="package-detail-card package-cod-card">
                <div class="package-detail-heading">
                    <h2>Setoran COD</h2>
                    <span class="package-status">{{ $paket->setoran ? 'Sudah Disetor' : 'Belum Disetor' }}</span>
                </div>
                @if ($paket->setoran)
                    <div class="package-detail-grid">
                        <div class="package-detail-item"><span>Jumlah Setoran</span><strong>Rp{{ number_format($paket->setoran->jumlah, 0, ',', '.') }}</strong></div>
                        <div class="package-detail-item"><span>Waktu Setoran</span><strong>{{ $paket->setoran->disetor_pada->format('d-m-Y H:i') }}</strong></div>
                    </div>
                @else
                    <div class="package-detail-grid">
                        <div class="package-detail-item"><span>Jumlah yang Harus Disetor</span><strong>Rp{{ number_format($paket->nilai_cod, 0, ',', '.') }}</strong></div>
                    </div>
                    @if ($paket->status === 'Terkirim')
                        <div class="package-detail-actions">
                            <button type="button" class="package-action-button is-primary" data-open-package-modal="setoranModal">Catat Setoran COD</button>
                        </div>
                    @else
                        <p class="package-detail-note">Setoran hanya dapat dicatat setelah paket berstatus Terkirim.</p>
                    @endif
                @endif
            </section>
        @endif

        {{-- RIWAYAT STATUS --}}
        <section class="package-detail-card package-history-card">
            <div class="package-detail-heading"><h2>Riwayat Status</h2></div>
            <div data-pagination data-page-size="10">
                <div class="customer-table-wrap">
                    <table class="customer-table package-history-table">
                        <thead>
                            <tr><th>Waktu</th><th>Dari</th><th>Ke</th><th>Pelaku</th><th>Catatan</th></tr>
                        </thead>
                        <tbody data-page-items>
                            @forelse ($paket->daftarRiwayat as $riwayat)
                                <tr data-page-item>
                                    <td>{{ $riwayat->dibuat_pada?->format('d-m-Y H:i') ?? '-' }}</td>
                                    <td>{{ $riwayat->status_dari ?? '-' }}</td>
                                    <td>{{ $riwayat->status_ke }}</td>
                                    <td>{{ $riwayat->tipe_pelaku }}: {{ $riwayat->nama_pelaku }}</td>
                                    <td>{{ $riwayat->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="customer-empty">Belum ada riwayat status.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-pagination" data-pagination-controls></div>
            </div>
        </section>

        {{-- TOMBOL BAGIAN BAWAH --}}
        <div class="package-footer-links">
            <a href="{{ route('paket.index') }}" class="package-action-button">Daftar Paket</a>
        </div>
    </section>

    {{-- MODAL SETORAN COD --}}
    @if ($paket->status_pembayaran && !$paket->setoran && $paket->status === 'Terkirim')
        <div class="form-modal-backdrop" id="setoranModal" data-package-action-modal data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}" aria-hidden="true" hidden>
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="setoranModalTitle">
                <div class="form-modal-header">
                    <div><h2 id="setoranModalTitle">Catat Setoran COD</h2><p>Konfirmasi setoran untuk paket ini.</p></div>
                </div>
                <form method="POST" action="{{ route('paket.setoran', $paket) }}">
                    @csrf
                    <p class="package-modal-note">Jumlah setoran: <strong>Rp{{ number_format($paket->nilai_cod, 0, ',', '.') }}</strong></p>
                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-package-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Catat Setoran</button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    {{-- MODAL PROSES TRANSIT --}}
    @if ($paket->status === 'Diterima')
        <div class="form-modal-backdrop" id="transitModal" data-package-action-modal data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}" aria-hidden="true" hidden>
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="transitModalTitle">
                <div class="form-modal-header">
                    <div><h2 id="transitModalTitle">Proses ke Transit</h2><p>Proses paket ke tahap Transit.</p></div>
                </div>
                <form method="POST" action="{{ route('paket.transit', $paket) }}">
                    @csrf
                    @error('paket')
                        <p class="package-form-error">{{ $message }}</p>
                    @enderror
                    <div class="form-modal-field">
                        <label for="catatanTransit">Catatan (opsional)</label>
                        <input type="text" id="catatanTransit" name="catatan" value="{{ old('catatan') }}" placeholder="Masukkan catatan">
                    </div>
                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-package-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Proses Transit</button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    {{-- MODAL PENUGASAN KURIR --}}
    @if ($paket->status === 'Transit' || ($paket->status === 'Gagal Kirim' && $paket->jumlah_gagal < 2))
        <div class="form-modal-backdrop" id="tugaskanModal" data-package-action-modal data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}" aria-hidden="true" hidden>
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="tugaskanModalTitle">
                <div class="form-modal-header">
                    <div><h2 id="tugaskanModalTitle">Tugaskan Kurir</h2><p>Pilih kurir untuk mengantarkan paket.</p></div>
                </div>
                <form method="POST" action="{{ route('paket.tugaskan', $paket) }}">
                    @csrf
                    @error('paket')
                        <p class="package-form-error">{{ $message }}</p>
                    @enderror
                    <div class="form-modal-field">
                        <label for="idKurir">Pilih Kurir</label>
                        <select name="id_kurir" id="idKurir" required>
                            <option value="">Pilih kurir</option>
                            @foreach ($daftarKurirAktif as $kurir)
                                <option value="{{ $kurir->id }}" @selected(old('id_kurir') == $kurir->id)>{{ $kurir->kode }} - {{ $kurir->nama }} ({{ $kurir->jumlah_paket_dikirim }} paket Dikirim)</option>
                            @endforeach
                        </select>
                        @error('id_kurir')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-modal-field package-modal-spaced-field">
                        <label for="catatanTugaskan">Catatan (opsional)</label>
                        <input type="text" id="catatanTugaskan" name="catatan" value="{{ old('catatan') }}" placeholder="Masukkan catatan">
                        @error('catatan')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-package-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Tugaskan Kurir</button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    {{-- MODAL RETUR --}}
    @if ($paket->status === 'Gagal Kirim' && $paket->jumlah_gagal >= 2)
        <div class="form-modal-backdrop" id="returModal" data-package-action-modal data-open-on-load="{{ $errors->any() ? 'true' : 'false' }}" aria-hidden="true" hidden>
            <section class="form-modal-card" role="dialog" aria-modal="true" aria-labelledby="returModalTitle">
                <div class="form-modal-header">
                    <div><h2 id="returModalTitle">Ubah ke Retur</h2><p>Paket sudah gagal dikirim sebanyak dua kali.</p></div>
                </div>
                <form method="POST" action="{{ route('paket.retur', $paket) }}">
                    @csrf
                    @error('paket')
                        <p class="package-form-error">{{ $message }}</p>
                    @enderror
                    <div class="form-modal-field">
                        <label for="catatanRetur">Catatan (opsional)</label>
                        <input type="text" id="catatanRetur" name="catatan" value="{{ old('catatan') }}" placeholder="Masukkan catatan">
                    </div>
                    <div class="form-modal-actions">
                        <button type="button" class="form-modal-cancel" data-close-package-modal>Batal</button>
                        <button type="submit" class="form-modal-submit">Ubah ke Retur</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
    @endsection
