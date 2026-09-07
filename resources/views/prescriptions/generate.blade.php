<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Preview - {{ $patient->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --font-scale: 1;
            --margin-top: 6mm;
            --margin-right: 8mm;
            --margin-bottom: 6mm;
            --margin-left: 8mm;
        }
        @page {
            size: A5 portrait;
            margin: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);
        }
        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-toolbar {
                display: none !important;
            }
            .preview-outer {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .prescription-wrapper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .prescription-page {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
        #prescriptionVisual {
            zoom: var(--font-scale);
        }
    </style>
    <!-- Dynamic Template Styles -->
    <style>
        {!! $templateStyles !!}
    </style>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800">
    <!-- Toolbar -->
    <div class="print-toolbar sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <!-- Left side Title -->
            <div class="shrink-0">
                <h1 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-file-prescription text-emerald-600"></i> Prescription Preview
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $patient->name }} &bull; {{ $followup->created_at->format('d M Y') }}</p>
            </div>

            <!-- Right side Controls -->
            <div class="flex gap-2 items-center flex-nowrap shrink-0 overflow-x-auto">
                <!-- Margins Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner text-xs">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Margin (mm)</span>
                    </div>
                    <div class="flex items-center px-1" title="Top Margin">
                        <i class="fas fa-arrow-up text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginTop" value="6" min="0" max="50" class="w-9 h-7 bg-transparent border-none text-center text-xs font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Right Margin">
                        <i class="fas fa-arrow-right text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginRight" value="8" min="0" max="50" class="w-9 h-7 bg-transparent border-none text-center text-xs font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Bottom Margin">
                        <i class="fas fa-arrow-down text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginBottom" value="6" min="0" max="50" class="w-9 h-7 bg-transparent border-none text-center text-xs font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Left Margin">
                        <i class="fas fa-arrow-left text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginLeft" value="8" min="0" max="50" class="w-9 h-7 bg-transparent border-none text-center text-xs font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                </div>

                <!-- Font Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner text-xs">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Zoom</span>
                    </div>
                    <button onclick="changeFontSize(-0.05)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition" title="Decrease Size">
                        <i class="fas fa-minus text-[10px]"></i>
                    </button>
                    <span class="w-12 text-center text-xs font-bold text-gray-700" id="fontSizeDisplay">100%</span>
                    <button onclick="changeFontSize(0.05)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition mr-1" title="Increase Size">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                    <div class="border-l border-gray-200 pl-1">
                        <button onclick="resetFontSize()" type="button" class="px-2 py-1 text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 rounded transition">RESET</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 pl-2 border-l border-gray-300">
                    <button onclick="goBack()" class="h-8 px-3 bg-white border border-gray-300 hover:bg-gray-50 hover:text-emerald-700 text-gray-700 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-edit"></i> Edit
                    </button>

                    <button onclick="window.print()" class="h-8 px-3 bg-gray-800 hover:bg-black text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <button type="button" onclick="document.getElementById('downloadPdfForm').submit()" class="h-8 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-download"></i> PDF
                    </button>
                </div>

                <!-- Hidden form for PDF download -->
                <form id="downloadPdfForm" action="{{ route('followups.prescription.download', ['followup' => $followup->id]) }}" method="POST" style="display: none;">
                    @csrf
                    @foreach ($selectedFields as $field)
                        <input type="hidden" name="selected_fields[]" value="{{ $field }}">
                    @endforeach
                    @foreach ($data as $key => $value)
                        @if (!str_ends_with($key, '_label'))
                            <input type="hidden" name="field_values[{{ str_replace('_label', '', $key) }}]" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="margin_top" id="input_margin_top" value="6">
                    <input type="hidden" name="margin_right" id="input_margin_right" value="8">
                    <input type="hidden" name="margin_bottom" id="input_margin_bottom" value="6">
                    <input type="hidden" name="margin_left" id="input_margin_left" value="8">
                    <input type="hidden" name="font_scale" id="fontScaleInput" value="1">
                </form>
            </div>
        </div>
    </div>

    <!-- Prescription Preview Canvas (Rendered Directly From Template) -->
    <div class="preview-outer max-w-3xl mx-auto my-6 px-4">
        <div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);" class="prescription-wrapper bg-white rounded-lg shadow-md border border-gray-200">
            {!! $templateBody !!}
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'prescription-settings';

        function loadSettings() {
            const settings = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};

            if (settings.fontScale) {
                currentFontScale = settings.fontScale;
                updateFontScale();
            }

            if (settings.margins) {
                document.getElementById('marginTop').value = settings.margins.top || 6;
                document.getElementById('marginRight').value = settings.margins.right || 8;
                document.getElementById('marginBottom').value = settings.margins.bottom || 6;
                document.getElementById('marginLeft').value = settings.margins.left || 8;
                updateMargins();
            }
        }

        function saveSettings() {
            const settings = {
                fontScale: currentFontScale,
                margins: {
                    top: document.getElementById('marginTop').value,
                    right: document.getElementById('marginRight').value,
                    bottom: document.getElementById('marginBottom').value,
                    left: document.getElementById('marginLeft').value
                }
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
        }

        function goBack() {
            window.history.back();
        }

        let currentFontScale = 1;

        function changeFontSize(delta) {
            currentFontScale = Math.round((currentFontScale + delta) * 100) / 100;
            if (currentFontScale < 0.6) currentFontScale = 0.6;
            if (currentFontScale > 1.8) currentFontScale = 1.8;
            updateFontScale();
            saveSettings();
        }

        function resetFontSize() {
            currentFontScale = 1;
            updateFontScale();
            saveSettings();
        }

        function updateMargins() {
            const mt = document.getElementById('marginTop').value || 0;
            const mr = document.getElementById('marginRight').value || 0;
            const mb = document.getElementById('marginBottom').value || 0;
            const ml = document.getElementById('marginLeft').value || 0;

            document.documentElement.style.setProperty('--margin-top', mt + 'mm');
            document.documentElement.style.setProperty('--margin-right', mr + 'mm');
            document.documentElement.style.setProperty('--margin-bottom', mb + 'mm');
            document.documentElement.style.setProperty('--margin-left', ml + 'mm');

            document.getElementById('input_margin_top').value = mt;
            document.getElementById('input_margin_right').value = mr;
            document.getElementById('input_margin_bottom').value = mb;
            document.getElementById('input_margin_left').value = ml;

            saveSettings();
        }

        function updateFontScale() {
            document.documentElement.style.setProperty('--font-scale', currentFontScale);
            const visual = document.getElementById('prescriptionVisual');
            if (visual) {
                visual.style.zoom = currentFontScale;
            }
            document.getElementById('fontSizeDisplay').textContent = Math.round(currentFontScale * 100) + '%';
            document.getElementById('fontScaleInput').value = currentFontScale.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', loadSettings);
    </script>
</body>
</html>
