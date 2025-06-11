<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('messages.Analysis') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6">
                <!-- Filters Section -->
                <form method="GET" action="{{ route('analytics.index') }}" id="analytics_filters"
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
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ request('doctor') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition focus:ring focus:ring-indigo-300">
                        Filter
                    </button>
                </form>

                <!-- Charts Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-5 mb-6">
                    {{-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 {{ __('Analysis') }}</h3> --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Follow-Up Frequency (Daily) -->
                        <div>
                            <canvas id="followUpFrequencyDailyChart" height="220"></canvas>
                        </div>
                        <!-- Follow-Up Frequency (Monthly) -->
                        <div>
                            <canvas id="followUpFrequencyMonthlyChart" height="220"></canvas>
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
                            <!-- Gender Distribution -->
                            <div class="flex-1 max-w-md">
                                <div class="h-[450px] w-full">
                                    <canvas id="genderDistributionChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                    borderColor: '#34D399',
                    backgroundColor: (ctx) => {
                        const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(52, 211, 153, 0.5)');
                        gradient.addColorStop(1, 'rgba(52, 211, 153, 0.05)');
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
        gradientBilled.addColorStop(0, 'rgba(147, 197, 253, 0.6)');
        gradientBilled.addColorStop(1, 'rgba(147, 197, 253, 0.05)');

        const gradientPaid = paymentCtx.createLinearGradient(0, 0, 0, 300);
        gradientPaid.addColorStop(0, 'rgba(134, 239, 172, 0.6)');
        gradientPaid.addColorStop(1, 'rgba(134, 239, 172, 0.05)');

        const gradientDue = paymentCtx.createLinearGradient(0, 0, 0, 300);
        gradientDue.addColorStop(0, 'rgba(252, 165, 165, 0.6)');
        gradientDue.addColorStop(1, 'rgba(252, 165, 165, 0.05)');

        new Chart(paymentCtx, {
            type: 'line',
            data: {
                labels: @json($paymentStatus->pluck('date')),
                datasets: [{
                    label: 'Billed',
                    data: @json($paymentStatus->pluck('billed')),
                    borderColor: '#60a5fa',
                    backgroundColor: gradientBilled,
                    borderWidth: 1,
                    fill: true,
                    pointRadius: 2,
                    pointBackgroundColor: '#60a5fa',
                    pointHoverRadius: 6,
                    tension: 0.4
                }, {
                    label: 'Paid',
                    data: @json($paymentStatus->pluck('paid')),
                    borderColor: '#34d399',
                    backgroundColor: gradientPaid,
                    borderWidth: 1,
                    fill: true,
                    pointRadius: 2,
                    pointBackgroundColor: '#34d399',
                    pointHoverRadius: 6,
                    tension: 0.4
                }, {
                    label: 'Due',
                    data: @json($paymentStatus->pluck('due')),
                    borderColor: '#f87171',
                    backgroundColor: gradientDue,
                    borderWidth: 1,
                    fill: true,
                    pointRadius: 2,
                    pointBackgroundColor: '#f87171',
                    pointHoverRadius: 6,
                    tension: 0.4
                }]
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
                }
            }
        });
    } else {
        document.getElementById('newVsExistingPatientsChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No patient data available.</p>';
    }

    // Chart 7: Gender Distribution
    if (@json($genderDistribution->count())) {
        const genderCtx = document.getElementById('genderDistributionChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: @json($genderDistribution->pluck('gender_group')),
                datasets: [{
                    label: 'Patient Count',
                    data: @json($genderDistribution->pluck('count')),
                    backgroundColor: ['#f472b6','#60a5fa','#facc15', '#9ca3af'],
                    borderColor: ['#f472b6','#60a5fa','#facc15', '#9ca3af'],
                    borderWidth: 1,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Gender Distribution of Patients'
                    },
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    } else {
        document.getElementById('genderDistributionChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No gender data available.</p>';
    }
</script>
