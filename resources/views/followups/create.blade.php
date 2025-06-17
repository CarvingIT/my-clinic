<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-5">
            {{ __('messages.Add Follow Up') }} - {{ $patient->name }}

            <span class="text-gray-600 text-sm">
                @if ($patient->birthdate || $patient->gender)
                    {{-- | {{ __('messages.Age') }}/{{ __('messages.Gender') }}: --}}
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
                <div class="flex flex-wrap items-start gap-x-2 gap-y-2 text-sm">
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Follow-up Creation Form (Left Column) -->
                        <div class="lg:col-span-2">
                            <form method="POST" action="{{ route('followups.store') }}" enctype="multipart/form-data"
                                id="followUpForm">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}" />
                                <input type="file" name="photos[]" id="photoFileInput" style="display:none;"
                                    accept="image/*">
                                <input type="hidden" name="photo_types" id="photoTypesInput">

                                <!-- Naadi Textarea -->
                                <div class="mb-6">
                                    <div class="justify-between flex items-center">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            {{ __('नाडी') }}
                                        </h2>
                                        <button type="button" onclick="openNadiModal()"
                                            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition text-lg">
                                            +
                                        </button>
                                    </div>

                                    <textarea id="nadiInput" name="nadi" rows="4"
                                        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>

                                    <!-- Presets Container -->
                                    <div id="nadiPresets"
                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 mt-4">
                                    </div>

                                    <x-input-error :messages="$errors->get('nadi')" class="mt-2" />
                                </div>

                                <!-- Lakshane Textarea -->
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center justify-between space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('लक्षणे') }}
                                        </h2>
                                        <button type="button" onclick="openLakshaneModal()"
                                            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition">
                                            +
                                        </button>
                                    </div>

                                    <textarea id="lakshane" name="diagnosis" rows="4"
                                        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                    <!-- Preset and Arrow Buttons Below Textarea -->
                                    <div class="mt-4 grid grid-cols-7 gap-2">
                                        <button type="button" onclick="insertArrow('↑')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↑
                                        </button>
                                        <button type="button" onclick="insertArrow('↓')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            ↓
                                        </button>
                                        <div id="lakshanePresets" class="col-span-5 grid grid-cols-5 gap-2"></div>
                                    </div>
                                </div>

                                {{-- Nidaan Input --}}
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center justify-between space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('messages.diagnosis') }}
                                        </h2>
                                    </div>
                                    <input type="text" name="nidan"
                                        class="tinymce-editor002 px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400" />
                                </div>

                                <!-- Chikitsa Textarea -->
                                @php
                                    $latestFollowUp = $followUps->first();
                                    $previousChikitsa = $latestFollowUp ? json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? '' : '';
                                @endphp
                                <div class="mt-6 mb-4 flex flex-col">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between space-x-2">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                                {{ __('चिकित्सा') }}</h2>
                                            <button type="button" onclick="openChikitsaModal()"
                                                class="w-10 h-10 rounded bg-gray-500 text-white text-xl font-bold hover:bg-gray-600 transition">
                                                +
                                            </button>
                                        </div>

                                        <textarea id="chikitsa" name="chikitsa" rows="4"
                                            class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                        <x-input-error :messages="$errors->get('chikitsa')" class="mt-2" />

                                        <!-- Presets Container -->
                                        <div id="chikitsaPresets"
                                            class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mt-4">
                                        </div>
                                    </div>

                                    <!-- Vishesh Textarea -->
                                    <div class="mt-4 mb-4">
                                        <div class="flex items-center justify-between space-x-2">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('messages.Vishesh') }}
                                            </h2>
                                        </div>
                                        <textarea name="vishesh"
                                            class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ $patient->vishesh }}</textarea>
                                    </div>

                                    {{-- Capture button --}}
                                    {{-- <div class="mt-4">
                                        <button type="button" id="openCameraModal"
                                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                            📷 Capture Photos
                                        </button>
                                    </div> --}}


                                    <!-- Camera Modal -->
                                    <div id="cameraModal"
                                        class="fixed inset-0 bg-gray-200 bg-opacity-75 hidden flex justify-center items-center transition-opacity duration-300 z-50">
                                        <div
                                            class="bg-white p-6 rounded-xl shadow-lg w-[800px] h-[650px] flex flex-row gap-6 border border-gray-300">
                                            <!-- Left Side: Camera and Controls -->
                                            <div class="w-1/2 flex flex-col gap-4">
                                                <h2 class="text-2xl font-bold tracking-wider text-blue-600">Capture
                                                    Interface</h2>

                                                <label class="block text-sm text-gray-700">Camera Source:</label>
                                                <select id="cameraSelect"
                                                    class="w-full p-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"></select>

                                                <label class="block text-sm text-gray-700">Capture Type:</label>
                                                <select id="photoType"
                                                    class="w-full p-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                                    <option value="patient_photo">Patient Photo</option>
                                                    <option value="lab_report">Lab Report</option>
                                                </select>

                                                <div
                                                    class="flex-1 overflow-hidden rounded-lg border border-gray-300 shadow-inner bg-gray-200">
                                                    <video id="cameraPreview" class="w-full h-full object-contain"
                                                        autoplay></video>
                                                </div>

                                                <div class="flex justify-between">
                                                    <button id="captureBtn" type="button"
                                                        class="px-5 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transform hover:scale-105 transition-all duration-200 shadow-md">📸
                                                        Capture</button>
                                                    <button id="closeCameraModal" type="button"
                                                        class="px-5 py-2 bg-gradient-to-r from-red-400 to-pink-400 text-white rounded-lg hover:from-red-500 hover:to-pink-500 transform hover:scale-105 transition-all duration-200 shadow-md">Close</button>
                                                </div>
                                            </div>

                                            <!-- Right Side: Separate Preview Sections -->
                                            <div class="w-1/2 flex flex-col gap-4">
                                                <!-- Patient Photos Section -->
                                                <div id="patientPhotosPreview"
                                                    class="flex-1 flex flex-col bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Patient Photos
                                                    </h3>
                                                    <div id="patientPhotosImages" class="flex-1 overflow-y-auto">
                                                    </div>
                                                </div>

                                                <!-- Lab Reports Section -->
                                                <div id="labReportsPreview"
                                                    class="flex-1 flex flex-col bg-gray-50 rounded-lg p-3 border border-gray-200">
                                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Lab Reports
                                                    </h3>
                                                    <div id="labReportsImages" class="flex-1 overflow-y-auto"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Numeric Input Boxes + Payment Method -->
                                    <div class="flex flex-wrap md:flex-nowrap items-start justify-center gap-10 mt-6">

                                        <!-- दिवस -->
                                        <div class="flex flex-col">
                                            <h2 class="text-md font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('दिवस') }}
                                            </h2>
                                            <input type="text" name="days" id="days" placeholder=""
                                                class=" reverse-transliteration py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>

                                        <!-- पुड्या -->
                                        <div class="flex flex-col">
                                            <h2 class="text-md font-semibold text-gray-800 dark:text-white mb-1">
                                                {{ __('पुड्या') }}
                                            </h2>
                                            <input type="text" name="packets" id="packets" placeholder=""
                                                class="reverse-transliteration py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>

                                        <!-- Total Due -->
                                        <div class="flex flex-col pl-2">
                                            <label for="total_due"
                                                class="text-md font-semibold text-gray-600 dark:text-gray-300 mb-1 block">
                                                {{ __('messages.Total Due') }}
                                            </label>
                                            <x-text-input id="total_due"
                                                class="px-3 py-1 block w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-md text-md"
                                                type="number" name="total_due" value="{{ old('total_due', 0) }}"
                                                readonly />
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="flex flex-col pl-2">
                                            <label for="payment_method"
                                                class="text-l font-semibold text-gray-700 dark:text-white mb-2">
                                                {{ __('messages.Payment Method') }}
                                            </label>
                                            <div class="flex items-center space-x-2">
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="cash" />
                                                    <span>Cash</span>
                                                </label>
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="card" />
                                                    <span>Card</span>
                                                </label>
                                                <label class="flex items-center space-x-1">
                                                    <input type="radio" name="payment_method" value="online" />
                                                    <span>Online</span>
                                                </label>
                                            </div>
                                            <x-input-error :messages="$errors->get('payment_method')" class="mt-1" />
                                        </div>


                                    </div>

                                </div>


                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

                                    <!-- All Dues -->
                                    <div style="display: none">
                                        <label for="all_dues"
                                            class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1 block">
                                            {{ __('messages.All Dues') }}
                                        </label>
                                        <x-text-input id="all_dues"
                                            class="px-3 py-2 block w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-md text-md"
                                            type="number" name="all_dues"
                                            value="{{ old('all_dues', $totalDueAll ?? 0) }}" readonly />
                                    </div>

                                    <!-- Total Due -->
                                    {{-- <div>
                                        <label for="total_due"
                                            class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1 block">
                                            {{ __('messages.Total Due') }}
                                        </label>
                                        <x-text-input id="total_due"
                                            class="px-3 py-2 block w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-md text-md"
                                            type="number" name="total_due" value="{{ old('total_due', 0) }}"
                                            readonly />
                                    </div> --}}

                                    <!-- Amount Billed -->
                                    <div>
                                        <label for="amount_billed"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Billed') }}
                                        </label>
                                        <x-text-input id="amount_billed"
                                            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="text" name="amount_billed" required />
                                    </div>

                                    <!-- Amount Paid -->
                                    <div>
                                        <label for="amount_paid"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Paid') }}
                                        </label>
                                        <x-text-input id="amount_paid"
                                            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="text" name="amount_paid" required />
                                    </div>

                                </div>



                                <script>
                                    function calculateTotalDue() {
                                        let allDues = parseFloat(document.getElementById('all_dues').value) || 0;
                                        let amountBilled = parseFloat(document.getElementById('amount_billed').value) || 0;
                                        let amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;

                                        let totalDue = allDues + amountBilled - amountPaid;
                                        // totalDue = totalDue > 0 ? totalDue : 0; // Prevent negative values

                                        document.getElementById('total_due').value = totalDue.toFixed(2); // Ensure 2 decimal places
                                    }

                                    // Ensure script runs after page load
                                    window.onload = function() {
                                        calculateTotalDue();

                                        document.getElementById('amount_billed').addEventListener('input', calculateTotalDue);
                                        document.getElementById('amount_paid').addEventListener('input', calculateTotalDue);
                                    };
                                </script>


                                <!-- Modal to Add New Nadi Preset -->
                                <!-- Nadi Modal -->
                                    <div id="nadiModal"
                                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                        <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">नाडी प्रीसेट व्यवस्थापन</h2>
                                            <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन नाडी जोडा</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                                                        <input type="text" id="nadiButtonText" placeholder="उदा. वेगवती"
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                                                        <input type="text" id="nadiPresetText" placeholder="उदा. वेगवती"
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                    </div>
                                                </div>
                                                <div class="mt-4 flex justify-end space-x-2">
                                                    <button type="button" onclick="clearNadiForm()"
                                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                                                    <button type="button" onclick="saveNadiPreset()"
                                                        class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
                                                </div>
                                            </div>
                                            <div class="max-h-96 overflow-y-auto">
                                                <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                                            <th class="p-2">बटण टेक्स्ट</th>
                                                            <th class="p-2">प्रीसेट टेक्स्ट</th>
                                                            <th class="p-2">स्रोत</th>
                                                            <th class="p-2">कृती</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="nadiPresetList"></tbody>
                                                </table>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button type="button" onclick="closeNadiModal()"
                                                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Lakshane Modal -->
                                    <div id="lakshaneModal"
                                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                        <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">लक्षणे प्रीसेट व्यवस्थापन</h2>
                                            <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन लक्षणे जोडा</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                                                        <input type="text" id="lakshaneButtonText" placeholder="उदा. अजीर्ण"
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                                                        <input type="text" id="lakshanePresetText" placeholder="उदा. अजीर्ण - "
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                    </div>
                                                </div>
                                                <div class="mt-4 flex justify-end space-x-2">
                                                    <button type="button" onclick="clearLakshaneForm()"
                                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                                                    <button type="button" onclick="saveLakshanePreset()"
                                                        class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
                                                </div>
                                            </div>
                                            <div class="max-h-96 overflow-y-auto">
                                                <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                                            <th class="p-2">बटण टेक्स्ट</th>
                                                            <th class="p-2">प्रीसेट टेक्स्ट</th>
                                                            <th class="p-2">स्रोत</th>
                                                            <th class="p-2">कृती</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lakshanePresetList"></tbody>
                                                </table>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button type="button" onclick="closeLakshaneModal()"
                                                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chikitsa Modal -->
                                    <div id="chikitsaModal"
                                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                        <div class="bg-white dark:bg-gray-800 p-6 rounded-md shadow-lg w-full max-w-2xl">
                                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">चिकित्सा प्रीसेट व्यवस्थापन</h2>
                                            <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन चिकित्सा जोडा</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">बटण टेक्स्ट</label>
                                                        <input type="text" id="chikitsaButtonText" placeholder="उदा. ज्वर"
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट टेक्स्ट</label>
                                                        <textarea id="chikitsaPresetText" rows="2" placeholder="उदा. महासुदर्शन, वैदेही..."
                                                            class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white"></textarea>
                                                    </div>
                                                </div>
                                                <div class="mt-4 flex justify-end space-x-2">
                                                    <button type="button" onclick="clearChikitsaForm()"
                                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded">Clear</button>
                                                    <button type="button" onclick="saveChikitsaPreset()"
                                                        class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded">Save</button>
                                                </div>
                                            </div>
                                            <div class="max-h-96 overflow-y-auto">
                                                <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-200 dark:bg-gray-700">
                                                            <th class="p-2">बटण टेक्स्ट</th>
                                                            <th class="p-2">प्रीसेट टेक्स्ट</th>
                                                            <th class="p-2">स्रोत</th>
                                                            <th class="p-2">कृती</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="chikitsaPresetList"></tbody>
                                                </table>
                                            </div>
                                            <div class="mt-4 flex justify-end">
                                                <button type="button" onclick="closeChikitsaModal()"
                                                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded">Close</button>
                                            </div>
                                        </div>
                                    </div>





                                <!-- Submit Button -->
                                <div class="flex items-center justify-between mt-4">

                                    <button type="button" id="openCameraModal"
                                        class="px-5 py-2.5 ms-4 text-xs font-medium tracking-wider bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        CAPTURE PHOTOS
                                    </button>

                                    <x-primary-button class="ms-4">
                                        {{ __('Add Follow Up') }}
                                    </x-primary-button>
                                </div>
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
                                        @foreach ($followUps as $followUp)
                                            <div
                                                class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-sm border border-gray-300 dark:border-gray-600">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $followUp->created_at->format('d M Y, h:i A') }}
                                                </p>
                                                @php $checkUpInfo = json_decode($followUp->check_up_info, true); @endphp
                                                <div
                                                    class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 [p]:m-0 [p]:text-sm [h1]:text-base [h2]:text-sm">
                                                    <strong>नाडी:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfo['nadi'] ?? '-') !!}
                                                    <div class="my-0.5"></div>

                                                    <strong>{{ __('लक्षणे') }}:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $followUp->diagnosis ?? '-') !!}
                                                    <div class="my-0.5"></div>

                                                    <strong>{{ __('चिकित्सा') }}:</strong>
                                                    {!! str_replace(['<p>', '</p>', '<div>', '</div>'], '', $checkUpInfo['chikitsa'] ?? '-') !!}
                                                    <div class="my-0.5"></div>

                                                    @if (!empty($checkUpInfo['days']))
                                                        <strong>{{ __('दिवस') }}:</strong>
                                                        {{ $checkUpInfo['days'] }}
                                                        <div class="my-0.5"></div>
                                                    @endif

                                                    @if (!empty($checkUpInfo['packets']))
                                                        <strong>{{ __('पुड्या') }}:</strong>
                                                        {{ $checkUpInfo['packets'] }}
                                                        <div class="my-0.5"></div>
                                                    @endif

                                                    @php
                                                        $amountPaid = $followUp->amount_paid ?? 0;
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
</x-app-layout>

