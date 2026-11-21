<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PaketIN</title>
</head>
<body>
    <h1>PaketIN</h1>
    <form action="{{ route('login') }}" method="post">
        @csrf
        <div>
            <label for="">Username</label>
            <input type="text" name="username" value="{{ old('username') }}">
        </div>
        <div>
            <label for="">Password</label>
            <input type="password" name="password">
        </div>
        @error('username')
            <p style="color:red"> {{ $message }} </p>
        @enderror
        @error('password')
             <p style="color:red"> {{ $message }} </p>
        @enderror
        <button type="submit">Masuk</button>
    </form>
</body>
</html>