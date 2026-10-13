<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pengajuan Keterlambatan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .card {
            border-radius: 16px;
        }

        .alasan {
            min-width: 180px;
            max-width: 300px;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body class="bg-light">

{{-- Navbar --}}
<nav class="navbar navbar-dark bg-primary">
    <div class="container">

        <a href="{{ route('dashboard') }}"
           class="navbar-brand fw-bold">
            Absensi Kelas
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="btn btn-light btn-sm">
                Logout
            </button>
        </form>

    </div>
</nav>

{{-- Konten --}}
<div class="container py-4">

    <div class="card border-0 shadow-sm mx-auto"
         style="max-width: 900px;">

        <div class="card-body p-4">

            <h3 class="fw-bold mb-1">
                Pengajuan Keterlambatan
            </h3>

            <p class="text-secondary mb-4">
                Gunakan formulir ini untuk memberikan alasan keterlambatan.
            </p>

            {{-- Notifikasi berhasil --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Peringatan wajib mengisi alasan --}}
            @if (session('wajib_alasan'))
                <div class="alert alert-warning">
                    <strong>Perhatian!</strong>
                    {{ session('wajib_alasan') }}
                </div>
            @endif

            {{-- Notifikasi error --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Periksa kembali pengajuanmu:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulir --}}
            <form action="{{ route('keterlambatan.store') }}"
                  method="POST">

                @csrf

                <div class="mb-3">
                    <label for="tanggal"
                           class="form-label fw-semibold">
                        Tanggal Keterlambatan
                    </label>

                    <input type="date"
                           id="tanggal"
                           name="tanggal"
                           class="form-control"
                           value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}"
                           max="{{ now('Asia/Jakarta')->toDateString() }}"
                           required>
                </div>

                <div class="mb-4">
                    <label for="alasan"
                           class="form-label fw-semibold">
                        Alasan Keterlambatan
                    </label>

                    <textarea id="alasan"
                              name="alasan"
                              class="form-control"
                              rows="4"
                              maxlength="2000"
                              placeholder="Jelaskan alasan keterlambatanmu..."
                              required>{{ old('alasan') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100">
                    Kirim Alasan
                </button>

            </form>

            <hr class="my-4">

            {{-- Riwayat --}}
            <h4 class="fw-bold mb-3">
                Riwayat Pengajuan Saya
            </h4>

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Alasan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pengajuan as $item)

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                   {{ $item->absensi?->tanggal
                                      ? \Carbon\Carbon::parse($item->absensi->tanggal)->format('d-m-Y')
                                      : '-' }}
                                </td>

                                <td class="alasan">
                                    {{ $item->alasan }}
                                </td>

                                <td>
                                    @if ($item->status === 'disetujui')
                                        <span class="badge bg-success">
                                            Disetujui
                                        </span>
                                    @elseif ($item->status === 'ditolak')
                                        <span class="badge bg-danger">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="text-center text-secondary py-4">
                                    Belum ada pengajuan keterlambatan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <a href="{{ route('dashboard') }}"
               class="btn btn-outline-secondary w-100 mt-3">
                Kembali ke Dashboard
            </a>

        </div>
    </div>
</div>

</body>
</html>