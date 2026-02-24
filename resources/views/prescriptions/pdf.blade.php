<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $patient_name }}</title>
    <style>
        @page {
            size: A5;
            margin: 8mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', 'Arial', 'Noto Sans Devanagari', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
            line-height: 1.4;
        }

        .prescription-page {
            width: 100%;
            max-width: 148mm;
            min-height: 200mm;
            margin: 0 auto;
            border: 1.5px solid #2c3e6b;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #2b6cb0 100%);
            color: #fff;
            padding: 10px 14px;
            text-align: center;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #e2a84b, #f6d365, #e2a84b);
        }
        .clinic-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .clinic-tagline {
            font-size: 9px;
            letter-spacing: 2px;
            opacity: 0.85;
            text-transform: uppercase;
        }

        /* Patient Info */
        .patient-bar {
            background: #f0f4ff;
            border-bottom: 1px solid #d0d8e8;
            padding: 8px 14px;
        }
        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px 16px;
        }
        .patient-grid .field {
            display: flex;
            align-items: baseline;
            gap: 4px;
            font-size: 10px;
        }
        .patient-grid .field .label {
            font-weight: 700;
            color: #2c3e6b;
            white-space: nowrap;
            min-width: 55px;
        }
        .patient-grid .field .value {
            color: #1a1a2e;
        }
        .patient-grid .field.full-width {
            grid-column: 1 / -1;
        }
        .date-bar {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #4a5568;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #cbd5e0;
        }

        /* Rx */
        .rx-divider {
            display: flex;
            align-items: center;
            padding: 4px 14px;
            gap: 8px;
        }
        .rx-symbol {
            font-family: 'Times New Roman', serif;
            font-size: 26px;
            font-weight: 700;
            font-style: italic;
            color: #2c5282;
            line-height: 1;
        }
        .rx-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #2c5282, transparent);
        }

        /* Body */
        .prescription-body {
            padding: 6px 14px 10px;
        }
        .section {
            margin-bottom: 8px;
        }
        .section-header {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2c5282;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .section-content {
            font-size: 11px;
            color: #2d3748;
            padding-left: 4px;
            line-height: 1.5;
        }

        /* Table */
        .med-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 4px;
        }
        .med-table th {
            background: #edf2f7;
            color: #2c3e6b;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            padding: 4px 6px;
            text-align: left;
            border-bottom: 1.5px solid #cbd5e0;
        }
        .med-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #edf2f7;
        }

        /* Instructions */
        .instructions-box {
            background: #fffbeb;
            border: 1px solid #f6e05e;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 9px;
            color: #744210;
            margin-top: 6px;
        }
        .instructions-box .title {
            font-weight: 700;
            margin-bottom: 2px;
        }

        /* Payment */
        .payment-strip {
            background: #f7fafc;
            border-top: 1px dashed #cbd5e0;
            padding: 4px 14px;
            display: flex;
            justify-content: space-around;
            font-size: 9px;
            color: #4a5568;
        }
        .payment-strip .pay-item strong {
            color: #2c3e6b;
        }

        /* Footer */
        .footer {
            border-top: 1.5px solid #2c3e6b;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .next-visit {
            font-size: 9px;
            color: #4a5568;
        }
        .next-visit strong {
            color: #2c5282;
        }
        .signature-area {
            text-align: center;
        }
        .signature-line {
            width: 100px;
            border-bottom: 1px solid #2c3e6b;
            margin-bottom: 3px;
            height: 24px;
        }
        .signature-label {
            font-size: 8px;
            color: #4a5568;
        }
        .doctor-name {
            font-size: 10px;
            font-weight: 700;
            color: #2c3e6b;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 60px;
            font-weight: 900;
            color: rgba(44, 82, 130, 0.03);
            letter-spacing: 8px;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="prescription-page">
        <div class="watermark">PRESCRIPTION</div>

        <div class="header">
            <div class="clinic-name">जातेगांवकर चिकित्सालय</div>
            <div class="clinic-tagline">{{ '{branch_name}' }}</div>
        </div>

        <div class="patient-bar">
            <div class="patient-grid">
                <div class="field">
                    <span class="label">Patient:</span>
                    <span class="value"><strong>{{ $patient_name }}</strong></span>
                </div>
                <div class="field">
                    <span class="label">ID:</span>
                    <span class="value">{{ $patient_id }}</span>
                </div>
                <div class="field">
                    <span class="label">Age/Sex:</span>
                    <span class="value">{{ $patient_age }} / {{ $patient_gender }}</span>
                </div>
                <div class="field">
                    <span class="label">Mobile:</span>
                    <span class="value">{{ $patient_mobile }}</span>
                </div>
                <div class="field">
                    <span class="label">Wt/Ht:</span>
                    <span class="value">{{ $patient_weight }} / {{ $patient_height }}</span>
                </div>
                <div class="field full-width">
                    <span class="label">Address:</span>
                    <span class="value">{{ $patient_address }}</span>
                </div>
            </div>
            <div class="date-bar">
                <span><strong>Date:</strong> {{ $followup_date }}</span>
                <span><strong>Doctor:</strong> {{ $doctor_name }}</span>
            </div>
        </div>

        <div class="rx-divider">
            <span class="rx-symbol">℞</span>
            <div class="rx-line"></div>
        </div>

        <div class="prescription-body">
            <div class="section">
                <div class="section-header">नाडी (Nadi / Pulse)</div>
                <div class="section-content">{!! $nadi !!}</div>
            </div>

            <div class="section">
                <div class="section-header">लक्षणे (Lakshane / Symptoms)</div>
                <div class="section-content">{!! $lakshane !!}</div>
            </div>

            <div class="section">
                <div class="section-header">निदान (Nidan / Diagnosis)</div>
                <div class="section-content">{!! $nidan !!}</div>
            </div>

            <div class="section">
                <div class="section-header">चिकित्सा (Chikitsa / Treatment)</div>
                <div class="section-content">{!! $chikitsa !!}</div>
            </div>

            <div class="section">
                <table class="med-table">
                    <thead>
                        <tr>
                            <th style="width:50%">Duration (दिवस)</th>
                            <th style="width:50%">Packets (पुडे)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $days }}</td>
                            <td>{{ $packets }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="instructions-box">
                <div class="title">विशेष सूचना (Special Instructions)</div>
                <div>{{ $vishesh }}</div>
            </div>
        </div>

        <div class="payment-strip">
            <div class="pay-item"><strong>Billed:</strong> ₹{{ $amount_billed }}</div>
            <div class="pay-item"><strong>Paid:</strong> ₹{{ $amount_paid }}</div>
            <div class="pay-item"><strong>Due:</strong> ₹{{ $amount_due }}</div>
        </div>

        <div class="footer">
            <div class="next-visit">
                <strong>Next Visit:</strong> As advised
            </div>
            <div class="signature-area">
                <div class="signature-line"></div>
                <div class="doctor-name">{{ $doctor_name }}</div>
                <div class="signature-label">Authorized Signatory</div>
            </div>
        </div>
    </div>
</body>
</html>
