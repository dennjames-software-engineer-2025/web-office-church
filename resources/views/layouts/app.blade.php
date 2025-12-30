<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gray-100">

    {{-- SIDEBAR FIXED --}}
    @include('layouts.navigation')

    {{-- CONTENT WRAPPER --}}
    <div class="ml-64 min-h-screen flex flex-col">

        {{-- HEADER --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

    </div>

    <script>
    document.querySelectorAll("[data-modal-toggle]").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-modal-target");
            document.getElementById(id).classList.remove("hidden");
        });
    });

    document.querySelectorAll("[data-modal-hide]").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.getAttribute("data-modal-hide");
            document.getElementById(id).classList.add("hidden");
        });
    });
    </script>

    {{-- SweetAlert Notif --}}
    @if(session('status'))
    <script>
    Swal.fire({
        icon: 'success',
        title: "{{ session('status') }}",
        timer: 2000,
        showConfirmButton: false
    });
    </script>
    @endif

    @if(session('error'))
    <script>
    Swal.fire({
        icon: 'error',
        title: "Terjadi Kesalahan",
        text: "{{ session('error') }}",
    });
    </script>
    @endif

</body>

</html>
