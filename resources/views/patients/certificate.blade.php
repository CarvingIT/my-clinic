<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate - {{ $patient->name }}</title>
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
            /* border-bottom: 1px solid #333; */
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
            <p class="date">Date: {{ now()->format('d/m/Y') }}</p>
        </div>

        @if (isset($startDate) && isset($endDate) && isset($medicalCondition))
            <p>To whom it may concern,</p>
            <p>This is to certify that <strong>{{ $patient->name }}</strong>,
                @if ($patient->birthdate)
                    aged {{ floor(abs(now()->diffInYears($patient->birthdate))) }} years,
                @endif
                has been under our care and treatment at the clinic from
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} to
                {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}, for the management and treatment of the medical
                condition {{ $medicalCondition }}.
            </p>

            <p>
                Based on our clinical assessment, it was determined that the patient was unfit for work during this period, and it was advised that they take sufficient rest to facilitate recovery.
            </p>
            <p>
                We strongly recommend that the patient adheres to the prescribed rest period to ensure a complete and speedy recovery.
            </p>
        @endif

        <div class="footer">
            <div class="doctor-signature"></div>
            <p>Doctor's Name and Signature</p>
            <p>Branch: {{ $checkUpInfo['branch_name'] ?? '' }}</p>
        </div>
    </div>
</body>

</html>