{{-- <script>
    const storageKey = "customNadiPresets";

    // Append nadi to textarea at cursor
    function appendNadi(nadi) {
        const editor = tinymce.get('nadiInput');
        if (!editor) {
            console.error("TinyMCE editor with ID 'nadiInput' not found.");
            return;
        }

        editor.focus();

        const selection = editor.selection;
        const rng = selection.getRng();
        const container = rng.startContainer;

        // Get full plain text content and the cursor position
        const fullText = editor.getContent({
            format: 'text'
        });
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';

        // Split the node text into before and after parts
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        // Determine if space is needed before or after
        const needsCommaBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
        const needsCommaAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

        // Construct the text to insert
        let insertText = '';
        if (needsCommaBefore) insertText += ' ';
        insertText += nadi;
        if (needsCommaAfter) insertText += ' ';

        // Insert the new text at the cursor
        selection.setContent(insertText);

        // Move the cursor to the end of inserted content
        editor.selection.collapse(false);
        editor.focus();
    }




    function createPresetElement(text, isCustom = true) {
        const presetDiv = document.createElement('div');
        presetDiv.className = "relative";

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            "nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-left pr-6";
        button.innerText = text;
        button.onclick = () => appendNadi(text);

        presetDiv.appendChild(button);

        if (isCustom) {
            const removeBtn = document.createElement('span');
            removeBtn.innerHTML = '❌';
            removeBtn.className = "absolute top-1 right-1 text-red-600 cursor-pointer text-xs";

            removeBtn.onclick = () => {
                if (confirm(`Are you sure you want to delete "${text}"?`)) {
                    presetDiv.remove();
                    removePresetFromStorage(text);
                }
            };

            presetDiv.appendChild(removeBtn);
        }

        document.getElementById('nadiPresets').appendChild(presetDiv);
    }

    function savePresetToStorage(value) {
        const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
        if (!stored.includes(value)) {
            stored.push(value);
            localStorage.setItem(storageKey, JSON.stringify(stored));
        }
    }

    function removePresetFromStorage(value) {
        const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
        const updated = stored.filter(item => item !== value);
        localStorage.setItem(storageKey, JSON.stringify(updated));
    }

    function loadCustomPresets() {
        const stored = JSON.parse(localStorage.getItem(storageKey)) || [];
        stored.forEach(preset => createPresetElement(preset, true));
    }

    // Modal Logic
    function toggleNadiModal(show) {
        const modal = document.getElementById('nadiModal');
        const input = document.getElementById('modalNadiInput');
        modal.classList.toggle('hidden', !show);
        if (show) {
            input.value = '';
            input.focus();
        }
    }

    function saveModalNadi() {
        const input = document.getElementById('modalNadiInput');
        const value = input.value.trim();
        if (!value) return;

        createPresetElement(value, true);
        savePresetToStorage(value);
        toggleNadiModal(false);
    }

    document.addEventListener('DOMContentLoaded', loadCustomPresets);
</script> --}}

