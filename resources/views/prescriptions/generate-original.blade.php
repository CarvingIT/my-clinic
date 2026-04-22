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
        :root {
            --font-scale: 1;
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
            height: 80mm;
            margin-bottom: 10mm;
            border-bottom: 2px solid #000;
            page-break-inside: avoid;
        }

        .prescription-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .section-header {
            font-weight: bold;
            font-size: calc(13px * var(--font-scale));
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 6px;
            color: #000;
            letter-spacing: 0.5px;
        }

        .section-body {
            font-size: calc(13px * var(--font-scale));
            line-height: 1.6;
            color: #000;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            font-size: calc(12px * var(--font-scale));
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
        }

        .patient-field {
            page-break-inside: avoid;
        }

        .patient-label {
            font-weight: bold;
            font-size: calc(11px * var(--font-scale));
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .treatment-line {
            display: flex;
            gap: 40px;
            margin-bottom: 10px;
            font-size: calc(12px * var(--font-scale));
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            font-size: calc(12px * var(--font-scale));
        }

        .payment-item {
            text-align: left;
        }

        .payment-label {
            font-weight: bold;
            font-size: calc(11px * var(--font-scale));
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .payment-amount {
            font-size: calc(14px * var(--font-scale));
            font-weight: bold;
        }

        .footer-section {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .signature-line {
            width: 100px;
            border-bottom: 1px solid #000;
            margin-bottom: 2px;
            height: 18px;
            display: block;
        }

        .doctor-name {
            font-size: calc(12px * var(--font-scale));
            font-weight: bold;
        }

        .doctor-title {
            font-size: calc(10px * var(--font-scale));
            margin-top: 2px;
        }

        .rx-symbol {
            text-align: left;
            font-size: calc(28px * var(--font-scale));
            font-weight: bold;
            font-style: italic;
            margin-bottom: 8px;
            color: #000;
        }
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
        <div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left);" class="prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300">
            <!-- Letterhead Space -->
            <div class="letterhead-space"></div>

            <!-- Rx Symbol -->
            <div class="rx-symbol">℞</div>

            <!-- Patient Information -->
            @if (count(array_filter(['patient_name', 'patient_id', 'patient_age', 'patient_gender', 'patient_mobile', 'patient_address'], fn($f) => in_array($f, $selectedFields))) > 0)
                <div class="patient-grid">
                    @if (in_array('patient_name', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Name</div>
                            <div>{{ strip_tags($data['patient_name'] ?? '') }}</div>
                        </div>
                    @endif
                    @if (in_array('patient_id', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Patient ID</div>
                            <div>{{ strip_tags($data['patient_id'] ?? '') }}</div>
                        </div>
                    @endif
                    @if (in_array('patient_age', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Age / Gender</div>
                            <div>
                                {{ strip_tags($data['patient_age'] ?? '') }}
                                @if (in_array('patient_gender', $selectedFields))
                                    / {{ strip_tags($data['patient_gender'] ?? '') }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                @if (in_array('patient_mobile', $selectedFields) || in_array('patient_address', $selectedFields))
                    <div class="patient-grid">
                        @if (in_array('patient_mobile', $selectedFields))
                            <div class="patient-field">
                                <div class="patient-label">Mobile</div>
                                <div>{{ strip_tags($data['patient_mobile'] ?? '') }}</div>
                            </div>
                        @endif
                        @if (in_array('patient_address', $selectedFields))
                            <div class="patient-field" style="grid-column: 1 / -1;">
                                <div class="patient-label">Address</div>
                                <div>{{ strip_tags($data['patient_address'] ?? '') }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Medical Sections -->
            @if (in_array('nadi', $selectedFields) && $data['nadi'])
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.nadi') }}</div>
                    <div class="section-body">{{ trim(strip_tags($data['nadi'])) }}</div>
                </div>
            @endif

            @if (in_array('lakshane', $selectedFields) && $data['lakshane'])
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.lakshane') }}</div>
                    <div class="section-body">{{ trim(strip_tags($data['lakshane'])) }}</div>
                </div>
            @endif

            @if (in_array('nidan', $selectedFields) && $data['nidan'])
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.nidan') }}</div>
                    <div class="section-body">{{ trim(strip_tags($data['nidan'])) }}</div>
                </div>
            @endif

            @if (in_array('chikitsa', $selectedFields) && $data['chikitsa'])
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.chikitsa') }}</div>
                    <div class="section-body">{{ trim(strip_tags($data['chikitsa'])) }}</div>
                </div>
            @endif

            <!-- Treatment Details -->
            @if (in_array('days', $selectedFields) || in_array('packets', $selectedFields))
                <div class="treatment-line">
                    @if (in_array('days', $selectedFields) && $data['days'])
                        <div>
                            <span style="font-weight: bold;">Duration (दिवस):</span>
                            <span style="margin-left: 5px;">{{ strip_tags($data['days']) }}</span>
                        </div>
                    @endif
                    @if (in_array('packets', $selectedFields) && $data['packets'])
                        <div>
                            <span style="font-weight: bold;">Packets (पुडे):</span>
                            <span style="margin-left: 5px;">{{ strip_tags($data['packets']) }}</span>
                        </div>
                    @endif
                </div>
            @endif

            @if (in_array('vishesh', $selectedFields) && $data['vishesh'])
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.vishesh') }}</div>
                    <div class="section-body">{{ trim(strip_tags($data['vishesh'])) }}</div>
                </div>
            @endif

            <!-- Payment Information -->
            @if (in_array('amount_billed', $selectedFields) || in_array('amount_paid', $selectedFields) || in_array('amount_due', $selectedFields))
                <div class="prescription-section">
                    <div class="section-header">{{ __('messages.payment_details') }}</div>
                    <div class="payment-grid">
                        @if (in_array('amount_billed', $selectedFields))
                            <div class="payment-item">
                                <div class="payment-label">Amount Billed</div>
                                <div class="payment-amount">₹ {{ strip_tags($data['amount_billed'] ?? '0.00') }}</div>
                            </div>
                        @endif
                        @if (in_array('amount_paid', $selectedFields))
                            <div class="payment-item">
                                <div class="payment-label">Amount Paid</div>
                                <div class="payment-amount">₹ {{ strip_tags($data['amount_paid'] ?? '0.00') }}</div>
                            </div>
                        @endif
                        @if (in_array('amount_due', $selectedFields))
                            <div class="payment-item">
                                <div class="payment-label">Amount Due</div>
                                <div class="payment-amount">₹ {{ strip_tags($data['amount_due'] ?? '0.00') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Footer with Signature -->
            <div class="footer-section">
                @if (in_array('branch_name', $selectedFields))
                    <div>
                        <div style="font-weight: bold; font-size: calc(11px * var(--font-scale)); text-transform: uppercase; margin-bottom: 2px;">Clinic</div>
                        <div style="font-size: calc(12px * var(--font-scale));">{{ strip_tags($data['branch_name'] ?? '') }}</div>
                    </div>
                @endif
                @if (in_array('doctor_name', $selectedFields))
                    <div style="text-align: center;">
                        <span class="signature-line"></span>
                        <div class="doctor-name">{{ strip_tags($data['doctor_name'] ?? '') }}</div>
                        <div class="doctor-title">Doctor</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

        <script>
        function goBack() {
            window.history.back();
        }
        
        let currentFontScale = 1;
        
        function changeFontSize(delta) {
            currentFontScale += delta;
            if (currentFontScale < 0.5) currentFontScale = 0.5;
            if (currentFontScale > 2.0) currentFontScale = 2.0;
            updateFontScale();
        }

        function resetFontSize() {
            currentFontScale = 1;
            updateFontScale();
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
        }

        function updateFontScale() {
            document.documentElement.style.setProperty('--font-scale', currentFontScale);
            document.getElementById('fontSizeDisplay').textContent = Math.round(currentFontScale * 100) + '%';
            document.getElementById('fontScaleInput').value = currentFontScale.toFixed(2);
        }
    </script>
</body>
</html>