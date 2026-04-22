<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @php
        $fontScale = $font_scale ?? 1;
    @endphp
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $patient->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'Noto Sans Devanagari', 'Arial', 'Times New Roman', serif;
            font-size: {{ 13 * $fontScale }}px;
            color: #000000;
            line-height: 1.5;
            background: white;
        }

        .letterhead-space {
            height: 80mm;
            margin-bottom: 10mm;
            border-bottom: 2px solid #000;
            page-break-inside: avoid;
        }

        .prescription-wrapper {
            width: 100%;
            background: white;
        }

        /* Rx Symbol */
        .rx-symbol {
            text-align: left;
            font-size: {{ 28 * $fontScale }}px;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 8px;
            color: #000;
        }

        /* Patient Information */
        .patient-section {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #000;
            page-break-inside: avoid;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            font-size: {{ 12 * $fontScale }}px;
        }

        .patient-field {
            page-break-inside: avoid;
        }

        .patient-label {
            font-weight: bold;
            font-size: {{ 11 * $fontScale }}px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000;
        }

        .patient-value {
            color: #000;
            font-size: {{ 12 * $fontScale }}px;
        }

        /* Section Headers */
        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .section-header {
            font-weight: bold;
            font-size: {{ 12 * $fontScale }}px;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 6px;
            color: #000;
            letter-spacing: 0.5px;
        }

        .section-body {
            padding-left: 0;
            font-size: {{ 13 * $fontScale }}px;
            line-height: 1.6;
            color: #000;
            margin-left: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
        }

        .section-body p {
            margin: 0;
            display: inline;
        }

        /* Treatment Details Line */
        .treatment-line {
            display: flex;
            gap: 40px;
            margin-bottom: 10px;
            font-size: {{ 12 * $fontScale }}px;
            page-break-inside: avoid;
        }

        .treatment-item {
            flex: 0 0 auto;
        }

        .treatment-label {
            font-weight: bold;
            display: inline;
        }

        .treatment-value {
            display: inline;
            margin-left: 5px;
        }

        /* Payment Information */
        .payment-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            font-size: {{ 12 * $fontScale }}px;
        }

        .payment-item {
            text-align: left;
        }

        .payment-label {
            font-weight: bold;
            font-size: {{ 11 * $fontScale }}px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .payment-amount {
            font-size: {{ 14 * $fontScale }}px;
            font-weight: bold;
        }

        /* Footer */
        .footer-section {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }

        .clinic-info {
            font-size: {{ 12 * $fontScale }}px;
        }

        .clinic-label {
            font-weight: bold;
            font-size: {{ 11 * $fontScale }}px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .clinic-value {
            font-size: {{ 12 * $fontScale }}px;
        }

        .signature-area {
            text-align: center;
        }

        .signature-line {
            width: 100px;
            border-bottom: 1px solid #000;
            margin-bottom: 2px;
            height: 18px;
        }

        .doctor-name {
            font-size: {{ 12 * $fontScale }}px;
            font-weight: bold;
        }

        .doctor-title {
            font-size: {{ 10 * $fontScale }}px;
            margin-top: 2px;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .prescription-wrapper {
                box-shadow: none;
                border: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="prescription-wrapper">
        <!-- Letterhead Space -->
        <div class="letterhead-space"></div>

        <!-- Rx Symbol -->
        <div class="rx-symbol">℞</div>

        <!-- Patient Information -->
        @if (count(array_filter(['patient_name', 'patient_id', 'patient_age', 'patient_gender', 'patient_mobile', 'patient_address'], fn($f) => in_array($f, $selectedFields))) > 0)
            <div class="patient-section">
                <div class="patient-grid">
                    @if (in_array('patient_name', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Name</div>
                            <div class="patient-value">{{ strip_tags($data['patient_name'] ?? '') }}</div>
                        </div>
                    @endif
                    @if (in_array('patient_id', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Patient ID</div>
                            <div class="patient-value">{{ strip_tags($data['patient_id'] ?? '') }}</div>
                        </div>
                    @endif
                    @if (in_array('patient_age', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Age / Gender</div>
                            <div class="patient-value">
                                {{ strip_tags($data['patient_age'] ?? '') }}
                                @if (in_array('patient_gender', $selectedFields))
                                    / {{ strip_tags($data['patient_gender'] ?? '') }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                <div class="patient-grid" style="margin-top: 8px;">
                    @if (in_array('patient_mobile', $selectedFields))
                        <div class="patient-field">
                            <div class="patient-label">Mobile</div>
                            <div class="patient-value">{{ strip_tags($data['patient_mobile'] ?? '') }}</div>
                        </div>
                    @endif
                    @if (in_array('patient_address', $selectedFields))
                        <div class="patient-field" style="grid-column: 1 / -1;">
                            <div class="patient-label">Address</div>
                            <div class="patient-value">{{ strip_tags($data['patient_address'] ?? '') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Medical Sections -->
        @if (in_array('nadi', $selectedFields) && $data['nadi'])
            <div class="section">
                <div class="section-header">{{ __('messages.nadi') }}</div>
                <div class="section-body">{{ trim(strip_tags($data['nadi'])) }}</div>
            </div>
        @endif

        @if (in_array('lakshane', $selectedFields) && $data['lakshane'])
            <div class="section">
                <div class="section-header">{{ __('messages.lakshane') }}</div>
                <div class="section-body">{{ trim(strip_tags($data['lakshane'])) }}</div>
            </div>
        @endif

        @if (in_array('nidan', $selectedFields) && $data['nidan'])
            <div class="section">
                <div class="section-header">{{ __('messages.nidan') }}</div>
                <div class="section-body">{{ trim(strip_tags($data['nidan'])) }}</div>
            </div>
        @endif

        @if (in_array('chikitsa', $selectedFields) && $data['chikitsa'])
            <div class="section">
                <div class="section-header">{{ __('messages.chikitsa') }}</div>
                <div class="section-body">{{ trim(strip_tags($data['chikitsa'])) }}</div>
            </div>
        @endif

        <!-- Treatment Details -->
        @if (in_array('days', $selectedFields) || in_array('packets', $selectedFields))
            <div class="treatment-line">
                @if (in_array('days', $selectedFields) && $data['days'])
                    <div class="treatment-item">
                        <span class="treatment-label">Duration (दिवस):</span>
                        <span class="treatment-value">{{ strip_tags($data['days']) }}</span>
                    </div>
                @endif
                @if (in_array('packets', $selectedFields) && $data['packets'])
                    <div class="treatment-item">
                        <span class="treatment-label">Packets (पुडे):</span>
                        <span class="treatment-value">{{ strip_tags($data['packets']) }}</span>
                    </div>
                @endif
            </div>
        @endif

        @if (in_array('vishesh', $selectedFields) && $data['vishesh'])
            <div class="section">
                <div class="section-header">{{ __('messages.vishesh') }}</div>
                <div class="section-body">{{ trim(strip_tags($data['vishesh'])) }}</div>
            </div>
        @endif

        <!-- Payment Information -->
        @if (in_array('amount_billed', $selectedFields) || in_array('amount_paid', $selectedFields) || in_array('amount_due', $selectedFields))
            <div class="payment-section">
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
                <div class="clinic-info">
                    <div class="clinic-label">Clinic</div>
                    <div class="clinic-value">{{ strip_tags($data['branch_name'] ?? '') }}</div>
                </div>
            @endif
            @if (in_array('doctor_name', $selectedFields))
                <div class="signature-area">
                    <div class="signature-line"></div>
                    <div class="doctor-name">{{ strip_tags($data['doctor_name'] ?? '') }}</div>
                    <div class="doctor-title">Doctor</div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
