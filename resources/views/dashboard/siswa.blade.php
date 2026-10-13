<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Siswa | Absensi Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            color: #212529;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #0d6efd;
        }

        .main-container {
            max-width: 1000px;
        }

        .dashboard-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.045);
        }

        .profile-card {
            background: linear-gradient(135deg, #0d6efd, #3399ff);
            color: white;
            border-radius: 18px;
        }

        .profile-label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
        }

        .profile-value {
            font-size: 16px;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
        }

        .btn-attendance {
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
        }

        .menu-link {
            padding: 15px;
            border: none;
            border-radius: 12px !important;
            margin-bottom: 10px;
            background: #f7f9fc;
            transition: 0.2s;
        }

        .menu-link:hover {
            background: #e8f1ff;
            color: #0d6efd;
        }

        .menu-icon {
            font-size: 20px;
            margin-right: 10px;
        }
    </style>
</head>

<body>

{{-- Navbar --}}
<nav class="navbar navbar-dark py-3">
    <div class="container">
        <span class="navbar-brand fw-bold mb-0">
            Absensi Kelas
        </span>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="btn btn-light btn-sm px-3">
                Logout
            </button>
        </form>
    </div>
</nav>

<main class="container main-container py-4 py-md-5">

    {{-- Judul Dashboard --}}
    <div class="mb-4">
        <h3 class="fw-bold mb-1">Dashboard Siswa</h3>

        <p class="text-secondary mb-0">
            Selamat datang di sistem informasi absensi kelas.
        </p>
    </div>

    {{-- Pesan Berhasil --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Informasi Siswa --}}
    <section class="dashboard-card p-3 p-md-4 mb-4">

        <div class="profile-card p-4">

            <p class="mb-1">
                Selamat Datang 👋
            </p>

            <h4 class="fw-bold mb-4">
                {{ $siswa->nama }}
            </h4>

            <div class="row g-3">

                <div class="col-12 col-md-6">
                    <div class="profile-label mb-1">
                        Nomor Induk Siswa
                    </div>

                    <div class="profile-value">
                        {{ $siswa->nis }}
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="profile-label mb-1">
                        Kelas
                    </div>

                    <div class="profile-value">
                        {{ $siswa->kelas->nama_kelas ?? 'Belum ditentukan' }}
                    </div>
                </div>

            </div>

        </div>

    </section>

    {{-- Absensi Hari Ini --}}
    <section class="dashboard-card p-3 p-md-4 mb-4">

        <h4 class="section-title mb-2">
            Absensi Hari Ini
        </h4>

        <p class="text-secondary mb-4">
            Aktifkan GPS dan izinkan akses lokasi untuk melakukan absensi
            di lingkungan sekolah.
        </p>

        <div id="pesanLokasi"
             class="alert d-none"
             role="alert"
             aria-live="polite">
        </div>

        {{-- Form Absensi --}}
        <form id="formAbsensi"
              action="{{ route('absensi.absen') }}"
              method="POST">

            @csrf

            <input type="hidden"
                   name="latitude"
                   id="latitude">

            <input type="hidden"
                   name="longitude"
                   id="longitude">

            <button type="button"
                    id="tombolAbsen"
                    class="btn btn-primary btn-lg w-100 btn-attendance">

                📍 Absen Sekarang

            </button>

        </form>

        <small class="text-secondary d-block mt-3">
            Pastikan izin lokasi browser sudah diaktifkan.
        </small>

    </section>

    {{-- Menu Siswa --}}
    <section class="dashboard-card p-3 p-md-4">

        <h4 class="section-title mb-3">
            Menu Siswa
        </h4>

        <div class="list-group">

            <a href="{{ route('rekap-kelas.index') }}"
               class="btn btn-primary">
                Lihat Kehadiran Teman Sekelas
            </a>

            <a href="{{ route('absensi.riwayat') }}"
               class="list-group-item list-group-item-action menu-link">

                <span class="menu-icon">📋</span>
                Riwayat Kehadiran

            </a>

            <a href="{{ route('pengajuan.index') }}"
               class="list-group-item list-group-item-action menu-link">

                <span class="menu-icon">📝</span>
                Pengajuan Izin atau Sakit

            </a>

            <a href="{{ route('dispensasi.siswa') }}" class="btn btn-info">
                 Informasi Dispensasi
            </a>

            <a href="{{ route('keterlambatan.index') }}"
               class="list-group-item list-group-item-action menu-link mb-0">

                <span class="menu-icon">⏰</span>
                Pengajuan Keterlambatan

            </a>

        </div>

    </section>

    {{-- Footer --}}
    <footer class="text-center text-secondary py-4">
        <small>Sistem Informasi Absensi Kelas</small>
    </footer>

</main>

{{-- Bootstrap JavaScript --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

{{-- JavaScript GPS --}}
<script>
    const tombolAbsen = document.getElementById('tombolAbsen');
    const pesanLokasi = document.getElementById('pesanLokasi');
    const formAbsensi = document.getElementById('formAbsensi');

    tombolAbsen.addEventListener('click', function () {

        if (!navigator.geolocation) {
            tampilkanPesan(
                'Browser tidak mendukung fitur lokasi.',
                'danger'
            );

            return;
        }

        tombolAbsen.disabled = true;
        tombolAbsen.textContent = 'Mengambil lokasi...';

        tampilkanPesan(
            'Sedang mengambil lokasi GPS. Tunggu sebentar...',
            'info'
        );

        navigator.geolocation.getCurrentPosition(

            function (posisi) {

                document.getElementById('latitude').value =
                    posisi.coords.latitude;

                document.getElementById('longitude').value =
                    posisi.coords.longitude;

                tampilkanPesan(
                    'Lokasi berhasil didapatkan. Mengirim absensi...',
                    'success'
                );

                formAbsensi.submit();
            },

            function (error) {

                let pesan = 'Gagal mendapatkan lokasi. Coba lagi.';

                if (error.code === 1) {
                    pesan =
                        'Izin lokasi ditolak. Izinkan browser mengakses lokasi.';
                } else if (error.code === 2) {
                    pesan =
                        'Lokasi tidak tersedia. Periksa GPS perangkat.';
                } else if (error.code === 3) {
                    pesan =
                        'Permintaan lokasi terlalu lama. Coba lagi.';
                }

                tampilkanPesan(pesan, 'danger');

                tombolAbsen.disabled = false;
                tombolAbsen.textContent = '📍 Absen Sekarang';
            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });

    function tampilkanPesan(pesan, jenis) {
        pesanLokasi.className = 'alert alert-' + jenis;
        pesanLokasi.textContent = pesan;
    }
</script>

</body>
</html>