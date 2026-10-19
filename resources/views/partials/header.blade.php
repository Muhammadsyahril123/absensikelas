<header class="smk-header">
    <div class="smk-header-inner">
        <a href="{{ url('/dashboard') }}" class="smk-brand">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo">
            <div>
                <strong>Absensi Kelas</strong>
                <span>SMK Informatika Sumedang</span>
            </div>
        </a>
        @auth
        <div class="smk-header-actions">
            <span class="smk-user">{{ auth()->user()->name ?? auth()->user()->username }}</span>
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>
        @endauth
    </div>
</header>
