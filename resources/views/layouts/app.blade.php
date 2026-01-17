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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

    {{-- For Grid Animation --}}
    <script src="https://cdn.jsdelivr.net/npm/animate-css-grid@1.1.0/dist/main.js"></script>

    {{-- For Drag and Drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>



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
            // First convert all Marathi digits to English digits
            let result = input.replace(/[०-९]/g, (match) => marathiToEnglishMapping[match]);

            // Then remove spaces between English digits to create proper numbers
            result = result.replace(/(\d)\s+(\d)/g, '$1$2');

            // Remove spaces before decimal points
            result = result.replace(/\s+\./g, '.');

            return result;
        }

        // Apply Marathi to English conversion to all text and number input fields
        document.querySelectorAll('input[type="text"], input[type="number"], input[type="tel"], input[type="email"]').forEach(inputField => {
            // Skip fields that explicitly don't want this conversion
            if (inputField.classList.contains('no-transliteration')) {
                return;
            }

            let timeoutId;
            let lastValue = inputField.value;

            // Function to apply conversion
            const applyConversion = () => {
                const currentValue = inputField.value;
                if (currentValue !== lastValue) {
                    const converted = convertMarathiToEnglish(currentValue);
                    if (converted !== currentValue) {
                        inputField.value = converted;
                        lastValue = converted;
                        // Dispatch custom event to notify other scripts of the conversion
                        inputField.dispatchEvent(new CustomEvent('marathiConverted', { detail: { original: currentValue, converted: converted } }));
                    } else {
                        lastValue = currentValue;
                    }
                }
            };

            // Listen to multiple events
            ['input', 'change', 'blur', 'focus', 'keyup', 'keydown'].forEach(eventType => {
                inputField.addEventListener(eventType, () => {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(applyConversion, 50);
                });
            });

            // Also listen for composition end (for IME like Google Input Tools)
            inputField.addEventListener('compositionend', () => {
                setTimeout(applyConversion, 10);
            });

            // Periodic check for changes (fallback)
            setInterval(() => {
                applyConversion();
            }, 500);
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
            statusbar: true,
            elementpath: false,
            height: 200,
            resize: true,
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
            statusbar: true,
            elementpath: false,
            height: 150,

        });
    </script>
    <script>
        tinymce.init({
            selector: '.tinymce-editor003',
            plugins: 'lists link table textcolor',
            toolbar: 'undo redo | bold italic underline | bullist numlist | forecolor backcolor',
            menubar: false,
            branding: false,
            statusbar: true,
            elementpath: false,
            height: 150,

        });
    </script>

    <!-- Dashboard Enhancements -->
    <script src="{{ asset('resources/js/dashboard-enhancements.js') }}"></script>

    <!-- Session Expiry Warning Modal -->
    <div id="session-expiry-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Session Expiring Soon</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Your session will expire in <span id="countdown">10</span> minutes. Would you like to extend it?
                    </p>
                </div>
                <div class="flex items-center px-4 py-3">
                    <button id="extend-session-btn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Extend Session
                    </button>
                    <button id="logout-btn" class="ml-3 px-4 py-2 bg-gray-300 text-gray-900 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Logout Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Session expiry warning script
        let sessionExpiryModal = document.getElementById('session-expiry-modal');
        let countdownElement = document.getElementById('countdown');
        let extendBtn = document.getElementById('extend-session-btn');
        let logoutBtn = document.getElementById('logout-btn');
        let countdownInterval;
        let timeLeft = 10 * 60; // 10 minutes in seconds

        function showSessionExpiryModal() {
            sessionExpiryModal.classList.remove('hidden');
            countdownInterval = setInterval(() => {
                timeLeft--;
                countdownElement.textContent = Math.ceil(timeLeft / 60);
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = '/logout';
                }
            }, 1000);
        }

        extendBtn.addEventListener('click', function() {
            axios.post('/extend-session', {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }).then(response => {
                if (response.data.status === 'extended') {
                    sessionExpiryModal.classList.add('hidden');
                    clearInterval(countdownInterval);
                    // Reset for next warning
                    timeLeft = 10 * 60;
                    // Reset the timeout for next warning
                    setTimeout(showSessionExpiryModal, (100 * 60 * 1000)); // Warn again in 100 minutes
                }
            }).catch(error => {
                console.error('Error extending session:', error);
            });
        });

        logoutBtn.addEventListener('click', function() {
            document.getElementById('logout-form').submit();
        });

        // Show modal 10 minutes before session expires (session lifetime is 120 minutes)
        setTimeout(showSessionExpiryModal, (110 * 60 * 1000));
    </script>

</body>

</html>
