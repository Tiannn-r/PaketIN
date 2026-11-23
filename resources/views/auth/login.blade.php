
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PaketIN</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-intro">
            <div>
                <h1>PaketIN<span>.</span></h1>
                <p>Sistem operasional ekspedisi .</p>
            </div>

            <div class="login-intro-footer">
                <span>INTERNAL ACCESS</span>
            </div>
        </section>

        <section class="login-content">
            <div class="login-form-wrap">
                <h2>Masuk</h2>
                <p class="login-caption">Masukkan username dan password akun Anda.</p>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="login-field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username"
                        >
                        @error('username')
                            <p class="login-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="login-field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password"
                        >
                        @error('password')
                            <p class="login-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="login-button">Masuk</button>
                </form>
            </div>
        </section>
    </main>
</bod >
</html>