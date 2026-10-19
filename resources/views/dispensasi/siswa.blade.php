@extends('layouts.app')
@section('title', 'Dispensasi')
@section('content')
<a href="{{ route('dashboard.siswa') }}" class="smk-back">← Kembali</a>
<h1 class="smk-section-title">Informasi Dispensasi</h1>
<p class="smk-section-sub">Dispensasi untukmu, kelasmu, atau seluruh sekolah.</p>
@forelse($dispensasi as $item)
<div class="smk-card" style="margin-bottom:0.85rem">
    <h3 style="margin:0 0 0.35rem;font-size:1rem">{{ $item->judul }}</h3>
    <p style="margin:0 0 0.5rem;color:#64748b;font-size:0.88rem">{{ $item->keterangan }}</p>
    <div style="font-size:0.8rem;color:#64748b">
        Tanggal: {{ $item->tanggal->format('d-m-Y') }} ·
        {{ $item->waktu_mulai ?? '--:--' }} – {{ $item->waktu_selesai ?? '--:--' }} ·
        @if($item->siswa_id) Siswa tertentu
        @elseif($item->kelas_id) {{ $item->kelas->nama_kelas ?? 'Kelas' }}
        @else Seluruh sekolah
        @endif
    </div>
</div>
@empty
<div class="smk-alert smk-alert-info">Belum ada dispensasi.</div>
@endforelse
@endsection
