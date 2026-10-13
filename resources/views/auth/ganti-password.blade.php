<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ganti Password</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="card border-0 shadow-sm rounded-4 w-100"
         style="max-width: 450px;">

        <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
                <h3 class="fw-bold">Ganti Password</h3>

                <p class="text-secondary mb-0">
                    Buat password pribadi untuk mengamankan akunmu.
                </p>
            </div>

            <div class="alert alert-info">
                Kamu perlu mengganti password sebelum melanjutkan ke dashboard.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Password Baru
                    </label>

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           minlength="8"
                           autocomplete="new-password"
                           required>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password Baru
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control"
                           minlength="8"
                           autocomplete="new-password"
                           required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Simpan Password
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>