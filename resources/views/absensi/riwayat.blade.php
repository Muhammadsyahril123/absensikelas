@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('content')
<a href="{{ route('dashboard.siswa') }}" class="smk-back">← Kembali</a>
<h1 class="smk-title">Riwayat Absensi</h1>
<p class="smk-sub">Catatan kehadiran Anda</p>
<div class="smk-table-wrap">
<table class="smk-table">
<thead><tr><th>Tanggal</th><th>Jam</th><th>Status</th></tr></thead>
<tbody>
@forelse($riwayat as $a)
<tr>
<td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d-m-Y') }}</td>
<td>{{ $a->waktu_absen }}</td>
<td>
@if($a->status==='hadir') <span class="smk-badge smk-badge-ok">Hadir</span>
@elseif($a->status==='terlambat') <span class="smk-badge smk-badge-warn">Terlambat</span>
@else <span class="smk-badge">{{ $a->status }}</span>
@endif
</td>
</tr>
@empty
<tr><td colspan="3">Belum ada data.</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
