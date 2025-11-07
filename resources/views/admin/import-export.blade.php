<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                <h2 class="font-bold text-2xl leading-tight">
                    {{ __('Import & Export Data') }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.export-files') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Manage Export Files</span>
                </a>
                <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 hover:bg-gray-100 font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
    @if(session('success') && str_contains(session('success'), 'Export completed'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center justify-between">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="showExportDetails()" class="ml-4 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    View Details
                </button>
            </div>
        </div>
    @endif            @if(session('success') && str_contains(session('success'), 'Import completed'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center justify-between">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="showImportDetails()" class="ml-4 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    View Details
                </button>
            </div>
        </div>
    @endif            @if(session('error'))
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
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">Import & Export Information</h3>
                                    <p class="text-xs text-gray-700 leading-relaxed">
                                        <strong class="text-indigo-700">Export:</strong> Download patient data and follow-ups as JSON for backup or migration.<br>
                                        <strong class="text-indigo-700">Import:</strong> Upload a previously exported JSON file to restore or merge data.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <span>Export Data</span>
                        </h3>
                        <form action="{{ route('admin.export-data') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-semibold text-gray-800 mb-2">Start Date (optional)</label>
                                    <input type="date" id="start_date" name="start_date" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-semibold text-gray-800 mb-2">End Date (optional)</label>
                                    <input type="date" id="end_date" name="end_date" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <span>Export Data</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Import Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span>Import Data</span>
                        </h3>

                        <form action="{{ route('admin.import-data') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <!-- Import Source Selection -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-800 mb-2">Import Source</label>

                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio" id="source_upload" name="import_source" value="upload" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked onchange="toggleImportSource()">
                                        <label for="source_upload" class="ml-2 block text-sm text-gray-900">
                                            Upload from local computer
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="radio" id="source_storage" name="import_source" value="storage" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" onchange="toggleImportSource()">
                                        <label for="source_storage" class="ml-2 block text-sm text-gray-900">
                                            Select from stored export files
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div id="upload_section" class="space-y-4">
                                <div>
                                    <label for="file" class="block text-sm font-semibold text-gray-800 mb-2">Select JSON File</label>
                                    <input type="file" id="file" name="file" accept=".json" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white" required>
                                    <p class="text-xs text-gray-600 mt-1">Upload a JSON file exported from this system.</p>
                                </div>
                            </div>

                            <!-- Storage Selection Section -->
                            <div id="storage_section" class="space-y-4 hidden">
                                <div>
                                    <label for="storage_file" class="block text-sm font-semibold text-gray-800 mb-2">Select Export File</label>
                                    <select id="storage_file" name="storage_file" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 bg-white">
                                        <option value="">Choose an export file...</option>
                                        @if($exportFiles->isNotEmpty())
                                            @foreach($exportFiles as $file)
                                                <option value="{{ $file['name'] }}">
                                                    {{ $file['name'] }} ({{ $file['size_human'] }} - {{ $file['date'] }} {{ $file['time'] }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No export files available</option>
                                        @endif
                                    </select>
                                    <p class="text-xs text-gray-600 mt-1">Select a previously exported JSON file from storage.</p>
                                    @if($exportFiles->isEmpty())
                                        <p class="text-xs text-amber-600 mt-1">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            No export files found. Create an export first to use this option.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 focus:outline-none focus:ring-4 focus:ring-green-300 transform hover:scale-105 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    <span>Import Data</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Details Modal -->
    <div id="exportDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-xl rounded-lg bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Export Results</h3>
                            <p class="text-sm text-gray-600">Detailed breakdown of the export process</p>
                        </div>
                    </div>
                    <button onclick="closeExportDetails()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if(session('export_stats'))
                    <div class="space-y-6">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Total Patients Card -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Total Patients</h4>
                                        <div class="text-3xl font-bold text-blue-600">
                                            {{ session('export_stats')['total_patients'] }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records exported</p>
                                    </div>
                                    <div class="text-blue-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
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
                                            {{ session('export_stats')['total_follow_ups'] }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records exported</p>
                                    </div>
                                    <div class="text-green-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Details -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                            <div class="p-6">
                                <div class="flex items-center space-x-2 mb-4">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Export Information</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h5 class="font-medium text-gray-700 mb-2">Export Date</h5>
                                        <p class="text-sm text-gray-600">{{ session('export_stats')['export_date'] }}</p>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-700 mb-2">File Name</h5>
                                        <p class="text-sm text-gray-600">{{ session('export_stats')['file_name'] }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h5 class="font-medium text-gray-700 mb-2">Date Range</h5>
                                        <p class="text-sm text-gray-600">
                                            @if(session('export_stats')['date_range']['start'] && session('export_stats')['date_range']['end'])
                                                From {{ session('export_stats')['date_range']['start'] }} to {{ session('export_stats')['date_range']['end'] }}
                                            @elseif(session('export_stats')['date_range']['start'])
                                                From {{ session('export_stats')['date_range']['start'] }} onwards
                                            @elseif(session('export_stats')['date_range']['end'])
                                                Up to {{ session('export_stats')['date_range']['end'] }}
                                            @else
                                                All data (no date filter)
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <a href="{{ route('admin.download-export', session('export_stats')['file_name']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Download File</span>
                                </a>
                                <button onclick="closeExportDetails()" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium">
                                    Close Details
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Details Modal -->
    <div id="importDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-xl rounded-lg bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Import Results</h3>
                            <p class="text-sm text-gray-600">Detailed breakdown of the import process</p>
                        </div>
                    </div>
                    <button onclick="closeImportDetails()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @if(session('import_stats'))
                    <div class="space-y-6">
                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Total Patients Card -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Total Patients Processed</h4>
                                        <div class="text-3xl font-bold text-blue-600">
                                            {{ session('import_stats')['patients_restored'] + session('import_stats')['patients_imported'] + session('import_stats')['patients_updated'] + session('import_stats')['patients_unchanged'] }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records processed</p>
                                    </div>
                                    <div class="text-blue-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Follow-ups Card -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Total Follow-ups Processed</h4>
                                        <div class="text-3xl font-bold text-green-600">
                                            {{ session('import_stats')['follow_ups_added'] + session('import_stats')['follow_ups_updated'] + session('import_stats')['follow_ups_unchanged'] }}
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">Records processed</p>
                                    </div>
                                    <div class="text-green-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">Patients</h4>
                                    </div>

                                    <!-- Patient Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                            <div class="text-2xl font-bold text-yellow-600">{{ session('import_stats')['patients_restored'] }}</div>
                                            <div class="text-sm text-gray-600">Restored</div>
                                        </div>

                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                            <div class="text-2xl font-bold text-green-600">{{ session('import_stats')['patients_imported'] }}</div>
                                            <div class="text-sm text-gray-600">Imported</div>
                                        </div>

                                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                            <div class="text-2xl font-bold text-blue-600">{{ session('import_stats')['patients_updated'] }}</div>
                                            <div class="text-sm text-gray-600">Updated</div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="text-2xl font-bold text-gray-600">{{ session('import_stats')['patients_unchanged'] }}</div>
                                            <div class="text-sm text-gray-600">Unchanged</div>
                                        </div>
                                    </div>

                                    <!-- Patient Names Details -->
                                    @if(!empty(session('import_stats')['patient_names']['restored']) || !empty(session('import_stats')['patient_names']['imported']) || !empty(session('import_stats')['patient_names']['updated']))
                                        <div class="space-y-3">
                                            @if(!empty(session('import_stats')['patient_names']['restored']))
                                                <div>
                                                    <h5 class="font-medium text-yellow-700 mb-1">Restored Patients</h5>
                                                    <p class="text-sm text-gray-600">{{ collect(session('import_stats')['patient_names']['restored'])->take(5)->join(', ') }}{{ count(session('import_stats')['patient_names']['restored']) > 5 ? ', +' . (count(session('import_stats')['patient_names']['restored']) - 5) . ' more' : '' }}</p>
                                                </div>
                                            @endif
                                            @if(!empty(session('import_stats')['patient_names']['imported']))
                                                <div>
                                                    <h5 class="font-medium text-green-700 mb-1">Imported Patients</h5>
                                                    <p class="text-sm text-gray-600">{{ collect(session('import_stats')['patient_names']['imported'])->take(5)->join(', ') }}{{ count(session('import_stats')['patient_names']['imported']) > 5 ? ', +' . (count(session('import_stats')['patient_names']['imported']) - 5) . ' more' : '' }}</p>
                                                </div>
                                            @endif
                                            @if(!empty(session('import_stats')['patient_names']['updated']))
                                                <div>
                                                    <h5 class="font-medium text-blue-700 mb-1">Updated Patients</h5>
                                                    <p class="text-sm text-gray-600">{{ collect(session('import_stats')['patient_names']['updated'])->take(5)->join(', ') }}{{ count(session('import_stats')['patient_names']['updated']) > 5 ? ', +' . (count(session('import_stats')['patient_names']['updated']) - 5) . ' more' : '' }}</p>
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">Follow-ups</h4>
                                    </div>

                                    <!-- Follow-up Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                            <div class="text-2xl font-bold text-green-600">{{ session('import_stats')['follow_ups_added'] }}</div>
                                            <div class="text-sm text-gray-600">Added</div>
                                        </div>

                                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                            <div class="text-2xl font-bold text-blue-600">{{ session('import_stats')['follow_ups_updated'] }}</div>
                                            <div class="text-sm text-gray-600">Updated</div>
                                        </div>

                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 col-span-2">
                                            <div class="text-2xl font-bold text-gray-600">{{ session('import_stats')['follow_ups_unchanged'] }}</div>
                                            <div class="text-sm text-gray-600">Unchanged</div>
                                        </div>
                                    </div>

                                    <!-- Follow-up Patient Names Details -->
                                    @if(!empty(session('import_stats')['follow_up_patient_names']['added']) || !empty(session('import_stats')['follow_up_patient_names']['updated']))
                                        <div class="space-y-3">
                                            @if(!empty(session('import_stats')['follow_up_patient_names']['added']))
                                                <div>
                                                    <h5 class="font-medium text-green-700 mb-1">Follow-ups Added for Patients</h5>
                                                    <p class="text-sm text-gray-600">{{ collect(session('import_stats')['follow_up_patient_names']['added'])->take(5)->join(', ') }}{{ count(session('import_stats')['follow_up_patient_names']['added']) > 5 ? ', +' . (count(session('import_stats')['follow_up_patient_names']['added']) - 5) . ' more' : '' }}</p>
                                                </div>
                                            @endif
                                            @if(!empty(session('import_stats')['follow_up_patient_names']['updated']))
                                                <div>
                                                    <h5 class="font-medium text-blue-700 mb-1">Follow-ups Updated for Patients</h5>
                                                    <p class="text-sm text-gray-600">{{ collect(session('import_stats')['follow_up_patient_names']['updated'])->take(5)->join(', ') }}{{ count(session('import_stats')['follow_up_patient_names']['updated']) > 5 ? ', +' . (count(session('import_stats')['follow_up_patient_names']['updated']) - 5) . ' more' : '' }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Import Details -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                            <div class="p-6">
                                <div class="flex items-center space-x-2 mb-4">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Import Information</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h5 class="font-medium text-gray-700 mb-2">Import Date</h5>
                                        <p class="text-sm text-gray-600">{{ session('import_stats')['import_date'] }}</p>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-700 mb-2">File Name</h5>
                                        <p class="text-sm text-gray-600">{{ session('import_stats')['file_name'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex justify-end">
                                <button onclick="closeImportDetails()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                                    Close Details
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showExportDetails() {
            document.getElementById('exportDetailsModal').classList.remove('hidden');
        }

        function closeExportDetails() {
            document.getElementById('exportDetailsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('exportDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportDetails();
            }
        });

        function showImportDetails() {
            document.getElementById('importDetailsModal').classList.remove('hidden');
        }

        function closeImportDetails() {
            document.getElementById('importDetailsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('importDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportDetails();
            }
        });

        function toggleImportSource() {
            const uploadRadio = document.getElementById('source_upload');
            const storageRadio = document.getElementById('source_storage');
            const uploadSection = document.getElementById('upload_section');
            const storageSection = document.getElementById('storage_section');
            const fileInput = document.getElementById('file');
            const storageSelect = document.getElementById('storage_file');

            if (uploadRadio.checked) {
                uploadSection.classList.remove('hidden');
                storageSection.classList.add('hidden');
                fileInput.required = true;
                storageSelect.required = false;
            } else if (storageRadio.checked) {
                uploadSection.classList.add('hidden');
                storageSection.classList.remove('hidden');
                fileInput.required = false;
                storageSelect.required = true;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleImportSource();
        });
    </script>
</x-app-layout>
