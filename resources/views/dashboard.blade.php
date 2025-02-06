<x-app-layout>
    <div class="relative py-20 bg-gradient-to-b from-indigo-50 via-gray-100 to-white overflow-hidden">
        <div class="relative px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center">
                <h1 class="text-5xl font-extrabold text-indigo-800 tracking-tight sm:text-6xl mb-6">
                    क्लिनिक
                </h1>
                <p class="text-lg sm:text-lg text-gray-700 font-semibold">
                    स्वागत आहे,
                    <span class="font-bold text-indigo-600">{{ Auth::user()->name }}</span>!
                </p>
                <p class="text-base sm:text-base text-gray-700 font-semibold pt-2">
                    {{__('messages.Branch')}}: <span class="font-bold text-indigo-600">{{ session('branch_name') }}</span>
                </p>
            </div>

            <!-- Main Content Section -->
            <div class="max-w-6xl mx-auto mt-12">
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                    <div class="p-8 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <!-- Patients Section -->
                            <a href="{{ route('patients.index') }}"
                               class="group bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl p-8 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-2xl font-semibold group-hover:text-indigo-200">
                                        {{ __('messages.patient_details') }}
                                    </h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <p class="text-lg text-indigo-100">
                                    रुग्णांचे व्यवस्थापन करा, शोधा आणि नवीन प्रोफाइल तयार करा.
                                </p>
                            </a>

                            <!-- Users Section -->
                            <a href="{{ route('users.index') }}"
                               class="group bg-teal-500 hover:bg-teal-600 text-white rounded-xl p-8 transition duration-300 ease-in-out transform hover:-translate-y-1 shadow-md">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-2xl font-semibold group-hover:text-teal-200">
                                        {{ __('messages.Staff') }}
                                    </h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.995 8.995 0 0112 21a8.995 8.995 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg text-teal-100">
                                    प्रणाली वापरकर्त्यांचे व्यवस्थापन करा, प्रोफाइल तयार करा आणि संपादित करा.
                                </p>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
