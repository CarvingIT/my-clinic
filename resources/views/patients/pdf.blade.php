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

        p {
            margin: 0;
            padding: 0;
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
            <td>{!! $patient->vishesh !!}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Height') }}</th>
            <td>{{ $patient->height }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.Weight') }}</th>
            <td>{{ $patient->weight }}</td>
        </tr>
        @if ($patient->height && $patient->weight)
            @php
                $heightInMeters = $patient->height / 100; // Convert cm to meters
                $bmi = $patient->weight / ($heightInMeters * $heightInMeters);

                // Determine BMI category
                if ($bmi < 18.5) {
                    $bmiCategory = __('Underweight');
                } elseif ($bmi >= 18.5 && $bmi < 25) {
                    $bmiCategory = __('Healthy Weight');
                } elseif ($bmi >= 25 && $bmi < 30) {
                    $bmiCategory = __('Overweight');
                } else {
                    $bmiCategory = __('Obese');
                }
            @endphp
            <tr>
                <th>{{ __('messages.BMI') }}</th>
                <td>{{ number_format($bmi, 2) }} ({{ $bmiCategory }})</td>
            </tr>
        @endif
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
                                    @if (in_array($key, ['वात', 'पित्त', 'कफ', 'सूक्ष्म', 'कठीण', 'साम']))
                                        @if (is_array($value))
                                            @foreach ($value as $option)
                                                <p>{{ __($option) }}</p>
                                            @endforeach
                                        @elseif ($value == 1)
                                            <p>{{ __($key) }}</p>
                                        @endif
                                    @endif
                                @endforeach

                                {{-- @if (isset($checkUpInfo['nadi']))
                                    <p><strong>{{ __('नाडी') }}:</strong> {!! $checkUpInfo['nadi'] !!}</p>
                                    @if (isset($checkUpInfo['nadi_dots']))
                                        @php
                                            $nadiDots = $checkUpInfo['nadi_dots'] ?? [[], [], []];
                                        @endphp
                                        <div style="margin-top: 8px; display: flex; gap: 1px;">
                                            @foreach($nadiDots as $box)
                                                <table style="border-collapse: collapse; background-color: #f3f4f6; border: 1px solid #f3f4f6; border-radius: 4px;">
                                                    <tbody>
                                                        @for($row = 0; $row < 3; $row++)
                                                            <tr>
                                                                @for($col = 0; $col < 3; $col++)
                                                                    @php $index = $row * 3 + $col; @endphp
                                                                    <td style="width: 3px; height: 3px; background-color: #ffffff; border-right: {{ $col < 2 ? '1px solid #d1d5db' : 'none' }}; border-bottom: {{ $row < 2 ? '1px solid #d1d5db' : 'none' }}; text-align: center; vertical-align: middle; {{ ($box[$index] ?? false) ? 'color: #ef4444; font-size: 6px;' : '' }}">
                                                                        {{ ($box[$index] ?? false) ? '■' : '' }}
                                                                    </td>
                                                                @endfor
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif --}}

                                @if (isset($followUp->diagnosis))
                                    <p><strong>{{ __('लक्षणे') }}:</strong> {!! $followUp->diagnosis !!}</p>
                                @endif
                            @endif
                        </td>

                        <td>
                            @if (isset($checkUpInfo['chikitsa']))
                                {!! $checkUpInfo['chikitsa'] !!}
                            @endif
                            @if (isset($checkUpInfo['days']))
                                <p><strong>{{ __('दिवस') }}:</strong> {{ $checkUpInfo['days'] }}</p>
                            @endif
                            @if (isset($checkUpInfo['packets']))
                                <p><strong>{{ __('पुड्या') }}:</strong> {{ $checkUpInfo['packets'] }}</p>
                            @endif
                        </td>
                        <td>
                            @if (isset($checkUpInfo['payment_method']))
                                <strong>{{ __('messages.Payment Method') }}:</strong>
                                {{ $checkUpInfo['payment_method'] }}<br>
                            @endif
                            <strong>{{ __('messages.Amount Billed') }}:</strong>
                            ₹{{ number_format($followUp->amount_billed ?? 0, 2) }}<br>
                            <strong>{{ __('messages.Amount Paid') }}:</strong>
                            ₹{{ number_format($followUp->amount_paid ?? 0, 2) }}<br>

                            @php
                                $totalDue = max(($followUp->amount_billed ?? 0) - ($followUp->amount_paid ?? 0), 0);
                            @endphp

                            <strong>{{ __('messages.Amount Due') }}:</strong>
                            <span class="{{ $totalDue == 0 ? 'text-green-600' : 'text-red-600' }}">
                                ₹{{ number_format($totalDue, 2) }}
                            </span><br>
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
