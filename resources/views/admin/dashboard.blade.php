@extends('layout.app')

@section('judul', 'Dashboard Admin')

@section('isi')
    <section class="dashboard-page">
        <div class="dashboard-heading">
            <div>
                <span class="dashboard-eyebrow">ADMIN PANEL</span>
                <h1>Dashboard Admin</h1>
                <p>Halo, {{ request()->user()->nama }}. Pilih fitur yang ingin kamu kelola.</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <a href="{{ route('pelanggan.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">PL</span>
                <span class="dashboard-card-content"><strong>Pelanggan</strong><small>Kelola data pelanggan.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('kurir.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">KR</span>
                <span class="dashboard-card-content"><strong>Kurir</strong><small>Kelola data dan akun kurir.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('paket.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">PK</span>
                <span class="dashboard-card-content"><strong>Paket</strong><small>Kelola data dan pengiriman paket.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('tarif.lihat') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">TR</span>
                <span class="dashboard-card-content"><strong>Tarif</strong><small>Lihat dan ubah tarif per zona.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('password.ganti') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">GP</span>
                <span class="dashboard-card-content"><strong>Ganti Password</strong><small>Ubah password akun Admin.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>
        </div>
    </section>
@endsection
