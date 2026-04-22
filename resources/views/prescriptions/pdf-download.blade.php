<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {{ $patient->name }}</title>
    @php
        $fontScale = $font_scale ?? 1;
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 15mm 15mm 15mm 15mm; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: {{ 12 * $fontScale }}px;
            color: #2c221e; /* Deep Earthy Grey/Brown */
            line-height: 1.6;
            background: #ffffff;
        }
        .prescription-container { width: 100%; margin: 0 auto; padding-top: 5px; }

        /* Ayurvedic Motif / Invocation */
        .invocation {
            text-align: center;
            font-family: 'Noto Sans Devanagari', 'Georgia', serif;
            font-size: {{ 16 * $fontScale }}px;
            font-weight: 600;
            color: #c05621; /* Saffron Accent */
            margin-bottom: 12px;
            letter-spacing: 2px;
        }

        /* Patient Banner - Warm Earthy Tones */
        .patient-table {
            width: 100%; border-collapse: collapse; margin-bottom: 25px;
            border-radius: 6px; overflow: hidden;
            font-size: {{ 11 * $fontScale }}px;
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
            font-size: {{ 10 * $fontScale }}px;
        }
        .patient-table td { color: #2c221e; width: 35%; font-weight: 500; }

        /* Split Layout */
        .main-body { width: 100%; display: table; table-layout: fixed; }
        .left-col { display: table-cell; width: 35%; vertical-align: top; padding-right: 25px; border-right: 1px solid #e6d8c3; }
        .right-col { display: table-cell; width: 65%; vertical-align: top; padding-left: 25px; }

        /* Section Titles - Classical Serif & Ayurvedic Green */
        .section-title {
            font-family: 'Georgia', serif;
            font-size: {{ 12 * $fontScale }}px;
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

        .content-text { font-size: {{ 13 * $fontScale }}px; color: #2c221e; margin-bottom: 6px; white-space: pre-wrap; word-break: break-word; }

        /* Rx Styling */
        .rx-wrapper { margin-bottom: 15px; }
        .rx-icon {
            font-family: 'Georgia', serif;
            font-size: {{ 36 * $fontScale }}px;
            font-weight: bold; font-style: italic;
            color: #276749;
            line-height: 1;
        }

        .treatment-box { margin-top: 10px; }

        /* Duration box matching traditional feel */
        .duration-box {
            margin-top: 30px; background: #fffaf0;
            border: 1px dashed #d4b895; padding: 12px 15px;
            border-radius: 5px; font-size: {{ 12 * $fontScale }}px;
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
            display: inline-block; font-size: {{ 11 * $fontScale }}px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.02);
        }
        .payment-line { margin-bottom: 3px; }
        .payment-line strong { display: inline-block; width: 100px; color: #744210; }

        .signature-area { margin-top: 20px; }
        .signature-line { display: inline-block; border-bottom: 1px solid #744210; width: 220px; margin-bottom: 8px; }
        .doctor-name { font-family: 'Georgia', serif; font-weight: bold; color: #276749; font-size: {{ 15 * $fontScale }}px; margin-top: 5px; }
        .clinic-name { color: #c05621; font-weight: 500; font-size: {{ 11 * $fontScale }}px; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="prescription-container">

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
                        <div class="content-text" style="font-size: {{ 14 * $fontScale }}px; line-height: 1.8;">
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
</body>
</html>
