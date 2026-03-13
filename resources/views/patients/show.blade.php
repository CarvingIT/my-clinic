<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-700 leading-tight">
            {{ __('messages.patient_details') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">

                    <!-- Display success message -->
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Patient Info (Left Side - 2/3 width) -->
                        <div class="lg:col-span-2">
                            <!-- Patient Header with Profile Picture -->
                            <div class="flex items-center gap-4 mb-4">
                                @php
                                    $profilePhoto = $uploads->where('photo_type', 'patient_photo')->sortBy('created_at')->first();
                                @endphp

                                <!-- Profile Picture -->
                                <div class="relative">
                                    @if($profilePhoto)
                                        <div class="relative group cursor-pointer" onclick="openProfilePictureModal('{{ route('uploads.show', $profilePhoto->id) }}')">
                                            <img src="{{ route('uploads.show', $profilePhoto->id) }}"
                                                 alt="Patient Profile Picture"
                                                 class="w-16 h-16 rounded-full object-cover border-2 border-indigo-200 shadow-md hover:shadow-lg transition-all duration-300">

                                            <!-- Hover overlay -->
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-full transition-all duration-300 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center border-2 border-indigo-200 shadow-md">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <!-- Edit Photo Button -->
                                    {{-- @if (Auth::check() && (Auth::user()->hasRole('doctor') || Auth::user()->hasRole('admin')))
                                        <button onclick="openEditProfilePictureModal()"
                                                class="absolute -bottom-1 -right-1 bg-indigo-600 hover:bg-indigo-700 text-white p-1.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300"
                                                title="Update Profile Picture">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    @endif --}}
                                </div>

                                <!-- Patient Name and ID -->
                                <div class="flex-1">
                                    <h2 class="text-2xl font-bold text-indigo-700 cursor-pointer hover:text-indigo-700 dark:hover:text-indigo-300 transition duration-400">
                                        {{ $patient->name }}
                                    </h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        Patient ID: {{ $patient->patient_id }}
                                    </p>
                                </div>
                            </div>

                            <!-- Complete Patient Information Grid -->
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3 text-sm" x-data="{ showMore: false }">
                                <!-- Vishesh Field - Always visible and spans 2 columns -->
                                @if ($patient->vishesh)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded lg:col-span-2 mt-4">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300 text-lg">{{ __('messages.Vishesh') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1 text-base leading-relaxed">
                                            {!! nl2br(html_entity_decode(strip_tags($patient->vishesh))) !!}
                                        </span>
                                    </div>
                                @endif

                                @if ($patient->birthdate || $patient->gender)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Age/Gender') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->birthdate?->age ?? __('') }}/{{ $patient->gender ?? __('') }}</span>
                                    </div>
                                @endif

                                @if ($patient->height)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Height') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->height }} cm</span>
                                    </div>
                                @endif

                                @if ($patient->weight)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Weight') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->weight }} kg</span>
                                    </div>
                                @endif

                                @if ($patient->height && $patient->weight)
                                    @php
                                        $heightInMeters = $patient->height / 100;
                                        $bmi = $patient->weight / ($heightInMeters * $heightInMeters);
                                        $bmiCategory = match (true) {
                                            $bmi < 18.5 => 'Underweight',
                                            $bmi >= 18.5 && $bmi < 25 => 'Normal',
                                            $bmi >= 25 && $bmi < 30 => 'Overweight',
                                            default => 'Obese',
                                        };
                                    @endphp
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('BMI') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ number_format($bmi, 1) }} ({{ $bmiCategory }})</span>
                                    </div>
                                @endif

                                @if ($patient->mobile_phone)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.mobile_phone') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->mobile_phone }}</span>
                                    </div>
                                @endif

                                @if ($patient->reference)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.reference') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->reference }}</span>
                                    </div>
                                @endif

                                @if ($patient->address)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded" x-show="showMore" x-transition>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.address') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->address }}</span>
                                    </div>
                                @endif

                                @if ($patient->occupation)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded" x-show="showMore" x-transition>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.occupation') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->occupation }}</span>
                                    </div>
                                @endif

                                @if ($patient->email_id)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded" x-show="showMore" x-transition>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Email ID') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->email_id }}</span>
                                    </div>
                                @endif

                                @if ($patient->birthdate)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded" x-show="showMore" x-transition>
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Birthdate') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{{ $patient->birthdate->format('d M Y') }}</span>
                                    </div>
                                @endif

                                <!-- Read More Button -->
                                                                <!-- Read More Button -->
                                <div class="lg:col-span-2 flex justify-center mt-2">
                                                                        <button @click="showMore = !showMore"
                                            class="bg-white hover:bg-indigo-50 text-indigo-600 hover:text-indigo-700 text-xs font-medium py-1 px-2 rounded-lg border border-indigo-200 hover:border-indigo-300 shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:scale-105">
                                        <span x-show="!showMore">{{ __('Read More') }}</span>
                                        <span x-show="showMore">{{ __('Read Less') }}</span>
                                    </button>
                                </div>

                            </div>



                        </div>

                        <!-- Reports Section (Right Side - 1/3 width) -->
                        <div class="lg:col-span-1">
                            @php
                                $allReports = [];
                                foreach ($patient->followUps as $followUp) {
                                    $checkUpInfo = json_decode($followUp->check_up_info, true) ?? [];
                                    if (!empty($checkUpInfo['reports']) && is_array($checkUpInfo['reports'])) {
                                        foreach ($checkUpInfo['reports'] as $index => $report) {
                                            if (isset($report['deleted_at'])) {
                                                continue;
                                            }
                                            $allReports[] = [
                                                'text' => $report['text'] ?? '',
                                                'timestamp' => $report['timestamp'] ?? '',
                                                'followup_date' => $followUp->created_at->format('d M Y'),
                                                'followup_id' => $followUp->id,
                                                'report_index' => $index
                                            ];
                                        }
                                    }
                                }
                                usort($allReports, function($a, $b) {
                                    return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
                                });
                            @endphp
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 h-full shadow-lg border border-gray-200 dark:border-gray-600">
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Reports
                                    </h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                                        {{ count($allReports) }}
                                    </span>
                                </div>

                                <!-- Search Bar -->
                                <div class="mb-4">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <input type="text" id="showReportSearch" placeholder="Search reports..."
                                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm">
                                    </div>
                                </div>

                                <!-- Reports List -->
                                <div id="showReportsList" class="space-y-3 max-h-56 overflow-y-auto scrollbar-thin scrollbar-thumb-indigo-300 dark:scrollbar-thumb-indigo-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800">
                                    @if(count($allReports) > 0)
                                        @foreach($allReports as $report)
                                            <div class="show-report-item bg-white dark:bg-gray-600 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-500"
                                                 data-text="{{ strtolower($report['text']) }}"
                                                 data-timestamp="{{ $report['timestamp'] }}"
                                                 data-followup-date="{{ $report['followup_date'] }}"
                                                 data-original-text="{{ $report['text'] }}"
                                                 data-followup-id="{{ $report['followup_id'] }}"
                                                 data-report-index="{{ $report['report_index'] }}">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="show-report-text text-sm text-gray-800 dark:text-gray-200 font-medium">
                                                            {!! nl2br(e($report['text'])) !!}
                                                        </div>
                                                        <div class="show-report-date text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $report['timestamp'] }} • Follow-up: {{ $report['followup_date'] }}
                                                        </div>
                                                    </div>
                                                    @if (Auth::check() && (Auth::user()->hasRole('doctor') || Auth::user()->hasRole('admin')))
                                                    <div class="flex flex-col space-y-1 ml-2">
                                                        <button type="button" onclick="showEditReport(this)"
                                                            class="w-6 h-6 bg-blue-500 text-white rounded hover:bg-blue-600 transition flex items-center justify-center"
                                                            title="Edit">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" onclick="showDeleteReport(this)"
                                                            class="w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 transition flex items-center justify-center"
                                                            title="Delete">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div class="text-sm">No reports found</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        </div>{{-- End grid --}}
                        <div class="flex justify-end mb-8 mt-3">
                            <a href="{{ route('followups.create', ['patient' => $patient->id]) }}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-md shadow-md transition duration-300">
                                {{ __('messages.add_follow_up') }}
                            </a>

                            <a href="{{ route('patients.export-pdf', $patient) }}" target="_blank"
                                class="bg-sky-400 hover:bg-sky-500 text-white font-medium py-2 px-6 ml-4 rounded-md shadow-md transition duration-300">
                                {{ __('messages.Export to PDF') }}
                            </a>

                            <!-- Share Patient Button (Modal Trigger) -->
                            <div x-data="{ openExportPatientModal: false }">
                                @if (Auth::check() && (Auth::user()->hasRole('doctor') || Auth::user()->hasRole('admin')))
                                    <button @click="openExportPatientModal = true" title="Share Patient"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 ml-4 rounded-md shadow-md transition duration-300">
                                        <i class="fas fa-share-alt mr-2"></i> {{ __('Share Patient') }}
                                    </button>
                                @endif

                                <!-- Modal -->
                                <div x-show="openExportPatientModal" x-cloak x-transition.opacity
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                                    <div @click.away="openExportPatientModal = false"
                                        class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl border border-blue-100 transition-all">

                                        <!-- Header -->
                                        <div
                                            class="flex items-center justify-between px-6 py-4 bg-blue-50 border-b border-blue-100 rounded-t-2xl">
                                            <h3 class="text-lg font-bold text-blue-800">
                                                {{ __('Share Patient Data') }}
                                            </h3>
                                            <button @click="openExportPatientModal = false"
                                                class="text-blue-800 text-2xl font-bold hover:text-red-500 transition">
                                                &times;
                                            </button>
                                        </div>

                                        <!-- Body -->
                                        <div class="px-6 py-5">
                                            <form method="POST"
                                                action="{{ route('patients.export_json', $patient->id) }}">
                                                @csrf
                                                <div class="mb-5">
                                                    <label for="email"
                                                        class="block text-sm font-semibold text-blue-700 mb-1">
                                                        📧 Recipient Email Address
                                                    </label>
                                                    <input type="email" id="email" name="email"
                                                        placeholder="e.g. doctor@example.com"
                                                        class="w-full border border-blue-300 rounded-lg px-4 py-2 text-sm text-blue-900 bg-blue-50 placeholder-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        required />
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        We'll send the exported patient data to this email address.
                                                    </p>
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                                                </div>

                                                <div class="flex justify-end gap-2 mt-6">
                                                    <button type="button" @click="openExportPatientModal = false"
                                                        class="bg-white hover:bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-lg border border-blue-300 transition">
                                                        {{ __('Cancel') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg shadow transition">
                                                        {{ __('Share') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Generate Certificate button --}}

                            <div x-data="{ open: false }">
                                <button @click="open = true"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 px-6 ml-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-certificate mr-2"></i>{{ __('messages.Generate Certificate') }}
                                </button>

                                <!-- Modal Backdrop -->
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm"
                                    @keydown.escape.window="open = false">

                                    <!-- Modal Content -->
                                    <div x-show="open"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="relative w-full max-w-2xl mx-4 my-6 bg-white rounded-xl shadow-2xl border border-gray-200"
                                        @click.away="open = false">

                                        <!-- Header -->
                                        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-green-50 rounded-t-xl">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-certificate text-white"></i>
                                                </div>
                                                <h3 class="text-xl font-bold text-gray-900">
                                                    {{ __('messages.Generate Certificate') }}
                                                </h3>
                                            </div>
                                            <button @click="open = false"
                                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors duration-200"
                                                aria-label="Close modal">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Body -->
                                        <div class="p-6">
                                            <form method="GET" action="{{ route('patients.certificate', $patient) }}" target="_blank">
                                                @csrf

                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                                    <div class="space-y-2">
                                                        <x-input-label for="start_date" :value="__('Start Date')" class="text-sm font-medium text-gray-700" />
                                                        <x-text-input type="date" id="start_date" name="start_date"
                                                            value="{{ old('start_date') }}"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                                            required />
                                                        <x-input-error :messages="$errors->get('start_date')" class="text-sm text-red-600 mt-1" />
                                                    </div>

                                                    <div class="space-y-2">
                                                        <x-input-label for="end_date" :value="__('End Date')" class="text-sm font-medium text-gray-700" />
                                                        <x-text-input type="date" id="end_date" name="end_date"
                                                            value="{{ old('end_date') }}"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                                            required />
                                                        <x-input-error :messages="$errors->get('end_date')" class="text-sm text-red-600 mt-1" />
                                                    </div>

                                                    <div class="space-y-2 md:col-span-2 lg:col-span-1">
                                                        <x-input-label for="medical_condition" :value="__('Medical Condition')" class="text-sm font-medium text-gray-700" />
                                                        <x-text-input type="text" id="medical_condition" name="medical_condition"
                                                            value="{{ old('medical_condition') }}"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200"
                                                            placeholder="Enter medical condition"
                                                            required />
                                                        <x-input-error :messages="$errors->get('medical_condition')" class="text-sm text-red-600 mt-1" />
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                                    <button type="button" @click="open = false"
                                                        class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                                        {{ __('messages.Close') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                                        <i class="fas fa-download mr-2"></i>{{ __('messages.Generate Certificate') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Consent Form Button --}}
                            <div x-data="{ openConsentModal: false }">
                                <button @click="openConsentModal = true"
                                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 px-6 ml-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-file-signature mr-2"></i>{{ __('messages.Consent Form') }}
                                </button>

                                <!-- Modal Backdrop -->
                                <div x-show="openConsentModal" x-cloak
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm"
                                    @keydown.escape.window="openConsentModal = false">

                                    <!-- Modal Content -->
                                    <div x-show="openConsentModal"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="relative w-full max-w-2xl mx-4 my-6 bg-white rounded-xl shadow-2xl border border-gray-200"
                                        @click.away="openConsentModal = false">

                                        <!-- Header -->
                                        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-t-xl">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-file-signature text-white"></i>
                                                </div>
                                                <h3 class="text-xl font-bold text-gray-900">
                                                    {{ __('messages.Consent Form') }}
                                                </h3>
                                            </div>
                                            <button @click="openConsentModal = false"
                                                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors duration-200"
                                                aria-label="Close modal">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Body -->
                                        <div class="p-6">
                                            <form method="GET" action="{{ route('patients.template', ['patient' => $patient, 'templateSlug' => 'consent_form']) }}" target="_blank">
                                                @csrf

                                                <div class="space-y-6">
                                                    <div class="space-y-2">
                                                        <x-input-label for="procedure_name" :value="__('Procedure/Treatment Name')" class="text-sm font-medium text-gray-700" />
                                                        <x-text-input type="text" id="procedure_name" name="procedure_name"
                                                            value="{{ old('procedure_name') }}"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200"
                                                            placeholder="Enter procedure or treatment name"
                                                            required />
                                                        <x-input-error :messages="$errors->get('procedure_name')" class="text-sm text-red-600 mt-1" />
                                                    </div>

                                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                                        <div class="flex items-start">
                                                            <div class="flex-shrink-0">
                                                                <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                                                            </div>
                                                            <div class="ml-3">
                                                                <h4 class="text-sm font-medium text-amber-800">Information</h4>
                                                                <p class="text-sm text-amber-700 mt-1">
                                                                    This consent form will be generated with the patient's details and the specified procedure/treatment.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Footer -->
                                                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 mt-6">
                                                    <button type="button" @click="openConsentModal = false"
                                                        class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                                        {{ __('messages.Close') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="px-6 py-2.5 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                                        <i class="fas fa-download mr-2"></i>{{ __('messages.Generate Consent Form') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reports Button --}}
                            <div x-data="{ openReportModal: false, selectedImageUrl: '' }">
                                <button @click="openReportModal = true" title="{{ __('messages.View Reports') }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 ml-4 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105 w-15 h-10 flex items-center justify-center">
                                    <i class="fas fa-file-image text-white text-2xl"></i>
                                </button>

                                <!-- Medical Reports Modal -->
                                <div x-show="openReportModal" style="background-color: rgba(0, 0, 0, 0.75)"
                                    class="fixed inset-0 flex items-center justify-center z-50" x-cloak>
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col overflow-hidden">
                                        <!-- Header -->
                                        <div
                                            class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                {{ __('messages.Medical Reports') }}
                                            </h2>
                                            <button @click="openReportModal = false; selectedImageUrl = ''"
                                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 overflow-y-auto p-6">
                                            <template x-if="!selectedImageUrl">
                                                <div>
                                                    @if ($uploads->count() > 0)
                                                        <div class="grid grid-cols-2 gap-6">
                                                            <!-- Left Column: Patient Photos -->
                                                            <div>
                                                                <h3
                                                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                                                    Patient Photos</h3>
                                                                <div class="space-y-4">
                                                                    @foreach ($uploads->where('photo_type', 'patient_photo') as $upload)
                                                                        <div @click="selectedImageUrl = '{{ route('uploads.show', $upload->id) }}'"
                                                                            class="relative flex items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition duration-200">
                                                                            <!-- Delete Button -->
                                                                            <form method="POST"
                                                                                action="{{ route('uploads.destroy', $upload->id) }}"
                                                                                onsubmit="return confirm('Are you sure you want to delete this photo?');"
                                                                                class="absolute top-2 right-2 z-10">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="text-red-600 hover:text-red-800 p-1 rounded-full bg-white/80 shadow-sm transition duration-200"
                                                                                    @click.stop>
                                                                                    <svg class="w-5 h-5"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M6 18L18 6M6 6l12 12">
                                                                                        </path>
                                                                                    </svg>
                                                                                </button>
                                                                            </form>
                                                                            <!-- Thumbnail -->
                                                                            <div class="flex-shrink-0 w-20 h-20">
                                                                                <img src="{{ route('uploads.show', $upload->id) }}"
                                                                                    alt="{{ $upload->photo_type }}"
                                                                                    class="w-full h-full object-cover rounded-md border border-gray-200 dark:border-gray-700">
                                                                            </div>
                                                                            <!-- Details -->
                                                                            <div class="ml-4 flex-1">
                                                                                <p
                                                                                    class="text-lg font-medium text-gray-800 dark:text-gray-200 capitalize">
                                                                                    {{ str_replace('_', ' ', $upload->photo_type) }}
                                                                                </p>
                                                                                <p
                                                                                    class="text-sm text-gray-500 dark:text-gray-400">
                                                                                    {{ __('messages.Uploaded') }}:
                                                                                    {{ $upload->created_at->format('d M Y, h:i A') }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    @if ($uploads->where('photo_type', 'patient_photo')->isEmpty())
                                                                        <p
                                                                            class="text-center text-gray-500 dark:text-gray-400 py-4">
                                                                            No patient photos available.</p>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Right Column: Lab Reports -->
                                                            <div>
                                                                <h3
                                                                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                                                    Lab Reports</h3>
                                                                <div class="space-y-4">
                                                                    @foreach ($uploads->where('photo_type', 'lab_report') as $upload)
                                                                        <div @click="selectedImageUrl = '{{ route('uploads.show', $upload->id) }}'"
                                                                            class="relative flex items-center p-3 bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition duration-200">
                                                                            <!-- Delete Button -->
                                                                            <form method="POST"
                                                                                action="{{ route('uploads.destroy', $upload->id) }}"
                                                                                onsubmit="return confirm('Are you sure you want to delete this photo?');"
                                                                                class="absolute top-2 right-2 z-10">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="text-red-600 hover:text-red-800 p-1 rounded-full bg-white/80 shadow-sm transition duration-200"
                                                                                    @click.stop>
                                                                                    <svg class="w-5 h-5"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M6 18L18 6M6 6l12 12">
                                                                                        </path>
                                                                                    </svg>
                                                                                </button>
                                                                            </form>
                                                                            <!-- Thumbnail -->
                                                                            <div class="flex-shrink-0 w-20 h-20">
                                                                                <img src="{{ route('uploads.show', $upload->id) }}"
                                                                                    alt="{{ $upload->photo_type }}"
                                                                                    class="w-full h-full object-cover rounded-md border border-gray-200 dark:border-gray-700">
                                                                            </div>
                                                                            <!-- Details -->
                                                                            <div class="ml-4 flex-1">
                                                                                <p
                                                                                    class="text-lg font-medium text-gray-800 dark:text-gray-200 capitalize">
                                                                                    {{ str_replace('_', ' ', $upload->photo_type) }}
                                                                                </p>
                                                                                <p
                                                                                    class="text-sm text-gray-500 dark:text-gray-400">
                                                                                    {{ __('messages.Uploaded') }}:
                                                                                    {{ $upload->created_at->format('d M Y, h:i A') }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    @if ($uploads->where('photo_type', 'lab_report')->isEmpty())
                                                                        <p
                                                                            class="text-center text-gray-500 dark:text-gray-400 py-4">
                                                                            No lab reports available.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="text-center text-gray-500 dark:text-gray-400 py-4">No
                                                            medical reports or photos available.</p>
                                                    @endif
                                                </div>
                                            </template>

                                            <!-- Full-Size Image View -->
                                            <template x-if="selectedImageUrl">
                                                <div class="relative h-full flex items-center justify-center">
                                                    <button @click="selectedImageUrl = ''"
                                                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200 z-10">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        <span class="sr-only">Back to list</span>
                                                    </button>
                                                    <img :src="selectedImageUrl" alt="Full-size image"
                                                        class="w-full h-auto max-h-[calc(90vh-120px)] object-contain rounded-lg">
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Footer -->
                                        <div x-show="!selectedImageUrl"
                                            class="flex justify-end p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                                            <button @click="openReportModal = false"
                                                class="text-red-600 hover:text-red-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                                {{ __('messages.Close') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Reports Button Ends here --}}


                            {{-- Chikitsa Ahwal --}}

                            <div x-data="{ openReportModal: false }">
                                <button @click="openReportModal = true" title="{{ __('Upload/View PDF') }}"
                                    class="bg-fuchsia-600 hover:bg-fuchsia-700 text-white font-medium py-2 px-6 ml-4 rounded-md shadow-md transition duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="w-6 h-6">
                                        <!-- White document icon -->
                                        <path fill="white"
                                            d="M224 0v128h128L224 0zM64 0C28.7 0 0 28.7 0 64v384c0 35.3 28.7 64 64 64h256c35.3 0 64-28.7 64-64V160H224c-17.7 0-32-14.3-32-32V0H64z" />
                                        <!-- Red PDF text -->
                                        <text x="95" y="380" font-size="130" font-weight="bold"
                                            fill="red">PDF</text>
                                    </svg>
                                </button>

                                <div x-show="openReportModal" style="background-color: rgba(0, 0, 0, 0.5)"
                                    class="fixed top-0 left-0 w-full h-full flex items-center shadow-lg overflow-y-auto z-50"
                                    x-cloak>
                                    <div class="container mx-auto lg:px-32 rounded-lg overflow-y-auto">
                                        <div class="bg-white relative shadow-lg rounded-lg">
                                            <div class="p-4">
                                                <div class="flex justify-between items-center">
                                                    <h2
                                                        class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 mb-2">
                                                        {{ __('messages.Medical Reports') }}</h2>
                                                    <button
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                                        data-modal-toggle="defaultModal" type="button"
                                                        @click="openReportModal = false"> <svg class="w-5 h-5"
                                                            fill="currentColor" viewBox="0 0 20 20"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd"></path>
                                                        </svg> </button>

                                                    <span class="sr-only">Close modal</span>
                                                    </button>
                                                </div>

                                                <div class="relative p-6 flex-auto">
                                                    @if ($patient->reports->count() > 0)
                                                        <div
                                                            class="mb-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                        </div>
                                                        @foreach ($patient->reports as $report)
                                                            <div
                                                                class="p-2 border border-gray-200 rounded-md shadow-sm flex  justify-between items-center">
                                                                <a href="{{ Storage::url($report->path) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:text-indigo-900">
                                                                    {{ $report->name }}
                                                                </a>

                                                                <p class="text-sm text-gray-500 mt-1">
                                                                    {{ __('messages.Uploaded') }}:
                                                                    {{ $report->created_at->format('d M Y, h:i A') }}
                                                                </p>

                                                                <form method="POST"
                                                                    action="{{ route('reports.destroy', $report) }}"
                                                                    onsubmit="return confirmDelete()"
                                                                    class="inline-block mt-2">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="text-red-600 hover:text-red-900 px-3 py-1 bg-red-100 hover:bg-red-200 rounded-md focus:ring focus:ring-red-200 focus:outline-none transition duration-200"><i
                                                                            class="fas fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                </div>
                                                @endif

                                                <form method="POST" action="{{ route('reports.store') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="patient_id"
                                                        value="{{ $patient->id }}">

                                                    <div class="mt-4">
                                                        <x-input-label for="report" :value="__('messages.Upload Reports')" />
                                                        <input type="file" name="report[]" id="report"
                                                            class="block mt-1 w-full border border-gray-300 rounded-md focus:ring focus:ring-indigo-200 focus:border-indigo-300 transition-all duration-200"
                                                            multiple>

                                                        <x-input-error :messages="$errors->get('report')" class="mt-2" />
                                                    </div>

                                                    <div class="flex items-center justify-end mt-4">
                                                        <x-primary-button
                                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg shadow-md transition duration-300">
                                                            {{ __('messages.Upload') }}
                                                        </x-primary-button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div
                                                class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
                                                <button
                                                    class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none ms-1 mb-1 ease-linear transition-all duration-150"
                                                    type="button" @click="openReportModal = false">
                                                    {{ __('messages.Close') }}
                                                </button>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reports Button Ends here --}}









                    </div>

                    <h2 class="text-xl font-semibold mb-2">{{ __('messages.follow_ups') }}</h2>

                    {{-- Outstanding Balance --}}
                    @if (isset($totalDueAll))
                        <div
                            class="
                                @if ($totalDueAll == 0) bg-green-200 text-green-800
                                @elseif($totalDueAll < 0) bg-blue-200 text-blue-800
                                @elseif($totalDueAll < 2000) bg-yellow-200 text-yellow-800
                                @else bg-red-200 text-red-800 @endif
                                p-4 rounded-md font-bold text-right pr-15">
                            {{ __('messages.Total Outstanding Balance') }}:
                            ₹{{ number_format($totalDueAll, 2) }}
                        </div>
                    @else
                        <div class="text-red-600 font-bold">Error: Total Outstanding Balance not found!</div>
                    @endif

                    {{-- Outstanding Balance End --}}

                    @if ($patient->followUps->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 text-black dark:bg-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Created At') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('नाडी') }}/{{ __('लक्षणे') }}
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('चिकित्सा') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Payments') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('messages.Actions') }}
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($patient->followUps->sortByDesc('created_at') as $followUp)
                                        <tr class="hover:bg-gray-50 transition duration-300 dark:hover:bg-gray-700">
                                            <td class="w-[220px] px-6 py-4 text-gray-600 dark:text-gray-300"
                                                style="vertical-align: top;">


                                                <p class="">
                                                    {{ $followUp->created_at->format('d M Y, h:i A') }}
                                                </p>
                                                {{-- {{ $followUp->created_at->format('d M Y, h:i A') }} --}}
                                                @php
                                                    $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                @endphp
                                                {{-- Seen by and OPD  --}}
                                                @if (isset($checkUpInfo['user_name']))
                                                    <p>
                                                        <span
                                                            class="font-bold text-gray-800 dark:text-gray-200">{{ __('S/B') }}:</span>
                                                        {{ $checkUpInfo['user_name'] }}
                                                    </p>
                                                @endif

                                                @if (isset($checkUpInfo['branch_name']))
                                                    <p>
                                                        <span
                                                            class="font-bold text-gray-800 dark:text-gray-200">{{ __('OPD') }}:</span>
                                                        {{ $checkUpInfo['branch_name'] }}
                                                    </p>
                                                @endif


                                            </td>
                                            <td class="px-6 py-4 align-top text-gray-600 dark:text-gray-300 max-w-xs break-words whitespace-normal"
                                                style="word-break: break-word; overflow-wrap: break-word;">
                                                @if ($followUp->check_up_info)
                                                    <div>
                                                        {{-- <h2 class="font-semibold text-gray-800 dark:text-gray-200">
                                                            {{ __('नाडी') }}</h2> --}}
                                                        @foreach ($checkUpInfo as $key => $value)
                                                            @if (in_array($key, [
                                                                    'वात',
                                                                    'पित्त',
                                                                    'कफ',
                                                                    'सूक्ष्म',
                                                                    'कठीण',
                                                                    'साम',
                                                                    'प्राण',
                                                                    'व्यान',
                                                                    'स्थूल',
                                                                    'तीक्ष्ण',
                                                                    'वेग',
                                                                    'अनियमित',
                                                                ]))
                                                                @if (is_array($value))
                                                                    @foreach ($value as $option)
                                                                        <p>{{ __($option) }}</p>
                                                                    @endforeach
                                                                @elseif ($value == 1)
                                                                    <p>{{ __($key) }}</p>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </div>




                                                    <div>

                                                        @if (isset($checkUpInfo['nadi']) && $checkUpInfo['nadi'] !== null && $checkUpInfo['nadi'] !== '')
                                                            <p>
                                                                {{-- <span
                                                                    class="font-bold text-gray-800 dark:text-gray-200">{{ __('नाडी') }}:</span> --}}
                                                                {!! $checkUpInfo['nadi'] !!}
                                                            </p>
                                                            @if (isset($checkUpInfo['nadi_dots']))
                                                                @php
                                                                    $nadiDots = $checkUpInfo['nadi_dots'] ?? [[], [], []];
                                                                @endphp
                                                                <div class="mt-2 flex gap-0.5">
                                                                    @foreach($nadiDots as $box)
                                                                        <div class="grid grid-cols-3 gap-0 bg-gray-100 dark:bg-gray-600 p-0.25 rounded border border-gray-100 dark:border-gray-600">
                                                                            @for($i = 0; $i < 9; $i++)
                                                                                <div class="w-3 h-3 flex items-center justify-center bg-white dark:bg-gray-800 {{ $i % 3 != 2 ? 'border-r border-gray-300 dark:border-gray-500' : '' }} {{ $i < 6 ? 'border-b border-gray-300 dark:border-gray-500' : '' }} {{ ($box[$i] ?? false) ? 'text-red-500 text-sm' : '' }}">
                                                                                    {{ ($box[$i] ?? false) ? '•' : '' }}
                                                                                </div>
                                                                            @endfor
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endif

@if (isset($followUp->diagnosis) && $followUp->diagnosis !== null && $followUp->diagnosis !== '')
    {{-- Note the change here --}}
    <p>
        {{-- <span
            class="font-bold text-gray-800 dark:text-gray-200">{{ __('लक्षणे') }}:</span> --}}
        {!! $followUp->diagnosis !!}
    </p>
@endif
@if (isset($checkUpInfo['nidan']) && $checkUpInfo['nidan'] !== null && $checkUpInfo['nidan'] !== '')
    <p>
        <span class="font-bold text-gray-800 dark:text-gray-200">{{ __('निदान') }}:</span>
        {!! str_replace(['<p>', '</p>'], ['', ''], trim($checkUpInfo['nidan'])) !!}
    </p>
@endif
{{-- <p><span
        class="font-bold text-gray-800 dark:text-gray-200">{{ __('चिकित्सा') }}:</span>
    @if (isset($checkUpInfo['chikitsa']))
        {{ $checkUpInfo['chikitsa'] }}
    @endif
</p> --}}                                                        {{-- @if ($followUp->patient_photos)
                                                            <img src="{{ route('followup.image', ['filename' => basename($followUp->patient_photos)]) }}"
                                                                alt="Patient Photo">
                                                        @else
                                                            <p>No photo available.</p>
                                                        @endif --}}


                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-2 py-4 align-top text-gray-600 dark:text-gray-300 max-w-xs break-words whitespace-normal"
                                                style=" word-break: break-word; overflow-wrap: break-word;">

                                                <div>
                                                    {{-- <span class="font-bold text-gray-800 dark:text-gray-200">{{ __('चिकित्सा') }}:</span> --}}
                                                    @if (isset($checkUpInfo['chikitsa']))
                                                        {!! $checkUpInfo['chikitsa'] !!}
                                                    @endif
                                                </div>

                                                <p>
                                                    @if (isset($checkUpInfo['days']) && $checkUpInfo['days'] !== null && $checkUpInfo['days'] !== '')
                                                        <p>
                                                            <span
                                                                class="font-bold text-gray-800 dark:text-gray-200">{{ __('दिवस') }}:</span>
                                                            {{ $checkUpInfo['days'] }}
                                                        </p>
                                                    @endif
                                                </p>
                                                <p>
                                                    @if (isset($checkUpInfo['packets']) && $checkUpInfo['packets'] !== null && $checkUpInfo['packets'] !== '')
                                                        <p>
                                                            <span
                                                                class="font-bold text-gray-800 dark:text-gray-200">{{ __('पुड्या') }}:</span>
                                                            {{ $checkUpInfo['packets'] }}
                                                        </p>
                                                    @endif
                                                </p>

                                                <!-- Display Photos Link -->
                                                @if ($followUp->uploads->isNotEmpty())
                                                    <p>
                                                        <span
                                                            class="font-bold text-gray-800 dark:text-gray-200">{{ __('Photos') }}:</span>
                                                        <a href="#"
                                                            class="text-blue-500 hover:underline open-images-popup"
                                                            data-uploads="{{ json_encode(
                                                                $followUp->uploads->map(function ($upload) {
                                                                        return [
                                                                            'url' => route('uploads.show', $upload->id),
                                                                            'type' => $upload->photo_type,
                                                                        ];
                                                                    })->toArray(),
                                                            ) }}">
                                                            <i class="fas fa-file"></i>
                                                            ({{ $followUp->uploads->count() }})
                                                        </a>
                                                    </p>
                                                @endif

                                            </td>


                                            <td class="w-[250px] px-6 py-2 align-top text-gray-600 dark:text-gray-300"
                                                style="">
                                                @if ($followUp->check_up_info)
                                                    @php
                                                        $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                    @endphp

                                                    <div>
                                                        @if (isset($checkUpInfo['payment_method']) &&
                                                                $checkUpInfo['payment_method'] !== null &&
                                                                $checkUpInfo['payment_method'] !== '')
                                                            <p class="">
                                                                {{-- <span
                                                                    class="font-bold text-gray-800 dark:text-gray-200">{{ __('messages.Payment Method') }}:</span> --}}
                                                                {{ $checkUpInfo['payment_method'] }}
                                                            </p>
                                                        @endif


                                                    </div>
                                                    <div>
                                                        {{-- @foreach ($patient->followUps as $followUp) --}}
                                                        @php
                                                            $amountBilled = $followUp->amount_billed ?? 0; // Total amount billed
                                                            $amountPaid = $followUp->amount_paid ?? 0; // Amount patient paid
                                                            $totalDue = $amountBilled - $amountPaid; // Due for this follow-up
                                                        @endphp

                                                        <div class="    ">
                                                            <p class="">
                                                                <span
                                                                    class="font-bold text-gray-800 dark:text-gray-200">{{ __('messages.Amount Billed') }}:</span>
                                                                ₹{{ number_format($amountBilled, 2) }}
                                                            </p>

                                                            <p class="">
                                                                <span
                                                                    class="font-bold text-gray-800 dark:text-gray-200">{{ __('messages.Amount Paid') }}:</span>
                                                                ₹{{ number_format($amountPaid, 2) }}
                                                            </p>

                                                            <p
                                                                class="{{ $totalDue < 0 ? 'text-blue-600' : ($totalDue == 0 ? 'text-green-600' : 'text-red-600') }} font-bold">
                                                                <span
                                                                    class="font-bold text-gray-800 dark:text-gray-200">
                                                                    {{ __('messages.Amount Due') }}:
                                                                </span>
                                                                ₹{{ number_format($totalDue, 2) }}
                                                            </p>

                                                        </div>
                                                        {{-- @endforeach --}}

                                                    </div>
                                                @endif
                                            </td>
                                            <td
                                                class="px-6 py-4 text-gray-600 dark:text-gray-300 flex gap-4 items-center">
                                                {{-- Print Prescription --}}
                                                <a href="{{ route('followups.prescription.print', ['followup' => $followUp->id]) }}"
                                                    target="_blank"
                                                    class="text-green-600 hover:text-green-800 font-medium"
                                                    title="Print Prescription">
                                                    <i class="fas fa-prescription"></i>
                                                </a>
                                                {{-- Download Prescription PDF --}}
                                                {{-- <a href="{{ route('followups.prescription', ['followup' => $followUp->id]) }}"
                                                    target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                                    title="Download Prescription PDF">
                                                    <i class="fas fa-file-medical"></i>
                                                </a> --}}
                                                <a href="{{ route('followups.edit', ['followup' => $followUp->id]) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium"title="Edit">

                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('followups.destroy', ['followup' => $followUp->id]) }}"
                                                    onsubmit="return confirmDelete()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 font-medium"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{ $patient->followUps->links() }}
                        </div>
                    @else
                        <p class="text-gray-600 bg-gray-100 p-4 rounded-md shadow-sm">
                            No follow-ups added for the patient.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Popup Modal -->
    <div id="imagesPopup"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden flex items-center justify-center z-50">
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full relative max-h-[80vh] overflow-y-auto">
            <button id="closePopup"
                class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">✖</button>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Follow-Up Images</h3>

            <!-- Patient Photos Section -->
            <div class="mb-6">
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Patient Photos</h4>
                <div id="patientPhotos" class="grid grid-cols-2 gap-4">
                    <!-- Patient photos will be injected here -->
                </div>
                <p id="noPatientPhotos" class="text-gray-500 dark:text-gray-400 hidden">No patient photos
                    available.</p>
            </div>

            <!-- Lab Reports Section -->
            <div>
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-2">Lab Reports</h4>
                <div id="labReports" class="grid grid-cols-2 gap-4">
                    <!-- Lab reports will be injected here -->
                </div>
                <p id="noLabReports" class="text-gray-500 dark:text-gray-400 hidden">No lab reports available.</p>
            </div>
        </div>
    </div>

    <!-- Profile Picture Modal -->
    <div id="profilePictureModal"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl w-full relative">
            <button id="closeProfilePictureModal"
                    class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 text-2xl">✖</button>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Patient Profile Picture</h3>
            <div class="flex justify-center">
                <img id="profilePictureImage" src="" alt="Profile Picture"
                     class="max-w-full max-h-[70vh] object-contain rounded-lg">
            </div>
        </div>
    </div>

    <!-- Edit Profile Picture Modal -->
    <div id="editProfilePictureModal"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md w-full relative">
            <button id="closeEditProfilePictureModal"
                    class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 text-2xl">✖</button>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Update Profile Picture</h3>

            <form method="POST" action="{{ route('patients.update', $patient->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- File Upload -->
                <div>
                    <label for="photo_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Choose New Photo
                    </label>
                    <input type="file" id="photo_file" name="photo_file" accept="image/*"
                           class="w-full rounded-lg border-2 border-gray-300 dark:border-gray-600 p-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted formats: JPEG, PNG, JPG. Max size: 2MB</p>
                </div>

                <!-- Camera Capture Option -->
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Or capture with camera:</p>
                    <button type="button" id="openCameraModal"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                        📷 Capture Photo with Camera
                    </button>
                </div>

                <!-- Hidden inputs for camera capture -->
                <input type="file" id="photoFileInput" name="photos[]" multiple style="display: none;">
                <input type="hidden" id="photoTypesInput" name="photo_types">

                <!-- Camera Modal -->
                <div id="cameraModal"
                     class="fixed inset-0 bg-gray-200 bg-opacity-75 hidden flex justify-center items-center transition-opacity duration-300 z-60">
                    <div class="bg-white p-6 rounded-xl shadow-lg w-[600px] h-[500px] flex flex-col gap-4 border border-gray-300">
                        <!-- Camera Section -->
                        <h4 class="text-xl font-bold tracking-wider text-indigo-600">Capture Profile Photo</h4>

                        <label class="block text-sm text-gray-700">Camera Source:</label>
                        <select id="cameraSelect"
                                class="w-full p-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200"></select>

                        <div class="flex-1 overflow-hidden rounded-lg border border-gray-300 shadow-inner bg-gray-200">
                            <video id="cameraPreview" class="w-full h-full object-contain" autoplay></video>
                        </div>

                        <div class="flex justify-between">
                            <button id="captureBtn" type="button"
                                    class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white rounded-lg hover:from-indigo-600 hover:to-cyan-600 transform hover:scale-105 transition-all duration-200 shadow-md">📸
                                Capture</button>
                            <button id="closeCameraModal" type="button"
                                    class="px-5 py-2 bg-gradient-to-r from-red-400 to-pink-400 text-white rounded-lg hover:from-red-500 hover:to-pink-500 transform hover:scale-105 transition-all duration-200 shadow-md">Close</button>
                        </div>

                        <!-- Preview Section -->
                        <div class="flex-1 flex flex-col bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <h5 class="text-lg font-semibold text-gray-700 mb-2">Captured Photo</h5>
                            <div id="patientPhotosImages" class="flex-1 overflow-y-auto"></div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeEditProfilePictureModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                        Update Photo
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

<!-- Report Edit Modal (for show page) -->
<div id="showReportModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Report</h2>
        <div class="mb-4">
            <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Report Text</label>
            <textarea id="showReportText" rows="4" placeholder="Enter your report here..."
                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeShowReportModal()"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg">Cancel</button>
            <button type="button" onclick="saveShowReport()"
                class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg">Update</button>
        </div>
    </div>
</div>

<script>
    // Show page report functionality
    let showEditFollowupId = null;
    let showEditReportIndex = null;
    let showEditReportItem = null;

    function showEditReport(button) {
        const item = button.closest('.show-report-item');
        showEditFollowupId = item.dataset.followupId;
        showEditReportIndex = item.dataset.reportIndex;
        showEditReportItem = item;
        document.getElementById('showReportText').value = item.dataset.originalText;
        const modal = document.getElementById('showReportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('showReportText').focus();
    }

    function closeShowReportModal() {
        const modal = document.getElementById('showReportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('showReportText').value = '';
        showEditFollowupId = null;
        showEditReportIndex = null;
        showEditReportItem = null;
    }

    function saveShowReport() {
        const reportText = document.getElementById('showReportText').value.trim();
        if (!reportText) {
            alert('Please enter a report.');
            return;
        }
        fetch(`/followups/${showEditFollowupId}/reports/${showEditReportIndex}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ text: reportText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const textDiv = showEditReportItem.querySelector('.show-report-text');
                if (textDiv) {
                    textDiv.innerHTML = reportText.replace(/\n/g, '<br>');
                }
                showEditReportItem.dataset.originalText = reportText;
                showEditReportItem.dataset.text = reportText.toLowerCase();
                closeShowReportModal();
            } else {
                alert('Error updating report: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('Error updating report. Please try again.'));
    }

    function showDeleteReport(button) {
        if (!confirm('Are you sure you want to delete this report?')) return;
        const item = button.closest('.show-report-item');
        const followupId = item.dataset.followupId;
        const reportIndex = item.dataset.reportIndex;
        fetch(`/followups/${followupId}/reports/${reportIndex}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.remove();
                // Update count badge
                const remaining = document.querySelectorAll('.show-report-item').length;
                const badge = document.querySelector('#showReportsList').closest('.bg-gradient-to-br').querySelector('.rounded-full');
                if (badge) badge.textContent = remaining;
            } else {
                alert('Error deleting report: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('Error deleting report. Please try again.'));
    }

    // Search functionality for show page reports
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('showReportSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('.show-report-item').forEach(item => {
                    const text = item.dataset.text || '';
                    const followupDate = item.dataset.followupDate || '';
                    const matches = !term || text.includes(term) || followupDate.toLowerCase().includes(term);
                    item.style.display = matches ? 'block' : 'none';

                    if (matches && term) {
                        const textDiv = item.querySelector('.show-report-text');
                        const dateDiv = item.querySelector('.show-report-date');
                        const regex = new RegExp(`(${term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                        if (textDiv) textDiv.innerHTML = item.dataset.originalText.replace(/\n/g, '<br>').replace(regex, '<mark>$1</mark>');
                        if (dateDiv) dateDiv.innerHTML = (item.dataset.timestamp + ' • Follow-up: ' + item.dataset.followupDate).replace(regex, '<mark>$1</mark>');
                    } else if (!term) {
                        const textDiv = item.querySelector('.show-report-text');
                        const dateDiv = item.querySelector('.show-report-date');
                        if (textDiv) textDiv.innerHTML = item.dataset.originalText.replace(/\n/g, '<br>');
                        if (dateDiv) dateDiv.innerHTML = item.dataset.timestamp + ' • Follow-up: ' + item.dataset.followupDate;
                    }
                });
            });
        }

        // Close modal on outside click
        document.getElementById('showReportModal').addEventListener('click', function (e) {
            if (e.target === this) closeShowReportModal();
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !document.getElementById('showReportModal').classList.contains('hidden')) {
                closeShowReportModal();
            }
        });
    });
</script>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this followup?");
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.open-images-popup');
        const popup = document.getElementById('imagesPopup');
        const patientPhotos = document.getElementById('patientPhotos');
        const labReports = document.getElementById('labReports');
        const noPatientPhotos = document.getElementById('noPatientPhotos');
        const noLabReports = document.getElementById('noLabReports');
        const closePopup = document.getElementById('closePopup');

        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const uploads = JSON.parse(this.getAttribute('data-uploads'));

                // Clear previous content
                patientPhotos.innerHTML = '';
                labReports.innerHTML = '';
                noPatientPhotos.classList.add('hidden');
                noLabReports.classList.add('hidden');

                // Group images by type
                const patientImages = uploads.filter(upload => upload.type === 'patient_photo');
                const labImages = uploads.filter(upload => upload.type === 'lab_report');

                // Populate Patient Photos
                if (patientImages.length > 0) {
                    patientImages.forEach(image => {
                        const img = document.createElement('img');
                        img.src = image.url;
                        img.classList.add('w-full', 'h-auto', 'rounded', 'border');
                        patientPhotos.appendChild(img);
                    });
                } else {
                    noPatientPhotos.classList.remove('hidden');
                }

                // Populate Lab Reports
                if (labImages.length > 0) {
                    labImages.forEach(image => {
                        const img = document.createElement('img');
                        img.src = image.url;
                        img.classList.add('w-full', 'h-auto', 'rounded', 'border');
                        labReports.appendChild(img);
                    });
                } else {
                    noLabReports.classList.remove('hidden');
                }

                // Show popup
                popup.classList.remove('hidden');
            });
        });

        closePopup.addEventListener('click', function() {
            popup.classList.add('hidden');
        });

        // Profile Picture Modal Functions
        window.openProfilePictureModal = function(imageUrl) {
            if (!imageUrl) {
                // No image to show, just return
                return;
            }
            const modal = document.getElementById('profilePictureModal');
            const image = document.getElementById('profilePictureImage');
            image.src = imageUrl;
            modal.classList.remove('hidden');
        };

        document.getElementById('closeProfilePictureModal').addEventListener('click', function() {
            document.getElementById('profilePictureModal').classList.add('hidden');
        });

        // Edit Profile Picture Modal Functions
        window.openEditProfilePictureModal = function() {
            document.getElementById('editProfilePictureModal').classList.remove('hidden');
        };

        window.closeEditProfilePictureModal = function() {
            document.getElementById('editProfilePictureModal').classList.add('hidden');
            // Reset camera if open
            const cameraModal = document.getElementById('cameraModal');
            if (cameraModal && !cameraModal.classList.contains('hidden')) {
                cameraModal.classList.add('hidden');
                stopCamera();
            }
        };

        document.getElementById('closeEditProfilePictureModal').addEventListener('click', function() {
            closeEditProfilePictureModal();
        });

        // Add form submit handler to ensure captured files are included
        const editProfileForm = document.querySelector('#editProfilePictureModal form');
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', function(e) {
                // Ensure captured camera files are added to the form before submission
                updateFileInput();
            });
        }

        // Camera functionality for profile picture
        let cameraStream = null;
        let capturedFiles = [];

        const cameraModal = document.getElementById("cameraModal");
        const openCameraModal = document.getElementById("openCameraModal");
        const closeCameraModalBtn = document.getElementById("closeCameraModal");
        const captureBtn = document.getElementById("captureBtn");
        const patientPhotosImages = document.getElementById("patientPhotosImages");
        const video = document.getElementById("cameraPreview");
        const cameraSelect = document.getElementById("cameraSelect");
        const photoFileInput = document.getElementById("photoFileInput");
        const photoTypesInput = document.getElementById("photoTypesInput");

        if (openCameraModal) {
            openCameraModal.addEventListener("click", async (e) => {
                e.preventDefault();
                cameraModal.classList.remove("hidden");
                await loadCameras();
            });
        }

        if (closeCameraModalBtn) {
            closeCameraModalBtn.addEventListener("click", (e) => {
                e.preventDefault();
                updateFileInput();
                cameraModal.classList.add("hidden");
                stopCamera();
            });
        }

        if (captureBtn) {
            captureBtn.addEventListener("click", (e) => {
                e.preventDefault();
                const canvas = document.createElement("canvas");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    const file = new File([blob], `profile_photo_${Date.now()}.png`, { type: "image/png" });

                    // Add to capturedFiles array
                    capturedFiles.push({ file, type: "patient_photo" });

                    // Create preview container
                    const previewContainer = document.createElement("div");
                    previewContainer.classList.add("preview-container", "relative", "inline-block", "m-1");

                    // Create image preview
                    const img = document.createElement("img");
                    img.src = URL.createObjectURL(blob);
                    img.classList.add("w-24", "h-24", "object-cover", "rounded", "border", "border-gray-300");

                    // Create delete button
                    const deleteBtn = document.createElement("button");
                    deleteBtn.innerHTML = "✖";
                    deleteBtn.classList.add("absolute", "top-0", "right-0", "bg-red-500", "text-white", "rounded-full", "w-5", "h-5", "text-xs", "flex", "items-center", "justify-center");
                    deleteBtn.addEventListener("click", () => {
                        const index = capturedFiles.findIndex(f => f.file === file);
                        if (index !== -1) capturedFiles.splice(index, 1);
                        previewContainer.remove();
                    });

                    // Append elements
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(deleteBtn);
                    patientPhotosImages.appendChild(previewContainer);
                }, "image/png");
            });
        }

        async function loadCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === "videoinput");
                if (videoDevices.length === 0) {
                    alert("No cameras found.");
                    return;
                }
                cameraSelect.innerHTML = "";
                videoDevices.forEach((device, index) => {
                    const option = document.createElement("option");
                    option.value = device.deviceId;
                    option.text = device.label || `Camera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                await startCamera(videoDevices[0]?.deviceId);
            } catch (error) {
                console.error("Error loading cameras:", error);
                alert("Failed to access camera. Please allow permissions.");
            }
        }

        async function startCamera(deviceId) {
            stopCamera();
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        deviceId: deviceId ? { exact: deviceId } : undefined
                    }
                });
                video.srcObject = cameraStream;
                video.play();
            } catch (error) {
                console.error("Error starting camera:", error);
                alert("Camera access denied or unavailable.");
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        if (cameraSelect) {
            cameraSelect.addEventListener("change", () => {
                startCamera(cameraSelect.value);
            });
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            const types = [];

            capturedFiles.forEach(({ file, type }) => {
                dataTransfer.items.add(file);
                types.push(type);
            });

            if (photoFileInput) {
                photoFileInput.files = dataTransfer.files;
            }
            if (photoTypesInput) {
                photoTypesInput.value = JSON.stringify(types);
            }
        }

    });
</script>

<style>
    @media print, (max-width: 0) {
        .pdf-content {
            box-shadow: none !important;
            margin: 0 !important;
