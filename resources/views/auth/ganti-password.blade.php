@extends('layout.app')

@section('judul', 'Ganti Password')

@section('isi')
<section class="password-page">
    <div class="password-card">
        <div class="password-heading">
            <div>
                <h1>Ganti Password</h1>
                <p>Perbarui password akun Anda untuk menjaga keamanan.</p>
            </div>
        </div>

        @if (session('sukses'))
            <div class="password-alert password-alert-success">{{ session('sukses') }}</div>
        @endif

        @if ($errors->any())
            <div class="password-alert password-alert-error">
                @foreach ($errors->all() as $e)
                    <p>{{ $e }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.ganti') }}">
            @csrf

            <div class="password-fields">
                <div class="password-field">
                    <label for="passwordLama">Password Lama</label>
                    <input type="password" id="passwordLama" name="password_lama" autocomplete="current-password" placeholder="Masukkan password lama">
                </div>

                <div class="password-field">
                    <label for="passwordBaru">Password Baru</label>
                    <input type="password" id="passwordBaru" name="password_baru" autocomplete="new-password" placeholder="Masukkan password baru">
                </div>

                <div class="password-field">
                    <label for="passwordKonfirmasi">Ulangi Password Baru</label>
                    <input type="password" id="passwordKonfirmasi" name="password_baru_confirmation" autocomplete="new-password" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="password-actions">
                <a href="{{ route('dashboard') }}" class="password-back">Kembali</a>
                <button type="submit" class="password-submit">Simpan Password</button>
            </div>
        </form>
    </div>
</section>
@endsection