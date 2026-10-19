@extends('layouts.app')
@section('title', 'Kelola Dispensasi')
@section('content')
<a href="{{ route('dashboard.guru_piket') }}" class="smk-back">← Kembali</a>
<h1 class="smk-section-title">Kelola Dispensasi</h1>
<p class="smk-section-sub">Input dispensasi sekolah, kelas, atau siswa.</p>

<div class="smk-card" style="margin-bottom:1.25rem">
    <form method="POST" action="{{ route('dispensasi.store') }}" class="smk-stack">
        @csrf
        <div>
            <label class="smk-label">Judul</label>
            <input name="judul" class="smk-input" value="{{ old('judul') }}" required maxlength="150">
        </div>
        <div>
            <label class="smk-label">Keterangan</label>
            <textarea name="keterangan" class="smk-textarea" rows="3" required>{{ old('keterangan') }}</textarea>
        </div>
        <div class="smk-row">
            <div>
                <label class="smk-label">Tanggal</label>
                <input type="date" name="tanggal" class="smk-input"
                       value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
            </div>
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
            <label class="smk-label">Tujuan</label>
            <select name="tujuan" id="tujuan" class="smk-select" required>
                <option value="sekolah" @selected(old('tujuan','sekolah')==='sekolah')>Seluruh sekolah</option>
                <option value="kelas" @selected(old('tujuan')==='kelas')>Kelas tertentu</option>
                <option value="siswa" @selected(old('tujuan')==='siswa')>Siswa tertentu</option>
            </select>
        </div>
        <div id="pilihKelas" style="{{ old('tujuan')==='kelas' ? '' : 'display:none' }}">
            <label class="smk-label">Pilih kelas</label>
            <select name="kelas_id" class="smk-select">
                <option value="">-- Pilih --</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}" @selected(old('kelas_id')==$item->id)>{{ $item->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div id="pilihSiswa" style="{{ old('tujuan')==='siswa' ? '' : 'display:none' }}">
            <label class="smk-label">Pilih siswa</label>
            <select name="siswa_id" class="smk-select">
                <option value="">-- Pilih --</option>
                @foreach($siswa as $item)
                    <option value="{{ $item->id }}" @selected(old('siswa_id')==$item->id)>
                        {{ $item->nama }} — {{ $item->nis }} ({{ $item->kelas->nama_kelas ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Simpan</button>
    </form>
</div>

<div class="smk-table-wrap">
    <table class="smk-table">
        <thead><tr><th>Judul</th><th>Tanggal</th><th>Tujuan</th><th></th></tr></thead>
        <tbody>
        @forelse($dispensasi as $item)
            <tr>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->tanggal->format('d-m-Y') }}</td>
                <td>
                    @if($item->siswa_id) {{ $item->siswa->nama ?? 'Siswa' }}
                    @elseif($item->kelas_id) {{ $item->kelas->nama_kelas ?? 'Kelas' }}
                    @else Seluruh sekolah
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('dispensasi.destroy', $item) }}"
                          onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="smk-btn smk-btn-danger" style="padding:0.35rem 0.7rem;font-size:0.75rem">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#64748b">Belum ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('tujuan')?.addEventListener('change', function () {
    document.getElementById('pilihKelas').style.display = this.value === 'kelas' ? '' : 'none';
    document.getElementById('pilihSiswa').style.display = this.value === 'siswa' ? '' : 'none';
});
</script>
@endpush
