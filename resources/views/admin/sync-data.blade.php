<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sync Data from Online Server') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <div class="flex justify-between items-start">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        @if(session('sync_stats'))
                            <button onclick="showSyncDetails()" class="ml-4 bg-blue-500 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded focus:outline-none">
                                View Details
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Sync Information:</strong> This will import patient and follow-up data from the online server. Only data updated on or after the selected date will be synced. Existing data will be updated if newer.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.sync-data.post') }}" id="sync-form">
                        @csrf

                        <div class="mb-4">
                            <label for="sync_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Date
                            </label>
                            <input type="date"
                                   id="sync_date"
                                   name="sync_date"
                                   value="{{ old('sync_date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   required>
                            @error('sync_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="api_username" class="block text-sm font-medium text-gray-700 mb-2">
                                API Username
                            </label>
                            <input type="text"
                                   id="api_username"
                                   name="api_username"
                                   value="{{ old('api_username') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   required>
                            @error('api_username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="api_password" class="block text-sm font-medium text-gray-700 mb-2">
                                API Password
                            </label>
                            <input type="password"
                                   id="api_password"
                                   name="api_password"
                                   value="{{ old('api_password') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   required>
                            @error('api_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    id="sync-button"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                <span id="button-text">Start Sync</span>
                                <span id="loading-spinner" class="hidden ml-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>

                    <script>
                        document.getElementById('sync-form').addEventListener('submit', function() {
                            const button = document.getElementById('sync-button');
                            const buttonText = document.getElementById('button-text');
                            const spinner = document.getElementById('loading-spinner');

                            button.disabled = true;
                            buttonText.textContent = 'Syncing...';
                            spinner.classList.remove('hidden');
                        });

                        function showSyncDetails() {
                            document.getElementById('syncDetailsModal').classList.remove('hidden');
                        }

                        function closeSyncDetails() {
                            document.getElementById('syncDetailsModal').classList.add('hidden');
                        }

                        // Close modal when clicking outside
                        document.getElementById('syncDetailsModal').addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeSyncDetails();
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Details Modal -->
    <div id="syncDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-xl rounded-lg bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Sync Results</h3>
                            <p class="text-sm text-gray-600">Detailed breakdown of the synchronization process</p>
                        </div>
                    </div>
                    <button onclick="closeSyncDetails()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if(session('sync_stats'))
                    <div class="space-y-6">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Total Patients Card -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Total Patients</h4>
                                        <div class="text-3xl font-bold text-blue-600">
                                            {{ (session('sync_stats')['patients_restored'] ?? 0) + (session('sync_stats')['patients_imported'] ?? 0) + (session('sync_stats')['patients_updated'] ?? 0) + (session('sync_stats')['patients_skipped'] ?? 0) }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records processed</p>
                                    </div>
                                    <div class="text-blue-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Follow-ups Card -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Total Follow-ups</h4>
                                        <div class="text-3xl font-bold text-green-600">
                                            {{ (session('sync_stats')['follow_ups_added'] ?? 0) + (session('sync_stats')['follow_ups_updated'] ?? 0) + (session('sync_stats')['follow_ups_skipped'] ?? 0) }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records processed</p>
                                    </div>
                                    <div class="text-green-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Breakdown -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Patients Section -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">Patients</h4>
                                    </div>

                                    <!-- Patient Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-green-600">{{ session('sync_stats')['patients_restored'] ?? 0 }}</div>
                                                    <div class="text-xs text-green-700 font-medium">Restored</div>
                                                </div>
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-blue-600">{{ session('sync_stats')['patients_imported'] ?? 0 }}</div>
                                                    <div class="text-xs text-blue-700 font-medium">Imported</div>
                                                </div>
                                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-yellow-600">{{ session('sync_stats')['patients_updated'] ?? 0 }}</div>
                                                    <div class="text-xs text-yellow-700 font-medium">Updated</div>
                                                </div>
                                                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-gray-600">{{ session('sync_stats')['patients_skipped'] ?? 0 }}</div>
                                                    <div class="text-xs text-gray-700 font-medium">Skipped</div>
                                                </div>
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Patient Names Details -->
                                    @if(session('sync_stats')['patient_names'] ?? false)
                                        <div class="space-y-3">
                                            @if(!empty(session('sync_stats')['patient_names']['restored'] ?? []))
                                                <div class="bg-green-50 rounded-md p-3 border border-green-200">
                                                    <h5 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                        Restored Patients
                                                    </h5>
                                                    <div class="text-sm text-green-700 bg-white rounded p-2 max-h-24 overflow-y-auto border">
                                                        {{ implode(', ', session('sync_stats')['patient_names']['restored']) }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if(!empty(session('sync_stats')['patient_names']['imported'] ?? []))
                                                <div class="bg-blue-50 rounded-md p-3 border border-blue-200">
                                                    <h5 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        Imported Patients
                                                    </h5>
                                                    <div class="text-sm text-blue-700 bg-white rounded p-2 max-h-24 overflow-y-auto border">
                                                        {{ implode(', ', session('sync_stats')['patient_names']['imported']) }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if(!empty(session('sync_stats')['patient_names']['updated'] ?? []))
                                                <div class="bg-yellow-50 rounded-md p-3 border border-yellow-200">
                                                    <h5 class="text-sm font-semibold text-yellow-800 mb-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Updated Patients
                                                    </h5>
                                                    <div class="text-sm text-yellow-700 bg-white rounded p-2 max-h-24 overflow-y-auto border">
                                                        {{ implode(', ', session('sync_stats')['patient_names']['updated']) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Follow-ups Section -->
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">Follow-ups</h4>
                                    </div>

                                    <!-- Follow-up Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-green-600">{{ session('sync_stats')['follow_ups_added'] ?? 0 }}</div>
                                                    <div class="text-xs text-green-700 font-medium">Added</div>
                                                </div>
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-yellow-600">{{ session('sync_stats')['follow_ups_updated'] ?? 0 }}</div>
                                                    <div class="text-xs text-yellow-700 font-medium">Updated</div>
                                                </div>
                                                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 col-span-2">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-2xl font-bold text-gray-600">{{ session('sync_stats')['follow_ups_skipped'] ?? 0 }}</div>
                                                    <div class="text-xs text-gray-700 font-medium">Skipped</div>
                                                </div>
                                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Follow-up Patient Names Details -->
                                    @if(session('sync_stats')['follow_up_patient_names'] ?? false)
                                        <div class="space-y-3">
                                            @if(!empty(session('sync_stats')['follow_up_patient_names']['added'] ?? []))
                                                <div class="bg-green-50 rounded-md p-3 border border-green-200">
                                                    <h5 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                        Added Follow-ups (Patients)
                                                    </h5>
                                                    <div class="text-sm text-green-700 bg-white rounded p-2 max-h-24 overflow-y-auto border">
                                                        {{ implode(', ', session('sync_stats')['follow_up_patient_names']['added']) }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if(!empty(session('sync_stats')['follow_up_patient_names']['updated'] ?? []))
                                                <div class="bg-yellow-50 rounded-md p-3 border border-yellow-200">
                                                    <h5 class="text-sm font-semibold text-yellow-800 mb-2 flex items-center">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Updated Follow-ups (Patients)
                                                    </h5>
                                                    <div class="text-sm text-yellow-700 bg-white rounded p-2 max-h-24 overflow-y-auto border">
                                                        {{ implode(', ', session('sync_stats')['follow_up_patient_names']['updated']) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-end">
                                <button onclick="closeSyncDetails()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                                    Close Details
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
