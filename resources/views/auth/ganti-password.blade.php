<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Ganti Password</title></head>
<body>
    <h1>Ganti Password</h1>
    @if (session('sukses')) <p style="color:green">{{ session('sukses') }}</p> @endif
    <form method="POST" action="{{ route('password.ganti') }}">
        @csrf
        <div><label>Password lama</label><br><input type="password" name="password_lama"></div>
        <div><label>Password baru</label><br><input type="password" name="password_baru"></div>
        <div><label>Ulangi password baru</label><br><input type="password" name="password_baru_confirmation"></div>
        @foreach ($errors->all() as $e) <p style="color:red">{{ $e }}</p> @endforeach
        <button type="submit">Simpan</button>
    </form>
    <a href="{{ route('dashboard') }}">Kembali</a>
</body>
</html>