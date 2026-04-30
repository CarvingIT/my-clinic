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
            --margin-top: 10mm;
            --margin-right: 10mm;
            --margin-bottom: 10mm;
            --margin-left: 10mm;
        }
        @page {
            size: A4;
            margin: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);
        }
        @media print {
            body {
                background: white;
            }
            .print-toolbar {
                display: none !important;
            }
            .prescription-wrapper {
                box-shadow: none !important;
                border: none !important;
            }
        }
        .letterhead-space {
            height: 50mm; /* Add space reserved for letterheads */
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 15mm 15mm 15mm 15mm; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: calc(16px * var(--font-scale));
            color: #2c221e; /* Deep Earthy Grey/Brown */
            line-height: 1.6;
            background: #ffffff;
        }
        .prescription-container { width: 100%; margin: 0 auto; padding-top: 5px; }

        /* Ayurvedic Motif / Invocation */
        .invocation {
            text-align: center;
            font-family: 'Noto Sans Devanagari', 'Georgia', serif;
            font-size: calc(16px * var(--font-scale));
            font-weight: 600;
            color: #c05621; /* Saffron Accent */
            margin-bottom: 12px;
            letter-spacing: 2px;
        }

        /* Patient Banner - Warm Earthy Tones */
        .patient-table {
            width: 100%; border-collapse: collapse; margin-bottom: 25px;
            border-radius: 6px; overflow: hidden;
            font-size: calc(14px * var(--font-scale));
            border: 1px solid #e6d8c3; /* Sandalwood Border */
        }
        .patient-table th, .patient-table td {
            padding: 7px 12px; border: 1px solid #e6d8c3;
            text-align: left; vertical-align: top;
        }
        .patient-table th {
            background-color: #fffaf0; /* Cream Background */
            color: #744210; /* Deep Warm Brown */
            font-weight: 700; width: 15%;
            text-transform: uppercase; letter-spacing: 0.5px;
            font-size: calc(15px * var(--font-scale));
        }
        .patient-table td { color: #2c221e; width: 35%; font-weight: 500; }

        /* Single Column Professional Layout */
        .main-body { width: 100%; display: block; margin: 30px 0; }
        .left-col { width: 100%; margin-bottom: 20px; padding: 20px; background: #fffaf0; border: 1px solid #e6d8c3; border-radius: 6px; }
        .left-col:empty { display: none; margin-bottom: 0; padding: 0; background: none; border: none; }
        .right-col { width: 100%; margin-bottom: 20px; }

        /* Section Titles - Classical Serif & Ayurvedic Green */
        .section-title {
            font-family: 'Georgia', serif;
            font-size: calc(13px * var(--font-scale));
            font-weight: 700;
            color: #744210;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 0;
            margin-top: 0;
            padding-bottom: 0;
            border-bottom: none;
            position: relative;
            display: inline;
            padding-bottom: 0;
            border-bottom: none;
            white-space: nowrap;
            margin-right: 12px;
        }
        .section-title:first-child { margin-top: 0; }

        /* Inline Item Container */
        .field-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
        }

        /* Hide section titles when labels disabled */
        .prescription-wrapper.hide-labels .section-title {
            display: none;
        }

        .content-text {
            font-size: calc(13px * var(--font-scale));
            color: #2c221e;
            margin-bottom: 0;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.6;
            flex: 1;
        }

        .field-item .content-text {
            margin-bottom: 0;
        }

        /* Rx Styling */
        .rx-wrapper {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-top: 2px solid #e6d8c3;
            border-bottom: 2px solid #e6d8c3;
        }
        .rx-icon {
            font-family: 'Georgia', serif;
            font-size: calc(48px * var(--font-scale));
            font-weight: bold;
            font-style: italic;
            color: #276749;
            line-height: 1;
        }

        .treatment-box {
            margin-top: 8px;
            padding: 2px 5px;
            background: #ffffff;
            border: 1px solid #e6d8c3;
            border-radius: 4px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: flex-start;
        }

        .treatment-content {
            padding-right: 8px;
            border-right: 1px solid #e6d8c3;
            min-width: 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Duration box with partition */
        .duration-box {
            margin: 0;
            padding: 32px 15px 0 12px;
            background: transparent;
            border: none;
            font-size: calc(12px * var(--font-scale));
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .duration-box strong { color: #744210; font-weight: 700; }
        .duration-item { display: block; margin: 2px 0; font-weight: 600; }

        .bottom-details { width: 100%; margin-top: 50px; display: flex; gap: 40px; justify-content: space-between; align-items: flex-end; }
        .bottom-cell-left { flex: 1; text-align: left; }
        .bottom-cell-right { flex: 1; text-align: right; }

        /* Payment Area */
        .payment-box {
            border: 2px solid #e6d8c3;
            padding: 12px 15px;
            background: linear-gradient(135deg, #fffaf0 0%, #fdf6ed 100%);
            border-radius: 5px;
            display: inline-block;
            font-size: calc(12px * var(--font-scale));
            box-shadow: inset 0 0 5px rgba(0,0,0,0.03);
        }
        .payment-line {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .payment-line:last-child { margin-bottom: 0; }
        .payment-line strong {
            color: #744210;
            font-weight: 700;
            min-width: 110px;
        }

        .signature-area { margin-top: 20px; text-align: center; }
        .signature-line { display: inline-block; border-bottom: 2px solid #744210; width: 200px; margin-bottom: 8px; }
        .doctor-name { font-family: 'Georgia', serif; font-weight: bold; color: #276749; font-size: calc(14px * var(--font-scale)); margin-top: 8px; }
        .clinic-name { color: #c05621; font-weight: 600; font-size: calc(11px * var(--font-scale)); margin-top: 3px; }

    </style>
</head>
<body class="bg-gray-50">
    <!-- Toolbar -->
    <div class="print-toolbar sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full px-4 py-3 flex items-center justify-between gap-4">
            <!-- Left side Title -->
            <div class="shrink-0">
                <h1 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-prescription text-blue-600 mr-2"></i> Prescription Preview
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $patient->name }} &bull; {{ now()->format('d M Y') }}</p>
            </div>

            <!-- Right side Controls -->
            <div class="flex gap-2 items-center flex-nowrap shrink-0 overflow-x-auto pb-1 md:pb-0">

                <!-- Margins Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Margin</span>
                    </div>
                    <div class="flex items-center px-1" title="Top Margin">
                        <i class="fas fa-arrow-up text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginTop" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Right Margin">
                        <i class="fas fa-arrow-right text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginRight" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Bottom Margin">
                        <i class="fas fa-arrow-down text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginBottom" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                    <div class="flex items-center px-1 border-l border-gray-200" title="Left Margin">
                        <i class="fas fa-arrow-left text-gray-400 text-[10px] ml-1"></i>
                        <input type="number" id="marginLeft" value="10" min="0" max="100" class="w-10 h-7 bg-transparent border-none text-center text-sm font-semibold text-gray-700 focus:ring-0 p-0" onchange="updateMargins()">
                    </div>
                </div>

                <!-- Font Control Group -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner">
                    <div class="px-2 border-r border-gray-200">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Font</span>
                    </div>
                    <button onclick="changeFontSize(-0.1)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition" title="Decrease Font">
                        <i class="fas fa-minus text-[10px]"></i>
                    </button>
                    <span class="w-12 text-center text-sm font-bold text-gray-700" id="fontSizeDisplay">100%</span>
                    <button onclick="changeFontSize(0.1)" type="button" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded transition mr-1" title="Increase Font">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                    <div class="border-l border-gray-200 pl-1">
                        <button onclick="resetFontSize()" type="button" class="px-2 py-1 text-[10px] font-bold text-blue-600 hover:bg-blue-100 rounded transition">RESET</button>
                    </div>
                </div>

                <!-- Labels Toggle -->
                <div class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200 shadow-inner">
                    <label class="flex items-center cursor-pointer px-2 py-1 gap-2">
                        <input type="checkbox" id="showLabelsToggle" checked onchange="toggleLabels()" class="w-4 h-4 rounded">
                        <span class="text-[10px] font-bold text-gray-700">Section Labels</span>
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 pl-2 border-l border-gray-300">
                    <button onclick="goBack()" class="h-9 px-4 bg-white border border-gray-300 hover:bg-gray-50 hover:text-blue-600 text-gray-700 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-edit"></i> Edit
                    </button>

                    <button onclick="window.print()" class="h-9 px-4 bg-gray-800 hover:bg-black text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <button type="button" onclick="document.getElementById('downloadPdfForm').submit()" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-download"></i> PDF
                    </button>
                </div>

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
                                        <input type="hidden" name="margin_top" id="input_margin_top" value="10">
                    <input type="hidden" name="margin_right" id="input_margin_right" value="10">
                    <input type="hidden" name="margin_bottom" id="input_margin_bottom" value="10">
                    <input type="hidden" name="margin_left" id="input_margin_left" value="10">
                    <input type="hidden" name="font_scale" id="fontScaleInput" value="1">

                </form>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>

    <!-- Prescription Document -->
    <div class="max-w-4xl mx-auto my-6 px-4">
                        <div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;" class="prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300">
            <!-- Letterhead Space -->
            <div class="letterhead-space"></div>

            <div class="prescription-container" style="width: 100%; margin: 0 auto;">


        <div class="invocation">|| श्री ||</div>

        <!-- Patient Banner Table - Dynamic Layout -->
        <table class="patient-table">
            <!-- Row 1: Name and ID/Date -->
            <tr>
                @if (in_array('patient_name', $selectedFields))
                    <th>{{ __('messages.Name') }}</th>
                    <td><strong>{{ strip_tags($data['patient_name'] ?? '') }}</strong></td>
                @endif
                @if (in_array('patient_id', $selectedFields))
                    <th>{{ __('ID') }}</th>
                    <td>{{ strip_tags($data['patient_id'] ?? '') }}</td>
                @elseif (in_array('patient_name', $selectedFields))
                    <th>{{ __('messages.Date') }}</th>
                    <td>{{ \Carbon\Carbon::parse($followup->created_at)->format('d M Y, h:i A') }}</td>
                @endif
            </tr>

            <!-- Row 2: Age/Gender and Mobile -->
            <tr>
                @if (in_array('patient_age', $selectedFields) || in_array('patient_gender', $selectedFields))
                    <th>{{ __('messages.Age/Gender') }}</th>
                    <td>
                        {{ strip_tags($data['patient_age'] ?? '') }}
                        @if(in_array('patient_gender', $selectedFields) && !empty($data['patient_gender']))
                            / {{ strip_tags($data['patient_gender']) }}
                        @endif
                    </td>
                @endif
                @if (in_array('patient_mobile', $selectedFields))
                    <th>{{ __('messages.mobile_phone') }}</th>
                    <td>{{ strip_tags($data['patient_mobile'] ?? '') }}</td>
                @endif
            </tr>

            <!-- Row 3: Weight/Height -->
            @if (in_array('patient_weight', $selectedFields) || in_array('patient_height', $selectedFields))
            <tr>
                @if (in_array('patient_weight', $selectedFields) && !empty($data['patient_weight']))
                    <th>{{ __('messages.patient_weight') }}</th>
                    <td>{{ strip_tags($data['patient_weight']) }}</td>
                @endif
                @if (in_array('patient_height', $selectedFields) && !empty($data['patient_height']))
                    <th>{{ __('messages.patient_height') }}</th>
                    <td>{{ strip_tags($data['patient_height']) }}</td>
                @endif
            </tr>
            @endif

            <!-- Row 4: Address -->
            @if (in_array('patient_address', $selectedFields) && !empty($data['patient_address']))
            <tr>
                <th>{{ __('messages.address') }}</th>
                <td colspan="3">{{ strip_tags($data['patient_address'] ?? '') }}</td>
            </tr>
            @endif
        </table>

        <!-- Medical Information Section -->
        @if (in_array('lakshane', $selectedFields) || in_array('nadi', $selectedFields) || in_array('nidan', $selectedFields) || in_array('vishesh', $selectedFields) || in_array('chikitsa', $selectedFields))
        <div class="left-col" style="margin-top: 20px;">
            <!-- Clinical Findings -->
            @if (in_array('lakshane', $selectedFields) && !empty($data['lakshane']))
                <div class="field-item">
                    <span class="section-title">{{ __('messages.lakshane') }}:</span>
                    <div class="content-text">{{ trim(strip_tags($data['lakshane'])) }}</div>
                </div>
            @endif

            @if (in_array('nadi', $selectedFields) && !empty($data['nadi']))
                <div class="field-item">
                    <span class="section-title">{{ __('messages.nadi') }}:</span>
                    <div class="content-text">{{ trim(strip_tags($data['nadi'])) }}</div>
                </div>
            @endif

            @if (in_array('nidan', $selectedFields) && !empty($data['nidan']))
                <div class="field-item">
                    <span class="section-title">{{ __('messages.diagnosis') }}:</span>
                    <div class="content-text"><strong>{{ trim(strip_tags($data['nidan'])) }}</strong></div>
                </div>
            @endif

            @if (in_array('vishesh', $selectedFields) && !empty($data['vishesh']))
                <div class="field-item">
                    <span class="section-title">{{ __('messages.Vishesh') }}:</span>
                    <div class="content-text" style="color: #c05621; font-weight: 500;">{{ trim(strip_tags($data['vishesh'])) }}</div>
                </div>
            @endif
        </div>
        @endif

        <!-- Treatment Section -->
        @if (in_array('chikitsa', $selectedFields) || in_array('days', $selectedFields) || in_array('packets', $selectedFields))
        <div class="right-col" style="margin-top: 20px;">
            <div class="section-title" style="display: block; width: 100%; text-align: center; border: none; padding-bottom: 10px; margin-bottom: 15px;">{{ __('messages.Treatment') }}</div>

            <div class="treatment-box">
                <div class="treatment-content">
                    @if (in_array('chikitsa', $selectedFields) && !empty($data['chikitsa']))
                        <div class="content-text" style="font-size: calc(14px * var(--font-scale)); line-height: 1.8;">
                            {!! trim(preg_replace('/\s+/', ' ', strip_tags($data['chikitsa']))) !!}
                        </div>
                    @elseif(in_array('chikitsa', $selectedFields))
                        <div class="content-text" style="color: #a0aec0; font-style: italic; text-align: center;">
                            <em>No medication prescribed for this visit.</em>
                        </div>
                    @else
                        <div style="min-height: 40px;"></div>
                    @endif
                </div>

                @if (in_array('days', $selectedFields) || in_array('packets', $selectedFields))
                    <div class="duration-box">
                        @if (in_array('days', $selectedFields) && !empty($data['days']))
                            <div class="duration-item" style="font-size: calc(12px * var(--font-scale)); font-weight: 700; color: #744210;">
                                <strong>दिवस:</strong> {{ strip_tags($data['days']) }}
                            </div>
                        @endif
                        @if (in_array('packets', $selectedFields) && !empty($data['packets']))
                            <div class="duration-item" style="font-size: calc(12px * var(--font-scale)); font-weight: 700; color: #744210;">
                                <strong>पुड्या:</strong> {{ strip_tags($data['packets']) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Footer block -->
        <div class="bottom-details" style="margin-top: 40px;">
            <div class="bottom-cell-left">
                @if ((in_array('amount_billed', $selectedFields) && !empty($data['amount_billed'])) ||
                     (in_array('amount_paid', $selectedFields) && !empty($data['amount_paid'])) ||
                     (in_array('amount_due', $selectedFields) && !empty($data['amount_due'])))
                    <div class="payment-box">
                        @if (in_array('amount_billed', $selectedFields) && !empty($data['amount_billed']))
                            <div class="payment-line">
                                <strong>{{ __('messages.Amount Billed') }}:</strong> ₹{{ strip_tags($data['amount_billed']) }}
                            </div>
                        @endif
                        @if (in_array('amount_paid', $selectedFields) && !empty($data['amount_paid']))
                            <div class="payment-line">
                                <strong>{{ __('messages.Amount Paid') }}:</strong> ₹{{ strip_tags($data['amount_paid']) }}
                            </div>
                        @endif
                        @if (in_array('amount_due', $selectedFields) && !empty($data['amount_due']))
                            <div class="payment-line" style="color: #c05621; font-weight: bold; margin-top: 5px;">
                                <strong>{{ __('messages.Balance Due') }}:</strong> ₹{{ strip_tags($data['amount_due']) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="bottom-cell-right signature-area">
                <div style="margin-bottom: 40px;"></div>
                <div class="signature-line"></div><br>
                <div class="doctor-name">
                    @if (in_array('doctor_name', $selectedFields) && !empty($data['doctor_name']))
                        Dr. {{ strip_tags($data['doctor_name']) }}
                    @else
                        Dr. Authorized Signatory
                    @endif
                </div>
                @if (in_array('branch_name', $selectedFields) && !empty($data['branch_name']))
                    <div class="clinic-name">{{ strip_tags($data['branch_name']) }}</div>
                @endif
            </div>
        </div>

            </div>
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'prescription-settings';

        // Load settings from localStorage
        function loadSettings() {
            const settings = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};

            // Restore font scale
            if (settings.fontScale) {
                currentFontScale = settings.fontScale;
                updateFontScale();
            }

            // Restore margins
            if (settings.margins) {
                document.getElementById('marginTop').value = settings.margins.top || 10;
                document.getElementById('marginRight').value = settings.margins.right || 10;
                document.getElementById('marginBottom').value = settings.margins.bottom || 10;
                document.getElementById('marginLeft').value = settings.margins.left || 10;
                updateMargins();
            }

            // Restore label visibility
            if (settings.showLabels !== undefined) {
                document.getElementById('showLabelsToggle').checked = settings.showLabels;
                toggleLabels();
            }
        }

        // Save settings to localStorage
        function saveSettings() {
            const settings = {
                fontScale: currentFontScale,
                margins: {
                    top: document.getElementById('marginTop').value,
                    right: document.getElementById('marginRight').value,
                    bottom: document.getElementById('marginBottom').value,
                    left: document.getElementById('marginLeft').value
                },
                showLabels: document.getElementById('showLabelsToggle').checked
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
        }

        function goBack() {
            window.history.back();
        }

        let currentFontScale = 1;

        function changeFontSize(delta) {
            currentFontScale += delta;
            if (currentFontScale < 0.5) currentFontScale = 0.5;
            if (currentFontScale > 2.0) currentFontScale = 2.0;
            updateFontScale();
            saveSettings();
        }

        function resetFontSize() {
            currentFontScale = 1;
            updateFontScale();
            saveSettings();
        }

        function toggleLabels() {
            const isVisible = document.getElementById('showLabelsToggle').checked;
            const wrapper = document.getElementById('prescriptionVisual');
            if (isVisible) {
                wrapper.classList.remove('hide-labels');
            } else {
                wrapper.classList.add('hide-labels');
            }
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
            document.getElementById('fontSizeDisplay').textContent = Math.round(currentFontScale * 100) + '%';
            document.getElementById('fontScaleInput').value = currentFontScale.toFixed(2);
        }

        // Load settings when page loads
        document.addEventListener('DOMContentLoaded', loadSettings);
    </script>
</body>
</html>
