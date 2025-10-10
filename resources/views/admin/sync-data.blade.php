<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <h2 class="font-bold text-2xl leading-tight">
                    {{ __('Sync Data from Online Server') }}
                </h2>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 hover:bg-gray-100 font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-r-lg shadow-md" role="alert">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        @if(session('sync_stats'))
                            <button onclick="showSyncDetails()" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg shadow-md transition duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>View Details</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-r-lg shadow-md" role="alert">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-xl border border-gray-100">
                <div class="p-6 bg-gradient-to-br from-gray-50 to-white border-b border-gray-200">
                    <div class="mb-6">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">Sync Information</h3>
                                    <p class="text-xs text-gray-700 leading-relaxed">
                                        <strong class="text-indigo-700">All Data:</strong> Imports ALL data from the online system (complete sync)<br>
                                        <strong class="text-indigo-700">Time-filtered sync:</strong> Imports data updated within the selected time period (Today, Last Week, Last Month)<br>
                                        <span class="text-gray-600">Existing data will be updated if newer versions are found.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.sync-data.post') }}" id="sync-form">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">
                                Select Time Period
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                <input type="hidden" id="time_period" name="time_period" value="{{ old('time_period', 'all') }}">
                                <button type="button" onclick="setTimePeriod('all')"
                                    class="flex flex-col items-center justify-center h-full px-4 py-3 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('time_period', 'all') == 'all' ? 'bg-indigo-600 text-white border-2 border-indigo-700' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span class="text-xs pointer-events-none">All Data</span>
                                </button>
                                <button type="button" onclick="setTimePeriod('today')"
                                    class="flex flex-col items-center justify-center h-full px-4 py-3 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('time_period') == 'today' ? 'bg-indigo-600 text-white border-2 border-indigo-700' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span class="text-xs pointer-events-none">Today</span>
                                </button>
                                <button type="button" onclick="setTimePeriod('last_3_days')"
                                    class="flex flex-col items-center justify-center h-full px-4 py-3 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('time_period') == 'last_3_days' ? 'bg-indigo-600 text-white border-2 border-indigo-700' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs pointer-events-none">Last 3 Days</span>
                                </button>
                                <button type="button" onclick="setTimePeriod('last_week')"
                                    class="flex flex-col items-center justify-center h-full px-4 py-3 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('time_period') == 'last_week' ? 'bg-indigo-600 text-white border-2 border-indigo-700' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span class="text-xs pointer-events-none">Last Week</span>
                                </button>
                                <button type="button" onclick="setTimePeriod('last_month')"
                                    class="flex flex-col items-center justify-center h-full px-4 py-3 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ old('time_period') == 'last_month' ? 'bg-indigo-600 text-white border-2 border-indigo-700' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 mb-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs pointer-events-none">Last Month</span>
                                </button>
                            </div>
                            <input type="hidden" id="sync_date" name="sync_date" value="{{ old('sync_date') }}">
                            <input type="checkbox" id="sync_all" name="sync_all" value="1" style="display: none;" {{ old('sync_all') ? 'checked' : '' }}>
                            @error('sync_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="api_username" class="block text-sm font-semibold text-gray-800 mb-3">
                                Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="api_username"
                                       name="api_username"
                                       value="{{ old('api_username') }}"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white"
                                       placeholder="Enter your username"
                                       required>
                            </div>
                            @error('api_username')
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="api_password" class="block text-sm font-semibold text-gray-800 mb-3">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password"
                                       id="api_password"
                                       name="api_password"
                                       value="{{ old('api_password') }}"
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white"
                                       placeholder="Enter your password"
                                       required>
                            </div>
                            @error('api_password')
                                <p class="mt-2 text-sm text-red-600 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-center">
                            <button type="submit"
                                    id="sync-button"
                                    class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform hover:scale-105 flex items-center space-x-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span id="button-text">Start Sync</span>
                                <span id="loading-spinner" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Selection Description -->
                    {{-- <div id="selection-description" class="mt-4 p-3 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg border border-indigo-200 shadow-sm">
                        <div class="flex items-start space-x-3">
                            <svg class="w-4 h-4 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-semibold text-indigo-800 mb-1">What will happen:</h4>
                                <p id="description-text" class="text-xs text-indigo-700 leading-relaxed">Please select a time period above to see what will be synchronized.</p>
                            </div>
                        </div>
                    </div> --}}

                    <script>
                        // Toggle date field requirement based on sync_all checkbox
                        document.getElementById('sync_all').addEventListener('change', function() {
                            const dateField = document.getElementById('sync_date');
                            const buttonText = document.getElementById('button-text');
                            if (this.checked) {
                                dateField.removeAttribute('required');
                                dateField.classList.add('opacity-50');
                                buttonText.textContent = 'Start Full Sync';
                            } else {
                                dateField.setAttribute('required', 'required');
                                dateField.classList.remove('opacity-50');
                                buttonText.textContent = 'Start Sync';
                            }
                        });

                        // Initialize on page load
                        document.addEventListener('DOMContentLoaded', function() {
                            const timePeriod = document.getElementById('time_period').value;
                            setTimePeriod(timePeriod, false); // Initialize without triggering form changes
                        });

                        function setTimePeriod(period, updateForm = true) {
                            document.getElementById('time_period').value = period;

                            // Update button styles
                            const buttons = document.querySelectorAll('#sync-form button[type="button"]');
                            buttons.forEach(btn => {
                                btn.className = 'px-4 py-3 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex flex-col items-center space-y-1';
                            });

                            // Highlight selected button
                            event.target.className = 'px-4 py-3 bg-indigo-600 text-white shadow-lg rounded-lg font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex flex-col items-center space-y-1';

                            const syncDateInput = document.getElementById('sync_date');
                            const syncAllCheckbox = document.getElementById('sync_all');
                            const buttonText = document.getElementById('button-text');
                            const descriptionText = document.getElementById('description-text');

                            if (period === 'all') {
                                syncAllCheckbox.checked = true;
                                syncDateInput.value = '';
                                buttonText.textContent = 'Start Full Sync';
                                descriptionText.textContent = 'This will perform a complete synchronization of ALL patient and follow-up data from the online server. This may take longer and will update existing records if newer versions are available.';
                            } else {
                                syncAllCheckbox.checked = false;
                                const today = new Date();
                                let targetDate;
                                let description = '';

                                if (period === 'today') {
                                    targetDate = today;
                                    description = 'This will synchronize data that has been updated today only. Only recent changes from the current day will be imported.';
                                } else if (period === 'last_3_days') {
                                    targetDate = new Date(today);
                                    targetDate.setDate(today.getDate() - 3);
                                    description = 'This will synchronize data updated in the last 3 days. Useful for catching up on recent changes without a full sync.';
                                } else if (period === 'last_week') {
                                    targetDate = new Date(today);
                                    targetDate.setDate(today.getDate() - 7);
                                    description = 'This will synchronize data updated in the last 7 days. A good balance between completeness and sync time.';
                                } else if (period === 'last_month') {
                                    targetDate = new Date(today);
                                    targetDate.setDate(today.getDate() - 30);
                                    description = 'This will synchronize data updated in the last 30 days. Includes more historical data but may take longer.';
                                }

                                syncDateInput.value = targetDate.toISOString().split('T')[0];
                                buttonText.textContent = 'Start Sync';
                                descriptionText.textContent = description;
                            }

                            if (updateForm) {
                                // Optional: Auto-submit or show preview
                            }
                        }

                        document.getElementById('sync-form').addEventListener('submit', function(e) {
                            const button = document.getElementById('sync-button');
                            const buttonText = document.getElementById('button-text');
                            const spinner = document.getElementById('loading-spinner');
                            const isFullSync = document.getElementById('sync_all').checked;

                            button.disabled = true;
                            buttonText.textContent = isFullSync ? 'Full Syncing...' : 'Syncing...';
                            spinner.classList.remove('hidden');

                            // Show progress modal immediately
                            showSyncProgress();

                            // Start progress simulation
                            simulateSyncProgress();
                        });

                        function showSyncProgress() {
                            document.getElementById('syncProgressModal').classList.remove('hidden');
                        }

                        function hideSyncProgress() {
                            document.getElementById('syncProgressModal').classList.add('hidden');
                        }

                        function simulateSyncProgress() {
                            const steps = [
                                { text: 'Connecting to online server...', duration: 2000 },
                                { text: 'Authenticating with API...', duration: 3500 },
                                { text: 'Fetching patients data...', duration: 500 },
                                { text: 'Finalizing sync operation...', duration: 500 },
                                { text: 'Completing sync...', duration: 1000 }
                            ];

                            const progressBar = document.getElementById('progress-bar');
                            const progressText = document.getElementById('progress-text');
                            const stepIndicator = document.getElementById('step-indicator');
                            const stepDots = document.querySelectorAll('#syncProgressModal .flex.items-center.space-x-3 .w-2');
                            const stepTexts = document.querySelectorAll('#syncProgressModal .flex.items-center.space-x-3 span:last-child');

                            let currentStep = 0;
                            let totalDuration = steps.reduce((sum, step) => sum + step.duration, 0);
                            let elapsedTime = 0;

                            function updateProgress() {
                                if (currentStep < steps.length) {
                                    const step = steps[currentStep];
                                    progressText.textContent = step.text;
                                    stepIndicator.textContent = `${currentStep + 1}/${steps.length}`;

                                    // Update step indicators
                                    stepDots.forEach((dot, index) => {
                                        if (index < currentStep) {
                                            dot.className = 'w-2 h-2 bg-green-600 rounded-full';
                                        } else if (index === currentStep) {
                                            dot.className = 'w-2 h-2 bg-blue-600 rounded-full animate-pulse';
                                        } else {
                                            dot.className = 'w-2 h-2 bg-gray-300 rounded-full';
                                        }
                                    });

                                    stepTexts.forEach((text, index) => {
                                        if (index < currentStep) {
                                            text.className = 'text-green-600';
                                        } else if (index === currentStep) {
                                            text.className = 'text-blue-700 font-medium';
                                        } else {
                                            text.className = 'text-gray-400';
                                        }
                                    });

                                    // Calculate progress percentage
                                    const stepProgress = (elapsedTime / totalDuration) * 100;
                                    const baseProgress = (currentStep / steps.length) * 100;
                                    const currentProgress = baseProgress + (stepProgress * (1 / steps.length));
                                    progressBar.style.width = Math.min(currentProgress, 100) + '%';

                                    elapsedTime += 100; // Update every 100ms

                                    // Move to next step when duration is reached
                                    if (elapsedTime >= steps.reduce((sum, s, i) => i <= currentStep ? sum + s.duration : sum, 0)) {
                                        currentStep++;
                                        if (currentStep < steps.length) {
                                            setTimeout(updateProgress, 100);
                                        } else {
                                            // All steps completed, the page will refresh with results
                                            setTimeout(() => {
                                                progressText.textContent = 'Sync completed successfully!';
                                                progressBar.style.width = '100%';
                                                // Mark final step as completed
                                                stepDots[steps.length - 1].className = 'w-2 h-2 bg-green-600 rounded-full';
                                                stepTexts[steps.length - 1].className = 'text-green-600';
                                            }, 500);
                                        }
                                    } else {
                                        setTimeout(updateProgress, 100);
                                    }
                                }
                            }

                            updateProgress();
                        }

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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
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

                        <!-- Errors and Issues Section -->
                        @if((session('sync_stats')['errors'] ?? false) && (!empty(session('sync_stats')['errors']['patients']) || !empty(session('sync_stats')['errors']['follow_ups'])))
                            <div class="bg-red-50 rounded-lg border border-red-200 shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-red-800">Errors & Issues</h4>
                                    </div>

                                    @if(!empty(session('sync_stats')['errors']['patients']))
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-red-700 mb-2">Patient Errors</h5>
                                            <div class="bg-white rounded p-3 border max-h-32 overflow-y-auto">
                                                <ul class="text-sm text-red-600 space-y-1">
                                                    @foreach(session('sync_stats')['errors']['patients'] as $error)
                                                        <li class="flex items-start">
                                                            <span class="text-red-500 mr-2">•</span>
                                                            {{ $error }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty(session('sync_stats')['errors']['follow_ups']))
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-red-700 mb-2">Follow-up Errors</h5>
                                            <div class="bg-white rounded p-3 border max-h-32 overflow-y-auto">
                                                <ul class="text-sm text-red-600 space-y-1">
                                                    @foreach(session('sync_stats')['errors']['follow_ups'] as $error)
                                                        <li class="flex items-start">
                                                            <span class="text-red-500 mr-2">•</span>
                                                            {{ $error }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Background Operations Section -->
                        @if(session('sync_stats')['background_operations'] ?? false)
                            <div class="bg-purple-50 rounded-lg border border-purple-200 shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-purple-800">Background Operations</h4>
                                    </div>
                                    <div class="bg-white rounded p-3 border max-h-40 overflow-y-auto">
                                        <ul class="text-sm text-purple-700 space-y-1">
                                            @foreach(session('sync_stats')['background_operations'] as $operation)
                                                <li class="flex items-start">
                                                    <span class="text-purple-500 mr-2">✓</span>
                                                    {{ $operation }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Sync Logs Section -->
                        @if(session('sync_stats')['sync_logs'] ?? false)
                            <div class="bg-gray-50 rounded-lg border border-gray-200 shadow-sm">
                                <div class="p-6">
                                    <div class="flex items-center space-x-2 mb-4">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">Sync Activity Log</h4>
                                    </div>
                                    <div class="bg-white rounded p-3 border max-h-60 overflow-y-auto">
                                        <div class="text-sm text-gray-700 space-y-1 font-mono">
                                            @foreach(session('sync_stats')['sync_logs'] as $log)
                                                <div class="flex items-start {{ strpos($log, 'ERROR:') === 0 ? 'text-red-600' : (strpos($log, 'WARNING:') === 0 ? 'text-yellow-600' : 'text-gray-600') }}">
                                                    <span class="mr-2 text-xs opacity-75">{{ date('H:i:s') }}</span>
                                                    <span>{{ $log }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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

    <!-- Sync Progress Modal -->
    <div id="syncProgressModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-xl rounded-lg bg-white">
            <div class="mt-3 text-center">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <div class="flex items-center justify-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-12 h-12 text-indigo-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Syncing Data</h3>
                    <p class="text-gray-600">Please wait while we synchronize your data from the online server</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                        <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span id="progress-text" class="text-gray-700 font-medium">Initializing sync...</span>
                        <span id="step-indicator" class="text-gray-500">0/8</span>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="text-left space-y-2 mb-6">
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                        <span class="text-gray-600">Connecting to online server</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <span class="text-gray-400">Authenticating with API</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <span class="text-gray-400">Fetching patient data</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <span class="text-gray-400">Finalizing sync operation</span>
                    </div>
                    <div class="flex items-center space-x-3 text-sm">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <span class="text-gray-400">Completing sync</span>
                    </div>
                </div>

                <!-- Loading Animation -->
                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>

                <!-- Note -->
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-700">
                        <strong>Note:</strong> This process may take a few minutes depending on the amount of data being synchronized.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
