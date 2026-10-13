<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Absensi Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eaf5ff, #c9e4ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0, 70, 150, 0.12);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: auto;
            border-radius: 18px;
            background: #0d6efd;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
        }

        .btn-login {
            background: #0d6efd;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn-login:hover {
            background: #0b5ed7;
            color: white;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="text-center mb-4">
        <div class="logo mb-3">A</div>

        <h4 class="fw-bold">Absensi Kelas</h4>

        <p class="text-secondary small">
            SMK Informatika Sumedang
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Username
            </label>

            <input
                type="text"
                name="username"
                class="form-control form-control-lg"
                placeholder="Masukkan NIS atau NIP"
                value="{{ old('username') }}"
                required
                autofocus
            >
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control form-control-lg"
                placeholder="Masukkan password"
                required
            >
        </div>

        <button type="submit" class="btn btn-login">
            Masuk
        </button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">
        Sistem Informasi Absensi Kelas
    </p>

</div>

</body>
</html>