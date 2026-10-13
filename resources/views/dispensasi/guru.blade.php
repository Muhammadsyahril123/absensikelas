<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemantauan Dispensasi</title>
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
        <span class="navbar-brand fw-bold">Pemantauan Dispensasi</span>
        <a href="{{ route('dashboard.guru') }}" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container py-4">
    <h3 class="fw-bold mb-3">Informasi Dispensasi</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Judul</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Tujuan</th>
                            <th>Guru Piket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dispensasi as $item)
                            <tr>
                                <td>{{ $item->judul }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td>{{ $item->tanggal->format('d-m-Y') }}</td>
                                <td>
                                    @if ($item->siswa_id)
                                        {{ $item->siswa->nama ?? 'Siswa' }}
                                    @elseif ($item->kelas_id)
                                        {{ $item->kelas->nama_kelas ?? 'Kelas' }}
                                    @else
                                        Seluruh Sekolah
                                    @endif
                                </td>
                                <td>{{ $item->guruPiket->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada dispensasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>