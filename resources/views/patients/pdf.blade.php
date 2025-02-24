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

        th,
        td {
            text-align: left;
            vertical-align: top;
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
        {{-- <tr>
            <th>{{ __('messages.Birthdate') }}</th>
            <td>{{ $patient->birthdate }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Gender') }}</th>
            <td>{{ $patient->gender }}</td>
        </tr> --}}
        <tr>
            <th>{{ __('messages.Age') }}/{{ __('messages.Gender') }}</th>
            <td>
                {{ $patient->birthdate?->age ?? __('') }}/{{ $patient->gender ?? __('') }}
            </td>
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
        {{-- <tr>
            <th>{{ __('messages.occupation') }}</th>
            <td>{{ $patient->occupation }}</td>
        </tr> --}}
        {{-- <tr>
            <th>{{ __('messages.Remark') }}</th>
            <td>{{ $patient->remark }}</td>
        </tr> --}}
        {{-- <tr>
            <th>{{ __('messages.Balance') }}</th>
            <td>{{ $patient->balance }}</td>
        </tr> --}}
    </table>

    <h2>{{ __('messages.follow_ups') }}</h2>

    @if ($patient->followUps->count() > 0)

        <table class="patient-details-table">
            <thead>
                <tr>
                    <th>{{ __('messages.Created At') }}</th>
                    <th>{{ __('नाडी') }}/{{ __('लक्षणे') }}</th>
                    <th>{{ __('चिकित्सा') }}</th>
                    <th>{{ __('messages.Payments') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($patient->followUps->sortByDesc('created_at') as $followUp)
                    @php
                        $checkUpInfo = json_decode($followUp->check_up_info, true);
                    @endphp
                    <tr>
                        <td>{{ $followUp->created_at->format('d M Y, h:i A') }}<br>
                            @if (isset($checkUpInfo['user_name']))
                                <strong>{{ __('S/B') }}:</strong> {{ $checkUpInfo['user_name'] }}<br>
                            @endif
                            @if (isset($checkUpInfo['branch_name']))
                                <strong>{{ __('OPD') }}:</strong> {{ $checkUpInfo['branch_name'] }}<br>
                            @endif
                        </td>
                        <td>
                            @if ($followUp->check_up_info)
                                @foreach ($checkUpInfo as $key => $value)
                                    @if (in_array($key, ['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठिन', 'साम']))
                                        @if (is_array($value))
                                            @foreach ($value as $option)
                                                <p>{{ __($option) }}</p>
                                            @endforeach
                                        @elseif ($value == 1)
                                            <p>{{ __($key) }}</p>
                                        @endif
                                    @endif
                                @endforeach

                                @if (isset($checkUpInfo['nadi']))
                                    <strong>{{ __('नाडी') }}:</strong> {{ $checkUpInfo['nadi'] }}<br>
                                @endif
                                @if (isset($followUp->diagnosis))
                                    <strong>{{ __('लक्षणे') }}:</strong> {{ $followUp->diagnosis }}<br>
                                @endif
                                @if (isset($checkUpInfo['days']))
                                    <strong>{{ __('दिवस') }}:</strong>
                                    {{ $checkUpInfo['days'] }}<br>
                                @endif
                                @if (isset($checkUpInfo['packets']))
                                    <strong>{{ __('पुड्या') }}:</strong>
                                    {{ $checkUpInfo['packets'] }}
                                @endif
                            @endif
                        </td>

                        <td>
                            @if (isset($checkUpInfo['chikitsa']))
                                {{ $checkUpInfo['chikitsa'] }}
                            @endif
                        </td>
                        <td>
                            @if (isset($checkUpInfo['payment_method']))
                                <strong>{{ __('messages.Payment Method') }}:</strong>
                                {{ $checkUpInfo['payment_method'] }}<br>
                            @endif
                            @if (isset($checkUpInfo['amount']))
                                <strong>{{ __('messages.Amount') }}:</strong> {{ $checkUpInfo['amount'] }}<br>
                            @endif
                            @if (isset($checkUpInfo['balance']))
                                <strong>{{ __('messages.Balance') }}:</strong> {{ $checkUpInfo['balance'] }}<br>
                            @endif
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
