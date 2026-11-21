<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Dashboard Manager</title></head>
<body>
    <h1>Dashboard Manager</h1>
    <p>Halo, {{ auth()->user()->nama }}</p>
    <a href="{{ route('password.ganti') }}">Ganti password</a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>