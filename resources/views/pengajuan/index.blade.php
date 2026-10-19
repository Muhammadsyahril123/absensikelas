@extends('layouts.app')
@section('title', 'Izin & Sakit')

@section('content')
<h1 class="smk-section-title">Pengajuan Izin / Sakit</h1>
<p class="smk-section-sub">Bisa satu hari atau rentang tanggal. Menunggu pemeriksaan guru piket.</p>

<div class="smk-card" style="margin-bottom:1.25rem">
    <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="smk-stack">
        @csrf
        <div>
            <label class="smk-label">Jenis</label>
            <select name="jenis" class="smk-select" required>
                <option value="">Pilih</option>
                <option value="izin" @selected(old('jenis')==='izin')>Izin</option>
                <option value="sakit" @selected(old('jenis')==='sakit')>Sakit</option>
            </select>
        </div>
        <div class="smk-row">
            <div>
                <label class="smk-label">Tanggal mulai</label>
                <input type="date" name="tanggal" class="smk-input"
                       value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required>
            </div>
            <div>
                <label class="smk-label">Tanggal selesai (opsional)</label>
                <input type="date" name="tanggal_selesai" class="smk-input" value="{{ old('tanggal_selesai') }}">
            </div>
        </div>
        <div>
            <label class="smk-label">Alasan</label>
            <textarea name="alasan" class="smk-textarea" rows="3" required>{{ old('alasan') }}</textarea>
        </div>
        <div>
            <label class="smk-label">Bukti (opsional)</label>
            <input type="file" name="bukti" class="smk-input" accept=".jpg,.jpeg,.png,.pdf">
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Kirim pengajuan</button>
    </form>
</div>

<div class="smk-table-wrap">
    <table class="smk-table">
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Alasan</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($pengajuan as $item)
            <tr>
                <td>
                    {{ $item->tanggal->format('d-m-Y') }}
                    @if($item->tanggal_selesai && $item->tanggal_selesai->ne($item->tanggal))
                        – {{ $item->tanggal_selesai->format('d-m-Y') }}
                    @endif
                </td>
                <td>
                    @if($item->jenis==='izin') <span class="smk-badge smk-badge-info">Izin</span>
                    @else <span class="smk-badge smk-badge-warning">Sakit</span>
                    @endif
                </td>
                <td>{{ $item->alasan }}</td>
                <td>
                    @if($item->status==='disetujui') <span class="smk-badge smk-badge-success">Disetujui</span>
                    @elseif($item->status==='ditolak') <span class="smk-badge smk-badge-danger">Ditolak</span>
                    @else <span class="smk-badge smk-badge-warning">Menunggu</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#64748b">Belum ada pengajuan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
