@extends('layouts.app')
@section('title', 'Catatan Ketidakhadiran')
@section('content')
@php
  $dash = $user->role === 'guru_piket' ? route('dashboard.guru_piket') : route('dashboard.guru');
  $listRoute = $user->role === 'guru_piket' ? 'guru-piket.catatan' : 'catatan.index';
  $tlRoute = $user->role === 'guru_piket' ? 'guru-piket.catatan.tindak-lanjut' : 'catatan.tindak-lanjut';
@endphp
<a href="{{ $dash }}" class="smk-back">← Kembali</a>
<div style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:space-between;align-items:center;margin-bottom:0.5rem">
    <div>
        <h1 class="smk-section-title">Catatan Ketidakhadiran Mapel</h1>
        <p class="smk-section-sub" style="margin:0">Tidak mengubah absensi GPS harian.</p>
    </div>
    @if($user->role === 'guru')
        <a href="{{ route('catatan.create') }}" class="smk-btn smk-btn-accent">+ Buat catatan</a>
    @endif
</div>

<div class="smk-card" style="margin-bottom:1rem">
    <form method="GET" action="{{ route($listRoute) }}" class="smk-row">
        <div>
            <label class="smk-label">Tanggal</label>
            <input type="date" name="tanggal" class="smk-input" value="{{ $tanggal }}">
        </div>
        <div>
            <label class="smk-label">Kelas</label>
            <select name="kelas_id" class="smk-select">
                <option value="">Semua</option>
                @foreach($daftarKelas as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div><button class="smk-btn smk-btn-primary">Filter</button></div>
    </form>
</div>

@forelse($catatan as $item)
<div class="smk-card" style="margin-bottom:0.85rem">
    <strong>{{ $item->siswa->nama ?? '-' }}</strong>
    <span style="color:#64748b;font-size:0.85rem"> · {{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>
    <div style="font-size:0.85rem;margin:0.4rem 0;color:#64748b">
        {{ $item->kegiatan ?? 'Mapel' }} ·
        {{ $item->waktu_mulai ?? '' }}–{{ $item->waktu_selesai ?? '' }} ·
        Pelapor: {{ $item->guruPelapor->name ?? $item->pembuat->name ?? '-' }}
    </div>
    <p style="margin:0 0 0.5rem;font-size:0.9rem">{{ $item->keterangan }}</p>
    @php $st = $item->status_tindak_lanjut ?? 'belum'; @endphp
    <span class="smk-badge {{ $st==='selesai' ? 'smk-badge-success' : ($st==='belum' ? 'smk-badge-muted' : 'smk-badge-warning') }}">
        {{ str_replace('_',' ', $st) }}
    </span>

    @php
        $bisaTL = $user->role === 'guru_piket'
            || ($user->role === 'guru' && $item->siswa && $kelasWaliIds->contains($item->siswa->kelas_id));
    @endphp
    @if($bisaTL)
    <form method="POST" action="{{ route($tlRoute, $item) }}" class="smk-stack" style="margin-top:0.85rem">
        @csrf @method('PATCH')
        <div class="smk-row">
            <div>
                <label class="smk-label">Status tindak lanjut</label>
                <select name="status_tindak_lanjut" class="smk-select" required>
                    <option value="belum" @selected($st==='belum')>Belum</option>
                    <option value="sudah_dihubungi" @selected($st==='sudah_dihubungi')>Sudah dihubungi</option>
                    <option value="perlu_tindak_lanjut" @selected($st==='perlu_tindak_lanjut')>Perlu tindak lanjut</option>
                    <option value="selesai" @selected($st==='selesai')>Selesai</option>
                </select>
            </div>
            <div>
                <label class="smk-label">Catatan</label>
                <input type="text" name="catatan_tindak_lanjut" class="smk-input"
                       value="{{ old('catatan_tindak_lanjut', $item->catatan_tindak_lanjut) }}">
            </div>
            <div><button class="smk-btn smk-btn-primary">Simpan</button></div>
        </div>
    </form>
    @endif
</div>
@empty
<div class="smk-alert smk-alert-info">Belum ada catatan pada tanggal ini.</div>
@endforelse
@endsection
