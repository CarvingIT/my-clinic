<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
            {{ __('messages.Analysis') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-lg rounded-lg p-6 pdf-content">
                <style>
                    @media print, (max-width: 0) {
                        .pdf-content {
                            max-width: 210mm !important;
                            margin: 0 auto !important;
                            padding: 20mm !important;
                            box-shadow: none !important;
                            background: white !important;
                        }
                        .pdf-content * {
                            color: black !important;
                            background: white !important;
                        }
                        .pdf-content canvas {
                            max-width: 100% !important;
                            height: auto !important;
                        }
                    }

                    /* Modern button animations */
                    #exportPdfBtn {
                        position: relative;
                        overflow: hidden;
                    }

                    #exportPdfBtn::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -100%;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                        transition: left 0.5s;
                    }

                    #exportPdfBtn:hover::before {
                        left: 100%;
                    }

                    #loadingSpinner {
                        transition: opacity 0.3s ease;
                    }

                    #progressBar {
                        transition: all 0.3s ease;
                        transform-origin: left;
                    }

                    /* Pulse animation for loading */
                    @keyframes pulse-loading {
                        0%, 100% {
                            opacity: 1;
                        }
                        50% {
                            opacity: 0.7;
                        }
                    }

                    .animate-pulse-loading {
                        animation: pulse-loading 1.5s ease-in-out infinite;
                    }
                </style>
                <!-- Filters Section -->
                <form method="GET" action="{{ route('analytics.index') }}" id="analytics_filters"
                    class="grid grid-cols-1 md:grid-cols-7 gap-4 mb-8 items-end">
                    <div class="flex flex-col">
                        <label for="from_date" class="text-gray-800 dark:text-gray-300 font-semibold mb-2">From:</label>
                        <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm w-full">
                    </div>

                    <div class="flex flex-col">
                        <label for="to_date" class="text-gray-800 dark:text-gray-300 font-semibold mb-2">To:</label>
                        <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm w-full">
                    </div>

                    <div class="flex flex-col">
                        <label for="branch_name" class="text-gray-800 dark:text-gray-300 font-semibold mb-2">Branch:</label>
                        <select id="branch_name" name="branch_name"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm w-full">
                            <option value="all" {{ request('branch_name') == 'all' ? 'selected' : '' }}>All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch }}"
                                    {{ request('branch_name') == $branch ? 'selected' : '' }}>
                                    {{ $branch }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="doctor" class="text-gray-800 dark:text-gray-300 font-semibold mb-2">Doctor:</label>
                        <select id="doctor" name="doctor"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm w-full">
                            <option value="all" {{ request('doctor') == 'all' ? 'selected' : '' }}>All</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ request('doctor') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="gender" class="text-gray-800 dark:text-gray-300 font-semibold mb-2">Gender:</label>
                        <select id="gender" name="gender"
                            class="border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 dark:bg-gray-800 dark:text-white shadow-sm w-full">
                            <option value="all" {{ request('gender') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="M" {{ request('gender') == 'M' ? 'selected' : '' }}>Male</option>
                            <option value="F" {{ request('gender') == 'F' ? 'selected' : '' }}>Female</option>
                            <option value="O" {{ request('gender') == 'O' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2 col-span-2">
                        <div class="h-6"></div> <!-- Spacer for label alignment -->
                        <div class="flex gap-2 w-full">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow-md hover:bg-indigo-700 transition focus:ring focus:ring-indigo-300 flex-1">
                                Filter
                            </button>
                            <button type="button" id="exportPdfBtn"
                                class="relative px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-md shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 focus:ring focus:ring-blue-300 flex-1 overflow-hidden group whitespace-nowrap">
                                <span class="relative z-10 flex items-center justify-center gap-2 min-w-0">
                                    <svg id="pdfIcon" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span id="buttonText" class="truncate">Save as PDF</span>
                                </span>
                                <!-- Loading spinner -->
                                <div id="loadingSpinner" class="absolute inset-0 flex items-center justify-center opacity-0">
                                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
                                </div>
                                <!-- Progress bar -->
                                <div id="progressBar" class="absolute bottom-0 left-0 h-0.5 bg-white opacity-0 transition-all duration-300"></div>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Charts Section -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <!-- Header -->
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6 text-center">
                        Analytics Dashboard
                    </h3>

                    <!-- Top Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Follow-Up Frequency (Daily) -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                                Daily Follow-Ups
                            </h4>
                            <div class="flex justify-center">
                                <canvas id="followUpFrequencyDailyChart" width="400" height="250"></canvas>
                            </div>
                        </div>

                        <!-- Follow-Up Frequency (Monthly) -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                                Monthly Follow-Ups
                            </h4>
                            <div class="flex justify-center">
                                <canvas id="followUpFrequencyMonthlyChart" width="400" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Chart -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm mb-8">
                        <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                            Payment Status Overview
                        </h4>
                        <div class="flex justify-center">
                            <canvas id="paymentStatusChart" width="800" height="300"></canvas>
                        </div>
                    </div>

                    <!-- Bottom Charts Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- New vs. Existing Patients -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                                New vs. Existing Patients
                            </h4>
                            <div class="flex justify-center">
                                <canvas id="newVsExistingPatientsChart" width="250" height="250"></canvas>
                            </div>
                        </div>

                        <!-- Age Distribution -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                                Age Distribution
                            </h4>
                            <div class="flex justify-center">
                                <canvas id="ageDistributionChart" width="250" height="250"></canvas>
                            </div>
                        </div>

                        <!-- Gender Distribution -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 text-center">
                                Gender Distribution
                            </h4>
                            <div class="flex justify-center">
                                <canvas id="genderDistributionChart" width="250" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Export to PDF with modern interactive button
    document.getElementById('exportPdfBtn').addEventListener('click', async function () {
        const button = this;
        const buttonText = document.getElementById('buttonText');
        const pdfIcon = document.getElementById('pdfIcon');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const progressBar = document.getElementById('progressBar');

        // Store original state
        const originalText = buttonText.textContent;
        const originalDisabled = button.disabled;

        // Start loading animation
        button.disabled = true;
        button.classList.add('animate-pulse');

        // Animate to loading state
        buttonText.textContent = 'Preparing...';
        pdfIcon.style.transform = 'scale(0.8)';

        // Show spinner with delay for smooth transition
        // setTimeout(() => {
        //     loadingSpinner.style.opacity = '1';
        //     buttonText.textContent = 'Generating PDF...';
        // }, 300);

        // Animate progress bar
        setTimeout(() => {
            progressBar.style.opacity = '1';
            progressBar.style.width = '30%';
        }, 600);

        setTimeout(() => {
            progressBar.style.width = '60%';
        }, 1200);

        setTimeout(() => {
            progressBar.style.width = '90%';
        }, 1800);

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
            compress: true
        });

        // Target the main content div that contains all the analytics data
        const content = document.querySelector('.pdf-content');

        if (!content) {
            alert('Analytics content not found. Please contact support.');
            return;
        }

        try {
            // Wait for charts to render completely
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Update progress
            progressBar.style.width = '95%';
            buttonText.textContent = 'Finalizing...';

            // Temporarily adjust content for PDF capture
            const originalScroll = window.scrollY;
            const originalTransform = content.style.transform;
            const originalWidth = content.style.width;

            // Remove any forced width and let content be natural
            content.style.width = 'auto';
            content.style.maxWidth = 'none';
            content.style.margin = '0';
            content.style.boxShadow = 'none';

            window.scrollTo(0, 0);

            // Capture the analytics content with natural dimensions
            const canvas = await html2canvas(content, {
                scale: 1.5, // Slightly higher quality but not too much
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0,
                ignoreElements: (element) => {
                    return element.id === 'exportPdfBtn' ||
                           element.tagName === 'BUTTON' ||
                           element.classList.contains('hover:bg-indigo-700') ||
                           element.classList.contains('hover:bg-blue-700');
                },
                onclone: (clonedDoc) => {
                    // Ensure all text is black for PDF
                    const elements = clonedDoc.querySelectorAll('*');
                    elements.forEach(el => {
                        el.style.color = '#000000';
                        el.style.backgroundColor = '#ffffff';
                    });
                }
            });

            // Restore original styles
            content.style.width = originalWidth;
            content.style.maxWidth = '';
            content.style.margin = '';
            content.style.boxShadow = '';
            window.scrollTo(0, originalScroll);

            const imgData = canvas.toDataURL('image/png', 0.95);

            // A4 dimensions in mm
            const pageWidth = 210;
            const pageHeight = 297;
            const margin = 10; // 10mm margin

            // Calculate scaling to fit content properly
            const availableWidth = pageWidth - (margin * 2);
            const availableHeight = pageHeight - (margin * 2);

            const imgAspectRatio = canvas.width / canvas.height;
            const pageAspectRatio = availableWidth / availableHeight;

            let imgWidth, imgHeight;

            if (imgAspectRatio > pageAspectRatio) {
                // Content is wider than page ratio - fit to width
                imgWidth = availableWidth;
                imgHeight = availableWidth / imgAspectRatio;
            } else {
                // Content is taller than page ratio - fit to height
                imgHeight = availableHeight;
                imgWidth = availableHeight * imgAspectRatio;
            }

            // Center the content
            const xOffset = margin + (availableWidth - imgWidth) / 2;
            const yOffset = margin;

            // Complete progress
            progressBar.style.width = '100%';
            buttonText.textContent = 'Saving...';

            // Add to PDF
            if (imgHeight <= availableHeight) {
                // Single page
                pdf.addImage(imgData, 'PNG', xOffset, yOffset, imgWidth, imgHeight);
            } else {
                // Multi-page if absolutely necessary
                let currentY = yOffset;
                let remainingHeight = imgHeight;

                while (remainingHeight > 0) {
                    const currentHeight = Math.min(remainingHeight, availableHeight);
                    pdf.addImage(imgData, 'PNG', xOffset, currentY, imgWidth, currentHeight);

                    remainingHeight -= availableHeight;
                    if (remainingHeight > 0) {
                        pdf.addPage();
                        currentY = margin - remainingHeight;
                    }
                }
            }

            // Success animation
            setTimeout(() => {
                buttonText.textContent = '✓ Success!';
                loadingSpinner.style.opacity = '0';
                button.classList.remove('animate-pulse');
                button.classList.add('bg-green-500', 'hover:bg-green-600');

                // Reset after success
                setTimeout(() => {
                    buttonText.textContent = originalText;
                    button.classList.remove('bg-green-500', 'hover:bg-green-600');
                    button.classList.add('from-blue-500', 'to-blue-600', 'hover:from-blue-600', 'hover:to-blue-700');
                    pdfIcon.style.transform = 'scale(1)';
                    progressBar.style.width = '0';
                    progressBar.style.opacity = '0';
                    button.disabled = originalDisabled;
                }, 3500);
            }, 200);

            pdf.save('analytics-report.pdf');

        } catch (error) {
            console.error('PDF export failed:', error);
            alert('PDF export failed: ' + error.message);

            // Error state
            buttonText.textContent = '❌ Failed';
            button.classList.remove('animate-pulse');
            button.classList.add('bg-red-500', 'hover:bg-red-600');

            // Reset after error
            setTimeout(() => {
                buttonText.textContent = originalText;
                button.classList.remove('bg-red-500', 'hover:bg-red-600');
                button.classList.add('from-blue-500', 'to-blue-600', 'hover:from-blue-600', 'hover:to-blue-700');
                pdfIcon.style.transform = 'scale(1)';
                loadingSpinner.style.opacity = '0';
                progressBar.style.width = '0';
                progressBar.style.opacity = '0';
                button.disabled = originalDisabled;
            }, 2000);
        }
    });
</script>
<script>
    // Chart.register(ChartDataLabels);

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

        const ageLabels = @json($ageDistribution->pluck('age_group'));
        const ageCounts = @json($ageDistribution->pluck('count'));
        const total = ageCounts.reduce((sum, val) => sum + val, 0);

        new Chart(ageCtx, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: ageLabels,
                datasets: [{
                    label: 'Patient Count',
                    data: ageCounts,
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
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const dataset = data.datasets[0];
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        hidden: isNaN(value),
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value) => {
                            return value; // Actual number on pie slice
                        },
                        font: {
                            weight: 'bold'
                        },
                        anchor: 'center',
                        align: 'center'
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

    if (@json($newVsExistingPatients['new'] + $newVsExistingPatients['existing'])) {
        const newVsExistingCtx = document.getElementById('newVsExistingPatientsChart').getContext('2d');
        const newCount = @json($newVsExistingPatients['new']);
        const oldCount = @json($newVsExistingPatients['existing']);
        const total = newCount + oldCount;

        new Chart(newVsExistingCtx, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: ['New Patients', 'Old Patients'],
                datasets: [{
                    label: 'Patient Count',
                    data: [newCount, oldCount],
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
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const dataset = data.datasets[0];
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        hidden: isNaN(value),
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value) => {
                            return value; // only the actual number on pie slice
                        },
                        font: {
                            weight: 'bold'
                        },
                        anchor: 'center',
                        align: 'center'
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

        const genderLabels = @json($genderDistribution->pluck('gender_group'));
        const genderCounts = @json($genderDistribution->pluck('count'));
        const total = genderCounts.reduce((sum, val) => sum + val, 0);

        new Chart(genderCtx, {
            type: 'doughnut',
            plugins: [ChartDataLabels],
            data: {
                labels: genderLabels,
                datasets: [{
                    label: 'Patient Count',
                    data: genderCounts,
                    backgroundColor: ['#60a5fa', '#f472b6', '#facc15', '#9ca3af'],
                    borderColor: ['#60a5fa', '#f472b6', '#facc15', '#9ca3af'],
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
                        position: 'top',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const dataset = data.datasets[0];
                                return data.labels.map((label, i) => {
                                    const value = dataset.data[i];
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label} (${percentage}%)`,
                                        fillStyle: dataset.backgroundColor[i],
                                        strokeStyle: dataset.borderColor[i],
                                        lineWidth: dataset.borderWidth,
                                        hidden: isNaN(value),
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value) => {
                            return value; // Only number on the pie slice
                        },
                        font: {
                            weight: 'bold'
                        },
                        anchor: 'center',
                        align: 'center'
                    }
                }
            }
        });
    } else {
        document.getElementById('genderDistributionChart').parentElement.innerHTML =
            '<p class="text-gray-600 dark:text-gray-400">No gender data available.</p>';
    }
</script>