<script>
    const nadiFieldId = {{ \App\Models\Field::where('name', 'nadi')->first()->id ?? 0 }};
const nadiStorageKey = 'customNadiPresets';

async function loadNadiPresets() {
    const container = document.getElementById('nadiPresets');
    container.innerHTML = '';

    try {
        const response = await axios.get(`/api/presets?field_id=${nadiFieldId}`);
        response.data.forEach(preset => {
            createPresetButton(preset.button_text, preset.preset_text, preset.id, true);
        });
    } catch (error) {
        console.error('Error loading nadi presets:', error);
    }

    const localPresets = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
    localPresets.forEach(preset => {
        createPresetButton(preset, preset, null, false);
    });
}

function createPresetButton(buttonText, presetText, id, isDatabase) {
    const presetDiv = document.createElement('div');
    presetDiv.className = 'relative';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-left pr-6';
    button.innerText = buttonText;
    button.onclick = () => appendNadi(presetText);

    presetDiv.appendChild(button);
    document.getElementById('nadiPresets').appendChild(presetDiv);
}

async function loadNadiPresetList() {
    const list = document.getElementById('nadiPresetList');
    list.innerHTML = '';

    try {
        const response = await axios.get(`/api/presets?field_id=${nadiFieldId}`);
        response.data.forEach(preset => {
            createPresetRow(preset, true);
        });
    } catch (error) {
        console.error('Error loading nadi preset list:', error);
    }

    const localPresets = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
    localPresets.forEach(preset => {
        createPresetRow({ button_text: preset, preset_text: preset, id: null }, false);
    });
}

