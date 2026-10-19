@extends('layouts.app')
@section('title', 'Kelola Siswa')
@section('content')
<a href="{{ route('dashboard.guru') }}" class="smk-back">← Kembali</a>
<h1 class="smk-section-title">Kelola Siswa (Wali Kelas)</h1>
<p class="smk-section-sub">
    @if(isset($kelasWali) && $kelasWali->count())
        Kelas wali: {{ $kelasWali->pluck('nama_kelas')->join(', ') }}
    @else
        Data siswa kelas wali
    @endif
</p>
<div class="smk-table-wrap">
    <table class="smk-table">
        <thead><tr><th>NIS</th><th>Nama</th><th>Kelas</th><th></th></tr></thead>
        <tbody>
        @forelse($siswa as $s)
            <tr>
                <td>{{ $s->nis }}</td>
                <td>{{ $s->nama }}</td>
                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td>
                    <form method="POST" action="{{ route('wali-kelas.siswa.reset-password', $s) }}"
                          onsubmit="return confirm('Reset password ke default?')">
                        @csrf
                        <button class="smk-btn smk-btn-outline" style="padding:0.35rem 0.7rem;font-size:0.75rem">Reset password</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#64748b">Tidak ada siswa.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
