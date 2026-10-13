<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin dan Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand fw-bold">
            Absensi Kelas
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm">Logout</button>
        </form>
    </div>
</nav>

<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 900px;">
        <div class="card-body p-4">

            <h3 class="fw-bold mb-1">Pengajuan Izin dan Sakit</h3>
            <p class="text-secondary">
                Tanggal pengajuan otomatis mengikuti tanggal hari ini.
                Pengajuan hanya dapat dilakukan satu kali sehari.
            </p>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Pengajuan gagal:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $tanggalHariIni = now('Asia/Jakarta')->toDateString();

                $sudahMengajukanHariIni = $pengajuan->contains(
                    fn ($item) => \Carbon\Carbon::parse($item->tanggal)->toDateString() === $tanggalHariIni
                );
            @endphp

            @if ($sudahMengajukanHariIni)
                <div class="alert alert-warning">
                    Kamu sudah mengajukan izin/sakit hari ini.
                    Kamu bisa mengajukan lagi besok.
                </div>
            @endif

            <form action="{{ route('pengajuan.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengajuan</label>
                    <input type="text"
                           class="form-control"
                           value="{{ now('Asia/Jakarta')->translatedFormat('l, d F Y') }}"
                           readonly>
                </div>

                <div class="mb-3">
                    <label for="jenis" class="form-label fw-semibold">
                        Jenis Pengajuan
                    </label>
                    <select id="jenis" name="jenis" class="form-select" required
                            {{ $sudahMengajukanHariIni ? 'disabled' : '' }}>
                        <option value="">Pilih jenis pengajuan</option>
                        <option value="izin" @selected(old('jenis') === 'izin')>Izin</option>
                        <option value="sakit" @selected(old('jenis') === 'sakit')>Sakit</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="alasan" class="form-label fw-semibold">
                        Alasan Pengajuan
                    </label>
                    <textarea id="alasan"
                              name="alasan"
                              class="form-control"
                              rows="4"
                              maxlength="2000"
                              placeholder="Jelaskan alasan izin atau sakit..."
                              required
                              {{ $sudahMengajukanHariIni ? 'disabled' : '' }}>{{ old('alasan') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="bukti" class="form-label fw-semibold">
                        Bukti Pendukung (Opsional)
                    </label>
                    <input type="file"
                           id="bukti"
                           name="bukti"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.pdf"
                           {{ $sudahMengajukanHariIni ? 'disabled' : '' }}>
                    <div class="form-text">
                        JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.
                    </div>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100"
                        {{ $sudahMengajukanHariIni ? 'disabled' : '' }}>
                    {{ $sudahMengajukanHariIni ? 'Sudah Mengajukan Hari Ini' : 'Kirim Pengajuan' }}
                </button>
            </form>

            <hr class="my-4">

            <h4 class="fw-bold mb-3">Riwayat Pengajuan Saya</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Alasan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $item)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                </td>
                                <td>
                                    @if ($item->jenis === 'izin')
                                        <span class="badge bg-primary">Izin</span>
                                    @else
                                        <span class="badge bg-info text-dark">Sakit</span>
                                    @endif
                                </td>
                                <td>{{ $item->alasan }}</td>
                                <td>
                                    @if ($item->status === 'disetujui')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($item->status === 'ditolak')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">
                                    Belum ada pengajuan.
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