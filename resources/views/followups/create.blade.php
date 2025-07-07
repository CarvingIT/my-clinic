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
                                    $previousChikitsa = $latestFollowUp
                                        ? json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? ''
                                        : '';
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
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">नाडी
                                            प्रीसेट व्यवस्थापन</h2>
                                        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन
                                                नाडी जोडा</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण
                                                        टेक्स्ट</label>
                                                    <input type="text" id="nadiButtonText"
                                                        placeholder="उदा. वेगवती"
                                                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट
                                                        टेक्स्ट</label>
                                                    <input type="text" id="nadiPresetText"
                                                        placeholder="उदा. वेगवती"
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
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">लक्षणे
                                            प्रीसेट व्यवस्थापन</h2>
                                        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन
                                                लक्षणे जोडा</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण
                                                        टेक्स्ट</label>
                                                    <input type="text" id="lakshaneButtonText"
                                                        placeholder="उदा. अजीर्ण"
                                                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट
                                                        टेक्स्ट</label>
                                                    <input type="text" id="lakshanePresetText"
                                                        placeholder="उदा. अजीर्ण - "
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
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">चिकित्सा
                                            प्रीसेट व्यवस्थापन</h2>
                                        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">नवीन
                                                चिकित्सा जोडा</h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm text-gray-700 dark:text-gray-300">बटण
                                                        टेक्स्ट</label>
                                                    <input type="text" id="chikitsaButtonText"
                                                        placeholder="उदा. ज्वर"
                                                        class="w-full px-3 py-2 border rounded dark:bg-gray-900 dark:text-white" />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm text-gray-700 dark:text-gray-300">प्रीसेट
                                                        टेक्स्ट</label>
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


