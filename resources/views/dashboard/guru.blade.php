<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Sistem Informasi Absensi Kelas</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container py-4">

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h3 class="mb-1">Dashboard Guru</h3>
            <p class="text-muted mb-0">
                Selamat datang, {{ auth()->user()->name }}.
                Silakan pilih menu yang ingin digunakan.
            </p>
        </div>
    </div>

    <div class="row g-3">

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Rekap Absensi</h5>
                    <p class="text-muted">
                        Melihat data kehadiran siswa dan memantau status absensi.
                    </p>
                    <a href="{{ route('rekap.index') }}" class="btn btn-primary">
                        Buka Rekap Absensi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Catatan Ketidakhadiran</h5>
                    <p class="text-muted">
                        Laporkan siswa yang tidak ada di kelas saat pelajaran,
                        dan tindak lanjuti laporan kelas tanggung jawabmu.
                    </p>
                    <a href="{{ route('catatan.index') }}" class="btn btn-primary">
                        Buka Catatan
                    </a>
                    <a href="{{ route('catatan.create') }}" class="btn btn-outline-primary">
                        Buat Catatan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Informasi Dispensasi</h5>
                    <p class="text-muted">
                        Melihat informasi dispensasi yang dibuat oleh Guru Piket.
                    </p>
                    <a href="{{ route('dispensasi.guru') }}" class="btn btn-outline-primary">
                        Lihat Dispensasi
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