function createPresetRow(preset, isDatabase) {
    const row = document.createElement('tr');
    row.className = 'border-b dark:border-gray-600';

    row.innerHTML = `
        <td class="p-2">${preset.button_text}</td>
        <td class="p-2">${preset.preset_text || preset.button_text}</td>
        <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
        <td class="p-2">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteNadiPreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
        </td>
    `;

    document.getElementById('nadiPresetList').appendChild(row);
}

function openNadiModal() {
    document.getElementById('nadiModal').classList.remove('hidden');
    loadNadiPresetList();
    clearNadiForm();
}

function closeNadiModal() {
    document.getElementById('nadiModal').classList.add('hidden');
}

function clearNadiForm() {
    document.getElementById('nadiButtonText').value = '';
    document.getElementById('nadiPresetText').value = '';
}

async function saveNadiPreset() {
    const buttonText = document.getElementById('nadiButtonText').value.trim();
    const presetText = document.getElementById('nadiPresetText').value.trim();

    if (!buttonText) {
        alert('Button text is required.');
        return;
    }

    try {
        await axios.post('/api/presets', {
            field_id: nadiFieldId,
            button_text: buttonText,
            preset_text: presetText || buttonText,
            display_order: 0
        });
        loadNadiPresets();
        loadNadiPresetList();
        clearNadiForm();
    } catch (error) {
        console.error('Error saving nadi preset:', error);
        alert('Failed to save preset.');
    }
}

