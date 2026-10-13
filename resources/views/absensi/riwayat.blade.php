<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Riwayat Absensi</span>

        <a href="{{ route('dashboard.siswa') }}"
           class="btn btn-light btn-sm">
            Kembali
        </a>
    </div>
</nav>

<div class="container py-4">

    <h3 class="fw-bold mb-1">Riwayat Kehadiran</h3>

    <p class="text-secondary">
        {{ auth()->user()->name }}
    </p>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            @if ($riwayat->isEmpty())
                <div class="alert alert-info mb-0">
                    Belum ada riwayat absensi.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Absen</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($riwayat as $absen)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($absen->tanggal)->format('d-m-Y') }}
                                    </td>

                                    <td>{{ $absen->waktu_absen }}</td>

                                    <td>
                                        @if ($absen->status === 'Hadir')
                                            <span class="badge bg-success">
                                                Hadir
                                            </span>
                                        @elseif ($absen->status === 'Terlambat')
                                            <span class="badge bg-warning text-dark">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ $absen->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $riwayat->links() }}
            @endif

        </div>
    </div>

</div>

</body>
</html>