@extends('layouts.app')
@section('title', 'Dashboard Siswa')
@section('content')
<div class="smk-card">
    <h1 class="smk-title">Selamat datang, {{ auth()->user()->name ?? auth()->user()->username }}</h1>
    <p class="smk-sub">Siswa · Absensi berbasis lokasi</p>

    @if(session('wajib_alasan'))
        <div class="smk-alert smk-alert-warning">{{ session('wajib_alasan') }}</div>
    @endif

    <form id="form-absen" action="{{ route('absensi.absen') }}" method="POST" class="smk-stack">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="button" id="btn-absen" class="smk-btn smk-btn-block">Absen sekarang</button>
    </form>
    <p id="absen-status" class="smk-sub" style="margin-top:0.5rem"></p>
</div>

<div class="smk-grid">
    <a class="smk-menu" href="{{ route('absensi.riwayat') }}"><h3>Riwayat Absensi</h3><p>Riwayat kehadiran</p></a>
    <a class="smk-menu" href="{{ route('pengajuan.index') }}"><h3>Izin / Sakit</h3><p>Pengajuan izin atau sakit</p></a>
    <a class="smk-menu" href="{{ route('keterlambatan.index') }}"><h3>Keterlambatan</h3><p>Alasan keterlambatan</p></a>
    <a class="smk-menu" href="{{ route('rekap-kelas.index') }}"><h3>Rekap Kelas</h3><p>Kehadiran sekelas</p></a>
    <a class="smk-menu" href="{{ route('dispensasi.siswa') }}"><h3>Dispensasi</h3><p>Informasi dispensasi</p></a>
</div>
@endsection
