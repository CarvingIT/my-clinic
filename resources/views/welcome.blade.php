<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Clinic</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Styles -->
    @vite('resources/css/app.css')
    <!-- Tailwind CSS -->
    @vite('resources/js/app.js')
</head>

<body class="font-sans antialiased bg-gradient-to-r from-blue-100 to-indigo-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white bg-opacity-80 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-gray-200">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-indigo-700">My Clinic</h1>
                <p class="mt-2 text-gray-500 font-medium">Welcome, please log in to access the system.</p>
            </div>
              <div class="mt-4">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                  <form method="POST" action="{{ route('login') }}">
                      @csrf

                          <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" class="block text-gray-700 font-medium mb-2"/>
                              <x-text-input id="email" class="block mt-1 w-full border border-gray-300 rounded-2xl shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300 py-1 px-2 " type="email" name="email" :value="old('email')" required autofocus autocomplete="username"/>
                             <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500"/>
                        </div>
                          <div class="mb-6">
                               <x-input-label for="password" :value="__('Password')" class="block text-gray-700 font-medium mb-2"/>
                              <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-2xl shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300 py-1 px-2" type="password" name="password" required autocomplete="current-password"/>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500"/>
                          </div>
                        <div class="flex items-center justify-between mt-6">
                            @if (Route::has('password.request'))
                            <a class="inline-block text-sm align-baseline font-bold text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring focus:ring-indigo-200 rounded-md" href="{{ route('password.request') }}">
                                  {{ __('Forgot your password?') }}
                                 </a>
                            @endif
                            <x-primary-button class="ms-3 py-2 px-4 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring focus:ring-indigo-200 focus:outline-none transition duration-200">
                               {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                   </form>
           </div>
          @if (Route::has('register'))
                <div class="mt-8 text-center text-gray-600">
                      Don't have an account? Contact admin.
                </div>
          @endif
      </div>
    </div>
</body>
</html>
