@extends('layouts.app')
@section('title', 'Pemeriksaan')
@section('content')
<a href="{{ route('dashboard.guru_piket') }}" class="smk-back">← Kembali</a>
<h1 class="smk-section-title">Pemeriksaan Pengajuan</h1>
<p class="smk-section-sub">Izin, sakit, dan alasan keterlambatan.</p>

<h2 class="smk-section-title" style="font-size:1rem;margin-top:0.5rem">Izin &amp; Sakit</h2>
@forelse($pengajuanIzinSakit as $item)
<div class="smk-card" style="margin-bottom:0.75rem">
    <strong>{{ $item->siswa->nama ?? '-' }}</strong>
    <span style="color:#64748b;font-size:0.85rem"> · {{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>
    <div style="font-size:0.85rem;color:#64748b;margin:0.35rem 0">
        {{ $item->jenis }} ·
        {{ $item->tanggal?->format('d-m-Y') }}
        @if($item->tanggal_selesai && $item->tanggal_selesai->ne($item->tanggal))
            – {{ $item->tanggal_selesai->format('d-m-Y') }}
        @endif
    </div>
    <p style="margin:0 0 0.5rem;font-size:0.9rem">{{ $item->alasan }}</p>
    @if($item->bukti)
        <p style="font-size:0.8rem"><a href="{{ asset('storage/'.$item->bukti) }}" target="_blank">Lihat bukti</a></p>
    @endif
    <span class="smk-badge {{ $item->status==='disetujui'?'smk-badge-success':($item->status==='ditolak'?'smk-badge-danger':'smk-badge-warning') }}">{{ $item->status }}</span>
    @if($item->status==='menunggu')
    <div style="display:flex;gap:0.5rem;margin-top:0.65rem;flex-wrap:wrap">
        <form method="POST" action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="disetujui">
            <button class="smk-btn smk-btn-success" style="padding:0.4rem 0.8rem;font-size:0.8rem">Setujui</button>
        </form>
        <form method="POST" action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}" onsubmit="return confirm('Tolak?')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="ditolak">
            <button class="smk-btn smk-btn-danger" style="padding:0.4rem 0.8rem;font-size:0.8rem">Tolak</button>
        </form>
    </div>
    @endif
</div>
@empty
<div class="smk-alert smk-alert-info">Tidak ada pengajuan izin/sakit.</div>
@endforelse

<h2 class="smk-section-title" style="font-size:1rem;margin-top:1.25rem">Keterlambatan</h2>
@forelse($pengajuanKeterlambatan as $item)
<div class="smk-card" style="margin-bottom:0.75rem">
    <strong>{{ $item->siswa->nama ?? '-' }}</strong>
    <span style="color:#64748b;font-size:0.85rem"> · {{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>
    <div style="font-size:0.85rem;color:#64748b;margin:0.35rem 0">
        Tanggal absensi:
        {{ $item->absensi?->tanggal ? \Carbon\Carbon::parse($item->absensi->tanggal)->format('d-m-Y') : '-' }}
    </div>
    <p style="margin:0 0 0.5rem;font-size:0.9rem">{{ $item->alasan }}</p>
    <span class="smk-badge {{ $item->status==='disetujui'?'smk-badge-success':($item->status==='ditolak'?'smk-badge-danger':'smk-badge-warning') }}">{{ $item->status }}</span>
    @if($item->status==='menunggu')
    <div style="display:flex;gap:0.5rem;margin-top:0.65rem;flex-wrap:wrap">
        <form method="POST" action="{{ route('guru-piket.pemeriksaan.keterlambatan', $item->id) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="disetujui">
            <button class="smk-btn smk-btn-success" style="padding:0.4rem 0.8rem;font-size:0.8rem">Setujui</button>
        </form>
        <form method="POST" action="{{ route('guru-piket.pemeriksaan.keterlambatan', $item->id) }}" onsubmit="return confirm('Tolak?')">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="ditolak">
            <button class="smk-btn smk-btn-danger" style="padding:0.4rem 0.8rem;font-size:0.8rem">Tolak</button>
        </form>
    </div>
    @endif
</div>
@empty
<div class="smk-alert smk-alert-info">Tidak ada pengajuan keterlambatan.</div>
@endforelse
@endsection