async function deleteNadiPreset(id, buttonText, isDatabase) {
    if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
        try {
            if (isDatabase && id) {
                await axios.delete(`/api/presets/${id}`);
            } else {
                const stored = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
                const updated = stored.filter(item => item !== buttonText);
                localStorage.setItem(nadiStorageKey, JSON.stringify(updated));
            }
            loadNadiPresets();
            loadNadiPresetList();
        } catch (error) {
            console.error('Error deleting nadi preset:', error);
            alert('Failed to delete preset.');
        }
    }
}

function appendNadi(text) {
    const editor = tinymce.get('nadiInput');
    if (!editor) return;

    editor.focus();
    const rng = editor.selection.getRng();
    const container = rng.startContainer;
    const cursorPos = rng.startOffset;
    const nodeText = container.textContent || '';
    const beforeText = nodeText.substring(0, cursorPos);
    const afterText = nodeText.substring(cursorPos);

    const needsSpaceBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(' ');
    const needsSpaceAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(' ');

    let insertText = '';
    if (needsSpaceBefore) insertText += ' ';
    insertText += text;
    if (needsSpaceAfter) insertText += ' ';

    editor.selection.setContent(insertText);
    editor.selection.collapse(false);
}

document.addEventListener('DOMContentLoaded', loadNadiPresets);
</script>




