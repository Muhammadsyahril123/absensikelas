@extends('layouts.app')
@section('title', 'Dashboard Guru Piket')
@section('content')
<div class="smk-card">
    <h1 class="smk-title">Selamat datang, {{ auth()->user()->name ?? auth()->user()->username }}</h1>
    <p class="smk-sub">Guru piket</p>
</div>
<div class="smk-grid">
    <a class="smk-menu" href="{{ route('guru-piket.pemeriksaan') }}">
        <h3>Pemeriksaan</h3>
        <p>Izin/sakit menunggu &amp; daftar siswa terlambat hari ini</p>
    </a>
    <a class="smk-menu" href="{{ route('dispensasi.piket') }}">
        <h3>Dispensasi</h3>
        <p>Catat dispensasi</p>
    </a>
    <a class="smk-menu" href="{{ route('guru-piket.catatan') }}">
        <h3>Catatan Mapel</h3>
        <p>Tindak lanjut laporan</p>
    </a>
    <a class="smk-menu" href="{{ route('pengaturan.edit') }}">
        <h3>Pengaturan</h3>
        <p>Lokasi &amp; jam absensi</p>
    </a>
</div>
@endsection
