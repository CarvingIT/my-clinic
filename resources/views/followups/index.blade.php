<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('messages.All Follow Ups') }}
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
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
                            <option value="all" {{ request('branch_name') == 'all' ? 'selected' : '' }}>All Branches
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
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm">
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

                    <button onclick="formSubmit();"
                        class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition focus:ring focus:ring-indigo-300">
                        Filter
                    </button>
                    <button id="exportCSV" onclick="csvExport();"
                        class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-md shadow-md hover:bg-green-700 transition focus:ring focus:ring-green-300">Export
                        to CSV</button>
                </form>

                {{-- Insights Section --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-5 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📊 {{ __('messages.Insights') }}
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-yellow-200 to-gray-50 dark:from-gray-800 dark:to-gray-900 shadow
                    transition-all duration-300 ease-in-out hover:bg-gradient-to-bl hover:from-gray-50 hover:to-yellow-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">🏢
                                {{ __('messages.Branch') }}</p>
                            <p class="text-lg font-semibold text-yellow-900 dark:text-gray-100">
                                {{ request('branch_name') ? request('branch_name') : __('messages.All Branches') }}
                            </p>
                        </div>

                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-orange-200 to-gray-50 dark:from-gray-800 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-gray-50 hover:to-orange-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">📆
                                {{ __('messages.Selected Date Range') }}</p>
                            <p class="text-base font-bold text-orange-900 dark:text-gray-100">
                                {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All' }}
                                →
                                {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'All' }}
                            </p>
                        </div>

                        <div
                            class="p-4 rounded-lg bg-gradient-to-br from-red-200 to-white dark:from-red-900 dark:to-gray-900 shadow
                    transition-all duration-400 ease-in-out hover:bg-gradient-to-bl hover:from-white hover:to-red-200 hover:scale-105 hover:shadow-lg">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">⚠️
                                {{ __('messages.Total Outstanding Balance') }}</p>
                            <p class="text-lg font-bold text-red-600 dark:text-red-300">
                                ₹{{ number_format($totalDueAll) }}</p>
                        </div>

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
                    </div>
                </div>

                {{-- Charts Section --}}
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-5 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 {{ __('messages.Charts') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Follow-Ups Over Time -->
                        <div>
                            <canvas id="followUpsOverTimeChart" height="200"></canvas>
                        </div>
                        <!-- Payment Status Breakdown -->
                        <div>
                            <canvas id="paymentStatusChart" height="200"></canvas>
                        </div>
                        <!-- Follow-Ups by Doctor -->
                        <div>
                            <canvas id="followUpsByDoctorChart" height="200"></canvas>
                        </div>
                        <!-- Payment Method Distribution -->
                        <div>
                            <canvas id="paymentMethodChart" height="200"></canvas>
                        </div>
                    </div>
                </div>




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
                                                class="text-indigo-700 dark:text-indigo-400 hover:underline font-semibold">
                                                {{ $followUp->patient->name }}
                                            </a>
                                        </td>
                                        <td class="text-center px-4 py-3">{{ $followUp->doctor->name }}</td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            ₹{{ number_format(@$followUp->amount_billed, 2) }}
                                        </td>
                                        <td
                                            class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">
                                            {{ @json_decode($followUp->check_up_info)->payment_method }}
                                        </td>
                                        <td
                                            class="text-right px-4 py-3 font-semibold text-green-600 dark:text-green-300">
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
        document.getElementById('follow_ups').action = "/followups";
        document.getElementById('follow_ups').submit();
    }

    function csvExport() {
        document.getElementById('follow_ups').action = "/export-followups";
        document.getElementById('follow_ups').submit();
    }

    // Chart 1: Follow-Ups Over Time
    const followUpsOverTimeCtx = document.getElementById('followUpsOverTimeChart').getContext('2d');
    new Chart(followUpsOverTimeCtx, {
        type: 'line',
        data: {
            labels: @json($followUpsOverTime->pluck('date')),
            datasets: [{
                label: 'Follow-Ups',
                data: @json($followUpsOverTime->pluck('count')),
                borderColor: '#4A90E2',
                backgroundColor: 'rgba(74, 144, 226, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Follow-Ups Over Time' },
                legend: { position: 'top' }
            },
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Number of Follow-Ups' }, beginAtZero: true }
            }
        }
    });

    // Chart 2: Payment Status Breakdown
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentStatusCtx, {
        type: 'pie',
        data: {
            labels: ['Fully Paid', 'Partially Paid', 'Unpaid'],
            datasets: [{
                label: 'Payment Status',
                data: [
                    @json($paymentStatus['fully_paid']),
                    @json($paymentStatus['partially_paid']),
                    @json($paymentStatus['unpaid'])
                ],
                backgroundColor: ['#50C878', '#FFCA28', '#FF6B6B'],
                borderColor: ['#FFFFFF', '#FFFFFF', '#FFFFFF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Payment Status Breakdown' },
                legend: { position: 'top' }
            }
        }
    });

    // Chart 3: Follow-Ups by Doctor
    const followUpsByDoctorCtx = document.getElementById('followUpsByDoctorChart').getContext('2d');
    new Chart(followUpsByDoctorCtx, {
        type: 'bar',
        data: {
            labels: @json($followUpsByDoctor->pluck('doctor.name')),
            datasets: [{
                label: 'Follow-Ups',
                data: @json($followUpsByDoctor->pluck('count')),
                backgroundColor: '#4A90E2',
                borderColor: '#4A90E2',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Follow-Ups by Doctor' },
                legend: { position: 'top' }
            },
            scales: {
                x: { title: { display: true, text: 'Doctor' } },
                y: { title: { display: true, text: 'Number of Follow-Ups' }, beginAtZero: true }
            }
        }
    });

    // Chart 4: Payment Method Distribution
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentMethodCtx, {
        type: 'doughnut',
        data: {
            labels: @json($paymentMethods->pluck('method')),
            datasets: [{
                label: 'Payment Methods',
                data: @json($paymentMethods->pluck('count')),
                backgroundColor: ['#4A90E2', '#FF6B6B', '#50C878', '#FFCA28'],
                borderColor: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Payment Method Distribution' },
                legend: { position: 'top' }
            }
        }
    });
</script>
