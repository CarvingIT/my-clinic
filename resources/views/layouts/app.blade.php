<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">

        {{-- Icons --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

        {{-- alpine js --}}
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Script to disable dark mode --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.documentElement.classList.remove('dark');
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Quill library -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            const marathiToEnglishMapping = {
            '०': '0',
            '१': '1',
            '२': '2',
            '३': '3',
            '४': '4',
            '५': '5',
            '६': '6',
            '७': '7',
            '८': '8',
            '९': '9'
        };

        function convertMarathiToEnglish(input) {
            // Remove spaces first, then replace Marathi digits with English digits
            return input.replace(/\s+/g, '').replace(/[०-९]/g, (match) => marathiToEnglishMapping[match]);
        }

        // Apply only to fields with the 'reverse-transliteration' class
        document.querySelectorAll('.reverse-transliteration').forEach(inputField => {
            inputField.addEventListener('input', function() {
                // Convert Marathi digits and remove spaces
                this.value = convertMarathiToEnglish(this.value);
            });
        });

        </script>
    </body>
</html>
