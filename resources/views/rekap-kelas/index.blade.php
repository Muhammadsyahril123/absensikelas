<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rekap Kehadiran Kelas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f7fb;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #0d6efd;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .judul {
            color: #17365d;
            font-weight: 700;
        }

        .table th {
            background: #eaf2ff;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85rem;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <span class="navbar-brand fw-bold">
            Rekap Kehadiran Kelas
        </span>

        <a href="{{ route('dashboard.siswa') }}"
           class="btn btn-light btn-sm">
            Kembali
        </a>
    </div>
</nav>

<div class="container py-4">

    {{-- INFORMASI KELAS --}}
    <div class="mb-4">
        <h3 class="judul">Rekap Kehadiran Siswa</h3>

        <p class="text-secondary mb-1">
            Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}
        </p>

        <p class="text-secondary">
            Lihat status kehadiran teman sekelas berdasarkan tanggal.
        </p>
    </div>

    {{-- PESAN VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PILIH TANGGAL --}}
    <div class="card mb-4">
        <div class="card-body">

            <form
                method="GET"
                action="{{ route('rekap-kelas.index') }}"
                class="row g-3 align-items-end"
            >

                <div class="col-md-5">
                    <label for="tanggal" class="form-label fw-semibold">
                        Pilih Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="form-control"
                        value="{{ $tanggal }}"
                        max="{{ now('Asia/Jakarta')->toDateString() }}"
                        required
                    >
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        Lihat Rekap
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- HITUNG JUMLAH STATUS --}}
    @php
        $jumlahHadir = 0;
        $jumlahTerlambat = 0;
        $jumlahBelumAbsen = 0;

        foreach ($daftarSiswa as $teman) {
            $absen = $dataAbsensi->get($teman->id);

            if (!$absen) {
                $jumlahBelumAbsen++;
                continue;
            }

            $status = strtolower(trim($absen->status));

            if ($status === 'hadir') {
                $jumlahHadir++;
            } elseif ($status === 'terlambat') {
                $jumlahTerlambat++;
            }
        }
    @endphp

    {{-- KARTU RINGKASAN --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Hadir</div>

                    <h3 class="text-success mb-0">
                        {{ $jumlahHadir }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Terlambat</div>

                    <h3 class="text-warning mb-0">
                        {{ $jumlahTerlambat }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary small">Belum Absen</div>

                    <h3 class="text-secondary mb-0">
                        {{ $jumlahBelumAbsen }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- TABEL KEHADIRAN --}}
    <div class="card">
        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Daftar Kehadiran
            </h5>

            <p class="text-secondary small">
                Tanggal:
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </p>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Waktu Absen</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($daftarSiswa as $teman)

                            @php
                                $absen = $dataAbsensi->get($teman->id);

                                $status = $absen
                                    ? strtolower(trim($absen->status))
                                    : null;
                            @endphp

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $teman->nama }}
                                </td>

                                <td>
                                    {{ $teman->nis ?? '-' }}
                                </td>

                                <td>
                                    @if ($absen && $absen->waktu_absen)
                                        {{ $absen->waktu_absen }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>

                                    @if (!$absen)

                                        <span class="badge bg-secondary">
                                            Belum Absen
                                        </span>

                                    @elseif ($status === 'hadir')

                                        <span class="badge bg-success">
                                            Hadir
                                        </span>

                                    @elseif ($status === 'terlambat')

                                        <span class="badge bg-warning text-dark">
                                            Terlambat
                                        </span>

                                    @else

                                        <span class="badge bg-info text-dark">
                                            {{ ucfirst($absen->status) }}
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

            <div class="small text-secondary">
                Rekap ini hanya menampilkan siswa yang berada di kelas
                yang sama dengan akun yang sedang login.

                <br>

                <strong>Catatan:</strong>
                Belum Absen berarti belum ada catatan absensi untuk
                tanggal yang dipilih, bukan berarti siswa pasti tidak masuk.
            </div>

        </div>
    </div>

</div>

</body>
</html>