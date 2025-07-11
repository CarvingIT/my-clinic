<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logoshortt.png') }}" alt="My Logo" class="h-12 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('messages.Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.index')">
                        {{ __('messages.patients') }}
                    </x-nav-link>

                    @if (Auth::user()->hasRole('admin'))
                        <x-nav-link :href="route('followups.index')" :active="request()->routeIs('followups.index')">
                            {{ __('messages.Ledger') }}
                        </x-nav-link>
                    @endif


                    <!-- Analysis Dropdown -->
                    @if (Auth::user()->hasRole('admin'))
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="inline-flex items-center px-1 py-6 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                {{ __('messages.Analysis') }}
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="absolute z-50 bg-white border border-gray-200 mt-2 rounded-md shadow-lg w-48"
                                x-cloak>
                                <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.index')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('messages.Patient Analysis') }}
                                </x-nav-link>
                                <x-nav-link :href="route('data-analysis.index')" :active="request()->routeIs('data-analysis.index')"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('messages.Followup Analysis') }}
                                </x-nav-link>
                            </div>
                        </div>
                    @endif


                    {{-- @if (Auth::user()->hasRole('admin'))
                    <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.index')">
                        {{ __('messages.Analysis') }}
                    </x-nav-link>
                    @endif --}}

                    {{-- <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.index')">
                        {{ __('messages.Analysis') }}
                    {{-- <x-nav-link :href="route('patient-dues.index')" :active="request()->routeIs('patient-dues.index')">
                        {{ __('messages.Patient with Dues') }}
                    </x-nav-link> --}}

                    <!-- Reports Dropdown -->
                    {{-- <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="inline-flex items-center px-1 py-6 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                            {{ __('messages.Reports') }}
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            class="absolute z-50 bg-white border border-gray-200 mt-2 rounded-md shadow-lg w-48"
                            x-cloak>
                            <x-nav-link :href="route('followups.index')" :active="request()->routeIs('followups.index')"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('messages.Ledger') }}
                            </x-nav-link>
                            <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.index')"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('messages.Analysis') }}
                            </x-nav-link>
                            <x-nav-link :href="route('patient-dues.index')" :active="request()->routeIs('patient-dues.index')"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('messages.Patient with Dues') }}
                            </x-nav-link>
                        </div>
                    </div> --}}


                    {{-- <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('messages.Staff') }}
                    </x-nav-link> --}}

                    @if (Auth::user()->hasRole('admin'))
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                            {{ __('messages.Staff') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.index')">
                        {{ __('messages.Queue') }}
                    </x-nav-link>
                    {{-- <x-nav-link :href="route('data-analysis.index')" :active="request()->routeIs('data-analysis.index')">
                        {{ __('Data Analysis') }}
                    </x-nav-link> --}}
                    {{-- <div x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
                        <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark', darkMode)" class="text-gray-800 dark:text-gray-200">
                            Toggle Dark Mode
                        </button>
                    </div> --}}
                </div>
            </div>



            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('messages.Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
