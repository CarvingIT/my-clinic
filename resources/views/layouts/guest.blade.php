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

        <script>
            (function() {
                var key = 'clinic_text_scale';
                var allowed = ['100', '110', '120', '130'];
                var scale = '100';

                try {
                    var saved = localStorage.getItem(key);
                    if (saved && allowed.includes(saved)) {
                        scale = saved;
                    }
                } catch (e) {
                    scale = '100';
                }

                document.documentElement.setAttribute('data-text-scale', scale);
                document.documentElement.style.setProperty('--app-text-scale-factor', String(Number(scale) / 100));
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-gray-900 antialiased bg-slate-50 bg-gradient-to-br from-indigo-50 via-white to-blue-50 overflow-y-auto">
        <div class="min-h-screen flex flex-col justify-center items-center p-4">
            <div class="text-center transition-all duration-500 transform hover:-translate-y-1">
                <a href="/" class="inline-block relative">
                    <div class="absolute inset-0 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
                    <img src="{{ asset('images/logofullt.png') }}" alt="Clinic Logo"
                        class="relative h-28 sm:h-32 object-contain filter drop-shadow-xl transition-transform duration-300 hover:scale-105 mx-auto" />
                </a>
                <h1 class="mt-4 text-2xl sm:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-700 to-indigo-700 tracking-wider drop-shadow-sm">
                    {{ config('app.clinic_name', 'My Clinic') }}
                </h1>
                <p class="text-xs sm:text-sm text-indigo-400 mt-1 tracking-widest uppercase font-semibold opacity-80">Secure Portal Access</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 sm:px-8 sm:py-8 bg-white/90 backdrop-blur-xl shadow-2xl shadow-blue-900/10 sm:rounded-2xl border border-white/60">
                {{ $slot }}
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-400 font-medium">
                &copy; {{ date('Y') }} {{ config('app.clinic_name', 'My Clinic') }}. All rights reserved.
            </div>
        </div>
    </body>

</html>