<script>
    const chikitsaTextarea = document.getElementById('chikitsa');
    const chikitsaStorageKey = "customChikitsaPresets";

    function insertChikitsaText(text) {
        const editor = tinymce.get('chikitsa');
        if (!editor) return;

        editor.focus();

        const rng = editor.selection.getRng();
        const startContainer = rng.startContainer;
        const startOffset = rng.startOffset;

        let precedingChar = '';
        if (startContainer.nodeType === 3 && startOffset > 0) {
            precedingChar = startContainer.data.substring(startOffset - 1, startOffset);
        }

        // For Commas
        // const needsComma = precedingChar && !precedingChar.match(/[\s,]/);
        // const insertText = (needsComma ? ', ' : '') + text;

        // For Spaces
        const needsSpace = precedingChar && !precedingChar.match(/\s/);
        const cleanText = text.replace(/,/g, ''); // 🔥 removes commas from the preset
        const insertText = (needsSpace ? ' ' : '') + cleanText;


        editor.selection.setContent(insertText);
    }


    function createChikitsaPreset(title, value, isCustom = true) {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';

        const btn = document.createElement('div');
        btn.className =
            'border p-2 rounded cursor-pointer bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition';
        btn.textContent = title;
        btn.onclick = () => insertChikitsaText(value);
        wrapper.appendChild(btn);

        if (isCustom) {
            const removeBtn = document.createElement('span');
            removeBtn.innerHTML = '❌';
            removeBtn.className = 'absolute top-1 right-1 text-red-600 cursor-pointer text-xs';
            removeBtn.onclick = () => {
                if (confirm(`"${title}" प्रीसेट काढायचे आहे का?`)) {
                    wrapper.remove();
                    removeChikitsaFromStorage(title);
                }
            };
            wrapper.appendChild(removeBtn);
        }

        document.getElementById('chikitsaPresets').appendChild(wrapper);
    }

    function showChikitsaModal() {
        document.getElementById('chikitsaModal').classList.remove('hidden');
    }

    function hideChikitsaModal() {
        document.getElementById('chikitsaModal').classList.add('hidden');
        document.getElementById('chikitsaPresetTitle').value = '';
        document.getElementById('chikitsaPresetValue').value = '';
    }

    function addChikitsaPreset() {
        const title = document.getElementById('chikitsaPresetTitle').value.trim();
        const value = document.getElementById('chikitsaPresetValue').value.trim();
        if (!title || !value) return alert("कृपया title आणि value दोन्ही भरा.");

        createChikitsaPreset(title, value, true);
        saveChikitsaToStorage({
            title,
            value
        });
        hideChikitsaModal();
    }

    function saveChikitsaToStorage(preset) {
        const stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        const exists = stored.find(p => p.title === preset.title);
        if (!exists) {
            stored.push(preset);
            localStorage.setItem(chikitsaStorageKey, JSON.stringify(stored));
        }
    }

    function removeChikitsaFromStorage(title) {
        let stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        stored = stored.filter(p => p.title !== title);
        localStorage.setItem(chikitsaStorageKey, JSON.stringify(stored));
    }

    function loadChikitsaPresets() {
        const stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        stored.forEach(preset => createChikitsaPreset(preset.title, preset.value, true));
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadChikitsaPresets();

        // For default presets
        document.querySelectorAll('.preset-box').forEach(box => {
            const value = box.dataset.preset;
            box.addEventListener('click', () => insertChikitsaText(value));
        });
    });
