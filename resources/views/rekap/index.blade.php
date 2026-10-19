@extends('layouts.app')
@section('title', 'Rekap Absensi')

@section('content')
<h1 class="smk-section-title">Rekap Absensi Kelas</h1>
<p class="smk-section-sub">Pantau kehadiran berdasarkan kelas dan tanggal.</p>

<div class="smk-card" style="margin-bottom:1.25rem">
    <form action="{{ route('rekap.index') }}" method="GET" class="smk-row">
        <div>
            <label class="smk-label">Kelas</label>
            <select name="kelas_id" class="smk-select" required>
                @foreach($daftarKelas as $kelas)
                    <option value="{{ $kelas->id }}" @selected($kelasTerpilih && $kelasTerpilih->id === $kelas->id)>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="smk-label">Tanggal</label>
            <input type="date" name="tanggal" class="smk-input" value="{{ $tanggal }}" required>
        </div>
        <div>
            <button type="submit" class="smk-btn smk-btn-primary" @disabled($daftarKelas->isEmpty())>Tampilkan</button>
        </div>
        @if($kelasTerpilih)
        <div>
            <a class="smk-btn smk-btn-success" href="{{ route('rekap.export', ['tanggal'=>$tanggal,'kelas_id'=>$kelasTerpilih->id]) }}">Export CSV</a>
        </div>
        @endif
    </form>
</div>

@if($kelasTerpilih)
    <div style="margin-bottom:1rem">
        <strong>{{ $kelasTerpilih->nama_kelas }}</strong>
        <span style="color:#64748b"> · {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</span>
    </div>
    <div class="smk-rekap-top">
        <div class="smk-stats">
        <div class="smk-stat"><div class="label">Total</div><div class="value blue">{{ $jumlahSiswa }}</div></div>
        <div class="smk-stat"><div class="label">Hadir</div><div class="value green">{{ $jumlahHadir }}</div></div>
        <div class="smk-stat"><div class="label">Terlambat</div><div class="value yellow">{{ $jumlahTerlambat }}</div></div>
        <div class="smk-stat"><div class="label">Belum absen</div><div class="value red">{{ $jumlahBelumAbsen }}</div></div>
    </div>
        <div class="smk-card smk-chart-card">
            <div class="smk-chart-head"><strong>Komposisi Kehadiran</strong><span>{{ $kelasTerpilih->nama_kelas }}</span></div>
            <div class="smk-chart-box"><canvas id="smkRekapChart"
                data-hadir="{{ $jumlahHadir }}" data-terlambat="{{ $jumlahTerlambat }}" data-belum="{{ $jumlahBelumAbsen }}"></canvas></div>
        </div>
    </div>
    <div class="smk-table-wrap">
        <table class="smk-table">
            <thead>
                <tr><th>No</th><th>NIS</th><th>Nama</th><th>Waktu</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($siswaRekap as $i => $s)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $s['nis'] }}</td>
                    <td>{{ $s['nama'] }}</td>
                    <td>{{ $s['waktu_absen'] ?? '-' }}</td>
                    <td>
                        @php $st = strtolower(trim($s['status'])); @endphp
                        @if($st==='hadir') <span class="smk-badge smk-badge-success">Hadir</span>
                        @elseif($st==='terlambat') <span class="smk-badge smk-badge-warning">Terlambat</span>
                        @else <span class="smk-badge smk-badge-muted">Belum Absen</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:#64748b">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif($daftarKelas->isEmpty())
    <div class="smk-alert smk-alert-info">Akun guru ini belum terhubung ke kelas.</div>
@endif
@endsection
