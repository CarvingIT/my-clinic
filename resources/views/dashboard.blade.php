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

    <div class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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


            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-100 to-indigo-100 rounded-2xl border border-blue-200 overflow-hidden">
                    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 gap-6">
                        <!-- Left: Greeting and User Info -->
                        <div class="flex items-center gap-4 w-full md:w-2/3">
                            <!-- User Avatar/Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center border border-blue-300">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-2xl font-semibold text-blue-900 mb-1">
                                    Welcome, <span class="text-blue-600">{{ Auth::user()->name }}</span>
                                </h1>
                                <div class="flex flex-wrap gap-2 items-center text-blue-700 text-sm mb-2">
                                    <span class="bg-blue-100 border border-blue-200 rounded px-2 py-0.5">{{ Auth::user()->role ?? 'User' }}</span>
                                    <span class="bg-blue-100 border border-blue-200 rounded px-2 py-0.5">{{ session('branch_name') }}</span>
                                    <span class="bg-blue-100 border border-blue-200 rounded px-2 py-0.5">{{ date('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Right: Clinic Logo/Name -->
                        <div class="hidden md:flex flex-col items-center justify-center w-1/3">
                            <div class="flex items-center justify-center w-full h-full" style="min-height: 100%">
                                <img src="{{ asset('images/logoshortt.png') }}" alt="Clinic Logo" class="h-20 md:h-24 w-auto object-contain" />
                            </div>
                        </div>
                    </div>
                    <!-- Stats Row -->
                    @php
                        $queue_count = \App\Models\Queue::whereDate('in_queue_at', today())->count();
                        $recent_patients = \App\Models\FollowUp::with('patient')
                            ->whereDate('created_at', today())
                            ->count();
                        $new_patients_today = \App\Models\Patient::whereDate('created_at', today())->count();
                        $revenue_today = \App\Models\FollowUp::whereDate('created_at', today())->sum('amount_paid');
                    @endphp
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 px-6 pb-6">
                        <div class="flex items-center gap-2 bg-white border border-blue-100 rounded-lg px-4 py-2 min-w-[120px]">
                            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-blue-900">{{ $recent_patients }}</div>
                                <div class="text-xs text-blue-600">Today's Patients</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white border border-blue-100 rounded-lg px-4 py-2 min-w-[120px]">
                            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-blue-900">{{ $queue_count }}</div>
                                <div class="text-xs text-blue-600">In Queue</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white border border-blue-100 rounded-lg px-4 py-2 min-w-[120px]">
                            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-blue-900">{{ $new_patients_today }}</div>
                                <div class="text-xs text-blue-600">New Patients</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white border border-blue-100 rounded-lg px-4 py-2 min-w-[120px]">
                            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100">
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-base font-semibold text-blue-900">₹{{ number_format($revenue_today) }}</div>
                                <div class="text-xs text-blue-600">Today's Revenue</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end flex-1">
                            <span class="text-lg font-bold text-blue-700 tracking-wide">जातेगांवकर चिकित्सालय</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @php
                    $total_patients = \App\Models\Patient::count();
                    $new_patients_this_month = \App\Models\Patient::whereMonth('created_at', now()->month)->count();
                    $follow_ups_this_month = \App\Models\FollowUp::whereMonth('created_at', now()->month)->count();
                    $total_revenue = \App\Models\FollowUp::sum('amount_paid');
                @endphp

                <!-- Total Patients Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Total Patients') }}</p>
                                <p class="text-3xl font-bold text-gray-800">{{ number_format($total_patients) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Patients Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('New Patients (This Month)') }}</p>
                                <p class="text-3xl font-bold text-gray-800">{{ number_format($new_patients_this_month) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Follow-ups Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Follow-ups (This Month)') }}</p>
                                <p class="text-3xl font-bold text-gray-800">{{ number_format($follow_ups_this_month) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-amber-100 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">{{ __('Total Revenue') }}</p>
                                <p class="text-3xl font-bold text-gray-800">₹{{ number_format($total_revenue) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Today's Queue -->
                <div class="lg:col-span-2 bg-white shadow-md rounded-xl overflow-hidden">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Today\'s Queue') }}</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $queue_entries = \App\Models\Queue::with('patient')
                                ->whereDate('in_queue_at', today())
                                ->orderBy('in_queue_at')
                                ->take(5)
                                ->get();
                        @endphp

                        @if($queue_entries->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient ID') }}</th>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient Name') }}</th>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Queue Time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($queue_entries as $entry)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $entry->patient->patient_id ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($entry->patient)
                                                        <a href="{{ route('patients.show', $entry->patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                            {{ $entry->patient->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400">{{ __('Unknown Patient') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $entry->in_queue_at->format('h:i A') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('queue.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    {{ __('View All') }} <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>{{ __('No patients in queue today.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-md rounded-xl overflow-hidden">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('Quick Actions') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('patients.create') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                <div class="p-2 bg-blue-100 rounded-md mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-blue-800">{{ __('messages.add_new_patient') }}</h4>
                                    <p class="text-sm text-blue-600">{{ __('Register new patient in the system') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('patients.index') }}" class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                <div class="p-2 bg-indigo-100 rounded-md mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-indigo-800">{{ __('messages.patients') }}</h4>
                                    <p class="text-sm text-indigo-600">{{ __('Manage all patient records') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('queue.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                <div class="p-2 bg-purple-100 rounded-md mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-purple-800">{{ __('messages.Queue') }}</h4>
                                    <p class="text-sm text-purple-600">{{ __('Manage patient queue') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('users.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                                <div class="p-2 bg-green-100 rounded-md mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-800">{{ __('messages.Staff') }}</h4>
                                    <p class="text-sm text-green-600">{{ __('Manage staff members') }}</p>
                                </div>
                            </a>
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
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date & Time') }}</th>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Patient') }}</th>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Doctor') }}</th>
                                            <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount Billed') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recent_follow_ups as $followUp)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $followUp->created_at->format('d M, Y - h:i A') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($followUp->patient)
                                                        <a href="{{ route('patients.show', $followUp->patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                            {{ $followUp->patient->name }}
                                                        </a>
                                                    @else
                                                        <span class="text-gray-400">{{ __('Unknown Patient') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $followUp->doctor ? $followUp->doctor->name : 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    ₹{{ number_format($followUp->amount_billed) }}
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
