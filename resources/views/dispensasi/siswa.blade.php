<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Dispensasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; }
        .card { border: 0; border-radius: 15px; }
        .navbar { background: #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand fw-bold">Informasi Dispensasi</span>
        <a href="{{ route('dashboard.siswa') }}" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container py-4">
    <h3 class="fw-bold mb-3">Dispensasi Sekolah</h3>
    <p class="text-secondary">Informasi dispensasi untuk kamu dan kelasmu.</p>

    @forelse ($dispensasi as $item)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="fw-bold">{{ $item->judul }}</h5>
                <p class="mb-2">{{ $item->keterangan }}</p>
                <div class="small text-secondary">
                    <div>Tanggal: {{ $item->tanggal->format('d-m-Y') }}</div>
                    <div>Waktu: {{ $item->waktu_mulai ?? '--:--' }} – {{ $item->waktu_selesai ?? '--:--' }}</div>
                    <div>Tujuan:
                        @if ($item->siswa_id)
                            Siswa tertentu
                        @elseif ($item->kelas_id)
                            {{ $item->kelas->nama_kelas ?? 'Kelas' }}
                        @else
                            Seluruh sekolah
                        @endif
                    </div>
                    <div>Dibuat oleh: {{ $item->guruPiket->name ?? 'Guru piket' }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada informasi dispensasi untukmu.</div>
    @endforelse
</div>

</body>
</html>