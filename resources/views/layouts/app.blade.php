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
    <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">

    {{-- Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">




    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/input_tools.js']) --}}

    {{-- tanslatiration


    <script src="https://www.google.com/js/api/client/platform.js?onload=onLoad&libraries=transliteration"></script>

    <script>
        // google.load("elements", "1", {
        //     packages: "transliteration"
        // });

        function onLoad() {
            var options = {
                sourceLanguage: google.elements.transliteration.LanguageCode.ENGLISH, // Source language
                destinationLanguage: [google.elements.transliteration.LanguageCode
                    .HINDI
                ], // Target language(s) -  Add more as needed
                shortcutKey: 'ctrl+g', // Optional shortcut key
                transliterationEnabled: true // Enable transliteration by default
            };

            var control =
                new google.elements.transliteration.TransliterationControl(options);

            // Get the IDs of your text input fields from your Laravel Blade template
            var ids = ["transliterate_text", "nadiInput"]; // Replace with your actual field IDs

            control.makeTransliteratable(ids);

            // Optional: Add a dropdown or UI element to select languages dynamically
            var inputToolDropDown = document.createElement("select");
            // ... (code to populate the dropdown with supported languages) ...

            // Example: Handling language selection (you'll need to implement the dropdown population)
            inputToolDropDown.addEventListener('change', function() {
                var selectedLanguage = this.value;
                options.destinationLanguage = [selectedLanguage]; // Set the new language
                control.setOptions(options); // Update the transliteration control
            });

            // Add the dropdown to your page (e.g., in a div with a specific ID)
            var languageSelectContainer = document.getElementById("language-select"); // Replace with your container ID
            languageSelectContainer.appendChild(inputToolDropDown);

        }

        // google.setOnLoadCallback(onLoad);
    </script> --}}

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


    {{-- <script src="http://www.google.com/js/api/client/platform.js?libraries=transliteration"></script> --}}
    <script src="{{ (app()->environment('local')) ? 'http' : 'https' }}://www.google.com/js/api/client/platform.js?onload=googleApiLoaded&libraries=transliteration"></script>
    <script src="{{ asset('js/input_tools.js') }}"></script>
</body>

</html>
