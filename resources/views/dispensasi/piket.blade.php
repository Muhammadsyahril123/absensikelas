<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Dispensasi</title>

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
        <span class="navbar-brand fw-bold">Kelola Dispensasi</span>
        <a href="{{ route('dashboard.guru_piket') }}" class="btn btn-light btn-sm">
            Kembali
        </a>
    </div>
</nav>

<div class="container py-4">
    <h3 class="fw-bold mb-3">Informasi Dispensasi</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Tambah Dispensasi</h5>

            <form method="POST" action="{{ route('dispensasi.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input
                        name="judul"
                        class="form-control"
                        value="{{ old('judul') }}"
                        required
                        maxlength="150"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea
                        name="keterangan"
                        class="form-control"
                        rows="3"
                        required
                    >{{ old('keterangan') }}</textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Waktu Mulai</label>
                        <input
                            type="time"
                            name="waktu_mulai"
                            class="form-control"
                            value="{{ old('waktu_mulai') }}"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Waktu Selesai</label>
                        <input
                            type="time"
                            name="waktu_selesai"
                            class="form-control"
                            value="{{ old('waktu_selesai') }}"
                        >
                    </div>
                </div>

                <div class="mt-3 mb-3">
                    <label class="form-label">Tujuan Dispensasi</label>
                    <select name="tujuan" id="tujuan" class="form-select" required>
                        <option value="sekolah" {{ old('tujuan', 'sekolah') === 'sekolah' ? 'selected' : '' }}>
                            Seluruh Sekolah
                        </option>
                        <option value="kelas" {{ old('tujuan') === 'kelas' ? 'selected' : '' }}>
                            Kelas Tertentu
                        </option>
                        <option value="siswa" {{ old('tujuan') === 'siswa' ? 'selected' : '' }}>
                            Siswa Tertentu
                        </option>
                    </select>
                </div>

                <div class="mb-3 {{ old('tujuan') === 'kelas' ? '' : 'd-none' }}" id="pilihKelas">
                    <label class="form-label">Pilih Kelas</label>

                    <select name="kelas_id" id="kelas_id" class="form-select">
                        <option value="">-- Pilih Kelas --</option>

                        @foreach ($kelas as $item)
                            <option
                                value="{{ $item->id }}"
                                {{ old('kelas_id') == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 {{ old('tujuan') === 'siswa' ? '' : 'd-none' }}" id="pilihSiswa">
                    <label class="form-label">Pilih Siswa</label>

                    <select name="siswa_id" id="siswa_id" class="form-select">
                        <option value="">-- Pilih Siswa --</option>

                        @foreach ($siswa as $item)
                            <option
                                value="{{ $item->id }}"
                                {{ old('siswa_id') == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->nama }} — NIS: {{ $item->nis }}
                                ({{ $item->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                            </option>
                        @endforeach
                    </select>

                    @if ($siswa->isEmpty())
                        <small class="text-danger">
                            Data siswa belum tersedia.
                        </small>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Dispensasi
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Daftar Dispensasi</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Tujuan</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($dispensasi as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->judul }}</strong>
                                    <div class="small text-secondary">
                                        {{ $item->keterangan }}
                                    </div>
                                    <div class="small">
                                        {{ $item->waktu_mulai ?? '--:--' }}
                                        –
                                        {{ $item->waktu_selesai ?? '--:--' }}
                                    </div>
                                </td>

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

                                <td>
                                    <form
                                        method="POST"
                                        action="{{ route('dispensasi.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus dispensasi ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada dispensasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const tujuan = document.getElementById('tujuan');
    const pilihKelas = document.getElementById('pilihKelas');
    const pilihSiswa = document.getElementById('pilihSiswa');

    function aturTujuan() {
        pilihKelas.classList.toggle('d-none', tujuan.value !== 'kelas');
        pilihSiswa.classList.toggle('d-none', tujuan.value !== 'siswa');
    }

    tujuan.addEventListener('change', aturTujuan);
    aturTujuan();
</script>

</body>
</html>