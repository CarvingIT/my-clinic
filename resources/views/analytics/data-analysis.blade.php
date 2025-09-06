<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.Followup Data Analysis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg p-6 pdf-content">
                <style>
                    @media print, (max-width: 0) {
                        .pdf-content {
                            max-width 210mm !important;
                            margin: 0 auto !important;
                            padding: 20mm !important;
                            box-shadow: none !important;
                            background: white !important;
                        }
                        .pdf-content * {
                            color: black !important;
                            background: white !important;
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
                <!-- Filters & Keyword Search Compact Bar -->
                <form method="GET" action="{{ route('data-analysis.index') }}"
                    class="bg-white border border-gray-200 rounded-lg p-4 mb-6 shadow-sm space-y-3 md:space-y-0 md:flex md:flex-wrap md:items-end md:gap-4">
                    <!-- Date From -->
                    <div class="flex-1 min-w-[150px]">
                        <x-input-label for="date_from" :value="__('From')" class="text-xs text-gray-500" />
                        <x-text-input id="date_from" name="date_from" type="date" class="block w-full text-sm"
                            value="{{ request('date_from') }}" />
                    </div>
                    <!-- Date To -->
                    <div class="flex-1 min-w-[150px]">
                        <x-input-label for="date_to" :value="__('To')" class="text-xs text-gray-500" />
                        <x-text-input id="date_to" name="date_to" type="date" class="block w-full text-sm"
                            value="{{ request('date_to') }}" />
                    </div>
                    <!-- Gender -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="gender" :value="__('Gender')" class="text-xs text-gray-500" />
                        <select id="gender" name="gender"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">All</option>
                            <option value="M" @selected(request('gender') === 'M')>Male</option>
                            <option value="F" @selected(request('gender') === 'F')>Female</option>
                            <option value="O" @selected(request('gender') === 'O')>Other</option>
                        </select>
                    </div>
                    <!-- Age Group -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="age_group" :value="__('Age Group')" class="text-xs text-gray-500" />
                        <select id="age_group" name="age_group"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            <option value="0-10" @selected(request('age_group') === '0-10')>0 - 10</option>
                            <option value="10-20" @selected(request('age_group') === '10-20')>10 - 20</option>
                            <option value="20-30" @selected(request('age_group') === '20-30')>20 - 30</option>
                            <option value="30-40" @selected(request('age_group') === '30-40')>30 - 40</option>
                            <option value="40-50" @selected(request('age_group') === '40-50')>40 - 50</option>
                            <option value="50-60" @selected(request('age_group') === '50-60')>50 - 60</option>
                            <option value="60-70" @selected(request('age_group') === '60-70')>60 - 70</option>
                            <option value="70-80" @selected(request('age_group') === '70-80')>70 - 80</option>
                            <option value="80+" @selected(request('age_group') === '80+')>80+</option>
                        </select>
                    </div>
                    <!-- Weight Group -->
                    <div class="flex-1 min-w-[130px]">
                        <x-input-label for="weight_range" :value="__('Weight Range')" class="text-xs text-gray-500" />
                        <select id="weight_range" name="weight_range"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">{{ __('All') }}</option>
                            <option value="0-30" @selected(request('weight_range') === '0-30')>0 - 30 kg</option>
                            <option value="31-50" @selected(request('weight_range') === '31-50')>31 - 50 kg</option>
                            <option value="51-70" @selected(request('weight_range') === '51-70')>51 - 70 kg</option>
                            <option value="71-90" @selected(request('weight_range') === '71-90')>71 - 90 kg</option>
                            <option value="91-999" @selected(request('weight_range') === '91-999')>91+ kg</option>
                        </select>
                    </div>


                    <!-- Keyword -->
                    <div class="flex-1 min-w-[200px]">
                        <x-input-label for="keyword" :value="__('Keyword')" class="text-xs text-gray-500" />
                        <x-text-input id="keyword" name="keyword" type="text" placeholder="Search..."
                            class="block w-full text-sm" />
                    </div>
                    <!-- Buttons -->
                    <div class="flex items-center gap-2 mt-2 md:mt-6">
                        <x-primary-button name="add_keyword" value="1" type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-xs px-3 py-2">
                            + Filter
                        </x-primary-button>
                        {{-- <x-primary-button class="bg-green-600 hover:bg-green-700 text-xs px-3 py-2">
                            Apply
                        </x-primary-button> --}}
                    </div>
                </form>
                <!-- Active Keywords -->
                @if (!empty($keywords))
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach ($keywords as $keyword)
                            <form method="GET" action="{{ route('data-analysis.index') }}"
                                class="inline-flex items-center bg-indigo-100 text-indigo-800 rounded-full px-3 py-1 text-xs font-semibold">
                                <input type="hidden" name="remove_keyword" value="{{ $keyword }}">
                                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                                <input type="hidden" name="gender" value="{{ request('gender') }}">
                                <button type="submit" class="mr-1 font-bold hover:text-red-600">×</button>
                                {{ $keyword }}
                            </form>
                        @endforeach
                        <form method="GET" action="{{ route('data-analysis.index') }}">
                            <x-secondary-button type="submit" name="clear_keywords" value="1" class="text-xs">
                                Clear all
                            </x-secondary-button>
                        </form>
                    </div>
                @endif
                <!-- Descriptive Filter Summary -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
                    <h3 class="font-semibold text-blue-800 mb-2">Filter Summary</h3>
                    <ul class="text-sm text-blue-900 space-y-1">
                        @if (request('date_from') || request('date_to'))
                            <li>
                                📅 Showing follow-ups
                                @if (request('date_from'))
                                    from
                                    <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</strong>
                                @endif
                                @if (request('date_to'))
                                    to
                                    <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</strong>
                                @endif
                            </li>
                        @endif
                        @if (request('gender'))
                            <li>
                                🧑‍⚕️ Gender filter: <strong>
                                    @php
                                        $genderLabels = ['M' => 'Male', 'F' => 'Female', 'O' => 'Other'];
                                    @endphp
                                    {{ $genderLabels[request('gender')] ?? request('gender') }}
                                </strong>
                            </li>
                        @endif
                        @if (request('age_group'))
                            <li>
                                👶 Age Group filter:
                                <strong>{{ request('age_group') }}</strong>
                            </li>
                        @endif
                        @if (request('weight_range'))
                            <li>
                                ⚖️ Weight Range:
                                <strong>{{ str_replace('-', ' - ', request('weight_range')) }} kg</strong>
                            </li>
                        @endif


                        @if (!empty($keywords))
                            <li>
                                🔍 Keyword(s) applied:
                                <span class="font-semibold text-indigo-700">
                                    {{ implode(', ', $keywords) }}
                                </span>
                            </li>
                        @endif
                        <li>
                            📊 Total results: <strong>{{ $matchCount }}</strong>
                        </li>
                    </ul>
                </div>
                <!-- Export PDF Button -->
                <div class="flex justify-end mb-6">
                    <button type="button" id="exportPdfBtn"
                        class="relative px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-md shadow-md hover:from-blue-600 hover:to-blue-700 transition-all duration-300 focus:ring focus:ring-blue-300 overflow-hidden group whitespace-nowrap min-w-[140px]">
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
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="w-1/6 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('messages.Patient Name') }}
                                </th>
                                <th
                                    class="w-1/12 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('messages.reference') }}
                                </th>
                                <th
                                    class="w-1/5 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('नाडी / लक्षणे') }}
                                </th>
                                <th
                                    class="w-1/5 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('चिकित्सा') }}
                                </th>
                                <th
                                    class="w-1/5 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('निदान ') }}
                                </th>
                                <th
                                    class="w-1/6 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('messages.Vishesh') }}
                                </th>
                                <th
                                    class="w-1/12 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider break-words hyphens-auto">
                                    {{ __('messages.Followup Timestamp') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($followUps as $followUp)
                                <tr>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 block">
                                            {{ optional($followUp->patient)->name ?? 'N/A' }}
                                            @if ($followUp->patient && $followUp->patient->birthdate)
                                                [{{ intval(\Carbon\Carbon::parse($followUp->patient->birthdate)->diffInYears($followUp->created_at)) }}]
                                            @elseif ($followUp->patient && $followUp->patient->age)
                                                [{{ intval($followUp->patient->age) }}y]
                                            @endif
                                        </a>
                                        @if ($followUp->patient && ($followUp->patient->height || $followUp->patient->weight))
                                            <div class="text-gray-500 text-sm">
                                                {{ $followUp->patient->height ? $followUp->patient->height . 'cm' : 'N/A' }}
                                                |
                                                {{ $followUp->patient->weight ? $followUp->patient->weight . 'kg' : 'N/A' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        {{ optional($followUp->patient)->reference ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        @php
                                            $checkUpInfo = json_decode($followUp->check_up_info, true) ?? [];
                                        @endphp
                                        @if (!empty($checkUpInfo['nadi']))
                                            <p>{!! $checkUpInfo['nadi'] !!}</p>
                                        @endif
                                        @if (!empty($followUp->diagnosis))
                                            <p>{!! $followUp->diagnosis !!}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        @if (!empty($checkUpInfo['chikitsa']))
                                            <p>{!! $checkUpInfo['chikitsa'] !!}</p>
                                        @endif
                                        {{-- @if (!empty($checkUpInfo['nidan']))
                                            <p>{!! $checkUpInfo['nidan'] !!}</p>
                                        @endif
                                        @if (!empty($checkUpInfo['days']))
                                            <p>{{ __('Days') }}: {{ $checkUpInfo['days'] }}</p>
                                        @endif
                                        @if (!empty($checkUpInfo['packets']))
                                            <p>{{ __('Packets') }}: {{ $checkUpInfo['packets'] }}</p>
                                        @endif --}}
                                    </td>

                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        @if (!empty($checkUpInfo['nidan']))
                                            <p>{!! $checkUpInfo['nidan'] !!}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        @if (!empty($followUp->patient->vishesh))
                                            <p>{!! optional($followUp->patient)->vishesh !!}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 break-words hyphens-auto">
                                        {{ $followUp->created_at->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $followUps->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Export to PDF functionality
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
        buttonText.textContent = 'Preparing...';
        pdfIcon.style.transform = 'scale(0.8)';

        // Show spinner with delay for smooth transition
        setTimeout(() => {
            loadingSpinner.style.opacity = '1';
            buttonText.textContent = 'Generating PDF...';
        }, 300);

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

        try {
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
                throw new Error('Content not found. Please contact support.');
            }

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
                }, 1500);
            }, 200);

            pdf.save('followup-data-analysis.pdf');

        } catch (error) {
            console.error('PDF export failed:', error);
            alert('PDF export failed: ' + error.message);

            // Error state
            buttonText.textContent = '❌ Failed';
            button.classList.remove('animate-pulse');
            button.classList.add('bg-red-500', 'hover:bg-red-600');
            loadingSpinner.style.opacity = '0';

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
