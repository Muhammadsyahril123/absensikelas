@extends('layouts.app')
@section('title', 'Ganti Kata Sandi')
@section('content')
<div class="smk-card" style="max-width:400px;margin:0 auto">
<h1 class="smk-title">Ganti kata sandi</h1>
<p class="smk-sub">Wajib diganti sebelum menggunakan sistem.</p>
<form method="POST" action="{{ route('password.update') }}" class="smk-stack">
@csrf
<div>
<label class="smk-label">Kata sandi baru</label>
<input type="password" name="password" class="smk-input" required>
</div>
<div>
<label class="smk-label">Ulangi kata sandi</label>
<input type="password" name="password_confirmation" class="smk-input" required>
</div>
<button type="submit" class="smk-btn smk-btn-block">Simpan</button>
</form>
</div>
@endsection
