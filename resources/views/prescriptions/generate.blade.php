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
        }
        @page {
            size: A4;
            margin: 10mm;
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
    <div class="print-toolbar sticky top-0 z-50 bg-white border-b-2 border-gray-300 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-prescription text-gray-800 mr-2"></i>Prescription Preview
                </h1>
                <p class="text-sm text-gray-600 mt-1">{{ $patient->name }} • {{ now()->format('d M Y') }}</p>
            </div>
                        <div class="flex gap-3 items-center">
                <div class="flex items-center bg-gray-200 rounded-lg px-2 shadow-sm border border-gray-300">
                    <span class="text-xs font-semibold uppercase text-gray-500 mr-2 ml-1">Font Size</span>
                    <button onclick="changeFontSize(-0.1)" type="button" class="p-2 text-gray-700 hover:text-black focus:outline-none" title="Decrease Font Size">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="mx-1 font-bold w-12 text-center" id="fontSizeDisplay">100%</span>
                    <button onclick="changeFontSize(0.1)" type="button" class="p-2 text-gray-700 hover:text-black focus:outline-none" title="Increase Font Size">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button onclick="resetFontSize()" type="button" class="ml-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-100 rounded transition" title="Reset Font Size">Reset</button>
                </div>
                <button onclick="goBack()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Edit
                </button>
                <button onclick="window.print()" class="px-6 py-3 bg-gray-800 hover:bg-black text-white rounded-lg font-semibold transition flex items-center gap-2">
                    <i class="fas fa-print"></i> Print / Save as PDF
                </button>
                <form action="{{ route('followups.prescription.download', ['followup' => $followup->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    @foreach ($selectedFields as $field)
                        <input type="hidden" name="selected_fields[]" value="{{ $field }}">
                    @endforeach
                    @foreach ($data as $key => $value)
                        @if (!str_ends_with($key, '_label'))
                            <input type="hidden" name="field_values[{{ str_replace('_label', '', $key) }}]" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="font_scale" id="fontScaleInput" value="1">
                    <button type="submit" class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-semibold transition flex items-center gap-2">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Prescription Document -->
    <div class="max-w-4xl mx-auto my-6 px-4">
        <div class="prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300 p-6">
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

        function updateFontScale() {
            document.documentElement.style.setProperty('--font-scale', currentFontScale);
            document.getElementById('fontSizeDisplay').textContent = Math.round(currentFontScale * 100) + '%';
            document.getElementById('fontScaleInput').value = currentFontScale.toFixed(2);
        }
    </script>
</body>
</html>
