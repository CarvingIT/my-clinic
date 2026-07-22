<input type="file" name="photos[]" id="photoFileInput" style="display:none;" accept="image/*">
<input type="hidden" name="photo_types" id="photoTypesInput">
<input type="hidden" name="reports" id="reportsInput" value="{{ old('reports', json_encode($isEdit ? ($checkUpInfo['reports'] ?? []) : [])) }}">

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
        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('nadi', $isEdit ? ($checkUpInfo['nadi'] ?? '') : '') }}</textarea>

    <!-- Nadi Dots Grid -->
    <div id="nadiGrid" class="mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 p-2 rounded shadow-lg flex gap-1 justify-center items-center">
        <span class="text-sm text-gray-600 dark:text-gray-400 mr-4">Nadi Points:</span>

        @php
            // Identify grid type from environment, default to legacy 3x3 layout.
            $nadiGridType = env('NADI_GRID_TYPE', '3x3');
        @endphp

        @if($nadiGridType === '5x1')
            <!-- Dynamic 5x1 Layout (5 columns, 1 row per box) -->
            @for($box = 0; $box < 3; $box++)
                <div class="grid grid-cols-5 gap-0 bg-gray-100 dark:bg-gray-600 p-0.5 rounded">
                    @for($i = 0; $i < 5; $i++)
                        <div class="w-4 h-4 cursor-pointer flex items-center justify-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $i < 4 ? 'border-r border-gray-300 dark:border-gray-500' : '' }}"
                             onclick="toggleDot(this, {{ $box }}, {{ $i }})"></div>
                    @endfor
                </div>
            @endfor
        @else
            <!-- Legacy 3x3 Layout (3 columns, 3 rows per box) -->
            @for($box = 0; $box < 3; $box++)
                <div class="grid grid-cols-3 gap-0 bg-gray-100 dark:bg-gray-600 p-0.5 rounded">
                    @for($i = 0; $i < 9; $i++)
                        <div class="w-4 h-4 cursor-pointer flex items-center justify-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $i % 3 != 2 ? 'border-r border-gray-300 dark:border-gray-500' : '' }} {{ $i < 6 ? 'border-b border-gray-300 dark:border-gray-500' : '' }}"
                             onclick="toggleDot(this, {{ $box }}, {{ $i }})"></div>
                    @endfor
                </div>
            @endfor
        @endif
    </div>

    <!-- Hidden input for dots data -->
    <input type="hidden" name="nadi_dots" id="nadiDotsInput" value="{{ old('nadi_dots', json_encode($isEdit ? ($checkUpInfo['nadi_dots'] ?? [[], [], []]) : [[], [], []])) }}">

    <!-- Presets Container -->
    <div id="nadiPresets"
        class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 gap-2 mt-4">
    </div>

    <x-input-error :messages="$errors->get('nadi')" class="mt-2" />
    <x-input-error :messages="$errors->get('nadi_dots')" class="mt-2" />
</div>

<!-- Lakshane Textarea -->
<div class="mt-4 mb-4">
    <div class="flex items-center justify-between space-x-2">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-1">
            {{ __('लक्षणे') }}
        </h2>
        <button type="button" onclick="openLakshaneModal()"
            class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition text-lg">
            +
        </button>
    </div>

    <textarea id="lakshane" name="diagnosis" rows="4"
        class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('diagnosis', $isEdit ? $followup->diagnosis : '') }}</textarea>
    <x-input-error :messages="$errors->get('diagnosis')" class="mt-2" />

    <!-- Presets Container with Arrows First -->
    <div id="lakshanePresetsContainer"
        class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 gap-2 mt-4">
        <!-- Arrow Buttons (same style as presets) -->
        <button type="button" onclick="insertArrow('↑')"
            class="w-full h-10 bg-gray-200 dark:bg-gray-700 px-3 py-1 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            ↑
        </button>
        <button type="button" onclick="insertArrow('↓')"
            class="w-full h-10 bg-gray-200 dark:bg-gray-700 px-3 py-1 rounded shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            ↓
        </button>

        <!-- Dynamic Presets Will Append Here -->
        <div id="lakshanePresets" class="contents w-full h-10"></div>
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
        class="tinymce-editor002 px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400"
        value="{{ old('nidan', $isEdit ? ($checkUpInfo['nidan'] ?? '') : '') }}" />
</div>

@php
    // Fetch the latest follow-up's 'chikitsa' if available
    $latestFollowUp = $followUps->first();
    $previousChikitsa = $latestFollowUp
        ? json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? ''
        : '';
@endphp

