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
    if (!isset($previousChikitsa)) {
        $latestFollowUp = isset($followUps) ? $followUps->first() : null;
        $previousChikitsa = $latestFollowUp
            ? (json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? '')
            : '';
    }
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

    <!-- Camera Modal (Native Mobile & Desktop Responsive UI) -->
    <div id="cameraModal"
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-md hidden flex justify-center items-center z-50 p-0 sm:p-4 transition-all duration-300">
        
        <div class="bg-white dark:bg-slate-900 w-full sm:max-w-5xl h-full sm:h-auto sm:max-h-[90vh] sm:rounded-3xl shadow-2xl border-0 sm:border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row overflow-hidden divide-y md:divide-y-0 md:divide-x divide-slate-200 dark:divide-slate-800">
            
            <!-- Left Side: Viewfinder & Camera Controls (Mobile App Experience / Desktop 3/5 Column) -->
            <div class="w-full md:w-3/5 flex flex-col justify-between p-3 sm:p-5 bg-white dark:bg-slate-900 relative flex-1 min-h-0">
                
                <!-- Top Header & Segmented Mode Switcher Bar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 z-10 pb-2 border-b border-slate-100 dark:border-slate-800">
                    
                    <!-- Segmented Tab Switcher (Clear Visual Selection) -->
                    <div class="grid grid-cols-2 p-1 bg-slate-200/80 dark:bg-slate-800 rounded-2xl border border-slate-300/70 dark:border-slate-700 shadow-inner flex-1 max-w-sm">
                        <button type="button" id="tabPatientPhotoBtn" onclick="selectCaptureType('patient_photo')"
                            class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all duration-200 bg-indigo-600 text-white shadow-md flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Patient Photo</span>
                        </button>
                        <button type="button" id="tabLabReportBtn" onclick="selectCaptureType('lab_report')"
                            class="py-1.5 px-3 rounded-xl text-xs font-semibold transition-all duration-200 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center gap-1.5 cursor-pointer bg-transparent">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Lab Report</span>
                        </button>
                    </div>

                    <!-- Flip Camera & Close Action Icons -->
                    <div class="flex items-center justify-end gap-2">
                        <button id="switchCameraBtn" type="button" title="Switch Camera"
                            class="px-3 py-1.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Flip</span>
                        </button>

                        <button id="closeCameraModal" type="button"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition font-bold text-sm">
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Hidden select for form JS compatibility -->
                <select id="photoType" class="hidden">
                    <option value="patient_photo">Patient Photo</option>
                    <option value="lab_report">Lab Report</option>
                </select>

                <!-- Live Stream Video Viewport (Native Camera Feel) -->
                <div id="viewfinderContainer" class="relative w-full my-auto flex-1 min-h-[260px] sm:min-h-[320px] rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center border border-slate-800 shadow-inner my-2">
                    <video id="cameraPreview" class="w-full h-full object-contain rounded-2xl" autoplay playsinline></video>
                    
                    <!-- Flash Effect overlay -->
                    <div id="cameraFlash" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150 z-20"></div>

                    <!-- Live Status Badge -->
                    <div class="absolute top-3 left-3 bg-slate-950/70 backdrop-blur-md px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-white/10 z-10">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-slate-100 tracking-wider">LIVE</span>
                    </div>
                </div>

                <!-- Native Mobile/Desktop Shutter Action Bar -->
                <div class="pt-2 flex items-center justify-between gap-3 z-10">
                    <div class="w-1/3">
                        <select id="cameraSelect"
                            class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 font-medium focus:ring-2 focus:ring-indigo-500 shadow-sm truncate"></select>
                    </div>

                    <!-- Large Circular Camera Shutter Button -->
                    <div class="w-1/3 flex justify-center">
                        <button id="captureBtn" type="button" title="Capture Photo"
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-full border-4 border-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 p-1 flex items-center justify-center transition hover:scale-105 active:scale-90 shadow-lg cursor-pointer">
                            <div class="w-full h-full bg-indigo-600 rounded-full flex items-center justify-center text-white shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9z"></path>
                                    <circle cx="12" cy="13" r="3" stroke-width="2"></circle>
                                </svg>
                            </div>
                        </button>
                    </div>

                    <div class="w-1/3 text-right">
                        <button type="button" onclick="document.getElementById('closeCameraModal').click()"
                            class="px-4 sm:px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow transition cursor-pointer">
                            Done
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side / Mobile Bottom Panel: Captured Media Gallery -->
            <div class="w-full md:w-2/5 p-4 sm:p-5 bg-slate-50/90 dark:bg-slate-800/50 flex flex-col gap-3.5 max-h-[30vh] md:max-h-none overflow-y-auto border-t md:border-t-0 border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Captured Gallery</h3>
                    </div>
                    <span id="totalCapturedBadge" class="text-[11px] bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 font-bold px-2.5 py-0.5 rounded-full border border-indigo-200 dark:border-indigo-800">0 items</span>
                </div>

                <!-- Patient Photos Card Section -->
                <div class="flex flex-col gap-2 bg-white dark:bg-slate-800 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>👤 Patient Photos</span>
                        <span id="patientPhotoCount" class="text-[11px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full">0</span>
                    </div>
                    <div id="patientPhotosImages" class="flex flex-wrap gap-2.5 min-h-[50px] p-1">
                        <!-- Dynamic Items -->
                    </div>
                </div>

                <!-- Lab Reports Card Section -->
                <div class="flex flex-col gap-2 bg-white dark:bg-slate-800 rounded-2xl p-3 border border-slate-200/80 dark:border-slate-700/80 shadow-sm">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>📄 Lab Reports</span>
                        <span id="labReportCount" class="text-[11px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded-full">0</span>
                    </div>
                    <div id="labReportsImages" class="flex flex-wrap gap-2.5 min-h-[50px] p-1">
                        <!-- Dynamic Items -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Image Preview Lightbox Modal -->
    <div id="imagePreviewModal" class="fixed inset-0 bg-slate-950/90 backdrop-blur-md hidden flex flex-col items-center justify-center z-[60] p-4 transition-all duration-300">
        <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center justify-center">
            <button type="button" onclick="closeImagePreviewModal()" class="absolute -top-12 right-0 text-white bg-slate-800/80 hover:bg-slate-700 px-4 py-1.5 rounded-full text-xs font-bold transition-all shadow-xl backdrop-blur-md flex items-center gap-1.5">
                <span>✕ Close</span>
            </button>
            <img id="fullSizePreviewImage" src="" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-slate-800 bg-slate-950" />
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
