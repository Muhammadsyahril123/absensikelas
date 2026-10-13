<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pemeriksaan Pengajuan</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Pemeriksaan Pengajuan</h3>
            <p class="text-muted mb-0">
                Pengelolaan izin, sakit, dan keterlambatan siswa.
            </p>
        </div>

        <a
            href="{{ route('dashboard.guru_piket') }}"
            class="btn btn-outline-secondary"
        >
            Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
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

    {{-- IZIN DAN SAKIT --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pengajuan Izin dan Sakit</h5>
        </div>

        <div class="card-body">
            @forelse ($pengajuanIzinSakit as $item)
                <div class="border rounded p-3 mb-3">

                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <h6 class="mb-1">
                                {{ $item->siswa->nama ?? 'Siswa tidak ditemukan' }}
                            </h6>

                            <div class="text-muted small">
                                Kelas:
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </div>

                            <div class="text-muted small">
                                Tanggal:
                                {{ $item->tanggal?->format('d-m-Y') ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <span class="badge
                                @if ($item->status === 'menunggu')
                                    bg-warning text-dark
                                @elseif ($item->status === 'disetujui')
                                    bg-success
                                @else
                                    bg-danger
                                @endif
                            ">
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <p class="mb-1">
                        <strong>Jenis:</strong>
                        {{ ucfirst($item->jenis) }}
                    </p>

                    <p class="mb-2">
                        <strong>Alasan:</strong>
                        {{ $item->alasan }}
                    </p>

                    @if ($item->bukti)
                        <p class="mb-2">
                            <strong>Bukti:</strong>
                            <a
                                href="{{ asset('storage/' . $item->bukti) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Lihat bukti
                            </a>
                        </p>
                    @endif

                    @if ($item->status === 'menunggu')
                        <div class="d-flex gap-2 flex-wrap">

                            <form
                                action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="status"
                                    value="disetujui"
                                >

                                <button class="btn btn-success btn-sm">
                                    Setujui
                                </button>
                            </form>

                            <form
                                action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}"
                                method="POST"
                                onsubmit="return confirm('Tolak pengajuan ini?')"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="status"
                                    value="ditolak"
                                >

                                <button class="btn btn-danger btn-sm">
                                    Tolak
                                </button>
                            </form>

                        </div>
                    @else
                        <div class="small text-muted">
                            Diperiksa oleh:
                            {{ $item->pemeriksa->name ?? '-' }}

                            @if ($item->diperiksa_pada)
                                <br>
                                Waktu pemeriksaan:
                                {{ $item->diperiksa_pada->format('d-m-Y H:i') }}
                            @endif
                        </div>
                    @endif

                </div>
            @empty
                <p class="text-muted mb-0">
                    Belum ada pengajuan izin atau sakit.
                </p>
            @endforelse
        </div>
    </div>

    {{-- KETERLAMBATAN --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Pengajuan Keterlambatan</h5>
        </div>

        <div class="card-body">
            @forelse ($pengajuanKeterlambatan as $item)
                <div class="border rounded p-3 mb-3">

                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <h6 class="mb-1">
                                {{ $item->siswa->nama ?? 'Siswa tidak ditemukan' }}
                            </h6>

                            <div class="text-muted small">
                                Kelas:
                                {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                            </div>

                            <div class="text-muted small">
                                Tanggal:
                                {{ $item->absensi?->tanggal
                                    ? \Illuminate\Support\Carbon::parse($item->absensi->tanggal)->format('d-m-Y')
                                    : '-' }}
                            </div>
                        </div>

                        <span class="badge
                            @if ($item->status === 'menunggu')
                                bg-warning text-dark
                            @elseif ($item->status === 'disetujui')
                                bg-success
                            @else
                                bg-danger
                            @endif
                        ">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>

                    <hr>

                    <p class="mb-2">
                        <strong>Alasan:</strong>
                        {{ $item->alasan }}
                    </p>

                    @if ($item->bukti)
                        <p class="mb-2">
                            <strong>Bukti:</strong>
                            <a
                                href="{{ asset('storage/' . $item->bukti) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Lihat bukti
                            </a>
                        </p>
                    @endif

                    @if ($item->status === 'menunggu')
                        <div class="d-flex gap-2 flex-wrap">

                            <form
                                action="{{ route('guru-piket.pemeriksaan.keterlambatan', $item->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="status"
                                    value="disetujui"
                                >

                                <button class="btn btn-success btn-sm">
                                    Setujui
                                </button>
                            </form>

                            <form
                                action="{{ route('guru-piket.pemeriksaan.keterlambatan', $item->id) }}"
                                method="POST"
                                onsubmit="return confirm('Tolak pengajuan ini?')"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="hidden"
                                    name="status"
                                    value="ditolak"
                                >

                                <button class="btn btn-danger btn-sm">
                                    Tolak
                                </button>
                            </form>

                        </div>
                    @else
                        <div class="small text-muted">
                            Diperiksa oleh:
                            {{ $item->pemeriksa->name ?? '-' }}

                            @if ($item->diperiksa_pada)
                                <br>
                                Waktu pemeriksaan:
                                {{ $item->diperiksa_pada->format('d-m-Y H:i') }}
                            @endif
                        </div>
                    @endif

                </div>
            @empty
                <p class="text-muted mb-0">
                    Belum ada pengajuan keterlambatan.
                </p>
            @endforelse
        </div>
    </div>

</div>

</body>
</html>