<!-- Chikitsa Textarea with Dravya Popup -->
<div class="mt-6 mb-4 flex flex-col">
    <div class="flex-1">
        <div class="flex items-start justify-between space-x-2 mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                {{ __('चिकित्सा') }}
            </h2>
            <div>
                <button type="button" onclick="openChikitsaModal()"
                    class="w-10 h-10 rounded bg-gray-500 text-white text-xl font-bold hover:bg-gray-600 transition mr-2">
                    +
                </button>
                <button type="button" onclick="showDravyaPopup()"
                    class="w-24 h-10 rounded bg-green-500 text-white text-sm font-semibold hover:bg-green-600 transition">
                    द्रव्य
                </button>
            </div>
        </div>

        <textarea id="chikitsa" name="chikitsa" rows="4"
            class="tinymce-editor px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('chikitsa', $isEdit ? ($checkUpInfo['chikitsa'] ?? '') : '') }}</textarea>
        <x-input-error :messages="$errors->get('chikitsa')" class="mt-2" />

        <!-- Presets Container -->
        <div id="chikitsaPresets"
            class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 mt-4"></div>
    </div>

    <!-- Dravya Popup -->
    <div id="dravyaPopup"
        class="fixed hidden bg-white dark:bg-gray-800 p-4 rounded shadow-md border border-gray-300 dark:border-gray-600 overflow-y-auto z-50"
        style="top: 200px; right: 150px; width: 400px; max-height: 70vh;">
        <div class="relative">
            <!-- Action Buttons Row -->
            <div class="absolute pb-4 right-4 flex items-center space-x-2 z-10">
                <!-- Add Button -->
                <button type="button" id="addDravyaBtn" onclick="toggleDravyaForm()"
                    class="bg-green-500 text-white hover:bg-green-600 w-8 h-8 rounded-full flex items-center justify-center text-base font-bold shadow">
                    +
                </button>

                <!-- Edit Button -->
                <button type="button" id="editDravyaBtn"
                    onclick="toggleEditDravyaMode()"
                    class="text-blue-600 hover:text-blue-800 w-8 h-8 rounded-full flex items-center justify-center text-base font-bold border border-blue-600 shadow">
                    ✎
                </button>

                <!-- Close Button -->
                <button type="button" onclick="hideDravyaPopup()"
                    class="text-red-600 hover:text-red-800 w-8 h-8 rounded-full flex items-center justify-center text-base font-bold border border-red-600 shadow">
                    ×
                </button>
            </div>

            <h3 class="text-base font-semibold mb-3 text-gray-800 dark:text-white">
                द्रव्य प्रीसेट्स</h3>

            <!-- Inline Form for Adding New Dravya -->
            <div id="dravyaForm"
                class="mb-3 p-3 bg-gray-100 dark:bg-gray-700 rounded hidden">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">
                    नवीन द्रव्य जोडा</h4>
                <div class="grid grid-cols-1 gap-2">
                    <input type="text" id="dravyaButtonText"
                        placeholder="उदा. अश्वगंधा"
                        class="w-full px-2 py-1 border rounded dark:bg-gray-900 dark:text-white text-sm" />
                    <input type="text" id="dravyaPresetText"
                        placeholder="उदा. अश्वगंधा"
                        class="w-full px-2 py-1 border rounded dark:bg-gray-900 dark:text-white text-sm" />
                </div>
                <div class="mt-2 flex justify-end space-x-2">
                    <button type="button" onclick="clearDravyaForm()"
                        class="px-2 py-1 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 rounded text-xs">Clear</button>
                    <button type="button" onclick="saveDravyaPreset()"
                        class="px-2 py-1 bg-blue-500 text-white hover:bg-blue-600 rounded text-xs">Save</button>
                </div>
            </div>

            <!-- Dynamic Dravya Presets -->
            <div id="dravyaPresets" class="grid grid-cols-4 gap-2"></div>

            <div class="mt-3 flex justify-end">
                <button type="button" onclick="hideDravyaPopup()"
                    class="px-3 py-1 bg-red-300 hover:bg-red-400 rounded dark:bg-red-600 dark:hover:bg-red-500 text-black dark:text-white text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Vishesh Textarea -->
    <div class="mt-4 mb-4">
        <div class="flex items-center justify-between space-x-2">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                {{ __('messages.Vishesh') }}
            </h2>
            <button type="button" onclick="openVisheshModal()"
                class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600 transition text-lg">
                +
            </button>
        </div>
        <textarea id="vishesh" name="vishesh"
            class="tinymce-editor px-2 py-1 block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400">{{ old('vishesh', $patient->vishesh) }}</textarea>

        <!-- Presets Container -->
        <div id="visheshPresets"
            class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 gap-2 mt-4">
        </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal"
        class="fixed inset-0 bg-gray-200 bg-opacity-75 hidden flex justify-center items-center transition-opacity duration-300 z-50">
        <div
            class="bg-white p-6 rounded-xl shadow-lg w-[800px] h-[650px] flex flex-row gap-6 border border-gray-300">
            <!-- Left Side: Camera and Controls -->
            <div class="w-1/2 flex flex-col gap-4">
                <h2 class="text-2xl font-bold tracking-wider text-blue-600">Capture Interface</h2>

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
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Patient Photos</h3>
                    <div id="patientPhotosImages" class="flex-1 overflow-y-auto"></div>
                </div>

                <!-- Lab Reports Section -->
                <div id="labReportsPreview"
                    class="flex-1 flex flex-col bg-gray-50 rounded-lg p-3 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Lab Reports</h3>
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
                value="{{ old('days', $isEdit ? ($checkUpInfo['days'] ?? '') : '') }}"
                class="reverse-transliteration py-1 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm transition-all duration-300 hover:border-indigo-400 w-24" />
        </div>

        <!-- पुड्या -->
        <div class="flex flex-col">
            <h2 class="text-md font-semibold text-gray-800 dark:text-white mb-1">
                {{ __('पुड्या') }}
            </h2>
            <input type="text" name="packets" id="packets" placeholder=""
                value="{{ old('packets', $isEdit ? ($checkUpInfo['packets'] ?? '') : '') }}"
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
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="payment_method" value="cash" class="payment-method-radio"
                        @if(str_contains(strtolower(old('payment_method', $isEdit ? ($followup->payment_method ?? '') : '')), 'cash')) checked @endif />
                    <span>Cash</span>
                </label>
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="payment_method" value="card" class="payment-method-radio"
                        @if(str_contains(strtolower(old('payment_method', $isEdit ? ($followup->payment_method ?? '') : '')), 'card')) checked @endif />
                    <span>Card</span>
                </label>
                <label class="flex items-center space-x-1 cursor-pointer">
                    <input type="radio" name="payment_method" value="online" class="payment-method-radio"
                        @if(str_contains(strtolower(old('payment_method', $isEdit ? ($followup->payment_method ?? '') : '')), 'online')) checked @endif />
                    <span>Online</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('payment_method')" class="mt-1" />
        </div>

        <script>
            // Payment method deselection functionality
            let lastSelectedPaymentMethod = null;
            const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');

            paymentMethodRadios.forEach(radio => {
                radio.addEventListener('click', function(e) {
                    if (this.checked && lastSelectedPaymentMethod === this.value) {
                        // If clicking the same option that's already selected, deselect it
                        this.checked = false;
                        lastSelectedPaymentMethod = null;
                    } else {
                        // If clicking a different option, select it
                        lastSelectedPaymentMethod = this.value;
                    }
                });
            });

            // Initialize last selected on page load
            document.addEventListener('DOMContentLoaded', function() {
                const checkedRadio = document.querySelector('.payment-method-radio:checked');
                if (checkedRadio) {
                    lastSelectedPaymentMethod = checkedRadio.value;
                }
            });
        </script>


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

    <!-- Amount Billed -->
    <div>
        <label for="amount_billed"
            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
            {{ __('messages.Amount Billed') }}
        </label>
        <x-text-input id="amount_billed"
            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
            type="text" name="amount_billed" value="{{ old('amount_billed', $isEdit ? ($followup->amount_billed ?? 0) : '') }}" required />
    </div>

    <!-- Amount Paid -->
    <div>
        <label for="amount_paid"
            class="text-md font-semibold text-gray-700 dark:text-white mb-1 block">
            {{ __('messages.Amount Paid') }}
        </label>
        <x-text-input id="amount_paid"
            class="reverse-transliteration px-2 py-1 block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-md text-md"
            type="text" name="amount_paid" value="{{ old('amount_paid', $isEdit ? ($amountPaid ?? 0) : '') }}" required />
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

        // Listen for Marathi conversion events on amount fields
        document.getElementById('amount_billed').addEventListener('marathiConverted', calculateTotalDue);
        document.getElementById('amount_paid').addEventListener('marathiConverted', calculateTotalDue);
    };
</script>

<!-- Submit Button -->
<div class="flex items-center justify-between mt-4">

    <button type="button" id="openCameraModal"
        class="px-5 py-2.5 text-xs font-medium tracking-wider bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        CAPTURE PHOTOS
    </button>

    <x-primary-button class="ms-4">
        {{ $isEdit ? __('Update Follow Up') : __('Add Follow Up') }}
    </x-primary-button>
</div>
