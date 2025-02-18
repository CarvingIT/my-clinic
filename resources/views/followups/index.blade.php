<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.All Follow Ups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-900">
                <div class="p-6 text-gray-900">
                    <div class="overflow-hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-white dark:bg-gray-900">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        {{ __('messages.Created At') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        {{ __('messages.Patient Name') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-s font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        {{ __('messages.Amount') }}
                                    </th>

                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700 dark:text-white">
                                @foreach ($followUps as $followUp)
                                    <tr class="hover:bg-gray-50 transition duration-300 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $followUp->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900">{{ $followUp->patient->name }}</a>
                                        </td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <!-- Ensure that $followUp->patient is not null before accessing its id -->
                                            @if ($followUp->patient)
                                                <a href="{{ route('patients.show', $followUp->patient->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $followUp->patient->name }}
                                                </a>
                                            @else
                                                <!-- Fallback if patient is not found -->
                                                <span class="text-gray-400">No Patient</span>
                                            @endif
                                        </td>


                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300"
                                        style="vertical-align: top;">
                                        @if ($followUp->check_up_info)
                                            @php
                                                $checkUpInfo = json_decode($followUp->check_up_info, true);
                                            @endphp

                                            {{-- <div>
                                                @if (isset($checkUpInfo['payment_method']) &&
                                                        $checkUpInfo['payment_method'] !== null &&
                                                        $checkUpInfo['payment_method'] !== '')
                                                    <p class="mt-2">
                                                        <span
                                                            class="font-bold text-gray-800 dark:text-gray-200">{{ __('messages.Payment Method') }}:</span>
                                                        {{ $checkUpInfo['payment_method'] }}
                                                    </p>
                                                @endif


                                            </div> --}}

                                            <div>
                                                @if (isset($checkUpInfo['amount']) && $checkUpInfo['amount'] !== null && $checkUpInfo['amount'] !== '')
                                                    <p class="mt-2">
                                                        <span
                                                            class="font-bold text-gray-600 dark:text-gray-200">{{ __('') }}</span>
                                                        {{ $checkUpInfo['amount'] }}
                                                    </p>
                                                @endif

                                                {{-- @if (isset($checkUpInfo['balance']) && $checkUpInfo['balance'] !== null && $checkUpInfo['balance'] !== '')
                                                    <p>
                                                        <span
                                                            class="font-bold text-gray-600 dark:text-gray-200">{{ __('messages.Balance') }}:</span>
                                                        {{ $checkUpInfo['balance'] }}
                                                    </p>
                                                @endif --}}


                                            </div>
                                        @endif
                                    </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $followUps->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
