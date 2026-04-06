<x-app-layout>
<?php
if (!function_exists('indFormat')) {
    function indFormat($num) {
        $num = round((float)$num);
        return preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))/i', '\1,', (string)$num);
    }
}
?>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('messages.Ledger') }}
        </h2>
    </x-slot>

    <div class="py-2 md:py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-3 md:p-5">

                                {{-- Filters Section --}}
                <form method="GET" action="{{ route('followups.index') }}" id="follow_ups"
                    class="mb-4 bg-gray-50 dark:bg-gray-800/50 p-3 sm:p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-3">

                    {{-- Top Row: Quick Periods & Action Buttons --}}
                    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Quick Period
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <input type="hidden" id="time_period" name="time_period" value="{{ request('time_period', 'all') }}">

                                @php
                                    $periods = [
                                        'all' => 'All',
                                        'today' => 'Today',
                                        'last_week' => 'Last Week',
                                        'this_month' => 'This Month',
                                        'last_month' => 'Last Month',
                                        'last_3_months' => '3 Months',
                                        'last_6_months' => '6 Months',
                                        'last_12_months' => '12 Months',
                                    ];
                                    $currentPeriod = request('time_period', 'all');
                                @endphp

                                @foreach($periods as $key => $label)
                                    <button type="button" onclick="setTimePeriod('{{ $key }}', this)"
                                        class="period-btn px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200 border shadow-sm
                                        {{ $currentPeriod == $key
                                            ? 'bg-indigo-600 border-indigo-600 text-white ring-1 ring-indigo-300 dark:ring-indigo-800'
                                            : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 pt-1 xl:pt-0 self-start xl:self-end">
                            <button type="button" onclick="formSubmit();"
                                class="flex items-center gap-1.5 px-5 py-2 text-sm bg-indigo-600 text-white font-semibold rounded-md shadow hover:bg-indigo-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                            <button type="button" id="exportCSV" onclick="csvExport();"
                                class="flex items-center gap-1.5 px-4 py-2 text-sm bg-green-600 text-white font-semibold rounded-md shadow hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                CSV
                            </button>
                        </div>
                    </div>

                    <div class="h-px bg-gray-200 dark:bg-gray-700 w-full"></div>

                    {{-- Bottom Row: Custom Controls --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                        <div class="flex flex-col relative">
                            <label for="from_date" class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>From Date:</label>
                            <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2.5 py-1.5 text-sm bg-white dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        </div>

                        <div class="flex flex-col relative">
                            <label for="to_date" class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>To Date:</label>
                            <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2.5 py-1.5 text-sm bg-white dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        </div>

                        <div class="flex flex-col relative">
                            <label for="branch_name" class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>Branch:</label>
                            <select id="branch_name" name="branch_name"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2.5 py-1.5 text-sm bg-white dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                                <option value="all" {{ request('branch_name') == 'all' ? 'selected' : '' }}>All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch }}" {{ request('branch_name') == $branch ? 'selected' : '' }}>
                                        {{ $branch }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col relative">
                            <label for="doctor" class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>Reporting Doctor:</label>
                            <select id="doctor" name="doctor"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-2.5 py-1.5 text-sm bg-white dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                                <option value="all" {{ request('doctor') == 'all' ? 'selected' : '' }}>All Doctors</option>
                                @php
                                    $doctorNames = \DB::table('follow_ups')
                                        ->select('check_up_info')
                                        ->get()
                                        ->map(function($fu) {
                                            $data = json_decode($fu->check_up_info, true);
                                            return $data['user_name'] ?? null;
                                        })
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach ($doctorNames as $doctorName)
                                    <option value="{{ $doctorName }}" {{ request('doctor') == $doctorName ? 'selected' : '' }}>
                                        {{ $doctorName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                {{-- Insights Section --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl shadow-sm p-4 mb-4 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        {{ __('messages.Insights') }}
                    </h3>

                    {{-- Filter Summary Section --}}
                    <div class="bg-indigo-50/50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 p-3 mb-3 rounded-r-md flex flex-col sm:flex-row sm:items-center gap-3">
                        <h4 class="text-sm font-bold text-indigo-800 dark:text-indigo-300 m-0 uppercase tracking-wide whitespace-nowrap">Filter Summary:</h4>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700 dark:text-gray-300">
                            @php
                                $filterSummary = [];

                                if (request('time_period') && request('time_period') != 'all') {
                                    $filterSummary[] = [
                                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                        'label' => 'Period',
                                        'value' => match (request('time_period')) {
                                            'today' => 'Today',
                                            'last_week' => 'Last Week',
                                            'this_month' => 'This Month',
                                            'last_month' => 'Last Month',
                                            'last_3_months' => 'Last 3 Months',
                                            'last_6_months' => 'Last 6 Months',
                                            'last_12_months' => 'Last 12 Months',
                                            default => 'Unknown',
                                        },
                                    ];
                                } elseif (request('from_date') || request('to_date')) {
                                    $from = request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'Start';
                                    $to = request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'End';
                                    $filterSummary[] = [
                                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
                                        'label' => 'Date Range',
                                        'value' => "$from → $to",
                                    ];
                                }

                                if (request('branch_name') && request('branch_name') != 'all') {
                                    $filterSummary[] = [
                                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
                                        'label' => 'Branch',
                                        'value' => request('branch_name'),
                                    ];
                                }

                                if (request('doctor') && request('doctor') != 'all') {
                                    $filterSummary[] = [
                                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
                                        'label' => 'Doctor',
                                        'value' => request('doctor'),
                                    ];
                                }
                            @endphp

                            @if (empty($filterSummary))
                                <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400 italic">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    No filters applied (showing all records)
                                </div>
                            @else
                                @foreach ($filterSummary as $filter)
                                    <div class="flex items-center gap-1.5 bg-white dark:bg-gray-800 px-2 py-1 rounded-md border border-indigo-100 dark:border-indigo-900/30 shadow-sm">
                                        <span class="text-indigo-500">{!! $filter['icon'] !!}</span>
                                        <span class="font-medium text-gray-600 dark:text-gray-400">{{ $filter['label'] }}:</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $filter['value'] }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
                        {{-- <div
                            class="p-4 rounded-lg bg-gradient-to-br from-yellow-200 to-gray-50 dark:from-gray-800 dark:to-gray-900 shadow
                    transition-all duration-300 ease-in-out hover:bg-gradient-to-bl hover:from-gray-50 hover:to-yellow-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">🏢
                                {{ __('messages.Branch') }}</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-gray-100">
                                {{ request('branch_name') ? request('branch_name') : __('messages.All Branches') }}
                            </p>
                        </div> --}}

                        {{-- <div
                            class="p-4 rounded-lg bg-gradient-to-br from-orange-200 to-gray-50 dark:from-gray-800 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-gray-50 hover:to-orange-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">📆
                                {{ __('messages.Selected Date Range') }}</p>
                            <p class="text-base font-bold text-orange-900 dark:text-gray-100">
                                {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All' }}
                                →
                                {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'All' }}
                            </p>
                        </div> --}}



                        <div x-data="{ openPatients: false }" class="h-full">
                            <div @click="openPatients = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-cyan-600 to-blue-500 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-cyan-600 to-blue-500 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Total Patients') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-500 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                {{ $totalPatients }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-cyan-50 dark:bg-cyan-900/30 border-cyan-100 dark:border-cyan-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">👤</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openPatients" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openPatients = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-cyan-50 dark:bg-cyan-900/30">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>👤</span> Patient Log
                                        </h3>
                                        <button @click="openPatients = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Patient Name</th>
                                                    <th class="px-5 py-3 font-semibold text-right">Phone</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($patientsList as $index => $patient)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3 font-medium">{{ $patient->name }}</td>
                                                        <td class="px-5 py-3 text-right">{{ $patient->mobile_phone ?? 'N/A' }}</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500 italic">No patients in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-data="{ openFollowups: false }" class="h-full">
                            <div @click="openFollowups = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-600 to-fuchsia-500 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-purple-600 to-fuchsia-500 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Total Follow-ups') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-fuchsia-500 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                {{ $totalFollowUps }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-purple-50 dark:bg-purple-900/30 border-purple-100 dark:border-purple-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">🔄</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openFollowups" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openFollowups = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-purple-50 dark:bg-purple-900/30">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>🔄</span> Follow-ups Log
                                        </h3>
                                        <button @click="openFollowups = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Date</th>
                                                    <th class="px-5 py-3 font-semibold">Patient</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($allFollowUpsList as $index => $fu)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3">{{ $fu->created_at->format('d M Y') }}</td>
                                                        <td class="px-5 py-3 font-medium">
<a target="_blank" href="{{ route('patients.show', $fu->patient_id) }}" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">{{ optional($fu->patient)->name ?? 'Unknown' }}</a>
</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500 italic">No follow-ups in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-data="{ openIncome: false }" class="h-full">
                            <div @click="openIncome = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-green-600 to-emerald-500 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-green-600 to-emerald-500 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Total Payment Received') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-500 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                ₹{{ indFormat($totalIncome) }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-green-50 dark:bg-green-900/30 border-green-100 dark:border-green-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">💰</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openIncome" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openIncome = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-green-50 dark:bg-green-900/30">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>💰</span> Income Log
                                        </h3>
                                        <button @click="openIncome = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Date</th>
                                                    <th class="px-5 py-3 font-semibold">Patient</th>
                                                    <th class="px-5 py-3 font-semibold text-right">Amount Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($paidFollowUpsList as $index => $fu)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3">{{ $fu->created_at->format('d M Y') }}</td>
                                                        <td class="px-5 py-3 font-medium">
<a target="_blank" href="{{ route('patients.show', $fu->patient_id) }}" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">{{ optional($fu->patient)->name ?? 'Unknown' }}</a>
</td>
                                                        <td class="px-5 py-3 text-right font-bold text-green-600 dark:text-green-400">₹{{ indFormat($fu->amount_paid) }}</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 italic">No payments received in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ openCash: false }" class="h-full">
                            <div @click="openCash = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-teal-600 to-emerald-400 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-teal-600 to-emerald-400 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Cash Payments') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-400 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                ₹{{ indFormat($cashPayments) }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-teal-50 dark:bg-teal-900/30 border-teal-100 dark:border-teal-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">💵</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openCash" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openCash = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-teal-50 dark:bg-teal-900/30">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>💵</span> Cash Payments Log
                                        </h3>
                                        <button @click="openCash = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Date</th>
                                                    <th class="px-5 py-3 font-semibold">Patient</th>
                                                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($cashFollowUps as $index => $fu)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3">{{ $fu->created_at->format('d M Y') }}</td>
                                                        <td class="px-5 py-3 font-medium">
<a target="_blank" href="{{ route('patients.show', $fu->patient_id) }}" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">{{ optional($fu->patient)->name ?? 'Unknown' }}</a>
</td>
                                                        <td class="px-5 py-3 text-right font-bold text-teal-600 dark:text-teal-400">₹{{ indFormat($fu->amount_paid) }}</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 italic">No cash payments in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ openDue: false }" class="h-full">
                            <div @click="openDue = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-red-600 to-orange-500 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-red-600 to-orange-500 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Total Outstanding Balance') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                ₹{{ indFormat($totalDueAll) }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-red-50 dark:bg-red-900/30 border-red-100 dark:border-red-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">⚠️</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openDue" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openDue = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-red-50 dark:bg-red-900/30">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                <span>⚠️</span> Due Balance Log
                                            </h3>
                                            <a href="{{ route('patient-dues.index') }}" class="text-xs bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 px-3 py-1 rounded-full font-semibold hover:bg-red-200 transition-colors">
                                                Manage Patient Dues ➡️
                                            </a>
                                        </div>
                                        <button @click="openDue = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Date</th>
                                                    <th class="px-5 py-3 font-semibold">Patient</th>
                                                    <th class="px-5 py-3 font-semibold text-right">Visit Bill</th><th class="px-5 py-3 font-semibold text-right">Account Net</th><th class="px-5 py-3 font-bold text-right text-red-600 dark:text-red-500">Actual Due</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($dueFollowUpsList as $index => $fu)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3">{{ $fu->created_at->format('d M Y') }}</td>
                                                        <td class="px-5 py-3 font-medium">
<a target="_blank" href="{{ route('patients.show', $fu->patient_id) }}" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">{{ optional($fu->patient)->name ?? 'Unknown' }}</a>
</td>
                                                        <td class="px-5 py-3 text-right font-medium text-gray-600 dark:text-gray-400">₹{{ indFormat($fu->amount_billed - $fu->amount_paid) }}</td>
<td class="px-5 py-3 text-right font-medium text-blue-600 dark:text-blue-400">₹{{ indFormat($patientBalances[$fu->patient_id] ?? 0) }}</td>
<td class="px-5 py-3 text-right font-bold text-red-600 dark:text-red-500 text-base">₹{{ indFormat($fu->real_due ?? 0) }}</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500 italic">No missing dues in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="sticky bottom-0 z-20"><tr><td colspan="6" class="p-0 border-0">
    <div class="bg-gray-50 dark:bg-gray-800/95 border-t-2 border-gray-200 dark:border-gray-700 p-5 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">

            <!-- Left Side: Explanation -->
            <div class="text-xs text-gray-500 dark:text-gray-400 max-w-sm space-y-2">
                <h4 class="font-bold text-gray-700 dark:text-gray-200 flex items-center gap-1"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> How is this calculated?</h4>
                <p>We sum up all outstanding visit bills, but automatically deduct any <strong class="text-green-600 dark:text-green-500">Advance Payments</strong> those specific patients already hold in their accounts to give you the true outstanding amount.</p>
            </div>

            @php
                $grossDues = $dueFollowUpsList->sum(fn($fu) => $fu->amount_billed - $fu->amount_paid);
                $advApplied = $grossDues - $totalDueAll;
                $advancesWithinDue = $dueFollowUpsList->unique('patient_id')->sum(function($fu) use ($patientBalances) {
                    return ($patientBalances[$fu->patient_id] ?? 0) < 0 ? abs($patientBalances[$fu->patient_id]) : 0;
                });
            @endphp

            <!-- Right Side: The Math -->
            <div class="flex flex-col gap-2 w-full md:w-auto min-w-[320px]">
                <!-- Line 1: Gross -->
                <div class="flex justify-between items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                    <span>Total Visit Bills (Raw):</span>
                    <span>₹{{ indFormat($grossDues) }}</span>
                </div>

                <!-- Line 2: Advances -->
                <div class="flex justify-between items-center text-sm font-medium text-green-600 dark:text-green-400 border-b border-gray-300 dark:border-gray-600 pb-2">
                    <span>Minus (-) Advances Used:</span>
                    <span>- ₹{{ indFormat($advApplied) }}</span>
                </div>

                <!-- Line 3: Net Real Outstanding -->
                <div class="flex justify-between items-center text-lg font-black text-red-600 dark:text-red-400 pt-1">
                    <span>Real Outstanding Owed:</span>
                    <span class="bg-red-100 dark:bg-red-900/40 px-3 py-1 rounded shadow-sm border border-red-200 dark:border-red-800">₹{{ indFormat($totalDueAll) }}</span>
                </div>

                @if($advancesWithinDue > 0)
                <div class="text-right mt-1">
                    <span class="text-[11px] font-medium text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-full border border-green-200 dark:border-green-800">
                        Total advances sitting in these accounts: ₹{{ indFormat($advancesWithinDue) }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    </td></tr></tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ openOnline: false }" class="h-full">
                            <div @click="openOnline = true" class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer h-full flex flex-col justify-between overflow-hidden isolate" tabindex="0">

    <!-- Animated Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-pink-600 to-rose-500 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>

    <!-- Immersive Glow Blob Behind Icon on Hover -->
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-pink-600 to-rose-500 opacity-0 group-hover:opacity-10 blur-2xl rounded-full transition-opacity duration-500 pointer-events-none z-0"></div>

    <div class="flex justify-between items-start mb-3 relative z-10">
        <div class="pr-2">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-1 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-300">{{ __('messages.Online Payments') }}</h3>
            <!-- Added group-hover:brightness-50 to make the numbers dramatically darker on hover -->
            <p class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-rose-500 truncate break-words transform origin-left group-hover:scale-105 filter group-hover:brightness-75 dark:group-hover:brightness-125 transition-all duration-300">
                ₹{{ indFormat($onlinePayments) }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-pink-50 dark:bg-pink-900/30 border-pink-100 dark:border-pink-800 border shadow-inner group-hover:bg-white dark:group-hover:bg-gray-800 transition-colors duration-300">
            <span class="text-lg drop-shadow-sm transform group-hover:scale-125 group-hover:rotate-6 transition-all duration-300 ease-out">💳</span>
        </div>
    </div>

    <div class="flex items-center text-xs font-medium text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300 relative z-10">
        <span>View detailed log</span>
        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </div>
</div>
                            <div x-show="openOnline" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4" style="display: none;">
                                <div @click.away="openOnline = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[80vh] overflow-hidden">
                                    <div class="px-5 py-4 border-b dark:border-gray-700 flex justify-between items-center bg-pink-50 dark:bg-pink-900/30">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>💳</span> Online Payments Log
                                        </h3>
                                        <button @click="openOnline = false" class="text-gray-500 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-800 rounded-full shadow-sm hover:shadow">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="p-0 overflow-y-auto flex-1">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-5 py-3 font-semibold w-16 text-center">#</th>
                                                    <th class="px-5 py-3 font-semibold">Date</th>
                                                    <th class="px-5 py-3 font-semibold">Patient</th>
                                                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y border-t dark:border-gray-700">
                                                @forelse($onlineFollowUps as $index => $fu)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">
                                                        <td class="px-5 py-3 text-gray-500 text-center">{{ $loop->iteration }}</td>
                                                        <td class="px-5 py-3">{{ $fu->created_at->format('d M Y') }}</td>
                                                        <td class="px-5 py-3 font-medium">
<a target="_blank" href="{{ route('patients.show', $fu->patient_id) }}" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">{{ optional($fu->patient)->name ?? 'Unknown' }}</a>
</td>
                                                        <td class="px-5 py-3 text-right font-bold text-pink-600 dark:text-pink-400">₹{{ indFormat($fu->amount_paid) }}</td>
                                                    </tr>
                                                @empty
                                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 italic">No online payments in this period.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- <a href="{{ route('patient-dues.index') }}" title="View all patient dues"
                        class=" flex justify-center">
                        <div
                            class="p-2 mt-3 w-full text-center rounded-lg bg-gradient-to-br from-red-200 to-white dark:from-red-900 dark:to-gray-900 shadow
        transition-all duration-300 ease-in-out hover:from-red-300 hover:to-white hover:shadow-xl hover:scale-105 cursor-pointer focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">

                            <p
                                class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center justify-center gap-1">
                                ⚠️ {{ __('messages.Total Outstanding Balance') }}
                                <span class="text-xs text-gray-500">➡️</span>
                            </p>

                            <p class="text-lg font-bold text-red-600 dark:text-red-300">
                                ₹{{ indFormat($totalDueAll) }}
                            </p>

                            <p class="text-xs text-gray-500 dark:text-gray-400">Click to view details</p>
                        </div>
                    </a> --}}


                </div>

                {{-- Charts Section --}}
                {{-- <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-5 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 {{ __('messages.Analysis') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Follow-Up Frequency (Daily) -->
                        <div>
                            <canvas id="followUpFrequencyDailyChart" height="220"></canvas>
                        </div>
                        <!-- Follow-Up Frequency (Monthly) -->
                        <div>
                            <canvas id="followUpFrequencyMonthlyChart" height="220"></canvas>
                        </div>
                        <!-- Follow-Up Frequency (Yearly) -->
                        <div>
                            <canvas id="followUpFrequencyYearlyChart" height="220"></canvas>
                        </div>
                        <!-- Payment Status -->
                        <div class="md:col-span-2">
                            <canvas id="paymentStatusChart" height="120"></canvas>
                        </div>
                        <div class="flex justify-center items-start gap-36 w-full md:col-span-2">
                            <!-- New vs. Existing Patients -->
                            <div class="flex-1 max-w-md">
                                <div class="h-[450px] w-full">
                                    <canvas id="newVsExistingPatientsChart" height="100"></canvas>
                                </div>
                            </div>

                            <!-- Age Distribution -->
                            <div class="flex-1 max-w-md">
                                <div class="h-[450px] w-full">
                                    <canvas id="ageDistributionChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div> --}}




                {{-- Table with Infinite Scroll --}}
                <div x-data="{
                    currentPage: {{ (int) $followUps->currentPage() }},
                    isLoading: false,
                    hasMore: @json($followUps->hasMorePages()),
                    totalCount: {{ (int) $followUps->total() }},
                    shownCount: {{ (int) ($followUps->lastItem() ?? 0) }},
                    remainingCount: {{ max(0, (int) $followUps->total() - (int) ($followUps->lastItem() ?? 0)) }},
                    init() {
                        // Monitor form submission to reset pagination
                        const form = document.getElementById('follow_ups');
                        if (form) {
                            const originalSubmit = form.onsubmit;
                            form.onsubmit = (e) => {
                                this.resetPagination();
                                if (originalSubmit) return originalSubmit(e);
                            };
                        }
                    },
                    resetPagination() {
                        this.currentPage = 1;
                        this.hasMore = true;
                        this.isLoading = false;
                        this.totalCount = {{ (int) $followUps->total() }};
                        this.shownCount = {{ (int) ($followUps->lastItem() ?? 0) }};
                        this.remainingCount = {{ max(0, (int) $followUps->total() - (int) ($followUps->lastItem() ?? 0)) }};
                    },
                    progressPercent() {
                        if (!this.totalCount) return 0;
                        return Math.min(100, Math.round((this.shownCount / this.totalCount) * 100));
                    },
                    loadMore() {
                        if (this.isLoading || !this.hasMore) return;

                        this.isLoading = true;
                        this.currentPage++;

                        const params = new URLSearchParams({
                            page: this.currentPage,
                            branch_name: document.getElementById('branch_name')?.value || 'all',
                            doctor: document.getElementById('doctor')?.value || 'all',
                            time_period: document.getElementById('time_period')?.value || 'all',
                            from_date: document.getElementById('from_date')?.value || '',
                            to_date: document.getElementById('to_date')?.value || ''
                        });

                        fetch('{{ route('followups.fetch') }}?' + params.toString())
                            .then(response => response.json())
                            .then(data => {
                                const tbody = document.getElementById('followups-main-tbody');
                                if (tbody && data.html) {
                                    tbody.innerHTML += data.html;
                                }
                                this.hasMore = data.hasMore;
                                this.currentPage = data.currentPage;
                                this.totalCount = Number(data.total ?? this.totalCount);
                                this.shownCount = Number(data.shownCount ?? this.shownCount);
                                this.remainingCount = Number(data.remainingCount ?? Math.max(0, this.totalCount - this.shownCount));
                                this.isLoading = false;
                            })
                            .catch(error => {
                                console.error('Error loading more:', error);
                                this.isLoading = false;
                            });
                    }
                }" class="overflow-x-auto" x-cloak>
                    {{-- Results status --}}
                    <div class="mb-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Showing <span class="text-indigo-600 dark:text-indigo-400" x-text="shownCount"></span>
                                of <span x-text="totalCount"></span> records
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Remaining: <span class="font-semibold" x-text="remainingCount"></span>
                            </p>
                        </div>
                        <div class="mt-2 h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 transition-all duration-300" :style="`width: ${progressPercent()}%`"></div>
                        </div>
                    </div>

                    <table id="followups-main-table" class="w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('messages.Created At') }} 📅</th>
                                <th class="px-4 py-3 text-center font-semibold">{{ __('messages.Patient Name') }} 👤
                                </th>
                                <th class="px-4 py-3 text-center font-semibold">{{ __('messages.doctor') }} 👤</th>
                                <th class="px-4 py-3 text-center font-semibold">{{ __('messages.Amount Billed') }}
                                <th class="px-4 py-3 text-center font-semibold">💳{{ __('messages.Payment Method') }}
                                </th>
                                <th class="px-4 py-3 text-right font-semibold"> 💰{{ __('messages.Amount Paid') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody id="followups-main-tbody" class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                            @foreach ($followUps as $followUp)
                                @if ($followUp->patient)
                                    <tr
                                        class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 animate-fadeIn">
                                        <td class="text-left px-4 py-3">
                                            {{ $followUp->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="text-center px-4 py-3">
                                            <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                                class="
                                                    font-semibold hover:underline
                                                    {{ $followUp->amount_paid < $followUp->amount_billed ? 'text-red-600 dark:text-red-400' : 'text-indigo-700 dark:text-indigo-400' }}
                                                ">
                                                {{ $followUp->patient->name }}
                                            </a>
                                        </td>

                                        <td class="text-center px-4 py-3">
                                            @php
                                                $checkUpInfo = json_decode($followUp->check_up_info, true);
                                            @endphp
                                            {{ $checkUpInfo['user_name'] ?? 'N/A' }}
                                        </td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            ₹{{ indFormat(@$followUp->amount_billed) }}
                                        </td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            {{ @json_decode($followUp->check_up_info)->payment_method }}
                                        </td>
                                        <td
                                            class="text-right px-4 py-3 font-semibold
                                            {{ $followUp->amount_paid < $followUp->amount_billed
                                                ? 'text-red-600 dark:text-red-400'
                                                : ($followUp->amount_paid > $followUp->amount_billed
                                                    ? 'text-green-600 dark:text-green-300'
                                                    : 'text-blue-600 dark:text-blue-300') }}">
                                            ₹{{ indFormat(@$followUp->amount_paid) }}
                                        </td>

                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                    </table>

                    {{-- No results message --}}
                    @if($followUps->count() === 0)
                    <div class="py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12H3m0 0l6-6m-6 6l-6-6m18 0H15m0 0l6 6m-6-6l-6 6m0 12H3m0 0l6-6m-6 6l-6-6m18 0h-6m0 0l6 6m-6-6l-6 6"></path></svg>
                        <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">No records found</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters</p>
                    </div>
                    @endif

                    {{-- Bottom progress (near action button for better context) --}}
                    <div class="pt-4" x-show="totalCount > 0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Progress: <span class="font-semibold" x-text="shownCount"></span>/<span class="font-semibold" x-text="totalCount"></span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="progressPercent() + '%'">0%</p>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 transition-all duration-300" :style="`width: ${progressPercent()}%`"></div>
                        </div>
                    </div>

                    {{-- Manual loader --}}
                    <div class="pt-4 text-center" x-show="hasMore">
                        <button type="button" @click="loadMore()"
                            :disabled="isLoading"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-md border border-indigo-300 text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!isLoading">Load more records (<span x-text="Math.min(remainingCount, 15)"></span>)</span>
                            <span x-show="isLoading">Loading...</span>
                        </button>
                    </div>

                    {{-- Loading Indicator --}}
                    <div id="infinite-scroll-sentinel" class="py-8 flex justify-center items-center" :class="{ 'opacity-0': !isLoading }" x-show="isLoading">
                        <div class="flex items-center gap-3">
                            <div class="flex space-x-2">
                                <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                                <div class="w-2 h-2 bg-gradient-to-r from-cyan-500 to-green-500 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                                <div class="w-2 h-2 bg-gradient-to-r from-green-500 to-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400" x-show="isLoading">
                                Loading more records...
                            </span>
                        </div>
                    </div>

                    {{-- End of list message --}}
                    <div class="py-8 text-center transition-all duration-300" :class="{ 'opacity-100': !hasMore, 'opacity-0 pointer-events-none': hasMore }">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                            ✨ You've reached the end of the list
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Added CSS Animations --}}
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out forwards;
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .animate-shimmer {
            background: linear-gradient(
                90deg,
                #f3f4f6 25%,
                #e5e7eb 50%,
                #f3f4f6 75%
            );
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
    </style>

    <script>
        // Form submission functions
        function formSubmit() {
            document.getElementById('follow_ups').action = "{{ route('followups.index') }}";
            document.getElementById('follow_ups').submit();
        }

        function csvExport() {
            document.getElementById('follow_ups').action = "{{ route('followups.export') }}";
            document.getElementById('follow_ups').submit();
        }

        function setTimePeriod(period, btnElement) {
            const timePeriodInput = document.getElementById('time_period');
            const fromDate = document.getElementById('from_date');
            const toDate = document.getElementById('to_date');

            timePeriodInput.value = period;

            if (period !== 'all') {
                const today = new Date();
                let start, end;

                // Handle date math accurately
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth();

                if (period === 'today') {
                    start = new Date(today);
                    end = new Date(today);
                } else if (period === 'last_week') {
                    start = new Date(today);
                    start.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1) - 7);
                    end = new Date(start);
                    end.setDate(start.getDate() + 6);
                } else if (period === 'this_month') {
                    start = new Date(currentYear, currentMonth, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_month') {
                    start = new Date(currentYear, currentMonth - 1, 1);
                    end = new Date(currentYear, currentMonth, 0);
                } else if (period === 'last_3_months') {
                    start = new Date(currentYear, currentMonth - 2, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_6_months') {
                    start = new Date(currentYear, currentMonth - 5, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_12_months') {
                    start = new Date(currentYear, currentMonth - 11, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                }

                const fmt = d => {
                    let m = (d.getMonth() + 1).toString().padStart(2, '0');
                    let day = d.getDate().toString().padStart(2, '0');
                    return d.getFullYear() + '-' + m + '-' + day;
                };

                fromDate.value = fmt(start);
                toDate.value = fmt(end);

                // visually indicate they are managed by the quick filter
                fromDate.classList.add('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
                toDate.classList.add('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
            } else {
                fromDate.value = '';
                toDate.value = '';
                fromDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
                toDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
            }

            // Auto-submit the form to apply filters
            formSubmit();
        }

        // Initialize inputs state directly based on value on load
        function initializeTimePeriod() {
            const timePeriodInput = document.getElementById('time_period');
            const fromDate = document.getElementById('from_date');
            const toDate = document.getElementById('to_date');
            const period = timePeriodInput ? timePeriodInput.value : 'all';

            if (period !== 'all') {
                const today = new Date();
                let start, end;
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth();

                if (period === 'today') {
                    start = new Date(today);
                    end = new Date(today);
                } else if (period === 'last_week') {
                    start = new Date(today);
                    start.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1) - 7);
                    end = new Date(start);
                    end.setDate(start.getDate() + 6);
                } else if (period === 'this_month') {
                    start = new Date(currentYear, currentMonth, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_month') {
                    start = new Date(currentYear, currentMonth - 1, 1);
                    end = new Date(currentYear, currentMonth, 0);
                } else if (period === 'last_3_months') {
                    start = new Date(currentYear, currentMonth - 2, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_6_months') {
                    start = new Date(currentYear, currentMonth - 5, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                } else if (period === 'last_12_months') {
                    start = new Date(currentYear, currentMonth - 11, 1);
                    end = new Date(currentYear, currentMonth + 1, 0);
                }

                const fmt = d => {
                    if(!d) return '';
                    let m = (d.getMonth() + 1).toString().padStart(2, '0');
                    let day = d.getDate().toString().padStart(2, '0');
                    return d.getFullYear() + '-' + m + '-' + day;
                };

                if (!fromDate.value) fromDate.value = fmt(start);
                if (!toDate.value) toDate.value = fmt(end);

                fromDate.classList.add('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
                toDate.classList.add('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
            } else {
                fromDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
                toDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
            }
        }

        // Automatically reset 'time_period' to 'all' if user interacts with custom date fields
        document.addEventListener('DOMContentLoaded', () => {
            const fromDate = document.getElementById('from_date');
            const toDate = document.getElementById('to_date');
            const changeToAll = () => {
                const timePeriodInput = document.getElementById('time_period');
                if (timePeriodInput.value !== 'all') {
                    timePeriodInput.value = 'all';
                    fromDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');
                    toDate.classList.remove('bg-indigo-50', 'text-indigo-700', 'font-semibold', 'border-indigo-300', 'dark:bg-indigo-900/30', 'dark:text-indigo-300', 'dark:border-indigo-700');

                    // Remove active styling from filter buttons
                    document.querySelectorAll('.period-btn').forEach(btn => {
                        btn.classList.remove('bg-indigo-600', 'border-indigo-600', 'text-white', 'ring-1', 'ring-indigo-300', 'dark:ring-indigo-800');
                        btn.classList.add('bg-white', 'dark:bg-gray-800', 'border-gray-200', 'dark:border-gray-600', 'text-gray-600', 'dark:text-gray-300');
                    });

                    // Set the All button to active if it exists
                    const allBtn = document.querySelector(`button[onclick="setTimePeriod('all', this)"]`);
                    if (allBtn) {
                        allBtn.classList.remove('bg-white', 'dark:bg-gray-800', 'border-gray-200', 'dark:border-gray-600', 'text-gray-600', 'dark:text-gray-300');
                        allBtn.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white', 'ring-1', 'ring-indigo-300', 'dark:ring-indigo-800');
                    }
                }
            };
            if(fromDate) fromDate.addEventListener('change', changeToAll);
            if(toDate) toDate.addEventListener('change', changeToAll);

            initializeTimePeriod();
        });
    </script>
</x-app-layout>