</script>




<script>
    function insertArrow(arrow) {
        let textarea = document.getElementById("lakshane");
        let start = textarea.selectionStart;
        let end = textarea.selectionEnd;

        // Insert the arrow at the cursor position
        let text = textarea.value;
        textarea.value = text.substring(0, start) + arrow + text.substring(end);

        // Move cursor after inserted arrow
        textarea.selectionStart = textarea.selectionEnd = start + arrow.length;

        // Focus back on textarea
        textarea.focus();
    }
</script>
<script>
    const lakshaneKey = 'customLakshanePresets';

    function insertText(text) {
        const editor = tinymce.get('lakshane');
        if (!editor) {
            console.error("TinyMCE editor with ID 'lakshane' not found.");
            return;
        }

        editor.focus();

        const selection = editor.selection;
        const rng = selection.getRng();
        const container = rng.startContainer;

        // Get surrounding text to decide if comma and space are needed
        // const cursorPos = rng.startOffset;
        // const nodeText = container.textContent || '';
        // const beforeText = nodeText.substring(0, cursorPos);
        // const afterText = nodeText.substring(cursorPos);

        // const needsCommaBefore = beforeText.trim().length > 0 && !beforeText.trim().endsWith(',');
        // const needsCommaAfter = afterText.trim().length > 0 && !afterText.trim().startsWith(',');

        // let insert = '';
        // if (needsCommaBefore) insert += ', ';
        // insert += text;
        // if (needsCommaAfter) insert += ',';

        // Get surrounding text to decide if space is needed
        const cursorPos = rng.startOffset;
        const nodeText = container.textContent || '';
        const beforeText = nodeText.substring(0, cursorPos);
        const afterText = nodeText.substring(cursorPos);

        const needsSpaceBefore = beforeText.length > 0 && !beforeText.endsWith(' ');
        const needsSpaceAfter = afterText.length > 0 && !afterText.startsWith(' ');

        let insert = '';
        if (needsSpaceBefore) insert += ' ';
        insert += text;
        if (needsSpaceAfter) insert += ' ';


        selection.setContent(insert);
        editor.selection.collapse(false);
        editor.focus();
    }

    function insertArrow(arrow) {
        const editor = tinymce.get('lakshane');
        if (!editor) {
            console.error("TinyMCE editor with ID 'lakshane' not found.");
            return;
        }

        editor.focus();

        // Directly insert the arrow at the current cursor position
        editor.insertContent(arrow);
        editor.selection.collapse(false);
        editor.focus();
    }


    function createLakshaneButton(text, isCustom = true) {
        const container = document.querySelector('.grid-cols-7'); // adjust if your grid changes
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            "w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition text-left pr-6";
        button.innerText = text;
        button.onclick = () => insertText(text);

        wrapper.appendChild(button);

        if (isCustom) {
            const remove = document.createElement('span');
            remove.innerHTML = '❌';
            remove.className = 'absolute top-1 right-1 text-red-600 cursor-pointer text-xs';
            remove.onclick = () => {
                if (confirm(`"${text}" काढायचे आहे का?`)) {
                    wrapper.remove();
                    const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
                    const updated = stored.filter(p => p !== text);
                    localStorage.setItem(lakshaneKey, JSON.stringify(updated));
                }
            };
            wrapper.appendChild(remove);
        }

        container.appendChild(wrapper);
    }

    function openLakshaneModal() {
        document.getElementById('lakshaneModal').classList.remove('hidden');
    }

    function closeLakshaneModal() {
        document.getElementById('lakshaneModal').classList.add('hidden');
    }

    function addLakshanePreset() {
        const input = document.getElementById('newLakshaneInput');
        const value = input.value.trim();
        if (!value) return;

        createLakshaneButton(value, true);

        const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
        if (!stored.includes(value)) {
            stored.push(value);
            localStorage.setItem(lakshaneKey, JSON.stringify(stored));
        }

        input.value = '';
        closeLakshaneModal();
    }

    function loadLakshanePresets() {
        const stored = JSON.parse(localStorage.getItem(lakshaneKey)) || [];
        stored.forEach(p => createLakshaneButton(p, true));
    }

    document.addEventListener('DOMContentLoaded', loadLakshanePresets);
