<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Absensi Kelas</title>
    <link rel="icon" href="{{ asset('images/logo-smk.png') }}">
    <link href="{{ asset('css/smk-ui.css') }}" rel="stylesheet">
</head>
<body>
<div class="smk-login-page">
    <div class="smk-login-card">
        <img src="{{ asset('images/logo-smk.png') }}" alt="Logo SMK Informatika Sumedang">
        <h1>Absensi Kelas</h1>
        <p class="sub">SMK Informatika Sumedang</p>

        @if($errors->any())
            <div class="smk-alert smk-alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.store') }}" method="POST" class="smk-stack">
            @csrf
            <div>
                <label class="smk-label" for="username">NIS / NIP</label>
                <input class="smk-input" type="text" id="username" name="username"
                       value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>
            <div>
                <label class="smk-label" for="password">Kata sandi</label>
                <input class="smk-input" type="password" id="password" name="password"
                       required autocomplete="current-password">
            </div>
            <button type="submit" class="smk-btn smk-btn-block">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
