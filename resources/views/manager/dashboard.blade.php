@extends('layout.app')

@section('judul', 'Dashboard Manager')

@section('isi')
    <section class="dashboard-page">
        <div class="dashboard-heading">
            <div>
                <span class="dashboard-eyebrow">MANAGER PANEL</span>
                <h1>Dashboard Manager</h1>
                <p>Halo, {{ request()->user()->nama }}. Pilih fitur yang ingin kamu kelola.</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <a href="{{ route('pelanggan.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">PL</span>
                <span class="dashboard-card-content"><strong>Pelanggan</strong><small>Lihat data pelanggan.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('kurir.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">KR</span>
                <span class="dashboard-card-content"><strong>Kurir</strong><small>Lihat data kurir.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('manager.tarif.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">TR</span>
                <span class="dashboard-card-content"><strong>Tarif</strong><small>Kelola tarif pengiriman per zona.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('manager.laporan.index') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">LP</span>
                <span class="dashboard-card-content"><strong>Laporan</strong><small>Lihat laporan pengiriman dan COD.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>

            <a href="{{ route('password.ganti') }}" class="dashboard-card">
                <span class="dashboard-card-icon" aria-hidden="true">GP</span>
                <span class="dashboard-card-content"><strong>Ganti Password</strong><small>Ubah password akun Manager.</small></span>
                <span class="dashboard-card-arrow" aria-hidden="true">↗</span>
            </a>
        </div>
    </section>
@endsection
