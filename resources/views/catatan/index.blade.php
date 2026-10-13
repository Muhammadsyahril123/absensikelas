<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Ketidakhadiran Saat Pelajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Catatan Ketidakhadiran</span>
        <div class="d-flex gap-2">
            @if ($user->role === 'guru')
                <a href="{{ route('catatan.create') }}" class="btn btn-light btn-sm">Buat Catatan</a>
                <a href="{{ route('dashboard.guru') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
            @else
                <a href="{{ route('dashboard.guru_piket') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
            @endif
        </div>
    </div>
</nav>

<div class="container py-4">

    <h3 class="fw-bold mb-1">Catatan Ketidakhadiran Saat Pelajaran</h3>
    <p class="text-secondary mb-4">
        Laporan siswa yang tidak ada di kelas pada jam pelajaran.
        Tidak mengubah status absensi harian.
    </p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="card shadow-sm border-0 mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ $tanggal }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select">
                    <option value="">Semua kelas</option>
                    @foreach ($daftarKelas as $k)
                        <option value="{{ $k->id }}"
                            @selected(request('kelas_id') == $k->id)>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Jam</th>
                            <th>Mapel / Kegiatan</th>
                            <th>Keterangan</th>
                            <th>Pembuat</th>
                            <th>Tindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($catatan as $i => $item)
                            @php
                                $bisaTindakLanjut = $user->role === 'guru_piket'
                                    || ($user->role === 'guru' && $kelasWaliIds->contains($item->siswa?->kelas_id));
                                $routeTindak = $user->role === 'guru_piket'
                                    ? 'guru-piket.catatan.tindak-lanjut'
                                    : 'catatan.tindak-lanjut';
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->siswa?->nama ?? '-' }}</div>
                                    <small class="text-secondary">{{ $item->siswa?->nis }}</small>
                                </td>
                                <td>{{ $item->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                                <td>
                                    @if ($item->waktu_mulai || $item->waktu_selesai)
                                        {{ substr($item->waktu_mulai ?? '', 0, 5) }}
                                        –
                                        {{ substr($item->waktu_selesai ?? '', 0, 5) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->kegiatan }}</td>
                                <td>{{ $item->keterangan ?: '-' }}</td>
                                <td>{{ $item->pembuat?->name ?? '-' }}</td>
                                <td>
                                    @php $st = $item->status_tindak_lanjut ?? 'belum'; @endphp
                                    @if ($st === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($st === 'sudah_dihubungi')
                                        <span class="badge bg-info text-dark">Sudah dihubungi</span>
                                    @elseif ($st === 'perlu_tindak_lanjut')
                                        <span class="badge bg-warning text-dark">Perlu tindak lanjut</span>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                    @if ($item->catatan_tindak_lanjut)
                                        <div class="small text-secondary mt-1">
                                            {{ $item->catatan_tindak_lanjut }}
                                        </div>
                                    @endif
                                    @if ($item->penindakLanjut)
                                        <div class="small text-muted">
                                            oleh {{ $item->penindakLanjut->name }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($bisaTindakLanjut)
                                        <button class="btn btn-sm btn-outline-primary"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#tl-{{ $item->id }}">
                                            Tindak Lanjut
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($bisaTindakLanjut)
                                <tr class="collapse" id="tl-{{ $item->id }}">
                                    <td colspan="9" class="bg-light">
                                        <form method="POST"
                                              action="{{ route($routeTindak, $item) }}"
                                              class="row g-2 align-items-end p-2">
                                            @csrf
                                            @method('PATCH')
                                            <div class="col-md-3">
                                                <label class="form-label small">Status</label>
                                                <select name="status_tindak_lanjut" class="form-select form-select-sm" required>
                                                    <option value="belum" @selected(($item->status_tindak_lanjut ?? 'belum') === 'belum')>Belum</option>
                                                    <option value="sudah_dihubungi" @selected(($item->status_tindak_lanjut ?? '') === 'sudah_dihubungi')>Sudah dihubungi</option>
                                                    <option value="perlu_tindak_lanjut" @selected(($item->status_tindak_lanjut ?? '') === 'perlu_tindak_lanjut')>Perlu tindak lanjut</option>
                                                    <option value="selesai" @selected(($item->status_tindak_lanjut ?? '') === 'selesai')>Selesai</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small">Catatan tindak lanjut</label>
                                                <input type="text" name="catatan_tindak_lanjut"
                                                       class="form-control form-control-sm"
                                                       value="{{ $item->catatan_tindak_lanjut }}"
                                                       placeholder="Contoh: sudah hubungi orang tua">
                                            </div>
                                            <div class="col-md-3">
                                                <button class="btn btn-sm btn-primary w-100">Simpan</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-secondary">
                                    Belum ada catatan untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
