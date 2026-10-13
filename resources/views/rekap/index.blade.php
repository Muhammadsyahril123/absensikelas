<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Absensi Kelas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f3f6fb;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #0d6efd;
        }

        .page-title {
            font-weight: 700;
            color: #172b4d;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .table thead th {
            background: #eaf2ff;
            white-space: nowrap;
        }

        .badge {
            font-size: 12px;
            padding: 7px 10px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard.guru') }}">
            Sistem Absensi Kelas
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-light btn-sm" type="submit">
                Keluar
            </button>
        </form>
    </div>
</nav>

<div class="container py-4">

    <div class="mb-4">
        <h3 class="page-title">Rekap Absensi Kelas</h3>
        <p class="text-secondary mb-0">
            Pantau kehadiran siswa berdasarkan kelas dan tanggal.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Filter kelas dan tanggal --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('rekap.index') }}" method="GET">
                <div class="row g-3 align-items-end">

                    <div class="col-md-5">
                        <label for="kelas_id" class="form-label">
                            Pilih Kelas
                        </label>

                        <select
                            name="kelas_id"
                            id="kelas_id"
                            class="form-select"
                            required
                        >
                            @foreach ($daftarKelas as $kelas)
                                <option
                                    value="{{ $kelas->id }}"
                                    @selected(
                                        $kelasTerpilih &&
                                        $kelasTerpilih->id === $kelas->id
                                    )
                                >
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="tanggal" class="form-label">
                            Tanggal Absensi
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            id="tanggal"
                            class="form-control"
                            value="{{ $tanggal }}"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            @disabled($daftarKelas->isEmpty())
                        >
                            Tampilkan Rekap
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @if ($kelasTerpilih)

        <div class="mb-3">
            <h5 class="fw-bold">
                {{ $kelasTerpilih->nama_kelas }}
            </h5>

            <p class="text-secondary">
                Tanggal:
                {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
            </p>
        </div>

        {{-- Statistik --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-secondary">Total Siswa</div>
                        <div class="stat-number text-primary">
                            {{ $jumlahSiswa }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-secondary">Hadir</div>
                        <div class="stat-number text-success">
                            {{ $jumlahHadir }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-secondary">Terlambat</div>
                        <div class="stat-number text-warning">
                            {{ $jumlahTerlambat }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-secondary">Belum Absen</div>
                        <div class="stat-number text-danger">
                            {{ $jumlahBelumAbsen }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Tabel siswa --}}
        <div class="card">
            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Daftar Kehadiran Siswa
                </h5>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Waktu Absen</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($siswaRekap as $index => $siswa)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $siswa['nis'] }}</td>
                                    <td>{{ $siswa['nama'] }}</td>

                                    <td>
                                        {{ $siswa['waktu_absen'] ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($siswa['status'] === 'Hadir')
                                            <span class="badge bg-success">
                                                Hadir
                                            </span>
                                        @elseif ($siswa['status'] === 'Terlambat')
                                            <span class="badge bg-warning text-dark">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Belum Absen
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        Belum ada siswa di kelas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <small class="text-secondary">
                    Status Belum Absen berarti sistem belum menemukan
                    catatan absensi pada tanggal yang dipilih.
                    Status ini bukan penetapan Alpa.
                </small>

            </div>
        </div>

    @elseif ($daftarKelas->isEmpty())

        <div class="alert alert-info">
            Akun guru ini belum terhubung dengan kelas mana pun.
        </div>

    @endif

</div>

</body>
</html>