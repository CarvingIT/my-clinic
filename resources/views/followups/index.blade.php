<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('messages.Ledger') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">

                {{-- Filters Section --}}
                <form method="GET" action="{{ route('followups.index') }}" id="follow_ups"
                    class="flex flex-wrap gap-4 mb-6 items-end">
                    <div class="flex flex-col font-weight-semibold">
                        <label for="from_date" class="text-gray-800 dark:text-gray-300 font-semibold">From:</label>
                        <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
                    </div>

                    <div class="flex flex-col">
                        <label for="to_date" class="text-gray-800 dark:text-gray-300 font-semibold">To:</label>
                        <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
                    </div>

                    <div class="flex flex-col">
                        <label for="branch_name" class="text-gray-800 dark:text-gray-300 font-semibold">Branch:</label>
                        <select id="branch_name" name="branch_name"
                            class="border border-gray-300 dark:border-gray-700 rounded-md pr-4 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
                            <option value="all" {{ request('branch_name') == 'all' ? 'selected' : '' }}>All
                            </option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch }}"
                                    {{ request('branch_name') == $branch ? 'selected' : '' }}>
                                    {{ $branch }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="doctor" class="text-gray-800 dark:text-gray-300 font-semibold">Doctor:</label>
                        <select id="doctor" name="doctor"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-2 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
                            <option value="all" {{ request('doctor') == 'all' ? 'selected' : '' }}>All
                            </option>
                            @php
                                $doctors = \App\Models\User::all();
                            @endphp
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ request('doctor') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Filter for Today, Last Week, Last Month --}}
                    <div class="flex flex-col">
                        <label class="text-gray-800 dark:text-gray-300 font-semibold">Time Period:</label>
                        <div class="flex gap-2">
                            <input type="hidden" id="time_period" name="time_period" value="{{ request('time_period', 'all') }}">
                            <button type="button" onclick="setTimePeriod('all')"
                                class="px-3 py-2 {{ request('time_period', 'all') == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-md font-semibold hover:bg-indigo-700 hover:text-white transition focus:ring focus:ring-indigo-300">
                                All
                            </button>
                            <button type="button" onclick="setTimePeriod('today')"
                                class="px-3 py-2 {{ request('time_period') == 'today' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-md font-semibold hover:bg-indigo-700 hover:text-white transition focus:ring focus:ring-indigo-300">
                                Today
                            </button>
                            <button type="button" onclick="setTimePeriod('last_week')"
                                class="px-3 py-2 {{ request('time_period') == 'last_week' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-md font-semibold hover:bg-indigo-700 hover:text-white transition focus:ring focus:ring-indigo-300">
                                Last Week
                            </button>
                            <button type="button" onclick="setTimePeriod('last_month')"
                                class="px-3 py-2 {{ request('time_period') == 'last_month' ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }} rounded-md font-semibold hover:bg-indigo-700 hover:text-white transition focus:ring focus:ring-indigo-300">
                                Last Month
                            </button>
                        </div>
                    </div>

                    <button onclick="formSubmit();"
                        class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition focus:ring focus:ring-indigo-300">
                        Filter
                    </button>
                    <button id="exportCSV" onclick="csvExport();"
                        class="px-3 py-2 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 transition focus:ring focus:ring-green-300">CSV</button>
                </form>

                {{-- Insights Section --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-5 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 {{ __('messages.Insights') }}
                    </h3>

                    {{-- Filter Summary Section --}}
                    <div class="bg-blue-50 dark:bg-gray-900 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                        <h4 class="text-md font-semibold text-blue-800 dark:text-blue-300 mb-3">Filter Summary</h4>
                        <ul class="text-sm list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                            @php
                                $filterSummary = [];

                                if (request('time_period') && request('time_period') != 'all') {
                                    $filterSummary[] = [
                                        'icon' => '⏳',
                                        'label' => 'Time Period',
                                        'value' => match (request('time_period')) {
                                            'today' => 'Today',
                                            'last_week' => 'Last Week',
                                            'last_month' => 'Last Month',
                                            default => 'Unknown',
                                        },
                                    ];
                                } elseif (request('from_date') || request('to_date')) {
                                    $from = request('from_date')
                                        ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y')
                                        : 'Start';
                                    $to = request('to_date')
                                        ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y')
                                        : 'End';
                                    $filterSummary[] = [
                                        'icon' => '📅',
                                        'label' => 'Date Range',
                                        'value' => "$from → $to",
                                    ];
                                }

                                if (request('branch_name') && request('branch_name') != 'all') {
                                    $filterSummary[] = [
                                        'icon' => '🏢',
                                        'label' => 'Branch',
                                        'value' => request('branch_name'),
                                    ];
                                }

                                if (request('doctor') && request('doctor') != 'all') {
                                    $doctor = \App\Models\User::find(request('doctor'));
                                    $filterSummary[] = [
                                        'icon' => '👨‍⚕️',
                                        'label' => 'Doctor',
                                        'value' => $doctor ? $doctor->name : 'Unknown',
                                    ];
                                }
                            @endphp

                            @if (empty($filterSummary))
                                <li class="text-gray-500 dark:text-gray-400 italic">No filters applied</li>
                            @else
                                @foreach ($filterSummary as $filter)
                                    <li>
                                        <span class="mr-1">{{ $filter['icon'] }}</span>
                                        {{ $filter['label'] }}: <span class="font-bold">{{ $filter['value'] }}</span>
                                    </li>
                                @endforeach
                                {{-- Example for total results if available --}}
                                {{-- @if (isset($totalPatients))
                                        <li>📊 Total Results: <span class="font-bold">{{ $totalPatients }}</span></li>
                                    @endif --}}
                            @endif
                        </ul>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
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



                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-cyan-200 to-white dark:from-blue-900 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-cyan-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">👤
                                {{ __('messages.Total Patients') }}</p>
                            <p class="text-xl font-bold text-cyan-600 dark:text-blue-300">{{ $totalPatients }}</p>
                        </div>

                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-purple-200 to-white dark:from-purple-900 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-purple-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">🔄
                                {{ __('messages.Total Follow-ups') }}</p>
                            <p class="text-lg font-bold text-purple-600 dark:text-purple-300">{{ $totalFollowUps }}</p>
                        </div>

                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-green-200 to-white dark:from-green-900 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-green-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">💰
                                {{ __('messages.Total Payment Received') }}</p>
                            <p class="text-lg font-bold text-green-600 dark:text-green-300">
                                ₹{{ number_format($totalIncome) }}</p>
                        </div>
                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-teal-200 to-white dark:from-teal-900 dark:to-gray-900 shadow transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-teal-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">💵
                                {{ __('messages.Cash Payments') }}</p>
                            <p class="text-lg font-bold text-teal-600 dark:text-teal-300">{{ $cashPayments }}</p>
                        </div>
                        <a href="{{ route('patient-dues.index') }}">
                            <div
                                class="p-4 rounded-lg bg-gradient-to-br from-red-200 to-white dark:from-red-900 dark:to-gray-900 shadow transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-red-200 hover:scale-105 hover:shadow-lg">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 gap-1">
                                    ⚠️ {{ __('messages.Total Outstanding Balance') }}
                                    <span class="text-xs text-gray-500">➡️</span>
                                </p>

                                <p class="text-lg font-bold text-red-600 dark:text-red-300">
                                    ₹{{ number_format($totalDueAll) }}
                                </p>

                                {{-- <p class="text-xs text-gray-500 dark:text-gray-400">Click to view details</p> --}}
                            </div>
                        </a>
                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-pink-200 to-white dark:from-pink-900 dark:to-gray-900 shadow transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-pink-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">💳
                                {{ __('messages.Online Payments') }}</p>
                            <p class="text-lg font-bold text-pink-600 dark:text-pink-300">{{ $onlinePayments }}</p>
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
                                ₹{{ number_format($totalDueAll) }}
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




                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
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

                        <tbody class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200">
                            @foreach ($followUps as $followUp)
                                @if ($followUp->patient)
                                    <tr
                                        class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
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

                                        <td class="text-center px-4 py-3">{{ $followUp->doctor->name ?? 'N/A' }}</td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            ₹{{ number_format(@$followUp->amount_billed, 2) }}
                                        </td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            {{ @json_decode($followUp->check_up_info)->payment_method }}
                                        </td>
                                        {{-- <td
                                            class="text-right px-4 py-3 font-semibold text-green-600 dark:text-green-300">
                                            ₹{{ number_format(@$followUp->amount_paid, 2) }}
                                        </td> --}}
                                        <td
                                            class="text-right px-4 py-3 font-semibold
                                            {{ $followUp->amount_paid < $followUp->amount_billed
                                                ? 'text-red-600 dark:text-red-400'
                                                : ($followUp->amount_paid > $followUp->amount_billed
                                                    ? 'text-green-600 dark:text-green-300'
                                                    : 'text-blue-600 dark:text-blue-300') }}">
                                            ₹{{ number_format(@$followUp->amount_paid, 2) }}
                                        </td>

                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mt-4">
                    {{ $followUps->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function formSubmit() {
        document.getElementById('follow_ups').action = "/followups";
        document.getElementById('follow_ups').submit();
    }

    function csvExport() {
        document.getElementById('follow_ups').action = "/export-followups";
        document.getElementById('follow_ups').submit();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    function setTimePeriod(period) {
        const timePeriodInput = document.getElementById('time_period');
        const fromDate = document.getElementById('from_date');
        const toDate = document.getElementById('to_date');
        timePeriodInput.value = period;

        // Disable and clear date inputs if period is not 'all'
        if (period !== 'all') {
            fromDate.disabled = true;
            toDate.disabled = true;
            fromDate.value = '';
            toDate.value = '';
        } else {
            fromDate.disabled = false;
            toDate.disabled = false;
        }

        // Update button styles
        document.querySelectorAll('.time-period-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
        });
        const activeButton = document.querySelector(`button[onclick="setTimePeriod('${period}')"]`);
        if (activeButton) {
            activeButton.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
            activeButton.classList.add('bg-indigo-600', 'text-white');
        }

        // Submit form
        formSubmit();
    }

    // Initialize button styles and input states on page load without submitting
    function initializeTimePeriod() {
        const timePeriodInput = document.getElementById('time_period');
        const fromDate = document.getElementById('from_date');
        const toDate = document.getElementById('to_date');
        const period = timePeriodInput.value || 'all';

        // Set input states
        if (period !== 'all') {
            fromDate.disabled = true;
            toDate.disabled = true;
            fromDate.value = '';
            toDate.value = '';
        } else {
            fromDate.disabled = false;
            toDate.disabled = false;
        }

        // Update button styles
        document.querySelectorAll('.time-period-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
        });
        const activeButton = document.querySelector(`button[onclick="setTimePeriod('${period}')"]`);
        if (activeButton) {
            activeButton.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-800', 'dark:text-gray-200');
            activeButton.classList.add('bg-indigo-600', 'text-white');
        }
    }

    // Run initialization on page load
    document.addEventListener('DOMContentLoaded', initializeTimePeriod);
    // Chart 1: Follow-Up Frequency (Daily)
    if (@json($followUpFrequencyDaily->count())) {
        const dailyCtx = document.getElementById('followUpFrequencyDailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($followUpFrequencyDaily->pluck('date')),
                datasets: [{
                    label: 'Follow-Ups',
                    data: @json($followUpFrequencyDaily->pluck('count')),
                    borderColor: '#60a5fa',
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(96, 165, 250, 0.6)');
                        gradient.addColorStop(1, 'rgba(96, 165, 250, 0.05)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#60a5fa',
                    pointHoverRadius: 6
                }]

            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Follow-Ups: ${context.parsed.y}`;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Daily Follow-Ups'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Follow-Ups'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        document.getElementById('followUpFrequencyDailyChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No daily follow-up data available.</p>';
    }

    // Chart 2: Follow-Up Frequency (Monthly)
    if (@json($followUpFrequencyMonthly->count())) {
        const monthlyCtx = document.getElementById('followUpFrequencyMonthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($followUpFrequencyMonthly->pluck('month')),
                datasets: [{
                    label: 'Follow-Ups',
                    data: @json($followUpFrequencyMonthly->pluck('count')),
                    borderColor: '#34D399', // Tailwind green-400
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(52, 211, 153, 0.5)'); // lighter green top
                        gradient.addColorStop(1, 'rgba(52, 211, 153, 0.05)'); // fade to transparent
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#34D399',
                    pointHoverRadius: 6,
                    pointHoverBorderWidth: 2,
                    pointHoverBorderColor: '#10B981'
                }]

            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Follow-Ups'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Follow-Ups'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        document.getElementById('followUpFrequencyMonthlyChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No monthly follow-up data available.</p>';
    }

    // Chart 3: Follow-Up Frequency (Yearly)
    // if (@json($followUpFrequencyYearly->count())) {
    //     const yearlyCtx = document.getElementById('followUpFrequencyYearlyChart').getContext('2d');
    //     new Chart(yearlyCtx, {
    //         type: 'line',
    //         data: {
    //             labels: @json($followUpFrequencyYearly->pluck('year')),
    //             datasets: [{
    //                 label: 'Follow-Ups',
    //                 data: @json($followUpFrequencyYearly->pluck('count')),
    //                 borderColor: '#FF6B6B',
    //                 backgroundColor: 'rgba(255, 107, 107, 0.2)',
    //                 fill: true,
    //                 tension: 0.4
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             plugins: {
    //                 title: {
    //                     display: true,
    //                     text: 'Yearly Follow-Ups'
    //                 },
    //                 legend: {
    //                     position: 'top'
    //                 }
    //             },
    //             scales: {
    //                 x: {
    //                     title: {
    //                         display: true,
    //                         text: 'Year'
    //                     }
    //                 },
    //                 y: {
    //                     title: {
    //                         display: true,
    //                         text: 'Number of Follow-Ups'
    //                     },
    //                     beginAtZero: true
    //                 }
    //             }
    //         }
    //     });
    // } else {
    //     document.getElementById('followUpFrequencyYearlyChart').parentElement.innerHTML =
    //         '<p class="text-gray-600 dark:text-gray-400">No yearly follow-up data available.</p>';
    // }

    // Chart 4: Age Distribution
    if (@json($ageDistribution->count())) {
        const ageCtx = document.getElementById('ageDistributionChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'doughnut',
            data: {
                labels: @json($ageDistribution->pluck('age_group')),
                datasets: [{
                    label: 'Patient Count',
                    data: @json($ageDistribution->pluck('count')),
                    backgroundColor: ['#86efac', '#93c5fd', '#FF6384', '#4BC0C0'],
                    borderColor: ['#86efac', '#93c5fd', '#FF6384', '#4BC0C0'],
                    borderWidth: 1,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Age Distribution of Patients'
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    } else {
        document.getElementById('ageDistributionChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No age data available.</p>';
    }

    // Chart 5: Payment Status
    if (@json($paymentStatus->count())) {
        const paymentCtx = document.getElementById('paymentStatusChart').getContext('2d');

        const gradientBilled = paymentCtx.createLinearGradient(0, 0, 0, 300);
        gradientBilled.addColorStop(0, 'rgba(147, 197, 253, 0.6)'); // Tailwind blue-300
        gradientBilled.addColorStop(1, 'rgba(147, 197, 253, 0.05)');

        const gradientPaid = paymentCtx.createLinearGradient(0, 0, 0, 300);
        gradientPaid.addColorStop(0, 'rgba(134, 239, 172, 0.6)'); // Tailwind green-300
        gradientPaid.addColorStop(1, 'rgba(134, 239, 172, 0.05)');

        const gradientDue = paymentCtx.createLinearGradient(0, 0, 0, 300);
        gradientDue.addColorStop(0, 'rgba(252, 165, 165, 0.6)'); // Tailwind red-300
        gradientDue.addColorStop(1, 'rgba(252, 165, 165, 0.05)');

        new Chart(paymentCtx, {
            type: 'line',
            data: {
                labels: @json($paymentStatus->pluck('date')),
                datasets: [{
                        label: 'Billed',
                        data: @json($paymentStatus->pluck('billed')),
                        borderColor: '#60a5fa', // Tailwind blue-400
                        backgroundColor: gradientBilled,
                        borderWidth: 1,
                        fill: true,
                        pointRadius: 2,
                        pointBackgroundColor: '#60a5fa',
                        pointHoverRadius: 6,
                        tension: 0.4
                    },
                    {
                        label: 'Paid',
                        data: @json($paymentStatus->pluck('paid')),
                        borderColor: '#34d399', // Tailwind green-400
                        backgroundColor: gradientPaid,
                        borderWidth: 1,
                        fill: true,
                        pointRadius: 2,
                        pointBackgroundColor: '#34d399',
                        pointHoverRadius: 6,
                        tension: 0.4
                    },
                    {
                        label: 'Due',
                        data: @json($paymentStatus->pluck('due')),
                        borderColor: '#f87171', // Tailwind red-400
                        backgroundColor: gradientDue,
                        borderWidth: 1,
                        fill: true,
                        pointRadius: 2,
                        pointBackgroundColor: '#f87171',
                        pointHoverRadius: 6,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ₹${context.parsed.y}`;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Payment Status'
                    },
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Amount (₹)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        document.getElementById('paymentStatusChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No payment data available.</p>';
    }


    // Chart 6: New vs. Existing Patients
    if (@json($newVsExistingPatients['new'] + $newVsExistingPatients['existing'])) {
        const newVsExistingCtx = document.getElementById('newVsExistingPatientsChart').getContext('2d');
        new Chart(newVsExistingCtx, {
            type: 'doughnut',
            data: {
                labels: ['New Patients', 'Old Patients'],
                datasets: [{
                    label: 'Patient Count',
                    data: [@json($newVsExistingPatients['new']), @json($newVsExistingPatients['existing'])],
                    backgroundColor: ['#93c5fd', '#86efac'],
                    borderColor: ['#93c5fd', '#86efac'],
                    borderWidth: 1,
                    hoverOffset: 20

                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'New vs. Old Patients'
                    },
                    legend: {
                        position: 'top'
                    }
                },

            }
        });
    } else {
        document.getElementById('newVsExistingPatientsChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No patient data available.</p>';
    }
</script>



