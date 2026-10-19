@extends('layouts.app')
@section('title', 'Buat Catatan')
@section('content')
<a href="{{ route('catatan.index') }}" class="smk-back">← Kembali</a>
<h1 class="smk-section-title">Buat Catatan Ketidakhadiran</h1>
<p class="smk-section-sub">Lapor siswa yang tidak hadir di jam pelajaran.</p>
<div class="smk-card">
    <form method="POST" action="{{ route('catatan.store') }}" class="smk-stack">
        @csrf
        <div>
            <label class="smk-label">Kelas</label>
            <select name="kelas_id" id="kelas_id" class="smk-select" required>
                <option value="">Pilih kelas</option>
                @foreach($daftarKelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="smk-label">Siswa</label>
            <select name="siswa_id" id="siswa_id" class="smk-select" required>
                <option value="">Pilih kelas dulu</option>
            </select>
        </div>
        <div>
            <label class="smk-label">Tanggal</label>
            <input type="date" name="tanggal" class="smk-input"
                   value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
        </div>
        <div class="smk-row">
            <div>
                <label class="smk-label">Waktu mulai</label>
                <input type="time" name="waktu_mulai" class="smk-input" value="{{ old('waktu_mulai') }}">
            </div>
            <div>
                <label class="smk-label">Waktu selesai</label>
                <input type="time" name="waktu_selesai" class="smk-input" value="{{ old('waktu_selesai') }}">
            </div>
        </div>
        <div>
            <label class="smk-label">Kegiatan / mapel</label>
            <input type="text" name="kegiatan" class="smk-input" value="{{ old('kegiatan') }}" required>
        </div>
        <div>
            <label class="smk-label">Keterangan</label>
            <textarea name="keterangan" class="smk-textarea" rows="3" required>{{ old('keterangan') }}</textarea>
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Simpan catatan</button>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('kelas_id')?.addEventListener('change', function () {
    var sel = document.getElementById('siswa_id');
    sel.innerHTML = '<option value="">Memuat...</option>';
    fetch('{{ route("catatan.siswa-by-kelas") }}?kelas_id=' + this.value)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">Pilih siswa</option>';
            (Array.isArray(data) ? data : (data.siswa || [])).forEach(function (s) {
                var o = document.createElement('option');
                o.value = s.id; o.textContent = s.nama + ' (' + s.nis + ')';
                sel.appendChild(o);
            });
        })
        .catch(function () { sel.innerHTML = '<option value="">Gagal memuat</option>'; });
});
</script>
@endpush
