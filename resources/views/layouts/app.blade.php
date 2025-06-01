<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
        <title>My Clinic</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.png') }}">


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
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

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

        <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>


        <script>
            tinymce.init({
                selector: '.tinymce-editor',
                plugins: 'lists link table textcolor',
                toolbar: 'undo redo | bold italic underline | bullist numlist | forecolor backcolor',
                menubar: false,
                branding: false,
                statusbar: false,
                height: 200,
                forced_root_block: false,
                content_style: `
                                body {
                                line-height: 1.4 !important;
                                margin: 0;
                                padding: 15px;
                                font-size: 17px;
                                }
                                p {
                                margin: 0 !important;
                                }
                            `,

            });
        </script>
        <script>
            tinymce.init({
                selector: '.tinymce-editor002',
                plugins: 'lists link table textcolor',
                toolbar: 'undo redo | bold italic underline | bullist numlist | forecolor backcolor',
                menubar: false,
                branding: false,
                statusbar: false,
                height: 100,

            });
        </script>
        <script>
            tinymce.init({
                selector: '.tinymce-editor003',
                plugins: 'lists link table textcolor',
                toolbar: 'undo redo | bold italic underline | bullist numlist | forecolor backcolor',
                menubar: false,
                branding: false,
                statusbar: false,
                height: 150,

            });
        </script>

    </body>
</html>
