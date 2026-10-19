@extends('layouts.app')
@section('title', 'Import Siswa')

@section('content')
<h1 class="smk-section-title">Import Siswa</h1>
<p class="smk-section-sub">Upload CSV per kelas. Dari Excel: Simpan sebagai → CSV.</p>

<div class="smk-card smk-card-soft" style="margin-bottom:1rem">
    <strong>Format file</strong>
    <pre style="background:#f8fafc;padding:0.75rem;border-radius:10px;font-size:0.8rem;overflow:auto">nis,nama
2024001010,Budi Setiawan
2024001011,Ani Lestari</pre>
    <p style="margin:0.5rem 0 0;font-size:0.8rem;color:#64748b">Password default: <code>password</code> (wajib ganti saat login).</p>
</div>

<div class="smk-card">
    <form method="POST" action="{{ route('import.siswa.store') }}" enctype="multipart/form-data" class="smk-stack">
        @csrf
        <div>
            <label class="smk-label">Kelas tujuan</label>
            <select name="kelas_id" class="smk-select" required>
                <option value="">Pilih kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" @selected(old('kelas_id')==$k->id)>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="smk-label">File CSV</label>
            <input type="file" name="file" class="smk-input" accept=".csv,.txt" required>
        </div>
        <button type="submit" class="smk-btn smk-btn-primary">Import</button>
        <a href="{{ route('dashboard.guru') }}" class="smk-btn smk-btn-outline">Kembali</a>
    </form>
</div>
@endsection
