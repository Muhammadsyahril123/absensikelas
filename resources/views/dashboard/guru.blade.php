@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('content')
<div class="smk-card">
    <h1 class="smk-title">Selamat datang, {{ auth()->user()->name ?? auth()->user()->username }}</h1>
    <p class="smk-sub">Guru</p>
</div>
<div class="smk-grid">
    <a class="smk-menu" href="{{ route('rekap.index') }}"><h3>Rekap Absensi</h3><p>Kehadiran per kelas</p></a>
    <a class="smk-menu" href="{{ route('catatan.index') }}"><h3>Catatan Mapel</h3><p>Lapor ketidakhadiran pelajaran</p></a>
    <a class="smk-menu" href="{{ route('dispensasi.guru') }}"><h3>Dispensasi</h3><p>Informasi dispensasi</p></a>
    <a class="smk-menu" href="{{ route('import.siswa') }}"><h3>Import Siswa</h3><p>Unggah CSV per kelas</p></a>
    @if(auth()->user()->guru && method_exists(auth()->user()->guru, 'kelasWali') && auth()->user()->guru->kelasWali()->exists())
    <a class="smk-menu" href="{{ route('guru-kelas.siswa.index') }}"><h3>Kelola Siswa</h3><p>Siswa kelas wali</p></a>
    @endif
</div>
@endsection
