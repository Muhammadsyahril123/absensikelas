<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Catatan Ketidakhadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold">Buat Catatan</span>
        <a href="{{ route('catatan.index') }}" class="btn btn-light btn-sm">Kembali</a>
    </div>
</nav>

<div class="container py-4" style="max-width: 640px;">

    <h3 class="fw-bold mb-1">Catat Siswa Tidak Ada di Kelas</h3>
    <p class="text-secondary mb-4">
        Fitur ini untuk melaporkan siswa yang tidak hadir saat pelajaran berlangsung.
        Tidak mengubah absensi pagi.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('catatan.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Kelas</label>
                    <select id="kelas_id" class="form-select" required>
                        <option value="">Pilih kelas</option>
                        @foreach ($daftarKelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Siswa</label>
                    <select name="siswa_id" id="siswa_id" class="form-select" required disabled>
                        <option value="">Pilih kelas dulu</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Jam mulai</label>
                        <input type="time" name="waktu_mulai" class="form-control"
                               value="{{ old('waktu_mulai') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Jam selesai</label>
                        <input type="time" name="waktu_selesai" class="form-control"
                               value="{{ old('waktu_selesai') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mata pelajaran / kegiatan</label>
                    <input type="text" name="kegiatan" class="form-control"
                           value="{{ old('kegiatan') }}"
                           placeholder="Contoh: Pemrograman Web" required maxlength="150">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan (opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="3"
                              placeholder="Contoh: tidak masuk kelas setelah istirahat">{{ old('keterangan') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan Catatan</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('kelas_id').addEventListener('change', async function () {
    const kelasId = this.value;
    const siswaSelect = document.getElementById('siswa_id');

    siswaSelect.innerHTML = '<option value="">Memuat...</option>';
    siswaSelect.disabled = true;

    if (!kelasId) {
        siswaSelect.innerHTML = '<option value="">Pilih kelas dulu</option>';
        return;
    }

    try {
        const res = await fetch('{{ route('catatan.siswa-by-kelas') }}?kelas_id=' + kelasId, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        siswaSelect.innerHTML = '<option value="">Pilih siswa</option>';
        data.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama + ' (' + s.nis + ')';
            siswaSelect.appendChild(opt);
        });
        siswaSelect.disabled = false;
    } catch (e) {
        siswaSelect.innerHTML = '<option value="">Gagal memuat siswa</option>';
    }
});
</script>
</body>
</html>
