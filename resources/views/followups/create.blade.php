<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight px-5">
            {{ __('messages.Add Follow Up') }} - {{ $patient->name }}

            <span class="text-gray-600 text-sm">
                @if ($patient->birthdate || $patient->gender)
                    {{-- | {{ __('messages.Age') }}/{{ __('messages.Gender') }}: --}}
                    {{ $patient->birthdate?->age ?? __('') }}/{{ $patient->gender ?? __('') }}
                @endif
                @if ($patient->vishesh)
                    | {{ __('messages.Vishesh') }}: {{ $patient->vishesh }}
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
                                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                        {{ __('नाडी') }}
                                    </h2>
                                    <textarea id="nadiInput" name="nadi" rows="4"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4 mt-4">
                                        @foreach (['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम', 'वेग', 'प्राण', 'व्यान', 'स्थूल', 'अल्प स्थूल', 'अनियमित', 'तीक्ष्ण', 'वेगवती'] as $nadi)
                                            <button type="button"
                                                class="nadi-box bg-gray-200 dark:bg-gray-700 p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                onclick="appendNadi('{{ $nadi }}')">{{ $nadi }}</button>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />
                                </div>


                                <!-- Diagnosis Textarea -->
                                <div class="mt-4 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
                                            {{ __('लक्षणे') }}
                                        </h2>
                                    </div>

                                    <textarea id="lakshane" name="diagnosis" rows="4"
                                        class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
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
                                        <button type="button" onclick="insertText('मल- ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मल
                                        </button>
                                        <button type="button" onclick="insertText('मूत्र - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            मूत्र
                                        </button>
                                        <button type="button" onclick="insertText('जिव्हा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            जिव्हा
                                        </button>
                                        <button type="button" onclick="insertText('निद्रा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            निद्रा
                                        </button>
                                        <button type="button" onclick="insertText('क्षुधा - ')"
                                            class="w-full px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                            क्षुधा
                                        </button>
                                    </div>
                                </div>


                                @php
                                    // Fetch the latest follow-up's 'chikitsa' if available
$latestFollowUp = $followUps->first();
$previousChikitsa = $latestFollowUp
    ? json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? ''
    : '';
                                @endphp
                                <!-- Chikitsa Textarea -->
                                <div class="mt-6 mb-4 flex flex-col">
                                    <div class="flex-1">
                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                                            {{ __('चिकित्सा') }}</h2>
                                        <textarea id="chikitsa" name="chikitsa" rows="4"
                                            class="px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"></textarea>
                                        <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

                                        <div class="mt-4 grid grid-cols-5 gap-4">
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी ">
                                                ज्वर
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="वरा, गुग्गुळ, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती">
                                                संधिशूल
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="हरीतकी, अमृता, सारिवा ">
                                                अर्श
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="कुटज, मुस्ता, विश्व ">
                                                ग्रहणी
                                            </div>
                                            <div class="border p-2 rounded cursor-pointer preset-box bg-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                                data-preset="{{ $previousChikitsa }}">
                                                चिकित्सा यथा पूर्व
                                            </div>
                                        </div>
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
                                    <div class="flex flex-wrap md:flex-nowrap items-start gap-10 mt-6">

                                        <!-- दिवस -->
                                        <div class="flex flex-col">
                                            <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                                {{ __('दिवस') }}
                                            </h2>
                                            <input type="number" name="days" id="days" placeholder=""
                                                class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>

                                        <!-- पुड्या -->
                                        <div class="flex flex-col">
                                            <h2 class="text-l font-semibold text-gray-800 dark:text-white mb-2">
                                                {{ __('पुड्या') }}
                                            </h2>
                                            <input type="number" name="packets" id="packets" placeholder=""
                                                class="px-2 py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="flex flex-col pl-32">
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
                                    <div>
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
                                    <div>
                                        <label for="total_due"
                                            class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1 block">
                                            {{ __('messages.Total Due') }}
                                        </label>
                                        <x-text-input id="total_due"
                                            class="px-3 py-2 block w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-white rounded-lg shadow-md text-md"
                                            type="number" name="total_due" value="{{ old('total_due', 0) }}"
                                            readonly />
                                    </div>

                                    <!-- Amount Billed -->
                                    <div>
                                        <label for="amount_billed"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Billed') }}
                                        </label>
                                        <x-text-input id="amount_billed"
                                            class="px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="number" name="amount_billed" required />
                                    </div>

                                    <!-- Amount Paid -->
                                    <div>
                                        <label for="amount_paid"
                                            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
                                            {{ __('messages.Amount Paid') }}
                                        </label>
                                        <x-text-input id="amount_paid"
                                            class="px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
                                            type="number" name="amount_paid" required />
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
                        <div
                            class="fixed mb-3 right-40 top-62 max-h-[calc(100vh-250px)] w-[375px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 scrollbar-thin">
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
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                <strong>{{ __('नाडी') }}:</strong>
                                                {{ $checkUpInfo['nadi'] ?? '-' }}<br>
                                                <strong>{{ __('लक्षणे') }}:</strong>
                                                {{ $followUp->diagnosis ?? '-' }}<br>
                                                <strong>{{ __('चिकित्सा') }}:</strong>
                                                {{ $checkUpInfo['chikitsa'] ?? '-' }}
                                            </p>
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
</x-app-layout>
<script>
    function appendNadi(nadi) {
        const input = document.getElementById('nadiInput');
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const currentValue = input.value;

        const before = currentValue.substring(0, start);
        const after = currentValue.substring(end);

        // Add comma if needed before or after
        const insert = (before && !before.endsWith(', ') ? ', ' : '') + nadi + (after && !after.startsWith(',') ? ', ' : '');

        // Set updated value and focus
        input.value = before + insert + after;

        const newPosition = before.length + insert.length;
        input.setSelectionRange(newPosition, newPosition);
        input.focus();
    }
</script>


<script>
    const textarea = document.getElementById('chikitsa');
    const presetBoxes = document.querySelectorAll('.preset-box');

    presetBoxes.forEach(box => {
        box.addEventListener('click', () => {
            const presetText = box.dataset.preset;

            // Get current cursor position
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const currentText = textarea.value;

            // Insert presetText at cursor
            const before = currentText.substring(0, start);
            const after = currentText.substring(end);

            // Add comma if needed before/after
            const insert = (before && !before.endsWith(', ') ? ', ' : '') + presetText + (after && !after.startsWith(',') ? ', ' : '');

            // Update textarea value
            textarea.value = before + insert + after;

            // Move the cursor after inserted text
            const newPosition = before.length + insert.length;
            textarea.setSelectionRange(newPosition, newPosition);
            textarea.focus();
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
     function insertText(text) {
        let textarea = document.getElementById("lakshane");
        let start = textarea.selectionStart;
        let end = textarea.selectionEnd;

        // Get current text and insert the new text at cursor
        let currentText = textarea.value;
        textarea.value = currentText.substring(0, start) + text + currentText.substring(end);

        // Move cursor to after the inserted text
        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        textarea.focus();
    }
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
