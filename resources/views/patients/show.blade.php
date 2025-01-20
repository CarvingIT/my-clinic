<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.patient_details') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">

                    <!-- Display success message -->
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <div x-data="{ open: false }">
                            <h2 @click="open = !open"
                                class="text-2xl font-bold text-indigo-700 mb-4 flex items-center cursor-pointer hover:text-indigo-700 dark:hover:text-indigo-300 transition duration-400"
                                style="cursor: pointer;">
                                {{ $patient->name }} ({{ $patient->patient_id }})
                                <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 ms-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 ms-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                                </svg>
                            </h2>

                            <div x-show="open" x-transition
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-600 border-b pb-4 mb-4">

                                <p>
                                    <span class=" font-semibold text-gray-700">{{ __('messages.Birthdate') }}:</span>
                                    {{ $patient->birthdate }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.Gender') }}:</span>
                                    {{ $patient->gender }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.mobile_phone') }}:</span>
                                    {{ $patient->mobile_phone }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.Email ID') }}:</span>
                                    {{ $patient->email_id }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.address') }}:</span>
                                    {{ $patient->address }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.Vishesh') }}:</span>
                                    {{ $patient->vishesh }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.occupation') }}:</span>
                                    {{ $patient->occupation }}
                                </p>
                                <p>
                                    <span class="font-semibold text-gray-700">{{ __('messages.Remark') }}:</span>
                                    {{ $patient->remark }}
                                </p>

                                <p class="col-span-1 md:col-span-2">
                                    <span class="font-semibold text-gray-700">{{ __('messages.Balance') }}:</span>
                                    {{ $patient->balance }}
                                </p>
                            </div>
                        </div>

                        <!-- Add Follow Up Button -->
                        <div class="flex justify-end mb-8">
                            <a href="{{ route('followups.create', ['patient' => $patient->id]) }}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-md shadow-md transition duration-300">
                                {{ __('messages.add_follow_up') }}
                            </a>

                            <a href="{{ route('patients.export-pdf', $patient) }}"
                                class="bg-green-600 hover:bg-green-700 text-black font-medium py-2 px-6 ml-4 rounded-md shadow-md transition duration-300">
                                {{ __('Export to PDF') }}
                            </a>

                            {{-- Generate Certificate button --}}

                            <div x-data="{ open: false }">
                                <button @click="open = true"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 ml-4 rounded-md shadow-md transition duration-300">
                                    {{ __('Generate Certificate') }}
                                </button>

                                <div x-show="open"
                                    class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
                                    <div class="relative w-auto max-w-3xl mx-auto my-6">
                                        <!--content-->
                                        <div
                                            class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
                                            <!--header-->
                                            <div
                                                class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
                                                <h3 class="text-3xl font-semibold">
                                                    Generate Certificate
                                                </h3>
                                                <button
                                                    class="p-1 ms-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                                                    onclick="toggleModal('modal-id')" @click="open = false">
                                                    <span
                                                        class="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                                                        ×
                                                    </span>
                                                </button>
                                            </div>
                                            <!--body-->
                                            <div class="relative p-6 flex-auto">
                                                <form method="GET"
                                                    action="{{ route('patients.certificate', $patient) }}"
                                                    target="_blank">
                                                    @csrf

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                        <div>
                                                            <x-input-label for="start_date" :value="__('Start Date')" />
                                                            <x-text-input type="date" id="start_date"
                                                                name="start_date" value="{{ old('start_date') }}"
                                                                class="mt-1 block w-full" />
                                                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                                        </div>

                                                        <div>
                                                            <x-input-label for="end_date" :value="__('End Date')" />
                                                            <x-text-input type="date" id="end_date" name="end_date"
                                                                value="{{ old('end_date') }}"
                                                                class="mt-1 block w-full" />
                                                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                                        </div>

                                                        <div>
                                                            <x-input-label for="medical_condition" :value="__('Medical Condition')" />
                                                            <x-text-input type="text" id="medical_condition"
                                                                name="medical_condition"
                                                                value="{{ old('medical_condition') }}"
                                                                class="mt-1 block w-full" />
                                                            <x-input-error :messages="$errors->get('medical_condition')" class="mt-2" />
                                                        </div>
                                                    </div>


                                                    <!--footer-->
                                                    <div
                                                        class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
                                                        <button
                                                            class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none ms-1 mb-1 ease-linear transition-all duration-150"
                                                            type="button" @click="open = false">
                                                            Close
                                                        </button>

                                                        <button type="submit"
                                                            class="bg-emerald-500 text-white active:bg-emerald-600 font-bold uppercase text-sm px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                                                            type="button">
                                                            Generate Certificate
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>








                        </div>

                        <h2 class="text-xl font-semibold mb-2">{{ __('messages.follow_ups') }}</h2>
                        @if ($patient->followUps->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('messages.Created At') }}</h2>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('नाडी') }}</h2>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('लक्षणे') }}
                                                </h2>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('चिकित्सा') }}</h2>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('messages.Additional') }}</h2>
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-m font-large text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                <h2 class="font-semibold text-gray-600 dark:text-white">
                                                    {{ __('messages.Actions') }}</h2>
                                            </th>



                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach ($patient->followUps->sortByDesc('created_at') as $followUp)
                                            <tr class="hover:bg-gray-50 transition duration-300">
                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                                    style="vertical-align: top;">


                                                    <p class="mt-2">
                                                        {{ $followUp->created_at->format('d M Y, h:i A') }}
                                                    </p>
                                                    {{-- {{ $followUp->created_at->format('d M Y, h:i A') }} --}}
                                                </td>
                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                                    style="vertical-align: top;">
                                                    @if ($followUp->check_up_info)
                                                        @php
                                                            $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                        @endphp

                                                        <div>
                                                            {{-- <h2 class="font-semibold text-gray-800 dark:text-white">
                                                            {{ __('नाडी') }}</h2> --}}
                                                            @foreach ($checkUpInfo as $key => $value)
                                                                @if (in_array($key, [
                                                                        'वात',
                                                                        'पित्त',
                                                                        'कफ',
                                                                        'सूक्ष्म',
                                                                        'कठिन',
                                                                        'साम',
                                                                        'प्राण',
                                                                        'व्यान',
                                                                        'स्थूल',
                                                                        'तीक्ष्ण',
                                                                        'वेग',
                                                                        'अनियमित',
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
                                                            <p class="mt-2"><span
                                                                    class="font-bold text-gray-800 dark:text-white">
                                                                    {{ __('निदान') }}:</span>
                                                                @if (isset($checkUpInfo['nidan']))
                                                                    {{ $checkUpInfo['nidan'] }}
                                                                @endif
                                                            </p>

                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('उपशय') }}:</span>
                                                                @if (isset($checkUpInfo['upashay']))
                                                                    {{ $checkUpInfo['upashay'] }}
                                                                @endif
                                                            </p>

                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('सल्ला') }}:</span>
                                                                @if (isset($checkUpInfo['salla']))
                                                                    {{ $checkUpInfo['salla'] }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                                    style="vertical-align: top;">
                                                    {{-- <h2 class="font-semibold text-gray-800 dark:text-white">{{ __('लक्षणे') }}</h2> --}}
                                                    {{ $followUp->diagnosis }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                                    style="vertical-align: top;">

                                                    {{-- <h2 class="font-semibold text-gray-800 dark:text-white">{{ __('चिकित्सा') }}</h2> --}}
                                                    @if ($followUp->treatment)
                                                        <p>{{ $followUp->treatment }}</p>
                                                    @endif
                                                    @if ($followUp->check_up_info)
                                                        @php
                                                            $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                        @endphp

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
                                                    @endif
                                                    @if (isset($checkUpInfo['chikitsa_combo']))
                                                        <p class="mt-2"><span
                                                                class="font-bold text-gray-800 dark:text-white">{{ __('messages.Chikitsa Combo') }}:
                                                            </span> {{ $checkUpInfo['chikitsa_combo'] }}</p>
                                                    @endif

                                                </td>


                                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                                    style="vertical-align: top;">
                                                    @if ($followUp->check_up_info)
                                                        @php
                                                            $checkUpInfo = json_decode($followUp->check_up_info, true);
                                                        @endphp







                                                        <div>
                                                            <p class="mt-2"><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Payment Method') }}:</span>
                                                                @if (isset($checkUpInfo['payment_method']))
                                                                    {{ $checkUpInfo['payment_method'] }}
                                                                @endif
                                                            </p>

                                                            {{-- <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">Certificate:</span>
                                                                @if (isset($checkUpInfo['certificate']))
                                                                    {{ $checkUpInfo['certificate'] }}
                                                                @endif
                                                            </p> --}}

                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Drawing') }}:</span>
                                                                @if (isset($checkUpInfo['drawing']))
                                                                    {{ $checkUpInfo['drawing'] }}
                                                                @endif
                                                            </p>


                                                        </div>

                                                        <div>
                                                            <p class="mt-2"><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Amount') }}:</span>
                                                                @if (isset($checkUpInfo['amount']))
                                                                    {{ $checkUpInfo['amount'] }}
                                                                @endif
                                                            </p>
                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Balance') }}:</span>
                                                                @if (isset($checkUpInfo['balance']))
                                                                    {{ $checkUpInfo['balance'] }}
                                                                @endif
                                                            </p>

                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Branch') }}:</span>
                                                                @if (isset($checkUpInfo['branch']))
                                                                    {{ $checkUpInfo['branch'] }}
                                                                @endif
                                                            </p>
                                                            <p><span
                                                                    class="font-bold text-gray-800 dark:text-white">{{ __('messages.Doctor') }}:</span>
                                                                @if (isset($checkUpInfo['doctor']))
                                                                    {{ $checkUpInfo['doctor'] }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-gray-600 dark:text-gray-300 flex gap-4 items-center">
                                                    <a href="{{ route('followups.edit', ['followup' => $followUp->id]) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium"title="Edit">

                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('followups.destroy', ['followup' => $followUp->id]) }}"
                                                        onsubmit="return confirmDelete()">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 font-medium"
                                                            title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                {{ $patient->followUps->links() }}
                            </div>
                        @else
                            <p class="text-gray-600 bg-gray-100 p-4 rounded-md shadow-sm">
                                No follow-ups added for the patient.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this followup?");
    }
</script>
