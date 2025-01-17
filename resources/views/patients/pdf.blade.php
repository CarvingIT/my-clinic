<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - {{ $patient->name }}</title>
    <style>
        body {
            font-family: 'Mangal', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 20px;
        }

        h1 {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 1.5em;
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }


        .patient-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .patient-details-table th,
        .patient-details-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .patient-details-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <h1>{{ $patient->name }} ({{ $patient->patient_id }})</h1>

    <h2>{{ __('रुग्णाची माहिती') }}</h2>
    <table class="patient-details-table">
        <tr>
            <th>{{ __('messages.Patient ID') }}</th>
            <td>{{ $patient->patient_id }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.name') }}</th>
            <td>{{ $patient->name }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Birthdate') }}</th>
            <td>{{ $patient->birthdate }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Gender') }}</th>
            <td>{{ $patient->gender }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.mobile_phone') }}</th>
            <td>{{ $patient->mobile_phone }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Email ID') }}</th>
            <td>{{ $patient->email_id }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.address') }}</th>
            <td>{{ $patient->address }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Vishesh') }}</th>
            <td>{{ $patient->vishesh }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.occupation') }}</th>
            <td>{{ $patient->occupation }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Remark') }}</th>
            <td>{{ $patient->remark }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Balance') }}</th>
            <td>{{ $patient->balance }}</td>
        </tr>
    </table>

    <h2>{{ __('messages.follow_ups') }}</h2>

    @if ($patient->followUps->count() > 0)

        <table class="patient-details-table">
            <thead>
                <tr>
                    <th>{{ __('messages.Created At') }}</th>
                    <th>{{ __('नाडी') }}</th>
                    <th>{{ __('लक्षणे') }}</th>
                    <th>{{ __('चिकित्सा') }}</th>
                    <th>{{ __('messages.Additional') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($patient->followUps->sortByDesc('created_at') as $followUp)
                    @php
                        $checkUpInfo = json_decode($followUp->check_up_info, true);
                    @endphp
                    <tr>
                        <td>{{ $followUp->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div>
                                @foreach ($checkUpInfo as $key => $value)
                                    @if (in_array($key, [
                                            'वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठिन', 'साम',
                                            'प्राण', 'व्यान', 'स्थूल', 'तीक्ष्ण', 'वेग', 'अनियमित',
                                        ]))
                                        @if (is_array($value))
                                            @foreach ($value as $option)
                                                <p>{{ __($option) }}</p>
                                            @endforeach
                                        @elseif ($value == 1)
                                            <p>{{ __($key) }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <div>
                                <p><span style="font-weight: bold;">{{ __('निदान') }}:</span> {{ $checkUpInfo['nidan'] ?? '' }}</p>
                                <p><span style="font-weight: bold;">{{ __('उपशय') }}:</span> {{ $checkUpInfo['upashay'] ?? '' }}</p>
                                <p><span style="font-weight: bold;">{{ __('सल्ला') }}:</span> {{ $checkUpInfo['salla'] ?? '' }}</p>
                            </div>
                        </td>
                        <td>{{ $followUp->diagnosis }}</td>
                        <td>
                            @if ($followUp->treatment)
                                <p>{{ $followUp->treatment }}</p>
                            @endif
                            @foreach ($checkUpInfo as $key => $value)
                                @if (in_array($key, ['अर्श', 'ग्रहणी', 'ज्वर/प्रतिश्याय']))
                                    @if (is_array($value))
                                        @foreach ($value as $option)
                                            <p>{{ __($option) }}</p>
                                        @endforeach
                                    @elseif($value == 1)
                                        <p>{{ __($key) }}</p>
                                    @endif
                                @endif
                            @endforeach
                            @if (isset($checkUpInfo['chikitsa_combo']))
                                <p><span style="font-weight: bold;">{{ __('messages.Chikitsa Combo') }}:</span> {{ $checkUpInfo['chikitsa_combo'] }}</p>
                            @endif
                        </td>
                        <td>
                            <p><span style="font-weight: bold;">{{ __('messages.Payment Method') }}:</span> {{ $checkUpInfo['payment_method'] ?? '' }}</p>
                            <p><span style="font-weight: bold;">{{ __('messages.Amount') }}:</span> {{ $checkUpInfo['amount'] ?? '' }}</p>
                            <p><span style="font-weight: bold;">{{ __('messages.Balance') }}:</span> {{ $checkUpInfo['balance'] ?? '' }}</p>
                            <p><span style="font-weight: bold;">{{ __('messages.Branch') }}:</span> {{ $checkUpInfo['branch'] ?? '' }}</p>
                            <p><span style="font-weight: bold;">{{ __('messages.Doctor') }}:</span> {{ $checkUpInfo['doctor'] ?? '' }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No follow-ups found.</p>
    @endif

</body>

</html>
