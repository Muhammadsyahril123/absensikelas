@extends('layouts.app')

@section('title', 'Pemeriksaan')

@section('content')
<a href="{{ route('dashboard.guru_piket') }}" class="smk-back">Kembali</a>

<h1 class="smk-title">Pemeriksaan harian</h1>

<p class="smk-sub">
    {{ \Carbon\Carbon::parse($hariIni)->format('d-m-Y') }}
    · Jam tutup absen {{ $jamTutup }}
</p>

@if($sudahLewatJamTutup)
    <div class="smk-alert smk-alert-info">
        Sudah lewat jam tutup. Siswa yang belum absen dan tanpa izin disetujui dihitung alfa.
    </div>
@else
    <div class="smk-alert smk-alert-info">
        Belum lewat jam tutup. Siswa yang belum absen masih berstatus belum absen (bukan alfa).
    </div>
@endif

<div class="smk-card">
    <p style="margin:0;font-size:0.9rem;line-height:1.6">
        Total siswa: <strong>{{ $ringkasan['total'] }}</strong><br>
        Hadir: <strong>{{ $ringkasan['hadir'] }}</strong> ·
        Terlambat: <strong>{{ $ringkasan['terlambat'] }}</strong> ·
        Izin (disetujui): <strong>{{ $ringkasan['izin'] }}</strong><br>
        Alfa: <strong>{{ $ringkasan['alfa'] }}</strong>
        @if(!$sudahLewatJamTutup)
            · Belum absen: <strong>{{ $ringkasan['belum'] }}</strong>
        @endif
        <br>
        Pengajuan izin/sakit menunggu: <strong>{{ $jumlahMenungguIzin }}</strong>
    </p>
</div>

<h2 class="smk-title" style="font-size:1rem;margin-top:1rem">Pengajuan izin dan sakit</h2>

@forelse($pengajuanIzinSakit as $item)
    <div class="smk-card">
        <strong>{{ $item->siswa->nama ?? '-' }}</strong>
        <span class="smk-sub" style="display:inline"> · {{ $item->siswa->kelas->nama_kelas ?? '-' }}</span>

        <div class="smk-sub" style="margin:0.35rem 0">
            {{ ucfirst($item->jenis) }} ·
            {{ $item->tanggal ? $item->tanggal->format('d-m-Y') : '-' }}
            @if($item->tanggal_selesai && $item->tanggal && !$item->tanggal_selesai->eq($item->tanggal))
                – {{ $item->tanggal_selesai->format('d-m-Y') }}
            @endif
        </div>

        <p style="margin:0 0 0.5rem;font-size:0.9rem">{{ $item->alasan }}</p>

        @if($item->status === 'disetujui')
            <span class="smk-badge smk-badge-ok">Disetujui</span>
        @elseif($item->status === 'ditolak')
            <span class="smk-badge smk-badge-bad">Ditolak</span>
        @else
            <span class="smk-badge smk-badge-warn">Menunggu</span>
        @endif

        @if($item->status === 'menunggu')
            <div class="smk-row" style="margin-top:0.75rem">
                <form method="POST" action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="disetujui">
                    <button type="submit" class="smk-btn smk-btn-success">Setujui</button>
                </form>
                <form method="POST" action="{{ route('guru-piket.pemeriksaan.izin-sakit', $item->id) }}" onsubmit="return confirm('Tolak pengajuan ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button type="submit" class="smk-btn smk-btn-danger">Tolak</button>
                </form>
            </div>
        @endif
    </div>
@empty
    <div class="smk-alert smk-alert-info">Belum ada pengajuan izin atau sakit.</div>
@endforelse

<h2 class="smk-title" style="font-size:1rem;margin-top:1.25rem">Siswa terlambat hari ini</h2>
<div class="smk-table-wrap">
    <table class="smk-table">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarTerlambat as $row)
                <tr>
                    <td>{{ $row['siswa']->nis }}</td>
                    <td>{{ $row['siswa']->nama }}</td>
                    <td>{{ $row['siswa']->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $row['absensi']->waktu_absen }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<h2 class="smk-title" style="font-size:1rem;margin-top:1.25rem">Alfa hari ini</h2>
<p class="smk-sub">Tidak absen GPS dan tidak punya izin disetujui.</p>
@if(!$sudahLewatJamTutup)
    <p class="smk-sub">Baru dihitung alfa setelah jam tutup {{ $jamTutup }}.</p>
@endif

<div class="smk-table-wrap">
    <table class="smk-table">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarAlfa as $row)
                <tr>
                    <td>{{ $row['siswa']->nis }}</td>
                    <td>{{ $row['siswa']->nama }}</td>
                    <td>{{ $row['siswa']->kelas->nama_kelas ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        @if($sudahLewatJamTutup)
                            Tidak ada alfa.
                        @else
                            Belum dihitung — masih dalam jam absen.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
