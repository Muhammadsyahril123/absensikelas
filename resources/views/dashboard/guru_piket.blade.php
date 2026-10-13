<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru Piket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Absensi Kelas</span>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container py-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-1">Dashboard Guru Piket</h3>
            <p class="text-secondary mb-0">
                Selamat datang, {{ auth()->user()->name }}!
            </p>
        </div>
    </div>

    <div class="row g-3">

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Kelola Dispensasi</h5>
                    <p class="text-muted">Buat dan kelola data dispensasi siswa/kelas.</p>
                    <a href="{{ route('dispensasi.piket') }}" class="btn btn-primary">
                        Buka Dispensasi
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Pemeriksaan Pengajuan</h5>
                    <p class="text-muted">Setujui atau tolak izin/sakit dan alasan keterlambatan.</p>
                    <a href="{{ route('guru-piket.pemeriksaan') }}" class="btn btn-primary">
                        Periksa Pengajuan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5>Catatan Ketidakhadiran</h5>
                    <p class="text-muted">
                        Lihat laporan ketidakhadiran saat pelajaran dan ambil alih tindak lanjut bila diperlukan.
                    </p>
                    <a href="{{ route('guru-piket.catatan') }}" class="btn btn-outline-primary">
                        Lihat Catatan
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
