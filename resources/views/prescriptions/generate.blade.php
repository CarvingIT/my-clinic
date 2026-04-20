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
            font-size: calc(12px * var(--font-scale));
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
            font-size: calc(11px * var(--font-scale));
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
            font-size: calc(10px * var(--font-scale));
        }
        .patient-table td { color: #2c221e; width: 35%; font-weight: 500; }

        /* Split Layout */
        .main-body { width: 100%; display: table; table-layout: fixed; }
        .left-col { display: table-cell; width: 35%; vertical-align: top; padding-right: 25px; border-right: 1px solid #e6d8c3; }
        .right-col { display: table-cell; width: 65%; vertical-align: top; padding-left: 25px; }

        /* Section Titles - Classical Serif & Ayurvedic Green */
        .section-title {
            font-family: 'Georgia', serif;
            font-size: calc(12px * var(--font-scale));
            font-weight: 700;
            color: #276749; /* Leafy Ayurvedic Green */
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px; margin-top: 22px;
            border-bottom: 1px solid #e6d8c3;
            padding-bottom: 4px;
            position: relative;
        }
        .section-title::after {
            content: ''; position: absolute; bottom: -1px; left: 0;
            width: 35px; height: 2px;
            background-color: #c05621; /* Saffron underline */
        }
        .section-title:first-child { margin-top: 0; }

        .content-text { font-size: calc(13px * var(--font-scale)); color: #2c221e; margin-bottom: 6px; white-space: pre-wrap; word-break: break-word; }

        /* Rx Styling */
        .rx-wrapper { margin-bottom: 15px; }
        .rx-icon {
            font-family: 'Georgia', serif;
            font-size: calc(36px * var(--font-scale));
            font-weight: bold; font-style: italic;
            color: #276749;
            line-height: 1;
        }

        .treatment-box { margin-top: 10px; }

        /* Duration box matching traditional feel */
        .duration-box {
            margin-top: 30px; background: #fffaf0;
            border: 1px dashed #d4b895; padding: 12px 15px;
            border-radius: 5px; font-size: calc(12px * var(--font-scale));
        }
        .duration-box strong { color: #744210; }
        .duration-item { display: inline-block; margin-right: 20px; }

        .bottom-details { width: 100%; margin-top: 50px; display: table; table-layout: fixed; }
        .bottom-cell-left { display: table-cell; width: 50%; vertical-align: bottom; }
        .bottom-cell-right { display: table-cell; width: 50%; vertical-align: bottom; text-align: right; }

        /* Payment Area */
        .payment-box {
            border: 1px solid #e6d8c3; padding: 10px;
            background: #fffaf0; border-radius: 4px;
            display: inline-block; font-size: calc(11px * var(--font-scale));
            box-shadow: inset 0 0 5px rgba(0,0,0,0.02);
        }
        .payment-line { margin-bottom: 3px; }
        .payment-line strong { display: inline-block; width: 100px; color: #744210; }

        .signature-area { margin-top: 20px; }
        .signature-line { display: inline-block; border-bottom: 1px solid #744210; width: 220px; margin-bottom: 8px; }
        .doctor-name { font-family: 'Georgia', serif; font-weight: bold; color: #276749; font-size: calc(15px * var(--font-scale)); margin-top: 5px; }
        .clinic-name { color: #c05621; font-weight: 500; font-size: calc(11px * var(--font-scale)); margin-top: 2px; }

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
                        <div id="prescriptionVisual" style="padding: var(--margin-top) var(--margin-right) var(--margin-bottom) var(--margin-left); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;" class="prescription-wrapper bg-white rounded-lg shadow-lg overflow-hidden border border-gray-300">
            <!-- Letterhead Space -->
            <div class="letterhead-space"></div>

            <div class="prescription-container" style="width: 100%; margin: 0 auto;">


        <div class="invocation">|| श्री ||</div>

        <!-- Patient Banner Table -->
        <table class="patient-table">
            <tr>
                @if (in_array('patient_name', $selectedFields))
                    <th>Patient Name</th>
                    <td><strong>{{ strip_tags($data['patient_name'] ?? '') }}</strong></td>
                @endif
                @if (in_array('patient_id', $selectedFields))
                    <th>Patient ID</th>
                    <td>{{ strip_tags($data['patient_id'] ?? '') }}</td>
                @else
                    <th>Date</th>
                    <td>{{ \Carbon\Carbon::parse($followup->created_at)->format('d M Y, h:i A') }}</td>
                @endif
            </tr>
            <tr>
                @if (in_array('patient_age', $selectedFields) || in_array('patient_gender', $selectedFields))
                    <th>Age / Gender</th>
                    <td>
                        {{ strip_tags($data['patient_age'] ?? '') }}
                        @if(in_array('patient_gender', $selectedFields) && !empty($data['patient_gender']))
                            / {{ strip_tags($data['patient_gender']) }}
                        @endif
                    </td>
                @endif
                @if (in_array('patient_mobile', $selectedFields))
                    <th>Contact</th>
                    <td>{{ strip_tags($data['patient_mobile'] ?? '') }}</td>
                @endif
            </tr>
            @if (in_array('patient_address', $selectedFields) && !empty($data['patient_address']))
            <tr>
                <th>Address</th>
                <td colspan="3">{{ strip_tags($data['patient_address'] ?? '') }}</td>
            </tr>
            @endif
        </table>

        <!-- Layout Matrix -->
        <div class="main-body">
            <!-- LEFT COLUMN: Patient Info & Vitals -->
            <div class="left-col">
                @if (in_array('lakshane', $selectedFields) && !empty($data['lakshane']))
                    <div class="section-title">Symptoms & Complaints</div>
                    <div class="content-text">{{ trim(strip_tags($data['lakshane'])) }}</div>
                @endif
                @if (in_array('nadi', $selectedFields) && !empty($data['nadi']))
                    <div class="section-title">Vitals & Examination</div>
                    <div class="content-text">{{ trim(strip_tags($data['nadi'])) }}</div>
                @endif
                @if (in_array('nidan', $selectedFields) && !empty($data['nidan']))
                    <div class="section-title">Diagnosis</div>
                    <div class="content-text"><strong>{{ trim(strip_tags($data['nidan'])) }}</strong></div>
                @endif
                @if (in_array('vishesh', $selectedFields) && !empty($data['vishesh']))
                    <div class="section-title">Special Advice</div>
                    <div class="content-text" style="color: #c05621; font-weight: 500;">{{ trim(strip_tags($data['vishesh'])) }}</div>
                @endif
            </div>

            <!-- RIGHT COLUMN: Rx Medicine -->
            <div class="right-col">
                <div class="rx-wrapper">
                    <span class="rx-icon">℞</span>
                </div>

                @if (in_array('chikitsa', $selectedFields) && !empty($data['chikitsa']))
                    <div class="treatment-box">
                        <div class="content-text" style="font-size: calc(14px * var(--font-scale)); line-height: 1.8;">
                            {!! $data['chikitsa'] !!}
                        </div>
                    </div>
                @else
                    <div class="content-text" style="color: #a0aec0; font-style: italic; margin-top: 10px;">
                        No medication prescribed for this visit.
                    </div>
                @endif

                @if (in_array('days', $selectedFields) || in_array('packets', $selectedFields))
                    <div class="duration-box">
                        @if (in_array('days', $selectedFields) && !empty($data['days']))
                            <div class="duration-item">
                                <strong>Duration:</strong> {{ strip_tags($data['days']) }} Days
                            </div>
                        @endif
                        @if (in_array('packets', $selectedFields) && !empty($data['packets']))
                            <div class="duration-item" style="border-left: 1px dashed #d4b895; padding-left: 20px;">
                                <strong>Packets:</strong> {{ strip_tags($data['packets']) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer block -->
        <div class="bottom-details">
            <div class="bottom-cell-left">
                @if (in_array('amount_billed', $selectedFields) || in_array('amount_paid', $selectedFields))
                    <div class="payment-box">
                        @if (in_array('amount_billed', $selectedFields) && !empty($data['amount_billed']))
                            <div class="payment-line">
                                <strong>Amount Billed:</strong> ₹{{ strip_tags($data['amount_billed']) }}
                            </div>
                        @endif
                        @if (in_array('amount_paid', $selectedFields) && !empty($data['amount_paid']))
                            <div class="payment-line">
                                <strong>Amount Paid:</strong> ₹{{ strip_tags($data['amount_paid']) }}
                            </div>
                        @endif
                        @if (in_array('amount_due', $selectedFields) && !empty($data['amount_due']))
                            <div class="payment-line" style="color: #c05621; font-weight: bold; margin-top: 5px;">
                                <strong>Balance Due:</strong> ₹{{ strip_tags($data['amount_due']) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="bottom-cell-right signature-area">
                <div style="margin-bottom: 40px;"></div>
                <div class="signature-line"></div><br>
                <div class="doctor-name">
                    Dr. {{ !empty($data['doctor_name']) ? strip_tags($data['doctor_name']) : 'Authorized Signatory' }}
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
