@extends('layouts.app')
@section('title', 'Rekap Kelas')
@section('content')
<a href="{{ route('dashboard.siswa') }}" class="smk-back">← Kembali</a>
<h1 class="smk-title">Rekap Kelas</h1>
<p class="smk-sub">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
<div class="smk-card">
<form method="GET" action="{{ route('rekap-kelas.index') }}" class="smk-row">
<div>
<label class="smk-label">Tanggal</label>
<input type="date" name="tanggal" class="smk-input" value="{{ $tanggal }}" required>
</div>
<div><button class="smk-btn">Tampilkan</button></div>
</form>
</div>
<div class="smk-table-wrap">
<table class="smk-table">
<thead><tr><th>NIS</th><th>Nama</th><th>Waktu</th><th>Status</th></tr></thead>
<tbody>
@forelse($daftarSiswa as $s)
@php $abs=$dataAbsensi->get($s->id); $st=$abs?->status??'belum'; @endphp
<tr>
<td>{{ $s->nis }}</td><td>{{ $s->nama }}</td><td>{{ $abs?->waktu_absen ?? '-' }}</td>
<td>
@if($st==='hadir') <span class="smk-badge smk-badge-ok">Hadir</span>
@elseif($st==='terlambat') <span class="smk-badge smk-badge-warn">Terlambat</span>
@else <span class="smk-badge">Belum</span>
@endif
</td>
</tr>
@empty
<tr><td colspan="4">Tidak ada data.</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
