<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-5">
            {{ __('Edit Follow Up') }} - {{ $patient->name }}

            <span class="text-gray-600 text-sm">
                @if ($patient->birthdate || $patient->gender)
                    {{ $patient->birthdate?->age ?? __('') }}/{{ $patient->gender ?? __('') }}
                @endif
                @if ($patient->height)
                    | {{ __('messages.Height') }}: {{ $patient->height }} cm
                @endif
                @if ($patient->weight)
                    | {{ __('messages.Weight') }}: {{ $patient->weight }} kg
                @endif
                @if ($patient->height && $patient->weight)
                    @php
                        $heightInMeters = $patient->height / 100;
                        $bmi = $patient->weight / ($heightInMeters * $heightInMeters);
                        $bmiCategory = match (true) {
                            $bmi < 18.5 => 'Underweight',
                            $bmi >= 18.5 && $bmi < 25 => 'Healthy Weight',
                            $bmi >= 25 && $bmi < 30 => 'Overweight',
                            default => 'Obese',
                        };
                    @endphp
                    | {{ __('BMI') }}: {{ number_format($bmi, 2) }}
                @endif
                @if (isset($totalDueAll))
                    | {{ __('messages.Total Outstanding Balance') }}: ₹{{ number_format($totalDueAll, 2) }}
                @endif
                @if ($patient->occupation)
                    <div>
                        <span class="font-semibold">| {{ __('messages.occupation') }}:</span>
                        <span>{{ $patient->occupation }}</span>
                    </div>
                @endif

                @if ($patient->reference)
                    <div>
                        <span class="font-semibold">| {{ __('messages.reference') }}:</span>
                        <span>{{ $patient->reference }}</span>
                    </div>
                @endif
                <div class="my-1"></div>
                <div class="flex flex-wrap items-start gap-x-2 text-sm">
                    @if ($patient->vishesh)
                        <div>
                            <span class="font-semibold">| {{ __('messages.Vishesh') }}:</span>
                            <span class="font-medium">{!! $patient->vishesh !!}</span>
                        </div>
                    @endif
                </div>
            </span>
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Patient Details (Left Side - 2/3 width) -->
                        <div class="lg:col-span-2">
                            <h2 class="text-2xl font-bold text-indigo-700 mb-4 flex items-center cursor-pointer hover:text-indigo-700 dark:hover:text-indigo-300 transition duration-400">
                                {{ $patient->name }} ({{ $patient->patient_id }})
                            </h2>
                            <!-- Complete Patient Information Grid -->
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3 text-sm" x-data="{ showMore: false }">
                                @if ($patient->vishesh)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded lg:col-span-2">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.Vishesh') }}:</span>
                                        <span class="text-gray-600 dark:text-gray-400 ml-1">{!! nl2br(html_entity_decode(strip_tags($patient->vishesh))) !!}</span>
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
                                foreach ($followUps as $fup) {
                                    $checkUpInfoVal = json_decode($fup->check_up_info, true) ?? [];
                                    if (!empty($checkUpInfoVal['reports']) && is_array($checkUpInfoVal['reports'])) {
                                        foreach ($checkUpInfoVal['reports'] as $index => $report) {
                                            if (isset($report['deleted_at'])) {
                                                continue;
                                            }
                                            $allReports[] = [
                                                'text' => $report['text'] ?? '',
                                                'timestamp' => $report['timestamp'] ?? '',
                                                'followup_date' => $fup->created_at->format('d M Y'),
                                                'followup_id' => $fup->id,
                                                'report_index' => $index
                                            ];
                                        }
                                    }
                                }
                                usort($allReports, function($a, $b) {
                                    return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
                                });
                            @endphp
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 h-full shadow-lg border border-gray-200 dark:border-gray-600">
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

                                <div class="flex gap-3 mb-4">
                                    <div class="relative flex-1">
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <input type="text" id="reportSearch" placeholder="Search reports..."
                                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 shadow-sm">
                                    </div>
                                    <button type="button" onclick="openReportModal()"
                                        class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-200 shadow-md flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add
                                    </button>
                                </div>

                                <div id="reportsList" class="space-y-3 max-h-40 overflow-y-auto scrollbar-thin scrollbar-thumb-indigo-300 dark:scrollbar-thumb-indigo-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800">
                                    @if(count($allReports) > 0)
                                        @foreach($allReports as $report)
                                            @php
                                                $isoTimestamp = $report['timestamp'];
                                                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})$/', $report['timestamp'], $matches)) {
                                                    try {
                                                        $date = new DateTime("{$matches[3]}-{$matches[2]}-{$matches[1]} {$matches[4]}:{$matches[5]}:{$matches[6]}");
                                                        $isoTimestamp = $date->format(DateTime::ATOM);
                                                    } catch (Exception $e) {
                                                        $isoTimestamp = $report['timestamp'];
                                                    }
                                                }
                                            @endphp
                                            <div class="report-item bg-white dark:bg-gray-600 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-500"
                                                 data-text="{{ strtolower($report['text']) }}"
                                                 data-display-timestamp="{{ $report['timestamp'] }}"
                                                 data-followup-date="{{ $report['followup_date'] }}"
                                                 data-original-text="{{ $report['text'] }}"
                                                 data-followup-id="{{ $report['followup_id'] }}"
                                                 data-report-index="{{ $report['report_index'] }}">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="text-sm text-gray-800 dark:text-gray-200 font-medium">
                                                            {!! nl2br(e($report['text'])) !!}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" data-timestamp="{{ $isoTimestamp }}">
                                                            {{ $report['timestamp'] }} • Follow-up: {{ $report['followup_date'] }}
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col space-y-1 ml-2">
                                                        <button type="button" onclick="editReport(this)"
                                                            class="w-6 h-6 bg-blue-500 text-white rounded hover:bg-blue-600 transition flex items-center justify-center"
                                                            title="Edit">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" onclick="deleteReport(this)"
                                                            class="w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 transition flex items-center justify-center"
                                                            title="Delete">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                            <div class="text-sm">No previous reports found</div>
                                            <div class="text-xs mt-1">Add your first report using the + button</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Follow-up Edit Form (Left Column) -->
                            <div class="lg:col-span-2">
                                <form method="POST" action="{{ route('followups.update', $followup) }}" enctype="multipart/form-data" id="followUpForm">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="patient_id" value="{{ $patient->id }}" />
                                    @include('followups.form-fields', ['isEdit' => true])
                                </form>
                            </div>

                            <!-- Previous Follow-ups (Right Column) -->
                            <!-- Parent container with relative positioning -->
                            <div class="relative min-h-screen">
                                <!-- Follow-ups div -->
                                <div
                                    class="absolute top-[62px] right-10 w-[375px] max-h-[calc(100vh-250px)] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 scrollbar-thin z-10 mb-3 md:static md:w-full md:mx-0 md:my-4">
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                        {{ __('Previous Follow-ups') }}
                                    </h3>

                                    @if ($followUps->count() > 0)
                                        <div class="space-y-4">
                                            @foreach ($followUps as $fup)
                                                <div
                                                    class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $fup->created_at->format('d M Y, h:i A') }}
                                                    </p>
                                                    @php $checkUpInfoVal = json_decode($fup->check_up_info, true); @endphp
                                                    <div
                                                        class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 [p]:m-0 [p]:text-sm [h1]:text-base [h2]:text-sm">
                                                        <strong>नाडी:</strong>
                                                        {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfoVal['nadi'] ?? '-') !!}
                                                        <div class="my-0.5"></div>

                                                        <strong>{{ __('लक्षणे') }}:</strong>
                                                        {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $fup->diagnosis ?? '-') !!}
                                                        <div class="my-0.5"></div>

                                                        <strong>{{ __('चिकित्सा') }}:</strong>
                                                        {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfoVal['chikitsa'] ?? '-') !!}
                                                        <div class="my-0.5"></div>

                                                        @if (!empty($checkUpInfoVal['days']))
                                                            <strong>{{ __('दिवस') }}:</strong>
                                                            {{ $checkUpInfoVal['days'] }}
                                                            <div class="my-0.5"></div>
                                                        @endif

                                                        @if (!empty($checkUpInfoVal['packets']))
                                                            <strong>{{ __('पुड्या') }}:</strong>
                                                            {{ $checkUpInfoVal['packets'] }}
                                                            <div class="my-0.5"></div>
                                                        @endif

                                                        @php
                                                            $amountPaid = $fup->amount_paid ?? 0;
                                                        @endphp
                                                        <strong>{{ __('दिलेली रक्कम') }}:</strong>
                                                        ₹{{ number_format($amountPaid, 2) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-600 dark:text-gray-400">
                                            {{ __('No previous follow-ups.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@include('followups.form-modals')
@include('followups.form-scripts')