{{-- Script for nadi presets --}}
<script>
    const nadiFieldId = {{ \App\Models\Field::where('name', 'nadi')->first()->id ?? 0 }};
    const nadiStorageKey = 'customNadiPresets';

    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            console.error(
                'CSRF token not found. Add <meta name="csrf-token" content="{{ csrf_token() }}"> to layout.');
            alert('CSRF token not found. Please check your Blade layout.');
        }
        return token || '';
    }

    async function loadNadiPresets() {
        const container = document.getElementById('nadiPresets');
        if (!container) {
            console.error('nadiPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!nadiFieldId) {
            alert('Nadi field ID is invalid (0). Check database seeding for "nadi" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${nadiFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createPresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading nadi presets:', error.response || error);
            alert(
                `Failed to load nadi presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
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
        button.className =
            'nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-500 transition w-full text-left pr-6';
        button.innerText = buttonText;
        button.onclick = () => appendNadi(presetText);

        presetDiv.appendChild(button);
        document.getElementById('nadiPresets').appendChild(presetDiv);
    }

    async function loadNadiPresetList() {
        const list = document.getElementById('nadiPresetList');
        if (!list) {
            console.error('nadiPresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${nadiFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createPresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading nadi preset list:', error.response || error);
            alert(
                `Failed to load nadi preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }

        const localPresets = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
        localPresets.forEach(preset => {
            createPresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
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
        const modal = document.getElementById('nadiModal');
        if (!modal) {
            console.error('nadiModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadNadiPresetList();
        clearNadiForm();
    }

    function closeNadiModal() {
        document.getElementById('nadiModal').classList.add('hidden');
    }

    function clearNadiForm() {
        const buttonText = document.getElementById('nadiButtonText');
        const presetText = document.getElementById('nadiPresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveNadiPreset() {
        const buttonText = document.getElementById('nadiButtonText').value.trim();
        const presetText = document.getElementById('nadiPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: nadiFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadNadiPresets();
            loadNadiPresetList();
            clearNadiForm();
            closeNadiModal();
        } catch (error) {
            console.error('Error saving nadi preset:', error.response || error);
            alert(
                `Failed to save nadi preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }
    }

    async function deleteNadiPreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(nadiStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(nadiStorageKey, JSON.stringify(updated));
                }
                loadNadiPresets();
                loadNadiPresetList();
            } catch (error) {
                console.error('Error deleting nadi preset:', error.response || error);
                alert(
                    `Failed to delete nadi preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
            }
        }
    }

    function appendNadi(text) {
        const editor = tinymce.get('nadiInput');
        if (!editor) {
            console.error('TinyMCE editor for nadiInput not found.');
            return;
        }

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


{{-- Script for chikitsa --}}

<script>
    const chikitsaFieldId = {{ \App\Models\Field::where('name', 'chikitsa')->first()->id ?? 0 }};
    const chikitsaStorageKey = 'customChikitsaPresets';

    async function loadChikitsaPresets() {
        const container = document.getElementById('chikitsaPresets');
        if (!container) {
            console.error('chikitsaPresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!chikitsaFieldId) {
            alert('Chikitsa field ID is invalid (0). Check database seeding for "chikitsa" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${chikitsaFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createChikitsaPresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading chikitsa presets:', error.response || error);
            alert(
                `Failed to load chikitsa presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }

        const localPresets = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        localPresets.forEach(preset => {
            createChikitsaPresetButton(preset, preset, null, false);
        });
    }

    function createChikitsaPresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'border p-2 rounded cursor-pointer bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition w-full text-left pr-6';
        button.innerText = buttonText;
        button.onclick = () => insertChikitsaText(presetText);

        presetDiv.appendChild(button);
        document.getElementById('chikitsaPresets').appendChild(presetDiv);
    }

    function insertChikitsaText(text) {
        const editor = tinymce.get('chikitsa');
        if (!editor) {
            console.error('TinyMCE editor for chikitsa not found.');
            return;
        }

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
        insertText += text.replace(/,/g, '');
        if (needsSpaceAfter) insertText += ' ';

        editor.selection.setContent(insertText);
        editor.selection.collapse(false);
    }

    async function loadChikitsaPresetList() {
        const list = document.getElementById('chikitsaPresetList');
        if (!list) {
            console.error('chikitsaPresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${chikitsaFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createChikitsaPresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading chikitsa preset list:', error.response || error);
            alert(
                `Failed to load chikitsa preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }

        const localPresets = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
        localPresets.forEach(preset => {
            createChikitsaPresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
        });
    }

    function createChikitsaPresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
            <td class="p-2">${preset.button_text}</td>
            <td class="p-2">${preset.preset_text || preset.button_text}</td>
            <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
            <td class="p-2">
                <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteChikitsaPreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
            </td>
        `;

        document.getElementById('chikitsaPresetList').appendChild(row);
    }

    function openChikitsaModal() {
        const modal = document.getElementById('chikitsaModal');
        if (!modal) {
            console.error('chikitsaModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadChikitsaPresetList();
        clearChikitsaForm();
    }

    function closeChikitsaModal() {
        document.getElementById('chikitsaModal').classList.add('hidden');
    }

    function clearChikitsaForm() {
        const buttonText = document.getElementById('chikitsaButtonText');
        const presetText = document.getElementById('chikitsaPresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveChikitsaPreset() {
        const buttonText = document.getElementById('chikitsaButtonText').value.trim();
        const presetText = document.getElementById('chikitsaPresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: chikitsaFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadChikitsaPresets();
            loadChikitsaPresetList();
            clearChikitsaForm();
            closeChikitsaModal();
        } catch (error) {
            console.error('Error saving chikitsa preset:', error.response || error);
            alert(
                `Failed to save chikitsa preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }
    }

    async function deleteChikitsaPreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(chikitsaStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(chikitsaStorageKey, JSON.stringify(updated));
                }
                loadChikitsaPresets();
                loadChikitsaPresetList();
            } catch (error) {
                console.error('Error deleting chikitsa preset:', error.response || error);
                alert(
                    `Failed to delete chikitsa preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadChikitsaPresets);
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
    const lakshaneFieldId = {{ \App\Models\Field::where('name', 'lakshane')->first()->id ?? 0 }};
    const lakshaneStorageKey = 'customLakshanePresets';

    async function loadLakshanePresets() {
        const container = document.getElementById('lakshanePresets');
        if (!container) {
            console.error('lakshanePresets container not found in DOM.');
            return;
        }
        container.innerHTML = '';

        if (!lakshaneFieldId) {
            alert('Lakshane field ID is invalid (0). Check database seeding for "lakshane" in fields table.');
            return;
        }

        try {
            const response = await axios.get(`/presets?field_id=${lakshaneFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createLakshanePresetButton(preset.button_text, preset.preset_text, preset.id, true);
            });
        } catch (error) {
            console.error('Error loading lakshane presets:', error.response || error);
            alert(
                `Failed to load lakshane presets: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }

        const localPresets = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
        localPresets.forEach(preset => {
            createLakshanePresetButton(preset, preset, null, false);
        });
    }

    function createLakshanePresetButton(buttonText, presetText, id, isDatabase) {
        const presetDiv = document.createElement('div');
        presetDiv.className = 'relative';

        const button = document.createElement('button');
        button.type = 'button';
        button.className =
            'w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition text-left pr-6';
        button.innerText = buttonText;
        button.onclick = () => insertLakshaneText(presetText);

        presetDiv.appendChild(button);
        document.getElementById('lakshanePresets').appendChild(presetDiv);
    }

    function insertLakshaneText(text) {
        const editor = tinymce.get('lakshane');
        if (!editor) {
            console.error('TinyMCE editor for lakshane not found.');
            return;
        }

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

    async function loadLakshanePresetList() {
        const list = document.getElementById('lakshanePresetList');
        if (!list) {
            console.error('lakshanePresetList container not found in DOM.');
            return;
        }
        list.innerHTML = '';

        try {
            const response = await axios.get(`/presets?field_id=${lakshaneFieldId}`, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            response.data.forEach(preset => {
                createLakshanePresetRow(preset, true);
            });
        } catch (error) {
            console.error('Error loading lakshane preset list:', error.response || error);
            alert(
                `Failed to load lakshane preset list: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }

        const localPresets = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
        localPresets.forEach(preset => {
            createLakshanePresetRow({
                button_text: preset,
                preset_text: preset,
                id: null
            }, false);
        });
    }

    function createLakshanePresetRow(preset, isDatabase) {
        const row = document.createElement('tr');
        row.className = 'border-b dark:border-gray-600';

        row.innerHTML = `
            <td class="p-2">${preset.button_text}</td>
            <td class="p-2">${preset.preset_text || preset.button_text}</td>
            <td class="p-2">${isDatabase ? 'Database' : 'LocalStorage'}</td>
            <td class="p-2">
                <button type="button" class="text-red-500 hover:text-red-700" onclick="deleteLakshanePreset('${preset.id || ''}', '${preset.button_text}', ${isDatabase})">Delete</button>
            </td>
        `;

        document.getElementById('lakshanePresetList').appendChild(row);
    }

    function openLakshaneModal() {
        const modal = document.getElementById('lakshaneModal');
        if (!modal) {
            console.error('lakshaneModal not found in DOM.');
            return;
        }
        modal.classList.remove('hidden');
        loadLakshanePresetList();
        clearLakshaneForm();
    }

    function closeLakshaneModal() {
        document.getElementById('lakshaneModal').classList.add('hidden');
    }

    function clearLakshaneForm() {
        const buttonText = document.getElementById('lakshaneButtonText');
        const presetText = document.getElementById('lakshanePresetText');
        if (buttonText && presetText) {
            buttonText.value = '';
            presetText.value = '';
        }
    }

    async function saveLakshanePreset() {
        const buttonText = document.getElementById('lakshaneButtonText').value.trim();
        const presetText = document.getElementById('lakshanePresetText').value.trim();

        if (!buttonText) {
            alert('Button text is required.');
            return;
        }

        try {
            await axios.post('/presets', {
                field_id: lakshaneFieldId,
                button_text: buttonText,
                preset_text: presetText || buttonText,
                display_order: 0
            }, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                withCredentials: true
            });
            loadLakshanePresets();
            loadLakshanePresetList();
            clearLakshaneForm();
            closeLakshaneModal();
        } catch (error) {
            console.error('Error saving lakshane preset:', error.response || error);
            alert(
                `Failed to save lakshane preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
        }
    }

    async function deleteLakshanePreset(id, buttonText, isDatabase) {
        if (confirm(`Are you sure you want to delete "${buttonText}"?`)) {
            try {
                if (isDatabase && id) {
                    await axios.delete(`/presets/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'Accept': 'application/json'
                        },
                        withCredentials: true
                    });
                } else {
                    const stored = JSON.parse(localStorage.getItem(lakshaneStorageKey)) || [];
                    const updated = stored.filter(item => item !== buttonText);
                    localStorage.setItem(lakshaneStorageKey, JSON.stringify(updated));
                }
                loadLakshanePresets();
                loadLakshanePresetList();
            } catch (error) {
                console.error('Error deleting lakshane preset:', error.response || error);
                alert(
                    `Failed to delete lakshane preset: ${error.response?.status || 'Unknown'} - ${error.response?.data?.message || error.message}`);
            }
        }
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
