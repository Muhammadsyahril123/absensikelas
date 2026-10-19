@extends('layouts.app')
@section('title', 'Alasan Keterlambatan')

@section('content')
<h1 class="smk-section-title">Alasan Keterlambatan</h1>
<p class="smk-section-sub">Cukup isi teks. Tidak perlu upload foto. Sanksi mengikuti guru piket di sekolah.</p>

@if(session('wajib_alasan'))
    <div class="smk-alert smk-alert-warning">{{ session('wajib_alasan') }}</div>
@endif

<div class="smk-card" style="margin-bottom:1.25rem">
    <form action="{{ route('keterlambatan.store') }}" method="POST" class="smk-stack">
        @csrf
        <div>
            <label class="smk-label">Tanggal terlambat</label>
            <input type="date" name="tanggal" class="smk-input"
                   value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
        </div>
        <div>
            <label class="smk-label">Alasan</label>
            <textarea name="alasan" class="smk-textarea" rows="4" required
                      placeholder="Contoh: ban bocor, hujan deras...">{{ old('alasan') }}</textarea>
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Kirim alasan</button>
    </form>
</div>

<h2 class="smk-section-title" style="font-size:1rem">Riwayat</h2>
<div class="smk-table-wrap">
    <table class="smk-table">
        <thead><tr><th>Tanggal</th><th>Alasan</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($pengajuan as $item)
            <tr>
                <td>{{ $item->absensi ? \Carbon\Carbon::parse($item->absensi->tanggal)->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->alasan }}</td>
                <td>
                    @if($item->status==='disetujui') <span class="smk-badge smk-badge-success">Disetujui</span>
                    @elseif($item->status==='ditolak') <span class="smk-badge smk-badge-danger">Ditolak</span>
                    @else <span class="smk-badge smk-badge-warning">Menunggu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;color:#64748b">Belum ada pengajuan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
