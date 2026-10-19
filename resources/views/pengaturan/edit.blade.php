@extends('layouts.app')
@section('title', 'Pengaturan Sekolah')

@section('content')
<h1 class="smk-section-title">Pengaturan Sekolah</h1>
<p class="smk-section-sub">Lokasi GPS, radius, dan jam absensi — dikelola guru piket.</p>

<div class="smk-card">
    <form method="POST" action="{{ route('pengaturan.update') }}" class="smk-stack">
        @csrf
        @method('PUT')
        <div>
            <label class="smk-label">Nama sekolah</label>
            <input type="text" name="nama_sekolah" class="smk-input"
                   value="{{ old('nama_sekolah', $pengaturan->nama_sekolah) }}" required>
        </div>
        <div class="smk-row">
            <div>
                <label class="smk-label">Latitude</label>
                <input type="text" name="latitude_sekolah" class="smk-input"
                       value="{{ old('latitude_sekolah', $pengaturan->latitude_sekolah) }}" required>
            </div>
            <div>
                <label class="smk-label">Longitude</label>
                <input type="text" name="longitude_sekolah" class="smk-input"
                       value="{{ old('longitude_sekolah', $pengaturan->longitude_sekolah) }}" required>
            </div>
        </div>
        <div>
            <label class="smk-label">Radius absen (meter)</label>
            <input type="number" name="radius_meter" class="smk-input"
                   value="{{ old('radius_meter', $pengaturan->radius_meter) }}" min="10" max="5000" required>
        </div>
        <div class="smk-row">
            <div>
                <label class="smk-label">Jam masuk</label>
                <input type="time" name="jam_masuk" class="smk-input"
                       value="{{ old('jam_masuk', substr($pengaturan->jam_masuk,0,5)) }}" required>
            </div>
            <div>
                <label class="smk-label">Batas toleransi</label>
                <input type="time" name="batas_toleransi" class="smk-input"
                       value="{{ old('batas_toleransi', substr($pengaturan->batas_toleransi,0,5)) }}" required>
            </div>
            <div>
                <label class="smk-label">Batas wajib alasan</label>
                <input type="time" name="batas_wajib_alasan" class="smk-input"
                       value="{{ old('batas_wajib_alasan', substr($pengaturan->batas_wajib_alasan,0,5)) }}" required>
            </div>
            <div>
                <label class="smk-label">Jam tutup absen</label>
                <input type="time" name="jam_tutup_absen" class="smk-input"
                       value="{{ old('jam_tutup_absen', substr($pengaturan->jam_tutup_absen ?? '10:00:00',0,5)) }}" required>
            </div>
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Simpan pengaturan</button>
    </form>
</div>
@endsection
