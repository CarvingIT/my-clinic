<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>इनव्हॉइस विवरणपत्र - {{ $patient->name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Noto Sans Devanagari';
            color: #1e293b;
            background-color: #f8fafc;
            margin: 0;
            padding: 24px;
        }

        .invoice-card {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 2px solid #0f766e;
        }

        .clinic-brand h1 {
            margin: 0;
            font-size: 22px;
            color: #0f766e;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .clinic-brand p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: #64748b;
        }

        .no-print {
            display: flex;
            gap: 10px;
        }

        .btn-print {
            background-color: #0f766e;
            color: #ffffff;
            padding: 9px 18px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .btn-print:hover {
            background-color: #115e59;
        }

        .patient-card-header {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            row-gap: 10px;
            column-gap: 20px;
            font-size: 14px;
        }

        .grid-full {
            grid-column: 1 / -1;
        }

        .label {
            font-weight: 700;
            color: #334155;
            margin-right: 6px;
        }

        .value {
            color: #0f172a;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .invoice-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .invoice-table td {
            border: 1px solid #e2e8f0;
            padding: 11px 14px;
            font-size: 14px;
            color: #1e293b;
        }

        .invoice-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .totals-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .totals-box {
            width: 320px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-row:last-child {
            border-bottom: none;
        }

        .totals-row.final-due {
            background-color: #fef2f2;
            border-top: 2px solid #ef4444;
            font-size: 16px;
            font-weight: 700;
            color: #991b1b;
        }

        .footer-signature {
            margin-top: 48px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
        }

        .sig-box {
            text-align: center;
            min-width: 180px;
        }

        .sig-line {
            border-top: 1px solid #94a3b8;
            margin-top: 40px;
            padding-top: 6px;
            font-weight: 600;
            color: #334155;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .patient-card-header {
                background-color: #ffffff;
                border: 1px solid #cbd5e1;
            }
            .invoice-table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .totals-row.final-due {
                background-color: #fef2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    <!-- Header -->
    <div class="top-bar">
        <div class="clinic-brand">
            <h1>रुग्ण इनव्हॉइस व खाते विवरणपत्र</h1>
            <p>Patient Billing Statement & Payment Ledger</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn-print">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                प्रिंट करा (Print)
            </button>
        </div>
    </div>

    <!-- Patient Info Section -->
    <div class="patient-card-header">
        <div class="patient-grid">
            <div>
                <span class="label">नाव:</span>
                <span class="value">{{ $patient->name }}</span>
            </div>
            <div>
                <span class="label">Pt. ID:</span>
                <span class="value">{{ $patient->patient_id }}</span>
            </div>
            <div>
                <span class="label">वय / लिंग:</span>
                <span class="value">
                    {{ $patient->birthdate ? floor(abs(now()->diffInYears($patient->birthdate))) . ' वर्षे' : '-' }} / {{ $patient->gender ?? '-' }}
                </span>
            </div>
            <div>
                <span class="label">कालावधी:</span>
                <span class="value">
                    @if($fromDate && $toDate)
                        {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} ते {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
                    @elseif($fromDate)
                        {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} पासून
                    @elseif($toDate)
                        {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }} पर्यंत
                    @else
                        सर्व रेकॉर्ड्स (All Records)
                    @endif
                </span>
            </div>
            <div class="grid-full">
                <span class="label">पत्ता:</span>
                <span class="value">{{ $patient->address ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 15%;">दिनांक</th>
                <th style="width: 25%;" class="text-right">एकूण बिल (₹)</th>
                <th style="width: 25%;" class="text-right">जमा रक्कम (₹)</th>
                <th style="width: 35%;">व्यवहार विवरण</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgerEntries as $entry)
                <tr>
                    <td>{{ optional($entry->date)->format('d/m/Y') }}</td>
                    <td class="text-right">
                        {{ $entry->billed_amount > 0 ? '₹' . number_format($entry->billed_amount, 2) : '-' }}
                    </td>
                    <td class="text-right" style="color: {{ $entry->paid_amount > 0 ? '#16a34a' : 'inherit' }}; font-weight: {{ $entry->paid_amount > 0 ? '600' : 'normal' }};">
                        {{ $entry->paid_amount > 0 ? '₹' . number_format($entry->paid_amount, 2) : '-' }}
                    </td>
                    <td>
                        {{ $entry->payment_method }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 24px; color: #64748b;">
                        निवडलेल्या कालावधीमध्ये कोणतेही बिल किंवा जमा नोंदी आढळल्या नाहीत.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totals Container -->
    <div class="totals-container">
        <div class="totals-box">
            @if($fromDate && $openingBalance != 0)
                <div class="totals-row" style="background-color: #f8fafc; font-weight: 600;">
                    <span class="label">प्रारंभिक शिल्लक (Opening):</span>
                    <span>₹{{ number_format($openingBalance, 2) }}</span>
                </div>
            @endif
            <div class="totals-row">
                <span class="label">एकूण बिल (Total Billed):</span>
                <span style="font-weight: 600;">₹{{ number_format($totalBilled, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="label">एकूण जमा (Total Paid):</span>
                <span style="color: #16a34a; font-weight: 600;">₹{{ number_format($totalPaid, 2) }}</span>
            </div>
            @if($totalExempted > 0)
                <div class="totals-row">
                    <span class="label">माफ केलेली रक्कम (Exempted):</span>
                    <span style="color: #d97706; font-weight: 600;">₹{{ number_format($totalExempted, 2) }}</span>
                </div>
            @endif
            <div class="totals-row final-due">
                <span class="label">एकूण बाकी (Total Due):</span>
                <span>₹{{ number_format($closingBalance, 2) }}</span>
            </div>
            @if(($fromDate || $toDate) && round($currentBalanceToday, 2) != round($closingBalance, 2))
                <div class="totals-row" style="font-size: 12px; color: #475569; background-color: #f8fafc;">
                    <span class="label">आजची एकूण उर्वरित बाकी:</span>
                    <span style="font-weight: 700; color: #0f172a;">₹{{ number_format($currentBalanceToday, 2) }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer Signature -->
    <div class="footer-signature">
        <div>
            <p style="margin: 0;">विवरणपत्र तयार केले: {{ now()->format('d/m/Y, h:i A') }}</p>
            <p style="margin: 4px 0 0 0; font-size: 12px; color: #94a3b8;">संगणकीय इनव्हॉइस विवरणपत्र</p>
        </div>
        <div class="sig-box">
            <div class="sig-line">डॉक्टरांची स्वाक्षरी</div>
        </div>
    </div>
</div>

</body>
</html>
