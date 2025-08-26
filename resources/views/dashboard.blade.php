<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ date('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 dashboard-container">
            <!-- Flash Messages -->
            <div class="max-w-7xl mx-auto mb-4">
                @if (session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm mb-4 relative">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove();" class="absolute top-2 right-3 text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-sm mb-4 relative">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove();" class="absolute top-2 right-3 text-green-500 hover:text-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Welcome Section -->
            <div class="mb-8">
                <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl border border-blue-100 overflow-hidden shadow-lg backdrop-blur-sm relative">
                    <!-- Decorative background pattern -->
                    {{-- <div class="absolute inset-0 opacity-5">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                            <defs>
                                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100" height="100" fill="url(#grid)" />
                        </svg>
                    </div> --}}
                    <div class="flex flex-col md:flex-row items-center justify-between p-8 md:p-10 gap-8 relative z-10">
                        <!-- Left: Greeting and User Info -->
                        <div class="flex items-center gap-6 w-full md:w-2/3">
                            <!-- User Avatar/Icon with animation -->
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center border-2 border-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-800 mb-2 leading-tight">
                                    Good {{ date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') }},
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                                        @php
                                            // Use hasRole if available, else fallback to roles array
                                            $isDoctor = false;
                                            if (method_exists(Auth::user(), 'hasRole')) {
                                                $isDoctor = Auth::user()->hasRole('doctor');
                                            } elseif(property_exists(Auth::user(), 'roles')) {
                                                $roles = Auth::user()->roles;
                                                if (is_array($roles) && in_array('doctor', $roles)) {
                                                    $isDoctor = true;
                                                }
                                            } elseif(property_exists(Auth::user(), 'role')) {
                                                $role = Auth::user()->role;
                                                if ((is_array($role) && in_array('doctor', $role)) || $role === 'doctor') {
                                                    $isDoctor = true;
                                                }
                                            }
                                        @endphp
                                        @if($isDoctor)
                                            Dr.
                                        @endif
                                        {{ Auth::user()->name }}
                                    </span>
                                </h1>
                                <div class="flex flex-wrap gap-3 items-center text-gray-700 text-sm mb-2">
                                    <span class="bg-white/80 backdrop-blur-sm border border-blue-200 rounded-full px-3 py-1 shadow-sm">
                                        <i class="fas fa-user-tie mr-1 text-blue-500"></i>
                                        @php
                                            $user = Auth::user();
                                            $roleLabel = 'User';
                                            if (method_exists($user, 'hasRole')) {
                                                if ($user->hasRole('admin')) {
                                                    $roleLabel = 'Admin';
                                                } elseif ($user->hasRole('doctor')) {
                                                    $roleLabel = 'Doctor';
                                                } elseif ($user->hasRole('staff')) {
                                                    $roleLabel = 'Staff';
                                                }
                                            }
                                        @endphp
                                        {{ $roleLabel }}
                                    </span>
                                    <span class="bg-white/80 backdrop-blur-sm border border-blue-200 rounded-full px-3 py-1 shadow-sm">
                                        <i class="fas fa-building mr-1 text-green-500"></i>{{ session('branch_name') }}
                                    </span>
                                    <span class="bg-white/80 backdrop-blur-sm border border-blue-200 rounded-full px-3 py-1 shadow-sm">
                                        <i class="fas fa-calendar mr-1 text-purple-500"></i>{{ date('d F Y') }}
                                    </span>
                                </div>
                                <p class="text-gray-600 font-medium">Ready to make a difference today!</p>
                            </div>
                        </div>
                        <!-- Right: Clinic Logo/Name with enhanced styling -->
                        {{-- <div class="hidden md:flex flex-col items-center justify-center w-1/3 relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent rounded-2xl"></div>
                            <div class="relative z-10 flex items-center justify-center w-full h-full p-4">
                                <div class="text-center">
                                    <img src="{{ asset('images/logoshortt.png') }}" alt="Clinic Logo" class="h-24 md:h-28 w-auto object-contain mb-2 drop-shadow-lg" />
                                    <div class="text-lg font-bold text-gray-700 tracking-wide">जातेगांवकर चिकित्सालय</div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    @php
                        $queue_count = \App\Models\Queue::whereDate('in_queue_at', today())->count();
                        $recent_patients = \App\Models\FollowUp::with('patient')
                            ->whereDate('created_at', today())
                            ->count();
                        $new_patients_today = \App\Models\Patient::whereDate('created_at', today())->count();
                        $revenue_today = \App\Models\FollowUp::whereDate('created_at', today())->sum('amount_paid');
                        $recurring_patients_today = \App\Models\FollowUp::whereDate('created_at', today())
                                ->whereHas('patient', function($q) {
                                    $q->whereDate('created_at', '<', today());
                                })
                                ->distinct('patient_id')
                                ->count('patient_id');
                    @endphp

                    <!-- Enhanced Stats Row with better animations and interactions -->
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 px-8 pb-8 relative z-10">
                        <div class="group flex items-center gap-3 bg-white/90 backdrop-blur-sm border border-blue-100 rounded-xl px-5 py-3 min-w-[140px] shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-500 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $recent_patients }}</div>
                                <div class="text-xs font-medium text-gray-600">Today's Patients</div>
                            </div>
                        </div>
                        <div class="group flex items-center gap-3 bg-white/90 backdrop-blur-sm border border-purple-100 rounded-xl px-5 py-3 min-w-[140px] shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-purple-500 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800 group-hover:text-purple-600 transition-colors">{{ $queue_count }}</div>
                                <div class="text-xs font-medium text-gray-600">In Queue</div>
                            </div>
                        </div>
                        <div class="group flex items-center gap-3 bg-white/90 backdrop-blur-sm border border-green-100 rounded-xl px-5 py-3 min-w-[140px] shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-500 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800 group-hover:text-green-600 transition-colors">{{ $new_patients_today }}</div>
                                <div class="text-xs font-medium text-gray-600">New Patients</div>
                            </div>
                        </div>
                        <!-- Recurring/Existing Patients Today Card -->

                        <div class="group flex items-center gap-3 bg-white/90 backdrop-blur-sm border border-blue-100 rounded-xl px-5 py-3 min-w-[140px] shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-500 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $recurring_patients_today }}</div>
                                <div class="text-xs font-medium text-gray-600">Existing Patients</div>
                            </div>
                        </div>
                        {{-- <div class="group flex items-center gap-3 bg-white/90 backdrop-blur-sm border border-amber-100 rounded-xl px-5 py-3 min-w-[140px] shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-500 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-gray-800 group-hover:text-amber-600 transition-colors">₹{{ number_format($revenue_today) }}</div>
                                <div class="text-xs font-medium text-gray-600">Today's Revenue</div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Enhanced Key Metrics Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @php
                    $total_patients = \App\Models\Patient::count();
                    $new_patients_this_month = \App\Models\Patient::whereMonth('created_at', now()->month)->count();
                    $follow_ups_this_month = \App\Models\FollowUp::whereMonth('created_at', now()->month)->count();
                    $total_revenue = \App\Models\FollowUp::sum('amount_paid');

                    // Calculate growth percentages (mock data for now)
                    $patient_growth = 12.5;
                    $new_patient_growth = 8.3;
                    $followup_growth = 15.2;
                    $revenue_growth = 22.1;
                @endphp

                <!-- Total Patients Card -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100 metric-card" data-interactive>
                    <div class="p-6 relative">
                        <!-- Background decoration -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-bl-3xl opacity-50"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                {{-- <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +{{ $patient_growth }}%
                                </span> --}}
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-2">{{ __('Total Patients') }}</p>
                            <p class="text-4xl font-bold text-gray-800 mb-2" data-metric="total_patients">{{ number_format($total_patients) }}</p>
                            <p class="text-gray-500 text-xs">Lifetime registrations</p>
                        </div>
                    </div>
                </div>                <!-- New Patients Card -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-bl-3xl opacity-50"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                {{-- <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +{{ $new_patient_growth }}%
                                </span> --}}
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-2">{{ __('New Patients') }}</p>
                            <p class="text-4xl font-bold text-gray-800 mb-2">{{ number_format($new_patients_this_month) }}</p>
                            <p class="text-gray-500 text-xs">This month</p>
                        </div>
                    </div>
                </div>

                <!-- Follow-ups Card -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-200 rounded-bl-3xl opacity-50"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="text-right">
                                {{-- <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    +{{ $followup_growth }}%
                                </span> --}}
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-2">{{ __('Follow-ups') }}</p>
                            <p class="text-4xl font-bold text-gray-800 mb-2">{{ number_format($follow_ups_this_month) }}</p>
                            <p class="text-gray-500 text-xs">This month</p>
                        </div>
                    </div>
                </div>

                <!-- Daily Schedule/Patient Queue Card -->
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-bl-3xl opacity-50"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ date('H:i') }}
                                </span>
                            </div>
                        </div>

                        @php
                            // Get today's queue with appointment times
                            $todays_queue = \App\Models\Queue::with('patient')
                                ->whereDate('in_queue_at', today())
                                ->orderBy('in_queue_at')
                                ->limit(3)
                                ->get();

                            $next_patient = $todays_queue->first();
                        @endphp

                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-2">{{ __('Next Patient in the Queue') }}</p>

                            @if($next_patient && $next_patient->patient)
                                <div class="mb-2">
                                    <div class="flex items-end mb-1">
                                        <p class="text-4xl font-bold text-gray-800 leading-none">{{ $next_patient->in_queue_at->format('h:i') }}</p>
                                        <p class="text-xs text-gray-500 ml-1 mb-1">{{ $next_patient->in_queue_at->format('A') }}</p>
                                    </div>
                                    <p class="text-sm font-medium text-indigo-600 truncate">{{ Str::limit($next_patient->patient->name, 15) }}</p>
                                </div>
                            @else
                                <div class="mb-2">
                                    <div class="flex items-end mb-1">
                                        <p class="text-4xl font-bold text-gray-800 leading-none">{{ date('h:i') }}</p>
                                        <p class="text-xs text-gray-500 ml-1 mb-1">{{ date('A') }}</p>
                                    </div>
                                    <p class="text-sm text-gray-500">No appointments</p>
                                </div>
                            @endif

                            {{-- <p class="text-gray-500 text-xs">{{ $queue_count }} patients in queue today</p> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Enhanced Today's Queue -->
                <div class="lg:col-span-2 bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                    <div class="border-b border-gray-100 px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                                    <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('Today\'s Queue') }}
                                </h3>
                                <p class="text-gray-600 text-sm mt-1">Current patient queue status</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-purple-600">{{ $queue_count }}</div>
                                <div class="text-xs text-gray-500">Patients waiting</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $queue_entries = \App\Models\Queue::with('patient')
                                ->whereDate('in_queue_at', today())
                                ->orderBy('in_queue_at')
                                ->take(3)
                                ->get();
                        @endphp

                        @if($queue_entries->count() > 0)
                            <div class="space-y-3">
                                @foreach($queue_entries as $index => $entry)
                                    <div class="flex items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200 border border-gray-200">
                                        <!-- Queue Position -->
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm mr-4">
                                            {{ $index + 1 }}
                                        </div>

                                        <!-- Patient Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    @if ($entry->patient)
                                                        <a href="{{ route('patients.show', $entry->patient->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-lg transition-colors">
                                                            {{ $entry->patient->name }}
                                                        </a>
                                                        <p class="text-gray-500 text-sm">ID: {{ $entry->patient->patient_id ?? 'N/A' }}</p>
                                                    @else
                                                        <span class="text-gray-400 font-medium">{{ __('Unknown Patient') }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-right flex flex-col items-end gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <form method="POST" action="{{ route('queue.in', $entry->id) }}" onsubmit="return confirm('Are you sure you want to mark this patient as in?')">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-800 font-medium px-2 py-0.5 rounded bg-green-100 hover:bg-green-200 transition-colors text-xs flex items-center" title="Mark as In">
                                                                <i class="fas fa-user-check"></i>
                                                            </button>
                                                        </form>
                                                        <span class="text-sm font-medium text-gray-800">{{ $entry->in_queue_at->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mb-2">
                                                        {{ $entry->in_queue_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Indicator -->
                                        <div class="flex-shrink-0 ml-4">
                                            <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="text-sm text-gray-600">
                                    Showing {{ $queue_entries->count() }} of {{ $queue_count }} patients
                                </div>
                                <a href="{{ route('queue.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    {{ __('Manage Queue') }}
                                    <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No patients in queue</h4>
                                <p class="text-gray-500 mb-4">{{ __('No patients in queue today. Great job keeping up!') }}</p>
                                <a href="{{ route('patients.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    {{ __('Add Patient') }}
                                    <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                                <!-- Enhanced Quick Actions -->
                <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
                    <div class="border-b border-gray-100 px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            {{ __('Quick Actions') }}
                        </h3>
                        <p class="text-gray-600 text-sm mt-1">Streamline your daily tasks</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4">
                            <a href="{{ route('patients.create') }}" class="group flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-blue-100">
                                <div class="flex-shrink-0 p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-blue-800 text-lg group-hover:text-blue-900 transition-colors">{{ __('Add New Patient') }}</h4>
                                    <p class="text-blue-600 text-sm">Register new patient in the system</p>
                                </div>
                                <div class="flex-shrink-0 text-blue-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>

                            <a href="{{ route('patients.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-indigo-100">
                                <div class="flex-shrink-0 p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-indigo-800 text-lg group-hover:text-indigo-900 transition-colors">{{ __('All Patients') }}</h4>
                                    <p class="text-indigo-600 text-sm">Manage all patient records</p>
                                </div>
                                <div class="flex-shrink-0 text-indigo-400 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>

                            {{-- <a href="{{ route('queue.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 hover:from-purple-100 hover:to-pink-100 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-purple-100">
                                <div class="flex-shrink-0 p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-purple-800 text-lg group-hover:text-purple-900 transition-colors">{{ __('Queue Management') }}</h4>
                                    <p class="text-purple-600 text-sm">Manage patient queue</p>
                                </div>
                                <div class="flex-shrink-0 text-purple-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a> --}}

                            <a href="{{ route('users.index') }}" class="group flex items-center p-4 bg-gradient-to-r from-green-50 to-teal-50 hover:from-green-100 hover:to-teal-100 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg border border-green-100">
                                <div class="flex-shrink-0 p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl mr-4 group-hover:scale-110 transition-transform duration-300 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-green-800 text-lg group-hover:text-green-900 transition-colors">{{ __('Staff Management') }}</h4>
                                    <p class="text-green-600 text-sm">Manage staff members</p>
                                </div>
                                <div class="flex-shrink-0 text-green-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        </div>

                        <!-- Additional Quick Stats -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">System Status</span>
                                <span class="flex items-center text-green-600 font-medium">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                    All systems operational
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Patient Activity -->
            <div class="mt-6">
                <div class="bg-white shadow-md rounded-xl overflow-hidden">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Recent Patient Activity') }}</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $recent_follow_ups = \App\Models\FollowUp::with(['patient', 'doctor'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp

                        @if($recent_follow_ups->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                                    <thead>
                                        <tr>
                                            <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tl-xl">{{ __('Date & Time') }}</th>
                                            <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Patient') }}</th>
                                            <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Doctor') }}</th>
                                            <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Nadi') }}</th>
                                            <th class="px-5 py-3 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tr-xl">{{ __('Chikitsa') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($recent_follow_ups as $followUp)
                                            @php
                                                $info = is_array($followUp->check_up_info) ? $followUp->check_up_info : json_decode($followUp->check_up_info, true);
                                                $nadi = $info['nadi'] ?? '';
                                                $chikitsa = $info['chikitsa'] ?? '';
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-700 font-medium">
                                                    {{ $followUp->created_at->format('d M, Y - h:i A') }}
                                                </td>
                                                <td class="px-5 py-3 whitespace-nowrap">
                                                    @if($followUp->patient)
                                                        <a href="{{ route('patients.show', $followUp->patient->id) }}" class="text-blue-700 hover:text-blue-900 font-semibold">
                                                            {{ $followUp->patient->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400 font-medium">{{ __('Unknown Patient') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-700 font-medium">
                                                    {{ $followUp->doctor ? $followUp->doctor->name : 'N/A' }}
                                                </td>
                                                <td class="px-5 py-3 text-sm font-medium align-middle" style="min-width:200px;max-width:350px;white-space:nowrap;">
                                                    <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-800" style="white-space:nowrap;">{!! $nadi !!}</span>
                                                </td>
                                                <td class="px-5 py-3 text-sm font-medium align-middle" style="min-width:200px;max-width:350px;white-space:nowrap;">
                                                    <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-800" style="white-space:nowrap;">{!! $chikitsa !!}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>{{ __('No recent patient activity.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
