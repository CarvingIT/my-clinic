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
}