</script>


<script>
    let cameraStream = null;
    let capturedFiles = []; // Array to store captured files and their types

    const cameraModal = document.getElementById("cameraModal");
    const openCameraModal = document.getElementById("openCameraModal");
    const closeCameraModal = document.getElementById("closeCameraModal");
    const captureBtn = document.getElementById("captureBtn");
    const patientPhotosImages = document.getElementById("patientPhotosImages"); // N
    const labReportsImages = document.getElementById("labReportsImages"); // N
    const video = document.getElementById("cameraPreview");
    const cameraSelect = document.getElementById("cameraSelect");
    const photoType = document.getElementById("photoType");
    const photoFileInput = document.getElementById("photoFileInput");
    const photoTypesInput = document.getElementById("photoTypesInput");

    // Safe initialization
    if (photoFileInput && photoTypesInput) {
        if (!photoFileInput.files || photoFileInput.files.length === 0) {
            const dataTransfer = new DataTransfer();
            photoFileInput.files = dataTransfer.files;
        }
        if (!photoTypesInput.value) {
            photoTypesInput.value = "[]";
        }
    } else {
        console.error("Photo inputs not found");
    }

    openCameraModal.addEventListener("click", async (e) => {
        e.preventDefault();
        cameraModal.classList.remove("hidden");
        await loadCameras();
    });

    closeCameraModal.addEventListener("click", (e) => {
        e.preventDefault();
        updateFileInput();
        cameraModal.classList.add("hidden");
        stopCamera();
    });

    async function loadCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === "videoinput");
            if (videoDevices.length === 0) {
                alert("No cameras found.");
                return;
            }
            cameraSelect.innerHTML = "";
            videoDevices.forEach((device, index) => {
                const option = document.createElement("option");
                option.value = device.deviceId;
                option.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(option);
            });
            await startCamera(videoDevices[0]?.deviceId);
        } catch (error) {
            console.error("Error loading cameras:", error);
            alert("Failed to access camera. Please allow permissions.");
        }
    }

    async function startCamera(deviceId) {
        stopCamera();
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    deviceId: deviceId ? {
                        exact: deviceId
                    } : undefined
                }
            });
            video.srcObject = cameraStream;
            video.play();
        } catch (error) {
            console.error("Error starting camera:", error);
            alert("Camera access denied or unavailable.");
        }
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
    }

    cameraSelect.addEventListener("change", () => {
        startCamera(cameraSelect.value);
    });

    captureBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            const file = new File([blob], `photo_${Date.now()}.png`, {
                type: "image/png"
            });
            const photoTypeValue = photoType.value;

            // Add to capturedFiles array
            capturedFiles.push({
                file,
                type: photoTypeValue
            });

            // Create preview container
            const previewContainer = document.createElement("div");
            previewContainer.classList.add("preview-container");

            // Create image preview
            const img = document.createElement("img");
            img.src = URL.createObjectURL(blob);
            img.classList.add("w-full", "h-full", "object-cover", "rounded", "border",
                "border-gray-300");

            // Create delete button
            const deleteBtn = document.createElement("button");
            deleteBtn.innerHTML = "✖";
            deleteBtn.classList.add("delete-btn");
            deleteBtn.addEventListener("click", () => {
                // Remove from capturedFiles
                const index = capturedFiles.findIndex(f => f.file === file);
                if (index !== -1) capturedFiles.splice(index, 1);
                // Remove preview from DOM
                previewContainer.remove();
            });

            // Append elements
            previewContainer.appendChild(img);
            previewContainer.appendChild(deleteBtn);

            // Append to the correct section based on photo type
            if (photoTypeValue === "patient_photo") {
                patientPhotosImages.appendChild(previewContainer);
            } else if (photoTypeValue === "lab_report") {
                labReportsImages.appendChild(previewContainer);
            }
        }, "image/png");
    });

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        const types = [];

        capturedFiles.forEach(({
            file,
            type
        }) => {
            dataTransfer.items.add(file);
            types.push(type);
        });

        photoFileInput.files = dataTransfer.files;
        photoTypesInput.value = JSON.stringify(types); // Store types as JSON string
    }
</script>
