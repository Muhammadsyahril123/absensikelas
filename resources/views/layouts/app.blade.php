<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi Kelas') — SMK Informatika Sumedang</title>
    <link rel="icon" href="{{ asset('images/logo-smk.png') }}">
    <link href="{{ asset('css/smk-ui.css') }}" rel="stylesheet">
</head>
<body>
    @include('partials.header')
    <main class="smk-main">
        @if(session('success'))
            <div class="smk-alert smk-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="smk-alert smk-alert-danger">{{ session('error') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="smk-alert smk-alert-danger">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        @yield('content')
    </main>
    @include('partials.footer')
    <script src="{{ asset('js/smk-ui.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
