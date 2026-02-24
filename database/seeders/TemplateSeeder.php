<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Medical Certificate Template
        Template::updateOrCreate(
            ['slug' => 'medical_certificate'],
            [
                'name' => 'Medical Certificate',
                'type' => 'certificate',
                'content' => $this->getMedicalCertificateContent(),
                'placeholders' => [
                    'patient_name',
                    'patient_age',
                    'start_date',
                    'end_date',
                    'medical_condition',
                    'current_date',
                    'branch',
                ],
                'is_active' => true,
            ]
        );

        // Consent Form Template
        Template::updateOrCreate(
            ['slug' => 'consent_form'],
            [
                'name' => 'Consent Form',
                'type' => 'form',
                'content' => $this->getConsentFormContent(),
                'placeholders' => [
                    'patient_name',
                    'patient_age',
                    'procedure_name',
                    'current_date',
                    'branch',
                ],
                'is_active' => true,
            ]
        );

        // Prescription Template
        Template::updateOrCreate(
            ['slug' => 'prescription'],
            [
                'name' => 'Prescription',
                'type' => 'prescription',
                'content' => $this->getPrescriptionContent(),
                'placeholders' => [
                    'patient_name',
                    'patient_age',
                    'patient_gender',
                    'patient_id',
                    'patient_mobile',
                    'patient_address',
                    'patient_weight',
                    'patient_height',
                    'current_date',
                    'followup_date',
                    'branch_name',
                    'doctor_name',
                    'nadi',
                    'nidan',
                    'lakshane',
                    'chikitsa',
                    'days',
                    'packets',
                    'vishesh',
                    'amount_billed',
                    'amount_paid',
                    'amount_due',
                ],
                'is_active' => true,
            ]
        );
    }

    /**
     * Get Medical Certificate HTML content
     */
    private function getMedicalCertificateContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate - {patient_name}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            border: 2px solid #333;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            color: #444;
        }
        .date {
            font-size: 16px;
            color: #555;
        }
        .footer {
            text-align: right;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        .doctor-signature {
            margin-bottom: 15px;
            height: 40px;
        }
        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
        }
        .bold-text {
            font-weight: bold;
        }
        .header p {
            font-size: 18px;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Medical Certificate</h1>
            <p class="date">Date: {current_date}</p>
        </div>

        <p>To whom it may concern,</p>
        <p>This is to certify that <strong>{patient_name}</strong>, aged {patient_age} years, has been under our care and treatment at the clinic from {start_date} to {end_date}, for the management and treatment of the medical condition {medical_condition}.</p>

        <p>Based on our clinical assessment, it was determined that the patient was unfit for work during this period, and it was advised that they take sufficient rest to facilitate recovery.</p>
        <p>We strongly recommend that the patient adheres to the prescribed rest period to ensure a complete and speedy recovery.</p>

        <div class="footer">
            <div class="doctor-signature"></div>
            <p>Doctor's Name and Signature</p>
            <p>Branch: {branch}</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get Consent Form HTML content
     */
    private function getConsentFormContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Form - {patient_name}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.8;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            border: 2px solid #333;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
            color: #444;
        }
        .date {
            font-size: 14px;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        p {
            font-size: 14px;
            color: #555;
            margin-bottom: 12px;
            text-align: justify;
        }
        .patient-info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .patient-info p {
            margin-bottom: 5px;
        }
        .consent-text {
            background-color: #fff9e6;
            padding: 15px;
            border-left: 4px solid #f0c040;
            margin-bottom: 20px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 12px;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Patient Consent Form</h1>
            <p class="date">Date: {current_date}</p>
        </div>

        <div class="patient-info">
            <p><strong>Patient Name:</strong> {patient_name}</p>
            <p><strong>Age:</strong> {patient_age} years</p>
            <p><strong>Procedure/Treatment:</strong> {procedure_name}</p>
        </div>

        <div class="section">
            <p class="section-title">Declaration and Consent:</p>
            <div class="consent-text">
                <p>I, <strong>{patient_name}</strong>, hereby consent to undergo the procedure/treatment mentioned above. I confirm that:</p>
            </div>
        </div>

        <div class="section">
            <ul>
                <li>I have been informed about the nature, purpose, and expected outcomes of the procedure/treatment.</li>
                <li>I understand the potential risks, complications, and side effects associated with this procedure/treatment.</li>
                <li>I have had the opportunity to ask questions and have received satisfactory answers.</li>
                <li>I understand that results may vary and no guarantees have been made regarding the outcome.</li>
                <li>I consent to the administration of anesthesia if deemed necessary by the treating physician.</li>
                <li>I authorize the medical team to take appropriate actions in case of any unforeseen medical emergency during the procedure.</li>
                <li>I have disclosed all relevant medical history, allergies, and current medications to the medical team.</li>
            </ul>
        </div>

        <div class="section">
            <p>By signing below, I acknowledge that I have read and understood the above information and voluntarily give my consent for the procedure/treatment.</p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p class="signature-label">Patient's Signature</p>
                <p class="signature-label">Date: _______________</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p class="signature-label">Doctor's Signature</p>
                <p class="signature-label">Date: _______________</p>
            </div>
        </div>

        <div class="footer">
            <p>Branch: {branch}</p>
            <p>This form must be signed prior to the commencement of any procedure/treatment.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get Prescription HTML content
     */
    private function getPrescriptionContent(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - {patient_name}</title>
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

        /* ── Header ── */
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
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .clinic-tagline {
            font-size: 9px;
            letter-spacing: 2px;
            opacity: 0.85;
            text-transform: uppercase;
        }
        .header-details {
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            margin-top: 4px;
            opacity: 0.9;
        }

        /* ── Patient Info Bar ── */
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
            word-break: break-word;
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

        /* ── Rx Symbol ── */
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

        /* ── Body ── */
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
        .section-content p {
            margin: 0;
        }

        /* ── Medication Table ── */
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
            letter-spacing: 0.5px;
            padding: 4px 6px;
            text-align: left;
            border-bottom: 1.5px solid #cbd5e0;
        }
        .med-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }

        /* ── Instructions ── */
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

        /* ── Footer ── */
        .footer {
            border-top: 1.5px solid #2c3e6b;
            padding: 8px 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
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
            letter-spacing: 0.5px;
        }
        .doctor-name {
            font-size: 10px;
            font-weight: 700;
            color: #2c3e6b;
        }

        /* ── Payment Strip ── */
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

        /* ── Watermark ── */
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
            z-index: 0;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .prescription-page { border: none; border-radius: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="prescription-page">
        <div class="watermark">PRESCRIPTION</div>

        <!-- Header -->
        <div class="header">
            <div class="clinic-name">जातेगांवकर चिकित्सालय</div>
            <div class="clinic-tagline">{branch_name}</div>
        </div>

        <!-- Patient Info -->
        <div class="patient-bar">
            <div class="patient-grid">
                <div class="field">
                    <span class="label">Patient:</span>
                    <span class="value"><strong>{patient_name}</strong></span>
                </div>
                <div class="field">
                    <span class="label">ID:</span>
                    <span class="value">{patient_id}</span>
                </div>
                <div class="field">
                    <span class="label">Age/Sex:</span>
                    <span class="value">{patient_age} / {patient_gender}</span>
                </div>
                <div class="field">
                    <span class="label">Mobile:</span>
                    <span class="value">{patient_mobile}</span>
                </div>
                <div class="field">
                    <span class="label">Wt/Ht:</span>
                    <span class="value">{patient_weight} / {patient_height}</span>
                </div>
                <div class="field full-width">
                    <span class="label">Address:</span>
                    <span class="value">{patient_address}</span>
                </div>
            </div>
            <div class="date-bar">
                <span><strong>Date:</strong> {followup_date}</span>
                <span><strong>Doctor:</strong> {doctor_name}</span>
            </div>
        </div>

        <!-- Rx Divider -->
        <div class="rx-divider">
            <span class="rx-symbol">℞</span>
            <div class="rx-line"></div>
        </div>

        <!-- Body -->
        <div class="prescription-body">
            <!-- Nadi -->
            <div class="section">
                <div class="section-header">नाडी (Nadi / Pulse)</div>
                <div class="section-content">{nadi}</div>
            </div>

            <!-- Lakshane -->
            <div class="section">
                <div class="section-header">लक्षणे (Lakshane / Symptoms)</div>
                <div class="section-content">{lakshane}</div>
            </div>

            <!-- Nidan -->
            <div class="section">
                <div class="section-header">निदान (Nidan / Diagnosis)</div>
                <div class="section-content">{nidan}</div>
            </div>

            <!-- Chikitsa -->
            <div class="section">
                <div class="section-header">चिकित्सा (Chikitsa / Treatment)</div>
                <div class="section-content">{chikitsa}</div>
            </div>

            <!-- Duration & Packets -->
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
                            <td>{days}</td>
                            <td>{packets}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Vishesh -->
            <div class="instructions-box">
                <div class="title">विशेष सूचना (Special Instructions)</div>
                <div>{vishesh}</div>
            </div>
        </div>

        <!-- Payment Strip -->
        <div class="payment-strip">
            <div class="pay-item"><strong>Billed:</strong> ₹{amount_billed}</div>
            <div class="pay-item"><strong>Paid:</strong> ₹{amount_paid}</div>
            <div class="pay-item"><strong>Due:</strong> ₹{amount_due}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="next-visit">
                <strong>Next Visit:</strong> As advised
            </div>
            <div class="signature-area">
                <div class="signature-line"></div>
                <div class="doctor-name">{doctor_name}</div>
                <div class="signature-label">Authorized Signatory</div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
